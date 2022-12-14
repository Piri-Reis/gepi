<?php
/**
 * Plan de classe
 * 
 * $Id$
 *
 * @copyright Copyright 2001, 2012 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun, Stephane Boireau
 * 
 * @package Trombinoscopes
 * @subpackage Conteneur
 * @license GNU/GPL 
 * @see check_token()
 * @see checkAccess()
 * @see get_groups_for_prof()
 * @see getSettingValue()
 * @see Session::security_check()
 */

/* This file is part of GEPI.
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

/**
 * Fichiers d'initialisation
 */
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

$sql="SELECT 1=1 FROM droits WHERE id='/mod_trombinoscopes/plan_de_classe.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/mod_trombinoscopes/plan_de_classe.php',
	administrateur='F',
	professeur='V',
	cpe='F',
	scolarite='F',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Plan de classe',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
	header("Location: ../logout.php?auto=1");
	die();
}

//On vérifie si le module est activé
if (getSettingValue("active_module_trombinoscopes")!='y') {
	die("Le module n'est pas activé.");
}

$id_groupe=isset($_POST['id_groupe']) ? $_POST['id_groupe'] : (isset($_GET['id_groupe']) ? $_GET['id_groupe'] : NULL);

$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_groupe INT(11) NOT NULL ,
login_prof VARCHAR(50) NOT NULL ,
dim_photo INT(11) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

$sql="CREATE TABLE IF NOT EXISTS t_plan_de_classe_ele (
id INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
id_plan INT( 11 ) NOT NULL,
login_ele VARCHAR(50) NOT NULL ,
x INT(11) NOT NULL ,
y INT(11) NOT NULL) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";
$create_table=mysqli_query($GLOBALS["mysqli"], $sql);

if((isset($_POST['copier_plan_classe']))&&(isset($_POST['id_groupe']))&&(preg_match("/^[0-9]{1,}$/", $_POST['id_groupe']))&&(isset($_POST['id_groupe_modele']))&&(preg_match("/^[0-9]{1,}$/", $_POST['id_groupe_modele']))) {
	check_token();

	$sql="SELECT * FROM t_plan_de_classe tpc, t_plan_de_classe_ele tpce WHERE tpc.id_groupe='".$_POST['id_groupe_modele']."' AND tpc.login_prof='".$_SESSION['login']."' AND tpc.id=tpce.id_plan;";
	//echo "$sql<br />";
	$res=mysqli_query($GLOBALS["mysqli"], $sql);
	if(mysqli_num_rows($res)>0) {
		$lig=mysqli_fetch_object($res);

		$sql="DELETE FROM t_plan_de_classe WHERE id_groupe='".$_POST['id_groupe']."';";
		//echo "$sql<br />";
		$del=mysqli_query($GLOBALS["mysqli"], $sql);

		$sql="INSERT INTO t_plan_de_classe SET id_groupe='".$_POST['id_groupe']."', login_prof='".$_SESSION['login']."', dim_photo='".$lig->dim_photo."';";
		//echo "$sql<br />";
		$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		if($insert) {
			$id_plan=mysqli_insert_id($GLOBALS['mysqli']);
			//echo "\$id_plan=$id_plan<br />";
			//if(preg_match("/^[0-9]{1,}$/", $id_plan)) {
				$current_group=get_group($_POST['id_groupe'], array("eleves"));
				/*
				echo "<pre>";
				print_r($current_group['eleves']['all']['list']);
				echo "</pre>";
				*/

				$cpt_ele=0;
				if(in_array($lig->login_ele, $current_group['eleves']['all']['list'])) {
					$sql="INSERT INTO t_plan_de_classe_ele SET id_plan='".$id_plan."', login_ele='".$lig->login_ele."', x='".$lig->x."', y='".$lig->y."';";
					//echo "$sql<br />";
					$insert=mysqli_query($GLOBALS["mysqli"], $sql);
					if($insert) {
						$cpt_ele++;
					}
				}


				while($lig=mysqli_fetch_object($res)) {
					if(in_array($lig->login_ele, $current_group['eleves']['all']['list'])) {
						$sql="INSERT INTO t_plan_de_classe_ele SET id_plan='".$id_plan."', login_ele='".$lig->login_ele."', x='".$lig->x."', y='".$lig->y."';";
						//echo "$sql<br />";
						$insert=mysqli_query($GLOBALS["mysqli"], $sql);
						if($insert) {
							$cpt_ele++;
						}
					}
				}

				echo "<span title=\"Plan de classe copié pour $cpt_ele élèves.\"><img src='../images/enabled.png' class='icone16' alt='Succès' /> $cpt_ele</span>";
			/*
			}
			else {
				echo "<img src='../images/icons/disabled.png' class='icone16' alt='Erreur' title=\"Erreur lors de la récupération du nouvel identifiant de plan de classe.\" />";
			}
			*/
		}
		else {
			echo "<img src='../images/disabled.png' class='icone16' alt='Erreur' title=\"Erreur lors de la définition des paramètres, dimensions.\" />";
		}
	}
	else {
		echo "<img src='../images/disabled.png' class='icone16' alt='Erreur' title=\"Erreur : Le modèle n'a pas été trouvé.\" />";
	}

	die();
}

