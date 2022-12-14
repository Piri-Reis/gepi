<?php
/*
 * $Id$
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
$current_matiere=isset($_POST['current_matiere']) ? $_POST['current_matiere'] : (isset($_GET['current_matiere']) ? $_GET['current_matiere'] : NULL);

$tab_options_sconet=get_tab_options_sconet();

if((isset($_GET['export_ele_csv']))&&(isset($_GET['matiere']))) {
	check_token();

	$csv="INE;ELENOET;ELE_ID;LOGIN;NOM;PRENOM;SEXE;NAISSANCE;CLASSES;\n";
	$sql="SELECT distinct e.* FROM j_eleves_groupes jeg, 
						j_groupes_matieres jgm, 
						j_eleves_classes jec,
						eleves e
					WHERE jeg.id_groupe=jgm.id_groupe AND 
						jec.login=jeg.login AND 
						jec.login=e.login AND 
						jgm.id_matiere='".$_GET['matiere']."';";
	$res=mysqli_query($GLOBALS["mysqli"],$sql);
	while($lig=mysqli_fetch_object($res)) {
		$csv.=$lig->no_gep.";".$lig->elenoet.";".$lig->ele_id.";".$lig->login.";".$lig->nom.";".$lig->prenom.";".$lig->sexe.";".formate_date($lig->naissance).";".get_chaine_liste_noms_classes_from_ele_login($lig->login).";\n";
	}

	$nom_fic=remplace_accents("liste_eleves_suivant_".$_GET['matiere'])."_".strftime("%Y%m%d_%H%M%S").".csv";
	send_file_download_headers('text/x-csv',$nom_fic);
	echo echo_csv_encoded($csv);
	die();
}

if (isset($_POST['isposted'])) {
	check_token();
	$ok = 'yes';
	$ok_categorie = 'yes';
	$code_matiere=isset($_POST['code_matiere']) ? $_POST['code_matiere'] : "";
	if (isset($_POST['reg_current_matiere'])) {
		// On v??rifie d'abord que l'identifiant est constitu?? uniquement de lettres et de chiffres :
		$matiere_name = $_POST['reg_current_matiere'];
		if ((!isset($_POST['matiere_categorie']))||(!is_numeric($_POST['matiere_categorie']))) {
			// On emp??che les mise ?? jour globale automatiques, car on n'est pas s??r de ce qui s'est pass?? si l'ID n'est pas num??rique...
			//$ok = "no";
			$ok_categorie = 'no';
			$matiere_categorie = "0";
		} else {
			$matiere_categorie = $_POST['matiere_categorie'];
		}
		//if (ereg ("^[a-zA-Z_]{1}[a-zA-Z0-9_]{1,19}$", $matiere_name)) {
		// Le POINT est interdit dans le nom court de mati??re (probl??me avec des noms de champs PHP sinon notamment dans matieres/index.php)
		if (preg_match("/^[a-zA-Z_]{1}[a-zA-Z0-9_-]{1,19}$/", $matiere_name)) {
			$sql="SELECT * from matieres WHERE matiere='$matiere_name'";
			//echo "$sql<br />";
			$verify_query = mysqli_query($GLOBALS["mysqli"], $sql);
			$verify = mysqli_num_rows($verify_query);
			if ($verify == 0) {
				//========================
				// Quand on poste un &, c'est un &amp; qui est re??u.
				//$matiere_nom_complet = $_POST['matiere_nom_complet'];
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
				$matiere_nom_complet = html_entity_decode($_POST['matiere_nom_complet']);
				if($matiere_nom_complet=="") {
					$matiere_nom_complet=$matiere_name;
				}
				//echo "\$matiere_nom_complet=$matiere_nom_complet<br />\n";
				//========================
				$matiere_priorite = $_POST['matiere_priorite'];
				$sql="INSERT INTO matieres SET matiere='".$matiere_name."', nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "',matiere_aid='n',matiere_atelier='n', code_matiere='$code_matiere';";
				//echo "$sql<br />\n";
				$register_matiere = mysqli_query($GLOBALS["mysqli"], $sql);
				if (!$register_matiere) {
					$msg = "Une erreur s'est produite lors de l'enregistrement de la nouvelle mati??re. <br />";
					$ok = 'no';
				} else {
					$msg = "La nouvelle mati??re a bien ??t?? enregistr??e. <br />";
				}
			} else {
				$msg = "Cette mati??re existe d??j?? !! <br />";
				$ok = 'no';
			}
		} else {
			$msg = "L'identifiant de mati??re doit ??tre constitu?? uniquement de lettres, de chiffres <em>(et ??ventuellement des tirets - et _)</em> avec un maximum de 19 caract??res ! <br />";
			if(!preg_match("/^[a-zA-Z]/", $matiere_name)) {
				$msg.="Le nom de mati??re doit commencer par une lettre.<br />";
			}
			$caracteres_non_valides=preg_replace("/[a-zA-Z0-9_-]/", "", $matiere_name);
			if($caracteres_non_valides!="") {
				$msg.="Le ou les caract??res (<span style='color:blue'>$caracteres_non_valides</span>) sont non valides.<br />";
			}
			$ok = 'no';
		}
	} else {

		$matiere_nom_complet = $_POST['matiere_nom_complet'];
		$matiere_nom_complet = html_entity_decode($_POST['matiere_nom_complet']);
		$matiere_priorite = $_POST['matiere_priorite'];
		$matiere_name = $_POST['matiere_name'];
		if ((!isset($_POST['matiere_categorie']))||(!is_numeric($_POST['matiere_categorie']))) {
			$matiere_categorie = "0";
			$ok_categorie = 'no';
		} else {
			$matiere_categorie = $_POST['matiere_categorie'];
		}

		$sql="UPDATE matieres SET nom_complet='".$matiere_nom_complet."', priority='".$matiere_priorite."', categorie_id = '" . $matiere_categorie . "', code_matiere='$code_matiere' WHERE matiere='".$matiere_name."';";
		//echo "$sql<br />\n";
		$register_matiere = mysqli_query($GLOBALS["mysqli"], $sql);

		if (!$register_matiere) {
			$msg = "Une erreur s'est produite lors de la modification de la mati??re <br />";
			$ok = 'no';
		} else {
			$msg = "Les modifications ont ??t?? enregistr??es ! <br />";
		}
	}

	if($ok=='yes') {
		if($code_matiere!='') {
			$sql="SELECT 1=1 FROM nomenclatures_valeurs WHERE type='matiere' AND code='".$code_matiere."' AND nom='option_sconet_saisie';";
			$test=mysqli_query($GLOBALS["mysqli"], $sql);
			if((mysqli_num_rows($test)>0)&&(!isset($_POST['option_sconet']))) {
				$sql="DELETE FROM nomenclatures_valeurs WHERE type='matiere' AND code='".$code_matiere."' AND nom='option_sconet_saisie';";
				$del=mysqli_query($GLOBALS["mysqli"], $sql);
			}
			elseif((mysqli_num_rows($test)==0)&&(isset($_POST['option_sconet']))) {
				$sql="INSERT INTO nomenclatures_valeurs SET type='matiere', code='".$code_matiere."', nom='option_sconet_saisie', valeur='y';";
				$insert=mysqli_query($GLOBALS["mysqli"], $sql);
			}
		}
	}

	if ((isset($_POST['force_defaut'])) and ($ok == 'yes')) {
		$sql="UPDATE j_groupes_matieres jgm, j_groupes_classes jgc SET jgc.priorite='".$matiere_priorite."' 
		WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
		//echo "$sql<br />";
		//$msg = rawurlencode($sql);
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if ((isset($_POST['force_defaut_categorie'])) and ($ok == 'yes') and ($ok_categorie == 'yes')) {
		$sql="UPDATE j_groupes_classes jgc, j_groupes_matieres jgm SET jgc.categorie_id='".$matiere_categorie."' 
		WHERE (jgc.id_groupe = jgm.id_groupe AND jgm.id_matiere='".$matiere_name."')";
		//echo "$sql<br />";
		//$msg = rawurlencode($sql);
		$req = mysqli_query($GLOBALS["mysqli"], $sql);
	}

	if($ok=='yes') {
		$login_prof=isset($_POST['login_prof']) ? $_POST['login_prof'] : NULL;
		if(isset($login_prof)) {
			// R??cup??rer la liste des profs actuellement associ??s
			$tab_profs_associes=array();
			$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$matiere_name' ORDER BY u.nom, u.prenom;";
			//echo "$sql<br />\n";
			$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_profs)>0) {
				while($lig=mysqli_fetch_object($res_profs)) {
					$tab_profs_associes[]=$lig->login;
				}
			}
	
			$nb_inser=0;
			for($loop=0;$loop<count($login_prof);$loop++) {
				if(!in_array($login_prof[$loop], $tab_profs_associes)) {
					// Recherche de l'ordre mati??re le plus ??lev?? pour ce prof
					$sql="SELECT MAX(ordre_matieres) max_ordre FROM j_professeurs_matieres WHERE id_professeur='".$login_prof[$loop]."';";
					//echo "$sql<br />\n";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						$ordre_matieres=1;
					}
					else {
						$ordre_matieres=old_mysql_result($res, 0, "max_ordre")+1;
					}
	
					// On ajoute le prof
					$sql="INSERT INTO j_professeurs_matieres SET id_professeur='$login_prof[$loop]', id_matiere='$matiere_name', ordre_matieres='$ordre_matieres';";
					//echo "$sql<br />\n";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if(!$insert) {
						$msg.="Erreur lors de l'association de ".$login_prof[$loop]." avec la mati??re $matiere_name<br />";
					}
					else {
						$nb_inser++;
					}
				}

			}
	
			if($nb_inser>0) {
				$msg.="$nb_inser professeur(s) a(ont) ??t?? associ??(s) avec la mati??re $matiere_name<br />";
			}
	
			$nb_suppr=0;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				if(!in_array($tab_profs_associes[$loop], $login_prof)) {
					$sql="SELECT 1=1 FROM j_groupes_professeurs jgp, j_groupes_matieres jgm WHERE (jgp.login='".$tab_profs_associes[$loop]."' AND jgm.id_matiere='$matiere_name' AND jgm.id_groupe=jgp.id_groupe)";
					//echo "$sql<br />";
					$res=mysqli_query($GLOBALS["mysqli"], $sql);
					if(mysqli_num_rows($res)==0) {
						/*
						$sql="SELECT ordre_matieres FROM j_professeurs_matieres WHERE id_professeur='$login_prof' AND id_matiere='$matiere_name';";
						$res=mysql_query($sql);
						*/
	
						$sql="DELETE FROM j_professeurs_matieres WHERE id_professeur='".$tab_profs_associes[$loop]."' AND id_matiere='$matiere_name';";
						//echo "$sql<br />\n";
						$suppr=mysqli_query($GLOBALS["mysqli"], $sql);
						if(!$suppr) {
							$msg.="Erreur lors de la suppression de l'association de ".$tab_profs_associes[$loop]." avec la mati??re $matiere_name<br />";
						}
						else {
							$nb_suppr++;
						}
					}
					else {
						$msg.="Dissociation impossible : Le professeur ".$tab_profs_associes[$loop]." enseigne la mati??re $matiere_name dans un ou des enseignements.<br />";
					}
				}
			}
	
			if($nb_suppr>0) {
				$msg.="$nb_suppr professeur(s) a(ont) ??t?? dissoci??(s) de la mati??re $matiere_name<br />";
			}
	
		}

	}

	/*
	// 20161011
	if((isset($_POST['force_modalite']))&&(isset($_POST['code_modalite_elect']))) {
		if($_POST['code_modalite_elect']=="") {






		}
		else {






		}
	}
	*/

	//$msg = rawurlencode($msg);
	header("location: index.php?msg=$msg");
	die();
}

