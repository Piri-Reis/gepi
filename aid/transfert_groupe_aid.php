<?php
/*
* Copyright 2001, 2017 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
*
* This file is part of GEPI.
*
* GEPI is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* GEPI is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with GEPI; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//$variables_non_protegees = 'yes';

// Initialisations files
require_once("../lib/initialisations.inc.php");


// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
	header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
	die();
} else if ($resultat_session == '0') {
	header("Location: ../logout.php?auto=1");
	die();
}

//======================================================================================
$sql="SELECT 1=1 FROM droits WHERE id='/aid/transfert_groupe_aid.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
$sql="INSERT INTO droits SET id='/aid/transfert_groupe_aid.php',
administrateur='V',
professeur='F',
cpe='F',
scolarite='F',
eleve='F',
responsable='F',
secours='F',
autre='F',
description='Transfert Groupe/AID',
statut='';";
$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}
//======================================================================================

//debug_var();

include("fonctions_aid.php");

//=========================================================

$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);

//=========================================================

$sql="CREATE TABLE IF NOT EXISTS j_groupes_aid (id_groupe INT(11) NOT NULL default '0', 
id_aid INT(11) NOT NULL default '0', 
indice_aid INT(11) NOT NULL default '0', 
etat varchar(255) NOT NULL default '', 
PRIMARY KEY  (id_groupe, id_aid), INDEX id_groupe_id_aid (id_groupe, id_aid)) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS['mysqli'], $sql);

// Cr??ation individuelle de cat??gorie
if((isset($_GET['creer_categorie']))&&(preg_match("/^[0-9]{1,}$/", $_GET['creer_categorie']))) {
	check_token();
	$msg="";

	$sql="SELECT g.id AS id_groupe, g.name, g.description, gt.* FROM groupes g, j_groupes_types jgt, groupes_types gt WHERE g.id=jgt.id_groupe AND gt.id=jgt.id_type AND jgt.id_type=gt.id AND g.id='".$_GET['creer_categorie']."' AND gt.nom_court!='local';";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		$msg="L'enseignement/groupe n??".$_GET['creer_categorie']." n'a pas ??t?? trouv?? ou n'est pas de type EPI, AP ou Parcours <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.</p>";
	}
	else {
		$lig=mysqli_fetch_object($res_grp);

		$sql="SELECT indice_aid FROM aid_config WHERE nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $lig->name)."' AND nom_complet='".mysqli_real_escape_string($GLOBALS['mysqli'], $lig->description)."';";
		$test=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($test)>0) {
			$msg.="Il existe d??j?? une cat??gorie du nom de ".$lig->name." <em>(".$lig->description.")</em>.<br />";
			$lig_cat_aid=mysqli_fetch_object($test);
			$indice_aid=$lig_cat_aid->indice_aid;

			$sql="SELECT 1=1 FROM j_groupes_aid WHERE id_groupe='".$_GET['creer_categorie']."' AND indice_aid='".$indice_aid."';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Le groupe n??".$_GET['creer_categorie']." est d??j?? associ?? ?? la cat??gorie AID ".$lig->name." <em>(".$lig->description.")</em>.<br />";
			}
			else {
				$sql="INSERT INTO j_groupes_aid SET id_groupe='".$_GET['creer_categorie']."', indice_aid='".$indice_aid."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
				}
				else {
					$msg.="Association du groupe avec la cat??gorie ".$lig->name." effectu??e <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
				}
			}
		}
		else {

			$sql="SELECT p.num_periode FROM j_groupes_classes jgc, periodes p WHERE jgc.id_classe=p.id_classe AND jgc.id_groupe='".$_GET['creer_categorie']."' ORDER BY p.num_periode DESC LIMIT 1;";
			$res_per=mysqli_query($GLOBALS['mysqli'], $sql);
			$lig_per=mysqli_fetch_object($res_per);

			$note_max=20;
			$display_begin=1;
			$display_end=$lig_per->num_periode;
			$display_nom='x';
			$message = '';
			$order_display1 = 'e';
			$order_display2 = '';
			$type_note = "every";
			$display_bulletin = "y";
			$autoriser_inscript_multiples = "y";
			$bull_simplifie = "y";
			$activer_outils_comp = "y";
			$feuille_presence = "n";

			$sql="SELECT MAX(indice_aid) AS max_aid FROM aid_config;";
			$res_cat_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($res_cat_aid)==0) {
				$indice_aid=1;
			}
			else {
				$lig_cat_aid=mysqli_fetch_object($res_cat_aid);
				$indice_aid=$lig_cat_aid->max_aid+1;
			}

			$sql="INSERT INTO aid_config SET
				nom='".$lig->name."',
				nom_complet='".$lig->description."',
				note_max='".$note_max."',
				display_begin='".$display_begin."',
				display_end='".$display_end."',
				type_note='".$type_note."',
				type_aid='".$lig->id."',
				order_display1 = '".$order_display1."',
				order_display2 = '".$order_display2."',
				message ='".$message."',
				display_nom='".$display_nom."',
				indice_aid='".$indice_aid."',
				display_bulletin='".$display_bulletin."',
				autoriser_inscript_multiples='".$autoriser_inscript_multiples."',
				bull_simplifie = '".$bull_simplifie."',
				feuille_presence = '".$feuille_presence."',
				outils_complementaires = '".$activer_outils_comp."'";
			$insert=mysqli_query($GLOBALS['mysqli'], $sql);
			if(!$insert) {
				$msg.="Erreur lors de la cr??ation de la cat??gorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
			}
			else {
				$msg.="Cat??gorie ".$lig->name." cr????e <em>".strftime("Le %d/%m/%Y ?? %H:%M:%S")."</em>.<br />";
				// Enregistrer une association?
				// Cr??er l'AID?

				$sql="SELECT 1=1 FROM j_groupes_aid WHERE id_groupe='".$_GET['creer_categorie']."' AND indice_aid='".$indice_aid."';";
				$test=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.="Le groupe n??".$_GET['creer_categorie']." est d??j?? associ?? ?? la cat??gorie AID ".$lig->name." <em>(".$lig->description.")</em>.<br />";
				}
				else {
					$sql="INSERT INTO j_groupes_aid SET id_groupe='".$_GET['creer_categorie']."', indice_aid='".$indice_aid."';";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
					}
				}
			}
		}
	}
}

// Association des groupes avec les cat??gories choisies dans les champs SELECT
// Et cr??ation/transfert par lots des AID
if(isset($_POST['enregistrer_assoc'])) {
	check_token();
	$msg="";

	$nb_reg=0;

	// Associer les groupes aux cat??gories

	$indice_aid=isset($_POST['indice_aid']) ? $_POST['indice_aid'] : array();
	foreach($indice_aid as $current_id_groupe => $current_indice_aid) {
		$sql="SELECT * FROM j_groupes_aid WHERE id_groupe='".$current_id_groupe."';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($test)>0) {
			if($current_indice_aid=="") {
				$sql="DELETE FROM j_groupes_aid WHERE id_groupe='".$current_id_groupe."';";
				//echo "$sql<br />";
				$del=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$del) {
					$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
				}
				else {
					$nb_reg++;
				}
			}
			else {
				$lig=mysqli_fetch_object($test);
				if($lig->indice_aid!=$current_indice_aid) {
					// PROBLEME POSSIBLE: Si on change de cat??gorie alors que l'association avec un aid d'une autre cat??gorie est faite.
					//                    Il faudrait repasser ?? id_aid='0'
					//                    Et traiter les ??ventuels choix d'AID avant de traiter les changements de cat??gorie pour ne pas passer lid_aid ?? z??ro par d??faut.

					// S'il existe un AID associ??, il faut migrer les donn??es
					if($lig->id_aid==0) {
						// On change juste de cat??gorie
						$sql="UPDATE j_groupes_aid SET indice_aid='".$current_indice_aid."' WHERE id_groupe='".$current_id_groupe."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS['mysqli'], $sql);
						if(!$update) {
							$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						// On v??rifie que l'AID existe
						$sql="SELECT * FROM aid WHERE id='".$lig->id_aid."';";
						$test_aid=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test_aid)==0) {
							$sql="UPDATE j_groupes_aid SET indice_aid='".$current_indice_aid."', id_aid='0' WHERE id_groupe='".$current_id_groupe."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);
							if(!$update) {
								$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
							}
							else {
								$nb_reg++;
							}
						}
						else {
							$lig_aid=mysqli_fetch_object($test_aid);

							$sql="UPDATE aid SET indice_aid='".$current_indice_aid."' WHERE id='".$lig_aid->id."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);

							$sql="UPDATE j_aid_eleves SET indice_aid='".$current_indice_aid."' WHERE id_aid='".$lig_aid->id."' AND indice_aid='".$lig->indice_aid."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);

							$sql="UPDATE j_aid_utilisateurs SET indice_aid='".$current_indice_aid."' WHERE id_aid='".$lig_aid->id."' AND indice_aid='".$lig->indice_aid."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);

							$sql="UPDATE j_groupes_aid SET indice_aid='".$current_indice_aid."' WHERE id_groupe='".$current_id_groupe."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);
							if(!$update) {
								$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
							}
							else {
								$nb_reg++;
							}
						}
					}
				}
			}
		}
		else {
			if($current_indice_aid!="") {
				$sql="INSERT INTO j_groupes_aid SET id_groupe='".$current_id_groupe."', indice_aid='".$current_indice_aid."';";
				//echo "$sql<br />";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'association du groupe avec la cat??gorie ".get_info_categorie_aid2($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}
	}

	// Cr??er les AID demand??s
	$temoin_reg=0;
	$creer_aid_lot=isset($_POST['creer_aid_lot']) ? $_POST['creer_aid_lot'] : array();
	for($loop=0;$loop<count($creer_aid_lot);$loop++) {
		$id_groupe=$creer_aid_lot[$loop];
		$group=get_group($id_groupe);

		//$temoin_reg=0;

		if(isset($group["name"])) {
			// L'AID existe-t-il d??j???
			$sql="SELECT * FROM j_groupes_aid WHERE id_groupe='".$id_groupe."';";
			//echo "$sql<br />";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)==0) {
				$msg.="ANOMALIE&nbsp;: Le groupe n??".$id_groupe." n'est associ?? ?? aucune cat??gorie AID <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
			}
			else {
				$lig_jga=mysqli_fetch_object($test);
				$indice_aid=$lig_jga->indice_aid;
				if($lig_jga->id_aid!=0) {
					// L'AID existe d??j??
					$aid_id=$lig_jga->id_aid;
					$info_aid=get_info_categorie_aid2($lig_jga->id_aid);

					// Inscription des ??l??ves
					$tab_ele=array();
					foreach($group["eleves"]["all"]["list"] as $current_login_ele) {
						$sql="SELECT 1=1 FROM j_aid_eleves WHERE login='".$current_login_ele."' AND id_aid='$aid_id' AND indice_aid='$indice_aid';";
						//echo "$sql<br />";
						$test_ele=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_ele)==0) {
							$sql="INSERT INTO j_aid_eleves SET login='".$current_login_ele."', id_aid='$aid_id', indice_aid='$indice_aid';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$insert) {
								$msg.="Erreur lors de l'ajout de l'??l??ve ".$current_login_ele." dans l'AID $info_aid<br />";
							}
							else {
								$temoin_reg++;
								$tab_ele[]=$current_login_ele;
							}
						}
						else {
							$tab_ele[]=$current_login_ele;
						}
					}
					//$msg.=count($tab_ele)." ??l??ve(s) associ??s ?? l'AID $nom_aid.<br />";

					// Inscription des professeurs
					$tab_prof=array();
					foreach($group["profs"]["list"] as $current_login_prof) {
						$sql="SELECT 1=1 FROM j_aid_utilisateurs WHERE id_utilisateur='".$current_login_prof."' AND id_aid='$aid_id' AND indice_aid='$indice_aid';";
						//echo "$sql<br />";
						$test_prof=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_prof)==0) {
							$sql="INSERT INTO j_aid_utilisateurs SET id_utilisateur='".$current_login_prof."', id_aid='$aid_id', indice_aid='$indice_aid';";
							//echo "$sql<br />";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if (!$insert) {
								$msg.="Erreur lors de l'ajout du professeur ".$current_login_prof." dans l'AID $info_aid<br />";
							}
							else {
								$temoin_reg++;
								$tab_prof[]=$current_login_prof;
							}
						}
						else {
							$tab_prof[]=$current_login_prof;
						}
					}
					//$msg.=count($tab_prof)." professeur(s) associ??s ?? l'AID $nom_aid.<br />";

					// Remplissage d'apr??s matieres_notes et matieres_appreciations
					$nb_notes=0;
					$tab_aid_note=array();
					$sql="SELECT * FROM matieres_notes WHERE id_groupe='".$id_groupe."';";
					//echo "$sql<br />";
					$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_note)>0) {
						while($lig_note=mysqli_fetch_object($res_note)) {
							if(in_array($lig_note->login, $tab_ele)) {
								$sql="SELECT 1=1 FROM aid_appreciations WHERE login='".$lig_note->login."' AND 
															id_aid='".$aid_id."' AND 
															periode='".$lig_note->periode."' AND 
															indice_aid='".$indice_aid."';";
								//echo "$sql<br />";
								$test_ele=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_ele)==0) {
									$sql="INSERT INTO aid_appreciations SET login='".$lig_note->login."', 
															id_aid='".$aid_id."', 
															periode='".$lig_note->periode."', 
															appreciation='', 
															statut='".$lig_note->statut."', 
															note='".$lig_note->note."', 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$insert) {
										$msg.="Erreur lors de l'enregistrement de la note pour ".$lig_note->login." en p??riode ".$lig_note->periode." dans l'AID $info_aid.<br />";
										$msg.="$sql<br />";
									}
									else {
										$temoin_reg++;
										$tab_aid_note[$lig_note->login][$lig_note->periode]="ok";
										$nb_notes++;
									}
								}
								else {
									$sql="UPDATE aid_appreciations SET statut='".$lig_note->statut."', 
															note='".$lig_note->note."' 
														WHERE login='".$lig_note->login."' AND 
															id_aid='".$aid_id."' AND 
															periode='".$lig_note->periode."' AND 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$update) {
										$msg.="Erreur lors de l'enregistrement de la note pour ".$lig_note->login." en p??riode ".$lig_note->periode." dans l'AID $info_aid.<br />";
										$msg.="$sql<br />";
									}
									else {
										$temoin_reg++;
										$tab_aid_note[$lig_note->login][$lig_note->periode]="ok";
										$nb_notes++;
									}
								}
							}
						}
					}
					//$msg.=$nb_notes." note(s) enregistr??e(s) <em>(toutes p??riodes confondues)</em>.<br />";

					$nb_app=0;
					$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='".$id_groupe."';";
					//echo "$sql<br />";
					$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_app)>0) {
						while($lig_app=mysqli_fetch_object($res_app)) {
							if(in_array($lig_app->login, $tab_ele)) {
/*
if($lig_app->login=='beaussart_a') {
echo "<pre>";
print_r($tab_aid_app[$lig_app->login]);
print_r($tab_aid_note[$lig_app->login]);
echo "</pre>";
}
*/
								$action_aid_app="update";
								if((!isset($tab_aid_app[$lig_app->login][$lig_app->periode]))&&(!isset($tab_aid_note[$lig_app->login][$lig_app->periode]))) {
									// On va quand m??me tester
									$sql="SELECT 1=1 FROM aid_appreciations WHERE id_aid='".$aid_id."' AND 
															periode='".$lig_app->periode."' AND 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$test=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($test)==0) {
										$action_aid_app="insert";
									}
								}

								if($action_aid_app=="insert") {
									$sql="INSERT INTO aid_appreciations SET login='".$lig_app->login."', 
															id_aid='".$aid_id."', 
															periode='".$lig_app->periode."', 
															appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."', 
															statut='-', 
															note='', 
															indice_aid='".$indice_aid."';";
								}
								else {
									$sql="UPDATE aid_appreciations SET appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."' 
														WHERE id_aid='".$aid_id."' AND 
															periode='".$lig_app->periode."' AND 
															indice_aid='".$indice_aid."';";
								}

								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$insert) {
									$msg.="Erreur lors de l'enregistrement de l'appr??ciation pour ".$lig_app->login." en p??riode ".$lig_app->periode." dans l'AID $info_aid.<br />";
									$msg.="$sql<br />";
								}
								else {
									$temoin_reg++;
									$nb_app++;
								}
							}
						}
					}


				}
				else {
					// On va cr??er l'AID puis le remplir
					$nom_aid=remplace_accents($group['name']."_".$group['classlist_string'], "'all_nospace'");

					// Le champ id de la table aid n'est pas auto_increment
					$aid_id=Dernier_id()+1;

					// Cr??er l'AID
					$sql="INSERT INTO aid SET id='".$aid_id."', 
									nom='".$nom_aid."', 
									indice_aid='".$indice_aid."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de la cr??ation de l'AID ".$nom_aid." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
					}
					else {
						$temoin_reg++;
						//$msg.="AID $nom_aid cr????.<br />";
						//$aid_id=mysqli_insert_id($GLOBALS['mysqli']);

						// Faire l'association
						$sql="UPDATE j_groupes_aid SET id_aid='".$aid_id."' WHERE id_groupe='".$id_groupe."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS['mysqli'], $sql);
						if(!$update) {
							$msg.="Erreur lors de l'association du groupe avec l'AID ".$nom_aid." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
						}
						else {
							//$msg.="AID $nom_aid associ?? ?? l'enseignement/groupe.<br />";
							// Remplir l'AID d'apr??s les bulletins du groupe

							// Inscription des ??l??ves
							$tab_ele=array();
							foreach($group["eleves"]["all"]["list"] as $current_login_ele) {
								$sql="INSERT INTO j_aid_eleves SET login='".$current_login_ele."', id_aid='$aid_id', indice_aid='$indice_aid'";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$insert) {
									$msg.="Erreur lors de l'ajout de l'??l??ve ".$current_login_ele."<br />";
								}
								else {
									$temoin_reg++;
									$tab_ele[]=$current_login_ele;
								}
							}
							//$msg.=count($tab_ele)." ??l??ve(s) associ??s ?? l'AID $nom_aid.<br />";

							// Inscription des professeurs
							$tab_prof=array();
							foreach($group["profs"]["list"] as $current_login_prof) {
								$sql="INSERT INTO j_aid_utilisateurs SET id_utilisateur='".$current_login_prof."', id_aid='$aid_id', indice_aid='$indice_aid'";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$insert) {
									$msg.="Erreur lors de l'ajout du professeur ".$current_login_prof."<br />";
								}
								else {
									$temoin_reg++;
									$tab_prof[]=$current_login_prof;
								}
							}
							//$msg.=count($tab_prof)." professeur(s) associ??s ?? l'AID $nom_aid.<br />";

							// Remplissage d'apr??s matieres_notes et matieres_appreciations
							$nb_notes=0;
							$tab_aid_note=array();
							$sql="SELECT * FROM matieres_notes WHERE id_groupe='".$id_groupe."';";
							//echo "$sql<br />";
							$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_note)>0) {
								while($lig_note=mysqli_fetch_object($res_note)) {
									if(in_array($lig_note->login, $tab_ele)) {
										$sql="INSERT INTO aid_appreciations SET login='".$lig_note->login."', 
																id_aid='".$aid_id."', 
																periode='".$lig_note->periode."', 
																appreciation='', 
																statut='".$lig_note->statut."', 
																note='".$lig_note->note."', 
																indice_aid='".$indice_aid."';";
										//echo "$sql<br />";
										$insert=mysqli_query($GLOBALS["mysqli"], $sql);
										if (!$insert) {
											$msg.="Erreur lors de l'enregistrement de la note pour ".$lig_note->login." en p??riode ".$lig_note->periode."<br />";
										}
										else {
											$temoin_reg++;
											$tab_aid_note[$lig_note->login][$lig_note->periode]="ok";
											$nb_notes++;
										}
									}
								}
							}
							//$msg.=$nb_notes." note(s) enregistr??e(s) <em>(toutes p??riodes confondues)</em>.<br />";

							$nb_app=0;
							$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='".$id_groupe."';";
							//echo "$sql<br />";
							$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_app)>0) {
								while($lig_app=mysqli_fetch_object($res_app)) {
									if(in_array($lig_app->login, $tab_ele)) {
										//if(!isset($tab_aid_app[$lig_app->login][$lig_app->periode])) {
										if((!isset($tab_aid_app[$lig_app->login][$lig_app->periode]))&&(!isset($tab_aid_note[$lig_app->login][$lig_app->periode]))) {
											$sql="INSERT INTO aid_appreciations SET login='".$lig_app->login."', 
																	id_aid='".$aid_id."', 
																	periode='".$lig_app->periode."', 
																	appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."', 
																	statut='-', 
																	note='', 
																	indice_aid='".$indice_aid."';";
											//echo "$sql<br />";
											$insert=mysqli_query($GLOBALS["mysqli"], $sql);
											if (!$insert) {
												$msg.="Erreur lors de l'enregistrement de l'appr??ciation pour ".$lig_app->login." en p??riode ".$lig_app->periode."<br />";
											}
											else {
												$temoin_reg++;
												$nb_app++;
											}
										}
										else {
											$sql="UPDATE aid_appreciations SET appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."' 
																WHERE id_aid='".$aid_id."' AND 
																	periode='".$lig_app->periode."' AND 
																	indice_aid='".$indice_aid."';";
											//echo "$sql<br />";
											$update=mysqli_query($GLOBALS["mysqli"], $sql);
											if (!$update) {
												$msg.="Erreur lors de l'enregistrement de l'appr??ciation pour ".$lig_app->login." en p??riode ".$lig_app->periode."<br />";
											}
											else {
												$temoin_reg++;
												$nb_app++;
											}
										}
									}
								}
							}
							//$msg.=$nb_app." appr??ciation(s) enregistr??e(s) <em>(toutes p??riodes confondues)</em>.<br />";
						}
					}
				}

			}
		}
		else {
			$msg.="Le groupe n??".$id_groupe." n'existe pas.<br />";
		}
	}

	if($nb_reg>0) {
		$msg.=$nb_reg." enregistrement(s) effectu??(s) <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
	}
	if($temoin_reg>0) {
		$msg.="Cr??ation/remplissage d'AID effectu?? <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
	}
}

