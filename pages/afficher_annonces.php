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

header {
    background-color: #343a40; 
    color: #fff; 
    padding: 10px 20px;
}

header h1 {
    margin: 0;
    font-size: 1.5rem;
}

nav .navbar-nav .nav-link {
    color: #fff; 
}

.container {
    margin-top: 20px;
}

.control-panel {
    margin-bottom: 20px;
    padding: 10px 0;
    border-bottom: 1px solid #ddd; 
}

.annonces-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.annonce-card {
    flex: 1 1 calc(25% - 20px);
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    transition: transform 0.2s ease-in-out;
}

.annonce-card:hover {
    transform: scale(1.03); 
}

.annonce-card img {
    width: 100%;
    height: auto;
    max-height: 180px; 
    object-fit: cover;
}

.annonce-card h2 {
    font-size: 1.1rem;
    margin-top: 10px;
    margin-bottom: 5px;
}

.annonce-card .author,
.annonce-card .price,
.annonce-card .date {
    font-size: 0.9rem;
    color: #6c757d; 
}

.pagination {
    margin-top: 20px;
}

.pagination .btn {
    padding: 5px 10px;
}

	</style>
</head>

<body>
    <header class="mb-4">
        <h1 class="text-center my-3">Annonces</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a href="#" class="nav-link">Annonces</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Gestion de vos annonces</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Modification du profil</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Déconnexion (test@test.test)</a></li>
            </ul>
        </nav>
        <div class="container mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <label for="elementsPerPage">Éléments par page :</label>
                    <select id="elementsPerPage" class="custom-select w-auto">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                    <span>20 annonces trouvées.</span>
                </div>
                <div>
                    <label for="sortOrder">Ordre :</label>
                    <select id="sortOrder" class="custom-select w-auto">
                        <option value="date">Date</option>
                        <option value="author">Auteur</option>
                        <option value="category">Catégorie</option>
                    </select>
                    <button id="sortDirection" class="btn btn-light">▲</button>
                    <button class="btn btn-primary">Rechercher</button>
                    <button class="btn btn-secondary" data-toggle="collapse" data-target="#advancedSearch">+</button>
                </div>
            </div>
            <div id="advancedSearch" class="collapse mt-3">
                <div class="card card-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="author">Auteur :</label>
                            <input type="text" class="form-control" id="author">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="category">Catégorie :</label>
                            <select id="category" class="form-control">
                                <option value="">Toutes</option>
                                <option value="1">Location</option>
                                <option value="2">Recherche</option>
                                <option value="3">À vendre</option>
                                <option value="4">À donner</option>
                                <option value="5">Service offert</option>
                                <option value="6">Autre</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="dateRange">Date :</label>
                            <div class="d-flex">
                                <input type="date" class="form-control mr-2" id="dateStart">
                                <input type="date" class="form-control" id="dateEnd">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <section id="annonces-container" class="row">
            <div class="col-md-3">
                <div class="card mb-4">
                    <img src="photos-annonce/cat.jpg" class="card-img-top" alt="Deux chateaux">
                    <div class="card-body">
                        <h5 class="card-title"><a href="/photos/pr.jpg">Deux chateaux</a></h5>
                        <p class="card-text">N2Test, P2Test</p>
                        <p class="card-text">500999.00 $</p>
                        <p class="card-text">2021-05-15 00:45:30</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-4">
                    <img src="photos-annonce/pr.jpg" class="card-img-top" alt="Une maison">
                    <div class="card-body">
                        <h5 class="card-title"><a href="#">Une maison</a></h5>
                        <p class="card-text">N2Test, P2Test</p>
                        <p class="card-text">55000.00 $</p>
                        <p class="card-text">2021-05-12 00:45:30</p>
                    </div>
                </div>
            </div>
          
        </section>

        <div class="pagination d-flex justify-content-center">
            <button onclick="prevPage()" class="btn btn-outline-primary mr-2">◀</button>
            <span>Page <span id="currentPage">1</span> de <span id="totalPages">4</span></span>
            <button onclick="nextPage()" class="btn btn-outline-primary ml-2">▶</button>
        </div>
    </main>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Les petites annonces GG</p>
    </footer>

  
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="scripts/annonces.js"></script>
</body>
</html>
