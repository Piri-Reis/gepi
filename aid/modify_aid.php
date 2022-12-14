<?php
/*
 * Copyright 2001, 2019 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Julien Jocal, Stephane Boireau
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
//extract($_GET, EXTR_OVERWRITE);
//extract($_POST, EXTR_OVERWRITE);

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

//debug_var();

$msg='';

// Initialisation des variables
$flag = filter_input(INPUT_GET,'flag') ? filter_input(INPUT_GET,'flag') : (filter_input(INPUT_POST, 'flag') ? filter_input(INPUT_POST, 'flag') : NULL);
$aid_id = filter_input(INPUT_GET,'aid_id') !== NULL ? filter_input(INPUT_GET,'aid_id') : (filter_input(INPUT_POST, 'aid_id') !== NULL ? filter_input(INPUT_POST, 'aid_id') : NULL);
$indice_aid = filter_input(INPUT_GET,'indice_aid') !== NULL ? filter_input(INPUT_GET,'indice_aid') : (filter_input(INPUT_POST, 'indice_aid') !== NULL ? filter_input(INPUT_POST, 'indice_aid') : NULL);
$add_eleve = filter_input(INPUT_POST, 'add_eleve') ? filter_input(INPUT_POST, 'add_eleve') : NULL;
$add_prof = filter_input(INPUT_POST, 'add_prof') ? filter_input(INPUT_POST, 'add_prof') : NULL;
$add_prof_gest = filter_input(INPUT_POST, 'add_prof_gest') ? filter_input(INPUT_POST, 'add_prof_gest') : NULL;
$reg_prof_login = filter_input(INPUT_POST, 'reg_prof_login') ? filter_input(INPUT_POST, 'reg_prof_login') : NULL ;
$reg_add_eleve_login = filter_input(INPUT_POST, 'reg_add_eleve_login') ? filter_input(INPUT_POST, 'reg_add_eleve_login') : NULL ;

if((!isset($indice_aid))&&
(isset($aid_id))) {
	$indice_aid=get_valeur_champ("aid", "id='".$aid_id."'", "indice_aid");
}

if((isset($indice_aid))&&
(isset($aid_id))) {
	$sql="SELECT 1=1 FROM aid WHERE indice_aid='".$indice_aid."' AND id='".$aid_id."';";
	$test=mysqli_query($mysqli, $sql);
	if(mysqli_num_rows($test)==0) {
		header("Location: ../accueil.php?msg=Incoh??rence indice_aid/id_aid.");
		die();
	}
}

// V??rification du niveau de gestion des AIDs
if (NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id) <= 0) {
	header("Location: ../logout.php?auto=1");
	die();
}

include_once 'fonctions_aid.php';
global $mysqli;

$NiveauGestionAid_categorie=NiveauGestionAid($_SESSION["login"],$indice_aid);
$is_super_gestionnaire_aid=is_super_gestionnaire_aid($indice_aid, $_SESSION["login"]);

if (($NiveauGestionAid_categorie >= 5) and (isset($add_prof) and ($add_prof == "yes"))) {
	check_token();
    // On commence par v??rifier que le professeur n'est pas d??j?? pr??sent dans cette liste.
	$test2 = Prof_deja_membre ($reg_prof_login, $aid_id, $indice_aid)->num_rows;
    if ($test2 != "0") {
        $msg = "Le professeur que vous avez tent?? d'ajouter appartient d??j?? ?? cet AID (".strftime("%d/%m/%Y ?? %H:%M:%S").").";
    } else {
        if ($reg_prof_login != '') {
			$reg_data = Sauve_prof_membre ($reg_prof_login, $aid_id, $indice_aid);
            if (!$reg_data) { $msg = "Erreur lors de l'ajout du professeur (".strftime("%d/%m/%Y ?? %H:%M:%S").") !"; } else { $msg = "Le professeur a bien ??t?? ajout?? (".strftime("%d/%m/%Y ?? %H:%M:%S").") !"; }
        }
    }
    $flag = "prof";
}

if ((($NiveauGestionAid_categorie >= 10)||($is_super_gestionnaire_aid)) and (isset($add_prof_gest) and ($add_prof_gest == "yes"))) {
	check_token();
    // On commence par v??rifier que le professeur n'est pas d??j?? pr??sent dans cette liste.
    $test2 = Prof_deja_gestionnaire ($reg_prof_login, $aid_id, $indice_aid)->num_rows;
    if ($test2 != "0") {
        $msg = "L'utilisateur que vous avez tent?? d'ajouter appartient d??j?? ?? la liste des gestionnaires de cette AID (".strftime("%d/%m/%Y ?? %H:%M:%S").").";
    } else {
        if ($reg_prof_login != '') {
            $reg_data = Sauve_prof_gestionnaire ($reg_prof_login, $aid_id, $indice_aid);
            if (!$reg_data) { $msg = "Erreur lors de l'ajout de l'utilisateur (".strftime("%d/%m/%Y ?? %H:%M:%S").") !"; } else { $msg = "L'utilisateur a bien ??t?? ajout?? (".strftime("%d/%m/%Y ?? %H:%M:%S").") !"; }
        }
    }
    $flag = "prof_gest";
}

// On appelle les informations de l'aid pour les afficher :
$call_data = Get_famille_aid ($indice_aid);
$nom_aid = old_mysql_result($call_data, 0, "nom");
$activer_outils_comp = old_mysql_result($call_data, 0, "outils_complementaires");
$autoriser_inscript_multiples = old_mysql_result($call_data, 0, "autoriser_inscript_multiples");


if (isset($add_eleve) and ($add_eleve == "yes")) {
	check_token();

	if(isset($_POST['suppr_ele'])) {
		$suppr_ele=$_POST['suppr_ele'];
		for($loop=0;$loop<count($suppr_ele);$loop++) {
			$retour_suppr=suppr_ele_aid($suppr_ele[$loop], $aid_id, $indice_aid);
			$msg.=$retour_suppr;
		}
	}

	// Les ??l??ves responsable : ?? chercher parmi les ??l??ves de l'AID
	// On commence par supprimer les ??l??ves responsables
	// sql_query("DELETE FROM j_aid_eleves_resp WHERE id_aid='$aid_id' AND indice_aid='$indice_aid'");
	Supprime_eleve_responsable($aid_id, $indice_aid);

	$eleve_resp=isset($_POST['eleve_resp']) ? $_POST['eleve_resp'] : NULL;
	if(isset($eleve_resp)) {
		// Les ??l??ves responsable sont ?? s??lectionner parmi les ??l??ves de l'AID
		$call_eleves = Extrait_eleves_deja_membres ($aid_id, $indice_aid);
		$nombre = mysqli_num_rows($call_eleves);
		$i = "0";
		while ($lig_ele=mysqli_fetch_object($call_eleves)) {
			$login_eleve=$lig_ele->login;
			if (in_array($login_eleve, $eleve_resp)) {
				//sql_query("INSERT INTO j_aid_eleves_resp SET id_aid='$aid_id', login='$login_eleve', indice_aid='$indice_aid'");
				Sauve_eleve_responsable($aid_id, $indice_aid, $login_eleve);
			}
			$i++;
		}
	}

    // On commence par v??rifier que l'??l??ve n'est pas d??j?? pr??sent dans cette liste, ni dans aucune.
    if ($autoriser_inscript_multiples == 'y') {
		$filtre =  " AND id_aid='".$aid_id."' ";
	}
    else {
		$filtre =  "";
	}
	$sql = "SELECT * FROM j_aid_eleves WHERE (login='".$reg_add_eleve_login."' AND indice_aid='".$indice_aid."'".$filtre.")";
	//echo $sql;
	$test = mysqli_query($GLOBALS["mysqli"], $sql);
	$test2 = mysqli_num_rows($test);
	$msg = "";
    if ($test2 != "0") {
        $msg = "L'??l??ve que vous avez tent?? d'ajouter appartient d??j?? ?? une AID";
    } else {
        if ($reg_add_eleve_login != '') {
            $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_eleves SET login='$reg_add_eleve_login', id_aid='$aid_id', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg = "Erreur lors de l'ajout de l'??l??ve"; } else { $msg = "L'??l??ve a bien ??t?? ajout??."; }
        }
    }
    $msg .= "<br />Les modifications ont ??t?? enregistr??es.";
    $flag = "eleve";

    // Cas o?? la cat??gorie d'AID est utilis??e pour la gestion des acc??s au trombinoscope,
    // Lister (ou non) uniquement les ??l??ves sans photographie.
    if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')) {
        if(isset($_POST['eleves_sans_photos'])){
		    		$_SESSION['eleves_sans_photos']="y";
			  } else {unset($_SESSION['eleves_sans_photos']);}
    }
}

$toutes_aid = \filter_input(\INPUT_POST, 'toutes_aids');
if ($toutes_aid == "y") {
	check_token();

    $msg = "";
    // On r??cup??re la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requ??te : '.mysqli_error($GLOBALS["mysqli"]));
    while($temp = mysqli_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    // On appelle toutes les aids de la cat??gorie
    $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysqli_num_rows($calldata);
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = old_mysql_result($calldata, $i, "id");
        // Y-a-il des profs responsables
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs WHERE id_aid = '$aid_id' and indice_aid='$indice_aid'");
        if ($test1 == 0) {
          // Pas de profs responsable donc on applique les changements
          foreach($liste_profs as $key){
              $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$key', id_aid = '$aid_id', indice_aid='$indice_aid'");
              if (!$reg_data) { $msg .= "Erreur lors de l'ajout du professeur $key !<br />"; }
          }
        }
        $i++;
    }
    $flag = "prof";
    if ($msg == '') {$msg = "Les modifications ont ??t?? enregistr??es.";}
}

if (isset($_POST["selection_aids"]) and ($_POST["selection_aids"] == "y")) {
	check_token();

    $msg = "";
    // On r??cup??re la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requ??te : '.mysqli_error($GLOBALS["mysqli"]));
    while($temp = mysqli_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    if((!isset($_POST["liste_aids"]))||(count($_POST["liste_aids"])==0)) {
      $msg = "Aucun AID n'a ??t?? s??lectionn??.<br />";
    }
    else {
      foreach($_POST["liste_aids"] as $key1) {
        foreach($liste_profs as $key2){
          $test = sql_query1("SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$key1."' and id_utilisateur = '".$key2."' and indice_aid='$indice_aid'");
          if ($test == -1) {
            $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_utilisateurs SET id_utilisateur= '$key2', id_aid = '$key1', indice_aid='$indice_aid'");
            if (!$reg_data) { $msg .= "Erreur lors de l'ajout du professeur $key2 ?? l'aid dont l'identifiant est $key1 !<br />"; }
          }
        }
      }
    }
    $flag = "prof";
    if ($msg == '') {$msg = "Les modifications ont ??t?? enregistr??es.";}
}


if (isset($_POST["toutes_aids_gest"]) and ($_POST["toutes_aids_gest"] == "y")) {
	check_token();

    $msg = "";
    // On r??cup??re la liste des profs responsable de cette Aids :
    $sql = "SELECT id_utilisateur FROM j_aid_utilisateurs_gest j WHERE (j.id_aid='$aid_id' and j.indice_aid='$indice_aid')";
    $query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requ??te : '.mysqli_error($GLOBALS["mysqli"]));
    while($temp = mysqli_fetch_array($query)) {
        $liste_profs[] = $temp["id_utilisateur"];
    }
    // On appelle toutes les aids de la cat??gorie
    $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid'");
    $nombreligne = mysqli_num_rows($calldata);
    $i = 0;
    while ($i < $nombreligne){
        $aid_id = old_mysql_result($calldata, $i, "id");
        // Y-a-il des profs responsables
        $test1 = sql_query1("SELECT count(id_utilisateur) FROM j_aid_utilisateurs_gest WHERE id_aid = '$aid_id' and indice_aid='$indice_aid'");
        if ($test1 == 0) {
          // Pas de profs responsable donc on applique les changements
          foreach($liste_profs as $key){
              $reg_data = mysqli_query($GLOBALS["mysqli"], "INSERT INTO j_aid_utilisateurs_gest SET id_utilisateur= '$key', id_aid = '$aid_id', indice_aid='$indice_aid'");
              if (!$reg_data) { $msg .= "Erreur lors de l'ajout de l'utilisateur $key !<br />"; }
          }
        }
        $i++;
    }
    $flag = "prof_gest";
    if ($msg == '') {$msg = "Les modifications ont ??t?? enregistr??es.";}
}

$calldata = mysqli_query($GLOBALS["mysqli"], "SELECT nom FROM aid where (id = '$aid_id' and indice_aid='$indice_aid')");
$aid_nom = old_mysql_result($calldata, 0, "nom");
$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'];

if (!isset($_GET["aid_id"]) OR !isset($_GET["indice_aid"]) OR !isset($_GET["flag"])) {
	$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI'].'?flag='.$flag.'&aid_id='.$aid_id.'&indice_aid='.$indice_aid;
}
// Ajout d'un style sp??cifique pour l'AID
$style_specifique = "aid/style_aid";

$themessage = 'Des modifications n ont pas ??t?? enregistr??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *********************
$titre_page = "Gestion des $nom_aid | Modifier les $nom_aid";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************
// debug_var();

//echo "NiveauGestionAid(".$_SESSION["login"].",$indice_aid,$aid_id)=".NiveauGestionAid($_SESSION["login"],$indice_aid,$aid_id)."<br />";
$NiveauGestionAid_AID_courant=NiveauGestionAid($_SESSION["login"],$indice_aid, $aid_id);

// On affiche un select avec la liste des aid de cette cat??gorie
if ($NiveauGestionAid_AID_courant >= 5) {
    $sql = "SELECT id, nom FROM aid WHERE indice_aid = '".$indice_aid."' ORDER BY numero, nom";
}
else if ($NiveauGestionAid_AID_courant >= 1) {
    $sql = "SELECT a.id, a.nom FROM aid a, j_aid_utilisateurs_gest j WHERE a.indice_aid = '".$indice_aid."' and j.id_utilisateur = '" . $_SESSION["login"] . "' and j.indice_aid = '".$indice_aid."' and  a.id=j.id_aid ORDER BY a.numero, a.nom";
}

$query = mysqli_query($GLOBALS["mysqli"], $sql) OR DIE('Erreur dans la requ??te select * from aid : '.mysqli_error($GLOBALS["mysqli"]));
$nbre = mysqli_num_rows($query);

$aff_precedent = '';
$aff_suivant = '';

// On recherche les AID pr??c??dente et suivante
for($a = 0; $a < $nbre; $a++){
	$aid_p[$a]["id"] = old_mysql_result($query, $a, "id");

	// On teste pour savoir quel est le aid_id actuellement affich??
	if ($a != 0) {
		// Alors on propose un lien vers l'AID pr??c??dente
		if ($aid_p[$a]["id"] == $aid_id) {
			$aid_precedent = $aid_p[$a-1]["id"];
			$aff_precedent = '
			<a href="modify_aid.php?flag='.$flag.'&amp;indice_aid='.$indice_aid.'&amp;aid_id='.$aid_precedent.'" onclick="return confirm_abandon (this, change, \''.$themessage.'\')">Aid pr??c??dente&nbsp;</a>';
		}
	}

	if ($a < ($nbre - 1)) {
		// alors on propose un lien vers l'AID suivante
		if ($aid_p[$a]["id"] == $aid_id) {
			$aid_suivant = old_mysql_result($query, $a+1, "id");
			$aff_suivant = '
			<a href="modify_aid.php?flag='.$flag.'&amp;indice_aid='.$indice_aid.'&amp;aid_id='.$aid_suivant.'" onclick="return confirm_abandon (this, change, \''.$themessage.'\')">&nbsp;Aid suivante</a>';
		}
	}
}
?>
<form action="modify_aid.php" method="post" name="autre_aid">
	<p class="bold">
		<a href="index2.php?indice_aid=<?php echo $indice_aid; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">
			<img src="../images/icons/back.png" alt="Retour" class="back_link" />
			Retour
		</a>&nbsp;|&nbsp;<?php echo $aff_precedent; ?>
		<select name="aid_id" id='aid_id_autre_aid' onchange="confirm_changement_aid(change, '<?php echo $themessage;?>');">
<?php
$indice_aid_champ_select=-1;
$compteur_aid=0;
// On recommence le query
$query = mysqli_query($GLOBALS["mysqli"], $sql) OR trigger_error('Erreur dans la requ??te select * from aid : '.mysqli_error($GLOBALS["mysqli"]), E_USER_ERROR);
while($infos = mysqli_fetch_array($query)){
	// On affiche la liste des "<option>"
	if ($aid_id == $infos["id"]) {
		$selected = ' selected="selected" ';
		$indice_aid_champ_select=$compteur_aid;
	}else{
		$selected = '';
	}
?>
			<option value="<?php echo $infos["id"]; ?>"<?php echo $selected; ?>>
				&nbsp;<?php echo $infos["nom"]; ?>&nbsp;
			</option>
<?php
	$compteur_aid++;
}
?>
		</select>
		
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="flag" value="<?php echo $flag; ?>" /><?php echo $aff_suivant; ?>

<?php
	if(((!isset($flag))||($flag!="eleve"))&&(($NiveauGestionAid_AID_courant>=1))) {
		echo "
		| <a href='".$_SERVER['PHP_SELF']."?flag=eleve&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">??l??ves de l'AID</a>";
	}
	if(acces("/groupes/get_csv.php", $_SESSION['statut'])) {
		echo "
		| <a href='../groupes/get_csv.php?id_aid=".$aid_id."' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/csv.png' class='icone16' /> 
			Export CSV classique
		</a>";
	}
	if(acces("/groupes/mes_listes.php", $_SESSION['statut'])) {
		echo "
		| <a href='../groupes/mes_listes.php#aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/csv_enabled.png' class='icone16' /> 
			Export CSV sp??cifique
		</a>";
	}
	if(acces("/impression/impression.php", $_SESSION['statut'])) {
		echo "
		| <a href='../impression/impression.php#aid' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/pdf.png' class='icone16' /> 
			PDF
		</a>";
	}
	if((getSettingAOui('active_module_trombinoscopes'))&&(acces("/mod_trombinoscopes/trombinoscopes.php", $_SESSION['statut']))) {
		echo "
		| <a href='../mod_trombinoscopes/trombinoscopes.php?aid=$aid_id&etape=2' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/trombinoscope.png' class='icone16' /> 
			Trombinoscope
		</a>";
	}
	if(((!isset($flag))||($flag!="prof"))&&(($NiveauGestionAid_AID_courant>=2))) {
		echo "
		| <a href='".$_SERVER['PHP_SELF']."?flag=prof&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/edit_user.png' class='icone16' /> 
			Professeurs de l'AID
		</a>";
	}
	if(((!isset($flag))||($flag!="prof_gest"))&&(($NiveauGestionAid_AID_courant>=5))) {
		echo "
		| <a href='".$_SERVER['PHP_SELF']."?flag=prof_gest&aid_id=".$aid_id."&indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">
			<img src='../images/icons/configure.png' class='icone16' /> 
			Gestionnaires de l'AID
		</a>";
	}
	if($NiveauGestionAid_categorie==10) {
		echo "
		| <a href='config_aid.php?indice_aid=".$indice_aid."' onclick=\"return confirm_abandon (this, change, '$themessage')\">Cat??gorie AID</a>";
	}
?>
	</p>

	<script type='text/javascript'>
		//onchange="document.autre_aid.submit();"

		// Initialisation
		change='no';

		function confirm_changement_aid(thechange, themessage)
		{
			if (!(thechange)) thechange='no';
			if (thechange != 'yes') {
				document.autre_aid.submit();
			}
			else{
				var is_confirmed = confirm(themessage);
				if(is_confirmed){
					document.autre_aid.submit();
				}
				else{
					document.getElementById('aid_id_autre_aid').selectedIndex=<?php echo $indice_aid_champ_select;?>;
				}
			}
		}

	</script>
</form>
<?php


if ($flag == "prof") { ?>
<p class='grand'><?php 
	echo "$nom_aid  $aid_nom";
	if((isset($aid_id))&&(isset($indice_aid))&&($NiveauGestionAid_categorie>=5)) {
		echo " <a href='add_aid.php?action=modif_aid&aid_id=$aid_id&indice_aid=$indice_aid' title=\"Modifier cet AID.\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a>";
	}
?></p>
<?php
    $call_liste_data = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.prenom, u.nom, u.statut FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid='$aid_id' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')  order by u.nom, u.prenom");
    $nombre = mysqli_num_rows($call_liste_data);
?>
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
<?php

	echo add_token_field();

    if ($nombre !=0) {
	?>
	<p class='bold'>Liste des professeurs responsables&nbsp;:</p>
	<p>
		Les noms des professeurs <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em> ci-dessous figurent <em>(selon le param??trage)</em> 
		sur les bulletins officiels et/ou les bulletins simplifi??s.
	</p>
<?php
        if ($activer_outils_comp == "y") {
?>
	<p>
		De plus ces professeurs <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em> peuvent modifier les fiches projet <em>(si l'administrateur a activ?? cette possibilit??)</em>.
	</p>
	<?php } ?>
	<hr />
	<table class="aid_tableau">
<?php
    }
    $i = "0";
    while ($i < $nombre) {
        $login_prof = old_mysql_result($call_liste_data, $i, "login");
        $nom_prof = old_mysql_result($call_liste_data, $i, "nom");
        $prenom_prof = old_mysql_result($call_liste_data, $i, "prenom");
        $statut_prof = old_mysql_result($call_liste_data, $i, "statut");
?>
		<tr>
			<td>
				<strong><?php echo $nom_prof." ".$prenom_prof; 
				if($statut_prof!='professeur') {
					echo " <em>(".$statut_prof.")</em>";
				}
				?></strong>
			</td>
			<td>
				<a href='../lib/confirm_query.php?liste_cible=<?php echo $login_prof; ?>&amp;liste_cible2=<?php echo $aid_id; ?>&amp;liste_cible3=<?php echo $indice_aid; ?>&amp;action=del_prof_aid<?php echo add_token_in_url(); ?>'>
					<font size=2>
					<img src="../images/icons/delete.png" title="Supprimer ce professeur" alt="Supprimer" />
					</font>
				</a>
			</td>
		</tr>
<?php
    $i++;
    }

    if ($nombre !=0) {
?>
	</table>
<?php
    } else {
?>
	<h4 style="color: red;">Il n'y a pas actuellement de professeur <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em> responsable !</h4>
<?php
    }
?>
	<p class='bold'>Ajouter un professeur <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em> responsable ?? la liste de l'AID&nbsp;:</p>
	<select size=1 name="reg_prof_login" onchange="changement()">
		<option value=''>(aucun)</option>
	<?php
	// Le statut autre n'a pas acc??s.
	//$sql="SELECT login, nom, prenom, statut FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut = 'autre') order by nom";
	//$sql="SELECT login, nom, prenom, statut FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur') order by nom";
	$sql="SELECT login, nom, prenom, statut FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' OR statut='scolarite' OR statut='cpe') order by statut, nom";
	$call_prof = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombreligne = mysqli_num_rows($call_prof);
	$statut_precedent='';
	while ($lig_prof=mysqli_fetch_object($call_prof)) {
		if($lig_prof->statut!=$statut_precedent) {
			if($statut_precedent!='') {
				echo "
		</optgroup>";
			}
			echo "
		<optgroup label='-- ".$lig_prof->statut." --'>\n";
			$statut_precedent=$lig_prof->statut;
		}
		echo "
			<option value=\"$lig_prof->login\" title=\"".casse_mot($lig_prof->nom,'maj')." ".casse_mot($lig_prof->prenom,'majf2')." ($lig_prof->login)\">".casse_mot($lig_prof->nom,'maj').' '.casse_mot($lig_prof->prenom,'majf2')." (".$lig_prof->statut.")</option>";
	}
	if($statut_precedent!='') {
		echo "
		</optgroup>";
	}
	?>
	</select>
	<p>
		<input type="hidden" name="add_prof" value="yes" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="submit" value='Enregistrer' />
	</p>
</form>
<?php
    if ($nombre != 0) {
?>
<hr />
<h2>Affecter cette liste aux Aids sans professeur <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em>  responsable</h2>
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
	<p>
		Si vous cliquez sur le bouton ci-dessous, les professeurs de la liste ci-dessus seront ??galement affect??s 
		?? toutes les AIDs de cette cat??gorie n'ayant pas encore de professeur <em title="Un professeur, un CPE ou un compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em>  responsable.
	</p>
	<?php echo add_token_field(); ?>
	<p>
		<input type="hidden" name="toutes_aids" value="y" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<br />
		<input type="submit" value="Affecter la liste aux Aids sans professeur" />
	</p>
</form>
<hr />
<h2>Affecter cette liste aux Aids s??lectionn??es</h2>
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
	<?php echo add_token_field(); ?>
	<p>
		<input type="hidden" name="selection_aids" value="y" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
	</p>
<?php
      // On appelle toutes les aids de la cat??gorie

      // $calldata = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid WHERE indice_aid='$indice_aid' ORDER BY numero, nom");
      // $nombreligne = mysqli_num_rows($calldata);
	  $calldata = Extrait_aid_sur_indice_aid ($indice_aid);
	  $nombreligne = $calldata->num_rows;
      $i = 0;
?>
	<select name="liste_aids[]" size="6" onchange="changement()" multiple>
<?php
while($obj = $calldata->fetch_object()) {
?>
		<option value="<?php echo $obj->id; ?>"><?php echo $obj->nom; ?></option>
<?php
	$i++;
}
?>
	</select>
	<p>Si vous cliquez sur le bouton ci-dessous, les professeurs <em title="Professeurs, CPE ou compte scolarit?? jouant un r??le de professeur sur l'AID, c'est-??-dire, par exemple, habilit?? ?? saisir les notes/appr??ciations des bulletins si l'AID est visible sur les bulletins.">(*)</em>  de la liste de cette AID 
		seront ??galement affect??s ?? toutes les AIDs s??lectionn??es ci-dessus.
	</p>
	<p><input type="submit" value="Affecter la liste aux Aids s??lectionn??es ci-dessus" /></p>

</form>
<?php
   }
}

if ($flag == "prof_gest") { ?>
<p class='grand'><?php echo "$nom_aid  $aid_nom"; ?></p>
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
	<?php echo add_token_field();
	$call_liste_data = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.prenom, u.nom, u.statut FROM utilisateurs u, j_aid_utilisateurs_gest j WHERE (j.id_aid='$aid_id' and u.login=j.id_utilisateur and j.indice_aid='$indice_aid')  order by u.nom, u.prenom");
    $nombre = mysqli_num_rows($call_liste_data);
    if ($nombre !=0) {
    ?>
	<p>Les gestionnaires peuvent ajouter ou supprimer des &eacute;l&egrave;ves dans cette AID.</p>
	<p class='bold'>Liste des utilisateurs gestionnaires :</p>
	<hr />
	<table class="aid_tableau" >
    <?php
    }
    while ($lig_user=mysqli_fetch_object($call_liste_data)) {
    ?>
		<tr>
			<td>
				<strong>
					<?php echo $lig_user->nom." ".$lig_user->prenom." (".$lig_user->statut.")"; ?>
				</strong>
			</td>
			<td>
				<a href='../lib/confirm_query.php?liste_cible=<?php echo $lig_user->login; ?>&amp;liste_cible2=<?php echo $aid_id; ?>&amp;liste_cible3=<?php echo $indice_aid; ?>&amp;action=del_gest_aid<?php echo add_token_in_url(); ?>'>
					<img src="../images/icons/delete.png" title="Supprimer ce professeur" alt="Supprimer" />
				</a>
			</td>
		</tr>
    <?php
    }

    if ($nombre != 0) { ?>
	</table>
    <?php
    } else { ?>
	<h4 style="color: red;">Il n'y a pas actuellement d'utilisateur gestionnaire !</h4>
    <?php
    }
    ?>
    <p class='bold'>Ajouter un utilisateur ?? la liste des gestionnaires de l'AID :</p>
    <select size=1 name="reg_prof_login" onchange="changement()">
		<option value=''>(aucun)</option>
    <?php
    $call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT login, nom, prenom, statut FROM utilisateurs WHERE  etat!='inactif' AND (statut = 'professeur' or statut = 'cpe' or statut = 'scolarite') order by nom, prenom");
    $nombreligne = mysqli_num_rows($call_prof);
    while ($lig_user=mysqli_fetch_object($call_prof)) {
    	echo "
		<option value=\"$lig_user->login\" title=\"".casse_mot($lig_user->nom,'maj')." ".casse_mot($lig_user->prenom,'majf2')." ($lig_user->login)\">";
		?>
			<?php echo casse_mot($lig_user->nom, "maj")." ".casse_mot($lig_user->prenom,'majf2')." (".$lig_user->statut.")"; ?>
		</option>
		<?php
    }
    ?>
    </select>
	<p>
		<input type="hidden" name="add_prof_gest" value="yes" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="submit" value='Enregistrer' />		
	</p>
</form>

    <?php
    if ($nombre != 0) {
    ?>
<hr />
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
	<h2>Affecter cette liste aux Aids sans gestionnaire</h2>
	<p>
		Si vous cliquez sur le bouton ci-dessous, les utilisateurs de la liste ci-dessus seront ??galement affect??s 
		?? toutes les AIDs de cette cat??gorie n'ayant pas encore de gestionnaire.	
	</p>
	<?php echo add_token_field(); ?>
	<p>
		<input type="hidden" name="toutes_aids_gest" value="y" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid; ?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id; ?>" />
		<br />
		<input type="submit" value="Affecter la liste aux Aids sans gestionnaire" />	
	</p>
</form>
	<?php 
   }
}

if ($flag == "eleve") {
	// On ajoute le nom des profs et le nombre d'??l??ves
	$aff_profs = "<font style=\"color: brown; font-size: 12px;\">(";
	$req_profs = mysqli_query($GLOBALS["mysqli"], "SELECT id_utilisateur FROM j_aid_utilisateurs WHERE id_aid = '".$aid_id."'");
	$nbre_profs = mysqli_num_rows($req_profs);
	for($a=0; $a<$nbre_profs; $a++) {
		$rep_profs[$a]["id_utilisateur"] = old_mysql_result($req_profs, $a, "id_utilisateur");
		$rep_profs_a = mysqli_fetch_array(mysqli_query($GLOBALS["mysqli"], "SELECT nom, civilite FROM utilisateurs WHERE login = '".$rep_profs[$a]["id_utilisateur"]."'"));
		$aff_profs .= "".$rep_profs_a["civilite"].$rep_profs_a["nom"]." ";
	}
		if($nbre_profs==0) {
			$aff_profs.="aucun professeur";
		}
		if ($NiveauGestionAid_categorie >= 5) {
			$aff_profs.=" <a href='modify_aid.php?flag=prof&aid_id=$aid_id&indice_aid=$indice_aid' title=\"Ajouter/supprimer des professeurs.\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/icons/add_user.png' class='icone16' alt='Ajouter/supprimer' /></a>";
		}
		$aff_profs .= ")</font>";
?>
    <p class='grand'><?php 
		echo "$nom_aid  $aid_nom";
		if((isset($aid_id))&&(isset($indice_aid))&&($NiveauGestionAid_categorie>=5)) {
			echo " <a href='add_aid.php?action=modif_aid&aid_id=$aid_id&indice_aid=$indice_aid' title=\"Modifier cet AID.\" onclick=\"return confirm_abandon (this, change, '$themessage')\"><img src='../images/edit16.png' class='icone16' alt='Editer' /></a>";
		}
		echo " $aff_profs";
    ?></p>

    <p class = 'bold'>Liste des ??l??ves de l'AID <?php echo $aid_nom ?>&nbsp;: 
	<a href='javascript:affichage_des_photos_ou_non()' id='temoin_photo'><img src='../images/icons/camera-photo.png' class='icone16' alt='Photo affich??e' title='Photo affich??e.
Cliquer pour masquer les photos.' /></a>
    </p>
    <hr />
    <?php
    $vide = 1;
    // Ajout d'un tableau ??? Pourquoi un tableau ? R??gis
?>
<form enctype="multipart/form-data" action="modify_aid.php" method="post">
	<?php echo add_token_field(); ?>
	<div id='fixe'></div>
	<div style='float:left; width:30em;'>
	<table class="aid_tableau boireaus boireaus_alt" style='margin-right:1em;'>
    <?php
    // appel de la liste des ??l??ves de l'AID :
    $sql="SELECT DISTINCT e.login, e.nom, e.prenom, e.elenoet
							FROM eleves e, j_aid_eleves j, j_eleves_classes jec, classes c
							WHERE (j.id_aid = '$aid_id' and
							e.login = j.login and
							jec.id_classe = c.id and
							jec.login = j.login AND
							j.indice_aid = '$indice_aid')
							ORDER BY c.classe, e.nom, e.prenom";
	//echo "$sql<br />";
    $call_liste_data = mysqli_query($GLOBALS["mysqli"], $sql);
    $nombre = mysqli_num_rows($call_liste_data);
    // On affiche d'abord le nombre d'??l??ves
    		$s = "";
		if ($nombre >= 2) {
			$s = "s";
		}
		else {
			$s = "";
		} 
?>
		<tr>
			<td>
				<?php echo $nombre; ?> ??l??ve<?php echo $s; ?>
			</td>
			<td>
			<!--td colspan='2'-->
				Suppr.<br />
				<a href='javascript:suppr_cocher_decocher(true)' title='Tout cocher'><img src='../images/enabled.png' class='icone16' alt='Tous'></a>/
				<a href='javascript:suppr_cocher_decocher(false)' title='Tout d??cocher'><img src='../images/disabled.png' class='icone16' alt='Aucun'></a>
			</td>
<?php 
    if ($activer_outils_comp == "y") {
?>
			<td>El??ve responsable (*)</td>
<?php 
    }
?>
		</tr>
<?php 
	$active_module_trombinoscopes=getSettingAOui("active_module_trombinoscopes");
	$i = "0";
	while($lig_ele=mysqli_fetch_object($call_liste_data)) {
		$vide = 0;

		$login_eleve=$lig_ele->login;
		$nom_eleve=$lig_ele->nom;
		$prenom_eleve=$lig_ele->prenom;
		$v_elenoet=$lig_ele->elenoet;

		$eleve_resp = sql_query1("select login from j_aid_eleves_resp where id_aid='$aid_id' and login ='$login_eleve' and indice_aid='$indice_aid'");

		$classe_eleve=get_last_class_ele($login_eleve, "classe");
		if($active_module_trombinoscopes) {
			echo "
		<tr onmouseover=\"affiche_photo_courante('".nom_photo($v_elenoet)."')\" onmouseout=\"vide_photo_courante();\">";
		}
		else {
			echo "
		<tr>";
		}
?>
			<td>
				<strong><?php echo $nom_eleve; ?> <?php echo $prenom_eleve; ?></strong>, <?php echo $classe_eleve ; ?>
			</td>
			<!--td>
				<a href='../lib/confirm_query.php?liste_cible=<?php echo $login_eleve; ?>&amp;liste_cible2=<?php echo $aid_id; ?>&amp;liste_cible3=<?php echo $indice_aid; ?>&amp;action=del_eleve_aid<?php echo add_token_in_url(); ?>'>
					<img src="../images/icons/delete.png" title="Supprimer cet ??l??ve" alt="Supprimer" />
				</a>
			</td-->
			<td>
<?php 
		echo "
				<input type='checkbox' name='suppr_ele[]' id='suppr_$i' value=\"$login_eleve\" title=\"Supprimer cet ??l??ve de l'AID\" />";

		// Dans le cas o?? la cat??gorie d'AID est utilis??e pour la gestion des acc??s au trombinoscope, on ajouter un lien sur la photo de l'??l??ve.
		if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) && (getSettingValue("active_module_trombinoscopes")=='y')) {
			$info="<div align='center'>\n";
			if($v_elenoet!="") {
				$photo=nom_photo($v_elenoet);
				//if($photo!=""){
				if($photo){
					$info.="<img src='".$photo."' width='150' alt=\"photo\" />";
				}
			}
			$info.="</div>\n";
			$titre = $nom_eleve." ".$prenom_eleve;
			$titre ="";
			$tabdiv_infobulle[]=creer_div_infobulle('info_popup_eleve'.$v_elenoet,$titre,"",$info,"",14,0,'y','y','n','n');

			if($photo!="") {
?>
				<a href='#' 
				   onmouseover="afficher_div('info_popup_eleve<?php echo $v_elenoet; ?>','y',30,-200);"
				   onmouseout="cacher_div('info_popup_eleve<?php echo $v_elenoet; ?>');">
					<img src='../images/icons/buddy.png' alt='Photo ??l??ve' />
				</a>
<?php } else { ?>
				<img src='../images/icons/buddy_no.png' alt='Pas de photo' />
<?php 
			}
		}
?>
			</td>
<?php 
        if ($activer_outils_comp == "y") {
?>
			<td class="center">
				<!--
				<input type="checkbox" name="<?php echo $login_eleve; ?>_resp" value="y" onchange="changement()"
				-->
				<input type="checkbox" name="eleve_resp[]" value="<?php echo $login_eleve; ?>" onchange="changement()"
					   <?php if ($eleve_resp!=-1) {echo " checked = 'checked' ";} ?>
					   />
			</td>
<?php } ?>
		</tr>
<?php 
    $i++;
    }
?>
	</table>
	<script type='text/javascript'>
		function affiche_photo_courante(photo) {
			document.getElementById('fixe').innerHTML="<img src='"+photo+"' width='150' alt='Photo' />";
		}

		function vide_photo_courante() {
			document.getElementById('fixe').innerHTML='';
		}

		function affichage_des_photos_ou_non() {
			if(document.getElementById('fixe').style.display=='') {
				document.getElementById('fixe').style.display='none';
				document.getElementById('temoin_photo').innerHTML="<img src='../images/icons/camera-photo-barre.png' class='icone16' alt='Photo masqu??e' title='Photo masqu??e.\\nCliquer pour afficher les photos.' />";
			}
			else {
				document.getElementById('fixe').style.display='';
				document.getElementById('temoin_photo').innerHTML="<img src='../images/icons/camera-photo.png' class='icone16' alt='Photo affich??e' title='Photo affich??e.\\nCliquer pour masquer les photos.' />";
			}
		}

		function suppr_cocher_decocher(mode) {
			for(i=0;i<<?php echo $nombre;?>;i++) {
				if(document.getElementById('suppr_'+i)) {
					document.getElementById('suppr_'+i).checked=mode;
				}
			}
		}
	</script>
	</div>
<?php

    if ($vide == 1) {
?>
	<p style="color: red;">Il n'y a pas actuellement d'??l??ves dans cette AID !</p>
<?php 
    }
	
    if ($autoriser_inscript_multiples == 'y') {
		$parent = a_parent ($aid_id, $indice_aid) ? Extrait_parent ($aid_id)->fetch_object()->parent : NULL;
		$call_eleve = Extrait_eleves_sur_aid_id ($aid_id, $parent);
    } else {
		$call_eleve = Extrait_eleves_sur_indice_aid ($indice_aid);
	}
	
    // $nombreligne = mysqli_num_rows($call_eleve);
    $nombreligne = $call_eleve->num_rows;
    if ($nombreligne != 0) {

        if (getSettingValue("num_aid_trombinoscopes")==$indice_aid) {
?>
	<p>Ci-dessous, lister uniquement les ??l??ves sans photographie 
		<input type="checkbox" name="eleves_sans_photos" value="y" onchange="changement()"
<?php 
            if(isset($_SESSION['eleves_sans_photos'])) {echo " checked = 'checked' ";}
?>
			   />
	</p>
<?php } ?>
	<p>
		<span class = 'bold'>Ajouter un ??l??ve ?? la liste de l'AID :</span>
		<a href="modify_aid_new.php?id_aid=<?php echo $aid_id; ?>&amp;indice_aid=<?php echo $indice_aid; ?>" onclick="return confirm_abandon (this, change, '<?php echo $themessage;?>')">
			Lister les ??l??ves par classe
		</a>
	</p>
	<select size="1" name="reg_add_eleve_login" onchange="changement()">
		<option value=''>(aucun)</option>
<?php 
        $i = "0" ;
        while ($i < $nombreligne) {
            $eleve = old_mysql_result($call_eleve, $i, 'login');
            $nom_el = old_mysql_result($call_eleve, $i, 'nom');
            $prenom_el = old_mysql_result($call_eleve, $i, 'prenom');
            $v_elenoet=old_mysql_result($call_eleve, $i, 'elenoet');
            $photo=nom_photo($v_elenoet);
            if (isset($_SESSION['eleves_sans_photos']) and ($photo!="")) {
                $affiche_ligne = "no";
            } else {
                $affiche_ligne = "yes";
			}
            if ($affiche_ligne == "yes") {
            $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT c.classe FROM classes c, j_eleves_classes j WHERE (j.login = '$eleve' and j.id_classe = c.id) order by j.periode DESC");
            $classe_eleve = @old_mysql_result($call_classe, '0', "classe");
?>
		<option value="<?php echo $eleve; ?>">
			<?php echo my_strtoupper($nom_el); ?>
			<?php echo casse_mot($prenom_el,'majf2'); ?>
			<?php echo $classe_eleve; ?>
		</option>
<?php            }
        $i++;
        }
        ?>
	</select>


<?php } else { ?>
	<p>Tous les ??l??ves de la base ont une AID. Impossible d'ajouter un ??l??ve ?? cette AID !</p>
<?php
    }
    ?>
	<p>
		<input type="hidden" name="add_eleve" value="yes" />
		<input type="hidden" name="indice_aid" value="<?php echo $indice_aid;?>" />
		<input type="hidden" name="aid_id" value="<?php echo $aid_id;?>" />
		<input type="submit" value='Enregistrer' />
	</p>
</form>
    <?php 

    if(($nombreligne>0)&&($autoriser_inscript_multiples != 'y')) {
        echo "<p><em>NOTE&nbsp;:</em> Les ??l??ves d??j?? inscrits dans un AID n'apparaissent pas dans le champ de s??lection ci-dessus.</p>";
    }

    if (getSettingValue("num_aid_trombinoscopes")==$indice_aid) {
		echo "<p><br /><br /><br /><br /><br /></p>";
	} // Pour que les trombines des derniers ??l??ves s'affichent correctement.
    if ($activer_outils_comp == "y") {?>
<p>
	(*) Les ??l??ves responsables peuvent par exemple acc??der dans certaines conditions ?? l'??dition des fiches AID.
</p>
    <?php }


}
require ("../lib/footer.inc.php");
?>
