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

// Resume session

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
    header("Location: ../utilisateurs/mon_compte.php?change_mdp=yes");
    die();
} else if ($resultat_session == '0') {
    header("Location: ../logout.php?auto=1");
    die();
}

//INSERT INTO `droits` VALUES ('/groupes/edit_class_grp_lot.php', 'V', 'F', 'F', 'F', 'F', 'F', 'Gestion des enseignements simples par lot.', '');
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}


$id_classe = isset($_GET['id_classe']) ? $_GET['id_classe'] : (isset($_POST['id_classe']) ? $_POST["id_classe"] : NULL);
if (!is_numeric($id_classe)) $id_classe = 0;
$classe = get_classe($id_classe);
$display = isset($_GET['display']) ? $_GET['display'] : (isset($_POST['display']) ? $_POST["display"] : NULL);
if ($display != "new") $display = "current";

//$tri_matiere=isset($_GET['tri_matiere']) ? $_GET['tri_matiere'] : (isset($_POST['tri_matiere']) ? $_POST["tri_matiere"] : "alpha");
$tri_matiere=isset($_GET['tri_matiere']) ? $_GET['tri_matiere'] : (isset($_POST['tri_matiere']) ? $_POST["tri_matiere"] : "priorite");

$msg="";

// =================================
// AJOUT: boireaus
$chaine_options_classes="";
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
}// =================================


