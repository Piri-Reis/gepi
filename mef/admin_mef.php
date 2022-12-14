<?php
/*
 * Copyright 2010-2019 Josselin Jacquard, Stephane Boireau
 *
 * This file and the mod_abs2 module is distributed under GPL version 3, or
 * (at your option) any later version.
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

$niveau_arbo = 1;
// Initialisations files
include("../lib/initialisationsPropel.inc.php");
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

// Check access
if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
    die();
}

if (empty($_GET['action']) and empty($_POST['action'])) { $action="";}
    else { if (isset($_GET['action'])) {$action=$_GET['action'];} if (isset($_POST['action'])) {$action=$_POST['action'];} }
if (empty($_GET['id']) and empty($_POST['id'])) { $id="";}
    else { if (isset($_GET['id'])) {$id=$_GET['id'];} if (isset($_POST['id'])) {$id=$_POST['id'];} }
if (empty($_GET['EXT_ID']) and empty($_POST['EXT_ID'])) { $EXT_ID="";}
    else { if (isset($_GET['EXT_ID'])) {$EXT_ID=$_GET['EXT_ID'];} if (isset($_POST['EXT_ID'])) {$EXT_ID=$_POST['EXT_ID'];} }
if (empty($_GET['LIBELLE_COURT']) and empty($_POST['LIBELLE_COURT'])) { $LIBELLE_COURT="";}
    else { if (isset($_GET['LIBELLE_COURT'])) {$LIBELLE_COURT=$_GET['LIBELLE_COURT'];} if (isset($_POST['LIBELLE_COURT'])) {$LIBELLE_COURT=$_POST['LIBELLE_COURT'];} }
if (empty($_GET['LIBELLE_LONG']) and empty($_POST['LIBELLE_LONG'])) { $LIBELLE_LONG="";}
    else { if (isset($_GET['LIBELLE_LONG'])) {$LIBELLE_LONG=$_GET['LIBELLE_LONG'];} if (isset($_POST['LIBELLE_LONG'])) {$LIBELLE_LONG=$_POST['LIBELLE_LONG'];} }
if (empty($_GET['LIBELLE_EDITION']) and empty($_POST['LIBELLE_EDITION'])) { $LIBELLE_EDITION="";}
    else { if (isset($_GET['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_GET['LIBELLE_EDITION'];} if (isset($_POST['LIBELLE_EDITION'])) {$LIBELLE_EDITION=$_POST['LIBELLE_EDITION'];} }

$mef = MefQuery::create()->findPk($id);
if ($action == 'supprimer') {
	check_token();
	if ($mef != null) {
		$mef->delete();
	}
} elseif ($action == 'supprimer_tous_mef') {
	check_token();
	$sql="TRUNCATE mef;";
	$menage=mysqli_query($GLOBALS["mysqli"], $sql);
} elseif ($action == 'ajouterdefaut') {
	check_token();
	ajoutMefParDefaut();
} elseif ($action == 'ajouterdefautlycee') {
	check_token();
	ajoutMefParDefautLycee();
} else {
	if ($EXT_ID != '') {
		check_token();
		if ($mef == null) {
			$mef = new Mef();
		}
		$mef->setMefCode(stripslashes($EXT_ID));
		$mef->setLibelleCourt(stripslashes($LIBELLE_COURT));
		$mef->setLibelleLong(stripslashes($LIBELLE_LONG));
		$mef->setLibelleEdition(stripslashes($LIBELLE_EDITION));
		$mef->save();

		if(isset($_POST['MEF_RATTACHEMENT'])) {
			$sql="UPDATE mef SET mef_rattachement='".$_POST['MEF_RATTACHEMENT']."' WHERE mef_code='".$EXT_ID."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
		}

		if(isset($_POST['CODE_MEFSTAT'])) {
			$sql="UPDATE mef SET code_mefstat='".$_POST['CODE_MEFSTAT']."' WHERE mef_code='".$EXT_ID."';";
			$update=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
}

// header
$titre_page = "Gestion des mef (module ??l??mentaire de formation)";
require_once("../lib/header.inc.php");

echo "<p class='bold'>";
echo "<a href=\"../accueil_admin.php\">";
echo "<img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>";
echo " | <a href=\"associer_eleve_mef.php\">Associer les ??l??ves aux MEFs</a>";
//echo " | <a href=\"associer_mef_matiere.php\">Associer les MEFs, les mati??res et les modalit??s ??lection</a>";
echo "</p>";
?>

<div style="text-align:center">
    <h2>D??finition des mef</h2>
<?php 

$sql="show fields from mef;";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)>0) {
	while($lig=mysqli_fetch_assoc($test)) {
		/*
		echo "<pre>";
		print_r($lig);
		echo "</pre>";
		*/
		if((isset($lig['Field']))&&($lig['Field']=='mef_code')) {
			if((isset($lig['Type']))&&($lig['Type']!='varchar(50)')) {
				echo "<p style='color:red; margin:1em; padding:1em;' class='fieldset_opacite50'>ANOMALIE&nbsp;: Le type du champ 'mef_code' la table 'mef' n'est pas correct.<br />Une <a href='../utilitaires/maj.php'>Mise ?? jour des tables</a> est requise.<br />Effectuez la en <strong>For??ant la mise ?? jour des tables</strong>.</p>";
			}
		}
	}
}

