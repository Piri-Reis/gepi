<?php

/**
 * Cette fonction renvoie la liste, ?ventuellement vide, des plugins install?s
 * @param integer $niveau_arbo niveau du script appelant dans l'arborescence Gepi
 * @param string $br cha?ne de caract?re ? intercaler entre chaque nom de plugin
 * @return string liste des plugins obsol?tes
 */
function verif_version_plugins($niveau_arbo,$br=" ") {
	global $gepiVersion,$gepiPath;
	$dossier_plugins=str_repeat("../",$niveau_arbo)."mod_plugins";
	$liste_plugins_installes="";
	$r_dossier_plugins=opendir($dossier_plugins);
	while($un_dossier=readdir($r_dossier_plugins)) {
		if (is_dir($dossier_plugins."/".$un_dossier) && $un_dossier<>"." && $un_dossier<>".." && file_exists($dossier_plugins."/".$un_dossier."/plugin.xml")) {
			$plugin_xml = simplexml_load_file($dossier_plugins."/".$un_dossier."/plugin.xml");
			if ($plugin_xml->versiongepi != $gepiVersion) {
				if ($liste_plugins_installes!="") $liste_plugins_installes.=$br;
				$liste_plugins_installes.=$plugin_xml->nom." ".$plugin_xml->version." (compatible Gepi >=".$plugin_xml->versiongepi.") ";
				}
			}
		}
	return $liste_plugins_installes;
}

?>