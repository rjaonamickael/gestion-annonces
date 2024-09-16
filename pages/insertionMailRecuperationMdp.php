<?php include('../composants/header.php'); ?>

<title>FAF - Mot de passe oublié </title>
</head>

<body>

    <div class="container-fluid my-4">
        <div id="divConnexion" class="col-4 m-auto">
            <h1 class="text-center" id="titreConnexion" style="margin-bottom: 50px;">Mot de passe oublié </h1>
            <form id="formConnexion" action="" method="POST" autocomplete="off">

                <div class="form-group">
                    <label for="tbEmail">Email</label>
                    <input type="email" class="form-control" id="tbEmail" name="tbEmail" placeholder="Email"
                        autocomplete="off"
                        value="<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>">
                    <p id="errEmail" class="text-danger font-weight-bold">
                        <?php
                        session_start();  // Commencez la session avant d'accéder à $_SESSION
                        if (isset($_SESSION['errors']) && in_array("Identifiant inexistant.", $_SESSION['errors'])) {
                            echo "Adresse email inexistant.";
                            unset($_SESSION['errors']); // Effacez les erreurs après les avoir affichées
                        } else if (isset($_SESSION['errors']) && in_array("Adresse email non confirmée.", $_SESSION['errors'])) {
                            echo "Adresse email pas encore confirmée. Consultez votre boîte mail.";
                            unset($_SESSION['errors']);
                        }
                        ?>
                    </p>
                </div>
                <div>
                    <!-- Faire la redirection vers la page de connection -->
                    <p><a href="../">Connectez vous ici</a>.</p>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary" id="btnConnecter">Valider</button>
                </div>
            </form>

            <script>
            window.onload = function() {
                const emailField = document.getElementById('tbEmail');
                const emailValue =
                    "<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>";
                emailField.value = emailValue;
            };
            </script>
        </div>
    </div>

    <?php


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include('../outils/DBConnexion.php');
        require '../configurations/security.config.php';
        require '../functions/connexionFunctions.php';


        // Récupérer les données du formulaire
        $email = $_POST['tbEmail'];

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

        if ($result->num_rows === 0) {
            // Ajouter l'erreur à la session
            $_SESSION['errors'] = ["Identifiant inexistant."];
        } else {
            $row = $result->fetch_assoc();

            if ($row['Statut'] == 0) {
                $_SESSION['errors'] = ["Adresse email non confirmée."];
            }
            // Vérifier le mot de passe
            else {

                // Préparer l'email de confirmation
                $dest = $email;
                $objet = "Récupération de mot de passe";

                $message = messageRcuperationMdp($email);

                // Envoi de l'email
                sendEmail($dest, $objet, $message);
                exit();
            }
        }

        $mysql->deconnexion();

        // Stocker les données du formulaire dans la session
        $_SESSION['form_data'] = [
            'email' => $email
        ];

        // Rediriger pour recharger la page et afficher les erreurs
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>

    <br>

    <?php include('../composants/footer.php'); ?>