if (isset($_POST['is_posted'])) {
	check_token();

	$checkmat=isset($_POST['checkmat']) ? $_POST['checkmat'] : array();
	//$id_grp=$_POST['id_grp'];
	$id_grp=isset($_POST['id_grp']) ? $_POST['id_grp'] : NULL;
	$prof=isset($_POST['prof']) ? $_POST['prof'] : array();;
	$id_matiere=isset($_POST['id_matiere']) ? $_POST['id_matiere'] : array();

	$msg.="<!--count(\$id_matiere)=".count($id_matiere)."-->\n";

	$nb_nouveaux_groupes=0;
	$nb_grp_maj=0;
	//for($i=0;$i<count($id_matiere);$i++){
	if(isset($_POST['compteur_matieres'])) {
		for($i=0;$i<$_POST['compteur_matieres'];$i++){
			unset($reg_clazz);
			if(isset($id_matiere[$i])) {
				$msg.="<!--\$id_matiere[$i]=".$id_matiere[$i]."-->\n";
				if($id_matiere[$i]!="") {
					if(isset($checkmat[$i])) {
						if($checkmat[$i]=="nouveau_groupe") {
							// C'est un nouveau groupe

							$msg.="<!--\$checkmat[$i]=nouveau_groupe-->\n";

							$sql="SELECT * FROM matieres WHERE matiere='$id_matiere[$i]'";
							$resultat_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
							$ligne_matiere=mysqli_fetch_object($resultat_matiere);

							$reg_clazz[0]=$id_classe;

							//$create = create_group($reg_nom_groupe, $reg_nom_complet, $reg_matiere, $reg_clazz);
							//echo "<!-- create_group($id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz); -->\n";
							$create = create_group($id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz);
							if (!$create) {
								//echo "<!-- erreur -->\n";
								$msg .= "Erreur lors de la cr??ation du groupe $id_matiere[$i]";
							}
							else {
								$nb_nouveaux_groupes++;

								$id_groupe=$create;
								if((isset($prof[$i]))&&($prof[$i]!='')) {
									$sql="INSERT INTO j_groupes_professeurs VALUES('$id_groupe','$prof[$i]','')";
									$resultat_prof=mysqli_query($GLOBALS["mysqli"], $sql);
								}

								// Affectation de tous les ??l??ves de la classe dans le groupe:
								$current_group = get_group($id_groupe);
								$reg_professeurs = (array)$current_group["profs"]["list"];
								unset($reg_eleves);
								$reg_eleves = array();

								$tab_eleves_groupe_toutes_periodes=array();
								$sql="SELECT * FROM periodes WHERE id_classe='$id_classe'";
								$result_list_periodes=mysqli_query($GLOBALS["mysqli"], $sql);
								while($ligne_periode=mysqli_fetch_object($result_list_periodes)) {
									//echo "<!-- \$ligne_periode->num_periode=$ligne_periode->num_periode -->\n";
									//echo "\$ligne_periode->num_periode=$ligne_periode->num_periode <br />\n";
									$reg_eleves[$ligne_periode->num_periode]=array();
									//$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' ORDER BY periode,login";
									$sql="SELECT DISTINCT login FROM j_eleves_classes WHERE id_classe='$id_classe' AND periode='$ligne_periode->num_periode' ORDER BY periode,login";
									$result_list_eleves=mysqli_query($GLOBALS["mysqli"], $sql);
									while($ligne_eleve=mysqli_fetch_object($result_list_eleves)){
										$reg_eleves[$ligne_periode->num_periode][]=$ligne_eleve->login;
										//echo "<!-- \$ligne_eleve->login=$ligne_eleve->login -->\n";

										if(!in_array($ligne_eleve->login, $tab_eleves_groupe_toutes_periodes)) {
											$tab_eleves_groupe_toutes_periodes[]=$ligne_eleve->login;
										}
									}
								}

								$code_modalite_elect_eleves=array();
								for($loop=0;$loop<count($tab_eleves_groupe_toutes_periodes);$loop++) {
									$sql="SELECT code_modalite_elect FROM sconet_ele_options seo, eleves e WHERE seo.ele_id=e.ele_id AND e.login='".$tab_eleves_groupe_toutes_periodes[$loop]."' AND seo.code_matiere='".$current_group["matiere"]["code_matiere"]."';";
									$res_cme=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($res_cme)>0) {
										$lig_cme=mysqli_fetch_object($res_cme);
										$code_modalite_elect_eleves[$lig_cme->code_modalite_elect]["eleves"][]=$tab_eleves_groupe_toutes_periodes[$loop];
									}
								}

								$create = update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, $reg_professeurs, $reg_eleves, $code_modalite_elect_eleves);
								if (!$create) {
									$msg .= "Erreur lors de la mise ?? jour du groupe $id_matiere[$i]";
								}
								//else {
								//	$msg .= "Le groupe a bien ??t?? mis ?? jour.";
								//}
							}
						}
						elseif($checkmat[$i]!="") {
							// Mise ?? jour du groupe $id_groupe=$checkmat[$i]
							$id_groupe=$checkmat[$i];
							//echo "\$id_groupe=$id_groupe<br />\n";
							$group=get_group($id_groupe);

							$sql="SELECT * FROM matieres WHERE matiere='$id_matiere[$i]'";
							$resultat_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
							$ligne_matiere=mysqli_fetch_object($resultat_matiere);

							$reg_clazz[0]=$id_classe;

							if(isset($group["profs"]["list"])){
								$tabprof=$group["profs"]["list"];
							}
							else{
								$tabprof=array();
							}
							if(isset($group["eleves"]["list"])){
								$tabele=$group["eleves"]["list"];
							}
							else{
								$tabele=array();
							}
							$tab_modalites=$group["modalites"];

							$create = update_group($id_groupe, $id_matiere[$i], $ligne_matiere->nom_complet, $id_matiere[$i], $reg_clazz, $tabprof, $tabele,$tab_modalites);

							if (!$create) {
								$msg .= "Erreur lors de la mise ?? jour du groupe $id_matiere[$i]";
							}
							else{
								if((!isset($prof[$i]))||($prof[$i]=="")) {
									$sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='$id_groupe'";
									$resultat_suppr_prof=mysqli_query($GLOBALS["mysqli"], $sql);
								}
								else{
									$sql="SELECT * FROM j_groupes_professeurs WHERE id_groupe='$id_groupe' AND login='$prof[$i]'";
									$resultat_verif_prof=mysqli_query($GLOBALS["mysqli"], $sql);
									if(mysqli_num_rows($resultat_verif_prof)==0){
										// On supprime le professeur pr??c??demment affect?? s'il y en avait un pour mettre le nouveau:
										$sql="DELETE FROM j_groupes_professeurs WHERE id_groupe='$id_groupe'";
										$resultat_suppr_prof=mysqli_query($GLOBALS["mysqli"], $sql);

										$sql="INSERT INTO j_groupes_professeurs VALUES('$id_groupe','$prof[$i]','')";
										$resultat_prof=mysqli_query($GLOBALS["mysqli"], $sql);
										$nb_grp_maj++;
									}
									else{
										// Le prof est d??j?? affect?? au groupe.
									}
								}
							}
						}
					}
					else {
						// On supprime le groupe:
						//$id_groupe=$checkmat[$i];
						$id_groupe=$id_grp[$i];
						if($id_groupe!="") {
							//echo "Suppression... \$id_groupe=$id_groupe<br />";
							if(test_before_group_deletion($id_groupe)) {
								if(!delete_group($id_groupe)){
									$msg.="Erreur lors de la suppression du groupe.<br />";
								}
								else {
									$msg.="Groupe n??$id_groupe supprim??.<br />";
								}
							}
							else{
								$msg.="Des notes sons saisies pour ce groupe. La suppression du groupe n??$id_groupe n'est pas possible.<br />";
							}
						}
					}
				}
			}
		}
	}

	if($nb_nouveaux_groupes>0) {
		$msg.="$nb_nouveaux_groupes enseignement(s) ajout??(s).<br />";
	}
	if($nb_grp_maj>0) {
		$msg.="$nb_grp_maj enseignement(s) mis ?? jour.<br />";
	}

	if($msg=="") {
		$msg="Aucune modification n'a ??t?? propos??e.<br />";
	}
}

