<?php
/*
 *
 * Copyright 2001, 2016 Thomas Belliard, Laurent Delineau, Edouard Hue, Eric Lebrun
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

$sql="SELECT 1=1 FROM droits WHERE id='/eleves/cherche_login.php';";
$test=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($test)==0) {
	$sql="INSERT INTO droits SET id='/eleves/cherche_login.php',
	administrateur='V',
	professeur='F',
	cpe='F',
	scolarite='V',
	eleve='F',
	responsable='F',
	secours='F',
	autre='F',
	description='Ajax: Recherche d un login',
	statut='';";
	$insert=mysqli_query($GLOBALS["mysqli"], $sql);
}

if (!checkAccess()) {
    header("Location: ../logout.php?auto=1");
	die();
}

header('Content-Type: text/html; charset=utf-8');

$auth_sso=getSettingValue("auth_sso") ? getSettingValue("auth_sso") : "";

$gepi_non_plugin_lcs_mais_recherche_ldap=false;
if((getSettingAOui('gepi_non_plugin_lcs_mais_recherche_ldap'))&&(file_exists("../secure/config_ldap.inc.php"))) {
	include("../secure/config_ldap.inc.php");

	$lcs_ldap_base_dn=$ldap_base_dn;
	$lcs_ldap_host=$ldap_host;
	$lcs_ldap_port=$ldap_port;
	$gepi_non_plugin_lcs_mais_recherche_ldap=true;

	$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
	$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;
}

$nom=isset($_POST['nom']) ? $_POST['nom'] : (isset($_GET['nom']) ? $_GET['nom'] : "");
$prenom=isset($_POST['prenom']) ? $_POST['prenom'] : (isset($_GET['prenom']) ? $_GET['prenom'] : "");
$statut_recherche=isset($_POST['statut_recherche']) ? $_POST['statut_recherche'] : (isset($_GET['statut_recherche']) ? $_GET['statut_recherche'] : "eleve");
$type_server=isset($_POST['type_server']) ? $_POST['type_server'] : (isset($_GET['type_server']) ? $_GET['type_server'] : "");

$erreur=0;

if(($type_server=='scribe')&&(($nom!="")||($prenom!=""))) {
	$ldap = new LDAPServerScribe();
	$ldap->connect();

	$nom=preg_replace("/[^A-Za-z]/","*",$nom);
	$prenom=preg_replace("/[^A-Za-z]/","*",$prenom);

	$filtre="";
	if($nom!='') {
		if($prenom!='') {
			$filtre="(&(".$ldap->champ_nom."=*$nom*)(".$ldap->champ_prenom."=*$prenom*))";
		}
		else {
			$filtre="(".$ldap->champ_nom."=*$nom*)";
		}
	}
	elseif($prenom!='') {
		$filtre="(".$ldap->champ_prenom."=*$prenom*)";
	}

	if($filtre!="") {
		$result= ldap_search ($ldap->ds, 'ou=utilisateurs,'.$ldap->get_base_branch(), $filtre);
		if ($result) {
			$info = @ldap_get_entries( $ldap->ds, $result );
			if($info["count"]==0) {
				echo "<span style='color:red;'>Aucun enregistrement n'a ??t?? trouv?? dans le LDAP pour ".$nom." ".$prenom.". Il est <strong>indispensable</strong>, pour un acc??s de l'??l??ve ?? Gepi, de cr??er le compte dans l'annuaire avant de le cr??er dans Gepi&nbsp;!!!</span><br />\n";
				$erreur++;
			}
			else {
				/*
				echo "<pre>";
				echo print_r($info);
				echo "</pre>";
				*/
				if($statut_recherche=="eleve") {
					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th>Login Scribe</th>\n";
					echo "<th>Nom</th>\n";
					echo "<th>Pr??nom</th>\n";
					echo "<th>Naissance</th>\n";
					echo "</tr>\n";
					$alt=1;
					for($i=0;$i<$info["count"];$i++) {

						//$nouvel_eleve->setNaissance(formater_date_pour_mysql($eleves[$nb]['entpersondatenaissance'][0]));
						$naissance=$infos[$i]['entpersondatenaissance'][0];
						$tab=explode("/",$naissance);
						$jour=$tab[0];
						$mois=$tab[1];
						$annee=$tab[2];

						$sexe=$info[$i]['entpersonsexe'][0];

						$employeenumber=(array_key_exists('employeenumber', $info[$i])) ? $info[$i]['employeenumber'][0] : "";

						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><a href=\"#\" onclick=\"document.getElementById('reg_login').value='".$info[$i][$ldap->champ_login][0]."';
															document.getElementById('nom').value='".$info[$i][$ldap->champ_nom][0]."';
															document.getElementById('prenom').value='".$info[$i][$ldap->champ_prenom][0]."';
															document.getElementById('birth_day').value='".$jour."';
															document.getElementById('birth_month').value='".$mois."';
															document.getElementById('birth_year').value='".$annee."';
															document.getElementById('reg_email').value='".$info[$i][$ldap->champ_email][0]."';
															document.getElementById('elenoet').value='".$employeenumber."';
															document.getElementById('reg_sexe$sexe').checked=true;
															return false;\"
										title=\"Compl??ter les champs de formulaire avec les informations trouv??es dans l'annuaire LDAP pour cet identifiant.\">".$info[$i][$ldap->champ_login][0]."</a></td>\n";
						echo "<td>".$info[$i][$ldap->champ_nom][0]."</td>\n";
						echo "<td>".$info[$i][$ldap->champ_prenom][0]."</td>\n";
						echo "<td>".$naissance."</td>\n";
						echo "</tr>\n";
					}
					echo "</table>\n";
				}
				else {
					echo "<table class='boireaus'>\n";
					echo "<tr>\n";
					echo "<th>Login Scribe</th>\n";
					echo "<th>Nom</th>\n";
					echo "<th>Pr??nom</th>\n";
					echo "</tr>\n";
					$alt=1;
					for($i=0;$i<$info["count"];$i++) {

						//$civilite=$infos[0]['personaltitle'][0];

						$sexe=$info[$i]['entpersonsexe'][0];

						$alt=$alt*(-1);
						echo "<tr class='lig$alt'>\n";
						echo "<td><a href=\"#\" onclick=\"document.getElementById('reg_login').value='".$info[$i][$ldap->champ_login][0]."';
															document.getElementById('reg_nom').value='".$info[$i][$ldap->champ_nom][0]."';
															document.getElementById('reg_prenom').value='".$info[$i][$ldap->champ_prenom][0]."';
															document.getElementById('reg_email').value='".$info[$i]["mail"][0]."';
															if('$sexe'=='F') {document.getElementById('reg_civilite').selectedIndex=2;} else {document.getElementById('reg_civilite').selectedIndex=1;}
															return false;\"
										title=\"Compl??ter les champs de formulaire avec les informations trouv??es dans l'annuaire LDAP pour cet identifiant.\">".$info[$i][$ldap->champ_login][0]."</a></td>\n";
						echo "<td>".$info[$i][$ldap->champ_nom][0]."</td>\n";
						echo "<td>".$info[$i][$ldap->champ_prenom][0]."</td>\n";
						//echo "<td>".$naissance."</td>\n";
					}
					echo "</table>\n";
				}
			}
			@ldap_free_result($result);
		}
		else {
			echo "<p style='color:red;>Echec de la recherche dans le LDAP.</p>\n";
		}
	}
}
elseif(($nom!="")||($prenom!="")) {

	$nom=preg_replace("/[^A-Za-z]/","*",$nom);
	$prenom=preg_replace("/[^A-Za-z]/","*",$prenom);

	if(($auth_sso=='lcs')||($gepi_non_plugin_lcs_mais_recherche_ldap)) {
		function connect_ldap($l_adresse,$l_port,$l_login,$l_pwd) {
			$ds = @ldap_connect($l_adresse, $l_port);
			if($ds) {
				// On dit qu'on utilise LDAP V3, sinon la V2 par defaut est utilise et le bind ne passe pas.
				$norme = @ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				// Acces non anonyme
				if ($l_login != '') {
					// On tente un bind
					$b = @ldap_bind($ds, $l_login, $l_pwd);
				} else {
					// Acces anonyme
					$b = @ldap_bind($ds);
				}
				if ($b) {
					return $ds;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}

		// Initialisation
		$lcs_ldap_people_dn = 'ou=people,'.$lcs_ldap_base_dn;
		$lcs_ldap_groups_dn = 'ou=groups,'.$lcs_ldap_base_dn;

		// On se connecte au LDAP
		$ds = connect_ldap($lcs_ldap_host,$lcs_ldap_port,"","");
		//echo "<p>CONNEXION AU LDAP</p>";

		// LDAP attribute
		$ldap_people_attr = array(
		"uid",               // login
		"cn",                // Prenom  Nom
		"sn",               // Nom
		"givenname",            // Pseudo
		"mail",              // Mail
		"homedirectory",           // Home directory personnal web space
		"description",
		"loginshell",
		"gecos",             // Date de naissance,Sexe (F/M),
		"employeenumber"    // identifiant gep
		);

		$filtre="";
		if($nom!='') {
			if($prenom!='') {
				$filtre="(&(sn=".preg_replace("/\*\*/", "*", "*$nom*").")(givenname=".preg_replace("/\*\*/", "*", "*$prenom*")."))";
			}
			else {
				$filtre="(sn=".preg_replace("/\*\*/", "*", "*$nom*").")";
			}
		}
		elseif($prenom!='') {
			//$filtre="(givenname=*$prenom*)";
			$filtre="(givenname=".preg_replace("/\*\*/", "*", "*$prenom*").")";
		}

		//echo "<p style='color:plum'><strong>Filtre=</strong>$filtre</p>";

		if($filtre!="") {
			$result= ldap_search ($ds, $lcs_ldap_people_dn, $filtre);
			if ($result) {
				$info = @ldap_get_entries( $ds, $result );
				if($info["count"]==0) {
					echo "<span style='color:red;'>Aucun enregistrement n'a ??t?? trouv?? dans le LDAP pour ".$nom." ".$prenom.". Il est <strong>indispensable</strong>, pour un acc??s de l'??l??ve ?? Gepi, de cr??er le compte dans l'annuaire avant de le cr??er dans Gepi&nbsp;!!!</span><br />\n";
					$erreur++;
				}
				else {
					/*
					echo "<pre>";
					echo print_r($info);
					echo "</pre>";
					*/
					if($statut_recherche=="eleve") {
						echo "<table class='boireaus'>\n";
						echo "<tr>\n";
						echo "<th>Login LCS</th>\n";
						echo "<th>Nom</th>\n";
						echo "<th>Pr??nom</th>\n";
						echo "<th>Naissance</th>\n";
						echo "</tr>\n";
						$alt=1;
						for($i=0;$i<$info["count"];$i++) {

							$tab=explode(",",$info[$i]["gecos"][0]);
							$jour=mb_substr($tab[1],6,2);
							$mois=mb_substr($tab[1],4,2);
							$annee=mb_substr($tab[1],0,4);
							$naissance=$jour."/".$mois."/".$annee;

							$sexe=$tab[2];

							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td><a href=\"#\" onclick=\"document.getElementById('reg_login').value='".$info[$i]["uid"][0]."';
																document.getElementById('nom').value=document.getElementById('td_recherche_ldap_nom_$i').innerHTML;
																document.getElementById('prenom').value=document.getElementById('td_recherche_ldap_prenom_$i').innerHTML;
																document.getElementById('birth_day').value='".$jour."';
																document.getElementById('birth_month').value='".$mois."';
																document.getElementById('birth_year').value='".$annee."';
																document.getElementById('reg_email').value='".(isset($info[$i]["mail"][0]) ? $info[$i]["mail"][0] : "")."';
																document.getElementById('elenoet').value='".(isset($info[$i]["employeenumber"][0]) ? $info[$i]["employeenumber"][0] : "")."';
																document.getElementById('reg_sexe$sexe').checked=true;
																return false;\"
											title=\"Compl??ter les champs de formulaire avec les informations trouv??es dans l'annuaire LDAP pour cet identifiant.\">".$info[$i]["uid"][0]."</a></td>\n";
							echo "<td id='td_recherche_ldap_nom_$i'>".casse_mot($info[$i]["sn"][0], "majf2")."</td>\n";
							echo "<td id='td_recherche_ldap_prenom_$i'>".casse_mot($info[$i]["givenname"][0], "majf2")."</td>\n";
							echo "<td>".$naissance."</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n";
					}
					else {
						echo "<table class='boireaus'>\n";
						echo "<tr>\n";
						echo "<th>Login LCS</th>\n";
						echo "<th>Nom</th>\n";
						echo "<th>Pr??nom</th>\n";
						echo "</tr>\n";
						$alt=1;
						for($i=0;$i<$info["count"];$i++) {

							$tab=explode(",",$info[$i]["gecos"][0]);
							/*
							$jour=mb_substr($tab[1],6,2);
							$mois=mb_substr($tab[1],4,2);
							$annee=mb_substr($tab[1],0,4);
							$naissance=$jour."/".$mois."/".$annee;
							*/
							$sexe=$tab[2];

							$alt=$alt*(-1);
							echo "<tr class='lig$alt'>\n";
							echo "<td><a href=\"#\" onclick=\"document.getElementById('reg_login').value='".$info[$i]["uid"][0]."';
																document.getElementById('reg_nom').value='".$info[$i]["sn"][0]."';
																document.getElementById('reg_prenom').value='".$info[$i]["givenname"][0]."';
																document.getElementById('reg_email').value='".$info[$i]["mail"][0]."';
																if('$sexe'=='F') {document.getElementById('reg_civilite').selectedIndex=2;} else {document.getElementById('reg_civilite').selectedIndex=1;}
																//document.getElementById('elenoet').value='".$info[$i]["employeenumber"][0]."';
																return false;\"
											title=\"Compl??ter les champs de formulaire avec les informations trouv??es dans l'annuaire LDAP pour cet identifiant.\">".$info[$i]["uid"][0]."</a></td>\n";
							echo "<td>".$info[$i]["sn"][0]."</td>\n";
							echo "<td>".$info[$i]["givenname"][0]."</td>\n";
							//echo "<td>".$naissance."</td>\n";
							echo "</tr>\n";
						}
						echo "</table>\n";
					}
				}
				@ldap_free_result ( $result );
			}
			else {
				echo "<p style='color:red;>Echec de la recherche dans le LDAP.</p>\n";
			}
		}
	}
}
?>