$themessage = 'Des modifications ont ??t?? effectu??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE *******************************
$titre_page = "Gestion des mati??res | Modifier une mati??re";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE ****************************

// On va chercher les infos de la mati??re que l'on souhaite modifier
if (isset($current_matiere)) {
	$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT nom_complet, priority, categorie_id, code_matiere from matieres WHERE matiere='".$current_matiere."'");
	if(mysqli_num_rows($call_data)==0) {
		echo "<p class='bold'><a href='index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>
		<p style='color:red'>La mati??re propos??e n'existe pas.</p>";
		require("../lib/footer.inc.php");
		die();
	}
	$lig_matiere=mysqli_fetch_object($call_data);
	$matiere_nom_complet = $lig_matiere->nom_complet;
	$matiere_priorite = $lig_matiere->priority;
	$matiere_cat_id = $lig_matiere->categorie_id;
	$code_matiere = $lig_matiere->code_matiere;
} else {
	$matiere_nom_complet = "";
	$matiere_priorite = "0";
	$current_matiere = "";
	$matiere_cat_id = "0";
	$code_matiere="";
}

$chaine_options_matieres="";
$sql="SELECT * FROM matieres ORDER BY nom_complet, matiere;";
$res_matiere_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_matiere_tmp)>0) {
	$matiere_prec="";
	$matiere_suiv="";
	$temoin_tmp=0;
	$cpt_matiere=0;
	$num_matiere=-1;
	while($lig_matiere_tmp=mysqli_fetch_object($res_matiere_tmp)){
		if($lig_matiere_tmp->matiere==$current_matiere) {
			// Index de la mati??re dans les <option>
			$num_matiere=$cpt_matiere;

			$chaine_options_matieres.="<option value='$lig_matiere_tmp->matiere' selected='true'>$lig_matiere_tmp->nom_complet</option>\n";
			$temoin_tmp=1;
			if($lig_matiere_tmp=mysqli_fetch_object($res_matiere_tmp)){
				$chaine_options_matieres.="<option value='$lig_matiere_tmp->matiere'>$lig_matiere_tmp->nom_complet</option>\n";
				$matiere_suiv=$lig_matiere_tmp->matiere;
			}
			else{
				$matiere_suiv="";
			}
		}
		else {
			$chaine_options_matieres.="<option value='$lig_matiere_tmp->matiere'>$lig_matiere_tmp->nom_complet</option>\n";
		}

		if($temoin_tmp==0) {
			$matiere_prec=$lig_matiere_tmp->matiere;
		}
		$cpt_matiere++;
	}
}

