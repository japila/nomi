<?php

/*==========================================================================

« ar/membres/instances_adr_c.php »

Application : affichage des noms et qualité des membres des instances (public)

Programme fait par Pierre Lavigne
Dernière mise à jour : 2006-12-01

Tables utilisées : 

Fichiers texte utilisés : 

Accès au programme : portail SG général

============================================================================
*/

	require_once("archives/Table2.php");
	require_once("archives/Formulaire2.class.php");
	require_once("archives/fonctions.php");
	require_once("archives/Connexion.php");
	require_once("archives/ExecRequete.php");
	require_once("archives/defini.php");
	require_once("archives/u111/person/fonctions_mem.php");
	require_once("archives/apdf/PDF_Label.php");
	require_once("archives/BD.class.php");

	$bd = NEW BD (USAGER, PASSE, BASE, SERVEUR);


//=================================================

function Verif_resultat ($resultat) {

	if (mysql_num_rows($resultat) == 0) {
		print(Entete());
		print("<br /><br /><span class=\"titre2\">&nbsp;D&eacute;sol&eacute;, cette liste n'est pas disponible pour l'instant&nbsp;</span>");
		print("</body></html>");
		exit();
	}
}//----------------------------------------------------------

function Entete() {

	$msg = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \n\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
	$msg .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
	$msg .= "<head>\n";
	$msg .= "  <title>Polytechnique - Membres d'une instance</title>\n";
	$msg .= "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n";
	$msg .= "  <link rel=\"stylesheet\" href=\"http://www.polymtl.ca/sg/css/instance.css\" type=\"text/css\" />\n";
        $msg .= "  <style type=\"text/css\">";
        $msg .= "  <!-- ";
        $msg .= "    SELECT {font-family:Arial; font-size:13px;}";
        $msg .= "    TD {font-family:Arial; font-size:11px;}";
        $msg .= "  --> ";
	$msg .= "  </style>";
	$msg .= "</head>\n";
	$msg .= "<body>\n";
	return $msg;
} // -----------------------------------------------------

if(!Verif_usager_unite ($PHP_AUTH_USER, "memad", $bd)) {
   print(Html_non_acces ($choix = "non-modif"));
   $bd->quitter();
   exit();
}

