<?php
session_start();
include '../outils/DBConnexion.php'; 

try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$noUtilisateur = 1; 

$query = "SELECT a.NoAnnonce, a.Parution, c.Description as Categorie, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat 
          FROM annonces a 
          JOIN categories c ON a.Categorie = c.NoCategorie
          ORDER BY a.Parution DESC 
          LIMIT 10";
$result = $mysql->cBD->query($query);

if ($result === false) {
    die("Erreur lors de l'exécution de la requête : " . $mysql->cBD->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affichage des Annonces</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background-color: #f8f9fa;
    }
    .container {
        margin-top: 20px;
    }
    table {
        width: 100%;
    }
    img {
        max-width: 100px;
        height: auto;
    }
    </style>
</head>
<body>
    <header class="mb-4">
        <h1 class="text-center my-3">Annonces</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a href="afficher_annonces.php" class="nav-link">Annonces</a></li>
                <li class="nav-item"><a href="gestion_annonce.php" class="nav-link">Gestion de vos annonces</a></li>
                <li class="nav-item"><a href="modification_profil.php" class="nav-link">Modification du profil</a></li>
                <li class="nav-item"><a href="../index.php" class="nav-link">Déconnexion (test@test.test)</a></li>
            </ul>
           
            <a href="AjoutAnnonce.php" class="btn btn-primary">Ajouter</a>
        </nav>
    </header>

    <main class="container">
        <section id="annonces-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Date de parution</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['NoAnnonce']); ?></td>
                        <td><img src="../photos/<?php echo htmlspecialchars($row['Photo']); ?>" alt="Image de l'annonce"></td>
                        <td><a href="#"><?php echo htmlspecialchars($row['DescriptionAbregee']); ?></a></td>
                        <td><?php echo htmlspecialchars($row['Categorie']); ?></td>
                        <td><?php echo $row['Prix'] !== null ? number_format($row['Prix'], 2, ',', ' ') . " $" : 'N/A'; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($row['Parution'])); ?></td>
                        <td><?php echo $row['Etat'] == 1 ? 'Actif' : 'Inactif'; ?></td>
                        <td>
                            <a href="modifier_annonce.php?NoAnnonce=<?php echo $row['NoAnnonce']; ?>" class="btn btn-success">Modification</a>
                            <a href="supprimer_annonce.php?NoAnnonce=<?php echo $row['NoAnnonce']; ?>" class="btn btn-danger">Retrait</a>
                            <a href="toggle_status.php?NoAnnonce=<?php echo $row['NoAnnonce']; ?>&etat=<?php echo $row['Etat']; ?>" 
                               class="btn btn-secondary">
                               <?php echo $row['Etat'] == 1 ? 'Désactiver' : 'Activer'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Aucune annonce trouvée.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Les petites annonces GG</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