if($chaine_options_matieres!="") {

	$lien_matiere_precedente="";
	if($matiere_prec!="") {
		$lien_matiere_precedente="<a href='".$_SERVER['PHP_SELF']."?current_matiere=".$matiere_prec."'".insert_confirm_abandon()."><img src='../images/icons/arrow-left.png' class='icone16' alt='Pr??c??dente' /></a>";
	}
	$lien_matiere_suivante="";
	if($matiere_suiv!="") {
		$lien_matiere_suivante="<a href='".$_SERVER['PHP_SELF']."?current_matiere=".$matiere_suiv."'".insert_confirm_abandon()."><img src='../images/icons/arrow-right.png' class='icone16' alt='Suivante' /></a>";
	}

	echo "<script type='text/javascript'>
	// Initialisation
	change='no';

	function confirm_changement_matiere(thechange, themessage)
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
				document.getElementById('current_matiere_chgt').selectedIndex=$num_matiere;
			}
		}
	}
</script>\n";

	echo "<form action=\"modify_matiere.php\" method=\"post\" name='form1'>
	<p class=\"bold\"><a href='index.php'".insert_confirm_abandon()."><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a> |
	 $lien_matiere_precedente
	<select name='current_matiere' id='current_matiere_chgt' onchange=\"confirm_changement_matiere(change, '$themessage');\">
		$chaine_options_matieres
	</select>
	$lien_matiere_suivante
	</p>
