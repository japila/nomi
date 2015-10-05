<?php

/*==========================================================================

Programme : archives/membres/instances.php

Application : affichage des instances et de leur date de mise � jour + envoi de notification

Programme fait par Pierre Lavigne
Derni�re mise � jour : 2006-12-06

Tables utilis�es : 

Fichiers texte utilis�s : 

Acc�s au programme : acc�s aux personnes autoris�es + SG + AR

*/

require_once("archives/fonctions.php");
require_once("archives/BD.class.php");
require_once("archives/defini.php");
require_once("archives/Formulaire.class");
require_once("archives/Table.php");

$bd = NEW BD(USAGER, PASSE, BASE, SERVEUR);
$msg = Verif_usager_unite ($PHP_AUTH_USER, "memad", $bd);
if($msg == FALSE) {
   Html_non_acces();
   exit();
}

if(!isset($action)) $action = "";

Switch ($action) {

   case "affichage-sg" : //-------------------------------------------------------
   case "" :
   
      $liste['CAD'] = "Conseil d'administration";
      $liste['CEX'] = ".......... Comit� ex�cutif";
      $liste['CRET'] = "......... Comit� de retraite";
      $liste['CVE'] = ".......... Comit� de v�rification";
      $liste['CER'] = "........... Comit� d'�thique de la recherche";
      $liste['CGO'] = ".......... Comit� de gouvernance";
      $liste['CAC'] = "Conseil acad�mique";
      $liste['ADD'] = "Assembl�e de direction";
      $liste['CCO'] = "Comit� de coordination";
      $liste['section1'] = "---------------------------------------------------";
      $liste['CET'] = "Commission des �tudes";
      $liste['CRE'] = "Commission de la recherche";
      $liste['CAI'] = "Commission des affaires institutionnelles";
      $liste['CRM'] = "Commission des ressources mat�rielles";
      $liste['section2'] = "---------------------------------------------------";
      $liste['CCE'] = "Comit� conseil en enseignement";
      $liste['CPRESS'] = "Comit� de la p�riode de ressourcement";
      $liste['CPROB'] = "Comit� des probations";
      $liste['CPROM'] = "Comit� des promotions";
      $liste['CGRI'] = "Comit� des griefs";
      $liste['CRP'] = "Comit� des relations professionnelles";
      $liste['AUUDEM'] = "Assembl�e universitaire de l'UdeM";
      $liste['CFESP'] = "Conseil de la Facult� des �tudes sup�rieures et postdoctorales";
      $liste['section3'] = "---------------------------------------------------";
      $liste['section4'] = ".....................<span style='font-family:Verdana; font-size:10pt;color:blue; font-weight:300;'>Conseil consultatif de l'�cole Polytechnique</span>";
      $liste['CEPOS'] = ".......... Cocep-comit� d'orientation strat�gique";
      $liste['CEPA'] = ".......... Cocep-tous les membres";
      $liste['CEPBIO'] = ".......... Cocep-biom�dical";
      $liste['CEPCH'] = ".......... Cocep-chimique";
      $liste['CEPCIV'] = ".......... Cocep-civil";
      $liste['CEPELE'] = ".......... Cocep-�lectrique";
      $liste['CEPGEO'] = ".......... Cocep-g�ologie";
      $liste['CEPINF'] = ".......... Cocep-informatique";
      $liste['CEPMGI'] = ".......... Cocep-MAGI";
	  $liste['CEPMEC'] = ".......... Cocep-m�canique";
      $liste['CEPMIN'] = ".......... Cocep-mines";
      
      $reqInstances = "SELECT * FROM sgInstances ORDER BY instance_nom ";
      $resInstances = $bd->execRequete($reqInstances);

      print(Html3("haut", "Instances - membres", "../../sg/css/instance.css" ));

      $aujourdhui = date("Y-m-d");

      $today = time();
      $today = $today - (10*24*60*60);  // aujourd'hui - 10 jours * 24h/jour * 60min./h * 60 sec./min.
      $limiteInferieure = date("Y-m-d", $today);
      $jour = date("Y-m-d");

      if(Verif_usager_unite_edition ($PHP_AUTH_USER, "nomi", "maj", $bd)) {
//      if (strstr(" p486735 p730063 ", $PHP_AUTH_USER)) {
         print(Imprime_titreListe("<a href='http://www.polymtl.ca/archives/membres/instances.php?action=notification'>Notification aux personnes ayant acc�s aux adresses compl�tes</a>", ""));
      }

      print("<table align='center'>\n");
      print("<tr><td align='center'><span style='font-family:Verdana; font-size:8pt;color:black; font-weight:700;'>Liste des instances et date de leur mise � jour sur le site web SG</b><br>(imprim&eacute;e le " . DateNormale(date("Y-m-d")) . ")<br>cliquez sur l'instance pour afficher la liste des membres<br><br>les instances modifi�es dans les <span style='background-color:red; color:white;font-size:11pt; '>10</span> derniers jours sont en <span style='background-color:red; color:white;;font-size:11pt;  '>rouge</span><br><br>les instances modifi�es <span style='background-color:yellow; color:black;;font-size:11pt; '>aujourd'hui</span> sont en <span style='background-color:yellow; color:black;;font-size:11pt; '>jaune</span></span></td></tr>");
      print("<tr><td>&nbsp;</td></tr>\n");

      while ($instance = $bd->objetSuivant($resInstances)) {
		if ($instance->instance_pweb_mem == $jour) {
		   $liste[$instance->instance_id] = '<span style="font-family:Verdana; font-size:9pt; background-color:yellow; color:black; font-weight:700;">' . $instance->instance_pweb_mem . " &nbsp; &nbsp; " . $liste[$instance->instance_id] . "</span>\n";
		} elseif ($instance->instance_pweb_mem > $limiteInferieure) {
		   $liste[$instance->instance_id] = '<span style="font-family:Verdana; font-size:9pt; background-color:red; color:white; font-weight:700;">' . $instance->instance_pweb_mem . " &nbsp; &nbsp; " . $liste[$instance->instance_id] . "</span>\n";
        } else {
           if(array_key_exists($instance->instance_id, $liste)) {
		      $liste[$instance->instance_id] = '<span style="font-family:Verdana; font-size:10pt; font-weight:300;">' . $instance->instance_pweb_mem . " &nbsp; &nbsp; " . $liste[$instance->instance_id] . "</span>";
	       }
	    }
     }

     foreach($liste as $key=>$value) {
	    if (trim(substr($value, 10)) != "" ) {
		   $noLigne++;      
		   $couleur = (($noLigne % 2) == 0) ? $couleur = "EBEBEB" : $couleur = "f9f9f9";
		   print("\n\n" . '<tr bgcolor="#' . $couleur . '"><td><a href="instances_adr_c.php?instance_id=' . $key . '&formeDe=liste">' .$value . '</a></td></tr>');
	    }
     }
     print("</table>\n");
     print(Html3("bas"));
     break; 

   case "notification" : //-----------------------------------------
      
	  if($pArch == FALSE) {
        Html_non_acces();
        exit();
	  }
      print(Html3("haut", "maj membres des instances", "../css/gesdep.css"));
	  include("../mnuar.php");
	  print("</table><br><table width='680' border='1' align='center' bgcolor='EBEBEB' cellspacing='0'><tr><td>\n");
	  print(Imprime_titreListe("Envoi de courriels notifiant maj des membres des instances", "titre"));
	  $f = new Formulaire ("POST", "instances.php");
	  $f->debutTable(HORIZONTAL);
	  $f->champValider ("maj-memad", "action");
	  $f->finTable();
	  $f->fin();
	  print("</td></tr></table>\n");
	  print(Html3("bas"));
	  break;
		
   case "maj-memad" ://-------------------------------------------------------------
			
	  $msgDebut = "";
	  $msgDebut .= "Bonjour<br />\n\n";
	  $msgDebut .= "Certaines listes des membres actifs des instances<br />\n";
	  $msgDebut .= "ont �t� mises � jour sur le site web.<br /><br />\n\n ";
	  $msgDebut .= "Les instances ayant �t� mises � jour <br />\n";
	  $msgDebut .= "dans les derniers --> 10 jours <-- sont<br />\n";
	  $msgDebut .= "pr�sent�es en gras de couleur rouge<br />\n";
	  $msgDebut .= "sur le lien suivant : <br /><br />\n\n";

	  $msgMilieu = "";
	  $msgMilieu .= "  http://www.polymtl.ca/archives/membres/instances.php?action=affichage-sg<br /><br />\n\n";

   	  $msgMilieu .= "Pierre Lavigne, webmestre du site du Secr�tariat g�n�ral";

	  $msgFinal = $msgDebut . $msgMilieu;

	  $titreFinal = "Maj des listes des membres actifs des instances";

	  $msgFinal_br = $msgFinal;
      $msgFinal_br = ereg_replace("\n", "<br>", $msgFinal);

	  print(Html3("haut" , "Maj des listes des membres actifs des instances", "../css/gesdep.css"));
	  include("../mnuar.php");
	  print($msgFinal_br);

	  print("</table><br><table width='680' border='1' align='center' bgcolor='EBEBEB' cellspacing='0'><tr><td>\n");
	  $f = new Formulaire ("POST", "instances.php");
	  $f->debutTable(HORIZONTAL);
	  $f->champValider ("envoyer les courriels", "action");
	  $f->champCache(msgFinal, $msgFinal);
	  $f->champCache(titreFinal, $titreFinal);
	  $f->champCache(instance, $instance);
	  $f->finTable();
	  $f->fin();
	  print("</td></tr></table>\n");
	  print(Html3("bas"));
	  break;
	
   case "envoyer les courriels" : //----------------------------------------------------

	  $organisme = "Archives Polytechnique" ;
	  $emetteur = "pierre.lavigne@polymtl.ca";
	  $header = "MIME-Version : 1.0\r\r\n";
      $header .= "Content-type: text/html; charset=iso-8859-1\r\r\n";   
	  $header .= "From: $organisme <$emetteur>\r\r\n";
  	  $msgFinal = stripslashes($msgFinal);
	  $titreFinal = stripslashes($titreFinal);

	  $bd = NEW BD(USAGER, PASSE, BASE, SERVEUR);

	  $reqMemad = "SELECT * FROM usagers_acces as usa, usagers as us "
               . " WHERE notif_pv > '0'  "
               . " AND no_unite LIKE '%memad%' "
		       . " AND us.no_usager = usa.no_usager "
               . " ORDER BY nomPrenom_usager ";
	  $resultat = $bd->execRequete($reqMemad);

	  while ($ligne = $bd->objetSuivant($resultat)) {

		$msgFinal2 = $msgFinal . $msgFin;
       	mail($ligne->email_usager, $titreFinal, $msgFinal2, $header,"-f $emetteur");
	}
	$bd->quitter();
	$url = "Location: http://www.polymtl.ca/archives";
	header($url);
	exit();
	break;
}

?>