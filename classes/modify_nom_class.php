<?php
/*
 *
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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


// Initialisations files
require_once("../lib/initialisations.inc.php");

extract($_GET, EXTR_OVERWRITE);
extract($_POST, EXTR_OVERWRITE);

// Resume session
$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
		header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
		die();
} else if ($resultat_session == '0') {
		header("Location: ../logout.php?auto=1");
		die();
}

if (!checkAccess()) {
		header("Location: ../logout.php?auto=1");
		die();
}

$msg = null;

$gepi_denom_mention=getSettingValue("gepi_denom_mention");
if($gepi_denom_mention=="") {
	$gepi_denom_mention="mention";
}

//debug_var();
if (isset($is_posted) and ($is_posted == '1')) {
	check_token();

	if (isset($display_rang)) $display_rang = 'y'; else $display_rang = 'n';
	if (isset($display_address)) $display_address = 'y'; else $display_address = 'n';
	if (isset($display_coef)) $display_coef = 'y'; else $display_coef = 'n';
	if (isset($display_mat_cat)) $display_mat_cat = 'y'; else $display_mat_cat = 'n';
	if (isset($display_nbdev)) $display_nbdev = 'y'; else $display_nbdev = 'n';
	if (isset($display_moy_gen)) $display_moy_gen = 'y'; else $display_moy_gen = 'n';
	if (isset($display_moy_gen_saisie_avis2)) $display_moy_gen_saisie_avis2 = 'y'; else $display_moy_gen_saisie_avis2 = 'n';

	//if (!isset($modele_bulletin)) $$modele_bulletin = 1;
	if (!isset($modele_bulletin)) {$modele_bulletin = 1;}

	// =========================
	// AJOUT: boireaus
	//rn_formule
	//rn_sign_nblig

	if(mb_strlen(preg_replace("/[0-9]/","",$rn_sign_nblig))!=0){$rn_sign_nblig=3;}

	if (isset($rn_nomdev)){$rn_nomdev='y';}else{$rn_nomdev='n';}
	if (isset($rn_toutcoefdev)){$rn_toutcoefdev='y';}else{$rn_toutcoefdev='n';}
	if (isset($rn_coefdev_si_diff)){$rn_coefdev_si_diff='y';}else{$rn_coefdev_si_diff='n';}
	if (isset($rn_datedev)){$rn_datedev='y';}else{$rn_datedev='n';}
	if (isset($rn_sign_chefetab)){$rn_sign_chefetab='y';}else{$rn_sign_chefetab='n';}
	if (isset($rn_sign_pp)){$rn_sign_pp='y';}else{$rn_sign_pp='n';}
	if (isset($rn_sign_resp)){$rn_sign_resp='y';}else{$rn_sign_resp='n';}
	if (isset($rn_abs_2)){$rn_abs_2='y';}else{$rn_abs_2='n';}
	
	// =========================

	// Mod ECTS
	if (!isset($ects_type_formation)) $ects_type_formation = '';
	if (!isset($ects_parcours)) $ects_parcours = '';
	if (!isset($ects_code_parcours)) $ects_code_parcours = '';
	if (!isset($ects_domaines_etude)) $ects_domaines_etude = '';
	if (!isset($ects_fonction_signataire_attestation)) $ects_fonction_signataire_attestation = '';

	// =========================
	if (!isset($rn_type_par_defaut)) $rn_type_par_defaut = "html";
	// 20121027
	// Param??tres enregistr??s dans la table 'classes_param':
	if (!isset($rn_aff_classe_nom)) $rn_aff_classe_nom = 1;

	// 20191015 : Martial Lenzen
	if (!isset($rn_aff_nomdev_choix)) $rn_aff_nomdev_choix = 2;

	// A MODIFIER EN CAS DE MODE CNIL STRICT
	if (!isset($rn_app)) $rn_app = 'n';
	if (!isset($rn_moy_classe)) $rn_moy_classe = 'n';
	if (!isset($rn_moy_min_max_classe)) $rn_moy_min_max_classe = 'n';
	if (!isset($rn_retour_ligne)) $rn_retour_ligne = 'n';
	if (!isset($rn_rapport_standard_min_font)) $rn_rapport_standard_min_font = 3;
	if (!isset($rn_adr_resp)) $rn_adr_resp = 'n';
	if (!isset($rn_bloc_obs)) $rn_bloc_obs = 'n';
	if (!isset($rn_col_moy)) $rn_col_moy = 'n';
	if (!isset($rn_moy_gen)) $rn_moy_gen = 'n';

	if (!isset($type_classe)) $type_classe = 'standard';

	// =========================

	if (isset($id_classe)) {
		if ($reg_class_name) {
			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat' WHERE id = '$id_classe'");
			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat', display_nbdev ='$display_nbdev' WHERE id = '$id_classe'");

			//$register_class = mysql_query("UPDATE classes SET classe='$reg_class_name', nom_complet='$reg_nom_complet', suivi_par='$reg_suivi_par', formule= '$reg_formule', format_nom='$reg_format', display_rang='$display_rang', display_address='$display_address', display_coef='$display_coef', display_mat_cat ='$display_mat_cat', display_nbdev ='$display_nbdev',display_moy_gen='$display_moy_gen' WHERE id = '$id_classe'");

			$register_class = mysqli_query($GLOBALS["mysqli"], "UPDATE classes SET classe='$reg_class_name',
													nom_complet='$reg_nom_complet',
													suivi_par='$reg_suivi_par',
													formule= '".html_entity_decode($reg_formule)."',
													format_nom='$reg_format',
													format_nom_eleve='$reg_elformat',
													display_rang='$display_rang',
													display_address='$display_address',
													display_coef='$display_coef',
													display_mat_cat ='$display_mat_cat',
													display_nbdev ='$display_nbdev',
													display_moy_gen='$display_moy_gen',
													modele_bulletin_pdf='$modele_bulletin',
													rn_nomdev='$rn_nomdev',
													rn_toutcoefdev='$rn_toutcoefdev',
													rn_coefdev_si_diff='$rn_coefdev_si_diff',
													rn_datedev='$rn_datedev',
													rn_sign_chefetab='$rn_sign_chefetab',
													rn_sign_pp='$rn_sign_pp',
													rn_sign_resp='$rn_sign_resp',
													rn_sign_nblig='$rn_sign_nblig',
													rn_formule='$rn_formule',
													rn_abs_2='$rn_abs_2',
													ects_type_formation='".$ects_type_formation."',
													ects_parcours='".$ects_parcours."',
													ects_code_parcours='".$ects_code_parcours."',
													ects_domaines_etude='".$ects_domaines_etude."',
													ects_fonction_signataire_attestation='".$ects_fonction_signataire_attestation."'
												WHERE id = '$id_classe'");

			//==================================
			// AJOUT 20191015 : lenzenm
			// sauvegarder le choix du nom long ou court pour les devoirs dans la table 'classes_param' au lieu de 'classes' :
			$register_class2 = saveParamClasse($id_classe, 'rn_aff_nomdev_choix', $rn_aff_nomdev_choix);
			if (!$register_class2) {$reg_ok = 'no';} else {$reg_ok = 'yes' ;}
			// Fin ajout
			//==================================

			if (!$register_class || $reg_ok=='no') {
					$msg .= "Une erreur s'est produite lors de la modification de la classe.";
					} else {
					$msg .= "La classe a bien ??t?? modifi??e.";
			}

			// On enregistre les infos relatives aux cat??gories de mati??res
			$tab_priorites_categories=array();
			$temoin_pb_ordre_categories="n";
			$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, priority FROM matieres_categories");
			while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
				//echo $row['nom_court']." : ";
				$reg_priority = $_POST['priority_'.$row["id"]];
				if (isset($_POST['moyenne_'.$row["id"]])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
				if (!is_numeric($reg_priority)) $reg_priority = 0;
				if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;
				//echo "$reg_priority -&gt; ";
				if(in_array($reg_priority, $tab_priorites_categories)) {
					$temoin_pb_ordre_categories="y";
					$reg_priority=max($tab_priorites_categories)+1;
				}
				$tab_priorites_categories[]=$reg_priority;
				//echo "$reg_priority<br />";
				//$test = old_mysql_result(mysql_query("select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')"), 0);

				$res_test=mysqli_query($GLOBALS["mysqli"], "select count(classe_id) FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] . "' and classe_id = '" . $id_classe . "')");
				$test = old_mysql_result($res_test, 0);

				if ($test == 0) {
					// Pas d'entr??e... on cr????
					$sql="INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "';";
				} else {
					// Entr??e existante, on met ?? jour
					$sql="UPDATE j_matieres_categories_classes SET priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "' WHERE (classe_id = '" . $id_classe . "' and categorie_id = '" . $row["id"] . "');";
				}
				//echo "$sql<br />";
				$res = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$res) {
					$msg .= "<br/>Une erreur s'est produite lors de l'enregistrement des donn??es de cat??gorie.";
				}
			}
			if($temoin_pb_ordre_categories=="y") {
				$msg.="<br /><strong>Anomalie&nbsp;:</strong> Les cat??gories de mati??res ne doivent pas avoir le m??me rang.<br />Cela risque de provoquer des probl??mes sur les bulletins.<br />Des mesures ont ??t?? prises pour imposer des ordres diff??rents, mais il se peut que l'ordre ne vous convienne pas.<br />\n";
			}

			// =========================
			// 20151015 (ajout du param??tre rn_aff_nomdev_choix)
			$tab_param=array('rn_aff_classe_nom', 'rn_aff_nomdev_choix', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy', 'rn_type_par_defaut', 'bull_prefixe_periode', 'gepi_prof_suivi', 'suivi_par_alt', 'suivi_par_alt_fonction', 'type_classe', 'display_moy_gen_saisie_avis2', 'rn_moy_gen');
			for($loop=0;$loop<count($tab_param);$loop++) {
				$tmp_name=$tab_param[$loop];
				if(!saveParamClasse($id_classe, $tmp_name, $$tmp_name)) {
					$msg.="<br />Erreur lors de l'enregistrement de $tmp_name.";
				}
			}
			// =========================


		} else {
		$msg .= "Veuillez pr??ciser le nom de la classe !";
		}
	} else {
		if ($reg_class_name) {
			//$register_class = mysql_query("INSERT INTO classes SET classe = '$reg_class_name', nom_complet = '$reg_nom_complet', suivi_par = '$reg_suivi_par', formule = '$reg_formule', format_nom = '$reg_format', display_rang = '$display_rang', display_address = '$display_address', display_coef = '$display_coef', display_mat_cat = '$display_mat_cat'");
			//$register_class = mysql_query("INSERT INTO classes SET classe = '$reg_class_name', nom_complet = '$reg_nom_complet', suivi_par = '$reg_suivi_par', formule = '$reg_formule', format_nom = '$reg_format', display_rang = '$display_rang', display_address = '$display_address', display_coef = '$display_coef', display_mat_cat = '$display_mat_cat', display_nbdev ='$display_nbdev'");
			$register_class = mysqli_query($GLOBALS["mysqli"], "INSERT INTO classes SET classe = '$reg_class_name',
														nom_complet = '$reg_nom_complet',
														suivi_par = '$reg_suivi_par',
														formule = '$reg_formule',
														format_nom = '$reg_format',
														format_nom_eleve = '$reg_elformat',
														display_rang = '$display_rang',
														display_address = '$display_address',
														display_coef = '$display_coef',
														display_mat_cat = '$display_mat_cat',
														display_nbdev ='$display_nbdev',
														display_moy_gen='$display_moy_gen',
														modele_bulletin_pdf='$modele_bulletin',
														rn_nomdev='$rn_nomdev',
														rn_toutcoefdev='$rn_toutcoefdev',
														rn_coefdev_si_diff='$rn_coefdev_si_diff',
														rn_datedev='$rn_datedev',
														rn_abs_2='$rn_abs_2',
														rn_sign_chefetab='$rn_sign_chefetab',
														rn_sign_pp='$rn_sign_pp',
														rn_sign_resp='$rn_sign_resp',
														rn_sign_nblig='$rn_sign_nblig',
														rn_formule='$rn_formule',
														ects_type_formation='".$ects_type_formation."',
														ects_parcours='".$ects_parcours."',
														ects_code_parcours='".$ects_code_parcours."',
														ects_domaines_etude='".$ects_domaines_etude."',
														ects_fonction_signataire_attestation='".$ects_fonction_signataire_attestation."'
													");

			//if (!$register_class || $reg_ok=='no') {
			if (!$register_class) {
				$msg .= "Une erreur s'est produite lors de l'enregistrement de la nouvelle classe.";
			} else {
				$msg .= "La nouvelle classe a bien ??t?? enregistr??e.";
				$id_classe = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);

				//==============================
				// AJOUT 20191015
				// sauvegarder le choix du nom long ou court pour les devoirs dans la table 'classes_param' au lieu de 'classes' :
				$register_class2 = saveParamClasse($id_classe, 'rn_aff_nomdev_choix', $rn_aff_nomdev_choix);
				if (!$register_class2) {$reg_ok = 'no';} else {$reg_ok = 'yes' ;}
				// Fin de l'ajout
				//==============================

				// On enregistre les infos relatives aux cat??gories de mati??res
				$tab_priorites_categories=array();
				$temoin_pb_ordre_categories="n";
				$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, priority FROM matieres_categories");
				while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
					$reg_priority = $_POST['priority_'.$row["id"]];
					if (isset($_POST['moyenne_'.$row["id"]])) {$reg_aff_moyenne = 1;} else { $reg_aff_moyenne = 0;}
					if (!is_numeric($reg_priority)) $reg_priority = 0;
					if (!is_numeric($reg_aff_moyenne)) $reg_aff_moyenne = 0;

					if(in_array($reg_priority, $tab_priorites_categories)) {
						$temoin_pb_ordre_categories="y";
						$reg_priority=max($tab_priorites_categories)+1;
					}
					$tab_priorites_categories[]=$reg_priority;

					$res = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_matieres_categories_classes SET classe_id = '" . $id_classe . "', categorie_id = '" . $row["id"] . "', priority = '" . $reg_priority . "', affiche_moyenne = '" . $reg_aff_moyenne . "'");

					if (!$res) {
						$msg .= "<br/>Une erreur s'est produite lors de l'enregistrement des donn??es de cat??gorie.";
					}
				}

				if($temoin_pb_ordre_categories=="y") {
					$msg.="<br /><strong>Anomalie&nbsp;:</strong> Les cat??gories de mati??res ne doivent pas avoir le m??me rang.<br />Cela risque de provoquer des probl??mes sur les bulletins.<br />Des mesures ont ??t?? prises pour imposer des ordres diff??rents, mais il se peut que l'ordre ne vous convienne pas.<br />\n";
				}

				$sql="SELECT login FROM utilisateurs WHERE etat='actif' AND statut='scolarite';";
				$res_scol=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_scol)>0) {
					$nb_scol=0;
					while($lig_scol=mysqli_fetch_object($res_scol)) {
						$sql="INSERT INTO j_scol_classes SET login='$lig_scol->login', id_classe='$id_classe';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="<br />Erreur lors de l'association du compte $lig_scol->login avec la classe.";
						}
						else {
							$nb_scol++;
						}
					}
					if($nb_scol==1) {
						$msg.="<br />Un compte scolarit?? associ?? avec la classe.";
					}
					if($nb_scol>1) {
						$msg.="<br />$nb_scol comptes scolarit?? associ??s avec la classe.";
						$msg.="<br />Pour modifier la liste des comptes associ??s, suivez <a href='./scol_resp.php'>ce lien</a>.";
					}
				}

				// =========================
				// 20191015 : Martial Lenzen
				// (ajout du param??tre rn_aff_nomdev_choix)
				$tab_param=array('rn_aff_classe_nom', 'rn_aff_nomdev_choix', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy', 'rn_type_par_defaut', 'bull_prefixe_periode', 'gepi_prof_suivi', 'suivi_par_alt', 'suivi_par_alt_fonction', 'type_classe', 'display_moy_gen_saisie_avis2', 'rn_moy_gen');
				for($loop=0;$loop<count($tab_param);$loop++) {
					$tmp_name=$tab_param[$loop];
					if(!saveParamClasse($id_classe, $tmp_name, $$tmp_name)) {
						$msg.="<br />Erreur lors de l'enregistrement de $tmp_name.";
					}
				}
				// =========================
			}

		} else {
		$msg .= "Veuillez pr??ciser le nom de la classe !";
		}
	}
}


$themessage  = 'Des informations ont ??t?? modifi??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Gestion des classes | Modifier les param??tres";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ***************************

//debug_var();

$id_class_prec=0;
$id_class_suiv=0;

$chaine_options_classes="";
if (isset($id_classe)) {
	// =================================
	// AJOUT: boireaus
	//$chaine_options_classes="";
	$sql="SELECT id, classe FROM classes ORDER BY classe";
	$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0){
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;

		$cpt_classe=0;
		$num_classe=-1;

		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
			if($lig_class_tmp->id==$id_classe){
				// Index de la classe dans les <option>
				$num_classe=$cpt_classe;

				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";
			}

			if($temoin_tmp==0){
				$id_class_prec=$lig_class_tmp->id;
			}

			$cpt_classe++;
		}
	}
	// =================================
}

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";

echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
if($id_class_prec!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe pr??c??dente</a>";}

if($chaine_options_classes!="") {

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_classe(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form1.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form1.submit();
			}
			else{
				document.getElementById('id_classe').selectedIndex=$num_classe;
			}
		}
	}
</script>\n";


	echo " | <select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
	echo $chaine_options_classes;
	echo "</select>\n";
}

if($id_class_suiv!=0){echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv' onclick=\"return confirm_abandon (this, change, '$themessage')\">Classe suivante</a>";}

//=========================
// On ne propose l'infobulle de navigation que pour une classe d??j?? existante.
$ouvrir_infobulle_nav="n";
if(isset($id_classe)) {
	$titre="Navigation";
	$texte="";
	$texte.="<img src='../images/icons/date.png' alt='' /> <a href='periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">P??riodes</a><br />";
	include "../lib/periodes.inc.php";
	if($nb_periode>1) {
		// On a $nb_periode = Nombre de p??riodes + 1
		$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">??l??ves</a><br />";
	}
	$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
	$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifi??e</a><br />";
	//$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Param??tres</a>";
	
	$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");
	
	if($ouvrir_infobulle_nav=="y") {
		$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;' title=\"Ce cadre de navigation est ouvert par d??faut au chargement de la page.\n\nCliquez pour ne pas ouvrir ce cadre par d??faut au chargement de la page.\"><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a></div>\n";
	}
	else {
		$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;' title=\"Ce cadre de navigation n'est pas ouvert par d??faut au chargement de la page.\n\nCliquez pour ouvrir ce cadre par d??faut au chargement de la page.\"><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a></div>\n";
	}

	$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

	$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y', 'y', 'n', 'n');
	
	echo " | <a href='#' onclick=\"afficher_div('navigation_classe', 'y',-100,20);\"";
	echo ">";
	echo "Navigation";
	echo "</a>";
}
//=========================

echo "</p>\n";
echo "</form>\n";

if(getSettingValue('GepiAdminImprBulSettings')!='yes') {
	echo "<p><b>Remarque&nbsp;: </b>Connectez vous avec un compte ayant le statut \"scolarit??\" pour ??diter les bulletins et avoir acc??s ?? d'autres param??tres d'affichage.</p>\n";
}

if (isset($id_classe)) {

	$call_nom_class = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id = '$id_classe'");

	if(mysqli_num_rows($call_nom_class)==0) {
		echo "<p>L'identifiant de classe '$id_classe' est inconnu.</p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$classe = old_mysql_result($call_nom_class, 0, 'classe');
	$nom_complet = old_mysql_result($call_nom_class, 0, 'nom_complet');
	$suivi_par = old_mysql_result($call_nom_class, 0, 'suivi_par');
	$formule = old_mysql_result($call_nom_class, 0, 'formule');
	$format_nom = old_mysql_result($call_nom_class, 0, 'format_nom');
	$format_nom_eleve = old_mysql_result($call_nom_class, 0, 'format_nom_eleve');
	if(!in_array($format_nom_eleve, array('np', 'pn'))) {
		$format_nom_eleve="np";
	}
	$display_rang = old_mysql_result($call_nom_class, 0, 'display_rang');
	$display_address = old_mysql_result($call_nom_class, 0, 'display_address');
	$display_coef = old_mysql_result($call_nom_class, 0, 'display_coef');
	$display_mat_cat = old_mysql_result($call_nom_class, 0, 'display_mat_cat');
	$display_nbdev = old_mysql_result($call_nom_class, 0, 'display_nbdev');
	$display_moy_gen = old_mysql_result($call_nom_class, 0, 'display_moy_gen');
	$modele_bulletin_pdf = old_mysql_result($call_nom_class, 0, 'modele_bulletin_pdf');

	// =========================
	$rn_nomdev=old_mysql_result($call_nom_class, 0, 'rn_nomdev');
	$rn_toutcoefdev=old_mysql_result($call_nom_class, 0, 'rn_toutcoefdev');
	$rn_coefdev_si_diff=old_mysql_result($call_nom_class, 0, 'rn_coefdev_si_diff');
	$rn_datedev=old_mysql_result($call_nom_class, 0, 'rn_datedev');
	$rn_formule=old_mysql_result($call_nom_class, 0, 'rn_formule');
	$rn_sign_chefetab=old_mysql_result($call_nom_class, 0, 'rn_sign_chefetab');
	$rn_sign_pp=old_mysql_result($call_nom_class, 0, 'rn_sign_pp');
	$rn_sign_resp=old_mysql_result($call_nom_class, 0, 'rn_sign_resp');
	$rn_sign_nblig=old_mysql_result($call_nom_class, 0, 'rn_sign_nblig');

	//$rn_col_moy=old_mysql_result($call_nom_class, 0, 'rn_col_moy');
	// =========================
	$rn_abs_2=old_mysql_result($call_nom_class, 0, 'rn_abs_2');
	//=========================
	// Ajout : Module ECTS
	$ects_type_formation = old_mysql_result($call_nom_class, 0, 'ects_type_formation');
	$ects_parcours = old_mysql_result($call_nom_class, 0, 'ects_parcours');
	$ects_code_parcours = old_mysql_result($call_nom_class, 0, 'ects_code_parcours');
	$ects_fonction_signataire_attestation = old_mysql_result($call_nom_class, 0, 'ects_fonction_signataire_attestation');
	$ects_domaines_etude = old_mysql_result($call_nom_class, 0, 'ects_domaines_etude');
	// =========================
	// 20191015 : Martial Lenzen (pour le param??tre rn_aff_nomdev_choix)
	// Param??tres enregistr??s dans la table 'classes_param':
	$tab_param=array('rn_aff_classe_nom', 'rn_aff_nomdev_choix', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy', 'rn_type_par_defaut', 'bull_prefixe_periode', 'gepi_prof_suivi', 'suivi_par_alt', 'suivi_par_alt_fonction', 'type_classe', 'display_moy_gen_saisie_avis2', 'rn_moy_gen');
	for($loop=0;$loop<count($tab_param);$loop++) {
		$tmp_name=$tab_param[$loop];
		$$tmp_name=getParamClasse($id_classe, $tmp_name, "");
	}
	if($rn_type_par_defaut=="") {$rn_type_par_defaut="html";}
	if($rn_aff_classe_nom=="") {$rn_aff_classe_nom=1;}

	// 20191015 : Martial Lenzen
	//if($rn_aff_nomdev_choix=="") {$rn_aff_nomdev_choix=2;}

	if($rn_rapport_standard_min_font=="") {$rn_rapport_standard_min_font=3;}
	if($bull_prefixe_periode=="") {$bull_prefixe_periode="Bulletin du ";}
	if($type_classe=="") {$type_classe="standard";}
	// =========================

} else {
	$classe = '';
	$nom_complet = '';
	$suivi_par = '';
	$formule = '';
	//$format_nom = 'np';
	$format_nom = 'cni';
	$format_nom_eleve = 'np';
	$display_rang = 'n';
	$display_address = 'n';
	$display_coef = 'n';
	$display_mat_cat = 'n';
	$display_nbdev = 'n';
	$display_moy_gen = 'n';
	$modele_bulletin_pdf = NULL;

	// =========================
	$rn_nomdev='n';

	$rn_aff_nomdev_choix='2';  // AJOUT 20151015 : lenzenm 
	// A VERIFIER: La valeur par d??faut devrait ??tre la valeur historique

	$rn_toutcoefdev='n';
	$rn_coefdev_si_diff='n';
	$rn_datedev='n';
	$rn_formule='';
	$rn_sign_chefetab='n';
	$rn_sign_pp='n';
	$rn_sign_resp='n';
	$rn_sign_nblig=3;

	$rn_col_moy="n";
	$rn_moy_gen='n';
	// =========================
	$rn_abs_2='n';
	// =========================
	$display_moy_gen_saisie_avis2='y';

	$suivi_par_alt='';
	$suivi_par_alt_fonction='';
	// 20151015 (pour le param??tre rn_aff_nomdev_choix)
	$tab_param=array('rn_aff_classe_nom', 'rn_aff_nomdev_choix', 'rn_app', 'rn_moy_classe', 'rn_moy_min_max_classe', 'rn_retour_ligne', 'rn_rapport_standard_min_font', 'rn_adr_resp', 'rn_bloc_obs', 'rn_col_moy', 'rn_type_par_defaut', 'bull_prefixe_periode', 'gepi_prof_suivi', 'type_classe', 'rn_moy_gen');
	for($loop=0;$loop<count($tab_param);$loop++) {
		$tmp_name=$tab_param[$loop];
		/*
			$$tmp_name=getParamClasse($id_classe, $tmp_name, "");
			echo "";
		*/
		$$tmp_name="n";
	}
	if($rn_type_par_defaut=="") {$rn_type_par_defaut="html";}
	if($rn_aff_classe_nom=="") {$rn_aff_classe_nom=1;}

	// 20191009 : Martial Lenzen
	//if($rn_aff_nomdev_choix=="") {$rn_aff_nomdev_choix=2;}

	if($rn_rapport_standard_min_font=="") {$rn_rapport_standard_min_font=3;}
	if($bull_prefixe_periode=="") {$bull_prefixe_periode="Bulletin du ";}
	if($type_classe=="") {$type_classe="standard";}
	// =========================

	// Mod ECTS
	$ects_type_formation = '';
	$ects_parcours = '';
	$ects_code_parcours = '';
	$ects_fonction_signataire_attestation = '';
	$ects_domaines_etude = '';
}

