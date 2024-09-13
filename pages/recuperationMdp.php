<?php
require '../functions/connexionFunctions.php';
include('../composants/header.php');
include '../outils/DBConnexion.php';

echo "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formInscription');
            const passwordInput = document.getElementById('tbInscriptionMDP');
            const passwordConfirmInput = document.getElementById('tbInscriptionMDPConfirmation');
            const passwordError = document.getElementById('errMdpConfirm');
            const urlParams = new URLSearchParams(window.location.search);

            form.addEventListener('submit', function(event) {
                let valid = true;

                // Vérification des mots de passe
                if (passwordInput.value !== passwordConfirmInput.value) {
                    passwordError.textContent = 'Les mots de passe ne correspondent pas.';
                    valid = false;
                } else {
                    // Validation de la longueur et du contenu du mot de passe
                    const password = passwordInput.value;
                    const passwordPattern = /^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{5,15}$/;

                    if (!passwordPattern.test(password)) {
                        passwordError.textContent = 'Le mot de passe doit contenir entre 5 et 15 caractères, incluant que des lettres et des chiffres.';
                        valid = false;
                    }
                }

                if (!valid) {
                    // Empêcher l'envoi du formulaire si les validations échouent
                    event.preventDefault();
                }

            });
        });
";
?>
<title>FAF - Récupération du mot de passe</title>
</head>

<body>
    <div class="container col-md-5 jumbotron">
        <h2 class="text-center" style="margin: 100px;">Récupération de mot de passe</h2><br>
        <form id="formInscription" method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDP" name="tbInscriptionMDP"
                        placeholder="Mot de Passe">
                    <p id="errMdp" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmation du Mot de passe</label>
                    <input type="password" class="form-control" id="tbInscriptionMDPConfirmation"
                        name="tbInscriptionMDPConfirmation" placeholder="Confirmez le mot de passe">
                    <p id="errMdpConfirm" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div>
                <input type="submit" value="S&#39;inscrire" class="btn btn-primary col-md-12" id="btnInscription">

            </div>
            <br>
            <div>
                <!-- Faire la redirection vers la page de connection -->
                <p>Déjà Membre ? <a href="../">Connectez vous ici</a>.</p>
            </div>
        </form>
    </div>



    <?php include('../composants/footer.php'); ?>