<?php
/*
* $Id$
*
* Copyright 2001-2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

include "../lib/periodes.inc.php";
if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

if(!getSettingAOui('active_bulletins')) {
	header("Location: ../accueil.php?msg=Module_inactif");
	die();
}

$editer_modele_mail=isset($_POST['editer_modele_mail']) ? $_POST['editer_modele_mail'] : (isset($_GET['editer_modele_mail']) ? $_GET['editer_modele_mail'] : NULL);
$valider_modele_mail=isset($_POST['editer_modele_mail']) ? $_POST['editer_modele_mail'] : NULL;
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
if((isset($id_classe))&&(!preg_match("/^[0-9]{1,}$/", $id_classe))) {unset($id_classe);}
$per=isset($_POST['per']) ? $_POST['per'] : (isset($_GET['per']) ? $_GET['per'] : NULL);
$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : NULL);
$mode2=isset($_POST['mode2']) ? $_POST['mode2'] : (isset($_GET['mode2']) ? $_GET['mode2'] : NULL);

// 20191211
$tab_id_classe_exclues_module_bulletins=get_classes_exclues_tel_module('bulletins');
if((isset($id_classe))&&(in_array($id_classe, $tab_id_classe_exclues_module_bulletins))) {
	header('Location: ../accueil.php?msg='.rawurlencode("Les bulletins ne sont pas g??r??s dans Gepi pour cette classe."));
	die();
}

if(isset($valider_modele_mail)) {
	$MsgMailVerifRemplissageBulletins=preg_replace('/(\\\n)+/',"\n",$_POST['MsgMailVerifRemplissageBulletins']);
	$MsgMailVerifRemplissageBulletins=preg_replace('/(\\\')+/',"'",$MsgMailVerifRemplissageBulletins);
	if(!saveSetting("MsgMailVerifRemplissageBulletins", $MsgMailVerifRemplissageBulletins)) {
		$msg="Erreur lors de l'enregistrement du mod??le de message MsgMailVerifRemplissageBulletins<br />";
	}
	else {
		$msg="Mod??le de message MsgMailVerifRemplissageBulletins enregistr??.<br />";
	}
}

$MsgMailVerifRemplissageBulletins=getSettingValue('MsgMailVerifRemplissageBulletins');
if($MsgMailVerifRemplissageBulletins=="") {
	$MsgMailVerifRemplissageBulletins="Bonjour(soir) ___NOM_PROF___,
 
Des moyennes et/ou appr??ciations ne sont pas remplies:
___LIGNE_APPRECIATIONS_MANQUANTES___
___LIGNE_MOYENNES_MANQUANTES___
 
Lorsqu'un ??l??ve n'a pas de note, veuillez saisir un tiret '-' pour signaler qu'il n'y a pas d'oubli de saisie de votre part.
En revanche, s'il s'agit d'une erreur d'affectation, vous disposez, en mode Visualisation d'un carnet de notes, d'un lien 'Signaler des erreurs d affectation' pour alerter l'administrateur Gepi sur un probl??me d'affectation d'??l??ves.
 
Je vous serais reconnaissant(e) de bien vouloir les remplir rapidement.
 
D'avance merci.
-- 
___NOM_EMETTEUR___";
}

$MsgMailVerifRemplissageBulletinsAbs=getSettingValue('MsgMailVerifRemplissageBulletinsAbs');
if($MsgMailVerifRemplissageBulletinsAbs=="") {
	$MsgMailVerifRemplissageBulletinsAbs="Bonjour(soir) ___NOM_PROF___,
 
Des saisies absences (totaux ou appreciation) ne sont pas remplies:
___LIGNE_ABS_MANQUANTES___
 
Je vous serais reconnaissant(e) de bien vouloir les remplir rapidement.
 
D'avance merci.
-- 
___NOM_EMETTEUR___";
}

//**************** EN-TETE *****************
$titre_page = "V??rification du remplissage des bulletins";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// On teste si un professeur peut effectuer cette operation
if (($_SESSION['statut'] == 'professeur') and getSettingValue("GepiProfImprBul")!='yes') {
	die("Droits insuffisants pour effectuer cette op??ration");
}

if (($_SESSION['statut'] == 'cpe') and getSettingValue("GepiCpeImprBul")!='yes') {
	die("Droits insuffisants pour effectuer cette op??ration");
}

if(isset($id_classe)) {
	$gepi_prof_suivi=retourne_denomination_pp($id_classe);
}
else {
	$gepi_prof_suivi=getSettingValue('gepi_prof_suivi');
}

//debug_var();

if(isset($editer_modele_mail)) {
	$param_lien="";
	if(isset($id_classe)) {
		$param_lien.="id_classe=$id_classe&";
	}
	if(isset($per)) {
		$param_lien.="per=$per&";
	}
	if(isset($mode)) {
		$param_lien.="mode=$mode&";
	}
	if($param_lien!="") {
		$param_lien="?".preg_replace("/&$/", "", $param_lien);
	}

	echo "<p class=bold><a href='".$_SERVER['PHP_SELF'].$param_lien."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>\n";

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='formulaire'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<input type='hidden' name='editer_modele_mail' value='y' />
		<input type='hidden' name='valider_modele_mail' value='y' />

		<h2>Mod??le de mail</h2>

		<p>Lors de la v??rification des moyennes, appr??ciations,... non remplies, vous pouvez envoyer un message pour alerter les coll??gues qu'il leur reste peu de temps pour effectuer les saisies manquantes.<br />
		Ce message peut ??tre personnalis?? ici.</p>

		<p class='strong'>Mod??le de mail&nbsp;: </p>
		<textarea name='MsgMailVerifRemplissageBulletins' id='MsgMailVerifRemplissageBulletins' cols='80' rows='15'>".stripslashes(getSettingValue('MsgMailVerifRemplissageBulletins'))."</textarea>

		<p class='strong'>Mod??le de mail pour les absences&nbsp;: </p>
		<textarea name='MsgMailVerifRemplissageBulletinsAbs' id='MsgMailVerifRemplissageBulletinsAbs' cols='80' rows='15'>".stripslashes(getSettingValue('MsgMailVerifRemplissageBulletinsAbs'))."</textarea>

		<p><input type='submit' value='Enregistrer' /></p>

		<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>NOTE&nbsp;:</em>
		<ul>
			<li>
				Vous pouvez utiliser certains 'jokers' dans le texte du message&nbsp;:<p>
				<ul>
					<li>___NOM_PROF___&nbsp;: La chaine civilit?? nom pr??nom.</li>
					<!--li>___ENSEIGNEMENT___&nbsp;: La chaine pr??cisant l'enseignement/groupe concern??.</li-->
					<!--li>___LISTE_ELEVES___&nbsp;: La chaine pr??cisant la liste des ??l??ves concern??s par les saisies manquantes.</li-->
					<li>___LIGNE_APPRECIATIONS_MANQUANTES___&nbsp;: Une ligne correspondant ?? la chaine<br />
					<span style='color:green;'>Appr??ciation(s) manquante(s) en ___ENSEIGNEMENT___ pour ___LISTE_ELEVES___.</span>
					<li>___LIGNE_MOYENNES_MANQUANTES___&nbsp;: Une ligne correspondant ?? la chaine<br />
					<span style='color:green;'>Moyenne(s) manquante(s) en ___ENSEIGNEMENT___ pour ___LISTE_ELEVES___.</span>
					<li>___DATE_CONSEIL___&nbsp;: La date du conseil de classe si elle est saisie.</li>
					<li>___NOM_EMETTEUR___&nbsp;: La chaine civilit?? nom pr??nom correspondant ?? celui qui ??met le message.</li>
				</ul>
			</li>
			<li>
				Dans le message, les lignes vides sont supprim??es.<br />
				Pour imposer une ligne vide dans le message, mettez un caract??re ESPACE sur la ligne en question.
			</li>
		</ul>
		<p style='text-indent:-4em; margin-left:4em; margin-top:1em;'><em>Exemples&nbsp;:</em> Voici des suggestions de mod??les de messages&nbsp;:<p>
		<ul>
			<li>Exemple 1&nbsp;: <a href='#' onclick=\"document.getElementById('MsgMailVerifRemplissageBulletins').innerHTML=document.getElementById('modele_exemple_1').innerHTML;\">Utiliser ce mod??le</a><br />
			<pre id='modele_exemple_1' style='color:green;'>Bonjour(soir) ___NOM_PROF___,
 
Des moyennes et/ou appr??ciations ne sont pas remplies:
___LIGNE_APPRECIATIONS_MANQUANTES___
___LIGNE_MOYENNES_MANQUANTES___
 
Lorsqu'un ??l??ve n'a pas de note, veuillez saisir un tiret '-' pour signaler qu'il n'y a pas d'oubli de saisie de votre part.
En revanche, s'il s'agit d'une erreur d'affectation, vous disposez, en mode Visualisation d'un carnet de notes, d'un lien 'Signaler des erreurs d affectation' pour alerter l'administrateur Gepi sur un probl??me d'affectation d'??l??ves.
 
Je vous serais reconnaissant(e) de bien vouloir les remplir rapidement.
 
D'avance merci.
-- 
___NOM_EMETTEUR___</pre></li>
			<li>Exemple 2&nbsp;: <a href='#' onclick=\"document.getElementById('MsgMailVerifRemplissageBulletins').innerHTML=document.getElementById('modele_exemple_2').innerHTML;\">Utiliser ce mod??le</a><br />
			<pre id='modele_exemple_2' style='color:green;'>Bonjour(soir) ___NOM_PROF___,
 
Les bulletins doivent ??tre remplis 8 jours avant la date du conseil (soit 8 jours avant le ___DATE_CONSEIL___) pour laisser une semaine au ".$gepi_prof_suivi." pour faire sa pr??paration des avis de conseil de classe et pour l'??tude des cas sensibles avec la direction.
 
Des moyennes et/ou appr??ciations ne sont pas remplies:
___LIGNE_APPRECIATIONS_MANQUANTES___
___LIGNE_MOYENNES_MANQUANTES___
 
Lorsqu'un ??l??ve n'a pas de note, veuillez saisir un tiret '-' pour signaler qu'il n'y a pas d'oubli de saisie de votre part.
En revanche, s'il s'agit d'une erreur d'affectation, vous disposez, en mode Visualisation d'un carnet de notes, d'un lien 'Signaler des erreurs d affectation' pour alerter l'administrateur Gepi sur un probl??me d'affectation d'??l??ves.
 
Je vous serais reconnaissant(e) de bien vouloir les remplir rapidement.
 
D'avance merci.
-- 
___NOM_EMETTEUR___</pre></li>
		</ul>
		";

	if(isset($id_classe)) {
		echo "
		<input type='hidden' name='id_classe' value='$id_classe' />";
	}
	if(isset($per)) {
		echo "
		<input type='hidden' name='per' value='$per' />";
	}
	if(isset($mode)) {
		echo "
		<input type='hidden' name='mode' value='$mode' />";
	}

	echo "
	</fieldset>
</form>";

	require("../lib/footer.inc.php");
	die();
}

$tab_date_conseil=array();
$sql="SELECT id_classe, date_evenement, classe FROM d_dates_evenements dde, d_dates_evenements_classes ddec, classes c WHERE type='conseil_de_classe' AND date_evenement>='".strftime("%Y-%m-%d %H:%M:%S")."' AND dde.id_ev=ddec.id_ev AND c.id=ddec.id_classe ORDER BY date_evenement;";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
while($lig_date=mysqli_fetch_object($res)) {
	// Pour ne retenir que la premi??re date de conseil dans le cas o?? les dates de conseil des 3 p??riodes seraient saisies d??s le d??but de l'ann??e:
	if(!isset($tab_date_conseil[$lig_date->id_classe])) {
		$tab_date_conseil[$lig_date->id_classe]['classe']=$lig_date->classe;
		$tab_date_conseil[$lig_date->id_classe]['date']=$lig_date->date_evenement;
	}
}

// Selection de la classe
if (!(isset($id_classe))) {
	echo "<p class='bold'><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>\n";

	if($_SESSION['statut']=='scolarite') {
		echo " | <a href='bull_index.php'>Visualisation et impression des bulletins</a>";
	}
	
	if(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes')) {
		echo " | <a href='param_bull.php'>Param??trage des bulletins</a>";
	}

	if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
		echo " | <a href='verrouillage.php' title=\"Verrouiller/d??verrouiller les p??riodes de notes en saisie pour telle ou telle classe.\">Verrouillage des saisies</a>";
	}

	echo "</p>\n";

	$tab_classes_exclues_des_bulletins=get_classes_exclues_tel_module('bulletins');

	echo "<b>Choisissez la classe&nbsp;:</b></p>\n<br />\n";
	//<table><tr><td>\n";
	if ($_SESSION["statut"] == "scolarite") {
		//$appel_donnees = mysql_query("SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe");
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' ORDER BY classe";
	}
	else {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_professeurs s, j_eleves_classes cc WHERE (s.professeur='" . $_SESSION['login'] . "' AND s.login = cc.login AND cc.id_classe = c.id)";
	}
	$appel_donnees = mysqli_query($GLOBALS["mysqli"], $sql);
	$lignes = mysqli_num_rows($appel_donnees);


	if($lignes==0) {
		echo "<p>Aucune classe ne vous est attribu??e.<br />Contactez l'administrateur pour qu'il effectue le param??trage appropri?? dans la Gestion des classes.</p>\n";
	}
	else {
		$tab_pp=get_tab_prof_suivi();


		unset($lien_classe);
		unset($txt_classe);
		// Tableau des identifiants de classes ?? retenir:
		$tab_id_classe=array();

		$tab_etat_periodes=array();

		while($lig_classe=mysqli_fetch_object($appel_donnees)) {
			if(!in_array($lig_classe->id, $tab_classes_exclues_des_bulletins)) {
				$tab_id_classe[]=$lig_classe->id;

				$lien_classe[]="verif_bulletins.php?id_classe=".$lig_classe->id;

				$chaine_classe="<strong>".ucfirst($lig_classe->classe)."</strong>";
				if(isset($tab_date_conseil[$lig_classe->id])) {
					$chaine_classe.=" <span style='font-variant:italic; font-size:small;' title=\"Date du prochain conseil de classe\">(".formate_date($tab_date_conseil[$lig_classe->id]['date'],"n","court").")</span>";
				}
				$tab_etat_periodes[$lig_classe->id]=html_etat_verrouillage_periode_classe($lig_classe->id);
				$chaine_classe.=" <span style='font-size:small;'>(".$tab_etat_periodes[$lig_classe->id].")</span>";
				$txt_classe[]=$chaine_classe;
			}
		}

		if(count($tab_id_classe)==0) {
			echo "<p>Aucune classe ne vous est attribu??e, ou aucune des classes n'utilise les bulletins dans Gepi.<br />Contactez l'administrateur pour qu'il effectue le param??trage appropri?? dans la Gestion des classes.</p>\n";
		}
		else {

			echo "<div style='margin-left:3em;'>\n";
			tab_liste($txt_classe,$lien_classe,3);
			echo "</div>\n";

			if(count($tab_date_conseil)>0) {
				$active_mod_alerte=getSettingAOui('active_mod_alerte');
				$active_mod_edt=getSettingAOui('autorise_edt_tous');

				echo "<br />
	<p class='bold'>Classes tri??es par dates de conseil de classe&nbsp;:</p>
	<div style='margin-left:3em;'>";
				foreach($tab_date_conseil as $id_classe => $value) {
					if(in_array($id_classe, $tab_id_classe)) {
						echo "
		<a href='".$_SERVER['PHP_SELF']."?id_classe=".$id_classe."'><strong>".$tab_date_conseil[$id_classe]['classe']."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style='font-variant:italic;font-size:small;' title=\"Date du prochain conseil de classe\">(".formate_date($tab_date_conseil[$id_classe]['date'], "y", "complet").")</span></a>";
						if(isset($tab_etat_periodes[$id_classe])) {
							echo " <span style='font-size:small;'>(".$tab_etat_periodes[$id_classe].")</span>";
						}

						if(isset($tab_pp[$id_classe])) {
							for($loop=0;$loop<count($tab_pp[$id_classe]);$loop++) {
								if($loop>0) {echo ",";}
								echo " ".affiche_utilisateur($tab_pp[$id_classe][$loop], $id_classe);

								if($active_mod_alerte) {
									echo " <a href='../mod_alerte/form_message.php?login_dest=".$tab_pp[$id_classe][$loop]."' title='D??poser une alerte/message dans le module Alertes.' target='_blank'><img src='../images/icons/module_alerte32.png' class='icone16' alt='Mail' /></a>";
								}

								if($active_mod_edt) {
									echo " <a href='../edt/index2.php?affichage=semaine&type_affichage=prof&login_prof=".$tab_pp[$id_classe][$loop]."' title=\"Voir l'EDT du professeur dans un nouvel onglet.\" target='_blank'><img src='../images/icons/edt2.png' class='icone16' alt='EDT2' /></a>";
								}

							}
						}
						echo "<br />";
					}
				}
				echo "<div>\n";
			}
		}
	}


	// 20171205
	if (in_array($_SESSION['statut'], array("scolarite", "administrateur"))) {
		$chaine_tableaux_page_accueil="";
		//if(getPref($_SESSION['login'], "accueil_tableau_ouverture_periode", "y")!="n") {
			$sql="SELECT * FROM classes c, periodes p WHERE c.id=p.id_classe LIMIT 1;";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)>0) {
				if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
					$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'ouverture/verrouillage des p??riodes.";
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.=chaine_title_explication_verrouillage_periodes();
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.="Vous pouvez supprimer l'affichage de ce tableau dans 'G??rer mon compte'.\"><a href='$gepiPath/bulletin/verrouillage.php' title=\"Modifier l'ouverture/verrouillage des p??riodes.\" style='color:black;'><strong>Tableau de l'ouverture/verrouillage des p??riodes</strong>&nbsp;<img src='$gepiPath/images/edit16.png' class='icone16' alt='Modifier' /></a>";
				}
				else {
					$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'ouverture/verrouillage des p??riodes.";
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.=chaine_title_explication_verrouillage_periodes();
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.="Vous pouvez supprimer l'affichage de ce tableau dans 'G??rer mon compte'.\"><strong>Tableau de l'ouverture/verrouillage des p??riodes</strong>";
				}
				$chaine_tableaux_page_accueil.=affiche_tableau_periodes_ouvertes();
				$chaine_tableaux_page_accueil.="</div>";
			}
		//}
		echo $chaine_tableaux_page_accueil;
	}

} else if (!(isset($per))) {
	echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	echo "<p class='bold'><a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> | \n";

	$tab_classes_exclues_des_bulletins=get_classes_exclues_tel_module('bulletins');

	// ===========================================
	// Ajout lien classe pr??c??dente / classe suivante
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id NOT IN (SELECT DISTINCT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe') ORDER BY classe;";

	$chaine_options_classes="";
	$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0) {
		$id_class_prec=0;
		$id_class_suiv=0;
		$temoin_tmp=0;
		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)) {
			//if(!in_array($lig_class_tmp->id, $tab_classes_exclues_des_bulletins)) {
				$info_conseil_classe="";
				if(isset($tab_date_conseil[$lig_class_tmp->id])) {
					$info_conseil_classe="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".formate_date($tab_date_conseil[$lig_class_tmp->id]['date'],"n","court").")";
				}

				if($lig_class_tmp->id==$id_classe) {
					$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'>$lig_class_tmp->classe".$info_conseil_classe."</option>\n";
					$temoin_tmp=1;
					if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)) {
						$info_conseil_classe="";
						if(isset($tab_date_conseil[$lig_class_tmp->id])) {
							$info_conseil_classe="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".formate_date($tab_date_conseil[$lig_class_tmp->id]['date'],"n","court").")";
						}

						$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe".$info_conseil_classe."</option>\n";
						$id_class_suiv=$lig_class_tmp->id;
					}
					else{
						$id_class_suiv=0;
					}
				}
				else {
					$chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe".$info_conseil_classe."</option>\n";
				}

				if($temoin_tmp==0) {
					$id_class_prec=$lig_class_tmp->id;
				}
			//}
		}
	}
	// =================================
	if(isset($id_class_prec)) {
		if($id_class_prec!=0) {
			echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec";
			echo "'><img src='../images/icons/arrow-left.png' class='icone16' title='Classe pr??c??dente' /></a>\n";
		}
	}
	if($chaine_options_classes!="") {
		echo " <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
	}
	if(isset($id_class_suiv)) {
		if($id_class_suiv!=0) {
			echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv";
			echo "'><img src='../images/icons/arrow-right.png' class='icone16' title='Classe suivante' /></a>\n";
		}
	}

	if($_SESSION['statut']=='scolarite') {
		echo " | <a href='bull_index.php'>Visualisation et impression des bulletins</a>";
	}
	
	if(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes')) {
		echo " | <a href='param_bull.php'>Param??trage des bulletins</a>";
	}

	if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
		echo " | <a href='verrouillage.php' title=\"Verrouiller/d??verrouiller les p??riodes de notes en saisie pour telle ou telle classe.\">Verrouillage des saisies</a>";
	}

	echo "</form>\n";
	//fin ajout lien classe pr??c??dente / classe suivante
	// ===========================================


	// On teste si les ??l??ves ont bien un CPE responsable

	$test1 = mysqli_query($GLOBALS["mysqli"], "SELECT distinct(login) login from j_eleves_classes WHERE id_classe='" . $id_classe . "'");
	$nb_eleves = mysqli_num_rows($test1);
	$j = 0;
	$flag = true;
	while ($j < $nb_eleves) {
		$login_e = old_mysql_result($test1, $j, "login");
		$test = old_mysql_result(mysqli_query($GLOBALS["mysqli"], "SELECT count(*) FROM j_eleves_cpe WHERE e_login='" . $login_e . "'"), 0);
		if ($test == "0") {
			$flag = false;
			break;
		}
		$j++;
	}

	if (!$flag) {
		echo "<p>ATTENTION&nbsp;: certains ??l??ves de cette classe n'ont pas de CPE responsable attribu??. Cela g??n??rera un message d'erreur sur la page d'??dition des bulletins. Il faut corriger ce probl??me avant impression (contactez l'administrateur).</p>\n";
	}

	$sql_classe="SELECT * FROM `classes`WHERE id=$id_classe";
	$call_classe=mysqli_query($GLOBALS["mysqli"], $sql_classe);
	$nom_classe=old_mysql_result($call_classe,0,"classe");

	//echo "<p><b> Classe&nbsp;: $nom_classe - Choisissez la p??riode&nbsp;: </b></p><br />\n";
	echo "<p><b> Classe&nbsp;: $nom_classe - Choisissez la p??riode et les points ?? v??rifier&nbsp;: </b></p><br />\n";
	include "../lib/periodes.inc.php";


	echo "<table class='boireaus'>\n";
	echo "<tr>\n";
	echo "<th rowspan='2'>V??rifier</th>\n";
	$i=1;
	while ($i < $nb_periode) {
		echo "<th>".ucfirst($nom_periode[$i])."</th>\n";
		$i++;
	}
	echo "</tr>\n";

	echo "<tr>\n";
	//echo "<th>V??rifier</th>\n";
	$i=1;
	while ($i < $nb_periode) {
		echo "<th>";
		echo "<span style='font-size:x-small;'>";
		if ($ver_periode[$i] == "P")  {
			$texte_courant="P??riode partiellement close.
Seule la saisie des avis du conseil de classe est possible.";
			echo " <span style='color:darkorange; font-variant:italic;' title=\"$texte_courant\">$texte_courant</span>\n";
		} else if ($ver_periode[$i] == "O")  {
			$texte_courant="P??riode enti??rement close.
Plus aucune saisie/modification n'est possible.";
			echo " <span style='color:red; font-variant:italic;' title=\"$texte_courant\">$texte_courant</span>\n";
		} else {
			$texte_courant="P??riode ouverte.
Les saisies/modifications sont possibles.";
			echo " <span style='color:green; font-variant:italic;' title=\"$texte_courant\">$texte_courant</span>\n";
		}
		echo "</span>\n";
		echo "</th>\n";
		$i++;
	}
	echo "</tr>\n";

	$alt=1;
	$i="1";
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<th>Notes et appr??ciations</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=note_app'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$alt=1;
	$i="1";
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<th>Notes et appr??ciations<br />sans AID de type EPI/AP/Parcours</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=note_app&mode2=sans_aid_epi_ap_parcours'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$alt=1;
	$i="1";
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<th>Notes et appr??ciations<br />sans AID</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=note_app&mode2=sans_aid'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$alt=$alt*(-1);
	$i="1";
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<th>Absences</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=abs'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

	$alt=$alt*(-1);
	$i="1";
	echo "<tr class='lig$alt white_hover'>\n";
	echo "<th>Avis du conseil</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=avis'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";


	//if(getSettingValue('avis_conseil_classe_a_la_mano')=='y') {
		$alt=$alt*(-1);
		$i="1";
		echo "<tr class='lig$alt white_hover'>\n";
		echo "<th>Tout sauf les avis du conseil</th>\n";
		while ($i < $nb_periode) {
				echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=tout_sauf_avis'>";
			echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
			echo "</td>\n";
			$i++;
		}
		echo "</tr>\n";
	//}

	$alt=$alt*(-1);
	$i="1";
	echo "<tr class='lig$alt'>\n";
	echo "<th>Tout</th>\n";
	while ($i < $nb_periode) {
		echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i'>";
		echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
		echo "</td>\n";
		$i++;
	}
	echo "</tr>\n";

    if ($gepiSettings['active_mod_ects'] == 'y') {
		$alt=$alt*(-1);
        $i="1";
        echo "<tr class='lig$alt'>\n";
        echo "<th>Cr??dits ECTS</th>\n";
        while ($i < $nb_periode) {

            echo "<td><a href='verif_bulletins.php?id_classe=$id_classe&amp;per=$i&amp;mode=ects'>";
            echo "<img src='../images/icons/chercher.png' width='32' height='32' alt=\"".ucfirst($nom_periode[$i])." \" title=\"".ucfirst($nom_periode[$i])." \" /></a>";
            echo "</td>\n";
            $i++;
        }
        echo "</tr>\n";
    }

	echo "</table>\n";

} else {

	$tab_verrouillage_periodes=get_verrouillage_classes_periodes();
	/*
	echo "<pre>";
	print_r($tab_verrouillage_periodes);
	echo "</pre>";
	*/

	$mode=isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "");
	if( $mode!='note_app' && $mode!='abs' && $mode!='avis' && $mode != 'ects' && $mode!='tout_sauf_avis') {
		$mode="tout";
	}

	echo "<div class='bold' style='float:left;'><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> </div>\n";

	// ===========================================
	// Ajout lien classe pr??c??dente / classe suivante
	$sql = "SELECT DISTINCT c.id,c.classe FROM classes c, periodes p, j_scol_classes jsc WHERE p.id_classe = c.id  AND jsc.id_classe=c.id AND jsc.login='".$_SESSION['login']."' AND c.id NOT IN (SELECT DISTINCT value FROM modules_restrictions WHERE module='bulletins' AND name='id_classe')  ORDER BY classe";
	//echo "$sql<br />";

	$tab_id_classe=array();
	$chaine_options_classes="";
	$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_class_tmp)>0) {
		$id_class_prec=0;
		$id_class_suiv=0;
		$nom_classe_prec="";
		$nom_classe_suiv="";
		$temoin_tmp=0;
		while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)) {
			$tab_id_classe[]=$lig_class_tmp->id;
			//echo "\$tab_id_classe[]=$lig_class_tmp->id<br />";

			if(isset($tab_verrouillage_periodes[$lig_class_tmp->id][$per])) {
				$style_option_courante=" style='color:".$couleur_verrouillage_periode[$tab_verrouillage_periodes[$lig_class_tmp->id][$per]]."' title=\"P??riode ".$traduction_verrouillage_periode[$tab_verrouillage_periodes[$lig_class_tmp->id][$per]]."\"";
				$complement_option_courante=" (".$traduction_verrouillage_periode[$tab_verrouillage_periodes[$lig_class_tmp->id][$per]].")";
			}
			else {
				$style_option_courante="";
				$complement_option_courante="";
			}

			$info_conseil_classe="";
			if(isset($tab_date_conseil[$lig_class_tmp->id])) {
				$info_conseil_classe="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".formate_date($tab_date_conseil[$lig_class_tmp->id]['date'],"n","court").")";
			}

			if($lig_class_tmp->id==$id_classe) {
				$chaine_options_classes.="<option value='$lig_class_tmp->id' selected='true'$style_option_courante>$lig_class_tmp->classe".$info_conseil_classe.$complement_option_courante."</option>\n";
				$temoin_tmp=1;
				if($lig_class_tmp=mysqli_fetch_object($res_class_tmp)) {
					$tab_id_classe[]=$lig_class_tmp->id;
					//echo "\$tab_id_classe[]=$lig_class_tmp->id<br />";
					$info_conseil_classe="";
					if(isset($tab_date_conseil[$lig_class_tmp->id])) {
						$info_conseil_classe="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".formate_date($tab_date_conseil[$lig_class_tmp->id]['date'],"n","court").")";
					}

					$chaine_options_classes.="<option value='$lig_class_tmp->id'$style_option_courante>$lig_class_tmp->classe".$info_conseil_classe.$complement_option_courante."</option>\n";
					$id_class_suiv=$lig_class_tmp->id;
					$nom_classe_suiv=$lig_class_tmp->classe;
				}
				else{
					$id_class_suiv=0;
				}
			}
			else {
				$chaine_options_classes.="<option value='$lig_class_tmp->id'$style_option_courante>$lig_class_tmp->classe".$info_conseil_classe.$complement_option_courante."</option>\n";
			}

			if($temoin_tmp==0) {
				$id_class_prec=$lig_class_tmp->id;
				$nom_classe_prec=$lig_class_tmp->classe;
			}
		}
	}

	echo "<div style='float:left;' class='bold'>
	<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
	// =================================
	$complement_url="";
	if(isset($mode2)) {
		$complement_url="&mode2=".$mode2;
	}
	if(isset($id_class_prec)) {
		if($id_class_prec!=0) {echo " | <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;per=$per&amp;mode=$mode".$complement_url."' title=\"Classe pr??c??dente : $nom_classe_prec\"><img src='../images/icons/back.png' class='icone16' alt='Pr??c??dente'></a>\n";}
	}
	if($chaine_options_classes!="") {
		echo " <select name='id_classe' onchange=\"document.forms['form1'].submit();\">\n";
		echo $chaine_options_classes;
		echo "</select>\n";
		echo "<input type='hidden' name='per' value='$per' />\n";
		echo "<input type='hidden' name='mode' value='$mode' />\n";
		if(isset($mode2)) {
			echo "<input type='hidden' name='mode2' value='$mode2' />\n";
		}
	}
	if(isset($id_class_suiv)) {
		if($id_class_suiv!=0) {echo " <a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;per=$per&amp;mode=$mode".$complement_url."' title=\"Classe suivante : $nom_classe_suiv\"><img src='../images/icons/forward.png' class='icone16' alt='Suivante'></a>\n";}
	}
	echo "
	</form>