// On ne va afficher l'entête que pour le choix du groupe, pas sur la partie réalisation du plan de classe
if((!isset($id_groupe))||($id_groupe=="")) {
	$javascript_specifique[] = "lib/tablekit";
	$utilisation_tablekit="ok";

	//**************** EN-TETE *****************
	$titre_page = "Plan de classe";
	/**
	* Entête de la page
	*/
	require_once("../lib/header.inc.php");
	//**************** FIN EN-TETE *************
	
	echo "<p class='bold'>\n";
	echo "<a href=\"trombinoscopes.php\"><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour </a> \n";
	echo "</p>\n";

	$tab_groups = get_groups_for_prof($_SESSION['login'],"classe puis matière");

	echo "<form action='".$_SERVER['PHP_SELF']."' method='post' name='form_choix_grp'>\n";
	echo add_token_field(true);
	echo "<p>Choisissez l'enseignement pour lequel vous souhaitez réaliser le plan de classe&nbsp;:</p>\n";

	echo "<table class='boireaus boireaus_alt resizable sortable'>\n";
	echo "<tr>\n";
	echo "<th class='nosort'>Choix</th>\n";
	echo "<th class='number'>Id</th>\n";
	echo "<th class='text'>Enseignement</th>\n";
	echo "<th class='text'>Classes</th>\n";
	echo "<th class='nosort'>Dimension<br />des photos</th>\n";
	echo "<th class='nosort'>Copier</th>\n";
	echo "<th class='nosort'>Coller</th>\n";
	echo "<th class='nosort'>Afficher</th>\n";
	echo "</tr>\n";
	for($loop=0;$loop<count($tab_groups);$loop++) {
		echo "<tr class='white_hover'>\n";
		echo "<td>\n";
		echo "<input type='radio' name='id_groupe' id='id_groupe_".$tab_groups[$loop]['id']."' value='".$tab_groups[$loop]['id']."' ";
		if($loop==0) {echo "checked ";}
		echo "/>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo $tab_groups[$loop]['id'];
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='id_groupe_".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['name']." (".$tab_groups[$loop]['description'].")</label>\n";
		echo "</td>\n";

		echo "<td>\n";
		echo "<label for='id_groupe_".$tab_groups[$loop]['id']."'>".$tab_groups[$loop]['classlist_string']."</label>\n";
		echo "</td>\n";

		echo "<td>\n";
		$dim_photo=100;
		$sql="SELECT dim_photo FROM t_plan_de_classe WHERE id_groupe='".$tab_groups[$loop]['id']."' AND login_prof='".$_SESSION['login']."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			$dim_photo=old_mysql_result($res,0);
		}
		echo "<input type='text' name='dim_photo_".$tab_groups[$loop]['id']."' value='$dim_photo' size='3' />";
		echo "</td>\n";


		echo "<td>\n";
		$sql="SELECT * FROM t_plan_de_classe tpc, t_plan_de_classe_ele tpce WHERE tpc.id_groupe='".$tab_groups[$loop]['id']."' AND tpc.login_prof='".$_SESSION['login']."' AND tpc.id=tpce.id_plan LIMIT 1;";
		//echo "$sql<br />";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)>0) {
			echo "<span title=\"Prendre ce plan de classe comme modèle pour coller dans un autre groupe.\">";
			echo "<input type='radio' name='id_groupe_modele' id='id_groupe_modele_".$tab_groups[$loop]['id']."' value='".$tab_groups[$loop]['id']."' />";
			echo "<label for='id_groupe_modele_".$tab_groups[$loop]['id']."'><img src='../images/icons/copy-16.png' class='icone16' alt='Copier' /></label>";
			echo "</span>";
		}
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='#' onclick='coller_plan_classe(".$tab_groups[$loop]['id'].");return false;'><img src='../images/icons/coller_23x24.png' width='23' height='24' alt='Coller' /></a><span id='span_resultat_collage_".$tab_groups[$loop]['id']."'></span>";
		echo "</td>\n";

		echo "<td>\n";
		echo "<a href='#' onclick=\"document.getElementById('id_groupe_".$tab_groups[$loop]['id']."').checked=true;document.form_choix_grp.submit();return false;\" title=\"Voir le plan de cette classe/groupe\"><img src='../images/icons/trombinoscope.png' width='16' height='16' alt='Voir' /></a>";
		echo "</td>\n";

		echo "</tr>\n";
	}
	echo "</table>\n";

	$trombi_plan_titre=getPref($_SESSION['login'], 'trombi_plan_titre', 'login');
	echo "<p>En titre au-dessus des photos, faire apparaître&nbsp;:<br />";
	echo "<input type='radio' name='trombi_plan_titre' id='trombi_plan_titre_login' value='login' ";
	if($trombi_plan_titre=='login') {echo "checked ";}
	echo "/><label for='trombi_plan_titre_login'>le login</label><br />\n";

	echo "<input type='radio' name='trombi_plan_titre' id='trombi_plan_titre_nom' value='nom' ";
	if($trombi_plan_titre=='nom') {echo "checked ";}
	echo "/><label for='trombi_plan_titre_nom'>le nom</label><br />\n";

	echo "<input type='radio' name='trombi_plan_titre' id='trombi_plan_titre_prenom' value='prenom' ";
	if($trombi_plan_titre=='prenom') {echo "checked ";}
	echo "/><label for='trombi_plan_titre_prenom'>le prénom</label><br />\n";
	echo "de l'élève.</p>\n";
	echo "<p><input type='submit' name='Valider' value='Valider' /></p>\n";
	echo "</form>\n";

	echo "<p><br /></p>\n";
	//echo "<p style='margin-left:4em; text-indent:-4em;'><em>NOTES&nbsp;:</em> Dans la page qui va s'ouvrir, vous pourrez déplacer les photos en cliquant sur l'entête de la photo (<em>la ligne de titre qui contient le nom ou le prénom,...</em>).<br />Vous pourrez une fois les photos positionnées, enregistrer cette position.";
	echo "<p style='margin-left:4em; text-indent:-4em;'><em>NOTES&nbsp;:</em> Dans la page qui va s'ouvrir, vous pourrez déplacer les photos en cliquer/maintenir_cliqué_glisser/déposer.<br />Vous pourrez une fois les photos positionnées selon votre convenance, enregistrer cette position.";
	if ((getSettingValue("active_module_absence_professeur")=='y')&&(getSettingValue("active_module_absence")=='2')) {
		echo "<br />Vous pourrez aussi effectuer la saisie des absences sur le plan de classe.<br />Dans la page de saisie des absences pour un groupe (<em>Onglet Saisir groupe</em>), vous trouverez la page de saisie sur le plan de classe (<em>si le plan de classe existe</em>) en suivant le lien sur l'icone <img src='../images/icons/trombino.png' width='20' height='20' alt='Icone trombi' />.";
	}
	echo "<br />Si un ou des élèves apparait comme manquant dans la liste, vérifiez si la date de sortie n'est pas renseignée <em>(cas d'élèves partis de l'établissement, puis revenus)</em>.<br />";
	echo "</p>\n";

	echo "<script type='text/javascript'>
	function coller_plan_classe(id_groupe) {
		csrf_alea=document.getElementById('csrf_alea').value;

		//alert('id_groupe='+id_groupe);

		id_groupe_modele='';
		champ_id_groupe_modele=document.form_choix_grp.id_groupe_modele;

		for (var i=0; i<champ_id_groupe_modele.length;i++) {
			//alert('champ_id_groupe_modele['+i+'].value='+champ_id_groupe_modele[i].value);
			if (champ_id_groupe_modele[i].checked) {
				id_groupe_modele=champ_id_groupe_modele[i].value;
				break;
			}
		}

		//alert('id_groupe_modele='+id_groupe_modele);

		if(id_groupe_modele!='') {
			//new Ajax.Updater($('span_regroupement_edt_associe'),'maj_inscript_ele_d_apres_edt.php?id_groupe=$id_groupe&action=editer_ec3&valider_ec3=y&id_nom_edt='+,{method: 'get'});

			new Ajax.Updater($('span_resultat_collage_'+id_groupe),'".$_SERVER['PHP_SELF']."',{method: 'post',
			parameters: {
				copier_plan_classe: 'y',
				id_groupe: id_groupe,
				id_groupe_modele: id_groupe_modele,
				csrf_alea: csrf_alea
			}});
		}
	}
