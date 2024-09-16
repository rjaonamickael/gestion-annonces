<?php 
session_start();
include '../outils/DBConnexion.php'; 

// Connexion à la base de données
try {
    $mysql = new MySQL('projet2', str_replace(".", "-", $_SERVER["SERVER_NAME"]) . ".php");
    $mysql->connexion();
    $mysql->selectionneBD();
} catch (Exception $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$noUtilisateur = 1; 

// Gestion de la pagination et du tri
$itemsPerPage = isset($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$orderBy = isset($_GET['orderBy']) ? $_GET['orderBy'] : 'Parution';
$orderDir = isset($_GET['orderDir']) && $_GET['orderDir'] === 'asc' ? 'ASC' : 'DESC';

$searchQuery = " WHERE a.Etat = 1 ";
$searchParams = [];

// Gestion des filtres de recherche
if (!empty($_GET['author'])) {
    $searchQuery .= " AND (u.Nom LIKE ? OR u.Prenom LIKE ?) ";
    $searchParams[] = '%' . $_GET['author'] . '%';
    $searchParams[] = '%' . $_GET['author'] . '%';
}

if (!empty($_GET['category'])) {
    $searchQuery .= " AND a.Categorie = ? ";
    $searchParams[] = $_GET['category'];
}

if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    $searchQuery .= " AND a.Parution BETWEEN ? AND ? ";
    $searchParams[] = $_GET['startDate'] . ' 00:00:00';
    $searchParams[] = $_GET['endDate'] . ' 23:59:59';
}

if (!empty($_GET['description'])) {
    $searchQuery .= " AND a.DescriptionAbregee LIKE ? ";
    $searchParams[] = '%' . $_GET['description'] . '%';
}

// Requête pour compter le nombre total d'annonces
$totalQuery = "SELECT COUNT(*) as total FROM annonces a 
               JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
               $searchQuery";
$totalStmt = $mysql->cBD->prepare($totalQuery);
if ($totalStmt === false) {
    die("Erreur lors de la préparation de la requête de comptage : " . $mysql->cBD->error);
}
if (!empty($searchParams)) {
    $types = str_repeat('s', count($searchParams));
    $totalStmt->bind_param($types, ...$searchParams);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalAds = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalAds / $itemsPerPage);

// Requête  pour récupérer les annonces avec les filtres 
$query = "SELECT a.NoAnnonce, a.Parution, c.Description as Categorie, a.DescriptionAbregee, a.Prix, a.Photo, a.Etat, u.Nom, u.Prenom, u.NoUtilisateur
          FROM annonces a 
          JOIN utilisateurs u ON a.NoUtilisateur = u.NoUtilisateur
          JOIN categories c ON a.Categorie = c.NoCategorie
          $searchQuery
          ORDER BY $orderBy $orderDir 
          LIMIT ? OFFSET ?";
$searchParams[] = $itemsPerPage;
$searchParams[] = $offset;

$stmt = $mysql->cBD->prepare($query);
if ($stmt === false) {
    die("Erreur lors de la préparation de la requête principale : " . $mysql->cBD->error);
}
if (!empty($searchParams)) {
    $types = str_repeat('s', count($searchParams) - 2) . 'ii'; 
    $stmt->bind_param($types, ...$searchParams);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die("Erreur lors de l'exécution de la requête principale : " . $stmt->error);
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

    .card {
        width: 100%;
        margin-bottom: 20px;
    }

    .card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    @media (max-width: 1200px) {
        .grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }

    .advanced-search {
        display: none;
        margin-top: 10px;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
    }

    body {
        background-color: #f8f9fa;
    }

    .container {
        margin-top: 20px;
    }

    .card {
        width: 100%;
        margin-bottom: 20px;
    }

    .card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
    }

    @media (max-width: 1200px) {
        .grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 992px) {
        .grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }

    .advanced-search {
        display: none;
        margin-top: 10px;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
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
                <li class="nav-item"><a href="deconnexion.php" class="nav-link">Déconnexion (test@test.test)</a></li>
            </ul>
            <form class="form-inline" method="get" action="afficher_annonces.php">
                <input type="text" name="description" placeholder="Recherche"
                    value="<?php echo htmlspecialchars($_GET['description'] ?? ''); ?>" class="form-control mr-2">
                <button type="submit" class="btn btn-primary mr-2">Rechercher</button>
                <button class="btn btn-primary" type="button" onclick="toggleAdvancedSearch()">+</button>
            </form>
        </nav>
    </header>

    <main class="container">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <label for="itemsPerPage">Éléments par page :</label>
                <select id="itemsPerPage" onchange="window.location.href='?itemsPerPage=' + this.value">
                    <?php foreach ([5, 10, 15, 20] as $option): ?>
                    <main class="container">
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <label for="itemsPerPage">Éléments par page :</label>
                                <select id="itemsPerPage"
                                    onchange="window.location.href='?itemsPerPage=' + this.value + '&page=1<?php echo '&author=' . urlencode($_GET['author'] ?? '') . '&category=' . urlencode($_GET['category'] ?? '') . '&startDate=' . urlencode($_GET['startDate'] ?? '') . '&endDate=' . urlencode($_GET['endDate'] ?? '') . '&description=' . urlencode($_GET['description'] ?? '') . '&orderBy=' . urlencode($_GET['orderBy'] ?? 'Parution') . '&orderDir=' . urlencode($_GET['orderDir'] ?? 'DESC'); ?>'">
                                    <?php foreach ([5, 10, 15, 20] as $option): ?>
                                    <option value="<?php echo $option; ?>"
                                        <?php echo $itemsPerPage == $option ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <strong><?php echo $totalAds; ?> annonces trouvées.</strong>
                            </div>
                            <div class="d-flex align-items-center">
                                <label for="orderBy" class="mr-2">Ordre :</label>
                                <select id="orderBy" class="mr-2"
                                    onchange="window.location.href='?orderBy=' + this.value + '&orderDir=' + document.getElementById('orderDir').value">
                                    <option value="Parution" <?php echo $orderBy == 'Parution' ? 'selected' : ''; ?>>
                                        Date</option>
                                    <option value="Nom" <?php echo $orderBy == 'Nom' ? 'selected' : ''; ?>>Auteur
                                    </option>
                                    <option value="Categorie" <?php echo $orderBy == 'Categorie' ? 'selected' : ''; ?>>
                                        Catégorie
                                    </option>
                                </select>
                                <select id="orderDir" class="mr-2"
                                    onchange="window.location.href='?orderBy=' + document.getElementById('orderBy').value + '&orderDir=' + this.value">
                                    <option value="asc" <?php echo $orderDir == 'ASC' ? 'selected' : ''; ?>>▲</option>
                                    <option value="desc" <?php echo $orderDir == 'DESC' ? 'selected' : ''; ?>>▼</option>
                                </select>
                                <form method="get" action="afficher_annonces.php" class="d-flex align-items-center">
                                    <input type="text" name="description" placeholder="Recherche"
                                        value="<?php echo htmlspecialchars($_GET['description'] ?? ''); ?>"
                                        class="form-control mr-2">
                                    <button type="submit" class="btn btn-primary">Rechercher</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                </select>
            </div>
            <div>
                <strong><?php echo $totalAds; ?> annonces trouvées.</strong>
            </div>
            <div class="d-flex align-items-center">
                <label for="orderBy" class="mr-2">Ordre :</label>
                <select id="orderBy" class="mr-2"
                    onchange="window.location.href='?orderBy=' + this.value + '&orderDir=' + document.getElementById('orderDir').value">
                    <option value="Parution" <?php echo $orderBy == 'Parution' ? 'selected' : ''; ?>>Date</option>
                    <option value="Nom" <?php echo $orderBy == 'Nom' ? 'selected' : ''; ?>>Auteur</option>
                    <option value="Categorie" <?php echo $orderBy == 'Categorie' ? 'selected' : ''; ?>>Catégorie
                    </option>
                </select>
                <select id="orderDir" class="mr-2"
                    onchange="window.location.href='?orderBy=' + document.getElementById('orderBy').value + '&orderDir=' + this.value">
                    <option value="asc" <?php echo $orderDir == 'ASC' ? 'selected' : ''; ?>>▲</option>
                    <option value="desc" <?php echo $orderDir == 'DESC' ? 'selected' : ''; ?>>▼</option>
                </select>
            </div>
        </div>

        <!-- Advanced Search Form -->
        <div class="advanced-search">
            <form method="get" action="afficher_annonces.php">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="author">Auteur :</label>
                        <input type="text" class="form-control" id="author" name="author"
                            value="<?php echo htmlspecialchars($_GET['author'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="category">Catégorie :</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Toutes</option>
                            <option value="Location"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'Location') ? 'selected' : ''; ?>>
                                Location</option>
                            <option value="Recherche"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'Recherche') ? 'selected' : ''; ?>>
                                Recherche</option>
                            <option value="À vendre"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'À vendre') ? 'selected' : ''; ?>>
                                À vendre</option>
                            <option value="À donner"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'À donner') ? 'selected' : ''; ?>>
                                À donner</option>
                            <option value="Service offert"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'Service offert') ? 'selected' : ''; ?>>
                                Service offert</option>
                            <option value="Autre"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == 'Autre') ? 'selected' : ''; ?>>
                                Autre</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="date">Date :</label>
                        <div class="d-flex">
                            <input type="date" class="form-control mr-1" name="startDate"
                                value="<?php echo htmlspecialchars($_GET['startDate'] ?? ''); ?>">
                            <input type="date" class="form-control" name="endDate"
                                value="<?php echo htmlspecialchars($_GET['endDate'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Appliquer les filtres</button>
            </form>
        </div>
        <!-- Formulaire de recherche  -->
        <div class="advanced-search">
            <form method="get" action="afficher_annonces.php">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="author">Auteur :</label>
                        <input type="text" class="form-control" id="author" name="author"
                            value="<?php echo htmlspecialchars($_GET['author'] ?? ''); ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="category">Catégorie :</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Toutes</option>
                            <option value="1"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '1') ? 'selected' : ''; ?>>
                                Location</option>
                            <option value="2"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '2') ? 'selected' : ''; ?>>
                                Recherche</option>
                            <option value="3"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '3') ? 'selected' : ''; ?>>
                                À vendre</option>
                            <option value="4"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '4') ? 'selected' : ''; ?>>
                                À donner</option>
                            <option value="5"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '5') ? 'selected' : ''; ?>>
                                Service offert</option>
                            <option value="6"
                                <?php echo (isset($_GET['category']) && $_GET['category'] == '6') ? 'selected' : ''; ?>>
                                Autre</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="date">Date :</label>
                        <div class="d-flex">
                            <input type="date" class="form-control mr-1" name="startDate"
                                value="<?php echo htmlspecialchars($_GET['startDate'] ?? ''); ?>">
                            <input type="date" class="form-control" name="endDate"
                                value="<?php echo htmlspecialchars($_GET['endDate'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary">Appliquer les filtres</button>
            </form>
        </div>

        <div class="grid-container">
            <?php if ($result && $result->num_rows > 0): ?>
            <!-- Affichage des annonces sous forme de cartes -->
            <div class="grid-container">
                <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="../photos/<?php echo htmlspecialchars($row['Photo']); ?>" class="card-img-top"
                        alt="Image de l'annonce">
                    <div class="card-body">
                        <h5 class="card-title">#<?php echo htmlspecialchars($row['NoAnnonce']); ?> -
                            <?php echo htmlspecialchars($row['Categorie']); ?></h5>
                        <p class="card-text"><a href="#"><?php echo htmlspecialchars($row['DescriptionAbregee']); ?></a>
                        </p>
                        <p><?php echo htmlspecialchars($row['Nom']) . ', ' . htmlspecialchars($row['Prenom']); ?></p>
                        <p><?php echo number_format($row['Prix'], 2, ',', ' ') . " $"; ?></p>
                        <p><?php echo date('Y-m-d H:i:s', strtotime($row['Parution'])); ?></p>
                    </div>
                </div>
                <div class="card">
                    <img src="../photos/<?php echo htmlspecialchars($row['Photo']); ?>" class="card-img-top"
                        alt="Image de l'annonce">
                    <div class="card-body">
                        <h5 class="card-title">#<?php echo htmlspecialchars($row['NoAnnonce']); ?> -
                            <?php echo htmlspecialchars($row['Categorie']); ?></h5>
                        <p class="card-text"><a
                                href="afficher_annonce.php?NoAnnonce=<?php echo htmlspecialchars($row['NoAnnonce']); ?>"><?php echo htmlspecialchars($row['DescriptionAbregee']); ?></a>
                        </p>
                        <p><?php echo htmlspecialchars($row['Nom']) . ', ' . htmlspecialchars($row['Prenom']); ?></p>
                        <p><?php echo number_format($row['Prix'], 2, ',', ' ') . " $"; ?></p>
                        <p><?php echo date('Y-m-d H:i:s', strtotime($row['Parution'])); ?></p>
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
    <!-- Contrôles de pagination -->
    <div class="d-flex justify-content-center mt-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link"
                        href="?page=1&itemsPerPage=<?php echo $itemsPerPage; ?>&author=<?php echo urlencode($_GET['author'] ?? ''); ?>&category=<?php echo urlencode($_GET['category'] ?? ''); ?>&startDate=<?php echo urlencode($_GET['startDate'] ?? ''); ?>&endDate=<?php echo urlencode($_GET['endDate'] ?? ''); ?>&description=<?php echo urlencode($_GET['description'] ?? ''); ?>&orderBy=<?php echo urlencode($_GET['orderBy'] ?? 'Parution'); ?>&orderDir=<?php echo urlencode($_GET['orderDir'] ?? 'DESC'); ?>">◀◀</a>
                </li>
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $page - 1; ?>&itemsPerPage=<?php echo $itemsPerPage; ?>&author=<?php echo urlencode($_GET['author'] ?? ''); ?>&category=<?php echo urlencode($_GET['category'] ?? ''); ?>&startDate=<?php echo urlencode($_GET['startDate'] ?? ''); ?>&endDate=<?php echo urlencode($_GET['endDate'] ?? ''); ?>&description=<?php echo urlencode($_GET['description'] ?? ''); ?>&orderBy=<?php echo urlencode($_GET['orderBy'] ?? 'Parution'); ?>&orderDir=<?php echo urlencode($_GET['orderDir'] ?? 'DESC'); ?>">◀</a>
                </li>
                <li class="page-item active">
                    <span class="page-link"><?php echo $page; ?></span>
                </li>
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $page + 1; ?>&itemsPerPage=<?php echo $itemsPerPage; ?>&author=<?php echo urlencode($_GET['author'] ?? ''); ?>&category=<?php echo urlencode($_GET['category'] ?? ''); ?>&startDate=<?php echo urlencode($_GET['startDate'] ?? ''); ?>&endDate=<?php echo urlencode($_GET['endDate'] ?? ''); ?>&description=<?php echo urlencode($_GET['description'] ?? ''); ?>&orderBy=<?php echo urlencode($_GET['orderBy'] ?? 'Parution'); ?>&orderDir=<?php echo urlencode($_GET['orderDir'] ?? 'DESC'); ?>">▶</a>
                </li>
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link"
                        href="?page=<?php echo $totalPages; ?>&itemsPerPage=<?php echo $itemsPerPage; ?>&author=<?php echo urlencode($_GET['author'] ?? ''); ?>&category=<?php echo urlencode($_GET['category'] ?? ''); ?>&startDate=<?php echo urlencode($_GET['startDate'] ?? ''); ?>&endDate=<?php echo urlencode($_GET['endDate'] ?? ''); ?>&description=<?php echo urlencode($_GET['description'] ?? ''); ?>&orderBy=<?php echo urlencode($_GET['orderBy'] ?? 'Parution'); ?>&orderDir=<?php echo urlencode($_GET['orderDir'] ?? 'DESC'); ?>">▶▶</a>
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
    <script>
    function toggleAdvancedSearch() {
        var advancedSearch = document.querySelector('.advanced-search');
        if (advancedSearch.style.display === 'none' || advancedSearch.style.display === '') {
            advancedSearch.style.display = 'block';
        } else {
            advancedSearch.style.display = 'none';
        }
    }
    </script>
</body>

</html>