</div>";

	/*
	echo "<pre>";
	print_r($tab_id_classe);
	echo "</pre>";

	echo "<pre>";
	print_r($tab_date_conseil);
	echo "</pre>";
	*/

	$id_class_prec=0;
	$id_class_suiv=0;
	$nom_classe_suiv="";
	$nom_classe_prec="";
	$info_classe_prec="";
	$info_classe_suiv="";
	$classe_courante_trouvee=0;
	$temoin_une_date_de_conseil_de_classe=0;
	$chaine_changement_classe_date_conseil="";
	foreach($tab_date_conseil as $current_id_classe => $value) {
		if(in_array($current_id_classe, $tab_id_classe)) {
			/*
			if($temoin_une_date_de_conseil_de_classe==0) {
				$chaine_changement_classe_date_conseil.="
<div style='float:left;' class='bold'>
	<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>
	 | 
		<input type='hidden' name='per' value='$per' />
		<input type='hidden' name='mode' value='$mode' />
		<select name='id_classe' onchange=\"document.forms['form2'].submit();\" title=\"Classes tri??es par date du prochain conseil.\">
			<option value=''>---</option>";
			}
			*/

			if($classe_courante_trouvee==1) {
				$id_class_suiv=$current_id_classe;
				$info_date_courante=formate_date($tab_date_conseil[$current_id_classe]['date'], "n", "court");
				$nom_classe_suiv=$tab_date_conseil[$current_id_classe]['classe'];
				$info_classe_suiv=$tab_date_conseil[$current_id_classe]['classe']." (".$info_date_courante.")";
				$classe_courante_trouvee++;
			}

			if(isset($tab_verrouillage_periodes[$current_id_classe][$per])) {
				$style_option_courante=" style='color:".$couleur_verrouillage_periode[$tab_verrouillage_periodes[$current_id_classe][$per]]."' title=\"P??riode ".$traduction_verrouillage_periode[$tab_verrouillage_periodes[$current_id_classe][$per]]."\"";
				$complement_option_courante=" (".$traduction_verrouillage_periode[$tab_verrouillage_periodes[$current_id_classe][$per]].")";
			}
			else {
				$style_option_courante="";
				$complement_option_courante="";
			}

			$chaine_changement_classe_date_conseil.="
			<option value='$current_id_classe'".$style_option_courante;
			if($current_id_classe==$id_classe) {
				$chaine_changement_classe_date_conseil.=" selected='selected'";
				$classe_courante_trouvee++;
			}
			$info_date_courante=formate_date($tab_date_conseil[$current_id_classe]['date'], "n", "court");
			$chaine_changement_classe_date_conseil.=">".$tab_date_conseil[$current_id_classe]['classe']."</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$info_date_courante.")".$complement_option_courante."</option>";

			if($classe_courante_trouvee==0) {
				$id_class_prec=$current_id_classe;
				$nom_classe_prec=$tab_date_conseil[$current_id_classe]['classe'];
				$info_date_courante=formate_date($tab_date_conseil[$current_id_classe]['date'], "n", "court");
				$info_classe_prec=$tab_date_conseil[$current_id_classe]['classe']." (".$info_date_courante.")";
			}

			$temoin_une_date_de_conseil_de_classe++;
		}
		/*
		// DEBUG:
		else {
			$chaine_changement_classe_date_conseil.="<option value=''>$current_id_classe</option>";
		}
		*/
	}
	if($temoin_une_date_de_conseil_de_classe>0) {
/*
		$chaine_changement_classe_date_conseil.="
		</select>
	</form>
</div>";
*/
		if($classe_courante_trouvee>0) {
			echo "
<div style='float:left;' class='bold'>
	<form action='".$_SERVER['PHP_SELF']."' name='form2' method='post'>
	 | 
		<input type='hidden' name='per' value='$per' />
		<input type='hidden' name='mode' value='$mode' />";
		if(isset($mode2)) {
			echo "
		<input type='hidden' name='mode2' value='$mode2' />";
		}
			if(isset($id_class_prec)) {
				if($id_class_prec!=0) {
					echo "
		<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_prec&amp;per=$per&amp;mode=$mode".$complement_url."' title=\"Classe pr??c??dente (par date de conseil de classe) : $info_classe_prec\"><img src='../images/icons/back.png' class='icone16' alt='Pr??c??dente'></a>\n";}
			}
			echo "
		<select name='id_classe' onchange=\"document.forms['form2'].submit();\" title=\"Classes tri??es par date du prochain conseil.\">
			<option value=''>---</option>".$chaine_changement_classe_date_conseil."
		</select>";
			if(isset($id_class_suiv)) {
				if($id_class_suiv!=0) {
					echo "
		<a href='".$_SERVER['PHP_SELF']."?id_classe=$id_class_suiv&amp;per=$per&amp;mode=$mode".$complement_url."' title=\"Classe suivante (par date de conseil de classe) : $info_classe_suiv\"><img src='../images/icons/forward.png' class='icone16' alt='Suivante'></a>\n";}
			}
			echo "
	</form>
</div>";
		}

	}

	if($_SESSION['statut']=='scolarite') {
		echo "<div style='float:left;' class='bold'>
	 | <a href='bull_index.php'>Visualisation et impression des bulletins </a>
</div>";
	}
	
	if(($_SESSION['statut']=='scolarite')&&(getSettingValue('GepiScolImprBulSettings')=='yes')) {
		echo "<div style='float:left;' class='bold'>
	 | <a href='param_bull.php'>Param??trage des bulletins </a>
</div>";
	}

	if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
		echo "<div style='float:left;' class='bold'>
	 | <a href='verrouillage.php' title=\"Verrouiller/d??verrouiller les p??riodes de notes en saisie pour telle ou telle classe.\">Verrouillage des saisies </a>
</div>";
	}

	// ===========================================

	echo "<div style='clear:both;'></div>\n";

	$tab_pp=get_tab_prof_suivi($id_classe);

	$bulletin_rempli = 'yes';
	$call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM classes WHERE id = '$id_classe'");
	$classe = old_mysql_result($call_classe, "0", "classe");

	//=========================
	$chaine_date_conseil_classe=affiche_date_prochain_conseil_de_classe_classe($id_classe);
	if($chaine_date_conseil_classe!='') {
		echo "<div style='float:right; width: 8em; margin:0.5em; padding:0.2em;' class='fieldset_opacite50'>".$chaine_date_conseil_classe."</div>";
	}
	//=========================

	echo "<p><strong>Classe&nbsp;: $classe - $nom_periode[$per] - Ann??e scolaire&nbsp;: ".getSettingValue("gepiYear")."</strong><br />