if ($action=="importnomenclature") {
	echo "<div style=\"text-align:center\">
<h2>Importer les mef</h2>
";

	if(!isset($_POST['is_posted'])) {
		$tempdir=get_user_temp_directory();
		if(!$tempdir){
			echo "<p style='color:red'>Il semble que le dossier temporaire de l'utilisateur ".$_SESSION['login']." ne soit pas d??fini!?</p>\n";
		}
		else {
			echo "<form enctype='multipart/form-data' action='".$_SERVER['PHP_SELF']."' method='post' id='form_envoi_xml'>
	<fieldset class='fieldset_opacite50'>
		".add_token_field()."
		<p>Veuillez fournir le fichier Nomenclature.xml:<br />
		<input type=\"file\" size=\"65\" name=\"nomenclature_xml_file\" id='input_xml_file' class='fieldset_opacite50' /></p>\n";
				if ($gepiSettings['unzipped_max_filesize']>=0) {
					echo "	<p style=\"font-size:small; color: red;\"><em>REMARQUE&nbsp;:</em> Vous pouvez fournir ?? Gepi le fichier compress?? issu directement de SCONET. (<em>Ex&nbsp;: Nomenclature.zip</em>)</p>";
				}
				echo "
		<input type='hidden' name='action' value='importnomenclature' />
		<input type='hidden' name='is_posted' value='yes' />

		<p><input type='submit' id='input_submit' value='Valider' />
		<input type='button' id='input_button' value='Valider' style='display:none;' onclick=\"check_champ_file()\" /></p>
	</fieldset>

	<script type='text/javascript'>
		document.getElementById('input_submit').style.display='none';
		document.getElementById('input_button').style.display='';

		function check_champ_file() {
			fichier=document.getElementById('input_xml_file').value;
			//alert(fichier);
			if(fichier=='') {
				alert('Vous n\'avez pas s??lectionn?? de fichier XML ?? envoyer.');
			}
			else {
				document.getElementById('form_envoi_xml').submit();
			}
		}
	</script>

</form>";

		}
	}
	else {
		$post_max_size=ini_get('post_max_size');
		$upload_max_filesize=ini_get('upload_max_filesize');
		$max_execution_time=ini_get('max_execution_time');
		$memory_limit=ini_get('memory_limit');

		$tempdir=get_user_temp_directory();
		$xml_file = isset($_FILES["nomenclature_xml_file"]) ? $_FILES["nomenclature_xml_file"] : NULL;

		if(!is_uploaded_file($xml_file['tmp_name'])) {
			echo "<p style='color:red;'>L'upload du fichier a ??chou??.</p>\n";

			echo "<p>Les variables du php.ini peuvent peut-??tre expliquer le probl??me:<br />\n";
			echo "post_max_size=".$post_max_size."<br />\n";
			echo "upload_max_filesize=$upload_max_filesize<br />\n";
			echo "</p>\n";
		}
		else {
			if(!file_exists($xml_file['tmp_name'])){
				echo "<p style='color:red;'>Le fichier aurait ??t?? upload??... mais ne serait pas pr??sent/conserv??.</p>\n";

				echo "<p>Les variables du php.ini peuvent peut-??tre expliquer le probl??me:<br />\n";
				echo "post_max_size=$post_max_size<br />\n";
				echo "upload_max_filesize=$upload_max_filesize<br />\n";
				echo "et le volume de ".$xml_file['name']." serait<br />\n";
				echo "\$xml_file['size']=".volume_human($xml_file['size'])."<br />\n";
				echo "</p>\n";

				echo "<p>Il semblerait que l'absence d'extension .XML ou .ZIP puisse aussi provoquer ce genre de sympt??mes.<br />Dans ce cas, ajoutez l'extension et r??-essayez.</p>\n";
			}
			else {
				echo "<p>Le fichier a ??t?? upload??.</p>\n";

				//$source_file=stripslashes($xml_file['tmp_name']);
				$source_file=$xml_file['tmp_name'];
				$dest_file="../temp/".$tempdir."/nomenclature.xml";
				$res_copy=copy("$source_file" , "$dest_file");

				//===============================================================
				// ajout prise en compte des fichiers ZIP: Marc Leygnac

				$unzipped_max_filesize=getSettingValue('unzipped_max_filesize')*1024*1024;
				// $unzipped_max_filesize = 0    pas de limite de taille pour les fichiers extraits
				// $unzipped_max_filesize < 0    extraction zip d??sactiv??e
				if($unzipped_max_filesize>=0) {
					$fichier_emis=$xml_file['name'];
					$extension_fichier_emis=my_strtolower(mb_strrchr($fichier_emis,"."));
					if (($extension_fichier_emis==".zip")||($xml_file['type']=="application/zip"))
						{
						require_once('../lib/pclzip.lib.php');
						$archive = new PclZip($dest_file);

						if (($list_file_zip = $archive->listContent()) == 0) {
							echo "<p style='color:red;'>Erreur : ".$archive->errorInfo(true)."</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(sizeof($list_file_zip)!=1) {
							echo "<p style='color:red;'>Erreur : L'archive contient plus d'un fichier.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						if(($list_file_zip[0]['size']>$unzipped_max_filesize)&&($unzipped_max_filesize>0)) {
							echo "<p style='color:red;'>Erreur : La taille du fichier extrait (<em>".$list_file_zip[0]['size']." octets</em>) d??passe la limite param??tr??e (<em>".lien_valeur_unzipped_max_filesize()."</em>).</p>\n";
							require("../lib/footer.inc.php");
							die();
						}

						$res_extract=$archive->extract(PCLZIP_OPT_PATH, "../temp/".$tempdir);
						if ($res_extract != 0) {
							echo "<p>Le fichier upload?? a ??t?? d??zipp??.</p>\n";
							$fichier_extrait=$res_extract[0]['filename'];
							unlink("$dest_file"); // Pour Wamp...
							$res_copy=rename("$fichier_extrait" , "$dest_file");
						}
						else {
							echo "<p style='color:red'>Echec de l'extraction de l'archive ZIP.</p>\n";
							require("../lib/footer.inc.php");
							die();
						}
					}
				}
				//fin  ajout prise en compte des fichiers ZIP
				//===============================================================

				if(!$res_copy) {
					echo "<p style='color:red;'>La copie du fichier vers le dossier temporaire a ??chou??.<br />V??rifiez que l'utilisateur ou le groupe apache ou www-data a acc??s au dossier temp/$tempdir</p>\n";
					// Il ne faut pas aller plus loin...
					require("../lib/footer.inc.php");
					die();
				}
				else{
					// Lecture du fichier Nomenclature... pour changer les codes num??riques d'options dans 'temp_gep_import2' en leur code gestion

					$dest_file="../temp/".$tempdir."/nomenclature.xml";

					libxml_use_internal_errors(true);
					$nomenclature_xml=simplexml_load_file($dest_file);
					if(!$nomenclature_xml) {
						echo "<p style='color:red;'>ECHEC du chargement du fichier avec simpleXML.</p>\n";
						echo "<p><a href='".$_SERVER['PHP_SELF']."?action=importnomenclature'>T??l??verser un autre fichier</a></p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$nom_racine=$nomenclature_xml->getName();
					if(my_strtoupper($nom_racine)!='BEE_NOMENCLATURES') {
						echo "<p style='color:red;'>ERREUR: Le fichier XML fourni n'a pas l'air d'??tre un fichier XML Nomenclatures.<br />Sa racine devrait ??tre 'BEE_NOMENCLATURES'.</p>\n";
						require("../lib/footer.inc.php");
						die();
					}

					$tab_champs_mef=array("CODE_MEF",
					"FORMATION",
					"LIBELLE_LONG",
					"LIBELLE_EDITION",
					"CODE_MEFSTAT",
					"MEF_RATTACHEMENT"
					);

					echo "<p>";
					echo "Analyse du fichier...<br />\n";

					$tab_mef=array();
					$i=-1;

					$objet_mefs=($nomenclature_xml->DONNEES->MEFS);
					foreach ($objet_mefs->children() as $mef) {
						$i++;
			
						$tab_mef[$i]=array();
			
						foreach($mef->attributes() as $key => $value) {
							$tab_mef[$i][mb_strtolower($key)]=trim($value);
						}

						foreach($mef->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_mef)) {
								$tab_mef[$i][mb_strtolower($key)]=preg_replace('/"/','',trim($value));
							}
						}
					}
					/*
					echo "<pre>";
					print_r($tab_mef);
					echo "</pre>";
					*/
					$nb_mef_deja=0;
					$nb_mef_reg=0;
					for($loop=0;$loop<count($tab_mef);$loop++) {
						$sql="SELECT 1=1 FROM mef WHERE mef_code='".$tab_mef[$loop]['code_mef']."';";
						$test=mysqli_query($GLOBALS["mysqli"], $sql);
						if(mysqli_num_rows($test)==0) {
							if((!isset($tab_mef[$loop]['libelle_long']))||($tab_mef[$loop]['libelle_long']=="")) {
								echo "<span style='color:red'>ERREUR&nbsp;:</span> Pas de libelle_long pour&nbsp;:<br />";
								echo print_r($tab_mef[$loop]);
								echo "<br />";
							}
							else {
								if((!isset($tab_mef[$loop]['formation']))||($tab_mef[$loop]['formation']=="")) {
									$tab_mef[$loop]['formation']="";
								}
								if((!isset($tab_mef[$loop]['libelle_edition']))||($tab_mef[$loop]['libelle_edition']=="")) {
									$tab_mef[$loop]['libelle_edition']=casse_mot($tab_mef[$loop]['libelle_long'],'majf2');
								}

								if((!isset($tab_mef[$loop]['mef_rattachement']))||($tab_mef[$loop]['mef_rattachement']=="")) {
									$tab_mef[$loop]['mef_rattachement']=$tab_mef[$loop]['code_mef'];
								}

								if(!isset($tab_mef[$loop]['code_mefstat'])) {
									$tab_mef[$loop]['code_mefstat']="";
								}

								$sql="INSERT INTO mef SET mef_code='".$tab_mef[$loop]['code_mef']."',
															libelle_court='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_mef[$loop]['formation'])."',
															libelle_long='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_mef[$loop]['libelle_long'])."',
															libelle_edition='".mysqli_real_escape_string($GLOBALS["mysqli"], $tab_mef[$loop]['libelle_edition'])."',
															code_mefstat='".$tab_mef[$loop]['code_mefstat']."',
															mef_rattachement='".$tab_mef[$loop]['mef_rattachement']."'
															;";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_mef_reg++;
								}
								else {
									echo "<span style='color:red'>ERREUR&nbsp;:</span> Erreur lors de l'import suivant&nbsp;:<br />$sql<br />";
								}
							}
						}
						else {
							$nb_mef_deja++;
						}
					}

					if($nb_mef_deja>0) {
						echo "<p>$nb_mef_deja mef d??j?? pr??sent(s) dans Gepi a(ont) ??t?? trouv??(s) dans le XML.</p>";
					}
					if($nb_mef_reg>0) {
						echo "<p>$nb_mef_reg mef a(ont) ??t?? import??(s) depuis le XML.</p>";
					}

					//=======================================================
					// 20160415

					echo "<p style='margin-top:1em;'>";
					echo "Analyse du fichier pour extraire les associations MEF/MATIERE/MODALITE_ELECTION...<br />\n";

					$tab_champs_programme=array("CODE_MEF",
					"CODE_MATIERE", 
					"CODE_MODALITE_ELECT");

					$programmes=array();
					$i=-1;

					$objet_programmes=($nomenclature_xml->DONNEES->PROGRAMMES);
					foreach ($objet_programmes->children() as $programme) {
						$i++;
						//echo "<p><b>Mati??re $i</b><br />";

						$programmes[$i]=array();

						/*
						<PROGRAMME>
							<CODE_MEF>1001000B11A</CODE_MEF>
							<CODE_MATIERE>005400</CODE_MATIERE>
							<CODE_MODALITE_ELECT>S</CODE_MODALITE_ELECT>
							<HORAIRE>0.00</HORAIRE>
						</PROGRAMME>

						foreach($programme->attributes() as $key => $value) {
							// <PROGRAMME>
							//echo "$key=".$value."<br />";
				
							$programmes[$i][my_strtolower($key)]=trim($value);
						}
						*/

						foreach($programme->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_programme)) {
								$programmes[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
								//echo "\$programme->$key=".$value."<br />";
							}
						}
					}

					$nb_insert_prog=0;

					// Faut-il supprimer les associations qui ne sont plus dans le XML?
					$tab_mef_mat=array();
					$sql="SELECT * FROM mef_matieres;";
					$res_mm=mysqli_query($mysqli, $sql);
					while($lig_mm=mysqli_fetch_object($res_mm)) {
						$tab_mef_mat[$lig_mm->mef_code][$lig_mm->code_matiere][]=$lig_mm->code_modalite_elect;
					}

					for($loop=0;$loop<count($programmes);$loop++) {
						if((isset($programmes[$loop]['code_mef']))&&
						(isset($programmes[$loop]['code_matiere']))&&
						(isset($programmes[$loop]['code_modalite_elect']))) {
							if((!isset($tab_mef_mat[$programmes[$loop]['code_mef']][$programmes[$loop]['code_matiere']]))||
							(!in_array($programmes[$loop]['code_modalite_elect'], $tab_mef_mat[$programmes[$loop]['code_mef']][$programmes[$loop]['code_matiere']]))) {
								$sql="INSERT INTO mef_matieres SET mef_code='".$programmes[$loop]['code_mef']."',
								code_matiere='".$programmes[$loop]['code_matiere']."',
								code_modalite_elect='".$programmes[$loop]['code_modalite_elect']."';";
								$insert=mysqli_query($mysqli, $sql);
								if($insert) {
									$nb_insert_prog++;
								}
							}
						}
					}

					if($nb_insert_prog>0) {
						echo "<p>$nb_insert_prog association(s) MEF/Mati??re/Modalit?? ??lection ont ??t?? import??es.</p>";
					}
					else {
						echo "<p>Aucune association MEF/Mati??re/Modalit?? ??lection n'a ??t?? ajout??e.</p>";
					}
					//=======================================================

					//=======================================================
					// 20160417

					echo "<p>";
					echo "Analyse du fichier pour extraire les MODALITE_ELECTION...<br />\n";

					$tab_champs_modalites=array("LIBELLE_COURT",
					"LIBELLE_LONG");

					$modalites=array();
					$i=-1;

					$objet_modalites=($nomenclature_xml->DONNEES->MODALITES_ELECTION);
					foreach ($objet_modalites->children() as $modalite) {
						$i++;
						//echo "<p><b>Mati??re $i</b><br />";

						$modalites[$i]=array();

						/*
						<MODALITE_ELECTION CODE_MODALITE_ELECT="S">
							<LIBELLE_COURT>TRONC COMM</LIBELLE_COURT>
							<LIBELLE_LONG>MATIERE ENSEIGNEE EN TRONC COMMUN</LIBELLE_LONG>
						</MODALITE_ELECTION>
						*/

						foreach($modalite->attributes() as $key => $value) {
							$modalites[$i][my_strtolower($key)]=trim($value);
						}

						foreach($modalite->children() as $key => $value) {
							if(in_array(my_strtoupper($key),$tab_champs_modalites)) {
								$modalites[$i][my_strtolower($key)]=preg_replace('/"/','',trim($value));
								//echo "\$modalite->$key=".$value."<br />";
							}
						}
					}

					/*
					echo "<pre>";
					print_r($modalites);
					echo "</pre>";
					*/

					$nb_insert_mod=0;

					// Faut-il supprimer les associations qui ne sont plus dans le XML?
					$tab_modalites=array();
					$sql="SELECT * FROM nomenclature_modalites_election;";
					$res_mm=mysqli_query($GLOBALS["mysqli"], $sql);
					while($lig_mm=mysqli_fetch_object($res_mm)) {
						$tab_modalites[$lig_mm->code_modalite_elect]=$lig_mm->libelle_court;
					}

					$sql="TRUNCATE nomenclature_modalites_election;";
					$del=mysqli_query($GLOBALS["mysqli"], $sql);

					for($loop=0;$loop<count($modalites);$loop++) {
						if((isset($modalites[$loop]['code_modalite_elect']))&&
						(isset($modalites[$loop]['libelle_court']))&&
						(isset($modalites[$loop]['libelle_long']))) {
							if(!array_key_exists($modalites[$loop]['code_modalite_elect'], $tab_modalites)) {
								$sql="INSERT INTO nomenclature_modalites_election SET code_modalite_elect='".$modalites[$loop]['code_modalite_elect']."',
								libelle_court='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_court'])."',
								libelle_long='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_long'])."';";
								$insert=mysqli_query($GLOBALS["mysqli"], $sql);
								if($insert) {
									$nb_insert_mod++;
								}
							}
							else {
								$sql="UPDATE nomenclature_modalites_election SET libelle_court='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_court'])."',
								libelle_long='".mysqli_real_escape_string($mysqli, $modalites[$loop]['libelle_long'])."' WHERE  code_modalite_elect='".$modalites[$loop]['code_modalite_elect']."';";
								$update=mysqli_query($GLOBALS["mysqli"], $sql);
							}
						}
					}

					if($nb_insert_mod>0) {
						echo "<p>$nb_insert_mod modalit??s ??lection ont ??t?? import??es.</p>";
					}
					else {
						echo "<p>Aucune modalit?? ??lection n'a ??t?? enregistr??e.</p>";
					}
					//=======================================================

				}
			}
		}

	}
	echo "
