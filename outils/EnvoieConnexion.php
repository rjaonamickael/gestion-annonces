<?php
session_start();
include('DBConnexion.php');

// Récupérer les données du formulaire
$email = $_POST['tbEmail'];
$motdepasse = $_POST['tbMdp'];

// Connexion à la base de données
$strNomBD = "projet2";
$strNomServeur = $_SERVER["SERVER_NAME"];
$strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

$mysql = new MySQL($strNomBD, $strInfosSensibles);
$mysql->connexion();
$mysql->selectionneBD();

// Vérifier les informations d'identification
$queryCheckUser = "SELECT * FROM utilisateurs WHERE Courriel = ? and Statut <> '0'";
$stmt = $mysql->cBD->prepare($queryCheckUser);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $errors = ["Identifiant ou mot de passe incorrect."];
} else {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['MotDePasse'];
    $userId = $row['NoUtilisateur'];
    
    // Vérifier le mot de passe
    if ($motdepasse === $row['MotDePasse']) {
        // Connexion réussie
        // Mettre à jour le nombre de connexions dans utilisateurs
        $queryUpdateUtilisateur = "UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = ?";
        $stmtUpdate = $mysql->cBD->prepare($queryUpdateUtilisateur);
        $stmtUpdate->bind_param("i", $userId);
        $stmtUpdate->execute();

        // Insérer une ligne dans la table connexions
        $queryInsertConnexion = "INSERT INTO connexions (NoUtilisateur, Connexion) VALUES (?, NOW())";
        $stmtInsert = $mysql->cBD->prepare($queryInsertConnexion);
        $stmtInsert->bind_param("i", $userId);
        $stmtInsert->execute();

        // Déconnexion
        $mysql->deconnexion();
        header("Location: ../pages/gestion_annonce.php");
        exit();
    } else {
        $errors = ["Identifiant ou mot de passe incorrect."];
    }
}

$mysql->deconnexion();

// Stocker les erreurs et les données du formulaire dans la session
$_SESSION['errors'] = $errors;
$_SESSION['form_data'] = [
    'email' => $email
];

header("Location: ../");
exit();
?>