if(!isset($instance_id)) {

   $listeInstances = ComboInstances_membre2 ();
	$formes['liste'] = "de liste à l'écran";
	$formes['publipostage'] = "publipostage (instance.csv)";
	$formes['étiquettes'] = "d'étiquettes (Avery 05159)";
	$formes['étiquettesSG2'] = "d'étiquettes (Sec.gén. Lj II)";
	$formes['étiquettesSG1200'] = "d'étiquettes (Sec.gén. Lj 1200)";
	$formes['fiches'] = "de fiches individuelles";
	if(strstr(" p486735 p301454 ", $PHP_AUTH_USER)) {
	   $formes['courriel'] = "de courriel à définir";
	}

	if(strstr(" p150705 p486735  p900884 p790142 p730063 ", $PHP_AUTH_USER)) {
	   $formes['membres-postes'] = "postes des membres";
	}



    $trialpha = "non";
	$f = new Formulaire ("POST", $PHP_SELF);
	$f->debutTable(HORIZONTAL);
    $f->champListe("Choisissez une instance", instance_id , $instance_id, 1,    $listeInstances);
	$f->champListe("produire sous forme ", formeDe, $formeDe, 1, $formes);
	$f->champValider ("go", "action");
    $f->finTable();
	$f->debutTable(HORIZONTAL);
	$f->champTexte("étiquettes :<br>no de ligne (1 &agrave; 7)", ligneimp, "1", 10,10) ;
	$f->champTexte("étiquettes :<br>no de colonne (1 ou 2)", colimp, "1", 10, 10) ;
	if($PHP_AUTH_USER == "p486735")
        $f->champTexte("tri alpha(oui-non)<br>liste seulement", trialpha, $trialpha, 5, 5) ;
	$f->finTable();
	$f->fin();

	$xhtml = Entete();
	$xhtml .= Menu_membre();

	$xhtml .= "<br /><table border=\"0\" bgcolor=\"#EBEBEB\" align=\"center\"><tr><td align='center'>\n";
	$xhtml .= "<a href='instances.php'>Liste des instances et de leur date de mise &agrave; jour</a></td></tr><tr><td>\n";
	$xhtml .= Imprime_titreListe("Liste des membres actifs d'une instance", "titreNavy");
    $xhtml .= $f->Get_formulaire();
 	$xhtml .= "</td></tr></table>\n";
	$xhtml .= "</body>\n";
	$xhtml .= "</html>";
	print($xhtml);

	if (strstr(" p486735 p301454 " , $PHP_AUTH_USER)) {
		print(Imprime_operation3("<a href='http://www.polymtl.ca/archives/u111/person/majCEPA.php'>regénérer Cocep tous les membres</a><br><br><a href='http://www.polymtl.ca/rensgen/repertoires/instance.php'>pour imprimer les listes mais <b>SANS</b> les adresses compl&egrave;tes et les nos de t&eacute;l&eacute;phone</a>"));
	}

} else {

	$connexion = Connexion(USAGER, PASSE, BASE, SERVEUR);
	
	$requete = "SELECT instance_nom, instance_pweb_mem, instance_notes " 
	             . " FROM sgInstances "
					 . " WHERE instance_id = '$instance_id' ";

	$resultat2 = ExecRequete($requete, $connexion);

	$ligne = LigneSuivante($resultat2);
	$instance = $ligne->instance_nom;
	$dateMaj = DateNormale($ligne->instance_pweb_mem);
	$instance_notes = $ligne->instance_notes;

	switch ($formeDe) {

		case "membres-postes" :

            $url = "Location: http://www.polymtl.ca/archives/membres/mem_postes.php";
		    header($url);
		    exit();
			break;

		case "courriel" :

           $url = 	"Location: http://www.polymtl.ca/archives/u111/person/cepaEmail.php?instance_id=$instance_id";
		   header($url);
		   exit();
		   break;

		case "fiches" :
			
			$impression = 0;
			$requete = "SELECT * "
	             . " FROM cPerson as cP, sgiPostes as p, sgiAffec as a "
					 . " WHERE a.instance_id = '$instance_id' "
					 . "     AND a.sgiPoste_id  = p.sgiPoste_id "
					 . "     AND a.cPerson_id = cP.cPerson_id ";
			switch ($instance_id) {
				case "CEPA" : 
					$requete .=  "     AND p.sgiPoste_no > -1 ";
					break;
				default :
					$requete .=  "     AND p.sgiPoste_no > 0 ";
					break;
			}
			$requete .=  " ORDER BY a.sgiPoste_no, cP.nom, cP.prenom ";

			$resultat = ExecRequete($requete, $connexion);
			Verif_resultat ($resultat);

			$html .= Entete();

			while ($ligne = LigneSuivante($resultat)) {

			if ( ($ligne->date_depart == "0000-00-00")  
						  OR ($ligne->date_depart == "") ) {

				if ($impression == 1)
					$html .= "\n<div style='page-break-before: always;'>\n";
	
				$html .= "<table cellpadding=\"5\">\n";

				$html .= "  <tr>\n    <td colspan=\"2\" align=\"center\">\n      <b>$instance :</b> mise à jour des informations sur un membre\n    </td>\n  </tr>\n";

				$html .= "  <tr><td colspan=\"2\">\n        &nbsp; &nbsp;</td></tr>\n";

				$appel = substr($ligne->appel . "\n             &nbsp; " . str_repeat("_", 80)	, 0, 80);	
				$html .= "  <tr><td align=\"right\">appellation</td><td>$appel</td></tr>\n";

				$prenom = substr($ligne->prenom . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">prénom</td><td>$prenom</td></tr>\n";

				$nom = substr($ligne->nom . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">nom</td><td>$nom</td></tr>\n";

				$titre1 = substr($ligne->titre1 . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">titre1</td><td>$titre1</td></tr>\n";

				$titre2 = substr($ligne->titre2 . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">titre2</td><td>$titre2</td></tr>\n";

				$organisme = substr($ligne->organisme . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">organisme</td><td>$organisme</td></tr>\n";

				$direction = substr($ligne->direction . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">direction</td><td>$direction</td></tr>\n"; 
			
				$service = substr($ligne->service . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">service</td><td>$service</td></tr>\n";

				$adr1 = substr($ligne->adr1 . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">adr1</td><td>$adr1</td></tr>\n";

				$adr2 = substr($ligne->adr2 . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">adr2</td><td>$adr2</td></tr>\n";

				$ville = substr($ligne->ville . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">ville</td><td>$ville</td></tr>\n";

				$province = substr($ligne->province . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .="  <tr><td align=\"right\">province</td><td>$province</td></tr>\n";

				$pays = substr($ligne->pays . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">pays</td><td>$pays</td></tr>\n";

				$codep = substr($ligne->codep . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">code postal</td><td>$codep</td></tr>\n";

				$telbur = substr($ligne->telbur . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">tél.bur</td><td>$telbur</td></tr>\n";

				$telfax = substr($ligne->telfax . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">tél.fax</td><td>$telfax</td></tr>\n";

				$telres = substr($ligne->telres . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">tél.res</td><td>$telres</td></tr>\n";

				$telcell = substr($ligne->telcell . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">tél.cell</td><td>$telcell</td></tr>\n";

				$telpagette = substr($ligne->telpagette . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">pagette</td><td>$telpagette</td></tr>\n";

				$courriel = substr($ligne->courriel . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\">courriel</td><td>$courriel</td></tr>\n";

				$notes = substr($ligne->cPerson_notes . "\n              &nbsp; " . str_repeat("_", 80), 0 , 80);	
				$html .= "  <tr><td align=\"right\" valign='top'>notes</td><td>$notes<br  /><br />\n" . str_repeat("_", 60) . "</td></tr>\n";
				
				$html .= "</table>\n\n";
								
				$person_id = $ligne->cPerson_id;

				$connexion = Connexion(USAGER, PASSE, BASE, SERVEUR);
				$requete = "SELECT * FROM cPerson as cp, sgiAffec as affec, "
							. "   sgInstances as inst "
							. " WHERE cp.cPerson_id = '$person_id' "
							. "    AND  cp.cPerson_id = affec.cPerson_id "
							. "    AND  inst.instance_id = affec.instance_id "
							. "    AND  affec.date_depart = '0000-00-00' "
							. "    AND  affec.sgiPoste_no > 0 ";

				$membres = ExecRequete($requete, $connexion);

				if (mysql_num_rows($membres) != 0) {
					$html .= "<br />\n<table>\n  <tr>\n      <td align='center' style='font-size:10pt;'>\n        <u><b>Membre actif des instances suivantes</b></u><span style='font-size:8pt;'>\n         ( depuis le  aaaa-mm-jj )</span><br /><br />\n      </td>\n  </tr>\n";
		
					while ($unMembre = LigneSuivante($membres)) { 
						$html .= "  <tr><td><span style='font-size:8pt;'>\n      &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;\n      " . $unMembre->instance_nom . " &nbsp; &nbsp; &nbsp; \n        ( ";
						if ($unMembre->date_arrivee != "0000-00-00") {
							$html .=  $unMembre->date_arrivee . " ) &nbsp; _______________________</span><br /></td></tr>\n";
						} else {
							$html .= " _______________ )</td></tr>\n";
						}
					}
					$html .= "</table>\n";
				}
				if ($impression == 1) 
					$html .= "</div>\n";
				else
					$impression = 1;
				
			}
			}
			
			$html .= "</body>\n";
			$html .= "</html>\n";

			$htmlFinal .= Ansi2iso ($html);
			print($htmlFinal);
			$pointcher = "fiches individuelles";
			break;

		case "liste" :

			

			$requete = "SELECT * "
	             . " FROM cPerson as cP, sgiPostes as p, sgiAffec as a "
					 . " WHERE a.instance_id = '$instance_id' "
					 . "     AND a.sgiPoste_id  = p.sgiPoste_id "
					 . "     AND a.cPerson_id = cP.cPerson_id ";
//					 ."      AND a.;
			switch ($instance_id) {
				case "CEPA" : 
					$requete .=  "     AND p.sgiPoste_no > -1 ";
					break;
				default :
//					$requete .=  "     AND p.sgiPoste_no > 0 ";
					break;
			}

			if ($trialpha == "oui") 
				 $requete .=  "  ORDER BY cP.nom, cP.prenom ";
			else 
			     $requete .=  " ORDER BY a.sgiPoste_no, cP.nom, cP.prenom ";

			$resultat = ExecRequete($requete, $connexion);
			Verif_resultat ($resultat);

			$html = "";
			$html .= Entete();

			$html .= "\n\n<table width=\"650\" align=\"center\">\n";
			$html .= "<tr>\n";
			$html .= "   <td><img src=\"../../../images/logopoly2.gif\" width=\"175\" height=\"75\"\n                  alt=\"Ecole Polytechnique de Montreal\" /></td>\n";
			$html .= "   <td align=\"center\">\n";
			$html .= "      <br /><h2>$instance</h2>\n";
			$html .= "      <h3>Liste des membres au $dateMaj</h3>\n";
			$html .= "   </td>\n";
			$html .= "</tr>\n";
			$html .= "<tr><td colspan=\"2\"><hr width=\"620\" /></td></tr>\n";
			$html .= "</table>\n\n";

			$html .= "<table width=\"650\" align=\"center\"><tr><td>\n";

			for ($indx = 1; $indx < 15; $indx++)
				$posteVacant[$indx] = 0;

			$invites = "";

			while ($ligne = LigneSuivante($resultat)) {

				$sans_no_poste = (strstr(" CPROM CPROB CPRESS AUUDEM CFESP CCE CAPP CGRI CRP ", $ligne->instance_id)) ? TRUE : FALSE;

//				$html .= "\n<table width=\"620\" border=\"0\"  cellspacing=\"0\" align=\"center\"><tr><td>\n";		
				
		
				switch ($ligne->appel) {

					case "Monsieur" :
							$appellation = "M. ";
							break;
	
					case "Madame" :
							$appellation = "Mme ";
						break;
				}

                $ligne->prenom = stripslashes($ligne->prenom);
                $ligne->nom = stripslashes($ligne->nom);

				$posteno = "";
				if($ligne->sgiPoste_no > 0) $posteno = $ligne->sgiPoste_no . " - "; 

				if( ($ligne->sgiPoste_no > 11) AND (strtolower($ligne->instance_id) == "cad") ) $posteno = "";
				if( ($ligne->sgiPoste_no > 5) AND (strtolower($ligne->instance_id) == "cex") ) $posteno = "";
				if( ($ligne->sgiPoste_no > 3) AND (strtolower($ligne->instance_id) == "cgo") )   $posteno = "";
				if( ($ligne->sgiPoste_no > 3) AND (strtolower($ligne->instance_id) == "cve") )   $posteno = "";

				$poste_nom = "";
				$paren = strpos($ligne->sgiPoste_nom, "(");
				if ($paren) {
					$poste_nom = substr($ligne->sgiPoste_nom, 0, $paren-1);
				} else {
					$poste_nom = $ligne->sgiPoste_nom;
				}
				$poste_nom = stripslashes($poste_nom);
		
				$ods = "";
				if (trim($ligne->organisme) != "") $ods .= $ligne->organisme;
				if (trim($ligne->direction) != "") $ods .= " - " . $ligne->direction;
				if (trim($ligne->service) != "") $ods .= " - " . $ligne->service;
				$ods = stripslashes($ods);

                $titres = "";
                if (trim($ligne->titre1) != "") $titres .= $ligne->titre1;
				if (trim($ligne->titre2) != "") $titres .= " - " . $ligne->titre2;
				$titres = stripslashes($titres);

                $adrs = "";
                if (trim($ligne->adr1) != "") $adrs .= $ligne->adr1;
				if (trim($ligne->adr2) != "") $adrs .= " - " . $ligne->adr2;
				$adrs = stripslashes($adrs);

				$mandatno = "";
				if ($ligne->mandat_no > 1) $mandatno = "mandat no " . $ligne->mandat_no . " : ";

				if ( ($ligne->date_arrivee == "9999-99-99") OR ($ligne->date_arrivee == "0000-00-00") ) {
					$dateEnfonction = "...";
				} elseif (trim($ligne->date_arrivee) != "") {
					$dateEnfonction = $ligne->date_arrivee;
				}

				if (trim($ligne->date_fin) != "0000-00-00") {
					$dateFinfonction = $ligne->date_fin;
				} else {
					$dateFinfonction = "...";
				}

				$dates_inclus = "(" . $mandatno . $dateEnfonction . " au " . $dateFinfonction . ")";

				$teles = "t&eacute;l. :";
				if (trim($ligne->telbur) != "") $teles .= "&nbsp; bur: " . $ligne->telbur;
				if (trim($ligne->telfax) != "") $teles .= " &nbsp; fax: " . $ligne->telfax;
				if (trim($ligne->telres) != "") $teles .= " &nbsp; res: " . $ligne->telres;
				if (trim($ligne->telcell) != "") $teles .= " &nbsp; cell: " . $ligne->telcell;

				$courriel = "";
				if (trim($ligne->courriel) != "") $courriel = "<a href=\"mailto:" . $ligne->courriel . "\">" . $ligne->courriel . "</a>";

				$notes = "";
				if (trim($ligne->cPerson_notes) != "") $notes = stripslashes($ligne->cPerson_notes);
		
				$province = "";
				if (trim($ligne->province) != "") $province = "(" . $ligne->province . ")";

				if (strpos($ligne->sgiPoste_nom, "(vacant)")) {
					if ($posteVacant[$posteno] != 1) {
					   $posteVacant[$posteno] = 1;
   					   if($sans_no_poste) $posteno = "";
					   $html .= "\n<div class=\"membre\">\n";		
					   $html .= "   <span class=\"petit\"><span class=\"fili\">" . $posteno;
					   $html .= $poste_nom . " </span>&nbsp; " ;   
					   $html .= "            poste vacant</span>" ;   
					   $html .= "\n</div>\n"	;

					}
				} else {

//				   $auJour = "2005-06-01"; //  pour des tests
//				   if ( ($ligne->date_depart == "0000-00-00")  OR ($ligne->date_depart == "") ) {  // ancienne commande

				   $auJour = date("Y-m-d"); // date réelle aujourd'hui
				   if ( (($ligne->date_depart == "0000-00-00")  OR ($ligne->date_depart == "") OR ($ligne->date_depart >= $auJour))         AND ($ligne->date_arrivee <=  $auJour) ) {   //=======================


				         if($ligne->sgiPoste_no == 0) { // pour les personnes invitées
                            $invites .= "   <br />&nbsp;&nbsp;&nbsp;<strong>" . $appellation . " " . $ligne->prenom . " " . $ligne->nom . "</strong>";
                            $invites .= ",&nbsp;" . strtolower($titres); 

                            if(trim($ods) != "") {
							    $invites .= "   <br />&nbsp;&nbsp;&nbsp;" . $ods;
						    } 
                       
							if(trim($adrs) != "") {
							    $invites .= "   <br />&nbsp;&nbsp;&nbsp;" . $adrs; 
						    } 
							 if(trim($teles) != "") {
							    $invites .= "   &nbsp;&nbsp;&nbsp;" . $teles; 
						    } 

							 if(trim($courriel) != "") {
							    $invites .= "   &nbsp;&nbsp;&nbsp;" . $courriel; 
						    } 

						    if(trim($notes) != "") {
							    $invites .= "   <br />&nbsp;&nbsp;&nbsp;" . $notes; 
						    } 
							$invites .= "<br />";

						 } else {

				           if( (($ligne->sgiPoste_no == 20) AND (strtolower($ligne->instance_id) == "cad")) OR 
							   (($ligne->sgiPoste_no == 6) AND (strtolower($ligne->instance_id) == "cex")) OR
							   (($ligne->sgiPoste_no == 4) AND (strtolower($ligne->instance_id) == "cgo")) OR
   							   (($ligne->sgiPoste_no == 4) AND (strtolower($ligne->instance_id) == "cve")) 
							 ) {
                             $html .= "<br />&nbsp;&nbsp;&nbsp;" . str_repeat("=", 27) . " O F F I C I E R (S) &nbsp; D E &nbsp; L A &nbsp; C O R P O R A T I O N &nbsp;" . str_repeat("=", 28) ;
						   }

						   $html .= "\n<div class=\"membre\">\n";		
						   if ($posteno != "") {
                               if($sans_no_poste) $posteno = "";
							   $html .= "   <span class=\"petit\">\n     <span class=\"fili\">" . $posteno;
							   $html .= $poste_nom . "\n     </span>\n" ;   
							   $html .= "     " . $dates_inclus . "\n   </span><br />\n";
						   }
						   $html .= "   <strong>" . $appellation . " " . $ligne->prenom . " " . trim($ligne->nom);
						   $html .= "</strong>\n";
			
						   if (trim($titres) != "") {
							   $html .= ",&nbsp;" . strtolower($titres) . "\n";  
						   }

						   if ($ods != "") {
							   $html .= "   <br />" . $ods . "\n";
						   }
		
		                   
						   if (trim($adrs) != "") {
							   $html .= "   <br />" . $adrs . "\n";
						   }

						   if (trim($ligne->ville) != "") {
							   $html .= "   <br />" . $ligne->ville . "  &nbsp; " . 	$province . " &nbsp; " . $ligne->pays . " &nbsp;" . $ligne->codep . "\n"; 
						   }

						   $html .= "   <br />" . $teles . "\n"; 

						   if ($courriel  != "") {
							   $html .= "   &nbsp;&nbsp;&nbsp;" . $courriel . "\n"; 
						   }

						   if ($notes != "") {
							  $html .= "   <br />" . $notes . "\n";
						   }
						   $html .= "</div>\n";

					   } // fin pour postes autre que le poste no 0

					}  // fin si dates valides
                    
				}  // fin si non-vacant
                
			}  // while nomination à faire
			$html .= "<br />\n";

			if(trim($invites) != "") {
               $html .= "<br />&nbsp;&nbsp;&nbsp;" . str_repeat("=", 35) . " P E R S O N N E S &nbsp; I N V I T É E S " . str_repeat("=", 35) . "<br />";
			   $html .= $invites;
               $html .= "<br />";
			}

			if (trim($instance_notes) != "") {
				$html .= "\n<table width=\"620\" border=\"1\"  cellspacing=\"0\" align=\"center\" 	bgcolor=\"EBEBEB\">\n";		
				$html .= "<tr><td align=\"center\">\n";
				$html .= $instance_notes;
				$html .= "</td></tr></table>\n";
			}
			
			$html .= "\n<hr width=\"620\" />\n\n";
			$html .= "</td></tr></table>\n\n";

			$html .= "<p class=\"basPage\">\n";
			$html .= "   Site web du Secrétariat général &nbsp; -- &nbsp; \n   page mise &agrave; jour par le <a href=\"mailto:pierre.lavigne@polymtl.ca\">Bureau des archives</a>\n &nbsp; &nbsp; &nbsp;\n   [ <a href=\"" . $HTTP_REFERER . "\">page précédente </a>]";
			$html .= "\n</p>\n\n\n";

			$html .= "</body>\n";
			$html .= "</html>\n";
	   		$pointcher = "liste mem " . $instance_id;		

			$htmlFinal .= Ansi2iso ($html);
			print($htmlFinal);
			break;

		case "publipostage" :

			$fichiercsv = $instance_id . ".csv";
			header("Content-type: text/x-csv");
			header("Content-disposition: attachement; filename=\"$fichiercsv\"");

			print("nom;prenom;appel;appelabrege;invite;titre1;titre2;organisme;direction; service;adr1;adr2;ville;province; pays;codep;telbur;telres;telfax; telcell;telpagette;courriel\n");

			$requete = "SELECT * "
			             . " FROM cPerson as cP, sgiPostes as p, sgiAffec as a "
						 . " WHERE a.instance_id = '$instance_id' "
						 . "     AND a.sgiPoste_id  = p.sgiPoste_id "
						 . "     AND a.cPerson_id = cP.cPerson_id ";
			switch ($instance_id) {
				case "CEPA" : 
					$requete .=  "     AND p.sgiPoste_no > -1 ";
					break;
				default :
					$requete .=  "     AND p.sgiPoste_no > 0 ";
					break;
			}
			$requete .=  " ORDER BY a.sgiPoste_no, cP.nom, cP.prenom ";
			$resultat = ExecRequete($requete, $connexion);

			while ($ligne = LigneSuivante($resultat)) {
	
				$presence = $ligne->presence;
				$role = $ligne->role;
				$date_reponse = $ligne->date_reponse;
				$cPerson_id = $ligne->cPerson_id;

				$reqPerson = "SELECT * FROM cPerson WHERE cPerson_id = 	'$cPerson_id' ";

				$resPersons = ExecRequete($reqPerson, $connexion);

				$unePerson = LigneSuivante($resPersons);

				$appel = $unePerson->appel;
				$appelabrege = $unePerson->appel;

				if ($appel == "M.") {
					$appel = "Monsieur";
					$invite = "invité";
				}
				if ($appel == "Mme") {
					$appel = "Madame";
					$invite = "invitée";
				}
				if ($appelabrege == "Monsieur") {
					$appelabrege = "M.";
					$invite = "invité";
				}
				if ($appelabrege == "Madame") {
					$appelabrege = "Mme";
					$invite = "invitée";
				}	

				if  ($ligne->date_depart == "0000-00-00")  
					print($unePerson->nom . ";" . $unePerson->prenom . ";" . $appel . ";" . $appelabrege . ";" . $invite . ";" .$unePerson->titre1 . ";" . $unePerson->titre2 . ";" . $unePerson->organisme . ";"	. $unePerson->direction . ";" . $unePerson->service . ";" . $unePerson->adr1 . ";" . $unePerson->adr2 . ";"	. $unePerson->ville . ";" . $unePerson->province . ";"	. $unePerson->pays . ";" . $unePerson->codep . ";"	. $unePerson->telbur . ";" . $unePerson->telres . ";"	. $unePerson->telfax . ";" . $unePerson->telcell . ";"	. $unePerson->telpagette . ";" . $unePerson->courriel . "\n");
			}
		   	$pointcher = "publipostage " . $instance_id;
			break;

		case "étiquettes" :
		case "étiquettesSG02" :
		case "étiquettesSG1200" :

			$requete = "SELECT * "
	             . " FROM cPerson as cP, sgiPostes as p, sgiAffec as a "
					 . " WHERE a.instance_id = '$instance_id' "
					 . "     AND a.sgiPoste_id  = p.sgiPoste_id "
					 . "     AND a.cPerson_id = cP.cPerson_id ";
//					 . " ORDER BY a.sgiPoste_no, cP.nom, cP.prenom ";
	        if ($trialpha == "oui") 
				 $requete .=  "  ORDER BY cP.nom, cP.prenom ";
			else 
			     $requete .=  " ORDER BY a.sgiPoste_no, cP.nom, cP.prenom ";



			$resultat = ExecRequete($requete, $connexion);
			Verif_resultat ($resultat);

			define('FPDF_FONTPATH','../../../archives/apdf/font/');

			$ligneimp = (int)($ligneimp);
			$colimp = (int)($colimp);

			switch ($formeDe) {
				case "étiquettes" :
					$pdf = new PDF_Label('5162', $colimp, $ligneimp);
					$sgLj = 1;
					break;
				case "étiquettesSG2" :
					$pdf = new PDF_Label('5162sg2', $colimp, $ligneimp);
					$sgLj = 0;
					break;
				case "étiquettesSG1200" :
					$pdf = new PDF_Label('5162sg1200', $colimp, $ligneimp);
					$sgLj = 0;
					break;
			}
			
			$pdf->Open();
			$pdf->AddPage();
					
			while ($ligne = LigneSuivante($resultat)) {
				
				if  ($ligne->date_depart == "0000-00-00") {
					
					$eti = "";

					$appelabrege = "";
					switch ($ligne->appel) {
						case	"Monsieur" :
							$appelabrege = "M.";
							$invite = "invité";
							break;

						case "Madame" :
							$appelabrege = "Mme";
							$invite = "invitée";
							break;
					}	

					if (trim($appelabrege) != "") $eti .= $appelabrege . " ";
					if (trim($ligne->prenom) != "") $eti .= $ligne->prenom . " ";
					if (trim($ligne->nom) != "") $eti .= $ligne->nom;
					if ( ($ligne->sgiPoste_no == 0) AND ($ligne->instance_id != "CEPA") ) 
						if (trim($ligne->nom) != "") $eti .= "  ($invite)";
					$eti .= "\n";

					if (trim($ligne->titre1) != "") $eti .= $ligne->titre1 . "\n";
					if (trim($ligne->titre2) != "") $eti .= $ligne->titre2 . "\n";
					if (trim($ligne->organisme) != "") $eti .= $ligne->organisme . "\n";
					if (trim($ligne->direction) != "") $eti .= $ligne->direction . "\n";
					if (trim($ligne->service) != "") $eti .= $ligne->service . "\n";
					if (trim($ligne->adr1) != "") $eti .= $ligne->adr1 . "\n";
					if (trim($ligne->adr2) != "") $eti .= $ligne->adr2 . "\n";
					if (trim($ligne->ville) != "") $eti .= $ligne->ville;
					if (trim($ligne->province) != "") $eti .= "   (" . $ligne->province . ")";
					if (trim($ligne->pays) != "") $eti .= "      " . $ligne->pays;
					$eti .= "\n";
					$codePostal = $ligne->codep;
					if ($codePostal == "COURRIER INTERNE") $codePostal = "C O U R R I E R    I N T E R N E";
					if ($codePostal == "COURRIER CAMPUS") $codePostal = "C O U R R I E R    C A M P U S";

					if (trim($codePostal) != "") $eti .= $codePostal . "\n";
/*
------------------------------------ test d'impression ---------------------
					$eti = "M. Pierre Lavigne\nArchiviste\nPaleontographe\n";
					$eti .= "Ecole Polytechnique de Montréal\nDAISG\nBureau des archives\n";
					$eti .= "C.P. 6079\nsucc. Centre-ville\n";
					$eti .= "Montréal (Québec)\nH3C 3A7";
------------------------------------ fin test d'impression ---------------------			
*/
					if ( ($sgLj == 0) && (strpos($eti, "Louise Jolicoeur") ) ) {
//                     rien imprimer
					} else {
						$pdf->Add_PDF_Label($eti);
					}
				}
			}

			$pdf->Output();
	
			$pointcher = "étiquettes " . $instance_id;
			break;

	}  // --- fin du switch  $formeDe

	$application = "mem-" . $instance_id;
	$jour = date("Y-m-d");
	$heure = date("H:i");
	$totalTrouve = 1;

	$connexion = Connexion(USAGER, PASSE, BASE, SERVEUR);
	$requete = "INSERT INTO docs_stats "
		      . " (docs_stats_id, no_unite, application, date, heure, "
	          . " recherche, resultat, no_usager, adr_ip) "
	          . " VALUES (0, '000', '$application', '$jour', '$heure', '$pointcher', '$totalTrouve', "
	    	  . " '$PHP_AUTH_USER', '$REMOTE_ADDR')";
	$resultat = ExecRequete($requete, $connexion);

	mysql_close($connexion);   

}  // -----instance_id présent ou non

$bd->quitter();

?>