</div>
<br />";

}
elseif ($action == "ajouter" OR $action == "modifier") { 
?>
<div style="text-align:center">
    <?php
	if($action=="ajouter") { 
	    echo "<h2>Ajout d'un mef</h2>";
	} elseif ($action=="modifier") {
	    echo "<h2>Modifier un mef</h2>";
	}
	?>

    <form action="admin_mef.php" method="post" id="form2">
    	<p>
<?php
echo add_token_field();
$tab_mef=get_tab_mef();
?>
    	</p>

    	<?php
    	/*
    	echo "<pre>";
    	print_r($tab_mef);
    	echo "</pre>";
    	*/
    	?>
      <table style='border-spacing: 2px;' class="menu">
        <tr>
          <td style='padding : 2px;'>Id ext??rieur (nomenclature EN)</td>
          <td>Libell?? court</td>
          <td>Libell?? long</td>
          <td>Libell?? d'??dition</td>
          <td>Code mefstat</td>
          <td title='Exemple: Le MEF  "6EME Bilangue" est rattach?? au MEF "6EME"'>Mef rattachement</td>
       </tr>
        <tr>
              <td>
           <?php
       // Initialisations
       $code_mefstat="";
       $mef_rattachement="";
       $mef_code="";

	   if ($mef != null) { ?>
	      <input name="id" type="hidden" id="id" value="<?php echo $id ?>" />
	   <?php
			//$sql="SELECT * FROM mef WHERE mef_code='".$mef->getMefCode()."';";
			// $mef->getMefCode() renvoie un truc bizarre.
			// Exemple: 2147483647 au lieu de 10010012110
			$sql="SELECT * FROM mef WHERE id='".$id."';";
			//echo "$sql<br />";
			$res_mef_courant=mysqli_query($GLOBALS["mysqli"], $sql);
			if(mysqli_num_rows($res_mef_courant)>0) {
				$code_mefstat=old_mysql_result($res_mef_courant, 0, "code_mefstat");
				$mef_rattachement=old_mysql_result($res_mef_courant, 0, "mef_rattachement");
				// Faute de le r??cup??rer correctement avec getMefCode()
				$mef_code=old_mysql_result($res_mef_courant, 0, "mef_code");
			}
			//echo "\$mef_rattachement=$mef_rattachement";
	   ?>
	   <?php } ?>
              	<input name="EXT_ID" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {
              	//echo $mef->getMefCode();
              	echo $mef_code;
              	}?>" />
              </td>
              <td><input name="LIBELLE_COURT" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleCourt();} ?>" /></td>
              <td><input name="LIBELLE_LONG" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleLong();} ?>" /></td>
              <td><input name="LIBELLE_EDITION" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $mef->getLibelleEdition();} ?>" /></td>
              <td><input name="CODE_MEFSTAT" type="text" size="14" maxlength="50" value="<?php  if ($mef != null) {echo $code_mefstat;} ?>" /></td>
              <td>
                  <select name='MEF_RATTACHEMENT'>
                      <option value=''<?php 
                      //if(($mef_rattachement!="")&&($mef_rattachement==$mef->getMefCode())) {echo " selected";}
                      if(($mef_rattachement!="")&&($mef_rattachement==$mef_code)) {echo " selected";}
                      ?>>---</option>
                      <?php
                          foreach($tab_mef as $key => $value) {
                              echo "
                       <option value='".$key."'";
                              if(($mef!=null)&&($key==$mef_rattachement)) {
                                  echo " selected";
                              }
                              echo ">".$value['designation_courte']."</option>";
                          }
                      ?>
                  </select>
              </td>
        </tr>
      </table>
     <p><input type="submit" name="Submit" value="Enregistrer" /></p>
    </form>