</script>";

	require("../lib/footer.inc.php");
	die();
}

//======================================
/**
* Entête de la page
*/
require_once("../lib/header.inc.php");
//======================================

//debug_var();

$dim_photo=isset($_POST['dim_photo_'.$id_groupe]) ? $_POST['dim_photo_'.$id_groupe] : (isset($_GET['dim_photo_'.$id_groupe]) ? $_GET['dim_photo_'.$id_groupe] : 100);

$dim_photo=preg_replace('/[^0-9]/','',$dim_photo);
if(($dim_photo=="")||($dim_photo==0)) {$dim_photo=100;}

if((isset($_POST['trombi_plan_titre']))&&(in_array($_POST['trombi_plan_titre'],array('login', 'nom', 'prenom')))) {
	savePref($_SESSION['login'] ,"trombi_plan_titre", $_POST['trombi_plan_titre']);
}
$trombi_plan_titre=getPref($_SESSION['login'], 'trombi_plan_titre', 'login');

$sql="SELECT * FROM t_plan_de_classe WHERE id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)>0) {
	$lig=mysqli_fetch_object($res);

	$id_plan=$lig->id;

	$tmp_dim_photo=$lig->dim_photo;
	if($tmp_dim_photo!=$dim_photo) {
		//$sql="UPDATE t_plan_de_classe SET dim_photo='$dim_photo' WHERE id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
		$sql="UPDATE t_plan_de_classe SET dim_photo='$dim_photo' WHERE id='$id_plan' AND id_groupe='$id_groupe' AND login_prof='".$_SESSION['login']."';";
		//echo "$sql<br />";
		$update=mysqli_query($GLOBALS["mysqli"], $sql);
	}
}
else {
	$sql="INSERT INTO t_plan_de_classe SET dim_photo='$dim_photo', id_groupe='$id_groupe', login_prof='".$_SESSION['login']."';";
	//echo "$sql<br />";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);

	$id_plan=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["mysqli"]))) ? false : $___mysqli_res);
}

