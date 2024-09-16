<?php
include('../composants/header.php');
include '../outils/DBConnexion.php';
?>

<title>FAF - Confirmation</title>
</head>

<body style="margin-top: 50px;">

    <div class="container-fluid my-4">
        <div id="divConnexion" class="col-4 m-auto">
            <h1 class="text-center" id="titreConnexion" style="margin-bottom: 50px;">Confirmation</h1>

            <?php
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


            // Vérifie si l'email est passé en paramètre GET
            if (isset($_GET['email'])) {
                $email = $_GET['email'];


                // Requête SQL préparée
                $query = $mysql->cBD->prepare("SELECT Statut FROM utilisateurs WHERE Courriel = ?");

                // Lier la variable $emailAdmin à ce "placeholder"
                $query->bind_param('s', $email);

                // Exécuter la requête
                $query->execute();

                // Récupérer le résultat
                $statut = 0;
                $query->bind_result($statut);
                $query->fetch();
                $query->close();

                //vérifier le statut du compte si en attente ou confirmé
                if ($statut === 0) {
                    // Requête SQL préparée
                    $query = $mysql->cBD->prepare("UPDATE utilisateurs SET Statut = 9 WHERE Courriel = ?");

                    // Lier la variable $emailAdmin à ce "placeholder"
                    $query->bind_param('s', $email);

                    // Exécuter la requête
                    $query->execute();

                    // Récupérer le résultat
                    $query->close();

                    echo '<div class="form-group">
                            <label>Votre adresse mail a été confirmée avec succès.</label>
                          </div>';
                } else {
                    echo '<div class="form-group">
                            <label>Votre adresse mail a déjà été confirmée.</label>
                          </div>';
                }
            } else {

            }
            ?>

            <div>
                <!-- Redirection vers la page de connexion -->
                <p> <a href="../">Connectez-vous ici</a>.</p>
            </div>

        </div>
    </div>

    <?php include('../composants/footer.php'); ?>