<?php

/*
* Copyright 2001, 2020 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
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

// Initialisation
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

//debug_var();
if((isset($id_classe))&&(!preg_match('/^[0-9]{1,}$/', $id_classe))) {
	$msg="Classe non choisie ou choix invalide.<br />";
	unset($id_classe);
}

//include "../lib/periodes.inc.php";
//**************** EN-TETE *****************
$titre_page = "Saisie des absences";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

// Tableau pour les autorisations exceptionnelles de saisie
$tab_autorisation_exceptionnelle_de_saisie=array();
$date_courante=time();
//echo "\$date_courante=$date_courante<br />";
$sql="SELECT * FROM abs_bull_delais WHERE UNIX_TIMESTAMP(date_limite)>'".time()."';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	while ($lig=mysqli_fetch_object($res)) {
		$tab_autorisation_exceptionnelle_de_saisie[$lig->id_classe][$lig->periode]['totaux']=$lig->totaux;
		$tab_autorisation_exceptionnelle_de_saisie[$lig->id_classe][$lig->periode]['appreciation']=$lig->appreciation;
	}
}

if (!isset($id_classe)) {
	echo "<p class=bold><a href='../accueil.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
	echo " | <a href='import_absences_sconet.php'>Importer les absences de Sconet par lots</a>\n";
	echo " | <a href='import_absences_csv.php'>Importer les absences depuis un CSV par lots</a>\n";
	echo " | <a href='import_absences_ent.php'>Importer les absences depuis un CSV ENT par lots</a>\n";
	echo "</p>\n";

	echo "<h2>Saisie des absences</h2>";

	if ((($_SESSION['statut']=="cpe")&&(getSettingValue('GepiAccesAbsTouteClasseCpe')=='yes'))||($_SESSION['statut']!="cpe")) {
		$sql="SELECT DISTINCT c.* FROM classes c, periodes p WHERE p.id_classe = c.id  ORDER BY classe;";
	} else {
		$sql="SELECT DISTINCT c.* FROM classes c, j_eleves_cpe e, j_eleves_classes jc WHERE (e.cpe_login = '".$_SESSION['login']."' AND jc.login = e.e_login AND c.id = jc.id_classe) ORDER BY classe;";
	}
	$calldata = mysqli_query($GLOBALS["mysqli"], $sql);
	$nombreligne = mysqli_num_rows($calldata);

	if($nombreligne==0) {
		echo "<p style='color:red'>Aucune classe ne vous est affect??e.</p>
		<br />
		<p style='text-indent:-6em; margin-left:6em; margin-top:1em;'><em>Remarque&nbsp;:</em> Il est possible, quand il y a plusieurs CPE, de donner les m??mes droits ?? tous les CPE ind??pendamment du CPE attribu?? ?? chaque ??l??ve.<br />
		Ce param??trage doit ??tre effectu?? en administrateur dans <strong>Gestion g??n??rale/Droits d'acc??s</strong>.</p>";

		require "../lib/footer.inc.php";
		die();
	}

	echo "<p>Total : $nombreligne classe";
	if($nombreligne>1){echo "s";}
	echo " - ";
	echo "Cliquez sur la classe pour laquelle vous souhaitez saisir les absences :</p>\n";
	if (!getSettingAOui('GepiAccesAbsTouteClasseCpe')) {
		echo "<p><em>Remarque&nbsp;:</em> s'affichent toutes les classes pour lesquelles vous ??tes responsable du suivi d'au moins un ".$gepiSettings['denomination_eleve']." de la classe.</p>\n";
	}

	/*
	$i = 0;
	while ($i < $nombreligne){
		$id_classe = old_mysql_result($calldata, $i, "id");
		$classe_liste = old_mysql_result($calldata, $i, "classe");
		echo "<br /><a href='index.php?id_classe=$id_classe'>$classe_liste</a>\n";
		$i++;

	}
	*/

	$i = 0;
	unset($tab_lien);
	unset($tab_txt);
	$tab_txt=array();
	$tab_lien=array();
	while ($lig=mysqli_fetch_object($calldata)) {
		$tab_lien[$i] = "index.php?id_classe=".$lig->id;
		$tab_txt[$i] = $lig->classe;
		$i++;
	}
	tab_liste($tab_txt,$tab_lien,3);



	echo "<br />\n";
} else {
	include "../lib/periodes.inc.php";

	// On choisit la p??riode :
	echo "<p class=bold><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Choisir une autre classe</a>";

	echo " | <a href='import_absences_sconet.php'>Importer les absences de Sconet par lots</a>\n";

	echo " | <a href='import_absences_csv.php'>Importer les absences depuis un CSV par lots</a>\n";

	echo " | <a href='import_absences_ent.php'>Importer les absences depuis un CSV ENT par lots</a>\n";

	echo "</p>\n";

	$call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
	$classe = old_mysql_result($call_classe, "0", "classe");
	echo "<h2>Classe de ".$classe."</h2>\n";
	echo "<p><b>Saisie manuelle - Choisissez la p??riode : </b></p>\n";
	//echo "<ul>\n";
	$i="1";
	echo "<table class='boireaus' cellpadding='3'>\n";

	// si le module de gestion des absences est activ?? alors on ajout un colspan de 2 pour l'ent??t d'importation
	$colspan = '3';
	if ( getSettingValue("active_module_absence") === 'y' || getSettingValue("abs2_import_manuel_bulletin")==='y') {
		$colspan = '4';
	}

	echo "<tr><th>P??riode</th><th style='width:6em;'>Saisir</th><th style='width:6em;'>Consulter</th><th colspan='$colspan'>Importer les absences</th></tr>\n";
	$alt=1;
	while ($i < $nb_periode) {
		$alt=$alt*(-1);
		echo "<tr class='lig$alt'>\n";
		echo "<th>".ucfirst($nom_periode[$i])."</th>\n";
		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
		  echo "<td><a href='saisie_absences.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/edit16.png' width='16' height='16' alt='Saisir' title='Saisir' /></a></td>\n";
		  //echo "<td><a href='saisir_groupe.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/edit16.png' width='16' height='16' alt='Saisir' title='Saisir' /></a></td>\n";
		}
		elseif(((isset($tab_autorisation_exceptionnelle_de_saisie[$id_classe][$i]['totaux']))&&($tab_autorisation_exceptionnelle_de_saisie[$id_classe][$i]['totaux']=='y'))||
		((isset($tab_autorisation_exceptionnelle_de_saisie[$id_classe][$i]['appreciation']))&&($tab_autorisation_exceptionnelle_de_saisie[$id_classe][$i]['appreciation']=='y'))) {
			echo "<td title=\"Autorisation exceptionnelle de saisie.\" style='background-color:orange'><a href='saisie_absences.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/edit16.png' width='16' height='16' alt='Saisir' title='Saisir' /></a></td>\n";
		}
		else {
			echo "<td style='color:red;'><img src='../images/disabled.png' width='20' height='20' alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."' /></td>\n";
		}
		echo "<td><a href='consulter_absences.php?id_classe=$id_classe&amp;periode_num=$i'><img src='../images/icons/chercher.png' width='16' height='16' alt='Consulter' title='Consulter' /></a></td>\n";

		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
			echo "<td style='width:5em;'><a href='import_absences_gep.php?id_classe=$id_classe&amp;periode_num=$i'>de GEP</a></td>\n";
		} else {
			echo "<td style='color:red;' colspan='$colspan'><img src='../images/disabled.png' width='20' height='20' alt='".$gepiClosedPeriodLabel."' title='".$gepiClosedPeriodLabel."' /></td>\n";
		}

	    // si le module de gestion des absences de gepi est activ?? alors on propose l'importation des absences de ce module
	    if ( getSettingValue("active_module_absence") === 'y' || getSettingValue("abs2_import_manuel_bulletin")==='y' ) {
			if(($ver_periode[$i] == "N")||
			(($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
				echo "<td style='width:5em;'><a href='import_absences_gepi.php?id_classe=$id_classe&amp;periode_num=$i'>de GEPI</a></td>\n";
			}
			/*
			else {
				echo "<td style='color:red;'>".$gepiClosedPeriodLabel."</td>\n";
			}
			*/
	    }

		if(($ver_periode[$i] == "N")||
		 (($ver_periode[$i]!="O")&&($_SESSION['statut']=='secours'))) {
			echo "<td style='width:5em;'><a href='import_absences_sconet.php?id_classe=$id_classe&amp;num_periode=$i'>de Sconet</a></td>\n";

			echo "<td style='width:5em;'>
				<a href='import_absences_csv.php?id_classe=$id_classe&amp;num_periode=$i'>depuis un CSV</a>
			</td>\n";
		}
		/*
		else {
			echo "<td style='color:red;'>".$gepiClosedPeriodLabel."</td>\n";
		}
		*/
		$i++;
	}
	echo "</table>\n";
	//echo "</ul>\n";

	echo "<p><br /></p>\n";

	echo "<p><i>NOTES:</i></p>\n";
	echo "<ul>\n";
	echo "<li><p>Pour l'importation des absences depuis GEP, les fichiers F_EABS.DBF et F_NOMA.DBF de la base GEP sont requis.</p></li>\n";
	echo "<li><p>Pour l'importation des absences depuis Sconet, le fichier ExportAbsences.xml de Sconet est requis.</p></li>\n";
	echo "</ul>\n";

	/*
	$i="1";
	// On propose l'importation ?? partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation ?? partir du fichier F_EABS.DBF de la base GEP (fichier F_NOMA.DBF ??galement requis) :</p>\n";
			echo "<ul><li><a href='import_absences_gep.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences ?? partir du fichier F_EABS.DBF</a></li></ul>\n";
		}
		$i++;
	}
	*/


	/*
	$i="1";
	// On propose l'importation ?? partir d'un fichier GEP
	while ($i < $nb_periode) {
		if ($ver_periode[$i] == "N") {
			echo "<p class='bold'>".ucfirst($nom_periode[$i])." - Importation ?? partir du fichier <b>exportAbsences.xml</b> de <b>Sconet</b> :</p>\n";
			echo "<ul><li><a href='import_absences_sconet.php?id_classe=$id_classe&amp;periode_num=$i'>Importer les absences ?? partir du fichier exportAbsences.xml</a></li></ul>\n";
		}
		$i++;
	}
	*/
}
require "../lib/footer.inc.php";
?>