</form>";

}
else {
	echo "<p class=\"bold\"><a href='index.php'<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a></p>";
}
echo "<form enctype=\"multipart/form-data\" action=\"modify_matiere.php\" method=\"post\">
<p><input type=\"submit\" value=\"Enregistrer\"></input></p>";
echo add_token_field();
?>

<div style='float:right; width: 20em; border: 1px solid black; margin-left: 1em; padding:2px;' class='fieldset_opacite50'>
<?php
	$tab_profs_associes=array();
	if($current_matiere!="") {
		$sql="SELECT u.login FROM j_professeurs_matieres jpm, utilisateurs u WHERE jpm.id_professeur=u.login and id_matiere='$current_matiere' ORDER BY u.nom, u.prenom;";
		$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_profs)>0) {
			while($lig=mysqli_fetch_object($res_profs)) {
				$tab_profs_associes[]=$lig->login;
			}
		}

		if(count($tab_profs_associes)>0) {
			echo "<div style='float:right; width:16px;'><a href='../eleves/recherche.php?is_posted_recherche2b=y&amp;rech_matiere[]=".$current_matiere.add_token_in_url()."' title=\"Extraire la liste des professeurs (qu'ils soient associ??s ?? la mati??re dans des enseignements ou non).\" target='_blank'><img src='../images/group16.png' class='icone16' /></a></div>";
			if(count($tab_profs_associes)>1) {
				echo "<p class='bold'>Les professeurs associ??s sont&nbsp;<br />\n";
			}
			elseif(count($tab_profs_associes)==1) {
				echo "<p class='bold'>Un professeur est associ??&nbsp;<br />\n";
			}
			echo "<br />\n";
			echo "<table class='boireaus' style='margin-left: 1em;'>\n";
			$alt=1;
			for($loop=0;$loop<count($tab_profs_associes);$loop++) {
				$alt=$alt*(-1);
				//echo civ_nom_prenom($tab_profs_associes[$loop],"ini")."<br />\n";
				echo "<tr class='lig$alt white_hover'>\n";
				echo "<td>\n";
				echo civ_nom_prenom($tab_profs_associes[$loop],"ini");
				echo "</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}

	$cpt=0;
	$sql="SELECT DISTINCT u.login,u.nom,u.prenom,u.civilite FROM utilisateurs u WHERE u.statut='professeur' AND u.etat='actif' ORDER BY u.nom, u.prenom;";
	$res_profs=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res_profs)>0) {
		echo "<p class='bold'>Associer des professeurs&nbsp;:</p>\n";
		//$cpt=0;
		while($lig=mysqli_fetch_object($res_profs)) {
			echo "<input type='checkbox' name='login_prof[]' id='login_prof_$cpt' value='$lig->login' ";
			echo "onchange=\"checkbox_change($cpt)\" ";
			if(in_array($lig->login,$tab_profs_associes)) {echo "checked ";$temp_style=" style='font-weight:bold;'";} else {$temp_style="";}
			echo "/><label for='login_prof_$cpt'><span id='texte_login_prof_$cpt'$temp_style>".$lig->civilite." ".$lig->nom." ".mb_substr($lig->prenom,0,1).".</span></label><br />\n";
			$cpt++;
		}
	}

	echo "<script type='text/javascript'>