if(isset($_POST['enregistrer_position'])) {
	$pos_div_x=isset($_POST['pos_div_x']) ? $_POST['pos_div_x'] : NULL;
	$pos_div_y=isset($_POST['pos_div_y']) ? $_POST['pos_div_y'] : NULL;
	if((isset($pos_div_x))&&(isset($pos_div_y))) {

		// A FAIRE: VERIFIER SI id_plan APPARTIENT BIEN AU PROF

		$sql="DELETE FROM t_plan_de_classe_ele WHERE id_plan='$id_plan';";
		//echo "$sql<br />";
		$menage=mysqli_query($GLOBALS["mysqli"], $sql);

		foreach($pos_div_x as $login_ele => $x) {
			if($x!="") {
				if(isset($pos_div_y[$login_ele])) {
					$y=$pos_div_y[$login_ele];

					$x=preg_replace("/px$/","",$x);
					if(preg_match("/^[0-9.]*$/", $x)) {
						$x=round($x);

						if($y!="") {
							$y=preg_replace("/px$/","",$y);
							if(preg_match("/^[0-9.]*$/", $y)) {
								$y=round($y);

								//$sql="INSERT INTO t_plan_de_classe SET id_groupe='$id_groupe', login='".$_SESSION['login']."', nom='div_".$login_ele."_x', valeur='$x';";
								$sql="INSERT INTO t_plan_de_classe_ele SET id_plan='$id_plan', login_ele='".$login_ele."', x='$x', y='$y';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
							}
						}
					}
				}
			}
		}

	}
}