$javascript_specifique[] = "lib/tablekit";
$utilisation_tablekit="ok";

$themessage  = 'Des informations ont ??t?? modifi??es. Voulez-vous vraiment quitter sans enregistrer ?';
//**************** EN-TETE **************************************
//$titre_page = "Gestion des groupes";
$titre_page = "Gestion des enseignements 'simples' par lot";
require_once("../lib/header.inc.php");
//**************** FIN EN-TETE **********************************

//debug_var();

echo "<form action='".$_SERVER['PHP_SELF']."' name='form1' method='post'>\n";
echo "<p class='bold'>\n";
echo "<a href='../classes/index.php'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
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
// AJOUT: boireaus 20081224
$titre="Navigation";
$texte="";
$texte.="<img src='../images/icons/date.png' alt='' /> <a href='../classes/periodes.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">P??riodes</a><br />";
include "../lib/periodes.inc.php";
if($nb_periode>1) {
	// On a $nb_periode = Nombre de p??riodes + 1
	$texte.="<img src='../images/icons/edit_user.png' alt='' /> <a href='../classes/classes_const.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">??l??ves</a><br />";
}
$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Enseignements</a><br />";
//$texte.="<img src='../images/icons/document.png' alt='' /> <a href='../groupes/edit_class_grp_lot.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">config.simplifi??e</a><br />";
$texte.="<img src='../images/icons/configure.png' alt='' /> <a href='../classes/modify_nom_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">Param??tres</a>";

$ouvrir_infobulle_nav=getSettingValue("ouvrir_infobulle_nav");

if($ouvrir_infobulle_nav=="y") {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/vert.png' width='16' height='16' /></a></div>\n";
}
else {
	$texte.="<div id='save_mode_nav' style='float:right; width:20px; height:20px;'><a href='#' onclick='modif_mode_infobulle_nav();return false;'><img src='../images/rouge.png' width='16' height='16' /></a></div>\n";
}

$texte.="<script type='text/javascript'>
	// <![CDATA[
	function modif_mode_infobulle_nav() {
		new Ajax.Updater($('save_mode_nav'),'../classes/classes_ajax_lib.php?mode=ouvrir_infobulle_nav',{method: 'get'});
	}
	//]]>
</script>\n";

$tabdiv_infobulle[]=creer_div_infobulle('navigation_classe',$titre,"",$texte,"",14,0,'y','y','n','n');

echo " | <a href='#' onclick=\"afficher_div('navigation_classe','y',-100,20);\"";
echo ">";
echo "Navigation";
echo "</a>";
//=========================