function checkbox_change(cpt) {
	if(document.getElementById('login_prof_'+cpt)) {
		if(document.getElementById('login_prof_'+cpt).checked) {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='bold';
		}
		else {
			document.getElementById('texte_login_prof_'+cpt).style.fontWeight='normal';
		}
	}
}
</script>\n";

?>
</div>

<table class='boireaus boireaus_alt'>
<tr>
<th>Nom de mati??re : </th>
<td>
<?php
if ((!isset($current_matiere))||($current_matiere=="")) {
    echo "<input type=text size='19' maxlength='19' name='reg_current_matiere' id='reg_current_matiere' onchange='changement()' /> (<span style='font-style: italic; font-size: small;'>19 caract??res maximum</span>)";
} else {
    echo "<input type='hidden' name='matiere_name' value=\"".$current_matiere."\" />".$current_matiere;
}
?>
</td></tr>
<tr>
<th>Nom complet : </th>
<td><input type='text' name='matiere_nom_complet' value="<?php echo $matiere_nom_complet;?>" onchange='changement()' /></td>
</tr>
<tr>
<th>Priorit?? d'affichage par d??faut</th>
<td>
<?php
echo "<select size='1' name='matiere_priorite' onchange='changement()' >\n";
$k = '0';
echo "<option value=0>0</option>\n";
$k='11';
$j = '1';
//while ($k < '51'){
while ($k < 110){
    echo "<option value=$k"; if ($matiere_priorite == $k) {echo " SELECTED";} echo ">$j</option>\n";
    $k++;
    $j = $k - 10;
}
echo "</select></td></tr>";