$current_group=get_group($id_groupe);
//echo "<h1 style='text-align:center; margin-top: 0.2em;'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</h1>";

$grp_order_by="c.classe, e.nom, e.prenom";

$sql="SELECT jeg.login, jeg.id_groupe, jeg.periode, e.login, e.nom, e.prenom, e.elenoet, g.id, g.name, g.description, c.classe
FROM eleves e, groupes g, j_eleves_groupes jeg, j_eleves_classes jec, classes c
WHERE jeg.login = e.login
AND jec.login = e.login
AND jec.id_classe=c.id
AND jeg.id_groupe = g.id
AND g.id = '".$id_groupe."'
AND (e.date_sortie is NULL OR e.date_sortie NOT LIKE '20%')
GROUP BY nom, prenom
ORDER BY $grp_order_by;";
//echo "$sql<br />";
$res=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res)==0) {
	echo "<h1 style='text-align:center; margin-top: 0.2em;'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</h1>";

	echo "<div style='position:absolute; top:0.5em; left:0.5em; width:5em; text-align:center;'>\n";
	echo "<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a><br />\n";
	echo "</div>\n";

	//echo "<p>Erreur lors de la requête $sql</p>\n";
	echo "<p>Le groupe proposé a l'air de ne comporter aucun élève.<br />Si cela vous semble erroné, contactez l'administrateur.</p>\n";
	require("../lib/footer.inc.php");
	die();
}

//================================
$largeur_img_fond=$dim_photo+30;
$hauteur_img_fond=$dim_photo+30;
//Création de l'image:
$img=imageCreate($largeur_img_fond,$hauteur_img_fond);
//$img=imagecreatetruecolor($largeur_img_fond,$hauteur_img_fond);

// Epaisseur initiale des traits...
imagesetthickness($img,2);

$x1=0;
$x2=$dim_photo+30;

$y1=0;
$y2=$dim_photo+30;

$couleur_fond=imageColorAllocate($img,255,255,255);
//imagecolorallocatealpha($img, 255, 255, 255, 127);

$couleur_trait=imageColorAllocate($img,0,0,0);
imageLine($img,$x1,$y1,$x2,$y1,$couleur_trait);
imageLine($img,$x1,$y1,$x1,$y2,$couleur_trait);

$chemin_img_fond="../temp/".get_user_temp_directory()."/fond_plan_classe.png";
imagePNG($img, $chemin_img_fond);

imageDestroy($img);
//================================

