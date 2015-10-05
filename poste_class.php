<?php

class POSTE {

// --- Partie privée : les variables

    var $champs, $valeurs, $champsValeurs, $idx, $nomTable;

    var $poste_id, $instance_id, $poste_no, $poste_nom, $poste_proc;

    var $enregis = array("instance_id", "poste_no", "poste_nom", "poste_proc");

    /* fonctions accesseurs */

    function set_instance_id ($instance_id) { $this->instance_id = Clean($instance_id); }
    function get_instance_id () { return $this->instance_id; }

    function set_poste_no ($poste_no) { $this->poste_no = Clean($poste_no); }
    function get_poste_no () { return $this->poste_no; }

    function set_poste_nom ($poste_nom) { $this->poste_nom = Clean($poste_nom); }
    function get_poste_nom () { return $this->poste_nom; }

    function set_poste_proc ($poste_proc) { $this->poste_proc = Clean($poste_proc); }
    function get_poste_proc () { return $this->poste_proc; }

          
//---- partie publique : les méthodes
          
    Function POSTE ($table) { // constructeur
          
          /*********************************************************
          liste des champs de l'enregistrement
          le champ « poste_id » n'est pas inclus à cause de la
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
    }//---------------------- constructeur pour POSTE
          
    Function Imprimer_form ($css, $mode, $forme, $listePostes) {
          print('<table border="0" bgcolor="#EBEBEB" align="center"><tr><td>');
          print(Imprime_titreListe("Gestion d'un poste d'une instance", "titre"));
          print("<br>");
          $f = new Formulaire ("post", "postes.php", "", TRUE , $forme);
          $f->debutTable(HORIZONTAL);
          $f->champListe("$instance_id", "instance_id", $this->instance_id, 1, $listePostes);
          $f->champTexte("$poste_no", "poste_no", $this->poste_no, 3, 58);
          $f->champTexte("$poste_nom", "poste_nom", $this->poste_nom, 3, 58);
          $f->champTexte("$poste_proc", "poste_proc", $this->poste_proc, 3, 58);
          $f->finTable();
          $f->debutTable(HORIZONTAL);
          if($mode == "ajouter") {
              $f->champValider ("sauver", "action");
          } else {
              $f->champValider ("sauver", "action");
              $f->champValider ("sauver", "action");
          }
          $f->finTable();
          $f->champCache("poste_id", $this->poste_id);
          $f->fin();
          print('</td></tr></table>');
    }//--------
          
    Function Sauver ($bd) {
          $requete = "SELECT poste_id FROM $this->nomTable WHERE poste_id = '$this->poste_id' ";
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
          //----- clé primaire « poste_id » placée au début des champs existants
          //----- « 0 » comme valeur correspondante dans les valeurs
          $this->champs = "poste_id, " . $this->champs;     
          $this->valeurs = "0, " . $this->valeurs;
          $requete = "INSERT INTO $this->nomTable (" . $this->champs . ") VALUES (" . $this->valeurs . ")";
          $res = $bd->execRequete($requete);
          $this->poste_id = mysql_insert_id();      //--- pour initialiser « poste_id » dans l'objet après l'insertion dans la BD
          $this->operation = "INSERTION ok -- $this->poste_id -- (enr. $this->poste_id)";
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
          $requete = "UPDATE $this->nomTable SET " . $this->champsValeurs . " WHERE poste_id = '$this->poste_id' " ;
          $resultat = $bd->execRequete($requete);
          $this->operation = "MAJ ok -- $this->poste_id -- (enr. $this->poste_id)";
          return $resultat;
    }//--------
          
    Function Detruire ($bd) {
          $requete = "DELETE FROM $this->nomTable WHERE poste_id = '$this->poste_id' " ;
          $resultat = $bd->execRequete($requete);
          $this->operation = "DESTRUCTION ok -- $this->poste_id -- (enr. $this->poste_id)";
          return $resultat;
    }//--------
          
    Function Affectation ($ligne, $poste_id) {
          foreach($ligne as $index=>$value) {
              if (in_array($index, $this->enregis)) { // initialiser seulement les éléments
                  $this->{$index} = stripslashes($value); // de $_POST qui font partie de l'enr.
              }
          }
          $this->poste_id = $poste_id; //****** pour initialiser poste_id dans la classe POSTE
          return;
    }//--------
          
    Function Get_operation () {
          return $this->operation;
    }//--------
          
    Function Get_poste ($bd, $poste_id) {
          $requete = "SELECT * FROM $this->nomTable WHERE poste_id = '$poste_id' ";
          $res = $bd->execRequete($requete);
          if (mysql_num_rows($res) > 0 ) {
              $ligne = $bd->ligneSuivante($res);
              $this->Affectation($ligne, $poste_id);
              $this->operation = "";
          } else {
              $this->poste_id = $poste_id;
          }
          return $res;
    }//--------
          
}//---fin de la classe
?>