<?php
/*
* Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

if((isset($_GET['creer_categorie']))&&(preg_match("/^[0-9]{1,}$/", $_GET['creer_categorie']))) {
	check_token();
	$msg="";

	$sql="SELECT g.id AS id_groupe, g.name, g.description, gt.* FROM groupes g, j_groupes_types jgt, groupes_types gt WHERE g.id=jgt.id_groupe AND gt.id=jgt.id_type AND jgt.id_type=gt.id AND g.id='".$_GET['creer_categorie']."';";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		$msg="L'enseignement/groupe n°".$_GET['creer_categorie']." n'a pas été trouvé ou n'est pas de type EPI, AP ou Parcours <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.</p>";
	}
	else {
		$lig=mysqli_fetch_object($res_grp);

		$sql="SELECT indice_aid FROM aid_config WHERE nom='".mysqli_real_escape_string($GLOBALS['mysqli'], $lig->name)."' AND nom_complet='".mysqli_real_escape_string($GLOBALS['mysqli'], $lig->description)."';";
		$test=mysqli_query($GLOBALS['mysqli'], $sql);
		if(mysqli_num_rows($test)>0) {
			$msg.="Il existe déjà une catégorie du nom de ".$lig->name." <em>(".$lig->description.")</em>.<br />";
			$lig_cat_aid=mysqli_fetch_object($test);
			$indice_aid=$lig_cat_aid->indice_aid;

			$sql="SELECT 1=1 FROM j_groupes_aid WHERE id_groupe='".$_GET['creer_categorie']."' AND indice_aid='".$indice_aid."';";
			$test=mysqli_query($GLOBALS['mysqli'], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="Le groupe n°".$_GET['creer_categorie']." est déjà associé à la catégorie AID ".$lig->name." <em>(".$lig->description.")</em>.<br />";
			}
			else {
				$sql="INSERT INTO j_groupes_aid SET id_groupe='".$_GET['creer_categorie']."', indice_aid='".$indice_aid."';";
				$insert=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$insert) {
					$msg.="Erreur lors de l'association du groupe avec la catégorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
				}
				else {
					$msg.="Association du groupe avec la catégorie ".$lig->name." effectuée <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
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
			$order_display1 = '';
			$order_display2 = '';
			$type_note = "every";
			$display_bulletin = "y";
			$autoriser_inscript_multiples = "y";
			$bull_simplifie = "y";
			$activer_outils_comp = "n";
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
				$msg.="Erreur lors de la création de la catégorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
			}
			else {
				$msg.="Catégorie ".$lig->name." créée <em>".strftime("Le %d/%m/%Y à %H:%M:%S")."</em>.<br />";
				// Enregistrer une association?
				// Créer l'AID?

				$sql="SELECT 1=1 FROM j_groupes_aid WHERE id_groupe='".$_GET['creer_categorie']."' AND indice_aid='".$indice_aid."';";
				$test=mysqli_query($GLOBALS['mysqli'], $sql);
				if(mysqli_num_rows($test)>0) {
					$msg.="Le groupe n°".$_GET['creer_categorie']." est déjà associé à la catégorie AID ".$lig->name." <em>(".$lig->description.")</em>.<br />";
				}
				else {
					$sql="INSERT INTO j_groupes_aid SET id_groupe='".$_GET['creer_categorie']."', indice_aid='".$indice_aid."';";
					$insert=mysqli_query($GLOBALS['mysqli'], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'association du groupe avec la catégorie ".$lig->name." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
					}
				}
			}
		}
	}
}

if(isset($_POST['enregistrer_assoc'])) {
	check_token();
	$msg="";

	$nb_reg=0;
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
					$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
				}
				else {
					$nb_reg++;
				}
			}
			else {
				$lig=mysqli_fetch_object($test);
				if($lig->indice_aid!=$current_indice_aid) {
					// PROBLEME POSSIBLE: Si on change de catégorie alors que l'association avec un aid d'une autre catégorie est faite.
					//                    Il faudrait repasser à id_aid='0'
					//                    Et traiter les éventuels choix d'AID avant de traiter les changements de catégorie pour ne pas passer lid_aid à zéro par défaut.

					// S'il existe un AID associé, il faut migrer les données
					if($lig->id_aid==0) {
						// On change juste de catégorie
						$sql="UPDATE j_groupes_aid SET indice_aid='".$current_indice_aid."' WHERE id_groupe='".$current_id_groupe."';";
						//echo "$sql<br />";
						$update=mysqli_query($GLOBALS['mysqli'], $sql);
						if(!$update) {
							$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
						}
						else {
							$nb_reg++;
						}
					}
					else {
						// On vérifie que l'AID existe
						$sql="SELECT * FROM aid WHERE id='".$lig->id_aid."';";
						$test_aid=mysqli_query($GLOBALS['mysqli'], $sql);
						if(mysqli_num_rows($test_aid)==0) {
							$sql="UPDATE j_groupes_aid SET indice_aid='".$current_indice_aid."', id_aid='0' WHERE id_groupe='".$current_id_groupe."';";
							//echo "$sql<br />";
							$update=mysqli_query($GLOBALS['mysqli'], $sql);
							if(!$update) {
								$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
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
								$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
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
					$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid2($current_indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
				}
				else {
					$nb_reg++;
				}
			}
		}
	}
	if($nb_reg>0) {
		$msg.=$nb_reg." enregistrement(s) effectué(s) <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
	}
}


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
			$msg.="ANOMALIE&nbsp;: Le groupe n°".$id_groupe." n'est associé à aucune catégorie AID <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
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

			// Créer l'AID
			$sql="INSERT INTO aid SET id='".$aid_id."', 
							nom='".$nom_aid."', 
							indice_aid='".$indice_aid."';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS['mysqli'], $sql);
			if(!$insert) {
				$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
			}
			else {
				$msg.="AID $nom_aid créé.<br />";
				//$aid_id=mysqli_insert_id($GLOBALS['mysqli']);

				// Faire l'association
				$sql="UPDATE j_groupes_aid SET id_aid='".$aid_id."' WHERE id_groupe='".$id_groupe."';";
				//echo "$sql<br />";
				$update=mysqli_query($GLOBALS['mysqli'], $sql);
				if(!$update) {
					$msg.="Erreur lors de l'association du groupe avec la catégorie ".get_info_categorie_aid($indice_aid)." <em>(".strftime("Le %d/%m/%Y à %H:%M:%S").")</em>.<br />";
				}
				else {
					$msg.="AID $nom_aid associé à l'enseignement/groupe.<br />";
					// Remplir l'AID d'après les bulletins du groupe

					// Inscription des élèves
					$tab_ele=array();
					foreach($group["eleves"]["all"]["list"] as $current_login_ele) {
						$sql="INSERT INTO j_aid_eleves SET login='".$current_login_ele."', id_aid='$aid_id', indice_aid='$indice_aid'";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if (!$insert) {
							$msg.="Erreur lors de l'ajout de l'élève ".$current_login_ele."<br />";
						}
						else {
							$tab_ele[]=$current_login_ele;
						}
					}
					$msg.=count($tab_ele)." élève(s) associés à l'AID $nom_aid.<br />";

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
					$msg.=count($tab_prof)." professeur(s) associés à l'AID $nom_aid.<br />";

					// Remplissage d'après matieres_notes et matieres_appreciations
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
									$msg.="Erreur lors de l'enregistrement de la note pour ".$lig_note->login." en période ".$lig_note->periode."<br />";
								}
								else {
									$tab_aid_note[$lig_note->login][$lig_note->periode]="ok";
									$nb_notes++;
								}
							}
						}
					}
					$msg.=$nb_notes." note(s) enregistrée(s) <em>(toutes périodes confondues)</em>.<br />";

					$nb_app=0;
					$sql="SELECT * FROM matieres_appreciations WHERE id_groupe='".$id_groupe."';";
					//echo "$sql<br />";
					$res_app=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_app)>0) {
						while($lig_app=mysqli_fetch_object($res_app)) {
							if(in_array($lig_app->login, $tab_ele)) {
								if(!isset($tab_aid_app[$lig_app->login][$lig_app->periode])) {
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
										$msg.="Erreur lors de l'enregistrement de l'appréciation pour ".$lig_app->login." en période ".$lig_app->periode."<br />";
									}
									else {
										$nb_app++;
									}
								}
								else {
									$sql="UPDATE aid_appreciations SET appreciation='".mysqli_real_escape_string($GLOBALS["mysqli"], $lig_app->appreciation)."'login='".$lig_app->login."'
														WHERE id_aid='".$aid_id."' AND 
															periode='".$lig_app->periode."' AND 
															indice_aid='".$indice_aid."';";
									//echo "$sql<br />";
									$update=mysqli_query($GLOBALS["mysqli"], $sql);
									if (!$update) {
										$msg.="Erreur lors de l'enregistrement de l'appréciation pour ".$lig_app->login." en période ".$lig_app->periode."<br />";
									}
									else {
										$nb_app++;
									}
								}
							}
						}
					}
					$msg.=$nb_app." appréciation(s) enregistrée(s) <em>(toutes périodes confondues)</em>.<br />";
				}
			}
		}
	}
}
//=========================================================

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont été modifiées. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "AID : Transfert groupes";
//echo "<div class='noprint'>\n";
require_once("../lib/header.inc.php");
//echo "</div>\n";
//**************** FIN EN-TETE *****************

//debug_var();

echo "<p class='bold'><a href='../accueil.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Accueil</a>
 | <a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Index AID</a>
 | <a href='".$_SERVER['PHP_SELF']."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Rafraichir sans enregistrer, ni revalider la dernière action/création</a>";
//echo "</p>\n";

if(!isset($mode)) {
	echo "</p>\n";

	echo "<h2>Création/Remplissage d'AID d'après des enseignements/groupes</h2>
	<p style='margin-top:1em;margin-bottom:1em;'>Cette page est conçue pour remplir des AID d'après des enseignements dans le cadre des EPI, AP et Parcours.<br />
	Vous pouvez créer les Catégories d'AID préalablement, ou créer les catégories d'après des enseignements ci-dessous.<br />
	Une fois les catégories créées et associées aux enseignements, vous pourrez créer des AID dans les catégories et remplir ces AID avec les notes/appréciations saisies pour les bulletins.<br />
	&nbsp;<br />
	Combien de catégories&nbsp;?<br />
	Il convient de créer une catégorie AID par famille d'EPI, c'est-à-dire par type d'EPI, avec les mêmes caractéristiques, destinés à apparaître sur les bulletins dans les mêmes périodes.<br />
	Par exemple, deux EPI correspondant à une même famille devraient a priori être associés à une même catégorie AID,<br />
	sauf si ces EPI ne sont pas destinés à apparaitre sur les mêmes périodes sur les bulletins <em>(cas d'un EPI traité à deux périodes différentes sur des classes différentes)</em>.<br />
	Dans ce dernier cas, si ces enseignements ont les mêmes nom/description, vous devrez passer par la page classique de création de catégorie, plutôt que par les icones ci-dessous.</p>
	<p style='margin-top:1em;margin-bottom:1em;'>Les étapes sont les suivantes&nbsp;:</p>
	<ol>
		<li><a href='index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Créer les catégories dans la page principale des AID</a>,<br />ou <strong>les créer ci-dessous</strong> à l'aide des liens/icones <img src='../images/icons/wizard.png' class='icone16' alt='Créer' /> en entête des <strong>colonnes Catégorie AID</strong>.</li>
		<li>Associer les enseignements dont le <strong>nom (description)</strong> coïncide avec la catégorie AID en cliquant sur l'icone <img src='../images/icons/wizard.png' class='icone16' alt='Créer' /> en ligne d'entête <strong>Catégorie AID</strong>, puis en validant l'association.</li>
		<li>Créer les AID et les remplir d'après le contenu des bulletins en cliquant sur les icones <img src='../images/icons/wizard.png' class='icone16' alt='Créer' /> dans la colonne AID <em>(la création n'est possible qu'une fois l'association avec une catégorie AID effectuée)</em>.</li>
	</ol>

<p><em>Note&nbsp;:</em> Il est possible de trier le tableau en cliquant sur les colonnes.</p>";

	$tab_cat_aid=array();
	$sql="SELECT ac.*, gt.nom_court AS nom_court_type, gt.nom_complet AS nom_complet_type FROM aid_config ac, groupes_types gt WHERE ac.type_aid=gt.id ORDER BY gt.id, ac.nom;";
	//echo "$sql<br />";
	$res_aid=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_aid)==0) {
		echo "<p style='color:red'>Il n'existe aucune catégorie d'AID de type AP, EPI ou Parcours.</p>";
	}
	else {
		echo "<p style='margin-left:3em;text-indent:-3em;'>Les catégories d'AID existantes, associées à des AP, EPI ou Parcours sont&nbsp;:<br />";
		while($lig_aid=mysqli_fetch_assoc($res_aid)) {
			$tab_cat_aid[]=$lig_aid;
			echo "<strong>".$lig_aid["nom_court_type"]."&nbsp;:</strong> ".$lig_aid["nom"]." <em>(".$lig_aid["nom_complet"].")</em><br />";
		}
		echo "</p>";
	}

	$sql="SELECT g.id AS id_groupe, g.name, g.description, gt.* FROM groupes g, j_groupes_types jgt, groupes_types gt WHERE g.id=jgt.id_groupe AND gt.id=jgt.id_type AND jgt.id_type=gt.id ORDER BY gt.id, g.name, g.description;";
	//echo "$sql<br />";
	$res_grp=mysqli_query($GLOBALS['mysqli'], $sql);
	if(mysqli_num_rows($res_grp)==0) {
		echo "<p style='color:red'>Aucun enseignement/groupe n'est associé à un type EPI, AP ou Parcours.</p>";
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
					<th title=\"Trier par nom d'enseignement\">Nom</th>
					<th title=\"Trier par description d'enseignement\">Description</th>
					<th title=\"Trier par nom de classe\">Classes</th>
					<th title=\"Trier par type d'enseignement\">Type</th>
					<th colspan='3'>
						Catégorie AID
						<a href='#' onclick=\"select_auto_cat(); return false;\" title=\"Associer les groupes/enseignements aux catégories de mêmes nom/description.\"><img src='../images/icons/wizard.png' class='icone16' alt='Associer' /></a>
					</th>
					<th>
						AID
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
					<td id='td_name_".$cpt."'>".$lig_grp->name."</td>
					<td id='td_description_".$cpt."'>".stripslashes($lig_grp->description)."</td>
					<td>$chaine_classes</td>
					<td>$lig_grp->nom_court</td>
					<td>".(isset($tab_grp_aid[$lig_grp->id_groupe]) ? "<img src='../images/enabled.png' class='icone20' alt='Catégorie créée' title='Catégorie créée/associée' />" : "<a href='".$_SERVER['PHP_SELF']."?creer_categorie=".$lig_grp->id_groupe."&".add_token_in_url()."' title=\"Créer une catégorie AID d'après le nom de l'enseignement.\"><img src='../images/icons/wizard.png' class='icone16' alt='Créer' /></a>")."</td>
					<td>
						<select name='indice_aid[".$lig_grp->id_groupe."]' id='indice_aid_".$cpt."' style='width:20em;' onchange=\"changement(); document.getElementById('span_cat_$cpt').style.display='none';\">
							<option value=''></option>";
		for($loop=0;$loop<count($tab_cat_aid);$loop++) {
			$selected="";
			if((isset($tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]))&&($tab_cat_aid[$loop]["indice_aid"]==$tab_grp_aid[$lig_grp->id_groupe]["indice_aid"])) {
				$selected=" selected='true'";
			}
			echo "
							<option value='".$tab_cat_aid[$loop]["indice_aid"]."'".$selected.">".$tab_cat_aid[$loop]["nom"]." (".$tab_cat_aid[$loop]["nom_complet"].")</option>";
		}
		echo "
						</select>
					</td>
					<td>
						<span id='span_cat_$cpt'>".(isset($tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]) ? "<a href='config_aid.php?indice_aid=".$tab_grp_aid[$lig_grp->id_groupe]["indice_aid"]."' target='_blank' title=\"Voir la catégorie dans un nouvel onglet.\"><img src='../images/icons/chercher.png' class='icone16' alt='Voir' /></a>" : "")."</span>
					</td>
					<td>";
		if(isset($tab_grp_aid[$lig_grp->id_groupe]["id_aid"])) {
			if($tab_grp_aid[$lig_grp->id_groupe]["id_aid"]!=0) {
				echo "<span title=\"AID n°".$tab_grp_aid[$lig_grp->id_groupe]["id_aid"]."\">".get_info_aid($tab_grp_aid[$lig_grp->id_groupe]["id_aid"])."</span>";
			}
			else {
				echo "<a href='".$_SERVER['PHP_SELF']."?creer_aid=".$lig_grp->id_groupe."&".add_token_in_url()."' title=\"Créer et remplir un AID d'après l'enseignement.\"><img src='../images/icons/wizard.png' class='icone16' alt='Créer' /></a>";
			}
		}
		else {
			// On ne propose rien si aucune catégorie n'est présente.
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
	function select_auto_cat() {
		nb_assoc=0;
		for(i=0;i<$cpt;i++) {
			if(document.getElementById('td_name_'+i)) {
				for(j=0;j<document.getElementById('indice_aid_'+i).options.length;j++) {
					/*
					if(i<1) {
						alert('Option '+j+' value='+document.getElementById('indice_aid_'+i).options[j].value);
						alert('Option '+j+' innerHTML='+document.getElementById('indice_aid_'+i).options[j].innerHTML);
						alert('Contenu TD compilés='+document.getElementById('td_name_'+i).innerHTML+' ('+document.getElementById('td_description_'+i).innerHTML+')');
					}
					*/
					if(document.getElementById('indice_aid_'+i).options[j].innerHTML==document.getElementById('td_name_'+i).innerHTML+' ('+document.getElementById('td_description_'+i).innerHTML+')') {
						document.getElementById('indice_aid_'+i).selectedIndex=j;
						nb_assoc++;
					}
				}
			}
		}
		if(nb_assoc>0) {
			alert(\"N'oubliez pas de valider l'enregistrement des associations proposées.\");
		}
	}
</script>";
}
else {
	echo "<p style='color:red'>Mode non encore implémenté.</p>";

}
echo "<p><br /></p>\n";
require("../lib/footer.inc.php");
?>