echo " | <a href='../init_xml2/init_alternatif.php?cat=classes' onclick=\"return confirm_abandon (this, change, '$themessage')\">Cr??ation par lots</a>";

echo "</p>\n";
echo "</form>\n";

?>



<?php
echo "<h3>Gestion des enseignements simples pour la classe :" . $classe["classe"]."</h3>\n";

echo "<p>Ne doivent ??tre saisis ici que les enseignements ne concernant qu'une classe (<i>pas les regroupements</i>) et un seul professeur par mati??re.</p>\n";


echo "<script language='javascript' type='text/javascript'>
	function test_prof(nb){
		// On ne d??coche pas le fait de ne pas mettre de prof...
		// ... il peut arriver en d??but d'ann??e qu'un prof ne soit pas nomm??...
		if(document.getElementById('prof_'+nb).selectedIndex!=0){
			document.getElementById('checkmat_'+nb).checked='true';
		}
	}
</script>\n";

// On peut basculer entre deux modes de saisie : seulement les groupes d??j?? associ??s, ou bien nouveaux groupes
if ($display == "current") {
	echo "<p><img src='../images/icons/add.png' alt='' class='back_link' /> <a href='edit_class_grp_lot.php?id_classe=".$id_classe."&amp;display=new' onclick=\"return confirm_abandon (this, change, '$themessage')\">Ajouter de nouveaux groupes</a></p>";
} else {
	echo "<p><img src='../images/icons/configure.png' alt='' class='back_link' /> <a href='edit_class_grp_lot.php?id_classe=".$id_classe."&amp;display=current' onclick=\"return confirm_abandon (this, change, '$themessage')\">Editer les groupes existants</a></p>";
}

if($tri_matiere=='alpha') {
	echo "<p style='font-size:x-small;'>Les mati??res sont tri??es par ordre alphab??tique.<br /><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;display=$display&amp;tri_matiere=priorite'>Trier les mati??res par priorit??</a></p>\n";
}
else {
	echo "<p style='font-size:x-small;'>Les mati??res sont tri??es par priorit??.<br /><a href='".$_SERVER['PHP_SELF']."?id_classe=$id_classe&amp;display=$display&amp;tri_matiere=alpha'>Trier les mati??res par ordre alphab??tique</a></p>\n";
}

//echo "<form enctype='multipart/form-data' action='add_group.php' name='new_group' method='get'>";
echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' name='new_groups' method='post'>\n";
echo add_token_field();
echo "<input type='hidden' name='tri_matiere' value='$tri_matiere' />\n";

