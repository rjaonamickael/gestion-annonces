<?php
require '../functions/connexionFunctions.php';
require '../configurations/security.config.php';
include('../composants/header.php');
include '../outils/DBConnexion.php';

// Définir le type de hashage
define('HASH_TYPE', 'sha256');

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
<<<<<<< HEAD
=======

>>>>>>> master
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Recevoir les valeurs du formulaire   
        $email = $_POST['tbinscriptionEmail'];
        $emailConfirm = $_POST['tbinscriptionEmailConfirmation'];
        $password = $_POST['tbInscriptionMDP'];
        $passwordConfirm = $_POST['tbInscriptionMDPConfirmation'];

        // Préparation de la connexion à la base de données
        $strNomBD = "projet2";
        $strNomServeur = $_SERVER["SERVER_NAME"];
        $strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

        // Création de l'objet de connexion
        $mysql = new MySQL($strNomBD, $strInfosSensibles);
        $mysql->connexion();
        $mysql->selectionneBD();

<<<<<<< HEAD
        if ($email === $emailConfirm && $password === $passwordConfirm) {
            // Vérifiez si l'adresse email a été déjà utilisée
            if (verificationEmail($mysql, $email) == 1) {
                echo "<script>alert('L\'adresse email est déjà utilisée.')</script>";
                $mysql->deconnexion();
            } else {
                // Générer une valeur "Sel" pour la sécurité du mot de passe
                $sel = bin2hex(random_bytes(16)); // Génère un "salt" de 16 octets et le convertit en chaîne hexadécimale

                // Hash du mot de passe avec le "salt" pour une sécurité accrue
                $passwordHashed = hash(HASH_TYPE, $password . $sel);

                // Appel de la fonction insereEnregistrement avec les valeurs correctes
                $mysql->insereEnregistrement(
                    'utilisateurs',             // Nom de la table
                    $email,                     // Courriel
                    $passwordHashed,            // MotDePasse
                    date('Y-m-d H:i:s'),        // Creation (date actuelle)
                    null,                       // Modification (par défaut null si nouvel utilisateur)
                    0,                          // NbConnexions
                    null,                       // NoEmpl (aucun employé par défaut)
                    'NomExemple',               // Nom (à adapter selon votre formulaire si disponible)
                    'PrenomExemple',            // Prénom (à adapter selon votre formulaire si disponible)
                    $sel,                       // Sel généré
                    1,                          // Statut (actif)
                    '0123456789',               // NoTelCellulaire (exemple, adapter selon votre besoin)
                    null,                       // NoTelMaison
                    null                        // NoTelTravail
                );

                // Déconnexion de la base de données
                $mysql->deconnexion();

                // Préparer et envoyer l'email de confirmation
                $dest = $email;
                $objet = "Confirmation de votre inscription";
                $message = messageInscription($email);
                sendEmail($dest, $objet, $message);
                echo "<script>alert('Inscription réussie. Veuillez vérifier votre courriel pour la confirmation.')</script>";
=======
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recevoir les valeurs du formulaire   
            $email = $_POST['tbinscriptionEmail'];
            $emailConfirm = $_POST['tbinscriptionEmailConfirmation'];
            $password = $_POST['tbInscriptionMDP'];
            $passwordConfirm = $_POST['tbInscriptionMDPConfirmation'];

            // Préparation de la connexion à la base de données
            $strNomBD = "projet2";
            $strNomServeur = $_SERVER["SERVER_NAME"];
            $strInfosSensibles = str_replace(".", "-", $strNomServeur) . ".php";

            // Création de l'objet de connexion
            $mysql = new MySQL($strNomBD, $strInfosSensibles);
            $mysql->connexion();
            $mysql->selectionneBD();

            if ($email === $emailConfirm && $password === $passwordConfirm) {
                // Vérifiez si l'adresse email a été déjà utilisée
                if (verificationEmail($mysql, $email) == 1) {
                    echo "<script>alert('L\'adresse email est déjà utilisée.')</script>";
                    $mysql->deconnexion();
                } else {
                    // Générer une valeur "Sel" pour la sécurité du mot de passe
                    $sel = bin2hex(random_bytes(16)); // Génère un "salt" de 16 octets et le convertit en chaîne hexadécimale
    
                    // Hash du mot de passe avec le "salt" pour une sécurité accrue
                    $passwordHashed = hash(HASH_TYPE, $password . $sel);

                    // Appel de la fonction insereEnregistrement avec les valeurs correctes
                    enregistrementUtilsateur($mysql, $email, $passwordHashed);

                    // Déconnexion de la base de données
                    $mysql->deconnexion();

                    // Préparer et envoyer l'email de confirmation
                    $dest = $email;
                    $objet = "Confirmation de votre inscription";
                    $message = messageInscription($email);
                    sendEmail($dest, $objet, $message);
                }
            } else {
                echo "<script>alert('Les emails ou mots de passe ne correspondent pas.')</script>";
>>>>>>> master
            }
        }
    }
<<<<<<< HEAD
=======


>>>>>>> master
    ?>

    <?php include('../composants/footer.php'); ?>
</body>
</html>
