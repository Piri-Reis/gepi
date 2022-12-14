<?php
/*
 *
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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
require_once("../lib/share-trombinoscope.inc.php");

unset($reg_login);
$reg_login = isset($_POST["reg_login"]) ? $_POST["reg_login"] : NULL;
unset($reg_nom);
$reg_nom = isset($_POST["reg_nom"]) ? $_POST["reg_nom"] : NULL;
unset($reg_prenom);
$reg_prenom = isset($_POST["reg_prenom"]) ? $_POST["reg_prenom"] : NULL;
unset($reg_email);
$reg_email = isset($_POST["reg_email"]) ? $_POST["reg_email"] : NULL;
unset($reg_sexe);
$reg_sexe = isset($_POST["reg_sexe"]) ? $_POST["reg_sexe"] : NULL;
unset($reg_no_nat);
$reg_no_nat = isset($_POST["reg_no_nat"]) ? $_POST["reg_no_nat"] : NULL;
unset($reg_no_gep);
$reg_no_gep = isset($_POST["reg_no_gep"]) ? $_POST["reg_no_gep"] : NULL;

unset($reg_mef_code);
$reg_mef_code = isset($_POST["reg_mef_code"]) ? $_POST["reg_mef_code"] : NULL;

unset($reg_auth_mode);
$reg_auth_mode = isset($_POST["reg_auth_mode"]) ? $_POST["reg_auth_mode"] : NULL;

unset($birth_year);
$birth_year = isset($_POST["birth_year"]) ? $_POST["birth_year"] : NULL;
unset($birth_month);
$birth_month = isset($_POST["birth_month"]) ? $_POST["birth_month"] : NULL;
unset($birth_day);
$birth_day = isset($_POST["birth_day"]) ? $_POST["birth_day"] : NULL;

unset($reg_tel_pers);
$reg_tel_pers = isset($_POST["reg_tel_pers"]) ? $_POST["reg_tel_pers"] : NULL;
unset($reg_tel_port);
$reg_tel_port = isset($_POST["reg_tel_port"]) ? $_POST["reg_tel_port"] : NULL;
unset($reg_tel_prof);
$reg_tel_prof = isset($_POST["reg_tel_prof"]) ? $_POST["reg_tel_prof"] : NULL;

//Gestion de la date de sortie de l'??tablissement
unset($date_sortie_jour);
$date_sortie_jour = isset($_POST["date_sortie_jour"]) ? $_POST["date_sortie_jour"] : "00";
unset($date_sortie_mois);
$date_sortie_mois = isset($_POST["date_sortie_mois"]) ? $_POST["date_sortie_mois"] : "00";
unset($date_sortie_annee);
$date_sortie_annee = isset($_POST["date_sortie_annee"]) ? $_POST["date_sortie_annee"] : "0000";

//Gestion de la date d'entr??e dans l'??tablissement
unset($date_entree_jour);
$date_entree_jour = isset($_POST["date_entree_jour"]) ? $_POST["date_entree_jour"] : "00";
unset($date_entree_mois);
$date_entree_mois = isset($_POST["date_entree_mois"]) ? $_POST["date_entree_mois"] : "00";
unset($date_entree_annee);
$date_entree_annee = isset($_POST["date_entree_annee"]) ? $_POST["date_entree_annee"] : "0000";

//=========================
// AJOUT: boireaus 20071107
unset($reg_regime);
$reg_regime = isset($_POST["reg_regime"]) ? $_POST["reg_regime"] : NULL;
unset($reg_doublant);
$reg_doublant = isset($_POST["reg_doublant"]) ? $_POST["reg_doublant"] : NULL;

//echo "\$reg_regime=$reg_regime<br />";
//echo "\$reg_doublant=$reg_doublant<br />";

//=========================

//debug_var();

// T??moin pour index_call_data.php
$page_courante="modify_eleve";

//=========================
$modif_adr_pers_id=isset($_GET["modif_adr_pers_id"]) ? $_GET["modif_adr_pers_id"] : NULL;
$adr_id=isset($_GET["adr_id"]) ? $_GET["adr_id"] : NULL;
//=========================


unset($reg_resp1);
$reg_resp1 = isset($_POST["reg_resp1"]) ? $_POST["reg_resp1"] : NULL;
unset($reg_resp2);
$reg_resp2 = isset($_POST["reg_resp2"]) ? $_POST["reg_resp2"] : NULL;

unset($reg_etab);
$reg_etab = isset($_POST["reg_etab"]) ? $_POST["reg_etab"] : NULL;

unset($mode);
$mode = isset($_POST["mode"]) ? $_POST["mode"] : (isset($_GET["mode"]) ? $_GET["mode"] : NULL);
unset($order_type);
$order_type = isset($_POST["order_type"]) ? $_POST["order_type"] : (isset($_GET["order_type"]) ? $_GET["order_type"] : NULL);
unset($quelles_classes);
$quelles_classes = isset($_POST["quelles_classes"]) ? $_POST["quelles_classes"] : (isset($_GET["quelles_classes"]) ? $_GET["quelles_classes"] : NULL);
unset($eleve_login);
$eleve_login = isset($_POST["eleve_login"]) ? $_POST["eleve_login"] : (isset($_GET["eleve_login"]) ? $_GET["eleve_login"] : NULL);
//echo "\$eleve_login=$eleve_login<br />";

$definir_resp = isset($_POST["definir_resp"]) ? $_POST["definir_resp"] : (isset($_GET["definir_resp"]) ? $_GET["definir_resp"] : NULL);
if(($definir_resp!=1)&&($definir_resp!=2)){$definir_resp=NULL;}

$definir_etab = isset($_POST["definir_etab"]) ? $_POST["definir_etab"] : (isset($_GET["definir_etab"]) ? $_GET["definir_etab"] : NULL);

//=========================
// Pour l'arriv??e depuis la page index.php suite ?? une recherche
$motif_rech=isset($_POST['motif_rech']) ? $_POST['motif_rech'] : (isset($_GET['motif_rech']) ? $_GET['motif_rech'] : NULL);
$mode_rech=isset($_POST['mode_rech']) ? $_POST['mode_rech'] : (isset($_GET['mode_rech']) ? $_GET['mode_rech'] : NULL);
if((isset($quelles_classes))&&(isset($mode_rech))&&($mode_rech=='contient')) {
	// On initialise des variables pour index_call_data.php
	if($quelles_classes=='recherche') {
		$mode_rech_nom="contient";
	}
	elseif($quelles_classes=='rech_prenom') {
		$mode_rech_prenom="contient";
	}
	elseif($quelles_classes=='rech_elenoet') {
		$mode_rech_elenoet="contient";
	}
	elseif($quelles_classes=='rech_ele_id') {
		$mode_rech_ele_id="contient";
	}
	elseif($quelles_classes=='rech_no_gep') {
		$mode_rech_no_gep="contient";
	}
}
if(isset($motif_rech)) {
	$motif_rech=stripslashes($motif_rech);
}
//=========================
//echo "\$motif_rech=$motif_rech<br />";
//echo "\$mode_rech=$mode_rech<br />";

$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;

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

if(!isset($eleve_login)) {
	if(acces('/eleves/index.php', $_SESSION['statut'])) {
		header("Location: ./index.php?msg=??l??ve non choisi.");
		die();
	}
	else {
		header("Location: ../accueil.php?msg=??l??ve non choisi.");
		die();
	}
}

if($_SESSION['statut']=='professeur') {
	if((!getSettingAOui('GepiAccesGestElevesProf'))&&(!getSettingAOui('GepiAccesGestElevesProfP'))) {
		header("Location: ../accueil.php?msg=Acc??s aux fiches ??l??ves non autoris??.");
		die();
	}

	if((getSettingAOui('GepiAccesGestElevesProfP'))&&(is_pp($_SESSION['login'], "", $eleve_login))) {
		// C'est OK
	}
	else {
		if(!getSettingAOui('GepiAccesGestElevesProf')) {
			header("Location: ../accueil.php?msg=Acc??s aux fiches ??l??ves non autoris??.");
			die();
		}

		if(!is_prof_ele($_SESSION['login'], $eleve_login)) {
			header("Location: ../accueil.php?msg=Vous n ??tes pas professeur de l ??l??ve ".civ_nom_prenom($eleve_login));
			die();
		}
	}
}

if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
	// On r??cup??re le RNE de l'??tablissement
	$rep_photos="../photos/".$_COOKIE['RNE']."/eleves/";
} else {
	$rep_photos="../photos/eleves/";
}

$is_pp=false;
if(($_SESSION['statut']=="professeur")&&(isset($eleve_login))&&(is_pp($_SESSION['login'], "", $eleve_login))) {
	$is_pp=true;
	// Est-ce que dans le cas false, un prof peut acc??der ?? cette page?
}

// 20160810
$saisir_lieu_naissance=isset($_POST['saisir_lieu_naissance']) ? $_POST['saisir_lieu_naissance'] : (isset($_GET['saisir_lieu_naissance']) ? $_GET['saisir_lieu_naissance'] : NULL);
$initiale_commune_naissance=isset($_POST['initiale_commune_naissance']) ? $_POST['initiale_commune_naissance'] : (isset($_GET['initiale_commune_naissance']) ? $_GET['initiale_commune_naissance'] : NULL);

if((isset($eleve_login))&&(isset($_POST['valider_saisie_lieu_naissance']))&&(isset($_POST['code_commune_insee']))&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
	check_token();

	if((isset($_POST['code_pays']))&&($_POST['code_pays']!="")&&($_POST['code_pays']!="100")) {
		// On enregistre le lieu de naissance au format code_pays@nom_commune
		$nom_commune=get_valeur_champ("communes", "code_commune_insee='".$_POST['code_commune_insee']."'", "commune");
		if($nom_commune=="") {
			$msg="Erreur&nbsp;: Le nom de commune n'a pas ??t?? retrouv?? pour l'??l??ve ".$eleve_login."<br />";
		}
		else {
			$sql="UPDATE eleves SET lieu_naissance='".mysqli_real_escape_string($GLOBALS['mysqli'], $_POST['code_pays']."@".$nom_commune)."' WHERE login='".$eleve_login."';";
			//echo "$sql<br />";
			$update=mysqli_query($mysqli, $sql);
			if(!$update) {
				$msg="Erreur lors de l'enregistrement du lieu de naissance de l'??l??ve ".$eleve_login."<br />";
			}
			else {
				$msg="Lieu de naissance de l'??l??ve ".$eleve_login." enregistr??.<br />";
			}
		}
	}
	else {
		$sql="UPDATE eleves SET lieu_naissance='".$_POST['code_commune_insee']."' WHERE login='".$eleve_login."';";
		//echo "$sql<br />";
		$update=mysqli_query($mysqli, $sql);
		if(!$update) {
			$msg="Erreur lors de l'enregistrement du lieu de naissance de l'??l??ve ".$eleve_login."<br />";
		}
		else {
			$msg="Lieu de naissance de l'??l??ve ".$eleve_login." enregistr??.<br />";
		}
	}

}

if((isset($eleve_login))&&(isset($_POST['valider_saisie_lieu_naissance']))&&(isset($_POST['nouvelle_commune']))&&($_POST['nouvelle_commune']!="")&&(isset($_POST['departement']))&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
	check_token();

	// G??n??ration d'un pseudo-code_commune_insee
	$sql="SELECT * FROM communes WHERE code_commune_insee LIKE 'c%' ORDER BY code_commune_insee DESC LIMIT 1;";
	//echo "$sql<br />";
	$res=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($res)==0) {
		$code_commune_insee="c00000001";
		//$code_commune_insee="c1";
	}
	else {
		$lig=mysqli_fetch_object($res);
		$code_commune_insee="c".sprintf("%08d",(substr($lig->code_commune_insee,1)+1));
		//$code_commune_insee="c".(substr($lig->code_commune_insee,1)+1);
	}


	$sql="INSERT INTO communes SET code_commune_insee='$code_commune_insee', departement='".$_POST['departement']."', commune='".mysqli_real_escape_string($GLOBALS['mysqli'], $_POST['nouvelle_commune'])."';";
	//echo "$sql<br />";
	$insert=mysqli_query($mysqli, $sql);
	if(!$insert) {
		$msg="Erreur lors de l'enregistrement de la nouvelle commune<br />";
	}
	else {

		if((isset($_POST['code_pays']))&&($_POST['code_pays']!="")&&($_POST['code_pays']!="100")) {
			// On enregistre le lieu de naissance au format code_pays@nom_commune

			$sql="UPDATE eleves SET lieu_naissance='".mysqli_real_escape_string($GLOBALS['mysqli'], $_POST['code_pays']."@".$_POST['nouvelle_commune'])."' WHERE login='".$eleve_login."';";
			//echo "$sql<br />";
			$update=mysqli_query($mysqli, $sql);
			if(!$update) {
				$msg="Erreur lors de l'enregistrement du lieu de naissance de l'??l??ve ".$eleve_login."<br />";
			}
			else {
				$msg="Lieu de naissance de l'??l??ve ".$eleve_login." enregistr??.<br />";
			}
		}
		else {
			$sql="UPDATE eleves SET lieu_naissance='".$code_commune_insee."' WHERE login='".$eleve_login."';";
			//echo "$sql<br />";
			$update=mysqli_query($mysqli, $sql);
			if(!$update) {
				$msg="Erreur lors de l'enregistrement du lieu de naissance de l'??l??ve ".$eleve_login."<br />";
			}
			else {
				$msg="Lieu de naissance de l'??l??ve ".$eleve_login." enregistr??.<br />";
			}
		}
	}

}


if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {
	// Le deuxi??me responsable prend l'adresse du premier
	if((isset($modif_adr_pers_id))&&(isset($adr_id))) {
		check_token();
		$sql="UPDATE resp_pers SET adr_id='$adr_id' WHERE pers_id='$modif_adr_pers_id';";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
		if(!$update){
			$msg="Echec de la modification de l'adresse du deuxi??me responsable.";
		}
	}

	if((isset($eleve_login))&&(isset($_POST['ele_id']))&&(isset($_POST['add_resp_legal_0']))&&(isset($_POST['is_posted']))&&($_POST['is_posted']=='add_resp_legal_0')) {
		check_token();

		$add_resp_legal_0=$_POST['add_resp_legal_0'];

		$msg="";

		$cpt=0;
		for($loop=0;$loop<count($add_resp_legal_0);$loop++) {
			$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='".$_POST['ele_id']."' AND pers_id='".$add_resp_legal_0[$loop]."';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($test)>0) {
				$msg.="L'??l??ve est d??j?? associ?? au responsable n??".$add_resp_legal_0[$loop].".<br />";
			}
			else {
				$sql="INSERT INTO responsables2 SET ele_id='".$_POST['ele_id']."', pers_id='".$add_resp_legal_0[$loop]."', resp_legal='0';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
				if($insert) {
					$cpt++;
				}
				else {
					$msg.="Erreur lors de l'association avec l'??l??ve n??".$add_ele_id_resp_legal_0[$loop].".<br />";
				}
			}
		}

		$msg.=$cpt." responsable(s) associ??(s) ?? cet ??l??ve en qualit?? de \"responsable(s)\" non l??gal(ux).<br />";
	}

	/*
	foreach($_POST as $post => $val){
		echo $post.' : '.$val."<br />\n";
	}

	echo "\$eleve_login=$eleve_login<br />";
	echo "\$valider_choix_resp=$valider_choix_resp<br />";
	echo "\$definir_resp=$definir_resp<br />";
	*/
	// Validation d'un choix de responsable
	if((isset($eleve_login))&&(isset($definir_resp))&&(isset($_POST['valider_choix_resp']))) {
		check_token();

		if($definir_resp==1){
			$pers_id=$reg_resp1;
		}
		else{
			$pers_id=$reg_resp2;
		}

		if($pers_id==""){
			// Recherche de l'ele_id
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele)==0){
				$msg="Erreur: L'??l??ve $eleve_login n'a pas l'air pr??sent dans la table 'eleves'.";
			}
			else{
				$lig_ele=mysqli_fetch_object($res_ele);

				$sql="DELETE FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
				$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
				if($suppr){
					$msg="Suppression de l'association de l'??l??ve avec le responsable $definir_resp r??ussie.";
				}
				else{
					$msg="Echec de la suppression l'association de l'??l??ve avec le responsable $definir_resp.";
				}
			}
		}
		else{
			$sql="SELECT 1=1 FROM resp_pers WHERE pers_id='$pers_id'";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);

			if(mysqli_num_rows($test)==0){
				$msg="Erreur: L'identifiant de responsable propos?? n'existe pas.";
			}
			else{
				// Recherche de l'ele_id
				$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
				$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_ele)==0){
					$msg="Erreur: L'??l??ve $eleve_login n'a pas l'air pr??sent dans la table 'eleves'.";
				}
				else{
					$lig_ele=mysqli_fetch_object($res_ele);

					//$sql="SELECT 1=1 FROM responsables2 WHERE pers_id='$pers_id' AND ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$sql="SELECT 1=1 FROM responsables2 WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($test)==0){
						$sql="INSERT INTO responsables2 SET pers_id='$pers_id', ele_id='$lig_ele->ele_id', resp_legal='$definir_resp', pers_contact='1'";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert){
							$msg="Association de l'??l??ve avec le responsable $definir_resp r??ussie.";
						}
						else{
							$msg="Echec de l'association de l'??l??ve avec le responsable $definir_resp.";
						}
					}
					else{
						$sql="UPDATE responsables2 SET pers_id='$pers_id' WHERE ele_id='$lig_ele->ele_id' AND resp_legal='$definir_resp'";
						$update=mysqli_query($GLOBALS["mysqli"], $sql);
						if($update){
							$msg="Association de l'??l??ve avec le responsable $definir_resp r??ussie.";
						}
						else{
							$msg="Echec de l'association de l'??l??ve avec le responsable $definir_resp.";
						}
					}
				}
			}
		}
		unset($definir_resp);
	}

	//debug_var();

	// Validation d'un choix d'??tablissement d'origine
	if((isset($eleve_login))&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {
		check_token();
	//if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")&&(isset($definir_etab))&&(isset($_POST['valider_choix_etab']))) {

		$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login';";
		$res_elenoet=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_elenoet)>0) {
			$lig_elenoet=mysqli_fetch_object($res_elenoet);
			$reg_no_gep=$lig_elenoet->elenoet;
			if($reg_no_gep!="") {
				if($reg_etab==""){
					//$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
					$sql="DELETE FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
					$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
					if($suppr){
						$msg="Suppression de l'association de l'??l??ve avec un ??tablissement r??ussie.";
					}
					else{
						$msg="Echec de la suppression l'association de l'??l??ve avec un ??tablissement.";
					}
				}
				else{
					$sql="SELECT 1=1 FROM etablissements WHERE id='$reg_etab'";
					//echo "$sql<br />";
					$test=mysqli_query($GLOBALS["mysqli"], $sql);

					if(mysqli_num_rows($test)==0){
						$msg="Erreur: L'??tablissement choisi (<i>$reg_etab</i>) n'existe pas dans la table 'etablissement'.";
					}
					else{
						//$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
						$sql="SELECT 1=1 FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);

						if(mysqli_num_rows($test)==0){
							//$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$eleve_login', id_etablissement='$reg_etab'";
							$sql="INSERT INTO j_eleves_etablissements SET id_eleve='$reg_no_gep', id_etablissement='$reg_etab'";
							$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							if($insert){
								$msg="Association de l'??l??ve avec l'??tablissement $reg_etab r??ussie.";
							}
							else{
								$msg="Echec de l'association de l'??l??ve avec l'??tablissement $reg_etab.";
							}
						}
						else{
							//$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$eleve_login'";
							$sql="UPDATE j_eleves_etablissements SET id_etablissement='$reg_etab' WHERE id_eleve='$reg_no_gep'";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if($update){
								$msg="Association de l'??l??ve avec l'??tablissement $reg_etab r??ussie.";
							}
							else{
								$msg="Echec de l'association de l'??l??ve avec l'??tablissement $reg_etab.";
							}
						}
					}
				}
			}
		}
		unset($definir_etab);
	}


	//================================================
	// Validation de modifications dans le formulaire de nom, pr??nom,...
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		check_token();

		// D??termination du format de la date de naissance
		$call_eleve_test = mysqli_query($GLOBALS["mysqli"], "SELECT naissance FROM eleves WHERE 1");
		$test_eleve_naissance = @old_mysql_result($call_eleve_test, "0", "naissance");
		$format = mb_strlen($test_eleve_naissance);


		// Cas de la cr??ation d'un ??l??ve
		$reg_nom = trim($reg_nom);
		$reg_prenom = trim($reg_prenom);
		$reg_email = trim($reg_email);
		if ($reg_resp1 == '(vide)') $reg_resp1 = '';
		if (!preg_match ("/^[0-9]{4}$/", $birth_year)) {$birth_year = "1900";}
		if(preg_match ("/^[1-9]{1}$/", $birth_month)) {$birth_month="0".$birth_month;}
		elseif (!preg_match ("/^[0-9]{2}$/", $birth_month)) {$birth_month = "01";}
		if(preg_match ("/^[1-9]{1}$/", $birth_day)) {$birth_day="0".$birth_day;}
		elseif (!preg_match ("/^[0-9]{2}$/", $birth_day)) {$birth_day = "01";}

		if ($format == '10') {
			// YYYY-MM-DD
			$reg_naissance = $birth_year."-".$birth_month."-".$birth_day." 00:00:00";
		}
		else {
			if ($format == '8') {
				// YYYYMMDD
				$reg_naissance = $birth_year.$birth_month.$birth_day;
				settype($reg_naissance,"integer");
			} else {
				// Format inconnu
				$reg_naissance = $birth_year.$birth_month.$birth_day;
			}
		}
		
		//gestion de la date de sortie de l'??l??ve
		//echo "date_sortie_annee=".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois=".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour=".$date_sortie_jour."<br/>";
		if (!preg_match ("/^[0-9]{4}$/", $date_sortie_annee)) {$date_sortie_annee = "0000";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_sortie_mois)) {$date_sortie_mois = "00";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_sortie_jour)) {$date_sortie_jour = "00";}
		//echo "date_sortie_annee=".$date_sortie_annee."<br/>";
		//echo "date_sortie_mois=".$date_sortie_mois."<br/>";
		//echo "date_sortie_jour=".$date_sortie_jour."<br/>";

		//cr??ation de la chaine au format timestamp
		$date_de_sortie_eleve = $date_sortie_annee."-".$date_sortie_mois."-".$date_sortie_jour." 00:00:00"; 
		
		//gestion de la date d'entr??e de l'??l??ve
		if (!preg_match ("/^[0-9]{4}$/", $date_entree_annee)) {$date_entree_annee = "0000";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_entree_mois)) {$date_entree_mois = "00";}
		if (!preg_match ("/^[0-9]{1,2}$/", $date_entree_jour)) {$date_entree_jour = "00";}
		//cr??ation de la chaine au format timestamp
		$date_entree_eleve = $date_entree_annee."-".$date_entree_mois."-".$date_entree_jour." 00:00:00"; 
		
		//===========================
		//AJOUT:
		if(!isset($msg)){$msg="";}
		//===========================

		$continue = 'yes';
		if (($reg_nom == '') or ($reg_prenom == '')) {
			$msg = "Les champs nom et pr??nom sont obligatoires.";
			$continue = 'no';
		}

		//$msg.="\$reg_login=$reg_login<br />";
		//if(isset($eleve_login)){$msg.="\$eleve_login=$eleve_login<br />";}

		// $reg_login non vide correspond ?? un nouvel ??l??ve.
		// On a saisi un login avant de valider
		if (($continue == 'yes') and (isset($reg_login))) {
			// CE CAS NE DOIT PLUS SE PRODUIRE PUISQUE J'AI AJOUT?? UNE PAGE add_eleve.php D'APRES L'ANCIENNE modify_eleve.php
			// On doit n??cessairement passer dans le else plus bas...

			//echo "\$reg_login=$reg_login<br/>";

			$msg = '';
			$ok = 'yes';
			if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_]{0,11}$/", $reg_login)) {
				if ($reg_no_gep != '') {
					$test1 = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE elenoet='$reg_no_gep'");
					$count1 = mysqli_num_rows($test1);
					if ($count1 != "0") {
						//$msg .= "Erreur : un ??l??ve ayant le m??me num??ro GEP existe d??j??.<br />";
						$msg .= "Erreur : un ??l??ve ayant le m??me num??ro interne Sconet (elenoet) existe d??j??.<br />";
						$ok = 'no';
					}
				}

				if ($reg_no_nat != '') {
					$test2 = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE no_gep='$reg_no_nat'");
					$count2 = mysqli_num_rows($test2);
					if ($count2 != "0") {
						$msg .= "Erreur : un ??l??ve ayant le m??me num??ro national existe d??j??.";
						$ok = 'no';
					}
				}

				if ($ok == 'yes') {
					$test = mysqli_query($GLOBALS["mysqli"], "SELECT login FROM eleves WHERE login='$reg_login'");
					$count = mysqli_num_rows($test);
					if ($count == "0") {

						if(!isset($ele_id)){
							// GENERER UN ele_id...
							/*
							$sql="SELECT MAX(ele_id) max_ele_id FROM eleves";
							$res_ele_id_eleve=mysql_query($sql);
							$max_ele_id = old_mysql_result($call_resp , 0, "max_ele_id");

							$sql="SELECT MAX(ele_id) max_ele_id FROM responsables2";
							$res_ele_id_responsables2=mysql_query($sql);
							$max_ele_id2 = old_mysql_result($call_resp , 0, "max_ele_id");

							if($max_ele_id2>$max_ele_id){$max_ele_id=$max_ele_id2;}
							$ele_id=$max_ele_id+1;
							*/
							// PB si on fait ensuite un import sconet le pers_id risque de ne pas correspondre... de provoquer des collisions.
							// QUAND ON LES METS A LA MAIN, METTRE UN ele_id, pers_id,... n??gatifs?

							// PREFIXER D'UN a...

							$sql="SELECT ele_id FROM eleves WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_eleve=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele_id_eleve)>0){
								$tmp=0;
								$lig_ele_id_eleve=mysqli_fetch_object($res_ele_id_eleve);
								$tmp=mb_substr($lig_ele_id_eleve->ele_id,1);
								$tmp++;
								$max_ele_id=$tmp;
							}
							else{
								$max_ele_id=1;
							}

							$sql="SELECT ele_id FROM responsables2 WHERE ele_id LIKE 'e%' ORDER BY ele_id DESC";
							$res_ele_id_responsables2=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($res_ele_id_responsables2)>0){
								$tmp=0;
								$lig_ele_id_responsables2=mysqli_fetch_object($res_ele_id_responsables2);
								$tmp=mb_substr($lig_ele_id_responsables2->ele_id,1);
								$tmp++;
								$max_ele_id2=$tmp;
							}
							else{
								$max_ele_id2=1;
							}

							$tmp=max($max_ele_id,$max_ele_id2);
							$ele_id="e".sprintf("%09d",max($max_ele_id,$max_ele_id2));
						}

						/*
						$reg_data1 = mysql_query("INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ereno = '".$reg_resp1."',
							ele_id = '".$ele_id."'
							");
						*/
						$sql="INSERT INTO eleves SET
							no_gep = '".$reg_no_nat."',
							nom='".$reg_nom."',
							prenom='".$reg_prenom."',
							email='".$reg_email ."',
							login='".$reg_login."',
							sexe='".$reg_sexe."',
							naissance='".$reg_naissance."',
							elenoet = '".$reg_no_gep."',
							ele_id = '".$ele_id."'";
						if(isset($reg_mef_code)) {$sql.=",mef_code='".$reg_mef_code."'";}
						if(isset($reg_tel_pers)) {$sql.=",tel_pers='".$reg_tel_pers."'";}
						if(isset($reg_tel_port)) {$sql.=",tel_port='".$reg_tel_port."'";}
						if(isset($reg_tel_prof)) {$sql.=",tel_prof='".$reg_tel_prof."'";}
						$reg_data1 = mysql_query();

						// R??gime:
						$reg_data3 = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$reg_login', doublant='-', regime='d/p'");
						if ((!$reg_data1) or (!$reg_data3)) {
							$msg = "Erreur lors de l'enregistrement des donn??es";
						} elseif ($mode == "unique") {
							$mess=rawurlencode("El??ve enregistr?? !");
							header("Location: index.php?msg=$mess");
							die();
						} elseif ($mode == "multiple") {
							$mess=rawurlencode("El??ve enregistr??.Vous pouvez saisir l'??l??ve suivant.");
							header("Location: modify_eleve.php?mode=multiple&msg=$mess");
							die();
						}
					} else {
						$msg="Un ??l??ve portant le m??me identifiant existe d??ja !";
					}
				}
			} else {
				$msg="L'identifiant choisi est constitu?? au maximum de 12 caract??res : lettres, chiffres ou \"_\" et ne doit pas commencer par un chiffre !";
			}
		} else if ($continue == 'yes') {
			// C'est une mise ?? jour pour un ??l??ve qui existait d??j?? dans la table 'eleves'.
			$sql="UPDATE eleves SET date_sortie = '$date_de_sortie_eleve', date_entree = '$date_entree_eleve', no_gep = '$reg_no_nat', nom='$reg_nom',prenom='$reg_prenom',sexe='$reg_sexe',naissance='".$reg_naissance."', ereno='".$reg_resp1."', elenoet = '".$reg_no_gep."'";

			if(isset($reg_tel_pers)) {$sql.=",tel_pers='".$reg_tel_pers."'";}
			if(isset($reg_tel_port)) {$sql.=",tel_port='".$reg_tel_port."'";}
			if(isset($reg_tel_prof)) {$sql.=",tel_prof='".$reg_tel_prof."'";}
			if(isset($reg_mef_code)) {$sql.=",mef_code='".$reg_mef_code."'";}

			$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="n";
			$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
			$res_email_utilisateur_ele=mysqli_query($GLOBALS["mysqli"], $sql_test);
			if(mysqli_num_rows($res_email_utilisateur_ele)==0) {
				$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="y";
			}

			/*
			if(getSettingValue('mode_email_ele')=='mon_compte') {
				$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
				$res_email_utilisateur_ele=mysql_query($sql_test);
				if(mysql_num_rows($res_email_utilisateur_ele)>0) {
					// Faut-il ins??rer un email? si l'email utilisateur est vide?
				}
				else {
					$sql.=",email='$reg_email'";
					$temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve="y";
				}
			}
			else {
			*/
				$sql.=",email='$reg_email'";
			//}
			$sql.=" WHERE login='".$eleve_login."';";
			//echo "$sql<br />";
			$reg_data = mysqli_query($GLOBALS["mysqli"], $sql);
			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des donn??es";
			}
			//elseif((getSettingValue('mode_email_ele')!='mon_compte')||($temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve=="y")) {
			else {
				/*
				// On met ?? jour la table utilisateurs si un compte existe pour cet ??l??ve
				$test_login = old_mysql_result(mysql_query("SELECT count(login) FROM utilisateurs WHERE login = '".$eleve_login ."'"), 0);
				if ($test_login > 0) {
				*/
				if($temoin_mon_compte_mais_pas_de_compte_pour_cet_eleve=='n') {

					$res = mysqli_query($GLOBALS["mysqli"], "UPDATE utilisateurs SET nom='".$reg_nom."', prenom='".$reg_prenom."', email='".$reg_email."', auth_mode='$reg_auth_mode' WHERE login = '".$eleve_login."'");
					//$msg.="TEMOIN test_login puis update<br />";
				}
			}

			if ($date_sortie_annee != "0000") {
				// On a une date de sortie, on met ?? jour la table d'agr??gation
				require_once("../lib/initialisationsPropel.inc.php");
				$eleve = EleveQuery::create()->findOneByLogin($eleve_login);
				$eleve->updateAbsenceAgregationTable();//pas besoin de sauver dateSortie, c'est d??j?? fait en mysql ligne 492
			}


			// Corriger le compte d'utilisateur
			$sql="UPDATE utilisateurs SET nom='$reg_nom', prenom='$reg_prenom', civilite='".(($reg_sexe=='M') ? 'M.' : 'Mlle')."' WHERE login = '".$eleve_login."' AND statut='eleve';";
			$update_utilisateur=mysqli_query($GLOBALS["mysqli"], $sql);


			if(isset($reg_doublant)){
				if ($reg_doublant!='R') {$reg_doublant = '-';}

				$call_regime = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysqli_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					// On va se retrouver ??ventuellement avec un r??gime vide... cela peut-il poser pb?
					$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$eleve_login', doublant='$reg_doublant';");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_regime SET doublant = '$reg_doublant' WHERE login='$eleve_login';");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}

			if(isset($reg_regime)){
				if (($reg_regime!='i-e')&&($reg_regime!='int.')&&($reg_regime!='ext.')&&($reg_regime!='d/p')) {
					$reg_regime='d/p';
				}

				$call_regime = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_eleves_regime WHERE login='$eleve_login'");
				$nb_test_regime = mysqli_num_rows($call_regime);
				if ($nb_test_regime == 0) {
					$reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_eleves_regime SET login='$eleve_login', regime='$reg_regime'");
					if (!($reg_data)) {$reg_ok = 'no';}
				} else {
					$reg_data = mysqli_query($GLOBALS["mysqli"], "UPDATE j_eleves_regime SET regime = '$reg_regime'  WHERE login='$eleve_login'");
					if (!($reg_data)) {$reg_ok = 'no';}
				}
			}

			if(($_SESSION['statut']=='administrateur')&&(isset($_POST['login_sso']))) {
				$enregistrer_sso_corresp="y";
				if($_POST['login_sso']!="") {
					$sql="SELECT login_gepi FROM sso_table_correspondance WHERE login_sso='".$_POST['login_sso']."' AND login_gepi!='".$eleve_login."';";
					$res_sso=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_sso)>0) {
						$lig_sso=mysqli_fetch_object($res_sso);

						$sql="SELECT * FROM utilisateurs WHERE login='".$lig_sso->login_gepi."';";
						$test_user=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test_user)>0) {
							$lig_user=mysqli_fetch_object($test_user);
							$msg.="ANOMALIE&nbsp;: La correspondance SSO propos??e ".$_POST['login_sso']." est d??j?? attribu??e ";
							if($lig_user->statut=="eleve") {
								$msg.=" ?? l'??l??ve <a href='../utilisateurs/edit_eleve.php?filtrage=afficher&critere_recherche=".preg_replace("/[^A-Za-z]/","%",ensure_ascii($lig_user->nom))."' target='_blank'>".$lig_sso->login_gepi."</a>";
							}
							elseif($lig_user->statut=="responsable") {
								$msg.=" au responsable <a href='../utilisateurs/edit_responsable.php?filtrage=afficher&critere_recherche_login=".$lig_sso->login_gepi."' target='_blank'>".$lig_sso->login_gepi."</a>";
							}
							else {
								$msg.=" au personnel <a href='../utilisateurs/modify_user.php?user_login=".$lig_sso->login_gepi."' target='_blank'>".$lig_sso->login_gepi."</a>";
							}
							$msg.="<br />Vous devriez faire le m??nage pour ne conserver qu'une seule association.<br />";
							$enregistrer_sso_corresp="n";
						}
						else {

							$sql="SELECT * FROM eleves WHERE login='".$lig_sso->login_gepi."';";
							$test_user=mysqli_query($GLOBALS["mysqli"], $sql);
							if(mysqli_num_rows($test_user)>0) {
								$lig_user=mysqli_fetch_object($test_user);
								$msg.="ANOMALIE&nbsp;: La correspondance SSO propos??e ".$_POST['login_sso']." est d??j?? attribu??e ";
								$msg.=" ?? l'??l??ve <a href='modify_eleve.php?eleve_login=".$lig_sso->login_gepi."' target='_blank'>".$lig_sso->login_gepi."</a>";
								$msg.="<br />Vous devriez faire le m??nage pour ne conserver qu'une seule association.<br />";
								$enregistrer_sso_corresp="n";
							}
							else {
								$sql="SELECT * FROM resp_pers WHERE login='".$lig_sso->login_gepi."';";
								$test_user=mysqli_query($GLOBALS["mysqli"], $sql);
								if(mysqli_num_rows($test_user)>0) {
									$lig_user=mysqli_fetch_object($test_user);
									$msg.="ANOMALIE&nbsp;: La correspondance SSO propos??e ".$_POST['login_sso']." est d??j?? attribu??e ";
									$msg.=" au responsable <a href='../responsables/modify_resp.php?pers_id=".$lig_user->pers_id."' target='_blank'>".$lig_sso->login_gepi."</a>";
									$msg.="<br />Vous devriez faire le m??nage pour ne conserver qu'une seule association.<br />";
									$enregistrer_sso_corresp="n";
								}
								else {
									$sql="DELETE FROM sso_table_correspondance WHERE login_gepi='".$lig_sso->login_gepi."';";
									$menage=mysqli_query($GLOBALS["mysqli"], $sql);
									$msg.="Suppression d'une scorie&nbsp;:<br />La correspondance SSO propos??e ".$_POST['login_sso']." ??tait associ??e au login ".$lig_sso->login_gepi." qui n'existe plus dans la table 'utilisateurs'.<br />";
								}
							}
						}
					}
				}

				if($enregistrer_sso_corresp=="y") {
					$sql="SELECT login_sso FROM sso_table_correspondance WHERE login_gepi='".$eleve_login."';";
					$res_sso=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_sso)>0) {
						$lig_sso=mysqli_fetch_object($res_sso);
						if($lig_sso->login_sso!=$_POST['login_sso']) {
							$sql="UPDATE sso_table_correspondance SET login_sso='".$_POST['login_sso']."' WHERE login_gepi='".$eleve_login."';";
							$update=mysqli_query($GLOBALS["mysqli"], $sql);
							if(!$update) {
								$msg.="Erreur lors de la mise ?? jour de la correspondance SSO.<br />";
							}
						}
					}
					else {
						$sql="INSERT INTO sso_table_correspondance SET login_sso='".$_POST['login_sso']."', login_gepi='".$eleve_login."';";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$insert) {
							$msg.="Erreur lors de l'enregistrement de la correspondance SSO.<br />";
						}
					}
				}
			}

			/*
			$call_test = mysql_query("SELECT * FROM j_eleves_etablissements WHERE id_eleve = '$eleve_login'");
			$count = mysql_num_rows($call_test);
			if ($count == "0") {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("INSERT INTO j_eleves_etablissements VALUES ('$eleve_login','$reg_etab')");
				}
			} else {
				if ($reg_etab != "(vide)") {
					$reg_data = mysql_query("UPDATE j_eleves_etablissements SET id_etablissement = '$reg_etab' WHERE id_eleve='$eleve_login'");
				} else {
					$reg_data = mysql_query("DELETE FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'");
				}
			}
			*/

			if (!$reg_data) {
				$msg = "Erreur lors de l'enregistrement des donn??es ! ";
			} else {
				//$msg = "Les modifications ont bien ??t?? enregistr??es !";
				// MODIF POUR AFFICHER MES TEMOINS...
				$msg .= "Les modifications ont bien ??t?? enregistr??es (".strftime("Le %d/%m/%Y ?? %H:%M:%S").") ! ";
			}


			// Envoi de la photo
			if(isset($reg_no_gep)){
				//echo "\$reg_no_gep=$reg_no_gep<br />";

				if (isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y') {
					$elenoet_ou_login=$eleve_login;
				}
				else {
					$elenoet_ou_login=$reg_no_gep;
				}

				if($elenoet_ou_login!=""){
					if((isset($GLOBALS['multisite']) AND $GLOBALS['multisite'] == 'y')||
					(mb_strlen(preg_replace("/[0-9]/","",$elenoet_ou_login))==0)||
					(preg_match("/^e[0-9]{1,}$/", $elenoet_ou_login))) {
						if(isset($_POST['suppr_filephoto'])){
							if($_POST['suppr_filephoto']=='y'){

								// R??cup??ration du nom de la photo en tenant compte des histoires des z??ro 02345.jpg ou 2345.jpg
								$photo=nom_photo($elenoet_ou_login);
/*
								if("$photo"!=""){
									if(unlink("../photos/eleves/$photo")){
 */
								if($photo){
									if(unlink($photo)){
										$msg.="La photo ".$photo." a ??t?? supprim??e. ";
									}
									else{
										$msg.="Echec de la suppression de la photo ".$photo." ";
									}
								}
								else{
									$msg.="Echec de la suppression de la photo correspondant ?? $elenoet_ou_login (<i>non trouv??e</i>) ";
								}
							}
						}

						// Contr??ler qu'un seul ??l??ve a bien cet elenoet???
						$nb_occurrence_identifiant=1;
						if (!isset($GLOBALS['multisite']) OR $GLOBALS['multisite'] != 'y') {
							$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
							$test=mysqli_query($GLOBALS["mysqli"], $sql);
							$nb_occurrence_identifiant=mysqli_num_rows($test);
						}

						if($nb_occurrence_identifiant==1){
							// filephoto
							if(isset($_FILES['filephoto'])){
								$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
								if($filephoto_tmp!=""){
									$filephoto_name=$_FILES['filephoto']['name'];
									$filephoto_size=$_FILES['filephoto']['size'];
									// Tester la taille max de la photo?

									if(is_uploaded_file($filephoto_tmp)){
										$dest_file=$rep_photos.encode_nom_photo($elenoet_ou_login).".jpg";
										//echo "\$dest_file=$dest_file<br />";
										$source_file=$filephoto_tmp;
										$res_copy=copy("$source_file" , "$dest_file");
										if($res_copy){
											$msg.="Mise en place de la photo effectu??e.";
											if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
												// si le redimensionnement des photos est activ?? on redimensionne
												if (getSettingValue("active_module_trombinoscopes_rt")!='')
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"),getSettingValue("active_module_trombinoscopes_rt"));
												else
													$redim_OK=redim_photo($dest_file,getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
												if (!$redim_OK) $msg .= " Echec du redimensionnement de la photo.";
											}
										}
										else{
											$msg.="Erreur lors de la mise en place de la photo.";
										}
									}
									else{
										$msg.="Erreur lors de l'upload de la photo.";
									}
								}
							}
						}
						elseif($nb_occurrence_identifiant==0){
								//$msg.="Le num??ro GEP de l'??l??ve n'est pas enregistr?? dans la table 'eleves'.";
								$msg.="Le num??ro interne Sconet (elenoet) de l'??l??ve n'est pas enregistr?? dans la table 'eleves'.";
						}
						else{
							//$msg.="Le num??ro GEP est commun ?? plusieurs ??l??ves. C'est une anomalie.";
							$msg.="Le num??ro interne Sconet (elenoet) est commun ?? plusieurs ??l??ves. C'est une anomalie.";
						}
					}
					else{
						//$msg.="Le num??ro GEP propos?? contient des caract??res non num??riques.";
						$msg.="Le num??ro interne Sconet (elenoet) propos?? contient des caract??res non num??riques.";
					}
				}
			}


			$temoin_ele_id="";
			$sql="SELECT ele_id FROM eleves WHERE login='$eleve_login'";
			$res_ele_id_eleve=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_ele_id_eleve)==0){
				$msg.="Erreur: Le champ ele_id n'est pas pr??sent. Votre table 'eleves' n'a pas l'air ?? jour.<br />";
				$temoin_ele_id="PB";
			}
			else{
				$lig_tmp=mysqli_fetch_object($res_ele_id_eleve);
				$ele_id=$lig_tmp->ele_id;
			}
		}
	}

	//================================================
}
elseif((($_SESSION['statut']=="professeur")&&($is_pp))||($_SESSION['statut']=="cpe")) {
	if (isset($_POST['is_posted']) and ($_POST['is_posted'] == "1")) {
		if(!isset($msg)){$msg="";}

		//debug_var();

		// En cpe ou prof, on n'a pas acc??s ?? la modification de la fiche... donc pas de reg_no_gep
		/*
		$sql="SELECT 1=1 FROM eleves WHERE login='$eleve_login' AND elenoet='$reg_no_gep';";
		//echo "$sql<br />";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)==0) {
			tentative_intrusion("2", "Tentative d'upload par un ".$_SESSION['statut']." de la photo d'un ??l??ve ($eleve_login) pour un elenoet ($reg_no_gep) ne correspondant pas ?? cet ??l??ve.");
			echo "Incoh??rence entre le login ??l??ve et son num??ro elenoet.";
			require ("../lib/footer.inc.php");
			die();
		}
		else {
		*/
		$sql="SELECT elenoet FROM eleves WHERE login='$eleve_login' AND elenoet!='';";
		//echo "$sql<br />";
		$test=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($test)==0) {
			$msg.="L'??l??ve n'a pas d'elenoet.<br />La mise en place de la photo n'est pas possible.<br />";
		}
		else {
			$reg_no_gep=old_mysql_result($test,0,"elenoet");

			// Envoi de la photo
			if((isset($reg_no_gep))&&(isset($eleve_login))) {
				if($reg_no_gep!="") {
					if(mb_strlen(preg_replace("/[0-9]/","",$reg_no_gep))==0) {
						if(($_SESSION['statut']=='professeur')&&(getSettingValue("GepiAccesGestPhotoElevesProfP")!='yes')) {
							tentative_intrusion("2", "Tentative d'upload par un professeur de la photo d'un ??l??ve ($eleve_login), sans avoir l'autorisation d'upload.");
							echo "L'upload de photo n'est pas autoris?? pour les professeurs.";
							require ("../lib/footer.inc.php");
							die();
						}
						elseif(($_SESSION['statut']=='cpe')&&(getSettingValue("CpeAccesUploadPhotosEleves")!='yes')) {
							tentative_intrusion("2", "Tentative d'upload par un cpe de la photo d'un ??l??ve ($eleve_login), sans avoir l'autorisation d'upload.");
							echo "L'upload de photo n'est pas autoris?? pour les cpe.";
							require ("../lib/footer.inc.php");
							die();
						}
						else {
							// On ne filtre pas sur un droit CPE limit?? ?? ses propres ??l??ves suivis
							//if(($_SESSION['statut']=='cpe')||(is_cpe($_SESSION['login'],"",$eleve_login))) {
							if(($_SESSION['statut']=='cpe')||
							(($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'],"",$eleve_login)))) {
								if(isset($_POST['suppr_filephoto'])) {
									check_token();
									if($_POST['suppr_filephoto']=='y'){

										// R??cup??ration du nom de la photo en tenant compte des histoires des z??ro 02345.jpg ou 2345.jpg
										$photo=nom_photo($reg_no_gep);
										if($photo){
											if(unlink($photo)){
												$msg.="La photo ".$photo." a ??t?? supprim??e. ";
											}
											else{
												$msg.="Echec de la suppression de la photo ".$photo." ";
											}
										}
										else{
											$msg.="Echec de la suppression de la photo correspondant ?? $reg_no_gep (<i>non trouv??e</i>) ";
										}
									}
								}

								// Contr??ler qu'un seul ??l??ve a bien cet elenoet???
								$sql="SELECT 1=1 FROM eleves WHERE elenoet='$reg_no_gep'";
								$test=mysqli_query($GLOBALS["mysqli"], $sql);
								$nb_elenoet=mysqli_num_rows($test);
								if($nb_elenoet==1){
									// filephoto
									if(isset($_FILES['filephoto'])){
										check_token();
										$filephoto_tmp=$_FILES['filephoto']['tmp_name'];
										if($filephoto_tmp!=""){
											$filephoto_name=$_FILES['filephoto']['name'];
											$filephoto_size=$_FILES['filephoto']['size'];
											// Tester la taille max de la photo?

											if(is_uploaded_file($filephoto_tmp)){
												$dest_file=$rep_photos.encode_nom_photo($reg_no_gep).".jpg";
												//echo "\$dest_file=$dest_file<br />";
												$source_file=$filephoto_tmp;
												$res_copy=copy("$source_file" , "$dest_file");
												if($res_copy){
													$msg.="Mise en place de la photo effectu??e.";
												}
												else{
													$msg.="Erreur lors de la mise en place de la photo.";
												}

												if (getSettingValue("active_module_trombinoscopes_rd")=='y') {
													// si le redimensionnement des photos est activ?? on redimenssionne
													$source = imagecreatefromjpeg($dest_file); // La photo est la source

													if (getSettingValue("active_module_trombinoscopes_rt")=='') {
														$destination = imagecreatetruecolor(getSettingValue("l_resize_trombinoscopes"), getSettingValue("h_resize_trombinoscopes"));
													} // On cr??e la miniature vide

													if (getSettingValue("active_module_trombinoscopes_rt")!='') {
														$destination = imagecreatetruecolor(getSettingValue("h_resize_trombinoscopes"), getSettingValue("l_resize_trombinoscopes"));
													} // On cr??e la miniature vide

													// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
													$largeur_source = imagesx($source);
													$hauteur_source = imagesy($source);
													$largeur_destination = imagesx($destination);
													$hauteur_destination = imagesy($destination);

													// On cr??e la miniature
													imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
													if (getSettingValue("active_module_trombinoscopes_rt")!='') {
														$degrees = getSettingValue("active_module_trombinoscopes_rt");
														// $destination = imagerotate($destination,$degrees);
														$destination = ImageRotateRightAngle($destination,$degrees);
													}
													// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
													imagejpeg($destination, $dest_file,100);
												}
											}
											else{
												$msg.="Erreur lors de l'upload de la photo.";
											}
										}
									}
								}
								elseif($nb_elenoet==0){
										//$msg.="Le num??ro GEP de l'??l??ve n'est pas enregistr?? dans la table 'eleves'.";
										$msg.="Le num??ro interne Sconet (elenoet) de l'??l??ve n'est pas enregistr?? dans la table 'eleves'.";
								}
								else{
									//$msg.="Le num??ro GEP est commun ?? plusieurs ??l??ves. C'est une anomalie.";
									$msg.="Le num??ro interne Sconet (elenoet) est commun ?? plusieurs ??l??ves. C'est une anomalie.";
								}
							}
							else {
								tentative_intrusion("2", "Tentative d'upload par un prof de la photo d'un ??l??ve ($eleve_login) dont il n'est pas ".getSettingValue('gepi_prof_suivi').".");
								echo "Upload de photo non autoris?? : Vous n'??tes pas ".getSettingValue('gepi_prof_suivi')." de cet ??l??ve.";
								require ("../lib/footer.inc.php");
								die();
							}
						}
					}
					else{
						//$msg.="Le num??ro GEP propos?? contient des caract??res non num??riques.";
						$msg.="Le num??ro interne Sconet (elenoet) propos?? contient des caract??res non num??riques.";
					}
				}
			}
		}
	}
}

// On appelle les informations de l'utilisateur pour les afficher :
if (isset($eleve_login)) {
	$call_eleve_info = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM eleves WHERE login='$eleve_login'");
	$eleve_nom = old_mysql_result($call_eleve_info, "0", "nom");
	$eleve_prenom = old_mysql_result($call_eleve_info, "0", "prenom");
	$eleve_email = old_mysql_result($call_eleve_info, "0", "email");

	if(getSettingValue('mode_email_ele')=='mon_compte') {
		$sql_test="SELECT email FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
		$res_email_utilisateur_ele=mysqli_query($GLOBALS["mysqli"], $sql_test);
		if(mysqli_num_rows($res_email_utilisateur_ele)>0) {
			$tmp_lig_email=mysqli_fetch_object($res_email_utilisateur_ele);

			if($tmp_lig_email->email!="") {
				if($tmp_lig_email->email!=$eleve_email) {
					//check_token();
					$sql="UPDATE eleves SET email='$tmp_lig_email->email' WHERE login='$eleve_login';";
					$update=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$update) {
						if(!isset($msg)) {$msg="";}
						$msg.="Erreur lors de la mise ?? jour du mail de l'??l??ve d'apr??s son compte d'utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
					else {
						if(!isset($msg)) {$msg="";}
						$msg.="Mise ?? jour de l'email de $eleve_login dans la table 'eleves' d'apr??s l'email de son compte utilisateur<br />$eleve_email -&gt; $tmp_lig_email->email<br />";
					}
				}
				$eleve_email = $tmp_lig_email->email;
			}
		}
	}

    $eleve_sexe = old_mysql_result($call_eleve_info, "0", "sexe");
    $eleve_naissance = old_mysql_result($call_eleve_info, "0", "naissance");
    if (mb_strlen($eleve_naissance) == 10) {
        // YYYY-MM-DD
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 8, 2);
    } elseif (mb_strlen($eleve_naissance) == 8 ) {
        // YYYYMMDD
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 4, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 6, 2);
    } elseif (mb_strlen($eleve_naissance) == 19 ) {
        // YYYY-MM-DD xx:xx:xx
        $eleve_naissance_annee = mb_substr($eleve_naissance, 0, 4);
        $eleve_naissance_mois = mb_substr($eleve_naissance, 5, 2);
        $eleve_naissance_jour = mb_substr($eleve_naissance, 8, 2);
    } else {
        // Format inconnu
        $eleve_naissance_annee = "??";
        $eleve_naissance_mois = "??";
        $eleve_naissance_jour = "????";
    }

    $eleve_lieu_naissance = old_mysql_result($call_eleve_info, "0", "lieu_naissance");

	//=======================================
	//Date de sortie de l'??l??ve (timestamps), ?? z??ro par d??faut
	$eleve_date_de_sortie =old_mysql_result($call_eleve_info, "0", "date_sortie"); 

	//echo "Date de sortie de l'??l??ve dans la base :  $eleve_date_de_sortie <br/>";
	//conversion en seconde (timestamp)
	$eleve_date_de_sortie_time=strtotime($eleve_date_de_sortie);

	if ($eleve_date_de_sortie!=0) {
		//r??cup??ration du jour, du mois et de l'ann??e
		$eleve_date_sortie_jour=date('d', $eleve_date_de_sortie_time); 
		$eleve_date_sortie_mois=date('m', $eleve_date_de_sortie_time);
		$eleve_date_sortie_annee=date('Y', $eleve_date_de_sortie_time); 
		//echo "La date n'est pas nulle J:$eleve_date_sortie_jour   M:$eleve_date_sortie_mois   A:$eleve_date_sortie_annee";
	} else {
		$eleve_date_sortie_jour="00"; 
		$eleve_date_sortie_mois="00";
		$eleve_date_sortie_annee="0000"; 
	}
	//=======================================
	// Date d'entr??e de l'??l??ve dans l'??tablissement
	$eleve_date_entree =old_mysql_result($call_eleve_info, "0", "date_entree"); 
	$eleve_date_entree_time=strtotime($eleve_date_entree);
	if ($eleve_date_entree!=0) {
	//r??cup??ration du jour, du mois et de l'ann??e
		$eleve_date_entree_jour=date('d', $eleve_date_entree_time); 
		$eleve_date_entree_mois=date('m', $eleve_date_entree_time);
		$eleve_date_entree_annee=date('Y', $eleve_date_entree_time); 
	} else {
		$eleve_date_entree_jour="00"; 
		$eleve_date_entree_mois="00";
		$eleve_date_entree_annee="0000"; 
	}
	//=======================================

	//$eleve_no_resp = old_mysql_result($call_eleve_info, "0", "ereno");
	$reg_no_nat = old_mysql_result($call_eleve_info, "0", "no_gep");
	$reg_no_gep = old_mysql_result($call_eleve_info, "0", "elenoet");
	$reg_ele_id = old_mysql_result($call_eleve_info, "0", "ele_id");
	$reg_mef_code = old_mysql_result($call_eleve_info, "0", "mef_code");

	$reg_tel_pers = old_mysql_result($call_eleve_info, "0", "tel_pers");
	$reg_tel_port = old_mysql_result($call_eleve_info, "0", "tel_port");
	$reg_tel_prof = old_mysql_result($call_eleve_info, "0", "tel_prof");

	//$call_etab = mysql_query("SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)");
	$id_etab=0;
	if($reg_no_gep!="") {
		$call_etab = mysqli_query($GLOBALS["mysqli"], "SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$reg_no_gep' and e.id = j.id_etablissement)");
		$id_etab = @old_mysql_result($call_etab, "0", "id");
	}

	//echo "SELECT e.* FROM etablissements e, j_eleves_etablissements j WHERE (j.id_eleve='$eleve_login' and e.id = j.id_etablissement)<br />";

	//=========================
	// AJOUT: boireaus 20071107
	$sql="SELECT * FROM j_eleves_regime WHERE login='$eleve_login';";
	//echo "$sql<br />\n";
	$res_regime=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_regime)>0) {
		$lig_tmp=mysqli_fetch_object($res_regime);
		$reg_regime=$lig_tmp->regime;
		$reg_doublant=$lig_tmp->doublant;
	}
	else {
		$reg_regime="d/p";
		$reg_doublant="-";
	}
	//=========================


	if(!isset($ele_id)){
		$ele_id=old_mysql_result($call_eleve_info, "0", "ele_id");
	}

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='1'";
	//echo "$sql<br />\n";
	$res_resp1=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_resp1)>0) {
		$lig_no_resp1=mysqli_fetch_object($res_resp1);
		$eleve_no_resp1=$lig_no_resp1->pers_id;
	}
	else {
		$eleve_no_resp1=0;
	}
	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

	$sql="SELECT pers_id FROM responsables2 WHERE ele_id='$ele_id' AND resp_legal='2'";
	//echo "$sql<br />\n";
	$res_resp2=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_resp2)>0){
		$lig_no_resp2=mysqli_fetch_object($res_resp2);
		$eleve_no_resp2=$lig_no_resp2->pers_id;
	}
	else {
		$eleve_no_resp2=0;
	}


} else {
	if (isset($reg_nom)) {$eleve_nom = $reg_nom;}
	if (isset($reg_prenom)) {$eleve_prenom = $reg_prenom;}
	if (isset($reg_email)) {$eleve_email = $reg_email;}
	if (isset($reg_sexe)) {$eleve_sexe = $reg_sexe;}
	if (isset($reg_no_nat)) {$reg_no_nat = $reg_no_nat;}
	if (isset($reg_no_gep)) {$reg_no_gep = $reg_no_gep;}
	if (isset($birth_year)) {$eleve_naissance_annee = $birth_year;}
	if (isset($birth_month)) {$eleve_naissance_mois = $birth_month;}
	if (isset($birth_day)) {$eleve_naissance_jour = $birth_day;}

	if (isset($reg_lieu_naissance)) {$eleve_lieu_naissance=$reg_lieu_naissance;}

	//$eleve_no_resp = 0;
	$eleve_no_resp1 = 0;
	$eleve_no_resp2 = 0;
	$id_etab = 0;

	//=========================
	// AJOUT: boireaus 20071107
	// On ne devrait pas passer par l??.
	// Quand on arrive sur modify_elve.php, le login de l'??l??ve doit exister.
	$reg_regime="d/p";
	$reg_doublant="-";
	//=========================
}