// Cr??ation individuelle d'un AID
if((isset($_GET['creer_aid']))&&(preg_match("/^[0-9]{1,}$/", $_GET['creer_aid']))) {
	check_token();
	$msg="";

	$id_groupe=$_GET['creer_aid'];
	$group=get_group($id_groupe);

	if(isset($group["name"])) {
		$sql="SELECT * FROM j_groupes_aid WHERE id_groupe='".$id_groupe."';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($test)==0) {
			$msg.="ANOMALIE&nbsp;: Le groupe n??".$id_groupe." n'est associ?? ?? aucune cat??gorie AID <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
		}
		else {
			$lig_grp_aid=mysqli_fetch_object($test);
			$indice_aid=$lig_grp_aid->indice_aid;

			$nom_aid=remplace_accents($group['name']."_".$group['classlist_string'], "'all_nospace'");

			// Le champ id de la table aid n'est pas auto_increment
			/*
			$sql="SELECT MAX(id) AS max_id FROM aid;";
			//echo "$sql<br />";
			$res_max_aid=mysqli_query($GLOBALS['mysqli'], $sql);
			$lig_max_aid=mysqli_fetch_object($res_max_aid);
			$aid_id=$lig_max_aid->max_id+1;
			*/
			$aid_id=Dernier_id()+1;

			// Cr??er l'AID
			$sql="INSERT INTO aid SET id='".$aid_id."', 
							nom='".$nom_aid."', 
							indice_aid='".$indice_aid."';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS['mysqli'], $sql);
			if(!$insert) {
				$msg.="Erreur lors de la cr??ation de l'AID ".$nom_aid." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
			}
			else {
				$msg.="AID $nom_aid cr????.<br />";
				//$aid_id=mysqli_insert_id($GLOBALS['mysqli']);

				// Faire l'association
				$sql="UPDATE j_groupes_aid SET id_aid='".$aid_id."' WHERE id_groupe='".$id_groupe."';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$update) {
					$msg.="Erreur lors de l'association du groupe avec l'AID ".$nom_aid." <em>(".strftime("Le %d/%m/%Y ?? %H:%M:%S").")</em>.<br />";
				}
				else {
					$msg.="AID $nom_aid associ?? ?? l'enseignement/groupe.<br />";
					// Remplir l'AID d'apr??s les bulletins du groupe

					// Inscription des ??l??ves
					$tab_ele=array();
					foreach($group["eleves"]["all"]["list"] as $current_login_ele) {
						$sql="INSERT INTO j_aid_eleves SET login='".$current_login_ele."', id_aid='$aid_id', indice_aid='$indice_aid'";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert) {
							$msg.="Erreur lors de l'ajout de l'??l??ve ".$current_login_ele."<br />";
						}
						else {
							$tab_ele[]=$current_login_ele;
						}
					}
					$msg.=count($tab_ele)." ??l??ve(s) associ??s ?? l'AID $nom_aid.<br />";

					// Inscription des professeurs
					$tab_prof=array();
					foreach($group["profs"]["list"] as $current_login_prof) {
						$sql="INSERT INTO j_aid_utilisateurs SET id_utilisateur='".$current_login_prof."', id_aid='$aid_id', indice_aid='$indice_aid'";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert) {
							$msg.="Erreur lors de l'ajout du professeur ".$current_login_prof."<br />";
						}
						else {
							$tab_prof[]=$current_login_prof;
						}
					}
					$msg.=count($tab_prof)." professeur(s) associ??s ?? l'AID $nom_aid.<br />";

					// Remplissage d'apr??s matieres_notes et matieres_appreciations
					$nb_notes=0;
					$tab_aid_note=array();
					$sql="SELECT * FROM matieres_notes WHERE id_groupe='".$id_groupe."';";
					//echo "$sql<br />";
					$res_note=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_note)>0) {
						while($lig_note=mysqli_fetch_object($res_note)) {
							if(in_array($lig_note->login, $tab_ele)) {
								$sql="INSERT INTO aid_appreciations SET login='".$lig_note->login."', 
														id_aid='".$aid_id."', 
														periode='".$lig_note->periode."', 
														appreciation='', 
														statut='".$lig_note->statut."', 
														note='".$lig_note->note."', 
														indice_aid='".$indice_aid."';";
								//echo "$sql<br />";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if (!$insert) {
									$msg.="Erreur lors de l'enregistrement de la note pour ".$lig_note->login." en p??riode ".$lig_note->periode."<br />";
								}
								else {
									$tab_aid_note[$lig_note->login][$lig_note->periode]="ok";
									$nb_notes++;
								}
							}
						}
					}
					$msg.=$nb_notes." note(s) enregistr??e(s) <em>(toutes p??riodes confondues)</em>.<br />";

					$nb_app=0;
					$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='".$id_groupe."';";
					//echo "$sql<br />";
					$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_app)>0) {
						while($lig_app=mysqli_fetch_object($res_app)) {
							if(in_array($lig_app->login, $tab_ele)) {
								//if(!isset($tab_aid_app[$lig_app->login][$lig_app->periode])) {
								if((!isset($tab_aid_app[$lig_app->login][$lig_app->periode]))&&(!isset($tab_aid_note[$lig_app->login][$lig_app->periode]))) {
									$sql="INSERT INTO aid_appreciations SET login='".$lig_app->login."', 
															id_aid='".$aid_id."', 
															periode='".$lig_app->periode."', 
															appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."', 
															statut='-', 
															note='', 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$insert=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$insert) {
										$msg.="Erreur lors de l'enregistrement de l'appr??ciation pour ".$lig_app->login." en p??riode ".$lig_app->periode."<br />";
									}
									else {
										$nb_app++;
									}
								}
								else {
									$sql="UPDATE aid_appreciations SET appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."' 
														WHERE id_aid='".$aid_id."' AND 
															periode='".$lig_app->periode."' AND 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$update) {
										$msg.="Erreur lors de l'enregistrement de l'appr??ciation pour ".$lig_app->login." en p??riode ".$lig_app->periode."<br />";
									}
									else {
										$nb_app++;
									}
								}
							}
						}
					}
					$msg.=$nb_app." appr??ciation(s) enregistr??e(s) <em>(toutes p??riodes confondues)</em>.<br />";
				}
			}
		}
	}
}
//=========================================================

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont ??t?? modifi??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "AID : Transfert groupes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accueil</a>
 | <a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Index AID</a>
 | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Rafraichir sans enregistrer, ni revalider la derni??re action/cr??ation</a>";
