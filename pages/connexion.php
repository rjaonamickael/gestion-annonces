<title>FAF - Connexion</title>
</head>

<body>

    <div class="container-fluid my-4">
        <div id="divConnexion" class="col-4 m-auto">
            <h1 class="text-center" id="titreConnexion">Connexion</h1>
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
                        }else if(isset($_SESSION['errors']) && in_array("Adresse email non confirmée.", $_SESSION['errors'])){
                            echo "Adresse email pas encore confirmée. Consultez votre boîte mail.";
                            unset($_SESSION['errors']);
                        }
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label for="tbMdp">Mot de passe</label>
                    <input type="password" class="form-control" id="tbMdp" name="tbMdp" placeholder="Mot de passe"
                        autocomplete="off">
                    <p id="errMdp" class="text-danger font-weight-bold">
                        <?php
                        
                        if (isset($_SESSION['errors']) && in_array("Mot de passe incorrect.", $_SESSION['errors'])) {
                            echo "Mot de passe incorrect.";
                            unset($_SESSION['errors']);
                        }
                        ?>
                    </p>
                </div>

                <div class="my-1 text-right">
                    <a href="./pages/inscription.php">Créer un compte</a> |
                    <a href="./pages/insertionMailRecuperationMdp.php">Mot de passe oublié</a>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary" id="btnConnecter">Connexion</button>
                </div>
            </form>

            <script>
            window.onload = function() {
                const emailField = document.getElementById('tbEmail');
                const emailValue =
                    "<?php echo isset($_SESSION['form_data']['email']) ? $_SESSION['form_data']['email'] : ''; ?>";
                emailField.value = emailValue;

                const passwordField = document.getElementById('tbMdp');
                passwordField.value = ""; // Toujours effacer le mot de passe pour la sécurité
            };
            </script>
        </div>
    </div>

    <?php


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include('./outils/DBConnexion.php');
        require './configurations/security.config.php';
        

        // Récupérer les données du formulaire
        $email = $_POST['tbEmail'];
        $password = $_POST['tbMdp'];

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
            $userId = $row['NoUtilisateur'];
            
            // Recréer le hash avec le mot de passe fourni et le salt récupéré
            $passwordHashed = hash(HASH_TYPE, $password);

            if($row['Statut'] == 0){
                $_SESSION['errors'] = ["Adresse email non confirmée."];            
            }
            // Vérifier le mot de passe
            else if ($passwordHashed === $row['MotDePasse']) {
                // Connexion réussie
                $queryUpdateUtilisateur = "UPDATE utilisateurs SET NbConnexions = NbConnexions + 1 WHERE NoUtilisateur = ?";
                $stmtUpdate = $mysql->cBD->prepare($queryUpdateUtilisateur);
                $stmtUpdate->bind_param("i", $userId);
                $stmtUpdate->execute();

                // Insérer une ligne dans la table connexions
                $queryInsertConnexion = "INSERT INTO connexions (NoUtilisateur, Connexion) VALUES (?, NOW())";
                $stmtInsert = $mysql->cBD->prepare($queryInsertConnexion);
                $stmtInsert->bind_param("i", $userId);
                $stmtInsert->execute();

                // Déconnexion
                $mysql->deconnexion();
                header("Location: ../pages/gestion_annonce.php");
                exit();
            } else {
                $_SESSION['errors'] = ["Mot de passe incorrect."];
            }
        }

        $mysql->deconnexion();

       
        $_SESSION['form_data'] = [
            'email' => $email
        ];

        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>

    <br>