(<em style='color:".$couleur_verrouillage_periode[$ver_periode[$per]].";'><span id='span_etat_verrouillage_classe'>P??riode ".$traduction_verrouillage_periode[$ver_periode[$per]]."</span>";
	if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
		echo " <a href='#'  onclick=\"afficher_div('div_modif_verrouillage','y',-20,20);return false;\" title=\"Verrouillez/d??verrouillez la p??riode pour cette classe.\"><img src='../images/icons/configure.png' class='icone16' alt='Modifier' /></a>";
	}
	echo "</em>) - (<em>".$gepi_prof_suivi."&nbsp;: ".liste_prof_suivi($id_classe, "profs", "y")."</em>)</p>";

	if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
		$titre_infobulle="Verrouillage de p??riode";
		$texte_infobulle="<p class='bold' style='text-align:center;'>Modifiez l'??tat de verrouillage ou non de la p??riode<br />pour la classe de $classe</p>
<p style='text-align:center;'>Passer la p??riode ?? l'??tat&nbsp;:<br />
<a href='verrouillage.php?mode=change_verrouillage&amp;id_classe=$id_classe&amp;num_periode=$per&amp;etat=N".add_token_in_url()."' onclick=\"changer_etat_verrouillage_periode($id_classe, $per, 'N');return false;\" target='_blank' style='color:".$couleur_verrouillage_periode['N']."'>ouverte en saisie</a> - 
<a href='verrouillage.php?mode=change_verrouillage&amp;id_classe=$id_classe&amp;num_periode=$per&amp;etat=P".add_token_in_url()."' onclick=\"changer_etat_verrouillage_periode($id_classe, $per, 'P');return false;\" target='_blank' style='color:".$couleur_verrouillage_periode['P']."'>partiellement close</a> - 
<a href='verrouillage.php?mode=change_verrouillage&amp;id_classe=$id_classe&amp;num_periode=$per&amp;etat=O".add_token_in_url()."' onclick=\"changer_etat_verrouillage_periode($id_classe, $per, 'O');return false;\" target='_blank' style='color:".$couleur_verrouillage_periode['O']."'>close</a><br />
&nbsp;</p>";
		$tabdiv_infobulle[]=creer_div_infobulle("div_modif_verrouillage",$titre_infobulle,"",$texte_infobulle,"",30,0,'y','y','n','n');
	}