if(getSettingValue('edt_version_defaut')=='1') {
	$avec_js_et_css_edt="y";
	$style_specifique[] = "edt_organisation/style_edt";
	$style_specifique[] = "templates/DefaultEDT/css/small_edt";
	$javascript_specifique[] = "edt_organisation/script/fonctions_edt";
}

$themessage  = 'Des informations ont ??t?? modifi??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *****************
$titre_page = "Gestion des ??l??ves | Ajouter/Modifier une fiche ??l??ve";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

//debug_var();

echo "<div align='center'>
	<div id='message_target_blank' style='color:red;'></div>
</div>\n";

/*
if ((isset($order_type)) and (isset($quelles_classes))) {
    echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
} else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
*/

/*
// D??sactiv?? pour permettre de renseigner un ELENOET manquant pour une conversion avec sconet
// Cela a en revanche ??t?? conserv?? sur la page index.php
// On ne devrait donc arriver ici lorsqu'une conversion est r??clam??e qu'en venant de conversion.php pour remplir un ELENOET
if(!getSettingValue('conv_new_resp_table')){
	$sql="SELECT 1=1 FROM responsables";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)>0){
		echo "<p>Une conversion des donn??es ??l??ves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}

	$sql="SHOW COLUMNS FROM eleves LIKE 'ele_id'";
	$test=mysql_query($sql);
	if(mysql_num_rows($test)==0){
		echo "<p>Une conversion des donn??es ??l??ves/responsables est requise.</p>\n";
		echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
		require("../lib/footer.inc.php");
		die();
	}
	else{
		$sql="SELECT 1=1 FROM eleves WHERE ele_id=''";
		$test=mysql_query($sql);
		if(mysql_num_rows($test)>0){
			echo "<p>Une conversion des donn??es ??l??ves/responsables est requise.</p>\n";
			echo "<p>Suivez ce lien: <a href='../responsables/conversion.php'>CONVERTIR</a></p>\n";
			require("../lib/footer.inc.php");
			die();
		}
	}
}
*/


