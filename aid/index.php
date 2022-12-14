<?php
/*
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
if((isset($_POST['sup']))&&($_SESSION['statut']!='administrateur')) {
	$call_data = sql_query("SELECT indice_aid FROM aid_config");
	$sup_all = "";
	$liste_cible = '';
	for ($i=0; ($row=sql_row($call_data,$i)); $i++) {
		$id = $row[0];
		$temp = "sup".$id;
		if (isset($_POST[$temp])) {
			$test = sql_count(sql_query("SELECT indice_aid FROM aid WHERE indice_aid='".$id."'"));
			if ($test != 0) {
				$sup_all = 'no';
			} else {
				$liste_cible = $liste_cible.$id.";";
			}
		}
	}
	$_SESSION['chemin_retour'] = $_SERVER['REQUEST_URI']."?sup_all=".$sup_all;
	header("Location: ../lib/confirm_query.php?liste_cible=$liste_cible&action=del_type_aid".add_token_in_url(false));
}

if (isset($_GET['sup_all'])) $sup_all = $_GET['sup_all']; else $sup_all = '';

if ($sup_all=='no') $msg.="Une ou plusieurs cat??gories aid n'ont pas pu ??tre supprim??es car elles contiennent des aid.";

$tab_droits_pages=array();
if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='cpe')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='professeur')) {
	$sql="SELECT * FROM droits WHERE ".$_SESSION['statut']."='V';";
	$res_droits=mysqli_query($mysqli, $sql);
	while($lig=mysqli_fetch_object($res_droits)) {
		$tab_droits_pages[]=$lig->id;
	}
}

//**************** EN-TETE *********************
$titre_page = "Gestion des AID";
if (!suivi_ariane($_SERVER['PHP_SELF'] ,$titre_page))
		echo "erreur lors de la cr??ation du fil d'ariane";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE *****************

?>
<p class="bold" style="margin-top: .5em;">
	<a href="../accueil.php"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a>
<?php
if($_SESSION['statut']=='administrateur') {
	echo " | 
	<a href='config_aid.php?mode=ajout'>Ajouter une cat??gorie d'AID</a>";
}

if(in_array('/aid/config_aid_fiches_projet.php', $tab_droits_pages)) {
	$test_outils_comp = sql_query1("select count(outils_complementaires) from aid_config where outils_complementaires='y'");
	if ($test_outils_comp != 0) {
	?>
		 | 
		<a href="config_aid_fiches_projet.php">Configurer les fiches projet</a>
	<?php
	}
}

if(in_array('/aid/transfert_groupe_aid.php', $tab_droits_pages)) {
	$sql="SELECT 1=1 FROM j_groupes_types jgt, groupes_types gt WHERE gt.id=jgt.id_type LIMIT 1;";
	$test=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($test)>0) {
		echo " | <a href='transfert_groupe_aid.php'>Transfert/migration Groupe-&gt;AID</a>";
	}
}
if(in_array('/aid/gerer_user_aid.php', $tab_droits_pages)) {
	echo " | <a href='gerer_user_aid.php'>G??rer les professeurs, gestionnaires et super-gestionnaires des AID</a>";
}
?>
</p>
<!-- <p class="medium"> -->
<?php

//$sql="SELECT * FROM aid_config ORDER BY order_display1, order_display2, nom";
if($_SESSION['statut']=='administrateur') {
	$sql="SELECT * FROM aid_config ORDER BY order_display1, order_display2, nom;";
}
else {
	$sql="SELECT ac.* FROM aid_config ac, 
					j_aidcateg_super_gestionnaires jacsg 
				WHERE ac.indice_aid=jacsg.indice_aid AND
					jacsg.id_utilisateur='".$_SESSION['login']."' 
				ORDER BY order_display1, order_display2, nom;";
}
$call_data = mysqli_query($GLOBALS["mysqli"], $sql);
$nb_aid = mysqli_num_rows($call_data);
if ($nb_aid == 0) {
?>
<p class='grand'>Il n'y a actuellement aucune cat??gorie d'AID</p>
<?php
} else {
?>
<form action="index.php" name="formulaire" method="post">
	<table class='boireaus'>
		<caption class="invisible">Cat??gories d'AID</caption>
		<tr>
			<th>Nom - Modifications</th>
			<th>Liste des aid de la cat??gorie</th>
			<th>Nom complet de l'AID</th>
<?php
if($_SESSION['statut']=='administrateur') {
?>
			<th><input type="submit" name="sup" value="Supprimer" /></th>
<?php
}
?>
		</tr>
<?php

	$i=0;
	$alt=1;
	while ($i < $nb_aid) {
		$nom_aid = @old_mysql_result($call_data, $i, "nom");
		$nom_complet_aid = @old_mysql_result($call_data, $i, "nom_complet");
		$indice_aid = @old_mysql_result($call_data, $i, "indice_aid");
		$outils_complementaires  = @old_mysql_result($call_data, $i, "outils_complementaires");
		if ($outils_complementaires=='y') {
			$display_outils = "<br /><span class='small'>(Outils compl??mentaires activ??s)</span>";
		} else {
			$display_outils="";
		}

		if ((getSettingValue("num_aid_trombinoscopes")==$indice_aid) and (getSettingValue("active_module_trombinoscopes")=='y')) {
			$display_trombino = "<br /><span class='small'>(Gestion des acc??s ??l??ves au trombinoscope)</span>";
		} else {
			$display_trombino="";
		}

		$sql="SELECT 1=1 FROM aid WHERE indice_aid = '$indice_aid';";
		$res=mysqli_query($mysqli, $sql);
		if(mysqli_num_rows($res)==0) {
			$title_tr=" title=\"Aucun AID n'est cr???? dans la cat??gorie.\"";
			$style_tr=" style='background-color:grey'";
		}
		else {
			$title_tr="";
			$style_tr="";
		}

		$alt=$alt*(-1);
?>
		<tr class='lig<?php echo $alt; ?>'<?php echo $title_tr.$style_tr;?>>
			<td>
				<a href='config_aid.php?indice_aid=<?php echo $indice_aid; ?>'><?php echo $nom_aid; ?> <img src='../images/edit16.png' class='icone16' alt='??diter' /></a><br /><span style='font-size:x-small'><?php echo $nom_complet_aid;?></span>
				<?php echo $display_outils; ?> <?php echo $display_trombino; ?>
			</td>
			<td>
				<a href='index2.php?indice_aid=<?php echo $indice_aid; ?>'>Liste des aid de la cat??gorie <img src='../images/icons/chercher.png' class='icone16' alt='??diter' /></a>
			</td>
			<td>
				<?php echo $nom_complet_aid; ?>
			</td>
<?php
if($_SESSION['statut']=='administrateur') {
?>
			<td class="center">
				<input type="checkbox" name="sup<?php echo $indice_aid; ?>" />
			</td>
<?php
}
?>
		</tr>
<?php
        $i++;
    }
?>
	</table>
</form>
<?php
}
require("../lib/footer.inc.php");