$sql="SELECT * FROM nomenclatures_valeurs WHERE type='matiere' AND nom='libelle_edition' ORDER BY valeur;";
$res_nomenclature=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_nomenclature)==0) {
	echo "
<tr>
	<th>Nomenclature</th>
	<td><a href='../gestion/admin_nomenclatures.php' ".insert_confirm_abandon()." title=\"Aucune nomenclature n'est enregistr??e dans Gepi.
L'import des nomenclatures est n??cessaire pour le Livret Scolaire Lyc??e.\">Aucune nomenclature</a></td>
</tr>";
}
else {
	echo "
<tr>
	<th title=\"La saisie des nomenclatures est n??cessaire pour le Livret Scolaire Lyc??e.\">Nomenclature</th>
	<td>
		<select name='code_matiere' onchange='changement()'>
			<option value=''>---</option>";

	while($lig_nomenclature=mysqli_fetch_object($res_nomenclature)) {
		$selected="";
		if($code_matiere==$lig_nomenclature->code) {
			$selected=" selected";
		}
		echo "
			<option value='".$lig_nomenclature->code."'$selected>".$lig_nomenclature->valeur."</option>";
	}

	echo "
		</select>
	</td>
</tr>";
}

?>
<tr>
<th>Cat??gorie par d??faut</th>
<td>
<?php
echo "<select size='1' name='matiere_categorie' onchange='changement()' >\n";
$get_cat = mysqli_query($GLOBALS["mysqli"], "SELECT id, nom_court FROM matieres_categories");
$test = mysqli_num_rows($get_cat);

if ($test == 0) {
    echo "<option disabled>Aucune cat??gorie d??finie</option>";
} else {
    while ($row = mysqli_fetch_array($get_cat,  MYSQLI_ASSOC)) {
        echo "<option value='".$row["id"]."'";
        if ($matiere_cat_id == $row["id"]) echo " SELECTED";
        echo ">".html_entity_decode($row["nom_court"])."</option>";
    }
}
echo "</select>";

//$tab_modalites=get_tab_modalites_election();

?>
	</td>
	</tr>

<?php
	if($code_matiere!='') {
?>
	<tr title="La mati??re est saisie comme option pour les ??l??ves dans Sconet.
	Cela permet lors de la mise ?? jour d'apr??s Sconet de ne pr??-inscrire l'??l??ve dans un enseignement de cette mati??re que si l'option a ??t?? saisie pour l'??l??ve dans Sconet.">
		<th><label for='option_sconet'>Option sconet&nbsp;:</label></th>
		<td>
			<input type='checkbox' name='option_sconet' id='option_sconet' value='y' <?php
				if(array_key_exists($code_matiere, $tab_options_sconet['code'])) {
					echo "checked ";
				}
			?>/>
		</td>
	</tr>
<?php
	}
?>
</table>
<p>
<label for='force_defaut' style='cursor: pointer;'><b>Pour toutes les classes, forcer la valeur de la priorit?? d'affichage ?? la valeur par d??faut ci-dessus :</b></label>
<input type="checkbox" name="force_defaut" id="force_defaut" onchange="changement()" checked />
</p>
<p>
<label for='force_defaut_categorie' style='cursor: pointer;'><b>Pour toutes les classes, forcer la valeur de la cat??gorie de mati??re ?? la valeur par d??faut ci-dessus :</b></label>
<input type="checkbox" name="force_defaut_categorie" id="force_defaut_categorie" onchange="changement()" checked />
</p>