?>
<!--form enctype="multipart/form-data" action="modify_eleve.php" method=post-->
<?php

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {

	// 20160809
	if((isset($eleve_login))&&(isset($saisir_lieu_naissance))) {
		echo "<p class='bold'><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | <a href=\"modify_eleve.php?eleve_login=$eleve_login&amp;saisir_lieu_naissance=y\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Choisir un autre lieu</a></p>\n";

		echo "<h2>D??finir le lieu de naissance de <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></h2>\n";

		// Par d??faut: France
		$code_pays=100;
		$code_commune_insee="";

		// Enregistrement actuel:
		if($eleve_lieu_naissance!="") {
			$info_lieu_naissance=get_commune($eleve_lieu_naissance, 1);
			if($info_lieu_naissance!="") {
				echo "<p>Le lieu de naissance actuellement enregistr?? est&nbsp;: ".$info_lieu_naissance."</p>";
			}
			$code_commune_insee=$eleve_lieu_naissance;
		}

		if(strstr($eleve_lieu_naissance, '@')) {
			$tmp_tab=explode('@',$code_commune_insee);
			$commune=$tmp_tab[1];

			$sql="SELECT * FROM pays WHERE code_pays='".$tmp_tab[0]."';";
			//echo "$sql<br />";
			$res_pays = mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_pays)==0) {
				$code_pays="";
			}
			else {
				$lig_pays=mysqli_fetch_object($res_pays);
				$code_pays=$lig_pays->code_pays;
			}
		}

		echo "<p class='bold' style='margin-top:1em'>D??finir un nouveau lieu de naissance&nbsp;:</p>";

		//if(!isset($initiale_commune_naissance)) {
			echo "<p>Choisir une commune parmi celles enregistr??es.<br />";
			echo "Afficher les communes dont le nom commence par";
			$alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$cpt=0;
			for($i=0;$i<strlen($alphabet);$i++) {
				$sql="SELECT 1=1 FROM communes WHERE commune LIKE '".substr($alphabet,$i,1)."%';";
				//echo "$sql<br />";
				$res_ini=mysqli_query($mysqli, $sql);
				if(mysqli_num_rows($res_ini)>0) {
					if($cpt>0) {
						echo "-";
					}
					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&saisir_lieu_naissance=y&initiale_commune_naissance=".substr($alphabet,$i,1)."' title=\"".mysqli_num_rows($res_ini)." commune(s) enregistr??e(s)\"> ".substr($alphabet,$i,1)." </a>";
					$cpt++;
				}
			}
			if($cpt==0) {
				echo " <span style='color:red'>aucune commune n'est enregistr??e actuellement</span>";
			}
			echo ".</p>";
		//}
		//else {

		if(isset($initiale_commune_naissance)) {
			echo "<form enctype='multipart/form-data' name='form_choix_commune_naissance' action='modify_eleve.php' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='eleve_login' value='$eleve_login' />
		<input type='hidden' name='valider_saisie_lieu_naissance' value='y' />
		<input type='hidden' name='initiale_commune_naissance' value='$initiale_commune_naissance' />
		<p>Choisir une commune de naissance&nbsp;: <select name='code_commune_insee'>";
			$sql="SELECT * FROM communes WHERE commune LIKE '$initiale_commune_naissance%' ORDER BY commune, departement;";
			//echo "$sql<br />";
			$res_commune=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_commune)>0) {
				while($lig_commune=mysqli_fetch_object($res_commune)) {
					$selected="";
					if($lig_commune->code_commune_insee==$code_commune_insee) {
						$selected=" selected='true'";
					}
					echo "
				<option value='".$lig_commune->code_commune_insee."'".$selected.">".$lig_commune->commune." (".$lig_commune->departement.")</option>";
				}
			}
			echo "</select></p>

		<p>Pays&nbsp;: <select name='code_pays'>";
			$sql="SELECT * FROM pays ORDER BY nom_pays;";
			//echo "$sql<br />";
			$res_pays = mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_pays)>0) {
				echo "
			<option value=''>---</option>";
				while($lig_pays=mysqli_fetch_object($res_pays)) {
					$selected="";
					if($lig_pays->code_pays==$code_pays) {
						$selected=" selected='true'";
					}
					echo "
			<option value='".$lig_pays->code_pays."'".$selected.">".$lig_pays->nom_pays."</option>";
				}
			}
			echo "</select></p>
		<p align='center'><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>";
		}

		echo "ou<br />
