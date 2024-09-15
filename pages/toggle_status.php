<?php
session_start();
include '../outils/DBConnexion.php'; 

// Create MySQL connection object
try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Check if NoAnnonce is set
if (!isset($_GET['NoAnnonce']) || !isset($_GET['etat'])) {
    die("Paramètres manquants.");
}

$noAnnonce = (int)$_GET['NoAnnonce'];
$etat = (int)$_GET['etat'];

// Toggle the status
$newEtat = $etat == 1 ? 0 : 1;

// Update the status in the database
$query = "UPDATE annonces SET Etat = ? WHERE NoAnnonce = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param('ii', $newEtat, $noAnnonce);

if ($stmt->execute()) {
    header("Location: gestion_annonce.php"); // Redirect back to the announcement management page
    exit();
} else {
    echo "Erreur lors de la mise à jour de l'état de l'annonce : " . $stmt->error;
}

$stmt->close();
$mysql->deconnexion();
?>