<?php
	/*
	echo "<p><label for='force_modalite' style='cursor: pointer;'><b>Pour toutes les classes et enseignements de cette mati??re, forcer ?? </label>
	<select name='code_modalite_elect' onchange='changement()'>
		<option value=''></option>";
	for($loop_m=0;$loop_m<count($tab_modalites);$loop_m++) {
		echo "
		<option value='".$tab_modalites[$loop_m]["code_modalite_elect"]."' title=\"".$tab_modalites[$loop_m]["libelle_long"]."\">".$tab_modalites[$loop_m]["libelle_long"]."</option>";
	}
	echo "
	</select> la valeur de modalit?? d'??lection&nbsp:</b>
	<input type=\"checkbox\" name=\"force_modalite\" id=\"force_modalite\" onchange=\"changement()\" checked /><br />
	</p>";
	*/
?>

<input type="hidden" name="isposted" value="yes" />
</form>
<script type='text/javascript'>
	if(document.getElementById('reg_current_matiere')) {
		document.getElementById('reg_current_matiere').focus();
	}
</script>
<!-- ============================================================================ -->
<hr />

<?php
if((isset($current_matiere))&&($current_matiere!="")) {
	$sql="SELECT DISTINCT g.id, g.name, g.description FROM groupes g, j_groupes_matieres jgm, j_groupes_classes jgc, classes c WHERE jgm.id_matiere='".$current_matiere."' AND jgm.id_groupe=g.id AND jgc.id_groupe=g.id AND jgc.id_classe=c.id ORDER BY c.classe, c.nom_complet";
	//echo "$sql<br />";
	$res_ens=mysqli_query($GLOBALS["mysqli"], $sql);
	$nb_ens=mysqli_num_rows($res_ens);
	if($nb_ens==0) {
		echo "<p>Aucun enseignement n'est associ?? ?? la mati??re $current_matiere.</p>\n";
	}
	else {
		echo "<p>$nb_ens enseignement(s) associ??(s) ?? la mati??re $current_matiere&nbsp;: ";
		$chaine_domaines="";
		for($loop=0;$loop<count($tab_domaines);$loop++) {
			$chaine_domaines.="&amp;rech_domaine[]=".$tab_domaines[$loop];
		}
		echo "<a href='../eleves/recherche.php?is_posted_recherche2=y&amp;rech_matiere[]=".$current_matiere.$chaine_domaines.add_token_in_url()."' title=\"Extraire la liste des professeurs (associ??s aux enseignements ci-dessous).\" target='_blank'><img src='../images/group16.png' class='icone16' /></a>";

		if(acces("/matieres/associations_matieres_enseignements.php", $_SESSION['statut'])) {
			echo " <a href='associations_matieres_enseignements.php?matiere=".$current_matiere."' title=\"Modifier les associations mati??re/enseignements.\"><img src='../images/edit16.png' class='icone16' /></a>";
		}

		echo "<br />";
		while($lig_ens=mysqli_fetch_object($res_ens)) {

			$sql="SELECT c.id, c.classe FROM j_groupes_classes jgc, classes c WHERE jgc.id_classe=c.id AND jgc.id_groupe='$lig_ens->id' ORDER BY c.classe, c.nom_complet;";
			$res_clas=mysqli_query($GLOBALS["mysqli"], $sql);
			$chaine_clas="";
			if(mysqli_num_rows($res_clas)>0) {
				$cpt_clas=0;
				while($lig_clas=mysqli_fetch_object($res_clas)) {
					if($cpt_clas>0) {$chaine_clas.=", ";}
					$chaine_clas.="<a href='../groupes/edit_class.php?id_classe=$lig_clas->id' title=\"Acc??der ?? la liste des enseignements de $lig_clas->classe\">$lig_clas->classe</a>";
					$cpt_clas++;
				}
			}

			$sql="SELECT u.login, u.civilite, u.nom, u.prenom FROM utilisateurs u, j_groupes_professeurs jgp WHERE jgp.login=u.login AND jgp.id_groupe='$lig_ens->id' ORDER BY u.nom, u.prenom;";
			$res_prof=mysqli_query($GLOBALS["mysqli"], $sql);
			$chaine_prof="";
			if(mysqli_num_rows($res_prof)>0) {
				$cpt_prof=0;
				while($lig_prof=mysqli_fetch_object($res_prof)) {
					if($cpt_prof>0) {$chaine_prof.=", ";}
					$chaine_prof.="<a href='../utilisateurs/modify_user.php?user_login=$lig_prof->login' title=\"Modifier l'utilisateur $lig_prof->login\">$lig_prof->civilite $lig_prof->nom ".mb_substr($lig_prof->prenom,0,1)."</a>";
					$cpt_prof++;
				}
			}

			echo "<a href='../groupes/edit_group.php?id_groupe=$lig_ens->id' title=\"Modifier l'enseignement n??$lig_ens->id\">$lig_ens->name (<em>$lig_ens->description</em>)</a>";
			if($chaine_clas!="") {
				echo " en $chaine_clas";
			}
			if($chaine_prof!="") {
				echo " avec $chaine_prof";
			}
			echo "<br />";
		}
		echo "</p>\n";


		$sql="select distinct jeg.login from j_eleves_groupes jeg, j_groupes_matieres jgm, j_eleves_classes jec where jeg.id_groupe=jgm.id_groupe AND jec.login=jeg.login AND jgm.id_matiere='$current_matiere';";
		$res_ele=mysqli_query($GLOBALS["mysqli"], $sql);
		$eff_ele=mysqli_num_rows($res_ele);
		echo "<p style='margin-top:1em;'>$eff_ele ??l??ve(s) sui(ven)t un enseignement dans la mati??re $current_matiere.";
		if($eff_ele>0) {
			echo "<br /><a href='".$_SERVER['PHP_SELF']."?export_ele_csv=y&matiere=".$current_matiere.add_token_in_url()."' target='_blank'>Exporter la liste des ??l??ves en CSV</a>";
		}
		echo "</p>";
	}
	echo "<hr />\n";
}
?>