<form enctype='multipart/form-data' name='form_saisie_lieu_naissance' action='modify_eleve.php' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='eleve_login' value='$eleve_login' />
		<input type='hidden' name='valider_saisie_lieu_naissance' value='y' />
		<p>D??finir une nouvelle commune&nbsp;: <input type='text' name='nouvelle_commune' value=\"\" /></p>

		<p>D??partement&nbsp;: <select name='departement'>
			<option value=''>---</option>";
		$sql="SELECT DISTINCT departement FROM communes ORDER BY departement;";
		//echo "$sql<br />";
		$res_dpt= mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_dpt)>0) {
			while($lig_dpt=mysqli_fetch_object($res_dpt)) {
				$selected="";
				echo "
			<option value='".$lig_dpt->departement."'".$selected.">".$lig_dpt->departement."</option>";
				}
		}
		echo "</select></p>

		<p>Pays&nbsp;: <select name='code_pays'>";
		$sql="SELECT * FROM pays ORDER BY nom_pays;";
		//echo "$sql<br />";
		$res_pays = mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res_pays)>0) {
			echo "
			<option value=''>---</option>";
			while($lig_pays=mysqli_fetch_object($res_pays)) {
				$selected="";
				if($lig_pays->code_pays==$code_pays) {
					$selected=" selected='true'";
				}
				echo "
			<option value='".$lig_pays->code_pays."'".$selected.">".$lig_pays->nom_pays."</option>";
				}
		}
		echo "</select></p>
		<p align='center'><input type='submit' value='Enregistrer' /></p>
	</fieldset>
</form>
<p style='margin-top:1em;'><em>NOTES&nbsp;:</em></p>
<ul>
	<li>Si vous utilisez Si??cle/Sconet, la m??thode recommand??e pour d??finir le lieu de naissance consiste ?? effectuer une ";
		if(($_SESSION['statut']=='administrateur')||(($_SESSION['statut']=='scolarite')&&(getSettingAOui('GepiAccesMajSconetScol')))) {
			echo "<a href='../responsables/maj_import3.php'>Mise ?? jour d'apr??s Sconet</a>";
		}
		else {
			echo "Mise ?? jour d'apr??s Sconet <em>(en administrateur, ou en compte scolarit?? si le droit est donn??)</em>";
		}
		echo ".</li>
</ul>";
/*
function get_commune($code_commune_insee,$mode){
    global $mysqli;
	$retour="";

	if(strstr($code_commune_insee,'@')) {
		// On a affaire ?? une commune ??trang??re
		$tmp_tab=explode('@',$code_commune_insee);
		$sql="SELECT * FROM pays WHERE code_pays='$tmp_tab[0]';";
		//echo "$sql<br />";
		
		$res_pays = mysqli_query($mysqli, $sql);
		if($res_pays->num_rows == 0) {
			$retour = stripslashes($tmp_tab[1])." ($tmp_tab[0])";
		}else {
			$lig_pays = $res_pays->fetch_object();
			$res_pays->close();
			$retour=stripslashes($tmp_tab[1])." (".$lig_pays->nom_pays.")";
		}
	}
	else {
		$sql="SELECT * FROM communes WHERE code_commune_insee='$code_commune_insee';";
		$res = mysqli_query($mysqli, $sql);
		if($res->num_rows > 0) {
			$lig=$res->fetch_object();
			if($mode==0) {
				$retour=$lig->commune;
			}
			elseif($mode==1) {
				$retour=$lig->commune." (<em>".$lig->departement."</em>)";
			}
			elseif($mode==2) {
				$retour=$lig->commune." (".$lig->departement.")";
			}
			$res->close();
		}
	}
	return $retour;
}
*/
		require("../lib/footer.inc.php");
		die();
	}

	//eleve_login=$eleve_login&amp;definir_resp=1
	if(isset($definir_resp)){
		if(!isset($valider_choix_resp)){

			echo "<p class='bold'><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

			echo "<p>Choix du responsable l??gal <b>$definir_resp</b> pour <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></p>\n";

			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : "";
			$afficher_tous_les_resp=isset($_POST['afficher_tous_les_resp']) ? $_POST['afficher_tous_les_resp'] : "n";
			//$critere_recherche=preg_replace("/[^a-zA-Z????????????????????????????????????????????????????????????????_ -]/", "", $critere_recherche);
			$critere_recherche=preg_replace("/[^a-zA-Z_ -]/", "%", nettoyer_caracteres_nom($critere_recherche,"a"," _-",""));

			if($critere_recherche==""){
				$critere_recherche=mb_substr($eleve_nom,0,3);
			}

			$nb_resp=isset($_POST['nb_resp']) ? $_POST['nb_resp'] : 20;
			if(mb_strlen(preg_replace("/[0-9]/","",$nb_resp))!=0) {
				$nb_resp=20;
			}
			$num_premier_resp_rech=isset($_POST['num_premier_resp_rech']) ? $_POST['num_premier_resp_rech'] : 0;
			if(mb_strlen(preg_replace("/[0-9]/","",$num_premier_resp_rech))!=0) {
				$num_premier_resp_rech=0;
			}

			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";
			echo "<p align='center'><input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_resp' value='$nb_resp' size='3' />\n";
			echo " responsables dont le <b>nom</b> contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo " ?? partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_resp_rech' value='$num_premier_resp_rech' size='4' />\n";
			echo "</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_resp' id='afficher_tous_les_resp' value='n' />\n";
			echo "<p align='center'><input type='button' name='afficher_tous' value='Afficher tous les responsables' onClick=\"document.getElementById('afficher_tous_les_resp').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";


			echo "<form enctype='multipart/form-data' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_resp' value='$definir_resp' />\n";

			if($definir_resp==1){
				$pers_id=$eleve_no_resp1;
			}
			else{
				$pers_id=$eleve_no_resp2;
			}

			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
			//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
			$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp";
			if($afficher_tous_les_resp!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE rp.nom like '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY rp.nom, rp.prenom";
			if($afficher_tous_les_resp!='y'){
				$sql.=" LIMIT $num_premier_resp_rech, $nb_resp";
			}
			//echo "$sql<br />";
			$call_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			$nombreligne = mysqli_num_rows($call_resp);
			// si la table des responsables est non vide :
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
				echo "<table align='center' class='boireaus' summary='Responsable'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='' onchange='changement();' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'><b>Responsable l??gal $definir_resp</b></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'><b>Adresse</b></td>\n";
				echo "</tr>\n";

				$cpt=1;
				$alt=1;
				while($lig_resp=mysqli_fetch_object($call_resp)){
					$alt=$alt*(-1);
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					echo "<tr class='lig$alt white_hover'>\n";
					echo "<td><input type='radio' name='reg_resp".$definir_resp."' value='$lig_resp->pers_id' ";
					if($lig_resp->pers_id==$pers_id){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>\n";
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id&amp;quitter_la_page=y' target='_blank'>".my_strtoupper($lig_resp->nom)." ".casse_mot($lig_resp->prenom,'majf2')."</a></td>\n";
					echo "<td>";

					$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$lig_resp->pers_id' AND rp.adr_id=ra.adr_id";
					$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_adr)==0){
						// L'adresse du responsable n'est pas d??finie:
						//echo "<font color='red'>L'adresse du responsable l??gal n'est pas d??finie</font>: <a href='../responsables/modify_resp.php?pers_id=$lig_resp->pers_id' target='_blank'>D??finir l'adresse du responsable l??gal</a>\n";
						echo "&nbsp;";
					}
					else{
						$chaine_adr1="";
						$lig_adr=mysqli_fetch_object($res_adr);
						if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
						echo $chaine_adr1;
					}

					echo "</td>\n";
					echo "</tr>\n";
					$cpt++;
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>\n";
			}
			else{
				echo "<p>Aucun responsable n'est d??fini, ou aucun responsable correspond ?? la recherche.</p>\n";
			}

			echo "<p>Si le responsable l??gal ne figure pas dans la liste, vous pouvez l'ajouter ?? la base<br />\n";
			echo "(<i>apr??s avoir, le cas ??ch??ant, sauvegard?? cette fiche</i>)<br />\n";
			if($_SESSION['statut']=="scolarite") {
				echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables ??l??ves</a>]</p>\n";
			}
			else{
				echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables ??l??ves</a>]</p>\n";
			}

			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}

	// 20150724
	if(isset($_GET['ajout_resp_legal_0'])){

		echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

			echo "<form enctype='multipart/form-data' name='resp' action='modify_eleve.php' method='post'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='eleve_login' value='$eleve_login' />
		<input type='hidden' name='ele_id' value='$ele_id' />
		
		<p>Choix d'un ou plusieurs <strong>responsables non l??gaux</strong> pour <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></p>\n";

		$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp WHERE pers_id NOT IN (SELECT pers_id FROM responsables2 WHERE ele_id='".$ele_id."') ORDER BY rp.nom, rp.prenom";
		//echo "$sql<br />";
		$call_resp=mysqli_query($GLOBALS["mysqli"], $sql);
		$nombreligne = mysqli_num_rows($call_resp);
		// si la table des responsables est non vide :
		if ($nombreligne != 0) {

			echo "
		<p align='center'><input type='submit' name='valider_choix_resp' value='Enregistrer' /></p>
		<table class='boireaus boireaus_alt'>
			<thead>
				<tr>
					<th>Cocher</th>
					<th>Id</th>
					<th>Nom</th>
					<th>Pr??nom</th>
					<th>Voir/Modifier</th>
				</tr>
			</thead>
			<tbody>";
			$cpt=0;
			while($lig_resp=mysqli_fetch_object($call_resp)){

				echo "
				<tr>
					<td><input type='checkbox' name='add_resp_legal_0[]' id='add_resp_legal_0".$cpt."' value='$lig_resp->pers_id' onchange=\"checkbox_change(this.id)\" /></td>
					<td><label for='add_resp_legal_0".$cpt."'>$lig_resp->pers_id</label></td>
					<td><label for='add_resp_legal_0".$cpt."' id='texte_add_resp_legal_0".$cpt."'>$lig_resp->nom</label></td>
					<td><label for='add_resp_legal_0".$cpt."'>$lig_resp->prenom</label></td>
					<td><a href='../responsables/modify_resp.php?pers_id=".$lig_resp->pers_id."&amp;quitter_la_page=y' title=\"Voir/modifier la fiche dans un nouvel onglet\" target='_blank'><img src='../images/edit16.png' alt='??diter' class='icone16' /></a></td>
				</tr>";
				$cpt++;
			}
			echo "
			</tbody>
		</table>
		<center><input type='submit' value='Enregistrer' /></center>
		<input type='hidden' name='is_posted' value='add_resp_legal_0' />

		<div id='fixe'>
			<input type='submit' value='Enregistrer' />
		</div>";
		}
		else{
			echo "<p>Aucun responsable n'est disponible.</p>\n";
		}

		echo "<p>Si le responsable ne figure pas dans la liste, vous pouvez l'ajouter ?? la base<br />\n";
		echo "(<i>apr??s avoir, le cas ??ch??ant, sauvegard?? cette fiche</i>)<br />\n";
		if($_SESSION['statut']=="scolarite") {
			echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables ??l??ves</a>]</p>\n";
		}
		else{
			echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables ??l??ves</a>]</p>\n";
		}

		echo "
	</fieldset>
</form>

<script type='text/javascript'>
	".js_checkbox_change_style()."
