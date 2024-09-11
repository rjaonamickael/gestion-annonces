<?php 
        require '../functions/connexionFunctions.php';
        include('../composants/header.php');

        echo scriptVerification();
    ?>
<title>FAF - inscription</title>
</head>

<body>
    <div class="container col-md-5 jumbotron">
        <h2 class="text-center">Enregistrement</h2><br>
        <form id="formInscription" method="POST" action="#">
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

    // Vérifier si les emails et les mots de passe correspondent
    if ($email === $emailConfirm && $password === $passwordConfirm) {
        // Hash du mot de passe pour une sécurité accrue
        $salt = /*"50c035e8b9f566c81ab141f93ce98e70984999db8d7608b0";/*/bin2hex(random_bytes(SALT_SIZE));
        $passwordHash = hash(HASH_TYPE, $salt . $password);

        // Déboguer
        // echo "<pre>";
        // print_r($salt);
        // echo "<br>";
        // print_r($passwordHash);
        // echo "</pre>";

        // Enregistrer l'utilisateur dans la base de données (supposons une table 'users')
        // Vous devez adapter ce code en fonction de votre base de données
        // $db = new PDO('mysql:host=localhost;dbname=yourdbname', 'username', 'password');
        // $stmt = $db->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        // $stmt->execute(['email' => $email, 'password' => $passwordHash]);

        // Préparer l'email de confirmation
        $dest = $email;       
        $objet = "Confirmation de votre inscription";
        $message = messageInscription($email);
        
        // Envoi de l'email
        sendEmail($dest,$objet,$message);

    } else {
        echo "<script type='text/javascript'>alert('Inscription non valide');</script>";
    }
}
?>


    <?php 
        include('../composants/footer.php');
    ?>