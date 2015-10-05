<?php

class PAFFEC {

// --- Partie privée : les variables

    var $champs, $valeurs, $champsValeurs, $idx, $nomTable;

    var $paffec_id, $poste_id, $usager_id, $paffec_mandatNo, $paffec_actif, $paffec_vacant, $paffec_debut, $paffec_arrivee, $paffec_fin, $paffec_depart, $sgaffec_raisonFin, $paffec_relance, $paffec_decret, $paffec_notes;

    var $enregis = array("poste_id", "usager_id", "paffec_mandatNo", "paffec_actif", "paffec_vacant", "paffec_debut", "paffec_arrivee", "paffec_fin", "paffec_depart", "sgaffec_raisonFin", "paffec_relance", "paffec_decret", "paffec_notes");

    /* fonctions accesseurs */

    function set_poste_id ($poste_id) { $this->poste_id = Clean($poste_id); }
    function get_poste_id () { return $this->poste_id; }

    function set_usager_id ($usager_id) { $this->usager_id = Clean($usager_id); }
    function get_usager_id () { return $this->usager_id; }

    function set_paffec_mandatNo ($paffec_mandatNo) { $this->paffec_mandatNo = Clean($paffec_mandatNo); }
    function get_paffec_mandatNo () { return $this->paffec_mandatNo; }

    function set_paffec_actif ($paffec_actif) { $this->paffec_actif = Clean($paffec_actif); }
    function get_paffec_actif () { return $this->paffec_actif; }

    function set_paffec_vacant ($paffec_vacant) { $this->paffec_vacant = Clean($paffec_vacant); }
    function get_paffec_vacant () { return $this->paffec_vacant; }

    function set_paffec_debut ($paffec_debut) { $this->paffec_debut = Clean($paffec_debut); }
    function get_paffec_debut () { return $this->paffec_debut; }

    function set_paffec_arrivee ($paffec_arrivee) { $this->paffec_arrivee = Clean($paffec_arrivee); }
    function get_paffec_arrivee () { return $this->paffec_arrivee; }

    function set_paffec_fin ($paffec_fin) { $this->paffec_fin = Clean($paffec_fin); }
    function get_paffec_fin () { return $this->paffec_fin; }

    function set_paffec_depart ($paffec_depart) { $this->paffec_depart = Clean($paffec_depart); }
    function get_paffec_depart () { return $this->paffec_depart; }

    function set_sgaffec_raisonFin ($sgaffec_raisonFin) { $this->sgaffec_raisonFin = Clean($sgaffec_raisonFin); }
    function get_sgaffec_raisonFin () { return $this->sgaffec_raisonFin; }

    function set_paffec_relance ($paffec_relance) { $this->paffec_relance = Clean($paffec_relance); }
    function get_paffec_relance () { return $this->paffec_relance; }

    function set_paffec_decret ($paffec_decret) { $this->paffec_decret = Clean($paffec_decret); }
    function get_paffec_decret () { return $this->paffec_decret; }

    function set_paffec_notes ($paffec_notes) { $this->paffec_notes = Clean($paffec_notes); }
    function get_paffec_notes () { return $this->paffec_notes; }

          
//---- partie publique : les méthodes
          
    Function PAFFEC ($table) { // constructeur
          
          /*********************************************************
          liste des champs de l'enregistrement
          le champ « paffec_id » n'est pas inclus à cause de la
          fonction MAJ qui ne doit pas comprendre la clé primaire
          *******************************************************
          */
          
          $this->i = 0;
          
          foreach($this->enregis as $value) {
              if ($this->i == 0) {
                  $this->champs = $value ;
              } else {
                  $this->champs .= ", " . $value ;
              }
              $this->{$value} = "";
              $this->i++;
          }
          $this->nomTable = $table;
          return;
    }//---------------------- constructeur pour PAFFEC
          
