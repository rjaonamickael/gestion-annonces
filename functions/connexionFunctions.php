<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require '../librairies/phpmailer/src/Exception.php';
require '../librairies/phpmailer/src/PHPMailer.php';
require '../librairies/phpmailer/src/SMTP.php';
require '../configurations/phpmailer.config.php';


function sendEmail($dest, $subject, $body)
{
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                    //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host = HOST;                                   //Set the SMTP server to send through
        $mail->SMTPAuth = true;                                   //Enable SMTP authentication
        $mail->Username = USERNAME;                               //SMTP username
        $mail->Password = PASSWORD;                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port = PORT;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('no-reply@projetphp.gg', 'no-reply@projetphp.gg');
        $mail->addAddress($dest);                                   //Name is optional

        //Content
        $mail->isHTML(true);                                        //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();

    } catch (Exception $e) {
        echo "<script type='text/javascript'>console.log($e);</script>";
    }
}

function messageInscription($email)
{
    $message = "
        <html>
        <head>
            <title>Confirmation de votre inscription</title>
        </head>
        <body>
            <p>Bonjour,</p>
            <p>Merci de vous être inscrit sur notre site. Veuillez cliquer sur le lien ci-dessous pour confirmer votre adresse email :</p>
            <p><a href='http://localhost:8000/pages/confirmationInscription.php?email=" . urlencode($email) . "'>Confirmer mon email</a></p>
            <p>Si vous n'avez pas demandé cette inscription, veuillez ignorer cet email.</p>
        </body>
        </html>
        ";

    return $message;
}



function scriptVerification()
{
    $script = "
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formInscription');
            const emailInput = document.getElementById('tbinscriptionEmail');
            const emailConfirmInput = document.getElementById('tbinscriptionEmailConfirmation');
            const passwordInput = document.getElementById('tbInscriptionMDP');
            const passwordConfirmInput = document.getElementById('tbInscriptionMDPConfirmation');
            const emailError = document.getElementById('errEmailConfirm');
            const passwordError = document.getElementById('errMdpConfirm');
            const urlParams = new URLSearchParams(window.location.search);

            form.addEventListener('submit', function(event) {
                let valid = true;
                
                // Réinitialiser les messages d'erreur
                emailError.textContent = '';
                passwordError.textContent = '';

                // Vérification des emails
                if (emailInput.value !== emailConfirmInput.value) {
                    emailError.textContent = 'Les adresses email ne correspondent pas.';
                    valid = false;
                }

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

                
                if(urlParams.has('emailExists')) {
                    const emailErrorPrincipal = document.getElementById('errEmail');
                    emailErrorPrincipal.textContent = 'L\'adresse email est déjà utilisée.';
                }
            });
        });
        
    </script>
    ";

    return $script;
}


function connectionDb()
{
    include 'DBConnexion.php';

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

    return $mysql;
}

function enregistrementUtilsateur($mysql, $email, $passwordHashed)
{


    // Date et heure actuelles
    $currentDateTime = date('Y-m-d H:i:s');

    $mysql->insereEnregistrement(
        'utilisateurs',
        NULL,                // NoUtilisateur, AUTO_INCREMENT
        $email,
        $passwordHashed,
        $currentDateTime,    // Creation
        0,                   // NbConnexions
        0,                   // Statut
        NULL,                // NoEmpl
        '',
        '',
        '',
        '',
        '',
        NULL                   // Modification (NULL par défaut)
    );

}

function verificationEmail($mysql, $email): int
{
    // Requête SQL préparée avec un "placeholder" ? pour l'email
    $query = $mysql->cBD->prepare("SELECT COUNT(*) FROM utilisateurs WHERE Courriel = ?");

    // Lier la variable $emailAdmin à ce "placeholder"
    $query->bind_param('s', $email);

    // Exécuter la requête
    $query->execute();

    // Récupérer le résultat
    $count = 0;
    $query->bind_result($count);
    $query->fetch();
    $query->close();

    return $count;
}

function messageRcuperationMdp($email)
{
    $message = "
        <html>
        <head>
            <title>Récupération de votre mot de passe</title>
        </head>
        <body>
            <p>Bonjour,</p>
            <p>Nous avons recu une demande de récuperation de votre mot de passe. Veuillez cliquer sur le lien ci-dessous pour confirmer cela :</p>
            <p><a href='http://localhost:8000/pages/recuperationMdp.php?email=" . urlencode($email) . "'>Récuperer mon mot de passe</a></p>
            <p>Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.</p>
        </body>
        </html>
        ";

    return $message;
}
?>