</script>";

		require("../lib/footer.inc.php");
		die();
	}


	//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";



	if(isset($definir_etab)){
		if(!isset($valider_choix_etab)){
			echo "<p class=bold><a href=\"modify_eleve.php?eleve_login=$eleve_login\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";

			//====================================================
			$critere_recherche=isset($_POST['critere_recherche']) ? $_POST['critere_recherche'] : (isset($_GET['critere_recherche']) ? $_GET['critere_recherche'] : "");
			$afficher_tous_les_etab=isset($_POST['afficher_tous_les_etab']) ? $_POST['afficher_tous_les_etab'] : (isset($_GET['afficher_tous_les_etab']) ? $_GET['afficher_tous_les_etab'] : "n");
			//$critere_recherche=my_ereg_replace("[^0-9a-zA-Z????????????????????????????????????????????????????????????????_ -]", "", $critere_recherche);
			$critere_recherche=preg_replace("/[^0-9a-zA-Z????????????????????????????????????????????????????????????????_ %-]/", "", preg_replace("/ /","%",$critere_recherche));
			// Saisir un espace ou % pour plusieurs portions du champ de recherche ou pour une apostrophe
			$champ_rech=isset($_POST['champ_rech']) ? $_POST['champ_rech'] : (isset($_GET['champ_rech']) ? $_GET['champ_rech'] : "nom");
			$tab_champs_recherche_autorises=array('nom','cp','ville','id');
			if(!in_array($champ_rech,$tab_champs_recherche_autorises)) {$champ_rech="nom";}

			/*
			if($critere_recherche==""){
				$critere_recherche=mb_substr($eleve_nom,0,3);
			}
			*/

			$nb_etab=isset($_POST['nb_etab']) ? $_POST['nb_etab'] : (isset($_GET['nb_etab']) ? $_GET['nb_etab'] : 20);
			if(mb_strlen(preg_replace("/[0-9]/","",$nb_etab))!=0) {
				$nb_etab=20;
			}
			$num_premier_etab_rech=isset($_POST['num_premier_etab_rech']) ? $_POST['num_premier_etab_rech'] : (isset($_GET['num_premier_etab_rech']) ? $_GET['num_premier_etab_rech'] : 0);
			if(mb_strlen(preg_replace("/[0-9]/","",$num_premier_etab_rech))!=0) {
				$num_premier_etab_rech=0;
			}

			$etab_order_by=isset($_POST['etab_order_by']) ? $_POST['etab_order_by'] : (isset($_GET['etab_order_by']) ? $_GET['etab_order_by'] : "ville,nom");
			$tab_champs_etab_order_by_autorises=array('ville,nom','id','nom','cp');
			if(!in_array($etab_order_by,$tab_champs_etab_order_by_autorises)) {$etab_order_by="ville,nom";}


			echo "<div align='center'>\n";
			echo "<div style='width:90%; border: 1px solid black;'>\n";
			echo "<!-- Formulaire de recherche/filtrage parmi les ??tablissements -->\n";
			echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			echo "<input type='hidden' name='definir_etab' value='$definir_etab' />\n";
			echo "<table border='0' summary='Filtrage'>\n";
			echo "<tr>\n";
			echo "<td valign='top'>\n";
			//echo "<p align='center'>";
			echo "<input type='submit' name='filtrage' value='Afficher' /> les ";
			echo "<input type='text' name='nb_etab' value='$nb_etab' size='3' />\n";
			echo " ??tablissements dont ";
			echo "</td>\n";
			echo "<td valign='top'>\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_nom' value='nom' ";
			if($champ_rech=="nom") {echo "checked ";}
			echo "/> <label for='champ_rech_nom' style='cursor: pointer;'>le <b>nom</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_rne' value='id' ";
			if($champ_rech=="id") {echo "checked ";}
			echo "/> <label for='champ_rech_rne' style='cursor: pointer;'>le <b>RNE</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_cp' value='cp' ";
			if($champ_rech=="cp") {echo "checked ";}
			echo "/> <label for='champ_rech_cp' style='cursor: pointer;'>le <b>code postal</b></label><br />\n";

			echo "<input type='radio' name='champ_rech' id='champ_rech_ville' value='ville' ";
			if($champ_rech=="ville") {echo "checked ";}
			echo "/> <label for='champ_rech_ville' style='cursor: pointer;'>la <b>ville</b></label>\n";

			echo "</td>\n";
			echo "<td valign='top'>\n";
			echo " contient: ";
			echo "<input type='text' name='critere_recherche' value='$critere_recherche' />\n";
			echo "<br />\n";
			echo "&nbsp;&nbsp;&nbsp;?? partir de l'enregistrement ";
			echo "<input type='text' name='num_premier_etab_rech' value='$num_premier_etab_rech' size='4' />\n";
			//echo "</p>\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "<input type='hidden' name='afficher_tous_les_etab' id='afficher_tous_les_etab' value='n' />\n";
			echo "<p align='center'>";

			echo "<input type='submit' name='filtrage2' value='Afficher la s??lection' /> ou ";

			echo "<input type='button' name='afficher_tous' value='Afficher tous les ??tablissements' onClick=\"document.getElementById('afficher_tous_les_etab').value='y'; document.form_rech.submit();\" /></p>\n";
			echo "</form>\n";
			echo "</div>\n";
			echo "</div>\n";
			//====================================================


			echo "<!-- Formulaire de choix de l'??tablissement -->\n";
			echo "<form enctype='multipart/form-data' name='form_choix_etab' action='modify_eleve.php' method='post'>\n";
			echo add_token_field();

			echo "<p>Choix de l'??tablissement d'origine pour <b>".casse_mot($eleve_prenom,'majf2')." ".my_strtoupper($eleve_nom)."</b></p>\n";

			echo "<input type='hidden' name='eleve_login' value='$eleve_login' />\n";
			//echo "<input type='hidden' name='reg_no_gep' value='$reg_no_gep' />\n";
			echo "<input type='hidden' name='definir_etab' value='y' />\n";


			//$sql="SELECT * FROM etablissements ORDER BY ville,nom";
			$sql="SELECT * FROM etablissements e";
			if($afficher_tous_les_etab!='y'){
				if($critere_recherche!=""){
					$sql.=" WHERE e.$champ_rech LIKE '%".$critere_recherche."%'";
				}
			}
			$sql.=" ORDER BY $etab_order_by";
			if($afficher_tous_les_etab!='y'){
				$sql.=" LIMIT $num_premier_etab_rech, $nb_etab";
			}
			//echo "$sql<br />";

			$chaine_param_tri="";
			if(isset($eleve_login)) {$chaine_param_tri.= "&amp;eleve_login=$eleve_login";}
			if(isset($definir_etab)) {$chaine_param_tri.= "&amp;definir_etab=$definir_etab";}
			if(isset($nb_etab)) {$chaine_param_tri.= "&amp;nb_etab=$nb_etab";}
			if(isset($champ_rech)) {$chaine_param_tri.= "&amp;champ_rech=$champ_rech";}
			if(isset($critere_recherche)) {$chaine_param_tri.= "&amp;critere_recherche=$critere_recherche";}
			if(isset($num_premier_etab_rech)) {$chaine_param_tri.= "&amp;num_premier_etab_rech=$num_premier_etab_rech";}
			if(isset($order_type)) {$chaine_param_tri.= "&amp;order_type=$order_type";}
			if(isset($quelles_classes)) {$chaine_param_tri.= "&amp;quelles_classes=$quelles_classes";}
			if(isset($motif_rech)) {$chaine_param_tri.= "&amp;motif_rech=$motif_rech";}
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";
			if(isset($afficher_tous_les_etab)) {$chaine_param_tri.= "&amp;afficher_tous_les_etab=$afficher_tous_les_etab";}

			$call_etab=mysqli_query($GLOBALS["mysqli"], $sql);
			$nombreligne = mysqli_num_rows($call_etab);
			if ($nombreligne != 0) {
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
				echo "<table align='center' class='boireaus' border='1' summary='Etablissement'>\n";
				echo "<tr>\n";
				echo "<td><input type='radio' name='reg_etab' value='' /></td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=id";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>RNE</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Niveau</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#96C8F0;'>\n";
				echo "<b>Type</b>\n";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Nom</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=cp";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Code postal</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "<td style='font-weight:bold; text-align:center; background-color:#AAE6AA;'>\n";
				echo "<a href='".$_SERVER['PHP_SELF']."?etab_order_by=ville,nom";
				echo $chaine_param_tri;
				echo "'>";
				echo "<b>Ville</b>\n";
				echo "</a>";
				echo "</td>\n";
				echo "</tr>\n";

				$temoin_checked="n";

				$cpt=1;
				$alt=1;
				while($lig_etab=mysqli_fetch_object($call_etab)){
					//if($cpt%2==0){$couleur="silver";}else{$couleur="white";}
					$alt=$alt*(-1);
					echo "<tr class='lig$alt white_hover'>\n";
					/*
					echo "<td style='text-align:center; background-color:$couleur;'><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
					}
					echo "onchange='changement();' /></td>";
					echo "<td style='text-align:center; background-color:$couleur;'><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->niveau</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->type</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->nom</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->cp</td>\n";
					echo "<td style='text-align:center; background-color:$couleur;'>$lig_etab->ville</td>\n";
					*/
					echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
					if($lig_etab->id==$id_etab){
						echo "checked ";
						$temoin_checked="y";
					}
					echo "onchange='changement();' /></td>\n";
					echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
					echo "<td>$lig_etab->niveau</td>\n";
					echo "<td>$lig_etab->type</td>\n";
					echo "<td>$lig_etab->nom</td>\n";
					echo "<td>$lig_etab->cp</td>\n";
					echo "<td>$lig_etab->ville</td>\n";

					echo "</tr>\n";
					$cpt++;
				}

				if(($temoin_checked=="n")&&($id_etab!=0)) {
					$sql="SELECT * FROM etablissements WHERE id='$id_etab';";
					$res_etab=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_etab)>0) {
						$lig_etab=mysqli_fetch_object($res_etab);

						$alt=$alt*(-1);
						echo "<tr class='lig$alt white_hover'>\n";
						echo "<td><input type='radio' name='reg_etab' value='$lig_etab->id' ";
						echo "checked ";
						echo "onchange='changement();' /></td>\n";
						echo "<td><a href='../etablissements/modify_etab.php?id=$lig_etab->id' target='_blank'>$lig_etab->id</a></td>\n";
						echo "<td>$lig_etab->niveau</td>\n";
						echo "<td>$lig_etab->type</td>\n";
						echo "<td>$lig_etab->nom</td>\n";
						echo "<td>$lig_etab->cp</td>\n";
						echo "<td>$lig_etab->ville</td>\n";

						echo "</tr>\n";
					}
				}

				echo "</table>\n";
				echo "<p align='center'><input type='submit' name='valider_choix_etab' value='Valider' /></p>\n";
			}
			else{
				echo "<p>Aucun ??tablissement n'est d??fini</p>\n";
			}

			echo "<p>Si un ??tablissement ne figure pas dans la liste, vous pouvez l'ajouter ?? la base<br />\n";
			echo "en vous rendant dans [Gestion des bases-><a href='../etablissements/index.php' onclick=\"return confirm_abandon (this, change, '$themessage')\">Gestion des ??tablissements</a>]</p>\n";


			if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
			if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
			if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
			if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";


			echo "</form>\n";
		}
		else{
			// On valide l'enregistrement...
			// ... il faut le faire plus haut avant le header...
		}
		require("../lib/footer.inc.php");
		die();
	}
}

//debug_var();

echo "<form enctype='multipart/form-data' name='form_choix_eleve' action='modify_eleve.php' method='post'>\n";
//echo add_token_field();
echo "<p class=bold><a href=\"index.php\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";
if((getSettingAOui('active_mod_engagements'))&&(acces('/mod_engagements/saisie_engagements_user.php', $_SESSION['statut']))) {
	echo " | <a href='../mod_engagements/saisie_engagements_user.php?login_user=$eleve_login&amp;retour=modify_eleve'>Saisir des engagements</a>";
}

$num_eleve_courant=-1;
if ((isset($order_type)) and (isset($quelles_classes))) {
    //echo "<p class=bold><a href=\"index.php?quelles_classes=$quelles_classes&amp;order_type=$order_type\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

	echo " | <a href=\"index.php?quelles_classes=$quelles_classes";
	if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
	if(isset($mode_rech)){echo "&amp;mode_rech=$mode_rech";}
	echo "&amp;order_type=$order_type\" onclick=\"return confirm_abandon (this, change, '$themessage')\">Retour ?? votre recherche</a>\n";

	//echo "PLOP";
	include("index_call_data.php");
	//echo "PLIP";

	echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
	echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
	if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
	if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";

	if((isset($calldata))&&(mysqli_num_rows($calldata)>0)) {
		//echo "\$eleve_login=$eleve_login<br />";
		echo " | ";



		//echo "<select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		$cpt_eleve=0;
		$num_eleve=-1;

		$login_eleve_prec=0;
		$login_eleve_suiv=0;
		$temoin_tmp=0;

		$chaine_options_login_eleves="";
		while($lig_calldata=mysqli_fetch_object($calldata)) {
			// 20190927
			/*
			echo "<!--\n";
			var_dump($lig_calldata);
			echo "-->\n";
			*/
			/*
			echo "<option value='".$lig_calldata->login."'";
			if($lig_calldata->login==$eleve_login) {
				echo " selected";
				$num_eleve_courant=$cpt_eleve;
			}
			echo ">".$lig_calldata->nom." ".$lig_calldata->prenom."</option>\n";
			$cpt_eleve++;
			*/

			if($lig_calldata->login==$eleve_login){
				$chaine_options_login_eleves.="<option value='$lig_calldata->login' selected='true'>".$lig_calldata->nom." ".$lig_calldata->prenom."</option>\n";

				$num_eleve=$cpt_eleve;

				$temoin_tmp=1;
				if($lig_calldata=mysqli_fetch_object($calldata)){
					$login_eleve_suiv=$lig_calldata->login;
					$chaine_options_login_eleves.="<option value='$lig_calldata->login'>".$lig_calldata->nom." ".$lig_calldata->prenom."</option>\n";
				}
				else{
					$login_eleve_suiv=0;
				}
			}
			else{
				$chaine_options_login_eleves.="<option value='$lig_calldata->login'>".$lig_calldata->nom." ".$lig_calldata->prenom."</option>\n";
			}

			if($temoin_tmp==0){
				$login_eleve_prec=$lig_calldata->login;
			}
			$cpt_eleve++;
		}

		if("$login_eleve_prec"!="0") {
			echo "<a href='#' onclick=\"document.getElementById('choix_eleve_login').selectedIndex=eval(document.getElementById('choix_eleve_login').selectedIndex-1);return confirm_changement_eleve(change, '$themessage');\" title='El??ve pr??c??dent'><img src='../images/icons/arrow-left.png' class='icone16' /></a>";
		}

		echo "<select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		echo $chaine_options_login_eleves;
		echo "</select>\n";
		if("$login_eleve_suiv"!="0") {
			echo "<a href='#' onclick=\"document.getElementById('choix_eleve_login').selectedIndex=eval(document.getElementById('choix_eleve_login').selectedIndex+1);return confirm_changement_eleve(change, '$themessage');\" title='El??ve suivant'><img src='../images/icons/arrow-right.png' class='icone16' /></a>";
		}

	}
	elseif((isset($tab_eleve))&&(count($tab_eleve)>0)) {
		//echo " | <select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		$cpt_eleve=0;
		$num_eleve=-1;

		$login_eleve_prec=0;
		$login_eleve_suiv=0;
		$temoin_tmp=0;

		$chaine_options_login_eleves="";

		for($loop=0;$loop<count($tab_eleve);$loop++) {
			/*
			echo "<option value='".$tab_eleve[$loop]['login']."'";
			if($tab_eleve[$loop]['login']==$eleve_login) {
				echo " selected";
				$num_eleve_courant=$cpt_eleve;
			}
			echo ">".$tab_eleve[$loop]['nom']." ".$tab_eleve[$loop]['prenom']."</option>\n";
			$cpt_eleve++;
			*/

			if($tab_eleve[$loop]['login']==$eleve_login) {
				$chaine_options_login_eleves.="<option value='".$tab_eleve[$loop]['login']."' selected='true'>".$tab_eleve[$loop]['nom']." ".$tab_eleve[$loop]['prenom']."</option>\n";

				$num_eleve_courant=$cpt_eleve;
				$num_eleve=$cpt_eleve;

				$temoin_tmp=1;
				if(isset($tab_eleve[$loop+1]['login'])){
					$loop++;
					$login_eleve_suiv=$tab_eleve[$loop]['login'];
					$chaine_options_login_eleves.="<option value='".$tab_eleve[$loop]['login']."'>".$tab_eleve[$loop]['nom']." ".$tab_eleve[$loop]['prenom']."</option>\n";
				}
				else{
					$login_eleve_suiv=0;
				}
			}
			else{
				$chaine_options_login_eleves.="<option value='".$tab_eleve[$loop]['login']."'>".$tab_eleve[$loop]['nom']." ".$tab_eleve[$loop]['prenom']."</option>\n";
			}

			if($temoin_tmp==0){
				$login_eleve_prec=$tab_eleve[$loop]['login'];
			}
			$cpt_eleve++;

		}
		echo " | ";

		if("$login_eleve_prec"!="0") {
			echo "<a href='#' onclick=\"document.getElementById('choix_eleve_login').selectedIndex=eval(document.getElementById('choix_eleve_login').selectedIndex-1);return confirm_changement_eleve(change, '$themessage');\" title='El??ve pr??c??dent'><img src='../images/icons/arrow-left.png' class='icone16' /></a>";
		}

		echo "<select name='eleve_login' id='choix_eleve_login' onchange=\"confirm_changement_eleve(change, '$themessage');\">\n";
		echo $chaine_options_login_eleves;
		echo "</select>\n";

		if("$login_eleve_suiv"!="0") {
			echo "<a href='#' onclick=\"document.getElementById('choix_eleve_login').selectedIndex=eval(document.getElementById('choix_eleve_login').selectedIndex+1);return confirm_changement_eleve(change, '$themessage');\" title='El??ve suivant'><img src='../images/icons/arrow-right.png' class='icone16' /></a>";
		}

	}
	echo "<input type='submit' id='bouton_submit_changement_eleve' value='Changer' />\n";
}
/*
else {
    echo "<p class=bold><a href=\"index.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";
}
*/
echo "</p>\n";
echo "</form>\n";

echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	if(document.getElementById('bouton_submit_changement_eleve')) {
		document.getElementById('bouton_submit_changement_eleve').style.display='none';
	}

	function confirm_changement_eleve(thechange, themessage)
	{
		if (!(thechange)) thechange='no';
		if (thechange != 'yes') {
			document.form_choix_eleve.submit();
		}
		else{
			var is_confirmed = confirm(themessage);
			if(is_confirmed){
				document.form_choix_eleve.submit();
			}
			else{
				document.getElementById('choix_eleve_login').selectedIndex=$num_eleve_courant;
			}
		}
	}
</script>\n";


$AccesDetailConnexionEle=AccesInfoEle('AccesDetailConnexionEle', $eleve_login);

echo "<form enctype='multipart/form-data' name='form_rech' action='modify_eleve.php' method='post'>\n";
echo add_token_field();

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

//echo "\$eleve_login=$eleve_login<br />";

if(isset($eleve_login)) {
	//$sql="SELECT 1=1 FROM utilisateurs WHERE login='$eleve_login' AND statut='eleve';";
	$sql="SELECT auth_mode FROM utilisateurs WHERE login='$eleve_login';";
	$test_compte=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_compte)>0) {
		$compte_eleve_existe="y";
		$user_auth_mode=old_mysql_result($test_compte, 0, "auth_mode");
	}
	else {
		$compte_eleve_existe="n";
	}

	if(($compte_eleve_existe=="y")&&
		($AccesDetailConnexionEle)) {
		//$journal_connexions=isset($_POST['journal_connexions']) ? $_POST['journal_connexions'] : (isset($_GET['journal_connexions']) ? $_GET['journal_connexions'] : 'n');
		//$duree=isset($_POST['duree']) ? $_POST['duree'] : NULL;
	
		echo "<div style='float:right; width:; height:;'><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'><img src='../images/icons/document.png' width='16' height='16' alt='Journal des connexions' /></a></div>\n";
	}
}

// 20190101
if (isset($eleve_login)) {
	$acces_tel_ele=get_acces_tel_ele($eleve_login);
	$acces_mail_ele=get_acces_mail_ele($eleve_login);

	$acces_adresse_responsable=get_acces_adresse_resp($eleve_login);
	$acces_tel_responsable=get_acces_tel_resp($eleve_login);
	$acces_mail_responsable=get_acces_mail_resp($eleve_login);
}

//echo "<table border='1'>\n";
echo "<table summary='Informations ??l??ve'>\n";
echo "<tr>\n";
echo "<td>\n";

echo "<table cellpadding='5' summary='Infos 1'>\n";
echo "<tr>\n";