if(isset($id_classe)) {
	echo "<div style='float:right; width:30em; font-size:x-small;'>".affiche_tableau_resp_classe($id_classe)."</div>";
}
?>
<form enctype="multipart/form-data" action="modify_nom_class.php" method="post">
<?php
echo add_token_field();
?>
<p>Nom court de la classe&nbsp;: <input type=text size=30 name=reg_class_name value = "<?php echo $classe; ?>" onchange='changement()' /></p>
<p>Nom complet de la classe&nbsp;: <input type=text size=50 name=reg_nom_complet value = "<?php echo $nom_complet; ?>"  onchange='changement()' /></p>

<p style='margin-top:1em;'>Pr??nom et nom du signataire des bulletins<?php if ($gepiSettings['active_mod_ects'] == "y") echo " et des attestations ECTS" ?> (<em>chef d'??tablissement ou son repr??sentant</em>)&nbsp;: <br /><input type=text size=30 name=reg_suivi_par value = "<?php echo $suivi_par; ?>"  onchange='changement()' /></p>
<?php
if ($gepiSettings['active_mod_ects'] == "y") {
?>
<p>Fonction du signataire sus-nomm?? (<em>ex.: "Proviseur"</em>)&nbsp;: <br /><input type="text" size="40" name="ects_fonction_signataire_attestation" value="<?php echo $ects_fonction_signataire_attestation;?>" onchange='changement()' /></p>
<?php
}

if(isset($id_classe)) {
	$gepi_prof_suivi_classe=getParamClasse($id_classe, 'gepi_prof_suivi', getSettingValue('gepi_prof_suivi'));
}
else {
	$gepi_prof_suivi_classe=getSettingValue('gepi_prof_suivi');
}
?>
<p>Formule ?? ins??rer sur les bulletins (<em>cette formule sera suivie des nom et pr??nom de la personne d??sign??e ci_dessus</em>)&nbsp;:<br /> <input type=text size=80 name=reg_formule value = "<?php echo $formule; ?>"  onchange='changement()' /></p>

<p style='margin-top:1em;'>D??signation alternative de la personne suivant la classe (<em>chef d'??tablissement ou son repr??sentant</em>) pouvant ??tre utilis??e dans des publipostages OOo&nbsp;: <br />
<input type='text' size='30' name='suivi_par_alt' value = "<?php echo $suivi_par_alt; ?>"  onchange='changement()' /><br />
Fonction associ??e (<em>chef, adjoint</em>)&nbsp;:<br />
<input type='text' size='30' name='suivi_par_alt_fonction' value = "<?php echo $suivi_par_alt_fonction; ?>"  onchange='changement()' />
</p>


<p style='margin-top:1em;'><b>D??nomination du professeur charg?? du suivi des ??l??ves&nbsp;:</b><br />
<input type="text" name="gepi_prof_suivi" id='gepi_prof_suivi' value="<?php echo $gepi_prof_suivi_classe; ?>" onchange='changement()' />
</p>

<p style='margin-top:1em;'><b>Formatage de l'identit?? des professeurs pour les bulletins&nbsp;:</b>
<br /><br />
<input type="radio" name="reg_format" id='reg_format_np' value="<?php echo "np"; ?>" <?php if ($format_nom=="np") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_np' style='cursor: pointer;'>Nom Pr??nom (<em>Durand Albert</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_pn' value="<?php echo "pn"; ?>" <?php if ($format_nom=="pn") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_pn' style='cursor: pointer;'>Pr??nom Nom (<em>Albert Durand</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_in' value="<?php echo "in"; ?>" <?php   if ($format_nom=="in") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_in' style='cursor: pointer;'>Initiale-Pr??nom Nom (<em>A. Durand</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_ni' value="<?php echo "ni"; ?>" <?php   if ($format_nom=="ni") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_ni' style='cursor: pointer;'>Initiale-Pr??nom Nom (<em>Durand A.</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_cnp' value="<?php echo "cnp"; ?>" <?php   if ($format_nom=="cnp") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_cnp' style='cursor: pointer;'>Civilit?? Nom Pr??nom (<em>M. Durand Albert</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_cpn' value="<?php echo "cpn"; ?>" <?php   if ($format_nom=="cpn") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_cpn' style='cursor: pointer;'>Civilit?? Pr??nom Nom (<em>M. Albert Durand</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_cin' value="<?php echo "cin"; ?>" <?php   if ($format_nom=="cin") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_cin' style='cursor: pointer;'>Civ. initiale-Pr??nom Nom (<em>M. A. Durand</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_cni' value="<?php echo "cni"; ?>" <?php   if ($format_nom=="cni") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_cni' style='cursor: pointer;'>Civ. Nom initiale-Pr??nom  (<em>M. Durand A.</em>)</label>
<br />
<input type="radio" name="reg_format" id='reg_format_cn' value="<?php echo "cn"; ?>" <?php   if ($format_nom=="cn") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_format_cn' style='cursor: pointer;'>Civ. Nom  (<em>M. Durand</em>)</label>

<p style='margin-top:1em;'><b>Formatage de l'identit?? des ??l??ves pour les bulletins&nbsp;:</b>
<br /><br />
<input type="radio" name="reg_elformat" id='reg_elformat_np' value="<?php echo "np"; ?>" <?php if ($format_nom_eleve=="np") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_elformat_np' style='cursor: pointer;'>Nom Pr??nom (<em>Durand Albert</em>)</label>
<br />
<input type="radio" name="reg_elformat" id='reg_elformat_pn' value="<?php echo "pn"; ?>" <?php if ($format_nom_eleve=="pn") echo " checked='checked ' "; ?> onchange='changement()' />
<label for='reg_elformat_pn' style='cursor: pointer;'>Pr??nom Nom (<em>Albert Durand</em>)</label>

<input type=hidden name=is_posted value=1 />
<?php
	if (isset($id_classe)) {echo "<input type=hidden name=id_classe value=$id_classe />";} 

	$checked_type_classe_standard=" checked";
	$checked_type_classe_non_sconet="";
	if($type_classe=="non_sconet") {
		$checked_type_classe_standard="";
		$checked_type_classe_non_sconet=" checked";
	}
?>

<br />
<a name='type_de_classe'></a><p style='margin-top:1em;' title="Certaines classes particuli??res n'existent pas dans Sconet.
Leur prise en compte dans des modules comme LSU peut perturber le fonctionnement du module.
D??clarez ces classes comme non standard/non Sconet permet de ne pas les prendre en compte dans ces modules.
Actuellement, le param??tre n'est pris en compte que pour le module LSU de Gepi."><b>Type de classe&nbsp;:</b><br />
<input type="radio" name="type_classe" id='type_classe_standard' value="standard" onchange='changement()'<?php echo $checked_type_classe_standard;?> /><label for='type_classe_standard'> classe standard <em>(Sconet)</em></label><br />
<input type="radio" name="type_classe" id='type_classe_non_sconet' value="non_sconet" onchange='changement()'<?php echo $checked_type_classe_non_sconet;?> /><label for='type_classe_non_sconet'> classe non standard <em>(hors Sconet,...)</em></label>
</p>

<br />
<br />
<!-- ========================================= -->
<style type='text/css'>
td {
	vertical-align:top;
}
</style>
<table style="border: 0;" cellpadding="5" cellspacing="5">
<tr>
	<td colspan='3'>
		<h2><b>Param??tres g??n??raux&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_mat_cat' style='cursor: pointer;'>Afficher les cat??gories de mati??res sur le bulletin (HTML), les relev??s de notes (HTML), et les outils de visualisation&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_mat_cat" id="display_mat_cat"  <?php   if ($display_mat_cat=="y") echo " checked='checked ' "; ?> onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Param??trage des cat??gories de mati??re pour cette classe<br />
		(<em>cet ordre n'est pris en compte dans les bulletins HTML que si case ci-dessus coch??e<br />En revanche, pour les bulletins PDF, l'ordre est pris en compte si vous choisissez un mod??le avec affichage des cat??gories de mati??res<em>)
	</td>
	<td>
		<table style='border: 1px solid black;'>
		<tr>
			<td style='width: auto; vertical-align:middle;'>Cat??gorie</td><td style='width: 100px; text-align: center; vertical-align:middle;'>Priorit?? d'affichage</td><td style='width: 100px; text-align: center; vertical-align:middle;'>Afficher la moyenne sur le bulletin</td>
		</tr>
		<?php
		$max_priority_cat=0;
		$get_max_cat = mysqli_query($GLOBALS["mysqli"], "SELECT priority FROM matieres_categories ORDER BY priority DESC LIMIT 1");
		if(mysqli_num_rows($get_max_cat)>0) {
			$max_priority_cat=old_mysql_result($get_max_cat, 0, "priority");
		}

		$tab_priorites_categories=array();
		$temoin_pb_ordre_categories="n";
		$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court, priority FROM matieres_categories");
		while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
			// Pour la cat??gorie, on r??cup??re les infos d??j?? enregistr??es pour la classe
			if (isset($id_classe)) {
				$sql="SELECT priority, affiche_moyenne FROM j_matieres_categories_classes WHERE (categorie_id = '" . $row["id"] ."' and classe_id = '" . $id_classe . "');";
				//echo "$sql<br />";
				$res_cat_classe=mysqli_query($GLOBALS["mysqli"], $sql);
				$infos = mysqli_fetch_object($res_cat_classe);
			} else {
				$infos = false;
			}

			if (!$infos) {
				$current_priority = $row["priority"];
				$current_affiche_moyenne = "0";
			} else {
				$current_priority = $infos->priority;
				$current_affiche_moyenne = $infos->affiche_moyenne;
				//echo $row["nom_court"]." -&gt; $current_priority<br />";
			}
			if(in_array($current_priority, $tab_priorites_categories)) {
				$temoin_pb_ordre_categories="y";
			}
			$tab_priorites_categories[]=$current_priority;

			echo "<tr>\n";
			echo "<td style='padding: 5px;'>".$row["nom_court"]."</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
					echo "<select name='priority_".$row["id"]."' size='1' onchange='changement()'>\n";
					for ($i=0;$i<max(100,$max_priority_cat);$i++) {
						echo "<option value='$i'";
						if ($current_priority == $i) echo " SELECTED";
						echo ">$i</option>\n";
					}
					echo "</select>\n";
			echo "</td>\n";
			echo "<td style='padding: 5px; text-align: center;'>\n";
				echo "<input type='checkbox' name='moyenne_".$row["id"]."'";
				if ($current_affiche_moyenne == '1') echo " CHECKED";
				echo " onchange='changement()' />\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		if($temoin_pb_ordre_categories=="y") {
			echo "<tr><td colspan='3' style='color:red; text-indent:-6em;padding-left:6em;'><strong>Anomalie&nbsp;:</strong> Les cat??gories de mati??res ne doivent pas avoir le m??me rang.<br />Cela risque de provoquer des probl??mes sur les bulletins.</td></tr>\n";
		}

		?>
		</table>
	</td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
		<h2><b>Param??tres g??n??raux des bulletins&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps; width: 35%;">
		Pr??fixe du titre du bulletin&nbsp;:<br />
		(<em style="font-variant: small-caps;">Par d??faut, on a "<strong>Bulletin du </strong>" suivi du nom de la p??riode</em>)
	</td>
	<td><?php 
			echo "
		<input type='text' name='bull_prefixe_periode' id='bull_prefixe_periode' value=\"$bull_prefixe_periode\" onchange='changement()' />";
		?>
	</td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
		<h2><b>Param??tres bulletin HTML&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps; width: 35%;">
		<label for='display_rang' style='cursor: pointer;'>Afficher sur le bulletin le rang de chaque ??l??ve&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_rang" id="display_rang"  <?php   if ($display_rang=="y") echo " checked='checked ' "; ?>  onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_address' style='cursor: pointer;'>Afficher le bloc adresse du responsable de l'??l??ve&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_address" id="display_address"  <?php   if ($display_address=="y") echo " checked='checked ' "; ?>  onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_coef' style='cursor: pointer;'>Afficher les coefficients des mati??res (uniquement si au moins un coef diff??rent de 0)&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_coef" id="display_coef"  <?php   if ($display_coef=="y") echo " checked='checked ' "; ?>  onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_moy_gen' style='cursor: pointer;'>Afficher les moyennes g??n??rales sur les bulletins (uniquement si au moins un coef diff??rent de 0)&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_moy_gen" id="display_moy_gen"  <?php   if ($display_moy_gen=="y") echo " checked='checked ' "; ?> onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_moy_gen_saisie_avis2' style='cursor: pointer;'>Afficher les moyennes g??n??rales lors de la saisie des avis du conseil de classe<br /><em>(saisie avec affichage du bulletin simplifi??e)</em>&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_moy_gen_saisie_avis2" id="display_moy_gen_saisie_avis2"  <?php   if ($display_moy_gen_saisie_avis2!="n") echo " checked='checked ' "; ?> onchange='changement()' />
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='display_nbdev' style='cursor: pointer;'>Afficher le nombre de devoirs sur le bulletin&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="display_nbdev" id="display_nbdev"  <?php   if ($display_nbdev=="y") echo " checked='checked ' "; ?> onchange='changement()' />
	</td>
</tr>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
		<h2><b>Param??tres bulletin PDF&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		S??lectionner le mod??le de bulletin pour l'impression en PDF&nbsp;:
	</td>
	<td><?php
		// Pour la classe, quel est le mod??le de bulletin d??ja selectionn??
		$quel_modele=$modele_bulletin_pdf;


		// s??lection des mod??le des bulletins.
		//$requete_modele = mysql_query('SELECT id_model_bulletin, nom_model_bulletin FROM '.$prefix_base.'model_bulletin ORDER BY '.$prefix_base.'model_bulletin.nom_model_bulletin ASC');
		$requete_modele = mysqli_query($GLOBALS["mysqli"], "SELECT id_model_bulletin, valeur as nom_model_bulletin FROM ".$prefix_base."modele_bulletin WHERE nom='nom_model_bulletin' ORDER BY ".$prefix_base."modele_bulletin.valeur ASC;");
		if(mysqli_num_rows($requete_modele)==0) {
			echo "<p style='color:red'>ANOMALIE&nbsp;: Il n'existe aucun mod??le de bulletin PDF.";
			if($_SESSION['login']=='administrateur') {
				echo "Vous devriez effectuer/forcer une <a href='../utilitaires/maj.php'>mise ?? jour de la base</a> pour corriger.<br />Prenez tout de m??me soin de v??rifier que personne d'autre que vous n'est connect??.\n";
			}
			else {
				echo "Contactez l'administrateur pour qu'il effectue une mise ?? jour de la base.\n";
			}
			echo "</p>\n";
		}
		else {
			//echo $quel_modele;
			echo "<select tabindex=\"5\" name=\"modele_bulletin\" onchange='changement()'>";
			if ($quel_modele == NULL) {
			echo "<option value=\"NULL\" selected=\"selected\" >Aucun mod??le de s??lectionn??</option>";
			}
			while($donner_modele = mysqli_fetch_array($requete_modele)) {
				echo "<option value=\"".$donner_modele['id_model_bulletin']."\"";
				if($quel_modele==$donner_modele['id_model_bulletin']) {
					echo "selected=\"selected\"";
				}
				echo ">".ucfirst($donner_modele['nom_model_bulletin'])."</option>\n";
			}
			echo "</select>\n";
		}
		?>
	</td>
</tr>

<?php
if(isset($id_classe)) {
?>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps; vertical-align: top;">
		<?php echo ucfirst($gepi_denom_mention);?>s pouvant appara??tre dans l'avis du conseil de classe sur les bulletins&nbsp;:
	</td>
	<td><?php
		$sql="SELECT DISTINCT m.* FROM j_mentions_classes j, mentions m WHERE j.id_classe='$id_classe' AND j.id_mention=m.id ORDER BY j.ordre, m.mention;";
		//echo "$sql<br />\n";
		 $res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			echo "<p>Aucune $gepi_denom_mention n'est d??finie pour cette classe.</p>\n";
		}
		else {
			echo "<ol>\n";
			while($lig=mysqli_fetch_object($res)) {
				echo "<li>".$lig->mention."</li>\n";
			}
			echo "</ol>\n";
		}
		echo "<p><a href='../saisie/saisie_mentions.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Param??trer les ".$gepi_denom_mention."s</a></p>\n";
		?>
	</td>
</tr>
<?php
}
?>
<!-- ========================================= -->
<tr>
	<td colspan='3'>
		<h2><b>Param??tres des relev??s de notes&nbsp;: </b></h2>
	</td>
