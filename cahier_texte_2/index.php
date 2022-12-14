<?php
/*
 *
 * Copyright 2009-2019 Josselin Jacquard, Stephane Boireau
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
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

$filtrage_extensions_fichiers_table_ct_types_documents='y';

// On désamorce une tentative de contournement du traitement anti-injection lorsque register_globals=on
if (isset($_GET['traite_anti_inject']) || isset($_POST['traite_anti_inject'])) $traite_anti_inject = "yes";

// Initialisations files
include("../lib/initialisationsPropel.inc.php");
require_once("../lib/initialisations.inc.php");

//=================================
//
//	emplois du temps - requirements
//
//=================================
include("../edt_organisation/cdt_requirements.php");



//echo("Debug Locale : ".setLocale(LC_TIME,0));

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

//On vérifie si le module est activé
//if (getSettingValue("active_cahiers_texte")!='y') {
if(!acces_cdt()) {
    die("Le module n'est pas activé.");
}

//recherche de l'utilisateur avec propel
$utilisateur = UtilisateurProfessionnelPeer::getUtilisateursSessionEnCours();
if ($utilisateur == null) {
	header("Location: ../logout.php?auto=1");
	die();
}

// On met le header en petit par défaut
$_SESSION['cacher_header'] = "y";
//**************** EN-TETE *****************
$titre_page = "Cahier de textes";

// 20221012
$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "cahier_texte_2/init_cahier_texte_2";
$utilisation_win = 'oui';
//$utilisation_jsdivdrag = "non";
$windows_effects = "non";
$message_deconnexion = "non";

//on regarde si les preferences pour le cdt sont precisees dans la requete
$cdt_version_pref = isset($_POST["cdt_version_pref"]) ? $_POST["cdt_version_pref"] :(isset($_GET["cdt_version_pref"]) ? $_GET["cdt_version_pref"] :NULL);
if ($cdt_version_pref != null) {
    $utilisateur->setPreferenceValeur("cdt_version", $cdt_version_pref);
}

//on regarde les preference de l'utilisateur
if ($utilisateur->getPreferenceValeur("cdt_version") == "1") {
    header("Location: ../cahier_texte/index.php?cdt_version_pref=1");
    die();
}


//on reste sur le cdt1, le navigateur n'etant pas compatible avec le cdt2
if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6' ) !== FALSE) {
    header("Location: ../cahier_texte/index.php");
    die();
}
//=================================
//
//		Init emplois du temps
//
//=================================
$edt_liens_target_blank="y";
include("../edt_organisation/cdt_initialisation.php");
$pas_de_message_deconnexion = 1;

require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *************
//-----------------------------------------------------------------------------------

//debug_var();

insere_lien_calendrier_crob("right");

// si l'id d'un groupe est spécifié, on l'enregistre dans un champ hidden, il sera utilisé par le javascript d'initialisation pour basculer vers le groupe concerné
echo "<input type='hidden' name='id_groupe_init' id='id_groupe_init' value='";
$id_groupe = isset($_POST["id_groupe"]) ? $_POST["id_groupe"] :(isset($_GET["id_groupe"]) ? $_GET["id_groupe"] :NULL);
if ($id_groupe != NULL) {
	echo $_GET["id_groupe"];
} else if (isset($_SESSION['id_groupe_session'])) {
	echo $_SESSION['id_groupe_session'];
}
echo "' />\n";

//============================================
// Pour pouvoir pointer une notice précise depuis une page externe:
echo "<input type='hidden' name='type_notice_init' id='type_notice_init' value='";
$type_notice = isset($_POST["type_notice"]) ? $_POST["type_notice"] :(isset($_GET["type_notice"]) ? $_GET["type_notice"] :NULL);
if ($type_notice != NULL) {
	echo $type_notice;
}
echo "' />\n";
//echo "\$type_notice=$type_notice<br />";

echo "<input type='hidden' name='id_ct_init' id='id_ct_init' value='";
$id_ct = isset($_POST["id_ct"]) ? $_POST["id_ct"] :(isset($_GET["id_ct"]) ? $_GET["id_ct"] :NULL);
if ($id_ct != NULL) {
	echo $id_ct;
}
echo "' />\n";
//echo "\$id_ct=$id_ct<br />";
//============================================

echo "<table width=\"98%\" cellspacing=0 align=\"center\" summary=\"Tableau d'entète\">\n";
echo "<tr>\n";
echo "<td valign='center'>\n";
echo "<button style='width: 200px;' onclick=\"javascript:
						getWinDernieresNotices().show();
						getWinDernieresNotices().toFront();
						return false;
				\">Voir les dernières notices</button>\n";
echo "<br />";
// Comment tester qu'une fenêtre est actuellement affichée?
echo "<button style='width: 200px;' onclick=\"javascript:
						getWinDernieresNotices().setLocation(155, 40);
						getWinDernieresNotices().hide();
						getWinCalendar().setLocation(0, GetWidth() - 245);
						getWinEditionNotice().setLocation(160, 334);
						getWinEditionNotice().setSize(GetWidth()-360, GetHeight() - 160);
						getWinListeNotices().setLocation(160, 0);
						getWinListeNotices().setSize(330, GetHeight() - 160);
						if(document.getElementById('win_banque_texte')) {
							if(document.getElementById('win_banque_texte').style.display!='none') {
								getWinBanqueTexte().setLocation(10, 40);
							}
						}
						if(document.getElementById('win_car_spec')) {
							if(document.getElementById('win_car_spec').style.display!='none') {
								getWinCarSpec().setLocation(10, 340);
							}
						}
						if(document.getElementById('win_archives')) {
							if(document.getElementById('win_archives').style.display!='none') {
								getWinArchives().setLocation(100, 400);
							}
						}
						if(document.getElementById('win_liste_notices_privees')) {
							if(document.getElementById('win_liste_notices_privees').style.display!='none') {
								getWinListeNoticesPrivees().setLocation(0, 100);
							}
						}
						if(document.getElementById('win_dev_classe')) {
							if(document.getElementById('win_dev_classe').style.display!='none') {
								getWinDevoirsDeLaClasse().setLocation(0, 304);
							}
						}
						return false;
				\">Repositionner les fenetres</button>\n";
echo "</td>";

echo "<td width='20 px'>";
echo "<a href=\"./index.php?cdt_version_pref=1\">\n";
echo "<img src='../images/icons/cdt2_1.png' alt='Utiliser la version 1 du cahier de textes' class='link' title='Utiliser la version 1 du cahier de textes'/> </a>";
//echo "<button style='width: 200px;' onclick=\"javascript:window.location.replace('./index.php?cdt_version_pref=1')
//				\">Utiliser la version 1 du cahier de textes</button>\n";
echo "</td>";
// **********************************************
// Affichage des différents groupes du professeur
// Récupération de toutes les infos sur le groupe
echo "<td valign='center'>";
$groups = $utilisateur->getGroupes();
if ($groups->isEmpty()) {
    echo "<br /><br />";
    echo "<b>Aucun cahier de textes n'est disponible.</b>";
    echo "<br /><br />";
}

if(!getSettingAOui("active_cahiers_texte")) {
	// On doit avoir acces_cdt_prof=y
	echo "<p style='text-align:center; color:red'>Ces cahiers de textes sont personnels. Ils ne sont pas accessibles des élèves, parents,...";
	if(getSettingValue("acces_cdt_prof_url_cdt_officiel")!="") {
		echo "<br />Les CDT officiels <em>(consultés par les élèves,...)</em> sont à l'adresse <a href='".getSettingValue("acces_cdt_prof_url_cdt_officiel")."' target='_blank'>".getSettingValue("acces_cdt_prof_url_cdt_officiel")."</a>";
	}
	echo "</p>";
}

$nom_ou_description_groupe_cdt=getPref($_SESSION['login'], "nom_ou_description_groupe_cdt", "name");
//echo "\$nom_ou_description_groupe_cdt=$nom_ou_description_groupe_cdt<br />";

$a = 1;
foreach($groups as $group) {
	$sql="SELECT 1=1 FROM j_groupes_visibilite WHERE id_groupe='".$group->getId()."' AND domaine='cahier_texte' AND visible='n';";
	$test_grp_visib=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test_grp_visib)==0) {
		echo "<a href=\"#\" style=\"font-size: 11pt;\"  onclick=\"javascript:
				id_groupe = '".$group->getId()."';
				getWinDernieresNotices().hide();
				getWinListeNotices();
				new Ajax.Updater('affichage_liste_notice', './ajax_affichages_liste_notices.php?id_groupe=".$group->getId()."', {encoding: 'utf-8'});
				getWinEditionNotice().setAjaxContent('./ajax_edition_compte_rendu.php?id_groupe=".$group->getId()."&today='+getCalendarUnixDate(), { 
				    		encoding: 'utf-8',
				    		onComplete : 
				    		function() {
				    			initWysiwyg();
							}
						}
				);
				return false;
			\">";

			echo "<span title=\"".$group->getName()." - ".$group->getDescriptionAvecClasses()." (";
			$cpt_prof=0;
			foreach($group->getProfesseurs() as $prof) {
				if($cpt_prof>0) {echo ", ";}
				echo casse_mot($prof->getNom(),"maj")." ".casse_mot($prof->getPrenom(),"majf2");
				$cpt_prof++;
			}
			echo ").\">";
			if($nom_ou_description_groupe_cdt=='name') {
				echo $group->getNameAvecClasses();
			}
			else {
				echo $group->getDescriptionAvecClasses();
			}
			echo "</span>";
		echo "</a>&nbsp;\n";

		if ($a == 3) {
			$a = 1;
		} else {
			$a = $a + 1;
		}
	}
}
echo "<a href='creer_sequence.php'>Pr&eacute;parer une s&eacute;quence enti&egrave;re</a></td>";
// Fin Affichage des différents groupes du professeur
// **********************************************
echo "<td width='250 px'></td>";
echo "</tr>\n";
echo "</table>\n<hr />";
//=================================
//
//	emplois du temps - affichage
//
//=================================
$edt_avec_semAB="y";
require_once("../edt_organisation/cdt_voir_view.php");

echo "<script type='text/javascript'>".js_checkbox_change_style()."</script>";

require("../lib/footer.inc.php");
?>