<br/><br/>
<?php /* fin du div de centrage du tableau pour ie5 */ ?>
</div>
<?php
} ?>
	<a href="admin_mef.php?action=ajouter"><img src='../images/icons/add.png' alt='' class='back_link' /> Ajouter des mef</a>
	<br/><br/>
	<a href="admin_mef.php?action=ajouterdefaut<?php echo add_token_in_url();?>"><img src='../images/icons/add.png' alt='' class='back_link' /> Ajouter les mef par d??faut de coll??ge</a>
	<br/><br/>
	<a href="admin_mef.php?action=ajouterdefautlycee<?php echo add_token_in_url();?>"><img src='../images/icons/add.png' alt='' class='back_link' /> Ajouter les mef par d??faut de lyc??e</a>
	<br/><br/>
	<a href="admin_mef.php?action=importnomenclature<?php echo add_token_in_url();?>"><img src='../images/icons/add.png' alt='' class='back_link' /> Importer les mef depuis un fichier Nomenclature.xml</a>
	<br/><br/>
    <table style='border-spacing: 1px;' class="menu">
      <tr>
        <th>Id</th>
        <th>Num??ro mef nomenclature EN</th>
        <th>Libelle Court</th>
        <th>Libelle Long</th>
        <th>Libelle Edition</th>
        <th>Mef rattachement</th>
        <th style="width: 25px;"></th>
        <th style="width: 25px;">
            <a href="admin_mef.php?action=supprimer_tous_mef<?php echo add_token_in_url();?>" onclick="return confirm('Etes-vous s??r de vouloir supprimer tous les MEF ?')">
                <img src="../images/icons/delete.png" 
                     class="icone20"
                     title="Supprimer tous les MEF" 
                     alt="Supprimer tout" />
            </a>
        </th>
     </tr>
    <?php
    $tab_mef=array();
    $sql="SELECT * FROM mef;";
    $res_mef=mysqli_query($GLOBALS["mysqli"], $sql);
    if(mysqli_num_rows($res_mef)>0) {
        while($lig_mef=mysqli_fetch_object($res_mef)) {
            $tab_mef[$lig_mef->mef_code]["libelle_court"]=$lig_mef->libelle_court;
            $tab_mef[$lig_mef->mef_code]["libelle_long"]=$lig_mef->libelle_long;
            $tab_mef[$lig_mef->mef_code]["libelle_edition"]=$lig_mef->libelle_edition;
            $tab_mef[$lig_mef->mef_code]["code_mefstat"]=$lig_mef->code_mefstat;
            $tab_mef[$lig_mef->mef_code]["mef_rattachement"]=$lig_mef->mef_rattachement;
        }
    }

    $mef_collection = new PropelCollection();
    $mef_collection = MefQuery::create()->find();
   foreach ($mef_collection as $mef) {
 ?>
        <tr>
	  <td><?php echo $mef->getId(); ?></td>
          <td><?php
              // On r??cup??re un truc bizarre
              //echo $mef->getMefCode();
              $sql="SELECT * FROM mef WHERE id='".$mef->getId()."';";
              $res_mef_courant=mysqli_query($GLOBALS["mysqli"], $sql);
              if(mysqli_num_rows($res_mef_courant)>0) {
                  echo old_mysql_result($res_mef_courant,0,"mef_code");
              }
              else {
                  echo "???";
              }
              ?></td>
          <td><?php echo $mef->getLibelleCourt(); ?></td>
          <td><?php echo $mef->getLibelleLong(); ?></td>
          <td><?php echo $mef->getLibelleEdition(); ?></td>
          <td>
          <?php
              if(mysqli_num_rows($res_mef_courant)>0) {
                  $mef_rattachement_courant=old_mysql_result($res_mef_courant,0,"mef_rattachement");
                  if(isset($tab_mef[$mef_rattachement_courant])) {
                      echo $tab_mef[$mef_rattachement_courant]['libelle_edition'];
                  }
                  else {
                      echo "???";
                  }
              }
              else {
                  echo "???";
              }
          ?>
          </td>
          <td><a href="admin_mef.php?action=modifier&amp;id=<?php echo $mef->getId(); echo add_token_in_url();?>">
                  <img src="../images/icons/configure.png" 
                       title="Modifier" 
                       alt="Modifier" />
              </a>
          </td>
          <td><a href="admin_mef.php?action=supprimer&amp;id=<?php echo $mef->getId(); echo add_token_in_url();?>" onclick="return confirm('Etes-vous s??r de vouloir supprimer ce mef ?')">
                  <img src="../images/icons/delete.png"
                       class="icone20"
                       title="Supprimer" 
                       alt="Supprimer" />
              </a>
          </td>
       </tr>
     <?php } ?>
    </table>
    <br/><br/>