//echo "</p>\n";

if(!isset($mode)) {
	echo "</p>\n";

	echo "<h2>Cr??ation/Remplissage d'AID d'apr??s des enseignements/groupes</h2>
	<p style='margin-top:1em;margin-bottom:1em;'>Cette page est con??ue pour remplir des AID d'apr??s des enseignements dans le cadre des EPI, AP et Parcours.<br />
	Vous pouvez cr??er les Cat??gories d'AID pr??alablement, ou cr??er les cat??gories d'apr??s des enseignements ci-dessous.<br />
	Une fois les cat??gories cr????es et associ??es aux enseignements, vous pourrez cr??er des AID dans les cat??gories et remplir ces AID avec les notes/appr??ciations saisies pour les bulletins.<br />
	&nbsp;<br />
	Combien de cat??gories&nbsp;?<br />
	Il convient de cr??er une cat??gorie AID par famille d'EPI, c'est-??-dire par type d'EPI, avec les m??mes caract??ristiques, destin??s ?? appara??tre sur les bulletins dans les m??mes p??riodes.<br />
	Par exemple, deux EPI correspondant ?? une m??me famille devraient a priori ??tre associ??s ?? une m??me cat??gorie AID,<br />
	sauf si ces EPI ne sont pas destin??s ?? apparaitre sur les m??mes p??riodes sur les bulletins <em>(cas d'un EPI trait?? ?? deux p??riodes diff??rentes sur des classes diff??rentes)</em>.<br />
	Dans ce dernier cas, si ces enseignements ont les m??mes nom/description, vous devrez passer par la page classique de cr??ation de cat??gorie, plut??t que par les icones ci-dessous.</p>
	<p style='margin-top:1em;margin-bottom:1em;'>Les ??tapes sont les suivantes&nbsp;:</p>
	<ol>
		<li><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Cr??er les cat??gories dans la page principale des AID</a>,<br />ou <strong>les cr??er ci-dessous</strong> ?? l'aide des liens/icones <img src='../images/icons/wizard.png' class='icone16' alt='Cr??er' /> en ent??te des <strong>colonnes Cat??gorie AID</strong>.<br />
		Vous devez cr??er une cat??gorie de chaque type/famille <em>(pas une pour chaque enseignement)</em>, puis passer ?? l'??tape 2.</li>
		<li>Associer les enseignements dont le <strong>nom (description)</strong> co??ncide avec la cat??gorie AID en cliquant sur l'icone <img src='../images/icons/wizard.png' class='icone16' alt='Cr??er' /> en ligne d'ent??te <strong>Cat??gorie AID</strong>, puis en validant l'association.</li>
		<li>Cr??er les AID et les remplir d'apr??s le contenu des bulletins en cliquant sur les icones <img src='../images/icons/wizard.png' class='icone16' alt='Cr??er' /> dans la colonne AID <em>(la cr??ation n'est possible qu'une fois l'association avec une cat??gorie AID effectu??e)</em>.</li>
	</ol>

<div class='fieldset_opacite50' style='margin:1em;padding:1em;'>
<p><strong style='color:red;'>Attention&nbsp;:</strong></p>
<p>Apr??s cr??ation des AID d'apr??s les enseignements et le transfert/copie des notes/appr??ciations des enseignements vers les AID, vos bulletins vont faire appara??tre les enseignements et AID simultan??ment... donc en double puisque correspondant l'un et l'autre <em>(enseignement et AID)</em> aux m??mes informations.<br />
Il faudrait faire disparaitre les uns ou les autres des bulletins.<br />
Vous pouvez&nbsp;:</p>
<ul>
	<li>
		<p>
			soit rendre les enseignements de type EPI, AP, Parcours invisibles sur les bulletins <em>(ils peuvent rester visibles dans les carnets de notes et cahiers de textes si vous le souhaitez)</em>.<br />
			La saisie de notes et appr??ciations dans les bulletins par les enseignants ne sera plus possible.<br />
			Ils devront faire la saisie dans les AID.<br />
			C'est la meilleure solution pour que les informations destin??es ?? remonter via LSUN soient remplies directement par les professeurs dans les AID.
		</p>
		<p>
			Pour cela utilisez la page de <a href='../classes/classes_param.php' target='_blank'>Param??trage de plusieurs classes par lots</a>.<br />
			Choisissez les classes,<br />
			Cochez les cases <strong>Modifier la visibilit?? des enseignements de type AP, EPI, Parcours</strong>,<br />
			D??cochez la colonne <strong>Visibilit?? sur les Bulletins</strong><br />
			et <strong>Validez</strong>.
		</p>
	</li>
	<li>soit ne pas afficher les AID sur les bulletins <em>(et ils ne seront alors utilis??s que pour la remont??e LSU)</em>.<br />
	Avec ce choix, les professeurs risquent de ne pas savoir o?? remplir les appr??ciations <em>(dans les enseignements ou dans les AID)</em> et vous ne saurez pas s'il faut re-provoquer un transfert enseignement-&gt;AID dans la pr??sente page.<br />
	Il suffit avec cette solution de d??cocher la case <strong>Afficher les donn??es sur les AID</strong> dans les <strong>Param??tres d'impression des bulletins</strong>.<br />
	C'est simple, mais risqu?? parce qu'il y aura toujours un professeur pour ne pas remplir ce que vous aurez choisi <em>(enseignement ou AID)</em>.</li>
</ul>
</div>

<p style='margin-top:1em;margin-bottom:1em;'><em>Note&nbsp;:</em> Il est possible de trier le tableau en cliquant sur les colonnes.</p>";

	$tab_cat_aid=array();
	$sql="SELECT ac.*, gt.nom_court AS nom_court_type, gt.nom_complet AS nom_complet_type FROM aid_config ac, groupes_types gt WHERE ac.type_aid=gt.id AND gt.nom_court!='local' ORDER BY gt.id, ac.nom;";
	//echo "$sql<br />";
	$res_aid=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		echo "<p style='color:red'>Il n'existe aucune cat??gorie d'AID de type AP, EPI ou Parcours.</p>";
	}
	else {
		echo "<p style='margin-left:3em;text-indent:-3em;'>Les cat??gories d'AID existantes, associ??es ?? des AP, EPI ou Parcours sont&nbsp;:<br />";
		while($lig_aid=mysqli_fetch_assoc($res_aid)) {
			$tab_cat_aid[]=$lig_aid;
			echo "<strong>".$lig_aid["nom_court_type"]."&nbsp;:</strong> ".$lig_aid["nom"]." <em>(".$lig_aid["nom_complet"].")</em><br />";
		}
		echo "</p>";
	}

	$sql="SELECT g.id AS id_groupe, g.name, g.description, gt.* FROM groupes g, j_groupes_types jgt, groupes_types gt WHERE g.id=jgt.id_groupe AND gt.id=jgt.id_type AND jgt.id_type=gt.id AND gt.nom_court!='local' ORDER BY gt.id, g.name, g.description;";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		echo "<p style='color:red'>Aucun enseignement/groupe n'est associ?? ?? un type EPI, AP ou Parcours.</p>";
		require("../lib/footer.inc.php");
		die();
	}

	$tab_grp_aid=array();
	$sql="SELECT * FROM j_groupes_aid;";
	//echo "$sql<br />";
	$res_grp_aid=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_grp_aid)>0) {
		while($lig_grp_aid=mysqli_fetch_object($res_grp_aid)) {
			$tab_grp_aid[$lig_grp_aid->id_groupe]["indice_aid"]=$lig_grp_aid->indice_aid;
			$tab_grp_aid[$lig_grp_aid->id_groupe]["id_aid"]=$lig_grp_aid->id_aid;
		}
	}

	echo "<form name='formulaire' action='".$_SERVER['PHP_SELF']."' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='enregistrer_assoc' value='y' />
		<p style='text-align:center;'><input type='submit' value='Enregistrer les associations' /></p>
		<table class='boireaus boireaus_alt sortable resizable'>
			<thead>
				<tr>
					<th class='number'>Id</th>
					<th class='text' title=\"Trier par nom d'enseignement\">Nom</th>
					<th class='text' title=\"Trier par description d'enseignement\">Description</th>
					<th class='text' title=\"Trier par nom de classe\">Classes</th>
					<th class='text' title=\"Trier par type d'enseignement\">Type</th>
					<th colspan='3'>
						Cat??gorie AID
						<a href='#' onclick=\"select_auto_cat(); return false;\" title=\"Associer les groupes/enseignements aux cat??gories de m??mes nom/description.\"><img src='../images/icons/wizard.png' class='icone16' alt='Associer' /></a>
					</th>
					<th colspan='3' class='nosort'>
						AID

						<a href='#' onclick=\"select_auto_creation_aid(); return false;\" title=\"Cocher les lignes pour lesquelles les AID peuvent ??tre cr????s
