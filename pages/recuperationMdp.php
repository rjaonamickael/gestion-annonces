<?php
// Inclure les fichiers nécessaires
include('../outils/DBConnexion.php');
require '../configurations/security.config.php';

// Commencer la session (si nécessaire)
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recevoir les valeurs du formulaire
    $email = $_GET['email'];
    $password = $_POST['tbMDP'];
    $passwordHashed = hash(HASH_TYPE, $password);

    // Préparation de la connexion à la base de données
    $strNomBD = "projet2";
    $strNomServeur = $_SERVER["SERVER_NAME"];
    $strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";
    $mysql = new MySQL($strNomBD, $strInfosSensibles);
    $mysql->connexion();
    $mysql->selectionneBD();

    // Requête SQL préparée
    $query = $mysql->cBD->prepare("UPDATE utilisateurs SET MotDePasse = ? WHERE Courriel = ?");
    $query->bind_param('ss', $passwordHashed, $email);
    $query->execute();

    // Fermer la requête et la connexion à la base de données
    $query->close();
    $mysql->deconnexion();

    // Rediriger l'utilisateur avec un paramètre de succès
    header("Location: recuperationMdp.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>FAF - Récupération du mot de passe</title>
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            /* Couleur verte pour succès */
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            /* Assure que le popup est au-dessus des autres éléments */
            font-size: 16px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formInscription');
            const passwordInput = document.getElementById('tbMDP');
            const passwordConfirmInput = document.getElementById('tbMDPConfirmation');
            const passwordError = document.getElementById('errMdpConfirm');
            const urlParams = new URLSearchParams(window.location.search);

            form.addEventListener('submit', function (event) {
                let valid = true;

                // Réinitialiser les messages d'erreur
                passwordError.textContent = '';

                // Vérification des mots de passe
                if (passwordInput.value !== passwordConfirmInput.value) {
                    passwordError.textContent = 'Les mots de passe ne correspondent pas.';
                    valid = false;
                } else {
                    // Validation de la longueur et du contenu du mot de passe
                    const password = passwordInput.value;
                    const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{5,15}$/;

                    if (!passwordPattern.test(password)) {
                        passwordError.textContent =
                            'Le mot de passe doit contenir entre 5 et 15 caractères, incluant que des lettres et des chiffres.';
                        valid = false;
                    }
                }

                if (!valid) {
                    // Empêcher l'envoi du formulaire si les validations échouent
                    event.preventDefault();
                }
            });
        });
    </script>
</head>



<body>
    <!-- Popup Notification -->
    <div id="popup" class="popup">
        <p>Le mot de passe a été modifié avec succès !</p>
    </div>

    <!-- Code JavaScript -->
    <script>
        window.onload = function () {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('success') === '1') {
                const popup = document.getElementById('popup');
                popup.style.display = 'block'; // Affiche le popup

                // Masquer le popup après 3 secondes
                setTimeout(function () {
                    popup.style.display = 'none';
                }, 3000);
            }
        };
    </script>

    <div class="container col-md-5 jumbotron">
        <h2 class="text-center" style="margin: 50px;">Récupérez votre mot de passe</h2><br>
        <form id="formInscription" method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="tbMDP" name="tbMDP" placeholder="Mot de Passe">
                    <p id="errMdp" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmation du Mot de passe</label>
                    <input type="password" class="form-control" id="tbMDPConfirmation" name="tbMDPConfirmation"
                        placeholder="Confirmez le mot de passe">
                    <p id="errMdpConfirm" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div>
                <input type="submit" value="Valider" class="btn btn-primary col-md-12" id="btnInscription">
            </div>
            <br>
            <div>
                <!-- Faire la redirection vers la page de connexion -->
                <p>Déjà Membre ? <a href="../">Connectez-vous ici</a>.</p>
            </div>
        </form>
    </div>

    <?php include('../composants/footer.php'); ?>
</body>

</html>