//============================================
// 20190318
echo "<div id='div_bull_simp' class='infobulle_corps' style='position: absolute; top: 220px; right: 20px; width: 700px; text-align:center; color: black; padding: 0px; border:1px solid black; display:none;'>\n";

	echo "<div class='infobulle_entete' style='color: #ffffff; cursor: move; width: 700px; font-weight: bold; padding: 0px;' onmousedown=\"dragStart(event, 'div_bull_simp')\">\n";
		echo "<div style='color: #ffffff; cursor: move; font-weight: bold; float:right; width: 16px; margin-right: 1px;'>\n";
		echo "<a href='#' onClick=\"cacher_div('div_bull_simp');return false;\">\n";
		echo "<img src='../images/icons/close16.png' style=\"width:16px; height:16px\" alt='Fermer' />\n";
		echo "</a>\n";
		echo "</div>\n";

		echo "<div id='titre_entete_bull_simp'></div>\n";
	echo "</div>\n";
	
	echo "<div id='corps_bull_simp' class='infobulle_corps' style='color: #ffffff; cursor: move; font-weight: bold; padding: 0px; height: 15em; width: 700px; overflow: auto;'>";
	echo "</div>\n";

echo "</div>\n";
//============================================

	//
	// V??rification de param??tres g??n??raux
	//
	$current_classe_nom_complet = old_mysql_result($call_classe, 0, "nom_complet");
	if ($current_classe_nom_complet == '') {
		$bulletin_rempli = 'no';
		echo "<p>Le nom long de la classe n'est pas d??fini !</p>\n";
	}
	$current_classe_suivi_par = old_mysql_result($call_classe, 0, "suivi_par");
	if ($current_classe_suivi_par == '') {
		$bulletin_rempli = 'no';
		echo "<p>La personne de l'administration charg??e de la classe n'est pas d??finie !</p>\n";
	}
	$current_classe_formule = old_mysql_result($call_classe, 0, "formule");
	if ($current_classe_formule == '') {
		$bulletin_rempli = 'no';
		echo "<p>La formule ?? la fin de chaque bulletin n'est pas d??finie !</p>\n";
	}
	$appel_donnees_eleves = mysqli_query($GLOBALS["mysqli"], "SELECT e.login, e.nom, e.prenom FROM eleves e, j_eleves_classes j WHERE (j.id_classe='$id_classe' AND j.login = e.login and j.periode='$per') ORDER BY e.nom, e.prenom;");
	$nb_eleves = mysqli_num_rows($appel_donnees_eleves);
	$j = 0;
	//
	//D??but de la boucle ??l??ve
	//

	switch($mode) {
		case 'note_app':
			echo "<p class='bold'>V??rification du remplissage des moyennes et appr??ciations&nbsp;:</p>\n";
			break;
		case 'avis':
			echo "<p class='bold'>V??rification du remplissage des avis du conseil de classe&nbsp;:</p>\n";
			break;
		case 'abs':
			echo "<p class='bold'>V??rification du remplissage des absences&nbsp;:</p>\n";
			break;
		case 'ects':
			echo "<p class='bold'>V??rification du remplissage des cr??dits ECTS&nbsp;:</p>\n";
			break;
		case 'tout_sauf_avis':
			echo "<p class='bold'>V??rification du remplissage des moyennes, appr??ciations et absences&nbsp;:</p>\n";
			break;
		case 'tout':
			echo "<p class='bold'>V??rification du remplissage des moyennes, appr??ciations, absences et avis du conseil de classe&nbsp;:</p>\n";
			break;
	}

	// Tableau pour stocker les infos ?? envoyer aux profs ?? propos des notes/app non remplies
	$tab_alerte_prof=array();
	//20191206
	$tab_alerte_absences=array();

	// Affichage sur 3 colonnes
	$nb_eleve_par_colonne=round($nb_eleves/2);

	echo "<table width='100%'>\n";
	echo "<tr valign='top' align='center'>\n";

	$cpt_i = 0;

	echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>\n";
	echo "<td align='left'>\n";

	$tab_grp_classe=array();
	if(($mode=="note_app")||($mode=="tout")||($mode=="tout_sauf_avis")) {
		$sql="SELECT DISTINCT id_groupe FROM j_eleves_groupes jeg, 
								j_eleves_classes jec 
							WHERE id_classe='".$id_classe."';";
		$groupeinfo = mysqli_query($GLOBALS["mysqli"], $sql);
		$lignes_groupes = mysqli_num_rows($groupeinfo);
		if($lignes_groupes>0) {
			while($lig_grp=mysqli_fetch_object($groupeinfo)) {
				$tab_grp_classe[$lig_grp->id_groupe]=get_group($lig_grp->id_groupe, array('matieres', 'classes', 'eleves', 'periodes', 'profs', 'visibilite'));
			}
		}
	}

	$temoin_note_app=0;
	$temoin_avis=0;
	$temoin_aid=0;
	$temoin_abs=0;
	$temoin_ects=0;
	$temoin_has_ects = false; // Ce t??moin sert dans les cas o?? en r??alit?? aucun ??l??ve ne suit d'enseignement ouvrant droit ?? ECTS.
	while($j < $nb_eleves) {

		//affichage 2 colonnes
		if(($cpt_i>0)&&(round($cpt_i/$nb_eleve_par_colonne)==$cpt_i/$nb_eleve_par_colonne)) {
			echo "</td>\n";
			echo "<td align='left'>\n";
		}

		$lig_ele=mysqli_fetch_object($appel_donnees_eleves);
		$id_eleve[$j] = $lig_ele->login;
		$eleve_nom[$j] = $lig_ele->nom;
		$eleve_prenom[$j] = $lig_ele->prenom;


		$affiche_nom = 1;
		if(($mode=="note_app")||($mode=="tout")||($mode=="tout_sauf_avis")) {
			//
			//V??rification des appr??ciations
			//

			//
			//D??but de la boucle mati??re
			//

			// Variable remont??e hors du test sur $mode
			//$affiche_nom = 1;
			$affiche_mess_app = 1;
			$affiche_mess_note = 1;
			foreach($tab_grp_classe as $current_group) {

				if (((!isset($current_group['visibilite']['bulletins']))||($current_group['visibilite']['bulletins']!='n'))&&(in_array($id_eleve[$j], $current_group["eleves"][$per]["list"]))) { // Si l'??l??ve suit cet enseignement pour la p??riode consid??r??e

					$group_id=$current_group['id'];

					//
					//V??rification des appr??ciations :
					//
					//$sql="SELECT * FROM matieres_appreciations WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per' AND appreciation='');";
					$sql="SELECT * FROM matieres_appreciations WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per');";
					//echo "$sql<br />";
					$test_app = mysqli_query($GLOBALS["mysqli"], $sql);
					// Il peut n'y avoir aucun enregistrement pour aucun ??l??ve du groupe (rien dans 'matieres_appreciations'), comme il peut juste y avoir un enregistrement avec appreciation='' pour un ??l??ve.
					$app="";
					if(mysqli_num_rows($test_app)>0) {
						$lig_app=mysqli_fetch_object($test_app);
						$app=$lig_app->appreciation;
					}
					if ($app == '') {
						$bulletin_rempli = 'no';
						if ($affiche_nom != 0) {
							//echo "<br /><br /><br />\n";
							echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
							//echo "<br />\n";
							echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=".$id_eleve[$j]."' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" title=\"Cliquez pour voir le bulletin simplifi?? dans un nouvel onglet.\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
						}
						if ($affiche_mess_app != 0) {
							echo "<br /><br />\n";
							echo "<b>Appr??ciations non remplies</b> pour les mati??res suivantes&nbsp;: \n";
						}
						$affiche_nom = 0;
						$affiche_mess_app = 0;
						//============================================
						// MODIF: boireaus
						// Pour les mati??res comme Histoire & G??o,...
						//echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
						echo "<br />--&gt; " . htmlspecialchars($current_group["description"]) . " (" . $current_group["classlist_string"] . ")  --  (";
						//============================================
						$m=0;
						$virgule = 1;
						foreach ($current_group["profs"]["list"] as $login_prof) {
							$email = retourne_email($login_prof);
							$nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
							$prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
							$civilite_prof = $current_group["profs"]["users"][$login_prof]["civilite"];

							if(!isset($tab_alerte_prof[$login_prof])) {
								$tab_alerte_prof[$login_prof]=array();
								$tab_alerte_prof[$login_prof]['civilite']=$civilite_prof;
								$tab_alerte_prof[$login_prof]['nom']=$nom_prof;
								$tab_alerte_prof[$login_prof]['prenom']=$prenom_prof;
								$tab_alerte_prof[$login_prof]['email']=$email;
							}

							if(!isset($tab_alerte_prof[$login_prof]['groupe'][$group_id])) {
								$tab_alerte_prof["$login_prof"]['groupe'][$group_id]['info']=$current_group["description"]." (".$current_group["classlist_string"].")";

							}

							$eleve_nom_prenom=my_strtoupper($eleve_nom[$j])." ".casse_mot($eleve_prenom[$j],'majf2');
							$tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante'][]=$eleve_nom_prenom;
							if(($email!="")&&(check_mail($email))) {
								$sujet_mail="[Gepi]: Appreciation non remplie: ".$id_eleve[$j];
								$message_mail="Bonjour,\r\n\r\nL'appr??ciation en ".$tab_alerte_prof[$login_prof]['groupe'][$group_id]['info']." pour $eleve_nom_prenom n'est pas remplie.\r\n";
								$message_mail.="Je vous serais reconnaissant(e) de bien vouloir la remplir rapidement.\r\nD'avance merci.\r\n\r\nCordialement\r\n-- \r\n".civ_nom_prenom($_SESSION['login']);

								echo "<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>";
							}
							else{
								echo casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof);
							}
							$m++;
							if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
							if ($virgule == 1) {echo ", ";}
						}
						echo ")\n";

						$temoin_note_app++;
					}
				}
			}

			//
			//V??rification des moyennes
			//

			//
			//D??but de la boucle mati??re
			//
			foreach($tab_grp_classe as $current_group) {
				$group_id = $current_group['id'];

				//if (in_array($id_eleve[$j], $current_group["eleves"][$per]["list"])) { // Si l'??l??ve suit cet enseignement pour la p??riode consid??r??e
				if (((!isset($current_group['visibilite']['bulletins']))||($current_group['visibilite']['bulletins']!='n'))&&(in_array($id_eleve[$j], $current_group["eleves"][$per]["list"]))) { // Si l'??l??ve suit cet enseignement pour la p??riode consid??r??e
					//
					//V??rification des moyennes :
					//
					//$sql="SELECT * FROM matieres_notes WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per' AND statut='-');";
					$sql="SELECT * FROM matieres_notes WHERE (login = '$id_eleve[$j]' and id_groupe = '" . $current_group["id"] . "' and periode = '$per');";
					//echo "$sql<br />";
					$test_notes = mysqli_query($GLOBALS["mysqli"], $sql);
					// Il peut n'y avoir aucun enregistrement pour aucun ??l??ve du groupe (rien dans 'matieres_appreciations'), comme il peut juste y avoir un enregistrement avec appreciation='' pour un ??l??ve.
					$note='';
					if(mysqli_num_rows($test_notes)>0) {
						$lig_note=mysqli_fetch_object($test_notes);
						if($lig_note->statut!='-') {
							$note='ok';
							// Pour debug on d??taille
							if($lig_note->statut=='') {
								$note=$lig_note->note;
							}
							else {
								$note=$lig_note->statut;
							}
						
						}
						else {
							$note='-';
						}
					}
					if ($note == '') {
						$bulletin_rempli = 'no';
						if ($affiche_nom != 0) {
							//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j] ";
							echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
							//echo "<br />\n";
							//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span> :";
							echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
						}
						if ($affiche_mess_note != 0) {echo "<br /><br /><b>Moyennes non remplies</b> pour les mati??res suivantes&nbsp;: ";}
						$affiche_nom = 0;
						$affiche_mess_note = 0;
						//============================================
						// MODIF: boireaus
						// Pour les mati??res comme Histoire & G??o,...
						//echo "<br />--> " . $current_group["description"] . " (" . $current_group["classlist_string"] . ")  --  (";
						echo "<br />--&gt; ";
						//echo $current_group["id"]." ";
						echo htmlspecialchars($current_group["description"])." (" . $current_group["classlist_string"] . ")  --   (";
						//============================================
						$m=0;
						$virgule = 1;
						foreach ($current_group["profs"]["list"] as $login_prof) {
							$email = retourne_email($login_prof);
							$civilite_prof = $current_group["profs"]["users"][$login_prof]["civilite"];
							$nom_prof = $current_group["profs"]["users"][$login_prof]["nom"];
							$prenom_prof = $current_group["profs"]["users"][$login_prof]["prenom"];
							//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";

							if(!isset($tab_alerte_prof[$login_prof])) {
								$tab_alerte_prof[$login_prof]=array();
								$tab_alerte_prof[$login_prof]['civilite']=$civilite_prof;
								$tab_alerte_prof[$login_prof]['nom']=$nom_prof;
								$tab_alerte_prof[$login_prof]['prenom']=$prenom_prof;
								$tab_alerte_prof[$login_prof]['email']=$email;
							}

							if(!isset($tab_alerte_prof[$login_prof]['groupe'][$group_id])) {
								$tab_alerte_prof["$login_prof"]['groupe'][$group_id]['info']=$current_group["description"]." (".$current_group["classlist_string"].")";

							}

							$tab_alerte_prof[$login_prof]['groupe'][$group_id]['moy_manquante'][]=my_strtoupper($eleve_nom[$j])." ".casse_mot($eleve_prenom[$j],'majf2');

							if(($email!="")&&(check_mail($email))) {
								$sujet_mail="[Gepi]: Moyenne manquante: ".$eleve_nom[$j];
								$message_mail="Bonjour,\r\n\r\nCordialement";
								echo "<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>";
							}
							else{
								echo casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof);
							}
							$m++;
							if ($m == count($current_group["profs"]["list"])) {$virgule = 0;}
							if ($virgule == 1) {echo ", ";}
						}
						echo ")\n";

						$temoin_note_app++;
					}
				}
				$i++;
			//Fin de la boucle mati??re
			}
		}


		if(($mode=="avis")||($mode=="tout")) {
			//
			//V??rification des avis des conseils de classe
			//
			$query_conseil = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM avis_conseil_classe WHERE (login = '$id_eleve[$j]' and periode = '$per')");
			//$avis = @old_mysql_result($query_conseil, 0, 'avis');
			$avis="";
			if(mysqli_num_rows($query_conseil)>0) {$avis=old_mysql_result($query_conseil, 0, 'avis');}
			if ($avis == '') {
				$bulletin_rempli = 'no';
				if ($affiche_nom != 0) {
					//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
					echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
					//echo "<br />\n";
					//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span> :";
					echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
				}
				echo "<br /><br />\n";
				echo "<b>Avis du conseil de classe</b> non rempli !";
				$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs j WHERE (j.login = '$id_eleve[$j]' and j.id_classe='$id_classe' and u.login=j.professeur)");
				$nb_result = mysqli_num_rows($call_prof);
				if ($nb_result != 0) {
					$login_prof = old_mysql_result($call_prof, 0, 'login');
					$email = retourne_email($login_prof);
					$nom_prof = old_mysql_result($call_prof, 0, 'nom');
					$prenom_prof = old_mysql_result($call_prof, 0, 'prenom');
					//echo " (<a href='mailto:$email'>$prenom_prof $nom_prof</a>)";
					//if($email!="") {
					if(($email!="")&&(check_mail($email))) {
						$sujet_mail="[Gepi]: Avis du conseil manquant: ".$id_eleve[$j];
						$message_mail="Bonjour,\r\n\r\nCordialement";
						echo "(<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>)";
					}
					else{
						echo "(".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof).")";
					}

				} else {
					echo " (pas de ".$gepi_prof_suivi.")";
				}

				$affiche_nom = 0;

				$temoin_avis++;

			}
		}


		if(($mode=="note_app")||($mode=="tout")||($mode=="tout_sauf_avis")) {
			//
			//V??rification des aid
			//
			if((!isset($mode2))||($mode2!="sans_aid")) {
				$complement_sql="";
				if((isset($mode2))&&($mode2=="sans_aid_epi_ap_parcours")) {
					$complement_sql=" AND type_aid='0'";
				}
				$sql="SELECT * FROM aid_config WHERE display_bulletin!='n'".$complement_sql." ORDER BY nom";
				$call_data = mysqli_query($GLOBALS["mysqli"], $sql);
				$nb_aid = mysqli_num_rows($call_data);
				$z=0;
				while ($z < $nb_aid) {
					$display_begin = @old_mysql_result($call_data, $z, "display_begin");
					$display_end = @old_mysql_result($call_data, $z, "display_end");
					if (($per >= $display_begin) and ($per <= $display_end)) {
						$indice_aid = @old_mysql_result($call_data, $z, "indice_aid");
						$type_note = @old_mysql_result($call_data, $z, "type_note");
						$call_data2 = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_config WHERE indice_aid = '$indice_aid'");
						$nom_aid = @old_mysql_result($call_data2, 0, "nom");
						$aid_query = mysqli_query($GLOBALS["mysqli"], "SELECT id_aid FROM j_aid_eleves WHERE (login='$id_eleve[$j]' and indice_aid='$indice_aid')");
						$aid_id = @old_mysql_result($aid_query, 0, "id_aid");
						if ($aid_id != '') {
							$aid_app_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM aid_appreciations WHERE (login='$id_eleve[$j]' AND periode='$per' and id_aid='$aid_id' and indice_aid='$indice_aid')");
							$query_resp = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_aid_utilisateurs j WHERE (j.id_aid = '$aid_id' and u.login = j.id_utilisateur and j.indice_aid='$indice_aid')");
							$nb_prof = mysqli_num_rows($query_resp);
							//
							// V??rification des appr??ciations
							//
							$aid_app = @old_mysql_result($aid_app_query, 0, "appreciation");
							if ($aid_app == '') {
								$bulletin_rempli = 'no';
								if ($affiche_nom != 0) {
									//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
									echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
									//echo "<br />\n";
									//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page</a>)</span>&nbsp;:";
									echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
								}
								echo "<br /><br />\n";
								echo "<b>Appr??ciation $nom_aid </b> non remplie (";
								$m=0;
								$virgule = 1;
								while ($m < $nb_prof) {
									$login_prof = @old_mysql_result($query_resp, $m, 'login');
									$email = retourne_email($login_prof);
									$nom_prof = @old_mysql_result($query_resp, $m, 'nom');
									$prenom_prof = @old_mysql_result($query_resp, $m, 'prenom');
									//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
									//if($email!="") {
									if(($email!="")&&(check_mail($email))) {
										$sujet_mail="[Gepi]: Appreciation AID manquante: ".$eleve_nom[$j];
										$message_mail="Bonjour,\r\n\r\nCordialement";
										echo "<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>";
									}
									else{
										echo casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof);
									}
									$m++;
									if ($m == $nb_prof) {$virgule = 0;}
									if ($virgule == 1) {echo ", ";}
								}
								echo ")\n";
								$affiche_nom = 0;

								$temoin_aid++;
							}
							//
							// V??rification des moyennes
							//
							$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");
							$periode_max = mysqli_num_rows($periode_query);
							if ($type_note == 'last') {$last_periode_aid = min($periode_max,$display_end);}
							if (($type_note=='every') or (($type_note=='last') and ($per == $last_periode_aid))) {
								$aid_note = @old_mysql_result($aid_app_query, 0, "note");
								$aid_statut = @old_mysql_result($aid_app_query, 0, "statut");


								if (($aid_note == '') or ($aid_statut == 'other')) {
									$bulletin_rempli = 'no';
									if ($affiche_nom != 0) {
										//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
										echo "<p style='border:1px solid black;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
										//echo "<br />\n";
										//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span>&nbsp;:";
										echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
									}
									echo "<br /><br />\n";
									echo "<b>Note $nom_aid </b>non remplie (";
									$m=0;
									$virgule = 1;
									while ($m < $nb_prof) {
										$login_prof = @old_mysql_result($query_resp, $m, 'login');
										$email = retourne_email($login_prof);
										$nom_prof = @old_mysql_result($query_resp, $m, 'nom');
										$prenom_prof = @old_mysql_result($query_resp, $m, 'prenom');
										//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
										//if($email!="") {
										if(($email!="")&&(check_mail($email))) {
											$sujet_mail="[Gepi]: Moyenne AID manquante: ".$eleve_nom[$j];
											$message_mail="Bonjour,\r\n\r\nCordialement";
											echo "<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>";
										}
										else{
											echo casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof);
										}
										$m++;
										if ($m == $nb_prof) {$virgule = 0;}
										if ($virgule == 1) {echo ", ";}
									}
									echo ")\n";
									$affiche_nom = 0;

									$temoin_aid++;
								}
							}
						}
					}
					$z++;
				}
			}
		}

		if(($mode=="abs")||($mode=="tout")||($mode=="tout_sauf_avis")) {
			//
			//V??rification des absences
			//
			$abs_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM absences WHERE (login='$id_eleve[$j]' AND periode='$per')");
			$abs1 = @old_mysql_result($abs_query, 0, "nb_absences");
			$abs2 = @old_mysql_result($abs_query, 0, "non_justifie");
			$abs3 = @old_mysql_result($abs_query, 0, "nb_retards");
			if (($abs1 == '') or ($abs2 == '') or ($abs3 == '')) {
				$bulletin_rempli = 'no';
				if ($affiche_nom != 0) {
					//echo "<p><span class='bold'> $eleve_prenom[$j] $eleve_nom[$j]<br />\n";
					echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]";
					//echo "<br />\n";
					//echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' target='bull'>bulletin simple dans une nouvelle page)</a></span>&nbsp;:";
					echo "(<a href='../prepa_conseil/edit_limite.php?id_classe=$id_classe&amp;periode1=$per&amp;periode2=$per&amp;choix_edit=2&amp;login_eleve=$id_eleve[$j]' onclick=\"afficher_div('div_bull_simp','y',-100,40); affiche_bull_simp('".$id_eleve[$j]."','".addslashes($eleve_nom[$j]." ".$eleve_prenom[$j])."','$id_classe','$per','$per');return false;\" target='bull'><img src='../images/icons/bulletin_simp.png' width='17' height='17' alt='bulletin simple dans une nouvelle page' title='bulletin simple dans une nouvelle page' /></a>)</span>&nbsp;:";
				}
				echo "<br /><br />\n";
				echo "<b>Rubrique \"Absences\" </b> non remplie. (";
				$query_resp = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom, u.civilite FROM utilisateurs u, j_eleves_cpe j WHERE (j.e_login = '$id_eleve[$j]' AND u.login = j.cpe_login)");
				$nb_prof = mysqli_num_rows($query_resp);
				$m=0;
				$virgule = 1;
				while ($lig_cpe=mysqli_fetch_object($query_resp)) {
					$login_prof = $lig_cpe->login;
					$email = retourne_email($login_prof);
					$nom_prof = $lig_cpe->nom;
					$prenom_prof = $lig_cpe->prenom;
					$civilite_prof = $lig_cpe->civilite;
					//echo "<a href='mailto:$email'>$prenom_prof $nom_prof</a>";
					//if($email!="") {
					if(($email!="")&&(check_mail($email))) {
						$sujet_mail="[Gepi]: Absences non remplies: ".$id_eleve[$j];
						$message_mail="Bonjour,\r\n\r\nCordialement";
						echo "<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>";					}
					else{
						echo casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof);
					}

					//============================
					//20191206
					// Pour les rappels et ouvertures exceptionnelles
					if(!isset($tab_alerte_absences[$login_prof])) {
						$tab_alerte_absences[$login_prof]=array();
						$tab_alerte_absences[$login_prof]['civilite']=$civilite_prof;
						$tab_alerte_absences[$login_prof]['nom']=$nom_prof;
						$tab_alerte_absences[$login_prof]['prenom']=$prenom_prof;
						$tab_alerte_absences[$login_prof]['email']=$email;
					}
					$eleve_nom_prenom=my_strtoupper($eleve_nom[$j])." ".casse_mot($eleve_prenom[$j],'majf2');
					$tab_alerte_absences[$login_prof]['absences']['infos_manquantes'][]=$eleve_nom_prenom;
					//============================

					$m++;
					if ($m == $nb_prof) {$virgule = 0;}
					if ($virgule == 1) {echo ", ";}
				}
				echo ")\n";
				$affiche_nom = 0;

				$temoin_abs++;
			}
		}


		if($gepiSettings['active_mod_ects'] == 'y' && (($mode=="ects")||($mode=="tout")||($mode=="tout_sauf_avis"))) {
			//
			//V??rification des ECTS
			//

			// On commence par regarder si l'??l??ve a des groupes qui ouvrent droit ?? des ECTS.
			$query_groupes_ects = mysqli_query($GLOBALS["mysqli"], "SELECT jgc.* FROM j_groupes_classes jgc, j_eleves_groupes jeg WHERE jgc.saisie_ects = '1' AND jgc.id_classe = '$id_classe' AND jgc.id_groupe = jeg.id_groupe AND jeg.login = '".$id_eleve[$j]."' AND jeg.periode = '$per'");
			if (mysqli_num_rows($query_groupes_ects) > 0) {
				$temoin_has_ects = true;
				$query_conseil = mysqli_query($GLOBALS["mysqli"], "SELECT ec.* FROM ects_credits ec, eleves e WHERE ec.id_eleve = e.id_eleve AND e.login = '$id_eleve[$j]' AND num_periode = '$per'");
				$nb = mysqli_num_rows($query_conseil);
				if ($nb == 0) {
					$bulletin_rempli = 'no';
					if ($affiche_nom != 0) {
						echo "<div style='border:1px solid black; margin-bottom:0.3em; padding:0.3em;' class='fieldset_opacite50'><span class='bold'>$eleve_prenom[$j] $eleve_nom[$j]</span>";
					}
					echo "<br /><br />\n";
					echo "<b>Cr??dits ECTS</b> non remplis !";

					// On r??cup??re le prof principal, si celui-ci est autoris?? ?? saisir les ECTS
					if ($gepiSettings['GepiAccesSaisieEctsPP'] == 'yes') {
						$call_prof = mysqli_query($GLOBALS["mysqli"], "SELECT u.login, u.nom, u.prenom FROM utilisateurs u, j_eleves_professeurs j WHERE (j.login = '$id_eleve[$j]' and j.id_classe='$id_classe' and u.login=j.professeur)");
						$nb_result = mysqli_num_rows($call_prof);
						if ($nb_result != 0) {
						$login_prof = old_mysql_result($call_prof, 0, 'login');
						$email = retourne_email($login_prof);
						$nom_prof = old_mysql_result($call_prof, 0, 'nom');
						$prenom_prof = old_mysql_result($call_prof, 0, 'prenom');
						//echo " (<a href='mailto:$email'>$prenom_prof $nom_prof</a>)";
						//if($email!="") {
						if(($email!="")&&(check_mail($email))) {
							$sujet_mail="[Gepi]: ECTS non remplis: ".$eleve_nom[$j];
							$message_mail="Bonjour,\r\n\r\nCordialement";
							echo " (<a href='mailto:$email?subject=$sujet_mail&amp;body=".rawurlencode($message_mail)."'>".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof)."</a>)";
						}
						else{
							echo " (".casse_mot($prenom_prof,'majf2')." ".my_strtoupper($nom_prof).")";
						}

						} else {
							echo " (pas de ".$gepi_prof_suivi.")";
						}
					}
					$affiche_nom = 0;
					$temoin_ects++;
				}
			}
		}

		if($affiche_nom==0) {
			echo "</div>";
		}

		$j++;
		//Fin de la boucle ??l??ve

		$cpt_i++;

		flush();
		ob_flush();
	}

	echo "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";

	$acces_autorisation_exceptionnelle_modif_cn=acces("/cahier_notes/autorisation_exceptionnelle_saisie.php", $_SESSION['statut']);
	$acces_autorisation_exceptionnelle_modif_bull_note=false;
	$acces_autorisation_exceptionnelle_modif_bull_app=false;
	if($_SESSION['statut']=='scolarite') {
		$acces_autorisation_exceptionnelle_modif_cn=getSettingAOui('PeutDonnerAccesCNPeriodeCloseScol');
		$acces_autorisation_exceptionnelle_modif_bull_note=getSettingAOui('PeutDonnerAccesBullNotePeriodeCloseScol');
		$acces_autorisation_exceptionnelle_modif_bull_app=getSettingAOui('PeutDonnerAccesBullAppPeriodeCloseScol');
	}
	$lignes_autoriser_saisie_cn=array();
	$lignes_autoriser_saisie_note_bull=array();
	$lignes_autoriser_saisie_app_bull=array();

	$acces_envoi_mail=acces('/bulletin/envoi_mail.php', $_SESSION['statut']);
	if($acces_envoi_mail) {
		$rowspan=" rowspan='2'";
	}
	else {
		$rowspan="";
	}

	$tab_num_mail=array();
	if((count($tab_alerte_prof)>0)||(count($tab_alerte_absences)>0)) {
		$num=0;

		//echo "<div style='border: 1px solid black'>";
		$param_lien="";
		if(isset($id_classe)) {
			$param_lien.="id_classe=$id_classe&amp;";
		}
		if(isset($per)) {
			$param_lien.="per=$per&amp;";
		}
		if(isset($mode)) {
			$param_lien.="mode=$mode&amp;";
		}

		echo "<p class='bold'>R??capitulatif&nbsp;: <a href='".$_SERVER['PHP_SELF']."?".$param_lien."editer_modele_mail=y' title=\"Editer le mod??le de mail.\"><img src='../images/edit16.png' class='icone16' alt='Editer le mod??le de mail' /></a></p>\n";
		echo "<table class='boireaus boireaus_alt boireaus_alt2' summary=\"Courriels\">\n";
		//$alt=1;

		//$tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante'][]
		foreach($tab_alerte_prof as $login_prof => $tab_prof) {

			$info_prof=$tab_alerte_prof[$login_prof]['civilite']." ".casse_mot($tab_alerte_prof[$login_prof]['nom'],'maj')." ".casse_mot($tab_alerte_prof[$login_prof]['prenom'],'majf2');

			$chaine_app_manquante="";
			$chaine_moy_manquante="";
			//$message="Bonjour(soir) ".$info_prof.",\n\nDes moyennes et/ou appr??ciations ne sont pas remplies:\n";
			foreach($tab_prof['groupe'] as $group_id => $tab_group) {
				if(isset($tab_group['app_manquante'])) {
					//$message.="Appr??ciation(s) manquante(s) en ".$tab_alerte_prof[$login_prof]['groupe'][$group_id]['info']." pour ";
					$chaine_app_manquante.="Appr??ciation(s) manquante(s) en ".$tab_alerte_prof[$login_prof]['groupe'][$group_id]['info']." pour ";
					//echo count($tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante']);

					for($loop=0;$loop<count($tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante']);$loop++) {
						if($loop>0) {
							//$message.=", ";
							$chaine_app_manquante.=", ";
						}
						//$message.=$tab_group['app_manquante'][$loop];
						//$message.=$tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante'][$loop];
						$chaine_app_manquante.=$tab_alerte_prof[$login_prof]['groupe'][$group_id]['app_manquante'][$loop];
					}
					//$message.=".\n";
					$chaine_app_manquante.=".\n";
				}

				if(isset($tab_group['moy_manquante'])) {
					//$message.="Moyenne(s) manquante(s) en ".$tab_group['info']." pour ";
					$chaine_moy_manquante.="Moyenne(s) manquante(s) en ".$tab_group['info']." pour ";
					for($loop=0;$loop<count($tab_group['moy_manquante']);$loop++) {
						if($loop>0) {
							//$message.=", ";
							$chaine_moy_manquante.=", ";
						}
						//$message.=$tab_group['moy_manquante'][$loop];
						//$message.=$tab_alerte_prof[$login_prof]['groupe'][$group_id]['moy_manquante'][$loop];
						$chaine_moy_manquante.=$tab_alerte_prof[$login_prof]['groupe'][$group_id]['moy_manquante'][$loop];
					}
					//$message.=".\n";
					$chaine_moy_manquante.=".\n";
				}

			}

			$message=stripslashes($MsgMailVerifRemplissageBulletins);
			$message=preg_replace("/___NOM_PROF___/", $info_prof, $message);
			//$message=preg_replace("/___ENSEIGNEMENT___/", "", $message);
			//$message=preg_replace("/___LISTE_ELEVES___/", "", $message);
			$message=preg_replace("/___LIGNE_APPRECIATIONS_MANQUANTES___/", $chaine_app_manquante, $message);
			$message=preg_replace("/___LIGNE_MOYENNES_MANQUANTES___/", $chaine_moy_manquante, $message);
			if(isset($tab_date_conseil[$id_classe]['date'])) {
				$message=preg_replace("/___DATE_CONSEIL___/", formate_date($tab_date_conseil[$id_classe]['date'], "y2", "court"), $message);
			}
			$message=preg_replace("/___NOM_EMETTEUR___/", civ_nom_prenom($_SESSION['login']), $message);

			echo "<tr>\n";
			echo "<td>\n";
			if(in_array($login_prof, $tab_pp)) {
				echo "<div style='float:right; width:16px;margin:3px;' title=\"Ce professeur est ".$gepi_prof_suivi." d'??l??ves de cette classe.\"><img src='../images/bulle_verte.png' width='9' height='9' alt=\"".$gepi_prof_suivi."\" /></div>";
			}
			if($tab_alerte_prof[$login_prof]['email']!="") {
				if(check_mail($tab_alerte_prof[$login_prof]['email'])) {
					$tab_num_mail[]=$num;
					$sujet_mail="[Gepi]: Appreciations et/ou moyennes manquantes";
					echo "<a href='mailto:".$tab_alerte_prof[$login_prof]['email']."?subject=$sujet_mail&amp;body=".rawurlencode($message)."'>".$info_prof."</a>";
					echo "<input type='hidden' name='sujet_$num' id='sujet_$num' value=\"$sujet_mail\" />\n";
					echo "<input type='hidden' name='mail_$num' id='mail_$num' value=\"".$tab_alerte_prof[$login_prof]['email']."\" />\n";
				}
			}
			else {
				echo $info_prof;
			}

			//echo "<br />";
			echo "</td>\n";
			echo "<td".$rowspan.">\n";
			//echo "<textarea id='message_$num' cols='50' rows='5'>$message</textarea>\n";
			echo "<textarea name='message_$num' id='message_$num' cols='50' rows='5'>$message</textarea>\n";
			//echo "<input type='hidden' name='message_$num' id='message_$num' value=\"".rawurlencode($message)."\" />\n";
			//echo "<input type='hidden' name='message_$num' id='message_$num' value=\"".rawurlencode(ereg_replace("\\\n",'_NEWLINE_',$message))."\" />\n";

			echo "</td>\n";

			if(($ver_periode[$per]=="P")&&
			($acces_autorisation_exceptionnelle_modif_cn||
			$acces_autorisation_exceptionnelle_modif_bull_note||
			$acces_autorisation_exceptionnelle_modif_bull_app)) {
				echo "<td".$rowspan.">\n";
				$ajout="";
				if(count($tab_prof['groupe'])==1) {
					foreach($tab_prof['groupe'] as $group_id => $tab_group) {
						$ajout="&amp;periode=$per&amp;id_groupe=$group_id";
						break;
					}
				}
				echo "<p>Autoriser exceptionnellement, bien que la p??riode soit partiellement close, <br />
<ul>";
				if($acces_autorisation_exceptionnelle_modif_cn) {
					echo "
	<li><a href='../cahier_notes/autorisation_exceptionnelle_saisie.php?id_classe=$id_classe".$ajout."' target='_blank'>la saisie/modification de notes du carnet de notes,</a>";

				foreach($tab_prof['groupe'] as $group_id => $tab_group) {
					$sql="SELECT * FROM acces_cn WHERE id_groupe='$group_id' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
					//echo "$sql<br />";
					$test = mysqli_query($mysqli, $sql);
					if($test->num_rows > 0) {
						while($lig_acces=mysqli_fetch_object($test)) {
							echo "<br />
		".$tab_group['info']."&nbsp;: Acc??s (<em>?? la saisie de notes dans le Carnet de notes</em>) ouvert jusqu'au ".formate_date($lig_acces->date_limite, "y", "court");
						}

						$lignes_autoriser_saisie_cn[]="
						<input type='checkbox' name='enseignement_periode[]' id='cn_enseignement_periode_".$group_id."_".$per."' value='".$group_id."|".$per."' onchange=\"checkbox_change(this.id)\" /><label for='cn_enseignement_periode_".$group_id."_".$per."' id='texte_cn_enseignement_periode_".$group_id."_".$per."'>".get_info_grp($group_id)."</label><br />";
					}
				}

				echo "</li>";
				}

				if($acces_autorisation_exceptionnelle_modif_bull_note) {
					echo "
	<li><a href='autorisation_exceptionnelle_saisie_note.php?id_classe=$id_classe".$ajout."' target='_blank'>la saisie/modification de notes du bulletin,</a>";

					foreach($tab_prof['groupe'] as $group_id => $tab_group) {
						$sql="SELECT * FROM acces_exceptionnel_matieres_notes WHERE  id_groupe='$group_id' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
						//echo "$sql<br />";
						$test = mysqli_query($mysqli, $sql);
						if($test->num_rows > 0) {
							while($lig_acces=mysqli_fetch_object($test)) {
								echo "<br />
			".$tab_group['info']."&nbsp;: Acc??s (<em>?? la saisie de notes dans les Bulletins</em>) ouvert jusqu'au ".formate_date($lig_acces->date_limite, "y", "court");
							}
						}

						$lignes_autoriser_saisie_note_bull[]="
							<input type='checkbox' name='enseignement_periode[]' id='bull_note_enseignement_periode_".$group_id."_".$per."' value='".$group_id."|".$per."' onchange=\"checkbox_change(this.id)\" /><label for='bull_note_enseignement_periode_".$group_id."_".$per."' id='texte_bull_note_enseignement_periode_".$group_id."_".$per."'>".get_info_grp($group_id)."</label><br />";
					}
				}

				if($acces_autorisation_exceptionnelle_modif_bull_app) {
					echo "</li>
	<li><a href='autorisation_exceptionnelle_saisie_app.php?id_classe=$id_classe".$ajout."' target='_blank'>la proposition de saisie d'appr??ciation(s) sur les bulletins.</a>";

					foreach($tab_prof['groupe'] as $group_id => $tab_group) {
						$sql="SELECT * FROM matieres_app_delais WHERE id_groupe='$group_id' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
						//echo "$sql<br />";
						$test = mysqli_query($mysqli, $sql);
						if($test->num_rows > 0) {
							while($lig_acces=mysqli_fetch_object($test)) {
								echo "<br />
			".$tab_group['info']."&nbsp;: Acc??s (<em>Appr??ciations des bulletins&nbsp;: ".$lig_acces->mode."</em>) ouvert jusqu'au ".formate_date($lig_acces->date_limite, "y", "court");
							}
						}

						$lignes_autoriser_saisie_app_bull[]="
							<input type='checkbox' name='enseignement_periode[]' id='bull_app_enseignement_periode_".$group_id."_".$per."' value='".$group_id."|".$per."' onchange=\"checkbox_change(this.id)\" /><label for='bull_app_enseignement_periode_".$group_id."_".$per."' id='texte_bull_app_enseignement_periode_".$group_id."_".$per."'>".get_info_grp($group_id)."</label><br />";
					}

					echo "</li>";
				}
				echo "
</ul>\n";
				echo "</td>\n";
			}
			echo "</tr>\n";

			if($acces_envoi_mail) {
				echo "<tr>\n";
				echo "<td>\n";
				if((!$acces_envoi_mail)||(!in_array($num, $tab_num_mail))) {
					echo "<span style='color: red;'>Pas de mail</span>";
				}
				else {
					echo "<span id='mail_envoye_$num'><a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a></span>";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}

			//echo "<a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a>";
			//echo "<br />\n";
			$num++;
		}

		//==================================================================
		// 20191206
		foreach($tab_alerte_absences as $login_prof => $tab_prof) {

			$info_prof=$tab_alerte_absences[$login_prof]['civilite']." ".casse_mot($tab_alerte_absences[$login_prof]['nom'],'maj')." ".casse_mot($tab_alerte_absences[$login_prof]['prenom'],'majf2');

			$chaine_infos_manquantes="Saisies absences manquantes (totaux ou appr??ciation) pour ";
			$cpt_ele_abs=0;
			foreach($tab_prof['absences']['infos_manquantes'] as $current_eleve_info) {

				if($cpt_ele_abs>0) {
					$chaine_infos_manquantes.=", ";
				}
				$chaine_infos_manquantes.=$current_eleve_info;
				$cpt_ele_abs++;
			}
			$chaine_infos_manquantes.=".\n";

			$message=stripslashes($MsgMailVerifRemplissageBulletinsAbs);
			$message=preg_replace("/___NOM_PROF___/", $info_prof, $message);
			//$message=preg_replace("/___ENSEIGNEMENT___/", "", $message);
			//$message=preg_replace("/___LISTE_ELEVES___/", "", $message);
			$message=preg_replace("/___LIGNE_ABS_MANQUANTES___/", $chaine_infos_manquantes, $message);
			//$message=preg_replace("/___LIGNE_MOYENNES_MANQUANTES___/", '', $message);
			if(isset($tab_date_conseil[$id_classe]['date'])) {
				$message=preg_replace("/___DATE_CONSEIL___/", formate_date($tab_date_conseil[$id_classe]['date'], "y2", "court"), $message);
			}

			//$message=preg_replace('#Des moyennes et/ou appr??ciations ne sont pas remplies:#', '', $message);
			$message=preg_replace("/___NOM_EMETTEUR___/", civ_nom_prenom($_SESSION['login']), $message);

			echo "<tr>\n";
			echo "<td>\n";
			if(in_array($login_prof, $tab_pp)) {
				echo "<div style='float:right; width:16px;margin:3px;' title=\"Ce professeur est ".$gepi_prof_suivi." d'??l??ves de cette classe.\"><img src='../images/bulle_verte.png' width='9' height='9' alt=\"".$gepi_prof_suivi."\" /></div>";
			}

			if($tab_alerte_absences[$login_prof]['email']!="") {
				if(check_mail($tab_alerte_absences[$login_prof]['email'])) {
					$tab_num_mail[]=$num;
					$sujet_mail="[Gepi]: Appreciations et/ou totaux absence manquants";
					echo "<a href='mailto:".$tab_alerte_absences[$login_prof]['email']."?subject=$sujet_mail&amp;body=".rawurlencode($message)."'>".$info_prof."</a>";
					echo "<input type='hidden' name='sujet_$num' id='sujet_$num' value=\"$sujet_mail\" />\n";
					echo "<input type='hidden' name='mail_$num' id='mail_$num' value=\"".$tab_alerte_absences[$login_prof]['email']."\" />\n";
				}
			}
			else {
				echo $info_prof;
			}

			//echo "<br />";
			echo "</td>\n";
			echo "<td".$rowspan.">\n";
			//echo "<textarea id='message_$num' cols='50' rows='5'>$message</textarea>\n";
			echo "<textarea name='message_$num' id='message_$num' cols='50' rows='5'>$message</textarea>\n";
			//echo "<input type='hidden' name='message_$num' id='message_$num' value=\"".rawurlencode($message)."\" />\n";
			//echo "<input type='hidden' name='message_$num' id='message_$num' value=\"".rawurlencode(ereg_replace("\\\n",'_NEWLINE_',$message))."\" />\n";

			echo "</td>";

			if(($ver_periode[$per]=="P")&&
			($acces_autorisation_exceptionnelle_modif_bull_note||
			$acces_autorisation_exceptionnelle_modif_bull_app)) {
				echo "<td".$rowspan.">\n";
/*
$_POST['enseignement_periode']=	Array (*)
$_POST[enseignement_periode]['0']=	viescolaire|1
$_POST['id_classe']=	34
*/
				echo "<p>Autoriser exceptionnellement, bien que la p??riode soit partiellement close, <br />
				<a href='../bulletin/autorisation_exceptionnelle_saisie_app.php?id_classe=$id_classe&enseignement_periode[0]=viescolaire|$per' target='_blank'>la saisie/modification des appr??ciations et absences</a>";

				$sql="SELECT * FROM abs_bull_delais WHERE id_classe='$id_classe' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."';";
				//echo "$sql<br />";
				$res_acces_abs=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($res_acces_abs)>0) {
					echo "<br />";
					$lig_abs=mysqli_fetch_object($res_acces_abs);

					echo "L'acc??s est actuellement ouvert jusqu'au ".strftime("%d/%m/%Y ?? %H:%M", mysql_date_to_unix_timestamp($lig_abs->date_limite)).".";
				}
				echo "</p>";

				echo "</td>\n";
			}
			echo "</tr>\n";

			if($acces_envoi_mail) {
				echo "<tr>\n";
				echo "<td>\n";
				if((!$acces_envoi_mail)||(!in_array($num, $tab_num_mail))) {
					echo "<span style='color: red;'>Pas de mail</span>";
				}
				else {
					echo "<span id='mail_envoye_$num'><a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a></span>";
				}
				echo "</td>\n";
				echo "</tr>\n";
			}

			//echo "<a href='#' onclick=\"envoi_mail($num);return false;\">Envoyer</a>";
			//echo "<br />\n";
			$num++;
		}
		//==================================================================

		echo "</table>\n";
		//echo "</div>";

		$tmp_tab=array_unique($lignes_autoriser_saisie_cn);
		if(count($tmp_tab)>1) {
			echo "<form action=\"../cahier_notes/autorisation_exceptionnelle_saisie.php\" method=\"post\" target=\"_blank\">
				<fieldset class='fieldset_opacite50' style='margin:0.5em;'>
					<p class='bold'>Acc??s exceptionnel ?? la saisie de notes dans le carnet de notes&nbsp;:</p>
					<input type='hidden' name='id_classe' value='$id_classe' />";
			foreach($tmp_tab as $key => $ligne) {
				echo $ligne;
			}
			echo "
					<input type='submit' name='id_classe' value='Ouvrir un acc??s exceptionnel pour les enseignements choisis' />
				</fieldset>
			</form>";
		}

		$tmp_tab=array_unique($lignes_autoriser_saisie_note_bull);
		if(count($tmp_tab)>1) {
			echo "<form action=\"../bulletin/autorisation_exceptionnelle_saisie_note.php\" method=\"post\" target=\"_blank\">
				<fieldset class='fieldset_opacite50' style='margin:0.5em;'>
					<p class='bold'>Acc??s exceptionnel ?? la saisie de moyennes dans les bulletins&nbsp;:</p>
					<input type='hidden' name='id_classe' value='$id_classe' />";
			foreach($tmp_tab as $key => $ligne) {
				echo $ligne;
			}
			echo "
					<input type='submit' name='id_classe' value='Ouvrir un acc??s exceptionnel pour les enseignements choisis' />
				</fieldset>
			</form>";
		}

		$tmp_tab=array_unique($lignes_autoriser_saisie_app_bull);
		if(count($tmp_tab)>1) {
			echo "<form action=\"../bulletin/autorisation_exceptionnelle_saisie_app.php\" method=\"post\" target=\"_blank\">
				<fieldset class='fieldset_opacite50' style='margin:0.5em;'>
					<p class='bold'>Acc??s exceptionnel ?? la saisie d'appr??ciations dans les bulletins&nbsp;:</p>
					<input type='hidden' name='id_classe' value='$id_classe' />";
			foreach($tmp_tab as $key => $ligne) {
				echo $ligne;
			}
			echo "
					<input type='submit' name='id_classe' value='Ouvrir un acc??s exceptionnel pour les enseignements choisis' />
				</fieldset>
			</form>";
		}
	}

	//echo "<input type='hidden' name='csrf_alea' id='csrf_alea' value='".$_SESSION['gepi_alea']."' />\n";
	echo add_token_field(true);

	//==============================================
	// 20171201
	if($ver_periode[$per]=="P") {
		$sql="SELECT ac.* FROM acces_cn ac,j_groupes_classes jgc WHERE jgc.id_groupe=ac.id_groupe AND id_classe='$id_classe' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
		//echo "$sql<br />";
		$test = mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			echo "<p style='margin-top:1em;'>Un ou des acc??s exceptionnels ?? la saisie de notes dans le <strong>carnet de notes</strong> <em>(bien que la p??riode soit partiellement close)</em> sont ouverts&nbsp;:<br />";
			while($lig=mysqli_fetch_object($test)) {
				echo "
					".get_info_grp($lig->id_groupe)."&nbsp;: Acc??s ouvert jusqu'au ".formate_date($lig->date_limite, "y", "court")."<br />";
			}
			echo "</p>";
		}

		$sql="SELECT aemn.* FROM acces_exceptionnel_matieres_notes aemn,j_groupes_classes jgc WHERE jgc.id_groupe=aemn.id_groupe AND id_classe='$id_classe' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
		//echo "$sql<br />";
		$test = mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			echo "<p style='margin-top:1em;'>Un ou des acc??s exceptionnels ?? la saisie de <strong>notes</strong> dans les <strong>bulletins</strong> <em>(bien que la p??riode soit partiellement close)</em> sont ouverts&nbsp;:<br />";
			while($lig=mysqli_fetch_object($test)) {
				echo "
					".get_info_grp($lig->id_groupe)."&nbsp;: Acc??s ouvert jusqu'au ".formate_date($lig->date_limite, "y", "court")."<br />";
			}
			echo "</p>";
		}

		$sql="SELECT mad.* FROM matieres_app_delais mad,j_groupes_classes jgc WHERE jgc.id_groupe=mad.id_groupe AND id_classe='$id_classe' AND periode='$per' AND date_limite>'".strftime("%Y-%m-%d %H:%M:%S")."' ORDER BY date_limite ASC;";
		//echo "$sql<br />";
		$test = mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($test)>0) {
			echo "<p style='margin-top:1em;'>Un ou des acc??s exceptionnels ?? la saisie d'<strong>appr??ciations</strong> dans les <strong>bulletins</strong> <em>(bien que la p??riode soit partiellement close)</em> sont ouverts&nbsp;:<br />";
			while($lig=mysqli_fetch_object($test)) {
				echo "
					".get_info_grp($lig->id_groupe)."&nbsp;: Acc??s ouvert jusqu'au ".formate_date($lig->date_limite, "y", "court")."<br />";
			}
			echo "</p>";
		}
	}
	//==============================================

	echo "<script type='text/javascript'>
	// <![CDATA[
	function envoi_mail(num) {
		csrf_alea=document.getElementById('csrf_alea').value;
		destinataire=document.getElementById('mail_'+num).value;
		sujet_mail=document.getElementById('sujet_'+num).value;
		message=document.getElementById('message_'+num).value;

		//alert(message);
		//new Ajax.Updater($('mail_envoye_'+num),'envoi_mail.php?destinataire='+destinataire+'&sujet_mail='+sujet_mail+'&message='+message,{method: 'get'});
		//new Ajax.Updater($('mail_envoye_'+num),'envoi_mail.php?destinataire='+destinataire+'&sujet_mail='+sujet_mail+'&message='+escape(message)+'&csrf_alea='+csrf_alea,{method: 'get'});

		document.getElementById('mail_envoye_'+num).innerHTML=\"<img src='../images/spinner.gif' width='20' height='20' alt='Action en cours d\'ex??cution' title='Action en cours d\'ex??cution' />\";

		//new Ajax.Updater($('mail_envoye_'+num),'envoi_mail.php?destinataire='+destinataire+'&sujet_mail='+sujet_mail+'&message='+encodeURIComponent(message)+'&csrf_alea='+csrf_alea,{method: 'get'});

		//message=encodeURIComponent(message);
		new Ajax.Updater($('mail_envoye_'+num),'envoi_mail.php',{method: 'post',
		parameters: {
			destinataire: destinataire,
			sujet_mail: sujet_mail,
			message: message,
			csrf_alea: csrf_alea
		}});

	}

	function changer_etat_verrouillage_periode(id_classe, num_periode, etat) {
		csrf_alea=document.getElementById('csrf_alea').value;
		new Ajax.Updater($('span_etat_verrouillage_classe'),'verrouillage.php?mode=change_verrouillage&num_periode='+num_periode+'&id_classe='+id_classe+'&etat='+etat+'&csrf_alea='+csrf_alea,{method: 'get'});
		cacher_div('div_modif_verrouillage');
	}

	// 20190318
	function affiche_bull_simp(login_eleve,designation_eleve,id_classe,num_per1,num_per2) {
		document.getElementById('titre_entete_bull_simp').innerHTML='Bulletin simplifi?? de '+designation_eleve+' p??riode '+num_per1+' ?? '+num_per2;
		document.getElementById('corps_bull_simp').innerHTML=\"<div style='width:5em;height:5em;padding:2.5em;text-align:center'><img src='../images/spinner.gif' class='icone16' alt='Patientez' /></div>\";
		new Ajax.Updater($('corps_bull_simp'),'../saisie/ajax_edit_limite.php?choix_edit=2&login_eleve='+login_eleve+'&id_classe='+id_classe+'&periode1='+num_per1+'&periode2='+num_per2,{method: 'get'});
	}

	".js_checkbox_change_style()."

	//]]>
