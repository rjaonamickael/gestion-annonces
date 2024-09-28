<?php

$email = isset($_SESSION['Courriel']);

// Connexion à la base de données
$strNomBD = "projet2";
$strNomServeur = $_SERVER["SERVER_NAME"];
$strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

$mysql = new MySQL($strNomBD, $strInfosSensibles);
$mysql->connexion();
$mysql->selectionneBD();


// Vérifier les informations d'identification
$query = "SELECT * FROM utilisateurs WHERE Courriel = ?";
$stmt = $mysql->cBD->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();


$row = $result->fetch_assoc();
$userId = $row['NoUtilisateur'];


// Insérer une ligne dans la table connexions
$queryInsertConnexion = "UPDATE connexions (NoUtilisateur, Deconnexion) WHERE NoUtilisateur = ? AND Deconnexion IS NULL";
$stmtInsert = $mysql->cBD->prepare($queryInsertConnexion);
$stmtInsert->bind_param("i", $userId);
$stmtInsert->execute();

?>