<p><b>Aide :</b></p>
<ul>
<li><b>Nom de mati??re</b>
<br /><br />Il s'agit de l'identifiant de la mati??re. Il est constitu?? au maximum de 20 caract??res : lettres, chiffres ou "_" et ne doit pas commencer par un chiffre.
Une fois enregistr??, il n'est plus possible de le modifier.
</li>
<li><b>Nom complet</b>
<br /><br />Il s'agit de l'intitul?? de la mati??re, tel qu'il appara??t aux utilisateurs sur les bulletins, les relev??s de notes, etc.
Une fois enregistr??, il est toujours possible de le modifier.
</li>
<li><b>Priorit?? d'affichage par d??faut</b>
<br /><br />Permet de d??finir l'ordre d'affichage par d??faut des mati??res dans le bulletin scolaire et dans les tableaux r??capitulatifs des moyennes.
<br /><b>Remarques :</b>
<ul>
<li>Lors de la gestion des mati??res dans une classe, c'est cette valeur qui est enregistr??e par d??faut. Il est alors possible de changer la valeur pour une classe donn??e.</li>
<li>Il est possible d'attribuer le m??me poids ?? plusieurs mati??res n'apparaissant pas sur un m??me bulletin. Par exemple, toutes les LV1 peuvent avoir le m??me poids, etc.</li>
<li>Si deux mati??res apparaissant sur un m??me bulletin ont la m??me priorit??, GEPI affiche la premi??re mati??re extraite de la base.</li>
</ul>
</ul>
<!--/li>
</ul-->
<?php require("../lib/footer.inc.php");?>