(ceux pour lesquels les enseignements d??j?? associ??s ?? une cat??gorie).\"><img src='../images/icons/wizard.png' class='icone16' alt='Associer' /></a>

						<a href='#' onclick=\"rafraichir_notes_app_aid(); return false;\" title=\"Cocher les lignes AID pour re-transf??rer les donn??es des bulletins vers les AID.

ATTENTION : Vous ne devriez pas effectuer cette action si des professeurs ont effectu?? des saisies manuelles dans les AID.
Vous ??craseriez alors les saisies dans les AID avec le contenu des enseignements sur les bulletins.\"><img src='../images/icons/actualiser.png' class='icone16' alt='Re-transf??rer' /></a>

						<a href='#' onclick=\"decocher_aid(); return false;\" title=\"D??cocher les lignes AID.\"><img src='../images/disabled.png' class='icone16' alt='D??cocher' /></a>
					</th>
				</tr>
			</thead>
			<tbody>";
	$cpt=0;
	while($lig_grp=mysqli_fetch_object($res_grp)) {
		$chaine_classes="";
		$sql="SELECT DISTINCT classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_groupe='".$lig_grp->id_groupe."' AND jgc.id_classe=c.id ORDER BY c.classe;";
		$res_clas=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($res_clas)>0) {
			$cpt_clas=0;
			while($lig_clas=mysqli_fetch_object($res_clas)) {
				if($cpt_clas>0) {$chaine_classes.=", ";}
				$chaine_classes.=$lig_clas->classe;
				$cpt_clas++;
			}
		}
		echo "
				<tr>
					<td>$lig_grp->id_groupe</td>
					<td id='td_name_".$cpt."'>".trim($lig_grp->name)."</td>
					<td id='td_description_".$cpt."'>".trim(stripslashes($lig_grp->description))."</td>
					<td>$chaine_classes</td>
					<td>
						$lig_grp->nom_court
						<span id='span_name_plus_description_".$cpt."' style='display:none'>".ensure_ascii(casse_mot(trim($lig_grp->name)." (".trim(stripslashes($lig_grp->description).")"), "min"))."</span>
					</td>
					<td>".(isset($tab_grp_aid[$lig_grp->id_groupe]) ? "<img src='../images/enabled.png' class='icone20' alt='Cat??gorie cr????e' title='Cat??gorie cr????e/associ??e' />" : "<a href='".$_SERVER['PHP_SELF']."?creer_categorie=".$lig_grp->id_groupe."&".add_token_in_url()."' title=\"Cr??er une cat??gorie AID d'apr??s le nom de l'enseignement.\"><img src='../images/icons/wizard.png' class='icone16' alt='Cr??er' /></a>")."</td>
					<td>
						<select name='indice_aid[".$lig_grp->id_groupe."]' id='indice_aid_".$cpt."' style='width:20em;' onchange=\"changement(); document.getElementById('span_cat_$cpt').style.display='none';\">
							<option value=''></option>";
		for($loop=0;$loop<count($tab_cat_aid);$loop++) {
			$selected="";
			if((isset($tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]))&&($tab_cat_aid[$loop]["indice_aid"]==$tab_grp_aid[$lig_grp->id_groupe]["indice_aid"])) {
				$selected=" selected='true'";
			}
			echo "
							<option value='".$tab_cat_aid[$loop]["indice_aid"]."'".$selected.">".trim($tab_cat_aid[$loop]["nom"])." (".trim($tab_cat_aid[$loop]["nom_complet"]).")</option>";
		}
		echo "
						</select>
					</td>
					<td>
						<span id='span_cat_$cpt'>".(isset($tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]) ? "<a href='config_aid.php?indice_aid=".$tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]."' target='_blank' title=\"Voir la cat??gorie dans un nouvel onglet.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>" : "")."</span>
					</td>";
		if(isset($tab_grp_aid[$lig_grp->id_groupe]["id_aid"])) {
			if($tab_grp_aid[$lig_grp->id_groupe]["id_aid"]!=0) {
				echo "
					<td>
						<img src='../images/enabled.png' class='icone20' alt='AID cr????' title='AID cr????' />
					</td>
					<td id='td_aid_existant_".$cpt."'>
						<span title=\"AID n??".$tab_grp_aid[$lig_grp->id_groupe]["id_aid"]."\">".get_info_aid($tab_grp_aid[$lig_grp->id_groupe]["id_aid"])."</span>
					</td>
					<td>
						<!-- Pouvoir re-provoquer le transfert des donn??es des bulletins (avec alerte sur le fait que si les professeurs ont fait des saisies manuelles dans l'AID, il ne faudrait plus provoquer le transfert/??crasement) -->
						<input type='checkbox' name='creer_aid_lot[]' id='creer_aid_lot_".$cpt."' value='".$lig_grp->id_groupe."' title=\"Re-remplir les notes/appr??ciations des AID d'apr??s le contenu des enseignements ci-contre dans les bulletins.

ATTENTION : Si les professeurs ont effectu?? des saisies manuelles 
            d appr??ciations/notes dans les AID, 
            vous ne devriez pas cocher ces cases pour lesquelles 
            le transfert enseignements vers AID a d??j?? ??t?? effectu??.
            Vous ??craseriez leurs saisies dans les AID.\" />
					</td>";
			}
			else {
				echo "
					<td>
						<a href='".$_SERVER['PHP_SELF']."?creer_aid=".$lig_grp->id_groupe."&".add_token_in_url()."' title=\"Cr??er et remplir un AID d'apr??s l'enseignement.\"><img src='../images/icons/wizard.png' class='icone16' alt='Cr??er' /></a>
					</td>
					<td id='td_aid_a_creer_".$cpt."'></td>
					<td>
						<!-- Colonne cr??ation/transfert -->
						<input type='checkbox' name='creer_aid_lot[]' id='creer_aid_lot_".$cpt."' value='".$lig_grp->id_groupe."' title=\"Cr??er l'AID pour cet enseignement.\" />
					</td>";
			}
		}
		else {
			// On ne propose rien si aucune cat??gorie n'est pr??sente.
				echo "
					<td></td>
					<td></td>
					<td></td>";
		}
		echo "
					</td>
				</tr>";
		$cpt++;
	}
	echo "
			</tbody>
		</table>
		<p style='text-align:center;'><input type='submit' value='Enregistrer les associations' /></p>
	</fieldset>
