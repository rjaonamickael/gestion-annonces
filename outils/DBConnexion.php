<?php
class MySQL
{
    /*
  |----------------------------------------------------------------------------------|
  | Attributs
  |----------------------------------------------------------------------------------|
  */
    public $cBD = null;                       /* Identifiant de connexion */
    public $nomBD = "";                       /* Nom de la base de données */
    public $nomFichierInfosSensibles = "";    /* Nom du fichier 'InfosSensibles' */
    public $OK = false;                       /* Opération réussie ou non */
    public $requete = "";                     /* Requête exécutée */

    /*
    |----------------------------------------------------------------------------------|
    | __construct
    |----------------------------------------------------------------------------------|
    */
    function __construct($strNomBD, $strNomFichierInfosSensibles)
    {
        $this->nomBD = $strNomBD;
        $this->nomFichierInfosSensibles = $strNomFichierInfosSensibles;
    }

    /*
    |----------------------------------------------------------------------------------|
    | connexion()
    |----------------------------------------------------------------------------------|
    */
    function connexion()
    {
        $infosSensibles = include($this->nomFichierInfosSensibles);

        $this->cBD = new mysqli($infosSensibles['host'], $infosSensibles['user'], $infosSensibles['password']);

        if ($this->cBD->connect_error) {
            die("Problème de connexion… " . $this->cBD->connect_error);
        }
    }

    /*
    |----------------------------------------------------------------------------------|
    | selectionneBD()
    |----------------------------------------------------------------------------------|
    */
    function selectionneBD()
    {
        $this->OK = $this->cBD->select_db($this->nomBD);
    }

    /*
    |----------------------------------------------------------------------------------|
    | deconnexion
    |----------------------------------------------------------------------------------|
    */
    function deconnexion()
    {
        $this->cBD->close();
    }

    /*
     |----------------------------------------------------------------------------------|
     | insereEnregistrement
     |----------------------------------------------------------------------------------|
     */
    function insereEnregistrement($strNomTable)
    {
        // Construire la requête SQL d'insertion
        $args = func_get_args();
        array_shift($args); // Retirer le nom de la table des arguments
        $values = [];

        foreach ($args as $value) {
            // Convertir les valeurs booléennes en 0 ou 1
            if (is_bool($value)) {
                $values[] = $value ? '1' : '0';
            } elseif (is_string($value)) {
                // Échapper les chaînes pour éviter les problèmes d'injection SQL
                $values[] = "'" . $this->cBD->real_escape_string($value) . "'";
            } elseif ($value === NULL) {
                // Gérer les valeurs NULL
                $values[] = "NULL";
            } else {
                // Pour les autres types de valeurs, convertir directement
                $values[] = $value;
            }
        }

        // Préparer la requête d'insertion
        $query = "INSERT INTO $strNomTable VALUES (" . implode(', ', $values) . ")";
        $this->requete = $query;
        $this->OK = $this->cBD->query($query);

        if (!$this->OK) {
            die("Erreur lors de l'insertion de l'enregistrement : " . $this->cBD->error);
        }
    }

    /*
     |----------------------------------------------------------------------------------|
     | supprimeEnregistrements
     |----------------------------------------------------------------------------------|
     */
    function supprimeEnregistrements($strNomTable, $strListeConditions = "")
    {
        $query = "DELETE FROM $strNomTable";

        if (!empty($strListeConditions)) {
            $query .= " WHERE $strListeConditions";
        }

        $this->requete = $query;
        $this->OK = $this->cBD->query($query);
    }
}
?>