</tr>
<!--
Afficher le nom des devoirs.
Si le nom est affich??, choisir entre nom long et court.
Afficher tous les coefficients des devoirs.
Afficher les coefficients des devoirs si des coefficients diff??rents
> > sont pr??sents.
Afficher les dates des devoirs.
> >
> >Et
Afficher un texte... (correspondant ?? ta demande)
> >Et encore
Afficher une case pour la signature des parents/responsables
Afficher une case pour la signature du prof principal
Afficher une case pour la signature du chef d'??tablissement
-->

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Type de relev?? ?? produire par d??faut&nbsp;:<br />
		<em style='font-size:small'>(si plusieurs classes sont s??lectionn??es, c'est le type de la premi??re qui est propos?? par d??faut)</em>
	</td>
	<td>
		<input type="radio" value="html" name="rn_type_par_defaut" id="rn_type_par_defaut_html"  <?php   if ($rn_type_par_defaut!="pdf") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_type_par_defaut_html' style='cursor: pointer;'>HTML</label><br />
		<input type="radio" value="pdf" name="rn_type_par_defaut" id="rn_type_par_defaut_pdf"  <?php   if ($rn_type_par_defaut=="pdf") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_type_par_defaut_pdf' style='cursor: pointer;'>PDF</label>
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Affichage du nom de la classe sur le relev??&nbsp;:
	</td>
	<td>
		<input type="radio" value="1" name="rn_aff_classe_nom" id="rn_aff_classe_nom_1"  <?php   if ($rn_aff_classe_nom=="1") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_aff_classe_nom_1' style='cursor: pointer;'>Nom long</label><br />
		<input type="radio" value="2" name="rn_aff_classe_nom" id="rn_aff_classe_nom_2"  <?php   if ($rn_aff_classe_nom=="2") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_aff_classe_nom_2' style='cursor: pointer;'>Nom court</label><br />
		<input type="radio" value="3" name="rn_aff_classe_nom" id="rn_aff_classe_nom_3"  <?php   if ($rn_aff_classe_nom=="3") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_aff_classe_nom_3' style='cursor: pointer;'>Nom court (Nom long)</label><br />
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_nomdev' style='cursor: pointer;'>Afficher le nom des devoirs&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_nomdev" id="rn_nomdev"  <?php   if ($rn_nomdev=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>

<!-- 20191009 - Martial Lenzen - D??but -->
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		Si la case "Afficher le nom des devoirs" est coch??e, utiliser le&nbsp;:
	</td>
	<td>
		<input type="radio" value="1" name="rn_aff_nomdev_choix" id="rn_aff_nomdev_choix_1"  <?php if ($rn_aff_nomdev_choix=="1") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_aff_nomdev_choix_1' style='cursor: pointer;'>Nom long</label><br />
		<input type="radio" value="2" name="rn_aff_nomdev_choix" id="rn_aff_nomdev_choix_2"  <?php if ($rn_aff_nomdev_choix=="2") echo " checked='checked ' "; ?> onchange='changement()' /><label for='rn_aff_nomdev_choix_2' style='cursor: pointer;'>Nom court</label><br />
	</td>
</tr>
<!-- 20191009 - Martial Lenzen - Fin -->

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_toutcoefdev' style='cursor: pointer;'>Afficher tous les coefficients des devoirs&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_toutcoefdev" id="rn_toutcoefdev"  <?php   if ($rn_toutcoefdev=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_coefdev_si_diff' style='cursor: pointer;'>Afficher les coefficients des devoirs si des coefficients diff??rents sont pr??sents&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_coefdev_si_diff" id="rn_coefdev_si_diff"  <?php   if ($rn_coefdev_si_diff=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_datedev' style='cursor: pointer;'>Afficher les dates des devoirs&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_datedev" id="rn_datedev"  <?php   if ($rn_datedev=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_abs_2' style='cursor: pointer;'>Afficher les absences (ABS2 et relev?? HTML)&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y"  name="rn_abs_2" id="rn_abs_2"  <?php   if ($rn_abs_2=="y") echo " checked='checked ' "; ?> onchange='changement()' />
	</td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Formule/Message ?? ins??rer sous le relev?? de notes&nbsp;:</td>
	<td><input type=text size=40 name="rn_formule" value="<?php echo $rn_formule; ?>" onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_sign_chefetab' style='cursor: pointer;'>Afficher une case pour la signature du chef d'??tablissement&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_sign_chefetab" id="rn_sign_chefetab"  <?php   if ($rn_sign_chefetab=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_sign_pp' style='cursor: pointer;'>Afficher une case pour la signature du prof principal&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_sign_pp" id="rn_sign_pp"  <?php   if ($rn_sign_pp=="y") echo " checked='checked ' "; ?> onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_sign_resp' style='cursor: pointer;'>Afficher une case pour la signature des parents/responsables&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_sign_resp" id="rn_sign_resp"  <?php   if ($rn_sign_resp=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Nombre de lignes pour la signature&nbsp;:</td>
	<td><input type="text" name="rn_sign_nblig" size="3" value="<?php echo $rn_sign_nblig;?>" onchange='changement()' /></td>
</tr>

<!-- 20121027 -->
<!-- A MODIFIER EN CAS DE MODE CNIL STRICT -->
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_app' style='cursor: pointer;'>Afficher l'appr??ciation/commentaire du professeur<br />(<em>sous r??serve d'autorisation par le professeur dans les param??tres du devoir</em>)&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_app" id="rn_app"  <?php   if ($rn_app=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_col_moy' style='cursor: pointer;'>Avec la colonne moyenne (<em title="Moyenne du carnet de notes :
Notez que tant que la p??riode n'est pas close, cette moyenne peut ??voluer
(ajout de notes, modifications de coefficients,...)">du CN</em>) de l'??l??ve&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_col_moy" id="rn_col_moy"  <?php   if ($rn_col_moy=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_moy_gen' style='cursor: pointer;'>Avec la ligne Moyenne g??n??rale (<em title="Moyenne g??n??rale du carnet de notes :
Notez que tant que la p??riode n'est pas close, cette moyenne peut ??voluer
(ajout de notes, modifications de coefficients,...).
Le professeur peut aussi modifier la moyenne lors du transfert du carnet de notes vers les bulletins.">du CN</em>) de l'??l??ve&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_moy_gen" id="rn_moy_gen"  <?php   if ($rn_moy_gen=="y") echo " checked='checked ' "; ?>  onchange='changement()' /><?php
		if(!getSettingAOui('cn_affiche_moy_gen')) {
			echo " <span style='color:red'>La fonctionnalit?? est d??sactiv??e en administrateur dans ";
			if($_SESSION['statut']=='administrateur') {
				echo "<a href='../cahier_notes_admin/index.php' target='_blank'>Gestion des modules/Carnets de notes</a>";
			}
			else {
				echo "<strong>Gestion des modules/Carnets de notes</strong>";
			}
			echo ".</span>";
		}
	?></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_moy_classe' style='cursor: pointer;'>Avec la moyenne de la classe pour chaque devoir&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_moy_classe" id="rn_moy_classe"  <?php   if ($rn_moy_classe=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_moy_min_max_classe' style='cursor: pointer;'>Avec les moyennes min/classe/max de chaque devoir&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_moy_min_max_classe" id="rn_moy_min_max_classe"  <?php   if ($rn_moy_min_max_classe=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_retour_ligne' style='cursor: pointer;'>Avec retour ?? la ligne apr??s chaque devoir si on affiche le nom du devoir ou le commentaire&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_retour_ligne" id="rn_retour_ligne"  <?php   if ($rn_retour_ligne=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>


<?php
	$titre_infobulle="Rapport taille polices\n";
	$texte_infobulle="<p>Pour que la liste des devoirs tienne dans la cellule, on r??duit la taille de la police.<br />Pour que cela reste lisible, vous pouvez fixer ici une taille minimale en dessous de laquelle ne pas descendre.</p><br /><p>Si la taille minimale ne suffit toujours pas ?? permettre l'affichage dans la cellule, on supprime les retours ?? la ligne.</p><br /><p>Et cela ne suffit toujours pas, le texte est tronqu?? (<em>dans ce cas, un relev?? HTML pourra permettre l'affichage (les hauteurs de cellules s'adaptent ?? la quantit?? de texte... L'inconv??nient&nbsp;: Une mati??re peut para??tre plus importante qu'une autre par la place qu'elle occupe)</em>).</p>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('a_propos_rapport_tailles_polices',$titre_infobulle,"",$texte_infobulle,"",35,0,'y', 'y', 'n', 'n');
?>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Rapport taille_standard / taille_minimale_de_police (<em>relev?? PDF</em>)  <?php
		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_rapport_tailles_polices', 'y',100,-50);\"  onmouseout=\"cacher_div('a_propos_rapport_tailles_polices');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Bloc observations en PDF'/></a>";
	?>&nbsp;:</td>
	<td><input type="text" name="rn_rapport_standard_min_font" size="3" value="<?php echo $rn_rapport_standard_min_font;?>" onchange='changement()' /></td>
</tr>

<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_adr_resp' style='cursor: pointer;'>Afficher le bloc adresse du responsable de l'??l??ve&nbsp;:</label>
	</td>
	<td><input type="checkbox" value="y" name="rn_adr_resp" id="rn_adr_resp"  <?php   if ($rn_adr_resp=="y") echo " checked='checked ' "; ?>  onchange='changement()' /></td>
</tr>

<?php
	$titre_infobulle="Bloc observations en PDF\n";
	$texte_infobulle="<p>Le bloc observations est affich?? si une des conditions suivantes est remplie&nbsp;:</p>\n";
	$texte_infobulle.="<ul>\n";
	$texte_infobulle.="<li>La case Bloc observations est coch??e.</li>\n";
	$texte_infobulle.="<li>Une des cases signature est coch??e.</li>\n";
	$texte_infobulle.="</ul>\n";
	$tabdiv_infobulle[]=creer_div_infobulle('a_propos_bloc_observations',$titre_infobulle,"",$texte_infobulle,"",35,0,'y', 'y', 'n', 'n');
?>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">
		<label for='rn_bloc_obs' style='cursor: pointer;'>Afficher le bloc observations (<em>relev?? PDF</em>) <?php
		echo "<a href=\"#\" onclick='return false;' onmouseover=\"afficher_div('a_propos_bloc_observations', 'y',100,-50);\"  onmouseout=\"cacher_div('a_propos_bloc_observations');\"><img src='../images/icons/ico_ampoule.png' width='15' height='25' alt='Aide sur Bloc observations en PDF'/></a>";
	?>&nbsp;:</label>
	</td>
	<td>
		<input type="checkbox" value="y" name="rn_bloc_obs" id="rn_bloc_obs"  <?php   if ($rn_bloc_obs=="y") echo " checked='checked ' "; ?>  onchange='changement()' />
	</td>
</tr>

<?php
if ($gepiSettings['active_mod_ects'] == "y") {
?>
<tr>
	<td colspan='3'>
		<h2><b>Param??tres des attestations ECTS&nbsp;: </b></h2>
	</td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Type de formation (ex: "Classe pr??paratoire scientifique")&nbsp;:</td>
	<td><input type="text" size="40" name="ects_type_formation" value="<?php echo $ects_type_formation;?>" onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Nom complet du parcours de formation (ex: "BCPST (Biologie, Chimie, Physique et Sciences de la Terre)")&nbsp;:</td>
	<td><input type="text" size="40" name="ects_parcours" value="<?php echo $ects_parcours;?>" onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Nom cours du parcours de formation (ex: "BCPST")&nbsp;:</td>
	<td><input type="text" size="40" name="ects_code_parcours" value="<?php echo $ects_code_parcours;?>" onchange='changement()' /></td>
</tr>
<tr>
	<td>&nbsp;&nbsp;&nbsp;</td>
	<td style="font-variant: small-caps;">Domaines d'??tude (ex: "Biologie, Chimie, Physique, Math??matiques, Sciences de la Terre")&nbsp;:</td>
	<td><input type="text" size="40" name="ects_domaines_etude" value="<?php echo $ects_domaines_etude;?>" onchange='changement()' /></td>
</tr>

<?php
} else {
?>
<input type="hidden" name="ects_type_formation" value="<?php echo $ects_type_formation;?>" />
<input type="hidden" name="ects_parcours" value="<?php echo $ects_parcours;?>" />
<input type="hidden" name="ects_code_parcours" value="<?php echo $ects_code_parcours;?>" />
<input type="hidden" name="ects_domaines_etude" value="<?php echo $ects_domaines_etude;?>" />
<input type="hidden" name="ects_fonction_signataire_attestation" value="<?php echo $ects_fonction_signataire_attestation;?>" />
<?php } ?>


</table>
<center><input type=submit value="Enregistrer" style="margin-top: 30px; margin-bottom: 100px;" /></center>
<div id='fixe'>
	<input type=submit value="Enregistrer" />
</div>
</form>
<?php

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe', 'y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>