if (isset($eleve_login)) {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td>";

	if($_SESSION['statut']=='administrateur') {$avec_lien="y";}
	else {$avec_lien="n";}
	$lien_image_compte_utilisateur=lien_image_compte_utilisateur($eleve_login, "eleve", "_blank", $avec_lien);

	if($_SESSION['statut']=="administrateur") {
		if($compte_eleve_existe=="y") {
			echo "<a href='../utilisateurs/edit_eleve.php?critere_recherche=$eleve_nom' title=\"Acc??der au compte de l'utilisateur.\">".$eleve_login;
			if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
			echo "</a>";
		}
		elseif(isset($eleve_nom)) {
			echo "$eleve_login <a href='../utilisateurs/create_eleve.php?filtrage=Afficher&amp;critere_recherche=".preg_replace("/[^A-Za-z]/", "%", $eleve_nom)."'";
			echo " onclick=\"return confirm_abandon (this, change, '$themessage')\"";
			echo " title=\"Ajouter un compte d'utilisateur pour cet ??l??ve.\"><img src='../images/icons/buddy_plus.png' class='icone16' /></a>";
		}
		else {
			// On ne devrait jamais arriver l??.
			echo $eleve_login;
		}
	}
	else {
		echo $eleve_login;
		if($lien_image_compte_utilisateur!="") {echo " ".$lien_image_compte_utilisateur;}
	}
	echo temoin_compte_sso($eleve_login);
	echo "<input type='hidden' name='eleve_login' size='20' ";
	if ($eleve_login) echo "value='$eleve_login'";
	echo " /></td>\n";
} else {
	echo "<th style='text-align:left;'>Identifiant GEPI * : </th>
	<td><input type='text' name='reg_login' size='20' value=\"\" onchange='changement();' /></td>\n";
}
echo "</tr>\n";

if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")) {

	if($compte_eleve_existe=="y") {
		echo "<tr><th style='text-align:left;'>Authentification&nbsp;:</th>\n";

		echo "<td style='text-align:left;'>";
		echo "<select id='select_auth_mode' name='reg_auth_mode' onchange='changement()'>
		<option value='gepi' ";
		if ($user_auth_mode=='gepi') echo ' selected ';
		echo ">Locale (base Gepi)</option>
		<option value='ldap' ";
		if ($user_auth_mode=='ldap') echo ' selected ';
		echo ">LDAP</option>
		<option value='sso' ";
		if ($user_auth_mode=='sso') echo ' selected ';
		echo ">SSO (Cas, LCS, LemonLDAP)</option>
		</select>\n";
		echo "</td>\n";
		echo "</tr>\n";

		if(($_SESSION['statut']=='administrateur')&&(getSettingAOui('sso_cas_table'))) {
			$sso_table_login_ent="";
			if((isset($eleve_login))&&($eleve_login!='')) {
				$sso_table_login_ent=get_valeur_champ('sso_table_correspondance', "login_gepi='$eleve_login'", 'login_sso');
			}
			elseif(isset($_POST['login_sso'])) {$sso_table_login_ent=$_POST['login_sso'];}
			echo "
		<tr>
			<th style='text-align:left;'>Correspondance SSO&nbsp;:</td>
			<td><input type='text' name='login_sso' id='login_sso' value='".$sso_table_login_ent."' /></td>
		</tr>";
		}
	}

	echo "
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td><input type='text' name='reg_nom' size='20' ";
	if (isset($eleve_nom)) {
		echo "value=\"".$eleve_nom."\"";
	}
	echo " onchange='changement();' /></td>
	</tr>
	<tr>
		<th style='text-align:left;'>Pr??nom * : </th>
		<td><input type='text' name='reg_prenom' size='20' ";
	if (isset($eleve_prenom)) {
		echo "value=\"".$eleve_prenom."\"";
	}
	echo " onchange='changement();' /></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo "	<th style='text-align:left;'>Email : </th>\n";
	echo "	<td>";

	/*
	if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(getSettingValue('mode_email_ele')=='mon_compte')) {
		if (isset($eleve_email)) {
			echo $eleve_email;
		}
		else {
			echo "&nbsp;";
		}
	}
	else {
	*/
		echo "<input type='text' name='reg_email' size='18' ";
		if (isset($eleve_email)) {
			echo "value=\"".$eleve_email."\"";
		}
		echo " onchange='changement();' />";
	//}

	if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(getSettingValue('mode_email_ele')=='mon_compte')) {
		if (isset($eleve_email)) {
			$txt_attention="ATTENTION : Le choix effectu?? dans 'Configuration g??n??rale' est de laisser l'utilisateur param??trer son adresse mail dans 'G??rer mon compte'. Ne modifiez l'adresse mail que si c'est vraiment souhaitable.";
			echo " <img src='../images/icons/ico_attention.png' width='22' height='19' alt=\"$txt_attention\" title=\"$txt_attention\" />";
		}
	}

	if((isset($eleve_email))&&($eleve_email!='')) {
		$tmp_date=getdate();
		echo " <a href='mailto:".$eleve_email."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
		if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
		echo ",%0d%0aCordialement.' title=\"Envoyer un courriel\">";
		echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
		echo "</a>";
	}
	echo "</td>\n";
	echo "</tr>\n";

	if(getSettingAOui('ele_tel_pers')) {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel personnel&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_pers' size='20' ";
		if (isset($reg_tel_pers)) echo "value=\"".$reg_tel_pers."\"";
		echo " onchange='changement();' />";
		if((isset($reg_tel_pers))&&(mb_substr($reg_tel_pers,0,3)=="+33")) {
			echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_pers);
		}
		echo "</td>\n";
		echo "</tr>\n";
	}

	if(!getSettingAOui('ele_tel_port')) {
		// Par d??faut, si on n'a pas enregistr?? la pr??f??rence dans Configuration g??n??rale, on affiche le tel port.
	}
	else {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel portable&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_port' size='20' ";
		if (isset($reg_tel_port)) echo "value=\"".$reg_tel_port."\"";
		echo " onchange='changement();' />";
		if((isset($reg_tel_port))&&(mb_substr($reg_tel_port,0,3)=="+33")) {
			echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_port);
		}
		echo "</td>\n";
		echo "</tr>\n";
	}

	if(getSettingAOui('ele_tel_prof')) {
		echo "<tr>\n";
		echo "<th style='text-align:left;'>Tel professionnel&nbsp;: </th>\n";
		echo "<td><input type='text' name='reg_tel_prof' size='20' ";
		if (isset($reg_tel_prof)) echo "value=\"".$reg_tel_prof."\"";
		echo " onchange='changement();' />";
		if((isset($reg_tel_prof))&&(mb_substr($reg_tel_prof,0,3)=="+33")) {
			echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_prof);
		}
		echo "</td>\n";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo "<th style='text-align:left;'>Identifiant National : </th>\n";
	echo "<td><input type='text' name='reg_no_nat' size='20' ";
	if (isset($reg_no_nat)) echo "value=\"".$reg_no_nat."\"";
	echo " onchange='changement();' /></td>\n";

	echo "</tr>\n";

	//echo "<tr><td>Num??ro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
	echo "<tr><th style='text-align:left;'>Num??ro interne Sconet (<em style='font-weight:normal'>elenoet</em>) : </th><td><input type='text' name='reg_no_gep' id='reg_no_gep' size='20' ";
	if (isset($reg_no_gep)) echo "value=\"".$reg_no_gep."\"";
	echo " onchange='changement();' />";
	if ((isset($reg_no_gep))&&(getSettingAOui('active_module_trombinoscopes'))) echo "&nbsp;<img src='../images/icons/ico_aide.png' class='icone16' title=\"Sans num??ro ELENOET, il risque de ne pas ??tre possible d'associer une photo ?? l'??l??ve.\" />";
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr><th style='text-align:left;'>Num??ro interne Sconet (<em style='font-weight:normal'>ele_id</em>) : </th><td>";
	if (isset($reg_ele_id)) {
		echo $reg_ele_id;
		if((isset($reg_no_gep))&&($reg_no_gep=='')&&(preg_match('/^e[0-9]{1,}/', $reg_ele_id))) {
			echo " <a href='#' onclick=\"document.getElementById('reg_no_gep').value='$reg_ele_id'; return false;\" title=\"Prendre pour ELENOET, la valeur de l'ELE_ID.\"><img src='../images/icons/paste.png' class='icone16' /></a>";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>
	<th style='text-align:left;'>MEF : </th>
	<td>
		<select name='reg_mef_code' onchange='changement();'>
			<option value=''>---</option>";
	$sql="SELECT * FROM mef ORDER BY libelle_long, libelle_edition, libelle_court;";
	$res_mef=mysqli_query($GLOBALS["mysqli"], $sql);
	while($lig_mef=mysqli_fetch_object($res_mef)) {
		echo "
			<option value='$lig_mef->mef_code'";
		if($lig_mef->mef_code==$reg_mef_code) {echo " selected";}
		echo " title='$lig_mef->mef_code|$lig_mef->libelle_court|$lig_mef->libelle_long|$lig_mef->libelle_edition'>";
		if($lig_mef->libelle_edition!="") {
			echo $lig_mef->libelle_edition;
		}
		elseif($lig_mef->libelle_long!="") {
			echo $lig_mef->libelle_long;
		}
		elseif($lig_mef->libelle_court!="") {
			echo $lig_mef->libelle_court;
		}
		else {
			echo $lig_mef->mef_code;
		}
		echo "</option>";
	}
	echo "
		</select>";
	if(acces("/mef/admin_mef.php", $_SESSION['statut'])) {
		echo " <a href='../mef/admin_mef.php' title='G??rer les MEFS' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/configure.png' width='16' height='16' ></a>";
	}
	echo "
	</td>
</tr>\n";

	//Date dentr??e dans l'??tablissement
	echo "<tr>
	<th style='text-align:left;'>Date d'entr??e dans l'??tablissement : <br/>(<em style='font-weight:normal'>respecter format JJ/MM/AAAA</em>)</th>
	<td>
		<div class='norme'>
			Jour  <input type='text' name='date_entree_jour' id='date_entree_jour' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_jour) and ($eleve_date_entree_jour!="00") ) {echo $eleve_date_entree_jour;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off'  title=\"Vous pouvez modifier le jour de sortie ?? l'aide des fl??ches Up et Down du pav?? de direction.\" /> 
		Mois  <input type='text' name='date_entree_mois' id='date_entree_mois' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_mois) and ($eleve_date_entree_mois!="00")) {echo $eleve_date_entree_mois;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off'  title=\"Vous pouvez modifier le mois de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" /> 
		Ann??e <input type='text' name='date_entree_annee' id='date_entree_annee' size='4' onchange='changement();' value=\"";
	if (isset($eleve_date_entree_annee) and ($eleve_date_entree_annee!="0000")) {echo $eleve_date_entree_annee;}
	echo "\" onKeyDown='clavier_2(this.id,event,2000,2100);' AutoComplete='off'  title=\"Vous pouvez modifier l'ann??e de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" />
		<a href='javascript:date_entree_aujourdhui()' title=\"Aujourd'hui\"><img src='../images/icons/wizard.png' width='20' height='20' title=\"Aujourd'hui\" /></a>
	</td>
</tr>\n";

	//Date de sortie de l'??tablissement
	echo "<tr>
	<th style='text-align:left;'>Date de sortie de l'??tablissement : <br/>(<em style='font-weight:normal'>respecter format JJ/MM/AAAA</em>)</th>
	<td>
		<div class='norme'>
			Jour  <input type='text' name='date_sortie_jour' id='date_sortie_jour' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_jour) and ($eleve_date_sortie_jour!="00") ) {echo $eleve_date_sortie_jour;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off'  title=\"Vous pouvez modifier le jour de sortie ?? l'aide des fl??ches Up et Down du pav?? de direction.\" /> 
		Mois  <input type='text' name='date_sortie_mois' id='date_sortie_mois' size='2' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_mois) and ($eleve_date_sortie_mois!="00")) {echo $eleve_date_sortie_mois;}
	echo "\" onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off'  title=\"Vous pouvez modifier le mois de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" /> 
		Ann??e <input type='text' name='date_sortie_annee' id='date_sortie_annee' size='4' onchange='changement();' value=\"";
	if (isset($eleve_date_sortie_annee) and ($eleve_date_sortie_annee!="0000")) {echo $eleve_date_sortie_annee;}
	echo "\" onKeyDown='clavier_2(this.id,event,2000,2100);' AutoComplete='off'  title=\"Vous pouvez modifier l'ann??e de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" />
		<a href='javascript:date_sortie_aujourdhui()' title=\"Aujourd'hui\"><img src='../images/disabled.png' width='20' height='20' title=\"Aujourd'hui\" /></a>
		<script type='text/javascript'>
function date_entree_aujourdhui() {
	aujourdhui=new Date();
	document.getElementById('date_entree_jour').value=aujourdhui.getDate();
	document.getElementById('date_entree_mois').value=aujourdhui.getMonth()+1;
	annee=aujourdhui.getYear();
	if(annee<1000) {
		//alert(annee);
		if(annee>70) {
			annee=1900+annee;
		}
		else {
			annee=2000+annee;
		}
	}
	document.getElementById('date_entree_annee').value=annee;
	changement();
}

function date_sortie_aujourdhui() {
	aujourdhui=new Date();
	document.getElementById('date_sortie_jour').value=aujourdhui.getDate();
	document.getElementById('date_sortie_mois').value=aujourdhui.getMonth()+1;
	annee=aujourdhui.getYear();
	if(annee<1000) {
		//alert(annee);
		if(annee>70) {
			annee=1900+annee;
		}
		else {
			annee=2000+annee;
		}
	}
	document.getElementById('date_sortie_annee').value=annee;
	changement();
}
</script>
	</td>