echo "<form action='".$_SERVER['PHP_SELF']."' name='form_reg_pos' method='post'>\n";
echo "<div style='float:right; width:40px;'>\n";
	echo "<div style='position:relative; top:18px; left:0px;'>\n";
	echo "<a href='javascript:decale_photos(-10,0)' title='Décaler les photos vers la gauche'>";
	echo "<img src='../images/arrow_left.png' width='18' height='18' />";
	echo "</a>";
	echo "</div\n>";

	echo "<div style='position:relative; top:-14px; left:18px;'>\n";
	echo "<a href='javascript:decale_photos(0,-10)' title='Décaler les photos vers le haut'>";
	echo "<img src='../images/up.png' width='18' height='18' />";
	echo "</a>";
	echo "<br />";
	echo "<a href='javascript:decale_photos(0,10)' title='Décaler les photos vers le bas'>";
	echo "<img src='../images/down.png' width='18' height='18' />";
	echo "</a>";
	echo "</div\n>";

	echo "<div style='position:relative; top:-50px; left:35px;'>\n";
	echo "<a href='javascript:decale_photos(10,0)' title='Décaler les photos vers la droite'>";
	echo "<img src='../images/arrow_right.png' width='18' height='18' />";
	echo "</a>";
	echo "</div\n>";
echo "</div\n>";

echo "<div style='float:right; width:40px; margin-right:4em;'>\n";
	echo "<a href='javascript:inverser_plan_classe()' title=\"Inverser le plan : Lorsqu'on projette au tableau avec un vidéoprojecteur, cela peut être utile.\">Inverser</a>";
echo "</div\n>";

echo "<h1 style='text-align:center; margin-top: 0.2em;'>".$current_group['name']." (".$current_group['description'].") en ".$current_group['classlist_string']."</h1>";

echo "<div style='position:absolute; top:0.5em; left:0.5em; width:5em; text-align:center;'>\n";
echo "<a href='".$_SERVER['PHP_SELF']."'><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a><br />\n";
echo "<input type='button' name='Enregistrer' value='Enregistrer' class='noprint' onclick='enregistrement_position_div_photo()' />\n";

//echo "<br />";
//echo "<a href='javascript:decale_photos(10,0)'>Clic</a>";

echo "</div>\n";

echo add_token_field();
echo "<input type='hidden' name='dim_photo_$id_groupe' value='$dim_photo' />\n";

$chaine_affichage_div="";
$unite_div_infobulle="px";
$chaine_login_ele="";

$repertoire="eleves";
while($lig=mysqli_fetch_object($res)) {

	$nom_photo = nom_photo($lig->elenoet,$repertoire);
	$photo = $nom_photo;

	$alt_nom_prenom_aff=mb_strtoupper($lig->nom)." ".casse_mot($lig->prenom,'majf2');

	if (($nom_photo) and (file_exists($photo))) {
		$info_image = getimagesize($photo);
		// largeur et hauteur de l'image d'origine
		$largeur = $info_image[0];
		$hauteur = $info_image[1];

		if($largeur>$hauteur) {$dif_ref=$largeur;}
		else {$dif_ref=$hauteur;}
		$ratio=$dif_ref/$dim_photo;

		// définit largeur et hauteur pour la nouvelle image
		$nouvelle_largeur = $largeur / $ratio;
		$nouvelle_hauteur = $hauteur / $ratio;

		$valeur[0]=$nouvelle_largeur;
		$valeur[1]=$nouvelle_hauteur;

	} else {
		$valeur[0]=$dim_photo;
		$valeur[1]=$dim_photo;
	}

	//echo "<div>";
	$texte="<img src='";
	if (($nom_photo) and (file_exists($photo))) {
		$texte.=$photo;
	}
	else {
		$texte.="images/trombivide.jpg";
	}

	$texte.="' style='border: 0px; width: ".$valeur[0]."px; height: ".$valeur[1]."px;' alt=\"".$alt_nom_prenom_aff."\" title=\"".$alt_nom_prenom_aff."\" />\n";

	//$titre="$lig->login";
	$titre=$lig->$trombi_plan_titre;

	echo creer_div_infobulle("div_".$lig->login,$titre,"",$texte,"",$valeur[0],"","yy","n","n","n",1000);
	$chaine_affichage_div.="document.getElementById('div_".$lig->login."').style.display='';\n";

	if($chaine_login_ele!='') {$chaine_login_ele.=",";}
	$chaine_login_ele.="'$lig->login'";

	echo "<input type='hidden' name='pos_div_x[".$lig->login."]' id='pos_div_".$lig->login."_x' value='' />\n";
	echo "<input type='hidden' name='pos_div_y[".$lig->login."]' id='pos_div_".$lig->login."_y' value='' />\n";
}

