<?php
session_start();
include('DBConnexion.php');

// Récupérer les données du formulaire
$email = $_POST['tbinscriptionEmail'];
$emailConfirmation = $_POST['tbinscriptionEmailConfirmation'];
$motdepasse = $_POST['tbInscriptionMDP'];
$motdepasseConfirmation = $_POST['tbInscriptionMDPConfirmation'];

// Validation des données
$errors = [];
if ($email !== $emailConfirmation) {
    $errors[] = "Les courriels ne correspondent pas.";
}
if ($motdepasse !== $motdepasseConfirmation) {
    $errors[] = "Les mots de passe ne correspondent pas.";
}
if (strlen($motdepasse) < 5 || strlen($motdepasse) > 15) {
    $errors[] = "Le mot de passe doit contenir entre 5 et 15 caractères.";
}
if (!preg_match('/^[a-zA-Z0-9]+$/', $motdepasse)) {
    $errors[] = "Le mot de passe doit contenir uniquement des lettres et des chiffres.";
}

// Connexion à la base de données
$strNomBD = "projet2";
$strNomServeur = $_SERVER["SERVER_NAME"];
$strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

$mysql = new MySQL($strNomBD, $strInfosSensibles);
$mysql->connexion();
$mysql->selectionneBD();

// Vérifier si l'email est déjà utilisé
$queryCheckEmail = "SELECT COUNT(*) AS count FROM utilisateurs WHERE Courriel = ?";
$stmt = $mysql->cBD->prepare($queryCheckEmail);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if ($row['count'] > 0) {
    $errors[] = "L'email est déjà utilisé.";
}

// Si aucune erreur, insérer les données
if (empty($errors)) {
    // Préparer les données pour l'insertion
    $currentDateTime = date('Y-m-d H:i:s'); // Date et heure actuelles

    $mysql->insereEnregistrement(
        'utilisateurs',
        NULL, // NoUtilisateur
        $email,
        $motdepasse,
        $currentDateTime, // Creation
        0, // NbConnexions
        0, // Statut
        NULL, // NoEmpl
        '',
        '',
        '',
        '',
        '',
        NULL // Modification (NULL par défaut)
    );

    // Déconnexion
    $mysql->deconnexion();

    // Redirection vers la page d'accueil
    header("Location: ../");
    exit();
} else {
    // Stocker les erreurs et les données du formulaire dans la session
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = [
        'email' => $email,
        'email_confirmation' => $emailConfirmation,
    ];
    header("Location: ./pages/inscription.php");
    exit();
}

?>