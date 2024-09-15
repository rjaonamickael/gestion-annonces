<?php
session_start();
include '../outils/DBConnexion.php'; 


try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}


if (!isset($_GET['NoAnnonce']) || !isset($_GET['etat'])) {
    die("Paramètres manquants.");
}

$noAnnonce = (int)$_GET['NoAnnonce'];
$etat = (int)$_GET['etat'];


$newEtat = $etat == 1 ? 0 : 1;


$query = "UPDATE annonces SET Etat = ? WHERE NoAnnonce = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param('ii', $newEtat, $noAnnonce);

if ($stmt->execute()) {
    header("Location: gestion_annonce.php"); 
    exit();
} else {
    echo "Erreur lors de la mise à jour de l'état de l'annonce : " . $stmt->error;
}

$stmt->close();
$mysql->deconnexion();
?>
