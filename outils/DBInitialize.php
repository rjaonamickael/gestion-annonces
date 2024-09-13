<?php
include 'DBConnexion.php';

// Nom de la base de donnees
$strNomBD = "projet2";

// Recuperation des informations du serveur
$strNomServeur = $_SERVER["SERVER_NAME"];
$strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

// Création de l'objet de connexion
$mysql = new MySQL($strNomBD, $strInfosSensibles);

// Connexion à la base de données
$mysql->connexion();

// Sélectionner la base de données
$mysql->selectionneBD();


// Requêtes pour créer les tables
$requeteUtilisateurs = "
CREATE TABLE IF NOT EXISTS utilisateurs (
    NoUtilisateur INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Courriel VARCHAR(50) NOT NULL UNIQUE,
    MotDePasse VARCHAR(64) NOT NULL,
    Creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    NbConnexions INT(4) UNSIGNED DEFAULT 0,
    Statut TINYINT(1) NOT NULL,
    NoEmpl INT(4) UNSIGNED,
    Nom VARCHAR(25),
    Prenom VARCHAR(20),
    NoTelMaison VARCHAR(15),
    NoTelTravail VARCHAR(21),
    NoTelCellulaire VARCHAR(15),
    Modification DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (Courriel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$requeteConnexions = "
CREATE TABLE IF NOT EXISTS connexions (
    NoConnexion INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NoUtilisateur INT(3) UNSIGNED,
    Connexion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Deconnexion DATETIME DEFAULT NULL,
    FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$requeteAnnonces = "
CREATE TABLE IF NOT EXISTS annonces (
    NoAnnonce INT(4) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NoUtilisateur INT(3) UNSIGNED,
    Parution DATETIME DEFAULT CURRENT_TIMESTAMP,
    Categorie TINYINT(1) UNSIGNED,
    DescriptionAbregee VARCHAR(50) NOT NULL,
    DescriptionComplete TEXT,
    Prix DECIMAL(8,2),
    Photo VARCHAR(50),
    MiseAJour DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Etat TINYINT(1) NOT NULL,
    FOREIGN KEY (NoUtilisateur) REFERENCES utilisateurs(NoUtilisateur) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$requeteCategories = "
CREATE TABLE IF NOT EXISTS categories (
    NoCategorie TINYINT(1) UNSIGNED PRIMARY KEY,
    Description VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Exécuter les requêtes
$mysql->cBD->query($requeteUtilisateurs);
$mysql->cBD->query($requeteConnexions);
$mysql->cBD->query($requeteAnnonces);
$mysql->cBD->query($requeteCategories);

// Vérifier les erreurs
if ($mysql->cBD->error) {
    die("Erreur lors de la création des tables : " . $mysql->cBD->error);
}

/* Commenter apres la premiere initialisation */

// Créer un utilisateur administrateur
$emailAdmin = 'admin@gmail.com';
$motdepasseAdmin = 'Secret123';

$query = $mysql->cBD->prepare("SELECT COUNT(*) FROM utilisateurs WHERE Courriel = ?");

// Lier la variable $emailAdmin à ce "placeholder"
$query->bind_param('s', $emailAdmin);

// Exécuter la requête
$query->execute();

// Récupérer le résultat
$query->bind_result($count);
$query->fetch();
$query->close();


if ($count == 0) {
    // L'utilisateur n'existe pas, on peut l'insérer
    $currentDateTime = date('Y-m-d H:i:s'); // Date et heure actuelles

    $mysql->insereEnregistrement(
        'utilisateurs',
        NULL,                // NoUtilisateur, AUTO_INCREMENT
        $emailAdmin,
        hash('sha256', $motdepasseAdmin),
        $currentDateTime,    // Creation
        0,                   // NbConnexions
        1,                   // Statut
        NULL,                // NoEmpl
        'Administrateur',
        'Admin',
        '',
        '',
        '',
        NULL                   // Modification (NULL par défaut)
    );

}

// Déconnexion
$mysql->deconnexion();

?>