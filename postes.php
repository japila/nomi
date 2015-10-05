<?php
session_start();

/*==========================================================================
« postes.php »
Application : permet l'édition d'un poste
Programme fait par Pierre Lavigne     dernière mise à jour : 2015-10-01
Tables utilisées : postes
Fichiers texte utilisés : aucun
Accès au programme : personnel du Secrétariat
============================================================================
*/

error_reporting(0);

require_once("../../utilitaires/defini.php");
require_once("../../utilitaires/BD.class.php");
require_once("../../utilitaires/Table.php");
require_once("../../utilitaires/Formulaire.class.php");
require_once("../pvi_fonctions.php");
require_once("../pvi_param.php");
require_once("../usagers_class.php");
require_once("../usaAcces_class.php");

require_once("paffec_class.php");
require_once("poste_class.php");

//******************** principal ********************************

foreach($_REQUEST as $key=>$value) {
   //$value = Clean($value);
   $$key = $value;
}

if (!isset($action)) $action = "xxx";
if (!isset($operation)) $operation = "";

$bd = NEW BD(USAGER, PASSE, BASE, SERVEUR);

if(GESTION_SESSIONS) {
   require_once("../../utilitaires/session.php");
   $appli = "pvi";
   $session = ControleAcces (SITE . "postes.php", $_REQUEST, session_id(), $bd, $appli, $operation, $css);
   if(!$session) exit();
}

$listePostes = ComboPostes($bd);
$listeUnites = ComboUnites($bd);

$css = "css/formulaire.css";





?>