</tr>\n";

}
else {
	echo "
	<tr>
		<th style='text-align:left;'>Nom * : </th>
		<td>";
	if (isset($eleve_nom)) {
		echo "$eleve_nom";
	}
	echo "</td>
	</tr>
	<tr>
		<th style='text-align:left;'>Pr??nom * : </th>
		<td>";
	if (isset($eleve_prenom)) {
		echo "$eleve_prenom";
	}
	echo "</td>
	</tr>";
	if($acces_mail_ele) {
		echo "
	<tr>
		<th style='text-align:left;'>Email : </th>
		<td>";
		if (isset($eleve_email)) {
			echo "$eleve_email";
		}
		if((isset($eleve_email))&&($eleve_email!='')) {
			$tmp_date=getdate();
			echo " <a href='mailto:".$eleve_email."?subject=".getSettingValue('gepiPrefixeSujetMail')."GEPI&amp;body=";
			if($tmp_date['hours']>=18) {echo "Bonsoir";} else {echo "Bonjour";}
			echo ",%0d%0aCordialement.' title=\"Envoyer un courriel\">";
			echo "<img src='../images/imabulle/courrier.jpg' width='20' height='15' alt='Envoyer un courriel' border='0' />";
			echo "</a>";
		}
		echo "</td>
	</tr>";
	}

	if($acces_tel_ele) {
		if(getSettingAOui('ele_tel_pers')) {
			echo "<tr>\n";
			echo "<th style='text-align:left;'>Tel personnel&nbsp;: </th>\n";
			echo "<td>";
			if (isset($reg_tel_pers)) echo $reg_tel_pers;
			if((isset($reg_tel_pers))&&(mb_substr($reg_tel_pers,0,3)=="+33")) {
				echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_pers);
			}
			echo "</td>\n";
			echo "</tr>\n";
		}

		if(getSettingAOui('ele_tel_port')) {
			echo "<tr>\n";
			echo "<th style='text-align:left;'>Tel portable&nbsp;: </th>\n";
			echo "<td>";
			if (isset($reg_tel_port)) echo $reg_tel_port;
			if((isset($reg_tel_port))&&(mb_substr($reg_tel_port,0,3)=="+33")) {
				echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_port);
			}
			echo "</td>\n";
			echo "</tr>\n";
		}

		if(getSettingAOui('ele_tel_prof')) {
			echo "<tr>\n";
			echo "<th style='text-align:left;'>Tel professionnel&nbsp;: </th>\n";
			echo "<td>";
			if (isset($reg_tel_prof)) echo $reg_tel_prof;
			if((isset($reg_tel_prof))&&(mb_substr($reg_tel_prof,0,3)=="+33")) {
				echo "<br />soit ".affiche_numero_tel_sous_forme_classique($reg_tel_prof);
			}
			echo "</td>\n";
			echo "</tr>\n";
		}
	}

	echo "
	<tr>
	<th style='text-align:left;'>Identifiant National : </th>\n";
	echo "<td>";
	if (isset($reg_no_nat)) echo "$reg_no_nat";
	echo "</td>\n";

	echo "</tr>\n";

	//echo "<tr><td>Num??ro GEP : </td><td><input type=text name='reg_no_gep' size=20 ";
	echo "<tr><th style='text-align:left;'>Num??ro interne Sconet (<i>elenoet</i>) : </th><td>";
	if (isset($reg_no_gep)) {
		echo "$reg_no_gep";
		if(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'){
			// N??cessaire pour les photos:
			echo "<input type='hidden' name='reg_no_gep' size='20' value=\"".$reg_no_gep."\" />\n";
		}
	}
	echo "</td>\n";
	echo "</tr>\n";

	if ((isset($eleve_date_entree))&&($eleve_date_entree!=0)) {
		//Date d'entr??e dans l'??tablissement
		echo "<tr><th style='text-align:left;'>Date d'entr??e dans l'??tablissement : <br/></th>";
		echo "<td><div class='norme'>";	
		
		if ((isset($eleve_date_entree_jour)) and ($eleve_date_entree_jour!="00")) echo $eleve_date_entree_jour."/";
		if ((isset($eleve_date_entree_mois)) and ($eleve_date_entree_mois!="00")) echo $eleve_date_entree_mois."/";
		if ((isset($eleve_date_entree_annee)) and ($eleve_date_entree_annee!="00")) echo $eleve_date_entree_annee; 
		echo "</td>\n";
		echo "</tr>\n";
	}

	if ((isset($eleve_date_de_sortie))&&($eleve_date_de_sortie!=0)) {
		//Date de sortie de l'??tablissement
		echo "<tr><th style='text-align:left;'>Date de sortie de l'??tablissement : <br/></th>";
		echo "<td><div class='norme'>";	
		
		if ((isset($eleve_date_sortie_jour)) and ($eleve_date_sortie_jour!="00")) echo $eleve_date_sortie_jour."/";
		if ((isset($eleve_date_sortie_mois)) and ($eleve_date_sortie_mois!="00")) echo $eleve_date_sortie_mois."/";
		if ((isset($eleve_date_sortie_annee)) and ($eleve_date_sortie_annee!="00")) echo $eleve_date_sortie_annee; 
		echo "</td>\n";
		echo "</tr>\n";
	}
}
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";
//echo "<td>\$reg_no_gep=$reg_no_gep</td>";
if((isset($reg_no_gep))&&($reg_no_gep!='')) {
	// R??cup??ration du nom de la photo en tenant compte des histoires des z??ro 02345.jpg ou 2345.jpg
	$photo=nom_photo($reg_no_gep);

	echo "<td align='center'>\n";
	//echo "reg_no_gep='$reg_no_gep'<br />";
	$temoin_photo="non";
	//echo "<td>\$photo=$photo</td>";
	if($photo){
		if(file_exists($photo)){
			$temoin_photo="oui";
			//echo "<td>\n";
			echo "<div align='center'>\n";
			// la photo sera r??duite si n??cessaire
			$dimphoto=dimensions_affichage_photo($photo,getSettingValue('l_max_aff_trombinoscopes'),getSettingValue('h_max_aff_trombinoscopes'));
			//echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border-right: 3px solid #FFFFFF; float: left;" alt="" />';
			echo '<img src="'.$photo.'" style="width: '.$dimphoto[0].'px; height: '.$dimphoto[1].'px; border: 0px; border: 3px solid #FFFFFF;" alt="" />';
			//echo "</td>\n";
			//echo "<br />\n";
			echo "</div>\n";
			echo "<div style='clear:both;'></div>\n";
		}
	}

	//echo "getSettingValue(\"GepiAccesGestPhotoElevesProfP\")=".getSettingValue("GepiAccesGestPhotoElevesProfP")."<br />";
  if ((getSettingValue("active_module_trombinoscopes")=='y') and 
  (($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")||
  (($_SESSION['statut']=='cpe')&&(getSettingValue("CpeAccesUploadPhotosEleves")=='yes'))||
  (($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')&&(isset($eleve_login))&&($is_pp)))) {
		echo "<div align='center'>\n";
		//echo "<span id='lien_photo' style='font-size:xx-small;'>";
		echo "<div id='lien_photo' style='border: 1px solid black; padding: 5px; margin: 5px;' class='fieldset_opacite50'>";
		echo "<a href='#' onClick=\"document.getElementById('div_upload_photo').style.display='';document.getElementById('lien_photo').style.display='';return false;\">";
		if($temoin_photo=="oui"){
			//echo "Modifier le fichier photo</a>\n";
			echo "Modifier le fichier photo</a>\n";
		}
		else{
			echo "Envoyer un fichier photo</a>\n";
			//echo "Envoyer<br />un fichier<br />photo</a>\n";
		}
		//echo "</span>\n";
		echo "</div>\n";
		echo "<div id='div_upload_photo' style='display:none;'>";
		echo "<input type='file' name='filephoto' />\n";
		if("$photo"!=""){
			if(file_exists($photo)){
				echo "<br />\n";
				echo "<input type='checkbox' name='suppr_filephoto' id='suppr_filephoto' value='y' onchange='changement();' /><label for='suppr_filephoto' style='cursor:pointer;'> Supprimer la photo existante</label>\n";
			}
		}
		echo "</div>\n";
		echo "</div>\n";
	}
	echo "</td>\n";
}


// Lien vers les inscriptions ?? des groupes:
if(isset($eleve_login)){
	echo "<td valign='top'>\n";
	// style='border: 1px solid black; text-align:center;'

	//echo "\$reg_regime=$reg_regime<br />";
	//echo "\$reg_doublant=$reg_doublant<br />";

	if(($_SESSION['statut']=="professeur")||($_SESSION['statut']=='cpe')) {
		echo "<table border='0' summary='Infos 2' class='fieldset_opacite50'>\n";

		echo "<tr><th style='text-align:left;'>N??(e) le: </th><td>$eleve_naissance_jour/$eleve_naissance_mois/$eleve_naissance_annee</td></tr>\n";
		if ($eleve_sexe == "M") {
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>Masculin</td></tr>\n";
		}
		elseif($eleve_sexe == "F"){
			echo "<tr><th style='text-align:left;'>Sexe: </th><td>F??minin</td></tr>\n";
		}

		echo "<tr><th style='text-align:left;'>R??gime: </th><td>";
		if ($reg_regime == 'i-e') {
			echo "Interne-extern??";
		}
		elseif ($reg_regime == 'int.') {
			echo "Interne";
		}
		elseif ($reg_regime == 'd/p') {
			echo "Demi-pensionnaire";
		}
		elseif ($reg_regime == 'ext.') {
			echo "Externe";
		}
		echo "</td></tr>\n";

		if ($reg_doublant == 'R') {echo "<tr><th style='text-align:left;'>Redoublant</th><td>Oui</td></tr>\n";}

		echo "</table>\n";

		if(acces("/eleves/visu_eleve.php", $_SESSION['statut'])) {
			echo "<br />\n";
			echo "<div style='border: 1px solid black; text-align:center;' class='fieldset_opacite50'>\n";
			echo "<a href='visu_eleve.php?ele_login=".$eleve_login."' title=\"Voir les onglets ??l??ve.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Voir' /> Consultation ??l??ve</a>";
			echo "</div>\n";
			echo "<br />\n";
		}
	}
	else{
		//=========================
		// AJOUT: boireaus 20071107
		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center'  summary='R??gime' class='fieldset_opacite50'>\n";
		echo "<tr>\n";
		echo "<th>R??gime: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>I-ext<br /><input type='radio' name='reg_regime' value='i-e' ";
		if ($reg_regime == 'i-e') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Int<br/><input type='radio' name='reg_regime' value='int.' ";
		if ($reg_regime == 'int.') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>D/P<br/><input type='radio' name='reg_regime' value='d/p' ";
		if ($reg_regime == 'd/p') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>Ext<br/><input type='radio' name='reg_regime' value='ext.' ";
		if ($reg_regime == 'ext.') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		//echo "<tr><td>&nbsp;</td></tr>\n";

		echo "<table style='border-collapse: collapse; border: 1px solid black;' align='center' summary='Redoublement' class='fieldset_opacite50'>\n";
		echo "<tr>\n";
		echo "<th>Redoublant: </th>\n";
		echo "<td style='text-align: center; border: 0px;'>O<br /><input type='radio' name='reg_doublant' value='R' ";
		if ($reg_doublant == 'R') {echo " checked";}
		echo " onchange='changement();' /></td>\n";
		echo "<td style='text-align: center; border: 0px; border-left: 1px solid #AAAAAA;'>N<br /><input type='radio' name='reg_doublant' value='-' ";
		if ($reg_doublant == '-') {echo " checked";}
		echo " onchange='changement();' /></td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		echo "<div style='border: 1px solid black; text-align:center;' class='fieldset_opacite50'>\n";
		echo "<a href='visu_eleve.php?ele_login=".$eleve_login."' title=\"Voir les onglets ??l??ve.\"><img src='../images/icons/ele_onglets.png' class='icone16' alt='Voir' /> Consultation ??l??ve</a>";
		echo "</div>\n";
		echo "<br />\n";
		//=========================

		echo "<div style='border: 1px solid black; text-align:center;' class='fieldset_opacite50'>\n";
		$sql="SELECT jec.id_classe,c.classe, jec.periode FROM j_eleves_classes jec, classes c WHERE jec.login='$eleve_login' AND jec.id_classe=c.id GROUP BY jec.id_classe ORDER BY jec.periode";
		$res_grp1=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_grp1)==0) {
			$acces_classes_ajout=acces('/classes/classes_ajout.php', $_SESSION['statut']);

			if($acces_classes_ajout) {
				echo "L'??l??ve n'est encore associ??(e) ?? aucune classe <a href='#' onclick=\" afficher_div('div_ajout_a_une_classe', 'y',-20,20);return false;\" title=\"Ajouter ?? une classe.\"><img src='../images/icons/add.png' class='icone16' alt='Ajouter' /></a>";
			}
			else {
				echo "<span title=\"Un compte administrateur est requis pour ajouter l'??l??ve ?? une classe.\">L'??l??ve n'est encore associ??(e) ?? aucune classe.</span>";
			}

			$titre_infobulle="Ajouter ?? une classe";
			$texte_infobulle="<form action='../classes/classes_ajout.php#ligne_$eleve_login' method='post' target='_blank'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p style='text-align:center;'>
			Ajouter ?? la classe de &nbsp;: 
			<select name='id_classe' id='id_classe_ajout'>
				<option value=''>---</option>";
			$sql=retourne_sql_mes_classes();
			$res_clas=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($res_clas)>0) {
				while($lig_clas=mysqli_fetch_object($res_clas)) {
					$texte_infobulle.="
				<option value='$lig_clas->id'>$lig_clas->classe</option>";
				}
			}
			$texte_infobulle.="
			</select>
			<input type='submit' name='filtrage' value='Ajouter' />
		</p>
	</fieldset>
</form>";
			$tabdiv_infobulle[]=creer_div_infobulle("div_ajout_a_une_classe",$titre_infobulle,"",$texte_infobulle,"",22,0,'y','y','n','n');
		}
		else {
			$acces_eleve_options=acces('/classes/eleve_options.php', $_SESSION['statut']);
			$acces_class_const=acces('/classes/classes_const.php', $_SESSION['statut']);
			while($lig_classe=mysqli_fetch_object($res_grp1)){
				if($acces_eleve_options) {
					echo "<a href='../classes/eleve_options.php?login_eleve=$eleve_login&amp;id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank' title=\"Consulter/modifier les enseignements suivis par cet ??l??ve.\">Enseignements suivis</a> en ";
				}
				else {
					echo "<a href='../eleves/visu_eleve.php?ele_login=".$eleve_login."&onglet=enseignements' target='_blank' title=\"Consulter la liste des enseignements suivis par cet ??l??ve.\">Enseignements suivis</a> en ";
				}

				if($acces_class_const) {
					$gepi_prof_suivi=retourne_denomination_pp($lig_classe->id_classe);
					echo "<a href='../classes/classes_const.php?id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank' title=\"Consulter/modifier la liste des ??l??ves de la classe.\nD??finir le ".$gepi_prof_suivi.", le CPE,...\">".preg_replace("/ /","&nbsp;",$lig_classe->classe)."</a>\n";
				}
				else {
					echo preg_replace("/ /","&nbsp;",$lig_classe->classe)."\n";
				}
				echo "<br />\n";

				//echo "D??finir/consulter <a href='../classes/classes_const.php?id_classe=$lig_classe->id_classe&amp;quitter_la_page=y' target='_blank'>le r??gime, le professeur principal, le CPE responsable</a> de l'??l??ve.\n";
				//echo "<br />\n";
			}
		}
		echo "</div>\n";

		echo "<div style='margin-top:1em; text-align:center;' class='fieldset_opacite50' title=\"Modalit??s d'accompagnement\">";
		// 20171124
		echo liste_modalites_accompagnement_eleve($eleve_login, "complet");

		if(acces_saisie_modalites_accompagnement()) {
			echo "<br /><a href='../gestion/saisie_modalites_accompagnement.php?login_eleve=".$eleve_login."' onclick=\"return confirm_abandon (this, change, '$themessage')\" style='font-size:small; text-decoration:none; color:black;'><img src='../images/icons/add.png' class='icone16' alt='Add' />Ajouter/Modifier des modalit??s d'accompagnement.</a>";
		}
		echo "</div>\n";

		//=========================
		// Infos compte utilisateur
		if((isset($compte_eleve_existe))&&($compte_eleve_existe=="y")&&(isset($eleve_login))&&
			(
				($_SESSION['statut']=="administrateur")||
				(($_SESSION['statut']=='scolarite')&&(getSettingAOui('ScolResetPassEle')))||
				(($_SESSION['statut']=='cpe')&&(getSettingAOui('CpeResetPassEle')))
			)
		) {
			echo "<div style='float: right; width:15 em; text-align: center; border: 1px solid black; margin:0.2em; background-image: url(\"../images/background/opacite50.png\");'>\n";
			if($_SESSION['statut']=="administrateur") {
				echo affiche_actions_compte($eleve_login);
				echo "<br />\n";
			}

			if((($user_auth_mode=='gepi')||
			(($user_auth_mode=='ldap')&&($gepiSettings['ldap_write_access'] == "yes")))&&
			(acces('/utilisateurs/reset_passwords.php', $_SESSION['statut']))) {
				echo affiche_reinit_password($eleve_login);
			}
			echo "</div>\n";
		}

		//=========================


		//==============================================
		// Engagements
		if(getSettingAOui('active_mod_engagements')) {
			$tab_engagements_user=get_tab_engagements_user($eleve_login);
			if(count($tab_engagements_user['indice'])>0) {
				echo "<div style='float: right; width:15em; text-align: center; margin:0.5em; margin:0.2em;' class='fieldset_opacite50' title=\"Engagements du responsable\">";
				if(acces("/mod_engagements/saisie_engagements_user.php", $_SESSION['statut'])) {
					echo "
			<div style='float: right; width:20px; height:20px;' title=\"Saisir/Modifier les engagements\"><a href='../mod_engagements/saisie_engagements_user.php?login_user=$eleve_login&amp;retour=modify_eleve'><img src='../images/icons/plus_moins.png' class='icone16' alt='Ajouter/Enlever'/></a></div>";
				}

				/*
				echo "<pre>";
				print_r($tab_engagements_user['indice']);
				echo "</pre>";
				*/
				echo "<div id='div_engagements_eleve'>";
				for($loop=0;$loop<count($tab_engagements_user['indice']);$loop++) {
					$detail_eng="";
					//if($tab_engagements_user['indice'][$loop]['id_type']=='id_classe') {
					if(($tab_engagements_user['indice'][$loop]['type']=='id_classe')&&($tab_engagements_user['indice'][$loop]['id_type']=='id_classe')) {
						$detail_eng=" en ".get_nom_classe($tab_engagements_user['indice'][$loop]['valeur']);
					}
					echo "<span title=\"".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."\n(".$tab_engagements_user['indice'][$loop]['engagement_description'].")\">".$tab_engagements_user['indice'][$loop]['nom_engagement'].$detail_eng."</span><br />";
				}
				echo "</div>\n";

				echo "</div>\n";
			}
		}
		//==============================================

	}

	//==============================================
	if(getSettingValue('edt_version_defaut')=='2') {
		$lien_edt=retourne_lien_edt2_eleve($eleve_login, time());
	}
	else {
		$lien_edt=retourne_lien_edt_eleve($eleve_login);
	}
	if($lien_edt!="") {
		echo "<div style='text-align:center; margin:0.5em;'>".$lien_edt."</div>\n";
	}

	$temoin_rss_ele=retourne_temoin_ou_lien_rss($eleve_login);
	if($temoin_rss_ele!="") {
		echo "<div style='text-align:center;'>".$temoin_rss_ele."</div>\n";
	}
	//==============================================

	echo "</td>\n";
}

echo "</tr>\n";
echo "</table>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

if (($reg_no_gep == '') and (isset($eleve_login))) {
   //echo "<font color=red>ATTENTION : Cet ??l??ve ne poss??de pas de num??ro GEP. Vous ne pourrez pas importer les absences ?? partir des fichiers GEP pour cet ??l??ves.</font>\n";
   echo "<font color='red'>ATTENTION : Cet ??l??ve ne poss??de pas de num??ro interne Sconet (<i>elenoet</i>). Vous ne pourrez pas importer les absences ?? partir des fichiers GEP/Sconet pour cet ??l??ve.<br />Vous ne pourrez pas d??finir l'??tablissement d'origine de l'??l??ve.<br />Cet ??l??ve ne pourra pas figurer dans le module trombinoscope.</font>\n";

	$sql="select value from setting where name='import_maj_xml_sconet'";
	$test_sconet=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_sconet)>0){
		$lig_tmp=mysqli_fetch_object($test_sconet);
		if($lig_tmp->value=='1'){
			echo "<br />";
			echo "<font color='red'>Vous ne pourrez pas non plus effectuer les mises ?? jour de ses informations depuis Sconet<br />(<i>l'ELENOET et l'ELE_ID ne correspondront pas aux donn??es de Sconet</i>).</font>\n";
		}
	}
}
//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

