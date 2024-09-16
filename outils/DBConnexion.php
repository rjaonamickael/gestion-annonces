<?php
class MySQL
{
    public $cBD = null;
    public $nomBD = "";
    public $nomFichierInfosSensibles = "";
    public $OK = false;
    public $requete = "";

    function __construct($strNomBD, $strNomFichierInfosSensibles)
    {
        $this->nomBD = $strNomBD;
        $this->nomFichierInfosSensibles = $strNomFichierInfosSensibles;
    }

    function connexion()
    {
        $infosSensibles = include($this->nomFichierInfosSensibles);
        $this->cBD = new mysqli($infosSensibles['host'], $infosSensibles['user'], $infosSensibles['password']);
        if ($this->cBD->connect_error) {
            die("Problème de connexion… " . $this->cBD->connect_error);
        }
    }

    function selectionneBD()
    {
        $this->OK = $this->cBD->select_db($this->nomBD);
    }

    function deconnexion()
    {
        $this->cBD->close();
    }

    // Ajoutez la fonction ici
    function insereEnregistrement($strNomTable, ...$args)
{
    // Liste des colonnes dans le bon ordre
    $colonnes = [
        'NoUtilisateur', 'Courriel', 'MotDePasse', 'Creation', 'NbConnexions', 'Statut', 
        'NoEmpl', 'Nom', 'Prenom', 'NoTelMaison', 'NoTelTravail', 'NoTelCellulaire', 'Modification'
    ]; 

    $values = [];

    foreach ($args as $value) {
        if (is_bool($value)) {
            $values[] = $value ? '1' : '0';
        } elseif (is_string($value)) {
            $values[] = "'" . $this->cBD->real_escape_string($value) . "'";
        } elseif ($value === NULL) {
            $values[] = "NULL";
        } else {
            $values[] = $value;
        }
    }

    // Vérifier que le nombre de valeurs correspond au nombre de colonnes
    if (count($values) !== count($colonnes)) {
        die("Erreur : Le nombre de valeurs ne correspond pas au nombre de colonnes.");
    }

    // Construire la requête d'insertion avec les noms de colonnes
    $query = "INSERT INTO $strNomTable (" . implode(', ', $colonnes) . ") VALUES (" . implode(', ', $values) . ")";
    $this->requete = $query;
    $this->OK = $this->cBD->query($query);

    if (!$this->OK) {
        die("Erreur lors de l'insertion de l'enregistrement : " . $this->cBD->error);
    }
}

}
?>