echo "<input type='hidden' name='enregistrer_position' value='y' />\n";
echo "<input type='hidden' name='id_groupe' value='$id_groupe' />\n";

echo "</form>\n";

$debug_coordonnees_photos=false;
if($debug_coordonnees_photos) {
	echo "<div id='debug_div' style='position:absolute; width:300px; top:0px; left:1000px;'>
	<textarea id='textarea_debug' cols='20' rows='20'>
	</textarea>
</div>";
}

echo "<script type='text/javascript'>
	function afficher_les_photos() {
		$chaine_affichage_div
	}

	document.body.style.backgroundImage=\"url('$chemin_img_fond')\";

	setTimeout('afficher_les_photos()',1000);

	var tab_ele=new Array($chaine_login_ele);

	function enregistrement_position_div_photo() {
		for(i=0;i<tab_ele.length;i++) {
			if(document.getElementById('pos_div_'+tab_ele[i]+'_x')) {
				document.getElementById('pos_div_'+tab_ele[i]+'_x').value=document.getElementById('div_'+tab_ele[i]).style.left;
			}
			if(document.getElementById('pos_div_'+tab_ele[i]+'_y')) {
				document.getElementById('pos_div_'+tab_ele[i]+'_y').value=document.getElementById('div_'+tab_ele[i]).style.top;
			}
		}

		document.form_reg_pos.submit();
	}

	function decale_photos(dx, dy) {
		for(i=0;i<tab_ele.length;i++) {
			if(document.getElementById('div_'+tab_ele[i])) {
				/*
				if(i<2) {
					alert('x['+i+']='+document.getElementById('div_'+tab_ele[i]).style.left);
					alert('Nettoyé x['+i+']='+document.getElementById('div_'+tab_ele[i]).style.left.replace('px',''));
				}
				*/
				x=document.getElementById('div_'+tab_ele[i]).style.left.replace('px','');
				x=eval(eval(x)+eval(dx));
				/*
				if(i<2) {
					alert('Augmenté x['+i+']='+x);
				}
				*/
				y=document.getElementById('div_'+tab_ele[i]).style.top.replace('px','');
				y=eval(eval(y)+eval(dy));

				document.getElementById('div_'+tab_ele[i]).style.left=x+'px';
				document.getElementById('div_'+tab_ele[i]).style.top=y+'px';
			}
		}
	}


	function inverser_plan_classe() {

		var x_min=-1;
		var x_max=-1;

		var y_min=-1;
		var y_max=-1;

		for(i=0;i<tab_ele.length;i++) {
			if(document.getElementById('div_'+tab_ele[i])) {
				x=document.getElementById('div_'+tab_ele[i]).style.left.replace('px','');
				y=document.getElementById('div_'+tab_ele[i]).style.top.replace('px','');

				if(x_min==-1) {
					x_min=x;
				}
				else {
					if(eval(x)<eval(x_min)) {
						x_min=x;
					}
				}

				if(x_max==-1) {
					x_max=x;
				}
				else {
					if(eval(x)>eval(x_max)) {
						x_max=x;
					}
				}

				if(y_min==-1) {
					y_min=y;
				}
				else {
					if(eval(y)<eval(y_min)) {
						y_min=y;
					}
				}

				if(y_max==-1) {
					y_max=y;
				}
				else {
					if(eval(y)>eval(y_max)) {
						y_max=y;
					}
				}

				".($debug_coordonnees_photos ? "// DEBUG:
				document.getElementById('textarea_debug').value=document.getElementById('textarea_debug').value+'\\n\\n'+tab_ele[i]+'\\n'+'x='+x+' et y='+y+' x_min='+x_min+' x_max='+x_max+' y_min='+y_min+' y_max='+y_max;" : "")."

				/*
				x=document.getElementById('div_'+tab_ele[i]).style.left.replace('px','');
				x=eval(eval(x)+eval(dx));
				y=document.getElementById('div_'+tab_ele[i]).style.top.replace('px','');
				y=eval(eval(y)+eval(dy));

				document.getElementById('div_'+tab_ele[i]).style.left=x+'px';
				document.getElementById('div_'+tab_ele[i]).style.top=y+'px';
				*/
			}
		}
		//alert('x_min='+x_min+' x_max='+x_max+' y_min='+y_min+' y_max='+y_max);
		".($debug_coordonnees_photos ? "// DEBUG:
		document.getElementById('textarea_debug').value=document.getElementById('textarea_debug').value+'\\n======================';
		document.getElementById('textarea_debug').value=document.getElementById('textarea_debug').value+'x_min='+x_min+' x_max='+x_max+' y_min='+y_min+' y_max='+y_max;" : "")."

		//Components.utils.import(\"resource://gre/modules/Console.jsm\");
		//console.log(\"Hello from Firefox code\");

		largeur=eval(x_max-x_min);
		longueur=eval(y_max-y_min);

		".($debug_coordonnees_photos ? "// DEBUG:
		document.getElementById('textarea_debug').value=document.getElementById('textarea_debug').value+'\\nlargeur='+largeur+' longueur='+longueur;" : "")."

		for(i=0;i<tab_ele.length;i++) {
		//for(i=0;i<2;i++) {
			if(document.getElementById('div_'+tab_ele[i])) {

				//document.getElementById('div_'+tab_ele[i]).style.opacity=0.5;

				x=document.getElementById('div_'+tab_ele[i]).style.left.replace('px','');
				y=document.getElementById('div_'+tab_ele[i]).style.top.replace('px','');

				x2=eval(x_min)+eval(largeur)-eval(x);
				y2=eval(y_min)+eval(longueur)-eval(y);

				//alert('x='+x+' et y='+y+'devient x='+x2+' et y='+y2);

				".($debug_coordonnees_photos ? "// DEBUG:
				document.getElementById('textarea_debug').value=document.getElementById('textarea_debug').value+'\\n\\n'+tab_ele[i]+'\\n'+'x='+x+' et y='+y+' devient x='+x2+' et y='+y2;" : "")."

				if(x2>=0) {
					document.getElementById('div_'+tab_ele[i]).style.left=x2+'px';
				}
				if(y2>=0) {
					document.getElementById('div_'+tab_ele[i]).style.top=y2+'px';
				}

				//document.getElementById('div_'+tab_ele[i]).style.opacity=1;
			}
		}
	}