</div>


<?php require("../lib/footer.inc.php");

// PROBLEME: Pour les MEF par d??faut de lyc??e, il faudrait MEF_RATTACHEMENT
function ajoutMefParDefaut() {
	/*
    $mef = new Mef();
    //$mef->setMefCode("1031000111");
    //$mef->setLibelleCourt("3G");
    $mef->setMefCode("1031001911");
    $mef->setLibelleCourt("3EME");
    $mef->setLibelleLong("3EME");
    $mef->setLibelleEdition("3eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    $mef->setMefCode("1021000111");
    $mef->setLibelleCourt("4G");
    $mef->setLibelleLong("4EME");
    $mef->setLibelleEdition("4eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    $mef->setMefCode("1011000111");
    $mef->setLibelleCourt("5G");
    $mef->setLibelleLong("5EME");
    $mef->setLibelleEdition("5eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }

    $mef = new Mef();
    //$mef->setMefCode("1001000111");
    //$mef->setLibelleCourt("6G");
    $mef->setMefCode("1001001211");
    $mef->setLibelleCourt("6EME");
    $mef->setLibelleLong("6EME");
    $mef->setLibelleEdition("6eme");
    if (MefQuery::create()->filterByMefCode($mef->getMefCode())->find()->isEmpty()) {
	$mef->save();
    }
    */

	$mef_clg=array();
	$mef_clg[]="CODE_MEF;LIBELLE_COURT;LIBELLE_LONG;LIBELLE_EDITION;CODE_MEFSTAT;MEF_RATTACHEMENT";
	$mef_clg[]="10310019110;3EME;3EME;3??me;21160010019;10310019110;";
	$mef_clg[]="10210001110;4EME;4EME;4??me;21150010001;10210001110;";
	$mef_clg[]="10110001110;5EME;5EME;5??me;21120010001;10110001110;";
	$mef_clg[]="10010012110;6EME;6EME;6??me;21110010012;10010012110;";

	for($loop=1;$loop<count($mef_clg);$loop++) {
		$tab=explode(";", $mef_clg[$loop]);
		$sql="SELECT * FROM mef WHERE mef_code='".$tab[0]."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$sql="INSERT INTO mef SET mef_code='".$tab[0]."', libelle_court='".$tab[1]."', libelle_long='".$tab[2]."', libelle_edition='".$tab[3]."', code_mefstat='".$tab[4]."', mef_rattachement='".$tab[5]."';";
			//echo "$sql<br />";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
}

function ajoutMefParDefautLycee() {
	$mef_lycee=array();
	$mef_lycee[]="CODE_MEF;LIBELLE_COURT;LIBELLE_LONG;CODE_MEFSTAT;MEF_RATTACHEMENT";
	$mef_lycee[]="20010015110;2DEGT2;2de GT2 (cas g??n??ral 2 ens.explo);22111410015;20010015110";
	$mef_lycee[]="20010015112;2EURO2;2de GT2 section europ??enne;22111410015;20010015110";
	$mef_lycee[]="20010016110;2DEGT3;2de GT3 (cas d??rogat. 3 ens.explo);22111410016;20010016110";
	$mef_lycee[]="20010017110;2DEGT1;2de GT1 (cas d??rogat. 1 ens.explo);22111410017;20010017110";
	$mef_lycee[]="20111010110;1S SVT;Premi??re scientifique SVT;22121111010;20111010110";
	$mef_lycee[]="20111010112;1SVTEU;1??re scientif. SVT europ??enne;22121111010;20111010110";
	$mef_lycee[]="20111011110;1S SI;Premi??re scientifique Sc.Industr.;22121111011;20111011110";
	$mef_lycee[]="20112005110;1ES;Premi??re ??conomique et sociale;22121312005;20112005110";
	$mef_lycee[]="20112005112;1ES-EU;1??re ??conomique et sociale europ??enne;22121312005;20112005110";
	$mef_lycee[]="20113019110;1L;Premi??re litt??raire;22121213019;20113019110";
	$mef_lycee[]="20113019112;1L-EU;1??re litt??raire europ??enne;22121213019;20113019110";
	$mef_lycee[]="20211010110;TS SVT;Terminale scientifique SVT;22131111010;20211010110";
	$mef_lycee[]="20211010112;TSVTEU;Terminale scientifique SVT europ??enne;22131111010;20211010110";
	$mef_lycee[]="20211011110;TS SI;Terminale scientifique Sc. Industr.;22131111011;20211011110";
	$mef_lycee[]="20212005110;TES;Terminale ??conomique et sociale;22131312005;20212005110";
	$mef_lycee[]="20212005112;TESEU;Terminale ??conomique et soc. europ??enne;22131312005;20212005110";
	$mef_lycee[]="20213019110;TL;Terminale litt??raire;22131213019;20213019110";
	$mef_lycee[]="20213019112;TLEU;Terminale litt??raire europ??enne;22131213019;20213019110";

	for($loop=1;$loop<count($mef_lycee);$loop++) {
		$tab=explode(";", $mef_lycee[$loop]);
		$sql="SELECT * FROM mef WHERE mef_code='".$tab[0]."';";
		$res=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res)==0) {
			$sql="INSERT INTO mef SET mef_code='".$tab[0]."', libelle_court='".$tab[1]."', libelle_long='".$tab[2]."', libelle_edition='".$tab[2]."', code_mefstat='".$tab[3]."', mef_rattachement='".$tab[4]."';";
			$insert=mysqli_query($GLOBALS["mysqli"], $sql);
		}
	}
}