    Function Imprimer_form ($css, $mode, $forme) {
          print('<table border="0" bgcolor="#EBEBEB" align="center"><tr><td>');
          print(Imprime_titreListe("titre de la liste", "titre"));
          print("<br>");
          $f = new Formulaire ("post", "paffecs.php", "", TRUE , $forme);
          $f->debutTable(HORIZONTAL);
                  $f->champTexte("$poste_id", "poste_id", $this->poste_id, 3, 58);
        $f->champTexte("$usager_id", "usager_id", $this->usager_id, 3, 58);
        $f->champTexte("$paffec_mandatNo", "paffec_mandatNo", $this->paffec_mandatNo, 3, 58);
        $f->champTexte("$paffec_actif", "paffec_actif", $this->paffec_actif, 3, 58);
        $f->champTexte("$paffec_vacant", "paffec_vacant", $this->paffec_vacant, 3, 58);
        $f->champTexte("$paffec_debut", "paffec_debut", $this->paffec_debut, 3, 58);
        $f->champTexte("$paffec_arrivee", "paffec_arrivee", $this->paffec_arrivee, 3, 58);
        $f->champTexte("$paffec_fin", "paffec_fin", $this->paffec_fin, 3, 58);
        $f->champTexte("$paffec_depart", "paffec_depart", $this->paffec_depart, 3, 58);
        $f->champTexte("$sgaffec_raisonFin", "sgaffec_raisonFin", $this->sgaffec_raisonFin, 3, 58);
        $f->champTexte("$paffec_relance", "paffec_relance", $this->paffec_relance, 3, 58);
        $f->champTexte("$paffec_decret", "paffec_decret", $this->paffec_decret, 3, 58);
        $f->champTexte("$paffec_notes", "paffec_notes", $this->paffec_notes, 3, 58);

          $f->finTable();
          $f->debutTable(HORIZONTAL);
          if($mode == "ajouter") {
              $f->champValider ("sauver", "action");
          } else {
              $f->champValider ("sauver", "action");
              $f->champValider ("sauver", "action");
          }
          $f->finTable();
          $f->champCache("paffec_id", $this->paffec_id);
          $f->fin();
          print('</td></tr></table>');
    }//--------
          
    Function Sauver ($bd) {
          $requete = "SELECT paffec_id FROM $this->nomTable WHERE paffec_id = '$this->paffec_id' ";
          $res = $bd->execRequete($requete);
          if (mysql_num_rows($res) > 0 )
              $this->Maj($bd);
          else
              $this->Inserer($bd);
    }//--------
          
    Function Inserer ($bd) {
          $this->i = 0;
          foreach($this->enregis as $value) {
              if ($this->i == 0) {
                  $this->valeurs = "'" . addslashes($this->{$value}) . "'";
              } else {
                  $this->valeurs .= ", '" . addslashes($this->{$value}) . "'";
              }
              $this->i++;
          }
          //----- clé primaire « paffec_id » placée au début des champs existants
          //----- « 0 » comme valeur correspondante dans les valeurs
          $this->champs = "paffec_id, " . $this->champs;     
          $this->valeurs = "0, " . $this->valeurs;
          $requete = "INSERT INTO $this->nomTable (" . $this->champs . ") VALUES (" . $this->valeurs . ")";
          $res = $bd->execRequete($requete);
          $this->paffec_id = mysql_insert_id();      //--- pour initialiser « paffec_id » dans l'objet après l'insertion dans la BD
          $this->operation = "INSERTION ok -- $this->paffec_id -- (enr. $this->paffec_id)";
          return;
    }//--------
          
    Function Maj ($bd) {
          $this->i = 0;
          foreach($this->enregis as $value) {
              if ($this->i == 0) {
                  $this->champsValeurs .= $value . " = '" . addslashes($this->{$value}) . "'";
              } else {
                  $this->champsValeurs .= ", " . $value . " = '" . addslashes($this->{$value}) . "'";
              }
              $this->i++;
          }
          $requete = "UPDATE $this->nomTable SET " . $this->champsValeurs . " WHERE paffec_id = '$this->paffec_id' " ;
          $resultat = $bd->execRequete($requete);
          $this->operation = "MAJ ok -- $this->paffec_id -- (enr. $this->paffec_id)";
          return $resultat;
    }//--------
          
    Function Detruire ($bd) {
          $requete = "DELETE FROM $this->nomTable WHERE paffec_id = '$this->paffec_id' " ;
          $resultat = $bd->execRequete($requete);
          $this->operation = "DESTRUCTION ok -- $this->paffec_id -- (enr. $this->paffec_id)";
          return $resultat;
    }//--------
          
    Function Affectation ($ligne, $paffec_id) {
          foreach($ligne as $index=>$value) {
              if (in_array($index, $this->enregis)) { // initialiser seulement les éléments
                  $this->{$index} = stripslashes($value); // de $_POST qui font partie de l'enr.
              }
          }
          $this->paffec_id = $paffec_id; //****** pour initialiser paffec_id dans la classe PAFFEC
          return;
    }//--------
          
    Function Get_operation () {
          return $this->operation;
    }//--------
          
    Function Get_paffec ($bd, $paffec_id) {
          $requete = "SELECT * FROM $this->nomTable WHERE paffec_id = '$paffec_id' ";
          $res = $bd->execRequete($requete);
          if (mysql_num_rows($res) > 0 ) {
              $ligne = $bd->ligneSuivante($res);
              $this->Affectation($ligne, $paffec_id);
              $this->operation = "";
          } else {
              $this->paffec_id = $paffec_id;
          }
          return $res;
    }//--------
          
}//---fin de la classe
?>