</script>\n";


	echo "<script type='text/javascript'>
largeur_fenetre=window.innerWidth;

function positionner_les_photos() {
	// Positionnement initial des photos
	x_ini=10;
	x_courant=x_ini;
	y_courant=50;

	for(i=0;i<tab_ele.length;i++) {
		if(document.getElementById('div_'+tab_ele[i])) {
			if(eval(x_courant+$dim_photo)>largeur_fenetre) {
				x_courant=x_ini;
				y_courant=eval(y_courant+$dim_photo);
			}
			else {
				x_courant=eval(x_courant+$dim_photo);
			}
			document.getElementById('div_'+tab_ele[i]).style.left=x_courant+'px';
			document.getElementById('div_'+tab_ele[i]).style.top=y_courant+'px';
		}
	}
\n";

$sql="SELECT * FROM t_plan_de_classe_ele WHERE id_plan='$id_plan';";
$res_pos=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_pos)>0) {
	echo "
	// Positionnement d apres ce qui est enregistre
";
	while($lig_pos=mysqli_fetch_object($res_pos)) {
		echo "if(document.getElementById('div_".$lig_pos->login_ele."')) {
	document.getElementById('div_".$lig_pos->login_ele."').style.top='".$lig_pos->y."px';
	document.getElementById('div_".$lig_pos->login_ele."').style.left='".$lig_pos->x."px';
}";
	}
}

echo "}

// Il faut attendre que les div soient initialisés dans le footer.
setTimeout('positionner_les_photos()', 2000);
</script>\n";


echo "<br />";
/**
* Pied de page
*/
require("../lib/footer.inc.php");
?>
