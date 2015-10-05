<?php
	
/*==========================================================================

� ar/membres/mem_postes.php �

Application : affichage de nom, pr�nom, instance, no de poste, titre du poste, dates de fonctions pour les membre des instances

Programme fait par Pierre Lavigne

Derni�re mise � jour : 2008-09-16

Tables utilis�es :  sgiAffec, sgInstances, sgiPostes, cPerson"

Acc�s au programme : portail SG g�n�ral gestion des nominations des membres aux instances

============================================================================
*/

require_once("archives/BD.class.php");
require_once("archives/defini.php");
require_once("archives/fonctions.php");
require_once("archives/Formulaire.class");
require_once("archives/Table.php");

//=================================================

$bd = NEW BD (USAGER, PASSE, BASE, SERVEUR);

$css=<<<EOD
           <style type="text/css">
           <!--
	           a:link {font-color:blue;}
   	           a:visited {font-color:blue;}
               TD, TH {font-family:Verdana; font-size:9pt;}
           -->
          </style
EOD;

	       print(Html3("haut", $action, $css));
          
		   if(!isset($instances)) {
		      $instances = "cad cex add cco cac cet cre cai cve";
		   }
		   $f = new Formulaire ("POST", "mem_postes.php", FALSE, "Form");  
	       $f->debutTable(HORIZONTAL);
//		   $instances = "cad cex add cco cac cet cre cai cve";
           $f->champTexte("entrez une ou plusieurs instances (s�par�es par un espace)&nbsp; &nbsp;<a href='http://www.polymtl.ca/archives/membres/instances_adr_c.php' style='color:blue;'>retour � mem</a><br><span style='font-size:smaller; font-weight:normal;'>enlever, ci-dessous, les instances qui ne sont pas n�cessaires </span>", instances, $instances, 85, 85);
           $f->champValider ("mem-postes", "action");
           $f->finTable();
           $f->fin();

	      $reqSel = "SELECT cp.nom, "
		              . "           cp.prenom, "
					  . "           ins.instance_id, "
					  . "           pos.sgiPoste_no, "
		              . "           pos.sgiPoste_nom, "
					  . "           aff.date_arrivee, "
					  . "           aff.date_depart "
                      . " FROM   sgiAffec as aff, "
					  . "           sgInstances as ins, "
					  . "           sgiPostes as pos, "
					  . "           cPerson as cp "
                      . " WHERE aff.sgiPoste_id = pos.sgiPoste_id "
					  . "     AND aff.instance_id = ins.instance_id "
					  . "     AND aff.cPerson_id = cp.cPerson_id "
					  . "     AND aff.instance_id = ins.instance_id "
                      . "     AND INSTR('$instances', aff.instance_id) "
					  . " ORDER by cp.nom ASC, "
					  . "               cp.prenom, "
					  . "               date_arrivee ASC ";
		  
          $resSel = $bd->execRequete($reqSel);

          print("<table width='950' border='1' cellpadding='2' cellspacing='0'>");
		  print("   <tr>");
		  print("      <th>nom</th>");
		  print("      <th>pr�nom</th>");
		  print("      <th>ins</th>");
		  print("      <th>no</th>");
  		  print("      <th>poste nom</th>");
		  print("      <th>d�but</th>");
		  print("      <th>fin</th>");
  		  print("   </tr>");

		  $nomTemp = "xxxxx";

		  while($per= $bd->objetSuivant($resSel)) {
             $nomPre = $per->nom . $per->prenom;
             if($nomPre != $nomTemp) {
				 if($couleur == "#EBEBEB") {
					 $couleur = "#FFFFFF";
				 } else {
	                 $couleur = "#EBEBEB";
				 }
                 $nomTemp = $nomPre;
			 }
             $posteNom = stripslashes($per->sgiPoste_nom);
			 if ($posteNom == "Directeur du d�partement des g�nies civil, g�ologique et des mines")
				 $posteNom = "D�partement CGM";
 			 if ($posteNom == "Directeur du d�partement de g�nie informatique et g�nie logiciel")
				 $posteNom = "D�partement GIGL";

 			 if ($posteNom == "Directeur du d�partement de math�matiques et g�nie industriel")
				 $posteNom = "D�partement MAGI";

			  print("   <tr bgcolor = '$couleur'>");
		     print("      <td>". stripslashes($per->nom) . "</td>");
		     print("      <td>". stripslashes($per->prenom) . "</td>");
		     print("      <td>". stripslashes($per->instance_id). "</td>");
		     print("      <td>". $per->sgiPoste_no . "</td>");
  		     print("      <td>". $posteNom . "</td>");
		     print("      <td>". $per->date_arrivee . "</td>");
		     print("      <td>". $per->date_depart . "</td>");
		     print("   </tr>");
		  }
		  print("</table>");
	      print(Html3("bas"));
?>	