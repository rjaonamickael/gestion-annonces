<?php
session_start();
include '../outils/DBConnexion.php'; // Adjust the path if needed

// Create MySQL connection object
try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$noUtilisateur = 1; // Temporary user number for testing

// Pagination variables
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 5; // Default to 5 items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Sorting variables
$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'Parution'; // Default sorting by date
$orderDir = isset($_GET['orderDir']) && $_GET['orderDir'] === 'asc' ? 'ASC' : 'DESC';

// Search filters
$searchQuery = " WHERE a.Etat = 1 ";
$searchParams = [];

// Filter by author
if (!empty($_GET['author'])) {
    $searchQuery .= " AND (u.Nom LIKE ? OR u.Prenom LIKE ?) ";
    $searchParams[] = '%' . $_GET['author'] . '%';
    $searchParams[] = '%' . $_GET['author'] . '%';
}

// Filter by category
if (!empty($_GET['category'])) {
    $searchQuery .= " AND a.Categorie = ? ";
    $searchParams[] = $_GET['category'];
}

// Filter by date range
if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    $searchQuery .= " AND a.Parution BETWEEN ? AND ? ";
    $searchParams[] = $_GET['startDate'] . ' 00:00:00';
    $searchParams[] = $_GET['endDate'] . ' 23:59:59';
}

// Filter by description
if (!empty($_GET['description'])) {
    $searchQuery .= " AND a.DescriptionAbregee LIKE ? ";
    $searchParams[] = '%' . $_GET['description'] . '%';
}

// Fetch total number of ads
$totalQuery = "SELECT COUNT(*) as total FROM annonces a 
               JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur" . $searchQuery;
$totalStmt = $mysql->cBD->prepare($totalQuery);
if (!empty($searchParams)) {
    $types = str_repeat('s', count($searchParams));
    $totalStmt->bind_param($types, ...$searchParams);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalAds = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalAds / $itemsPerPage);

// Fetch ads for display with pagination, sorting, and filtering
$query = "SELECT a.NoAnnonce, a.Parution, a.Categorie, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, u.NoUtilisateur
          FROM annonces a 
          JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
          $searchQuery
          ORDER BY $orderBy $orderDir 
          LIMIT ? OFFSET ?";
$searchParams[] = $itemsPerPage;
$searchParams[] = $offset;

$stmt = $mysql->cBD->prepare($query);
if (!empty($searchParams)) {
    $types = str_repeat('s', count($searchParams) - 2) . 'ii'; // Add 'ii' for LIMIT and OFFSET
    $stmt->bind_param($types, ...$searchParams);
}
$stmt->execute();
$result = $stmt->get_result();

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
        body { background-color: #f8f9fa; }
        .container { margin-top: 20px; }
        .card { width: 18rem; margin-bottom: 20px; }
        .card img { width: 100%; height: auto; }
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
            <li class="nav-item"><a href="deconnexion.php" class="nav-link">Déconnexion (test@test.test)</a></li>
        </ul>
    </nav>
</header>

<main class="container">
    <div class="d-flex justify-content-between mb-3">
        <div>
            <label for="itemsPerPage">Éléments par page :</label>
            <select id="itemsPerPage" onchange="window.location.href='?itemsPerPage=' + this.value">
                <?php foreach ([5, 10, 15, 20] as $option): ?>
                    <option value="<?php echo $option; ?>" <?php echo $itemsPerPage == $option ? 'selected' : ''; ?>>
                        <?php echo $option; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div> 
            <strong><?php echo $totalAds; ?> annonces trouvées.</strong>
        </div>
        <div>
            <label for="orderBy">Ordre :</label>
            <select id="orderBy" onchange="window.location.href='?orderBy=' + this.value + '&orderDir=' + document.getElementById('orderDir').value">
                <option value="Parution" <?php echo $orderBy == 'Parution' ? 'selected' : ''; ?>>Date</option>
                <option value="Nom" <?php echo $orderBy == 'Nom' ? 'selected' : ''; ?>>Auteur</option>
                <option value="Categorie" <?php echo $orderBy == 'Categorie' ? 'selected' : ''; ?>>Catégorie</option>
            </select>
            <select id="orderDir" onchange="window.location.href='?orderBy=' + document.getElementById('orderBy').value + '&orderDir=' + this.value">
                <option value="asc" <?php echo $orderDir == 'ASC' ? 'selected' : ''; ?>>▲</option>
                <option value="desc" <?php echo $orderDir == 'DESC' ? 'selected' : ''; ?>>▼</option>
            </select>
        </div>
        <div>
            <form method="get" action="afficher_annonces.php">
                <input type="text" name="description" placeholder="Recherche" value="<?php echo htmlspecialchars($_GET['description'] ?? ''); ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
    </div>

    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3">
                    <div class="card">
                        <img src="../photos/<?php echo htmlspecialchars($row['Photo']); ?>" class="card-img-top" alt="Image de l'annonce">
                        <div class="card-body">
                            <h5 class="card-title">#<?php echo htmlspecialchars($row['NoAnnonce']); ?> - <?php echo htmlspecialchars($row['Categorie']); ?></h5>
                            <p class="card-text"><a href="#"><?php echo htmlspecialchars($row['DescriptionAbregee']); ?></a></p>
                            <p><?php echo htmlspecialchars($row['Nom']) . ', ' . htmlspecialchars($row['Prenom']); ?></p>
                            <p><?php echo number_format($row['Prix'], 2, ',', ' ') . " $"; ?></p>
                            <p><?php echo date('Y-m-d H:i:s', strtotime($row['Parution'])); ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Aucune annonce trouvée.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination controls -->
    <div class="d-flex justify-content-center">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=1">◀◀</a>
                </li>
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">◀</a>
                </li>
                <li class="page-item active">
                    <span class="page-link"><?php echo $page; ?></span>
                </li>
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">▶</a>
                </li>
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $totalPages; ?>">▶▶</a>
                </li>
            </ul>
        </nav>
    </div>
</main>

<footer class="text-center mt-4">
    <p>&copy; 2024 Les petites annonces GG</p>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