echo "<table border='0' class='boireaus resizable sortable' summary='Tableau des mati??res'>\n";
echo "<tr valign='top'>";
echo "<th>&nbsp;</th>\n";
echo "<th style='font-weight: bold;' class='text' title=\"Trier dans l'ordre alphab??tique\">Mati??re</th>\n";
echo "<th style='font-weight: bold;' class='number' title=\"Trier dans l'ordre de priorit?? d??fini dans la page de Gestion des mati??res\">Priorit??</th>\n";
echo "<th style='font-weight: bold;'>Professeur</th>\n";
echo "</tr>\n";
if($tri_matiere=='alpha') {
	//$result_matiere=mysql_query("SELECT matiere, nom_complet FROM matieres ORDER BY matiere");
	$result_matiere=mysqli_query($GLOBALS["mysqli"], "SELECT matiere, nom_complet, priority FROM matieres ORDER BY nom_complet, matiere");
}
else {
	$result_matiere=mysqli_query($GLOBALS["mysqli"], "SELECT matiere, nom_complet, priority FROM matieres ORDER BY priority");
}
$nb_mat=mysqli_num_rows($result_matiere);
$cpt=0;
$alt=1;
while($ligne_matiere=mysqli_fetch_object($result_matiere)){
	$groupe_existant="non";

	$alt=$alt*(-1);

	$display_current = false;
	// R??cup??ration des infos d??j?? saisies:
	$sql="SELECT jgm.id_groupe FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_classe='$id_classe' AND jgm.id_matiere='$ligne_matiere->matiere' AND jgc.id_groupe=jgm.id_groupe";
	$result_grp=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($result_grp)==0 and $display == "new") {
		$display_current = true;
		echo "<tr class='lig$alt'>\n";
		echo "<td>\n";
		echo "<input type='hidden' name='id_matiere[$cpt]' value='$ligne_matiere->matiere' />\n";
		echo "<input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='nouveau_groupe' onchange='changement()' />\n";
		//echo "<input type='hidden' name='id_matiere[]' value='$ligne_matiere->matiere' />\n";
		//echo "<input type='checkbox' name='checkmat[]' id='checkmat_".$cpt."' value='nouveau_groupe' />\n";
		//echo "<input type='hidden' name='id_grp[$cpt]' value='' />\n";
		echo "</td>\n";
	}
	elseif(mysqli_num_rows($result_grp)==1 and $display == "current") {
		$display_current = true;
		echo "<tr class='lig$alt'>\n";
		$ligne_grp=mysqli_fetch_object($result_grp);

		$sql="SELECT * FROM j_groupes_professeurs WHERE id_groupe='$ligne_grp->id_groupe'";
		$result_verif_grp_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($result_verif_grp_prof)>1){
			//echo "<td colspan='3'>Le groupe associ?? ?? la mati??re $ligne_matiere->matiere pour cette classe a plusieurs professeurs d??finis.<br />Ce n'est pas un enseignement 'simple'.<br />A traiter ailleurs...</td>\n";

			echo "<td>&nbsp;</td>\n";

			//echo "<td colspan='2' style='text-align:left;'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs professeurs</i>), accessible par <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">G??rer les enseignements</a>.</td>\n";
			echo "<td colspan='3' style='text-align:left;'>$ligne_matiere->nom_complet: <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">g??r?? ici</a> (<i>autres professeurs impliqu??s</i>)</td>\n";
			$groupe_existant="trop";
		}
		else {
			$sql="SELECT * FROM j_groupes_classes jgc, j_groupes_matieres jgm WHERE jgc.id_groupe='$ligne_grp->id_groupe' AND jgm.id_matiere='$ligne_matiere->matiere' AND jgc.id_groupe=jgm.id_groupe";
			//echo "<td>$sql</td>\n";
			$result_verif_grp_classes=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($result_verif_grp_classes)==1) {
				echo "<td>\n";
				echo "<input type='hidden' name='id_matiere[$cpt]' value='$ligne_matiere->matiere' />\n";
				echo "<input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='$ligne_grp->id_groupe' onchange='changement()' checked />\n";
				echo "<input type='hidden' name='id_grp[$cpt]' value='$ligne_grp->id_groupe' />\n";
				echo "</td>\n";
				$groupe_existant="oui";
			}
			else {
				//echo "<td colspan='3'>Le groupe associ?? ?? la mati??re $ligne_matiere->matiere est associ?? ?? plusieurs classes.<br />Ce n'est pas un enseignement 'simple'.<br />A traiter ailleurs...</td>\n";

				echo "<td>&nbsp;</td>\n";

				//echo "<td colspan='2' style='text-align:left;'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs classes</i>), accessible par <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">G??rer les enseignements</a>.</td>\n";
				echo "<td colspan='3' style='text-align:left;'>$ligne_matiere->nom_complet: <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">g??r?? ici</a> (<i>autres classes impliqu??es</i>)</td>\n";

				$groupe_existant="trop";
			}
		}
	}
	elseif(mysqli_num_rows($result_grp)>1 and $display == "current") {
		$display_current = true;
		echo "<tr class='lig$alt'>\n";
		// C'est le bazar... plusieurs groupes existent pour cette mati??re dans cette classe
		//echo "<td colspan='3'>La mati??re $ligne_matiere->matiere a plusieurs groupes d??finis pour cette classe.<br />Ce n'est pas un enseignement 'simple'.<br />Elle devra ??tre trait??e ailleurs...</td>\n";

		echo "<td>&nbsp;</td>\n";

		//echo "<td colspan='2' style='text-align:left;'>$ligne_matiere->matiere: groupe complexe (<i>plusieurs classes</i>), accessible par <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">G??rer les enseignements</a>.</td>\n";
		echo "<td colspan='3' style='text-align:left;'>$ligne_matiere->nom_complet: <a href='edit_class.php?id_classe=$id_classe' onclick=\"return confirm_abandon (this, change, '$themessage')\">g??r?? ici</a> (<i>autres classes et plusieurs professeurs impliqu??s</i>)</td>\n";

		$groupe_existant="trop";
	}

	//echo "<td><input type='checkbox' name='checkmat[$cpt]' id='checkmat_".$cpt."' value='coche' /></td>\n";
	if($groupe_existant!="trop" and $display_current) {
		echo "<td style='text-align:left;'>\n";
		echo "<span style='display:none'>".htmlspecialchars($ligne_matiere->nom_complet)."</span>";
		echo "<label for='checkmat_".$cpt."' style='cursor:pointer;'>";
		echo htmlspecialchars($ligne_matiere->nom_complet);
		echo "</label>\n";
		echo "</td>\n";
		echo "<td>$ligne_matiere->priority</td>\n";
		//$sql="SELECT jpm.id_professeur,u.nom,u.prenom,u.civilite FROM j_professeurs_matieres jpm, matieres m, utilisateurs u WHERE jpm.id_matiere=m.matiere AND m.matiere='$ligne_matiere->matiere' AND u.login=jpm.id_professeur ORDER BY jpm.id_professeur";
		$sql="SELECT jpm.id_professeur,u.nom,u.prenom,u.civilite FROM j_professeurs_matieres jpm, matieres m, utilisateurs u WHERE jpm.id_matiere=m.matiere AND m.matiere='$ligne_matiere->matiere' AND u.login=jpm.id_professeur AND u.etat='actif' ORDER BY jpm.id_professeur";
		$result_prof=mysqli_query($GLOBALS["mysqli"], $sql);
		echo "<td style='text-align:left;'>\n";
		echo "<select name='prof[$cpt]' id='prof_".$cpt."' onchange='test_prof($cpt);changement();'>\n";
		echo "<option value=''>---</option>\n";
		$selected="";
		while($ligne_prof=mysqli_fetch_object($result_prof)) {
			if($groupe_existant=="oui"){
				$sql="SELECT * FROM j_groupes_professeurs jgp WHERE jgp.id_groupe='$ligne_grp->id_groupe' AND jgp.login='$ligne_prof->id_professeur'";
				$result_verif=mysqli_query($GLOBALS["mysqli"], $sql);
				if(mysqli_num_rows($result_verif)==0) {
					$selected="";
				}
				else{
					$selected=" selected";
				}
			}
			echo "<option value='$ligne_prof->id_professeur'$selected>".casse_mot($ligne_prof->prenom,'majf2')." ".my_strtoupper($ligne_prof->nom)."</option>\n";
		}
		echo "</select>\n";
		echo "</td>\n";
		echo "</tr>\n";
	}
	$cpt++;
}
echo "</table>\n";


echo "<input type='hidden' name='compteur_matieres' value='$cpt' />\n";

echo "<input type='hidden' name='mode' value='groupe' />\n";
echo "<input type='hidden' name='id_classe' value='" . $id_classe . "' />\n";
echo "<input type='hidden' name='is_posted' value='oui' />\n";
echo "<p><input type='submit' value='Valider' /></p>\n";
echo "</form>\n";

/*
//$groups = get_groups_for_class($id_classe);
$groups = get_groups_for_class($id_classe,"","n");
foreach ($groups as $group) {
	//$group["description"]
	$current_group = get_group($group["id"]);

}
foreach($current_group["profs"]["list"] as $prof) {
	if (!$first) echo ", ";
	echo $current_group["profs"]["users"][$prof]["prenom"];
	echo " ";
	echo $current_group["profs"]["users"][$prof]["nom"];
	$first = false;
}
*/

if($ouvrir_infobulle_nav=='y') {
	echo "<script type='text/javascript'>
	setTimeout(\"afficher_div('navigation_classe','y',-100,20);\",1000)
</script>\n";
}

require("../lib/footer.inc.php");

?>