/*
if($_SESSION['statut']=="professeur") {
	if ($eleve_sexe == "M") {
		echo "<b>Sexe:</b> Masculin<br />";
	}
	elseif($eleve_sexe == "F"){
		echo "<b>Sexe:</b> F??minin<br />";
	}

	echo "<b>N??(e) le</b>: $eleve_naissance<br />\n";
}
else{
*/
if(($_SESSION['statut']!="professeur")&&($_SESSION['statut']!="cpe")) {
?>
<center>
<!--table border = '1' CELLPADDING = '5'-->
<table class='boireaus' cellpadding='5' summary='Sexe'>
<tr><td><div class='norme'><b>Sexe :</b> <br />
<?php
if (!(isset($eleve_sexe))) {$eleve_sexe="M";}
?>
<label for='reg_sexeM' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeM' value=M <?php if ($eleve_sexe == "M") { echo "CHECKED" ;} ?> onchange='changement();' /> Masculin</label>
<label for='reg_sexeF' style='cursor: pointer;'><input type=radio name=reg_sexe id='reg_sexeF' value=F <?php if ($eleve_sexe == "F") { echo "CHECKED" ;} ?> onchange='changement();' /> F??minin</label>
</div></td>

<td><div class='norme'>
<b>Date de naissance (<em>respecter format 00/00/0000</em>) :</b> <br />
<?php

echo "Jour <input type='text' name='birth_day' id='birth_day' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_jour)) {echo $eleve_naissance_jour;}
echo "' onKeyDown='clavier_2(this.id,event,1,31);' AutoComplete='off' title=\"Vous pouvez modifier le jour de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" />";

echo " Mois <input type='text' name='birth_month' id='birth_month' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_mois)) {echo $eleve_naissance_mois;}
echo "' onKeyDown='clavier_2(this.id,event,1,12);' AutoComplete='off' title=\"Vous pouvez modifier le mois de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" />";

echo " Ann??e <input type='text' name='birth_year' id='birth_year' size='2' onchange='changement();' value='";
if (isset($eleve_naissance_annee)) {echo $eleve_naissance_annee;}
echo "' onKeyDown='clavier_2(this.id,event,1970,2100);' AutoComplete='off' title=\"Vous pouvez modifier l'ann??e de naissance ?? l'aide des fl??ches Up et Down du pav?? de direction.\" />";


if(getSettingValue('ele_lieu_naissance')=='y') {
	echo "<br />\n";
	echo "<b>Lieu de naissance&nbsp;:</b> ";
	if(isset($eleve_lieu_naissance)) {echo get_commune($eleve_lieu_naissance,1);}
	else {echo "<span style='color:red'>Non d??fini</span>";}
	// 20160809
	if((isset($eleve_login))&&(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite"))) {
		echo "&nbsp;<a href='".$_SERVER['PHP_SELF']."?eleve_login=".$eleve_login."&amp;saisir_lieu_naissance=y' title=\"Saisir/modifier le lieu de naissance\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Modifier' /></a>";
	}
	echo "\n";
}
?>
</div></td>

</tr>
</table></center>

<p><b>Remarque</b> :
<br />- Les champs * sont obligatoires.</p>
<?php
}


echo "<input type=hidden name=is_posted value=\"1\" />\n";
if (isset($order_type)) echo "<input type=hidden name=order_type value=\"$order_type\" />\n";
if (isset($quelles_classes)) echo "<input type=hidden name=quelles_classes value=\"$quelles_classes\" />\n";
if (isset($motif_rech)) echo "<input type=hidden name=motif_rech value=\"$motif_rech\" />\n";
if (isset($mode_rech)) echo "<input type=hidden name=mode_rech value=\"$mode_rech\" />\n";
if (isset($eleve_login)) echo "<input type=hidden name=eleve_login value=\"$eleve_login\" />\n";
if (isset($mode)) echo "<input type=hidden name=mode value=\"$mode\" />\n";

if($_SESSION['statut']=='professeur'){
  if (($is_pp)&&(getSettingValue("active_module_trombinoscopes")=='y') && (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes')){
		echo "<center><input type=submit value=Enregistrer /></center>\n";
	}
}
else{
	echo "<center><input type=submit value=Enregistrer /></center>\n";
}
echo "</form>\n";

//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";


if(isset($eleve_login)){
	//$sql="SELECT rp.nom,rp.prenom,rp.pers_id,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.resp_legal='1' AND r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	//$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom,ra.* FROM responsables2 r, resp_adr ra, resp_pers rp WHERE r.pers_id=rp.pers_id AND rp.adr_id=ra.adr_id ORDER BY rp.nom, rp.prenom";
	$sql="SELECT DISTINCT rp.pers_id,rp.nom,rp.prenom FROM resp_pers rp ORDER BY rp.nom, rp.prenom";
	$call_resp=mysqli_query($GLOBALS["mysqli"], $sql);
	$nombreligne = mysqli_num_rows($call_resp);
	// si la table des responsables est non vide :
	if ($nombreligne != 0) {

		echo "<br />\n";
		echo "<hr />\n";
		echo "<h3>Envoi des bulletins par voie postale</h3>\n";

		//echo "\$eleve_no_resp1=$eleve_no_resp1<br />\n";

		echo "<i>Si vous n'envoyez pas les bulletins scolaires par voie postale, vous pouvez ignorer cette rubrique.</i><br />
	(<em>l'adresse peut n??anmoins aussi servir pour les modules absences, discipline,...</em>)<br />\n<br />\n";

		$temoin_tableau="";
		$chaine_adr1='';
		// Lorsque le $eleve_no_resp1 est non num??rique (cas sans sconet), on a p000000012 et il consid??re que p000000012==0
		// Il faut comparer des chaines de caract??res.
		//if($eleve_no_resp1==0){
		if("$eleve_no_resp1"=="0"){
			// Le responsable 1 n'est pas d??fini:
			echo "<p>Le responsable l??gal 1 n'est pas d??fini";
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo ".";
			}
			else{
				echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D??finir le responsable l??gal 1</a>";
			}
			echo "</p>\n";
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp1'";
			$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_resp)==0){
				// Bizarre: Le responsable 1 n'est pas d??fini:
				echo "<p>Le responsable l??gal 1 n'est pas d??fini";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo ".";
				}
				else{
					echo ": <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D??finir le responsable l??gal 1</a>";
				}
				echo "</p>\n";
			}
			else{
				$temoin_tableau="oui";
				$lig_resp=mysqli_fetch_object($res_resp);
				echo "<table border='0' summary='Responsable l??gal 1'>\n";
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable l??gal 1 est: </td>\n";
				echo "<td>";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom);
				}
				else{
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1' target='_blank'>";
					//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">";
					echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">";
					echo casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom);
					echo "</a>";
				}
				echo "</td>\n";

				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					//echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1'>Modifier l'association</a></td>\n";
					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=1";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					//echo "'>Modifier le responsable</a></td>\n";
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				if($acces_adresse_responsable) {
					echo "<tr valign='top'>\n";
					// La 1??re colonne est dans le rowspan

					$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp1' AND rp.adr_id=ra.adr_id";
					$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_adr)==0){
						// L'adresse du responsable 1 n'est pas d??finie:
						echo "<td colspan='2'>\n";
						//if($_SESSION['statut']=="professeur") {
						if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
							echo "L'adresse du responsable l??gal 1 n'est pas d??finie.\n";
						}
						else{
							//echo "L'adresse du responsable l??gal 1 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>D??finir l'adresse du responsable l??gal 1</a>\n";
							//echo "L'adresse du responsable l??gal 1 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">D??finir l'adresse du responsable l??gal 1</a>\n";
							echo "L'adresse du responsable l??gal 1 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">D??finir l'adresse du responsable l??gal 1</a>\n";
						}
						echo "</td>\n";
						$adr_id_1er_resp="";
					}
					else{
						echo "<td>\n";
						$lig_adr=mysqli_fetch_object($res_adr);
						$adr_id_1er_resp=$lig_adr->adr_id;
						if("$lig_adr->adr1"!=""){$chaine_adr1.="$lig_adr->adr1, ";}
						if("$lig_adr->adr2"!=""){$chaine_adr1.="$lig_adr->adr2, ";}
						if("$lig_adr->adr3"!=""){$chaine_adr1.="$lig_adr->adr3, ";}
						if("$lig_adr->adr4"!=""){$chaine_adr1.="$lig_adr->adr4, ";}
						if("$lig_adr->cp"!=""){$chaine_adr1.="$lig_adr->cp, ";}
						if("$lig_adr->commune"!=""){$chaine_adr1.="$lig_adr->commune";}
						if("$lig_adr->pays"!=""){$chaine_adr1.=" (<i>$lig_adr->pays</i>)";}
						echo $chaine_adr1;
						echo "</td>\n";
						//if($_SESSION['statut']!="professeur") {
						if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
							echo "<td>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
							echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp1&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
							echo "</td>\n";
						}
					}
					echo "</tr>\n";
				}
				else {
					echo "<tr valign='top'>\n";
					// La 1??re colonne est dans le rowspan
					echo "<td colspan='2'></td>\n";
					echo "</tr>\n";
				}
				//echo "</table>\n";
			}
		}





		$chaine_adr2='';
		//if($eleve_no_resp2==0){
		if("$eleve_no_resp2"=="0"){
			// Le responsable 2 n'est pas d??fini:
			if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>Le responsable l??gal 2 n'est pas d??fini: </p>\n";
 			}
			else{
				echo "<p>Le responsable l??gal 2 n'est pas d??fini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D??finir le responsable l??gal 2</a></p>\n";
			}
		}
		else{
			$sql="SELECT nom,prenom FROM resp_pers WHERE pers_id='$eleve_no_resp2'";
			$res_resp=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_resp)==0){
				// Bizarre: Le responsable 2 n'est pas d??fini:
				if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}

				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<p>Le responsable l??gal 2 n'est pas d??fini.</p>\n";
				}
				else{
					echo "<p>Le responsable l??gal 2 n'est pas d??fini: <a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D??finir le responsable l??gal 2</a></p>\n";
				}
			}
			else{
				$lig_resp=mysqli_fetch_object($res_resp);

				if($temoin_tableau!="oui"){
					echo "<table border='0' summary='Responsable l??gal 2'>\n";
					$temoin_tableau="oui";
				}
				echo "<tr valign='top'>\n";
				echo "<td rowspan='2'>Le responsable l??gal 2 est: </td>\n";
				//if($_SESSION['statut']=="professeur") {
				if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<td>".casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom)."</td>\n";
				}
				else{
					echo "<td><a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>".casse_mot($lig_resp->prenom,'majf2')." ".my_strtoupper($lig_resp->nom)."</a></td>\n";

					echo "<td><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_resp=2";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Changer de responsable</a></td>\n";
				}
				echo "</tr>\n";

				if($acces_adresse_responsable) {
					echo "<tr valign='top'>\n";
					// La 1??re colonne est dans le rowspan

					$sql="SELECT ra.* FROM resp_adr ra, resp_pers rp WHERE rp.pers_id='$eleve_no_resp2' AND rp.adr_id=ra.adr_id";
					$res_adr=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res_adr)==0){
						// L'adresse du responsable 2 n'est pas d??finie:
						echo "<td colspan='2'>\n";
						//if($_SESSION['statut']=="professeur") {
						if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
							echo "L'adresse du responsable l??gal 2 n'est pas d??finie.\n";
						}
						else{
							//echo "L'adresse du responsable l??gal 2 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>D??finir l'adresse du responsable l??gal 2</a>\n";
							//echo "L'adresse du responsable l??gal 2 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\">D??finir l'adresse du responsable l??gal 2</a>\n";
							echo "L'adresse du responsable l??gal 2 n'est pas d??finie: <a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' target='_blank' onclick=\"return confirm_abandon (this, change, '$themessage');\">D??finir l'adresse du responsable l??gal 2</a>\n";
						}
						echo "</td>\n";
					}
					else{
						echo "<td>\n";
						$lig_adr=mysqli_fetch_object($res_adr);

						if(!isset($adr_id_1er_resp)) {$adr_id_1er_resp='';}
						if(($lig_adr->adr_id!="")&&($lig_adr->adr_id!=$adr_id_1er_resp)){
							$adr_id_2eme_resp=$lig_adr->adr_id;
							if("$lig_adr->adr1"!=""){$chaine_adr2.="$lig_adr->adr1, ";}
							if("$lig_adr->adr2"!=""){$chaine_adr2.="$lig_adr->adr2, ";}
							if("$lig_adr->adr3"!=""){$chaine_adr2.="$lig_adr->adr3, ";}
							if("$lig_adr->adr4"!=""){$chaine_adr2.="$lig_adr->adr4, ";}
							if("$lig_adr->cp"!=""){$chaine_adr2.="$lig_adr->cp, ";}
							if("$lig_adr->commune"!=""){$chaine_adr2.="$lig_adr->commune";}
							if("$lig_adr->pays"!=""){$chaine_adr2.=" (<i>$lig_adr->pays</i>)";}

							//if("$chaine_adr1"=="$chaine_adr2"){
							if(casse_mot("$chaine_adr1",'min')==casse_mot("$chaine_adr2",'min')){
								echo "$chaine_adr2<br />\n<span style='color: red;'>Les adresses sont identiques, mais sont enregistr??es sous deux identifiants diff??rents (<i>$adr_id_1er_resp et $lig_adr->adr_id</i>); vous devriez modifier l'adresse pour pointer vers le m??me identifiant d'adresse.</span>";
							}
							else{
								echo "$chaine_adr2";
							}
						}
						else{
							echo "M??me adresse.";
						}
						echo "</td>\n";
						//if($_SESSION['statut']!="professeur") {
						if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
							echo "<td>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2#adresse' target='_blank'>Modifier l'adresse du responsable</a>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onClick='affiche_message_raffraichissement();' target='_blank'>Modifier l'adresse du responsable</a>\n";
							//echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"affiche_message_raffraichissement(); return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
							echo "<a href='../responsables/modify_resp.php?pers_id=$eleve_no_resp2&amp;quitter_la_page=y#adresse' onclick=\"return confirm_abandon (this, change, '$themessage');\" target='_blank'>Modifier l'adresse du responsable</a>\n";
							if((isset($adr_id_1er_resp))&&(isset($adr_id_2eme_resp))){
								if("$adr_id_1er_resp"!="$adr_id_2eme_resp"){
									echo "<br />";
									echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;modif_adr_pers_id=$eleve_no_resp2&amp;adr_id=$adr_id_1er_resp";
									if (isset($order_type)) {echo "&amp;order_type=$order_type";}
									if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
									if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
									if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
									//echo "'>Prendre l'adresse de l'autre responsable</a>";
									echo add_token_in_url();
									echo "' onclick=\"return confirm_abandon (this, change, '$themessage');\">Prendre l'adresse de l'autre responsable</a>";
								}
							}
							echo "</td>\n";
						}
					}
					echo "</tr>\n";
				}
				else {
					echo "<tr valign='top'>\n";
					// La 1??re colonne est dans le rowspan
					echo "<td colspan='2'></td>\n";
					echo "</tr>\n";
				}
				//echo "</table>\n";
			}
		}
		if($temoin_tableau=="oui"){echo "</table>\n";$temoin_tableau="non";}


		echo "<script type='text/javascript'>
	function affiche_message_raffraichissement() {
		document.getElementById('message_target_blank').innerHTML=\"Pensez ?? rafraichir la page apr??s modification de l'adresse responsable.<br />Cependant, si vous avez modifi?? des informations dans la pr??sente page, pensez ?? les enregistrer avant de recharger la page.\";
	}
</script>\n";


		if("$chaine_adr2"!=""){
			if("$chaine_adr1"!=""){
				if("$chaine_adr1"!="$chaine_adr2"){
					echo "<p><b>Les adresses des deux responsables l??gaux ne sont pas identiques. Par cons??quent, le bulletin sera envoy?? aux deux responsables l??gaux.</b></p>\n";
				}
				else{
					echo "<p><b>Les adresses des deux responsables l??gaux sont identiques. Par cons??quent, le bulletin ne sera envoy?? qu'?? la premi??re adresse.</b>";
					echo "</p>\n";
				}
			}
			else{
				echo "<p><b>Le bulletin ne sera envoy?? qu'au deuxi??me responsable.</b></p>\n";
			}
		}
		else{
			if("$chaine_adr1"!=""){
				echo "<p><b>Le bulletin ne sera envoy?? qu'au premier responsable.</b></p>\n";
			}
			else{
				echo "<p><b>Aucune adresse n'est renseign??e. Le bulletin ne pourra pas ??tre envoy??.</b></p>\n";
			}
		}


		//if(($eleve_no_resp1==0)||($eleve_no_resp2==0)){
		if(("$eleve_no_resp1"=="0")||("$eleve_no_resp2"=="0")){
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>Si le responsable l??gal ne figure pas dans la liste, prenez contact avec l'administrateur ou avec une personne disposant du statut 'scolarit??'.</p>\n";
			}
			else{
				echo "<p>Si le responsable l??gal ne figure pas dans la liste, vous pouvez l'ajouter ?? la base<br />\n";
				echo "(<i>apr??s avoir, le cas ??ch??ant, sauvegard?? cette fiche</i>)<br />\n";

				if($_SESSION['statut']=="scolarite") {
					echo "en vous rendant dans [<a href='../responsables/index.php'>Gestion des fiches responsables ??l??ves</a>]</p>\n";
				}
				else{
					echo "en vous rendant dans [Gestion des bases-><a href='../responsables/index.php'>Gestion des responsables ??l??ves</a>]</p>\n";
				}
			}
		}


		// 20150724
		echo "<p style='text-indent:-4em; margin-left: 4em; margin-top: 2em;'><em>NOTE&nbsp;:</em> A titre indicatif parce que par d??faut (*), les responsables non l??gaux ne sont pas concern??s par les bulletins.<br />";
		if(isset($ele_id)) {
			$sql="SELECT * FROM resp_pers rp, responsables2 r WHERE r.pers_id=rp.pers_id AND r.ele_id='".$ele_id."' AND r.resp_legal='0' ORDER BY rp.nom, rp.prenom;";
			$res_resp0=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_resp0)>0){
				while($lig_resp0=mysqli_fetch_object($res_resp0)) {
					echo "
<a href='../responsables/modify_resp.php?pers_id=".$lig_resp0->pers_id."' title=\"Voir/modifier la fiche de ce responsable.\" onclick=\"return confirm_abandon (this, change, '$themessage')\">".$lig_resp0->nom." ".$lig_resp0->prenom."</a> (<em>responsable non l??gal</em>)<br />";
				}
			}
		}
		// 20190101
		if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
			echo "
<a href='modify_eleve.php?eleve_login=".$eleve_login."&amp;ajout_resp_legal_0=y' onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/add.png' class='icone16' alt='Ajouter' /> Ajouter un responsable non l??gal</a><br />";
		}
		echo "
<br />
(*) Les responsables non l??gaux ne sont pas destinataires par d??faut des bulletins.<br />
Vous pouvez toutefois autoriser la g??n??ration de bulletins pour certains responsables non l??gaux<br />
(<em>cela peut se r??v??ler utile dans des cas compliqu??s, par exemple avec des enfants en famille d'accueil</em>).<br />
Vous pouvez m??me cr??er des comptes utilisateurs pour des responsables non l??gaux.<br />
Contr??lez".(($_SESSION['statut']=='administrateur') ? " les droits Responsables dans <a href='../gestion/droits_acces.php#responsable' target='_blank'>Gestion g??n??rale/Droits d'acc??s</a>" : ", en administrateur, les droits Responsables dans <strong>Gestion g??n??rale/Droits d'acc??s</strong>")."</p>";

	}
}



//if(isset($eleve_login)){
if((isset($eleve_login))&&(isset($reg_no_gep))&&($reg_no_gep!="")) {

	echo "<br />\n";
	echo "<hr />\n";

	echo "<h3>Etablissement d'origine</h3>\n";

	//$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$eleve_login'";
	$sql="SELECT * FROM j_eleves_etablissements WHERE id_eleve='$reg_no_gep'";
	$res_etab=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_etab)==0) {
		echo "<p>L'??tablissement d'origine de l'??l??ve n'est pas renseign??.";
		//if($_SESSION['statut']!="professeur") {
		if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
			echo "<br />\n";
			echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
			//echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
			if (isset($order_type)) {echo "&amp;order_type=$order_type";}
			if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
			if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
			if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
			echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Renseigner l'??tablissement d'origine</a>";
		}
		echo "</p>\n";
	}
	else{
		$lig_etab=mysqli_fetch_object($res_etab);

		if("$lig_etab->id_etablissement"==""){
			//if($_SESSION['statut']=="professeur") {
			if(!in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
				echo "<p>L'??tablissement d'origine de l'??l??ve n'est pas renseign??.</p>\n";
			}
			else{
				echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
				//echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;reg_no_gep=$reg_no_gep&amp;definir_etab=y";
				if (isset($order_type)) {echo "&amp;order_type=$order_type";}
				if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
				if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
				if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
				echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">D??finir l'??tablissement d'origine</a>";
				echo "</p>\n";
			}
		}
		else{
			$sql="SELECT * FROM etablissements WHERE id='$lig_etab->id_etablissement'";
			$res_etab2=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_etab2)==0) {
				echo "<p>L'association avec l'identifiant d'??tablissement existe (<i>$lig_etab->id_etablissement</i>), mais les informations correspondantes n'existent pas dans la table 'etablissement'.";
				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<br />\n";

					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}

					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'??tablissement d'origine</a>";
				}
				echo "</p>\n";
			}
			else{
				echo "<p>L'??tablissement d'origine de l'??l??ve est&nbsp;:<br />\n";
				$lig_etab2=mysqli_fetch_object($res_etab2);
				echo "&nbsp;&nbsp;&nbsp;";
				if($lig_etab2->niveau=="college"){
					echo "Coll??ge";
				}
				elseif($lig_etab2->niveau=="lycee"){
					echo "Lyc??e";
				}
				else{
					echo casse_mot($lig_etab2->niveau,'majf2');
				}
				echo " ".$lig_etab2->type." ".$lig_etab2->nom.", ".$lig_etab2->cp.", ".$lig_etab2->ville." (<i>$lig_etab->id_etablissement</i>)";
				//if($_SESSION['statut']!="professeur") {
				if(in_array($_SESSION['statut'], array("administrateur", "scolarite"))) {
					echo "<br />\n";
					echo "<a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;definir_etab=y";
					if (isset($order_type)) {echo "&amp;order_type=$order_type";}
					if (isset($quelles_classes)) {echo "&amp;quelles_classes=$quelles_classes";}
					if (isset($motif_rech)) {echo "&amp;motif_rech=$motif_rech";}
					if (isset($mode_rech)) {echo "&amp;mode_rech=$mode_rech";}
					echo "' onclick=\"return confirm_abandon (this, change, '$themessage')\">Modifier l'??tablissement d'origine</a>";
				}
				echo "</p>\n";
			}
		}
	}
	echo "<p><br /></p>\n";
}

if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='n')&&
		($AccesDetailConnexionEle)
	) {
		echo "<hr />\n";

		echo "<p><a href='".$_SERVER['PHP_SELF']."?eleve_login=$eleve_login&amp;journal_connexions=y#connexion' title='Journal des connexions'>Journal des connexions</a></p>\n";
	//}
}


if((isset($eleve_login))&&($compte_eleve_existe=="y")&&($journal_connexions=='y')&&
		($AccesDetailConnexionEle)
	) {
	echo "<hr />\n";
	// Journal des connexions
	echo "<a name=\"connexion\"></a>\n";
	if (isset($_POST['duree'])) {
		$duree = $_POST['duree'];
	} else {
		$duree = '7';
	}
	
	journal_connexions($eleve_login,$duree,'modify_eleve');
	echo "<p><br /></p>\n";
}


require("../lib/footer.inc.php");
?>
