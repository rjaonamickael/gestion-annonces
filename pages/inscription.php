<?php
require '../functions/connexionFunctions.php';
include('../composants/header.php');
include '../outils/DBConnexion.php';

echo scriptVerification();
?>
<title>FAF - inscription</title>
</head>

<body>
    <div class="container col-md-5 jumbotron">
        <h2 class="text-center">Enregistrement</h2><br>
        <form id="formInscription" method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmail" name="tbinscriptionEmail"
                        placeholder="Courriel @">
                    <p id="errEmail" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Confirmation du Courriel</label>
                    <input type="email" class="form-control" id="tbinscriptionEmailConfirmation"
                        name="tbinscriptionEmailConfirmation" placeholder="Confirmez le courriel">
                    <p id="errEmailConfirm" class="text-danger font-weight-bold"></p>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label>Mot de passe</label>
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

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Recevoir les valeurs du formulaire   
        $email = $_POST['tbinscriptionEmail'];
        $emailConfirm = $_POST['tbinscriptionEmailConfirmation'];
        $password = $_POST['tbInscriptionMDP'];
        $passwordConfirm = $_POST['tbInscriptionMDPConfirmation'];

        // Préparation de la connexion à la base de données
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


        if ($email === $emailConfirm && $password === $passwordConfirm) {
            //Vérifiez si le mails a été déjà utilisé
            if (verificationEmail($mysql, $email) == 1) {
                echo "<script>alert('L\'adresse email est déjà utilisée.')</script>";
                $mysql->deconnexion();
            } else {
                // Hash du mot de passe pour une sécurité accrue
                $salt = bin2hex(random_bytes(SALT_SIZE));
                $passwordHashed = hash(HASH_TYPE, $salt . $password);

                enregistrementUtilsateur($mysql, $email, $password, $salt);
                // Déconnexion
                $mysql->deconnexion();

                // Préparer l'email de confirmation
                $dest = $email;
                $objet = "Confirmation de votre inscription";
                $message = messageInscription($email);

                // Envoi de l'email
                sendEmail($dest, $objet, $message);

            }
        }
    }

    ?>

    <?php include('../composants/footer.php'); ?>