</form>

<script type='text/javascript'>
	// https://gist.github.com/alisterlf/3490957
	function RemoveAccents(str) {
	  var accents    = '????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????';
	  var accentsOut = \"AAAAAAaaaaaaOOOOOOOooooooEEEEeeeeeCcDIIIIiiiiUUUUuuuuNnSsYyyZz\";
	  str = str.split('');
	  var strLen = str.length;
	  var i, x;
	  for (i = 0; i < strLen; i++) {
	    if ((x = accents.indexOf(str[i])) != -1) {
		str[i] = accentsOut[x];
	    }
	  }
	  return str.join('');
	}

	function select_auto_cat() {
		nb_assoc=0;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('td_name_'+i)) {
				for(j=0;j<document.getElementById('indice_aid_'+i).options.length;j++) {
					/*
					if(i<1) {
						alert('Option '+j+' value='+document.getElementById('indice_aid_'+i).options[j].value);
						alert('Option '+j+' innerHTML='+document.getElementById('indice_aid_'+i).options[j].innerHTML);
						alert('Contenu TD compil??s='+document.getElementById('td_name_'+i).innerHTML+' ('+document.getElementById('td_description_'+i).innerHTML+')');
					}
					*/

					//if(document.getElementById('indice_aid_'+i).options[j].innerHTML==document.getElementById('td_name_'+i).innerHTML+' ('+document.getElementById('td_description_'+i).innerHTML+')') {
					tmp_var=RemoveAccents(document.getElementById('indice_aid_'+i).options[j].innerHTML);
					if(tmp_var.toLowerCase()==document.getElementById('span_name_plus_description_'+i).innerHTML) {

						document.getElementById('indice_aid_'+i).selectedIndex=j;
						nb_assoc++;
					}
				}
			}
		}
		if(nb_assoc>0) {
			alert(\"N'oubliez pas de valider l'enregistrement des associations propos??es.\");
		}
	}

	function select_auto_creation_aid() {
		nb_assoc=0;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('td_aid_a_creer_'+i)) {
				if(document.getElementById('creer_aid_lot_'+i)) {
					document.getElementById('creer_aid_lot_'+i).checked=true;
					nb_assoc++;
				}
			}
		}
		if(nb_assoc>0) {
			alert(\"N'oubliez pas de valider les cr??ations demand??es.\");
		}
	}

	function rafraichir_notes_app_aid() {
		var is_confirmed = confirm(\"ATTENTION : Si les professeurs ont effectu?? des saisies manuelles d appr??ciations/notes dans les AID, vous ne devriez pas cocher ces cases pour lesquelles le transfert enseignements vers AID a d??j?? ??t?? effectu??e. Vous ??craseriez leurs saisies dans les AID. Voulez-vous quand m??me cocher ces cases?\");
		if(is_confirmed) {
			nb_assoc=0;
			for(i=0;i<$cpt;i++) {
				if(document.getElementById('td_aid_existant_'+i)) {
					if(document.getElementById('creer_aid_lot_'+i)) {
						document.getElementById('creer_aid_lot_'+i).checked=true;
						nb_assoc++;
					}
				}
			}
			if(nb_assoc>0) {
				alert(\"N'oubliez pas de valider les re-transfert bulletins->aid demand??s.\");
			}
		}
	}

	function decocher_aid() {
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('creer_aid_lot_'+i)) {
				document.getElementById('creer_aid_lot_'+i).checked=false;
			}
		}
	}
</script>";
}
else {
	echo "<p style='color:red'>Mode non encore impl??ment??.</p>";

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>