</script>\n";

	//=============================================
	// 20190318
	// Pour le positionnement relatif des div ouverts depuis le bulletin simplifi??:
	$inclusion_depuis_graphes='y';
	include_once("../lib/bulletin_simple.inc.php");
	lib_corriger_appreciation();
	lib_signalement_fautes();
	// lib/share-html.inc.php
	//necessaire_bull_simple();
	//=============================================

	//if ($bulletin_rempli == 'yes') {
	if (($bulletin_rempli == 'yes')&&(($mode=='tout')||($mode=='tout_sauf_avis'))) {
		echo "<p class='bold'>Toutes les rubriques des bulletins de cette classe ont ??t?? renseign??es, vous pouvez proc??der ?? l'impression finale.</p>\n";
		echo "<ul><li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=rien'>cliquant ici.</a></p></li>\n";
		echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=retour'>cliquant ici.</a> puis revenir ?? la page outil de v??rification.</p></li>\n";
		echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_bull'>cliquant ici.</a> puis aller ?? la page impression des bulletins.</p></li>\n";
		//echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_html'>cliquant ici.</a> puis aller ?? la page impression des bulletins HTML.</p></li>\n";
		//echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_pdf'>cliquant ici.</a> puis aller ?? la page impression des bulletins PDF.</p></li></ul>\n";
	} elseif(($temoin_note_app==0)&&($temoin_aid==0)&&($mode=='note_app')) {
		echo "<p class='bold'>Toutes les moyennes et appr??ciations des bulletins de cette classe ont ??t?? renseign??es.</p>\n";
	} elseif(($temoin_avis==0)&&($mode=='avis')) {
		echo "<p class='bold'>Tous les avis de conseil de classe des bulletins de cette classe ont ??t?? renseign??s.</p>\n";
	} elseif(($temoin_abs==0)&&($mode=='abs')) {
		echo "<p class='bold'>Toutes les absences et retards des bulletins de cette classe ont ??t?? renseign??s.</p>\n";
    }elseif ($gepiSettings['active_mod_ects'] == 'y' && $temoin_ects == 0 && $mode=='ects' && $temoin_has_ects) {
        echo "<p class='bold'>Tous les cr??dits ECTS de cette classe ont ??t?? renseign??s.</p>\n";
	} else{
		echo "<br /><p class='bold'>*** Fin des v??rifications. ***</p>\n";
		/*
		echo "<ul><li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=rien'>cliquant ici.</a></p></li>";
		echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=retour'>cliquant ici.</a> puis revenir ?? la page outil de v??rification.</p></li>";
		echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_html'>cliquant ici.</a> puis aller ?? la page impression des bulletins HTML.</p></li>";
		echo "<li><p class='bold'>Acc??der directement au verrouillage de la p??riode en <a href='verrouillage.php?classe=$id_classe&periode=$per&action=imprime_pdf'>cliquant ici.</a> puis aller ?? la page impression des bulletins PDF.</p></li></ul>";
			*/
	}

}
require("../lib/footer.inc.php");
?>
