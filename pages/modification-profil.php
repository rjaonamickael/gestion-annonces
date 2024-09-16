<?php

session_start();

include ('../outils/DBConnexion.php');

// Initialisation de la connexion à la base de données
$strNomBD = "projet2";
$strNomServeur = $_SERVER["SERVER_NAME"];
$strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

$mysql = new MySQL($strNomBD, $strInfosSensibles);
$mysql->connexion();
$mysql->selectionneBD();

// Variables pour les données du formulaire
$status = $employeeNumber = $lastName = $firstName = $homePhone = $workPhone = $workExtension = $cellPhone = '';

// Recuperation de l'email
$email = !empty($_SESSION['Courriel'])? $_SESSION['Courriel'] : "";

// Vérifier si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupération des données du formulaire
    $status = $_POST['status'];
    $employeeNumber = $_POST['employeeNumber'];
    $lastName = $_POST['lastName'];
    $firstName = $_POST['firstName'];
    $homePhone = $_POST['homePhone'];
    $workPhone = $_POST['workPhone'];
    $workExtension = $_POST['workExtension'];
    $cellPhone = $_POST['cellPhone'];

    if (empty($lastName) || empty($firstName)) {
        die('Le nom de famille et le prénom sont requis.');
    }

    // Préparer la requête de mise à jour
    $updateQuery = "UPDATE utilisateurs SET
        Statut = IFNULL(NULLIF('$status', ''), Statut),
        NoEmpl = IFNULL(NULLIF('$employeeNumber', ''), NoEmpl),
        Nom = '$lastName',
        Prenom = '$firstName',
        NoTelMaison = IFNULL(NULLIF('$homePhone', ''), NoTelMaison),
        NoTelTravail = IFNULL(NULLIF(CONCAT('$workPhone', ' #', '$workExtension'), ''), NoTelTravail),
        NoTelCellulaire = IFNULL(NULLIF('$cellPhone', ''), NoTelCellulaire)
        WHERE Courriel = '$email';";

    // Exécuter la requête de mise à jour
    $mysql->requete = $updateQuery;
    $mysql->OK = $mysql->cBD->query($updateQuery);

    if ($mysql->OK) {
        echo "<script>alert('Profil mis à jour avec succès.');</script>";
    } else {
        die("Erreur lors de la mise à jour du profil : " . $mysql->cBD->error);
    }

    // Déconnexion
    $mysql->deconnexion();
} else {
    if(!empty($email)){
        $querySelectUser = "SELECT * FROM utilisateurs WHERE Courriel = '$email'";
        $result = $mysql->cBD->query($querySelectUser);
        if ($result && $row = $result->fetch_assoc()) {
            $status = $row['Statut'] ?? '';
            $employeeNumber = $row['NoEmpl'] ?? '';
            $lastName = $row['Nom'] ?? '';
            $firstName = $row['Prenom'] ?? '';
            $homePhone = $row['NoTelMaison'] ?? '';
            $workPhone = strtok($row['NoTelTravail'], '#') ?? '';
            $workExtension = str_replace($workPhone . '#', '', $row['NoTelTravail']) ?? '';
            $cellPhone = $row['NoTelCellulaire'] ?? '';
        }
    }
    $mysql->deconnexion();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour du Profil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-control[readonly] {
            background-color: #e9ecef;
        }
        .form-check-label {
            margin-left: 5px;
        }
        .error {
            color: #dc3545;
            font-size: 0.875em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Mise à jour du profil</h2>
        <form id="profileForm" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <a href="../pages/recuperationMdp.php" class="d-block mb-2">Accédez à la modification de mot de passe ici</a>
            </div>
            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" class="form-control" name="status">
                    <option value="">Choisir un statut...</option>
                    <option value="4" <?php if ($status == '4') echo 'selected'; ?>>Enseignant</option>
                    <option value="2" <?php if ($status == '2') echo 'selected'; ?>>Cadre</option>
                    <option value="3" <?php if ($status == '3') echo 'selected'; ?>>Employé de soutien</option>
                    <option value="5" <?php if ($status == '5') echo 'selected'; ?>>Professionnel</option>
                </select>
            </div>
            <div class="form-group">
                <label for="employeeNumber">Numéro Emploi</label>
                <input type="text" class="form-control" id="employeeNumber" name="employeeNumber" placeholder="1 a 9999" value="<?php echo htmlspecialchars($employeeNumber); ?>">
                <div class="error" id="employeeNumberError"></div>
            </div>
            <div class="form-group">
                <label for="lastName">Nom de famille</label>
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Nom de famille" required value="<?php echo htmlspecialchars($lastName); ?>">
                <div class="error" id="lastNameError"></div>
            </div>
            <div class="form-group">
                <label for="firstName">Prénom</label>
                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="Prénom" required value="<?php echo htmlspecialchars($firstName); ?>">
                <div class="error" id="firstNameError"></div>
            </div>
            <div class="form-group">
                <label for="homePhone">Numéro Téléphone Maison</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" name="homePhone" id="homePhone" placeholder="(111) 111-1111" value="<?php echo htmlspecialchars($homePhone); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="workPhone">Numéro Téléphone Bureau</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" id="workPhone" name="workPhone" placeholder="(111) 111-1112" value="<?php echo htmlspecialchars($workPhone); ?>">
                    <input type="text" class="form-control ml-2" id="workExtension" name="workExtension" placeholder="Poste" value="<?php echo htmlspecialchars($workExtension); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="cellPhone">Numéro Téléphone Cellulaire</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" id="cellPhone" name="cellPhone" placeholder="(111) 111-1113" value="<?php echo htmlspecialchars($cellPhone); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
        </form>
        <a href="gestion_annonce.php" class="d-block text-center mt-3">Retour à la liste des annonces</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../scripts/profil.js"></script>
</body>
</html>
