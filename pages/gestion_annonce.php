<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Annonces</title>
   
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
        .nav-link {
            color: #fff;
        }
        .table thead th {
            vertical-align: middle;
        }
        .btn-modify {
            background-color: #28a745;
            color: white;
        }
        .btn-remove {
            background-color: #dc3545;
            color: white;
        }
        .btn-deactivate {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <header class="mb-4">
        <h1 class="text-center my-3">Gestion des Annonces</h1>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a href="#" class="nav-link">Annonces</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Gestion de vos annonces</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Modification du profil</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Déconnexion (test@test.test)</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Gestion de vos annonces</h2>
           
            <button class="btn btn-primary" data-toggle="modal" data-target="#addAnnonceModal">Ajouter</button>
        </div>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>No Annonce</th>
                    <th>Description</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Date de parution</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>3</td>
                    <td><a href="#">Orange</a></td>
                    <td>Recherche</td>
                    <td>3.50 $</td>
                    <td>2021-04-18 00:45:30</td>
                    <td>Actif</td>
                    <td>
                        <button class="btn btn-sm btn-modify">Modification</button>
                        <button class="btn btn-sm btn-remove">Retrait</button>
                        <button class="btn btn-sm btn-deactivate">Désactiver</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>2</td>
                    <td><a href="#">Pomme</a></td>
                    <td>Location</td>
                    <td>4.00 $</td>
                    <td>2021-04-17 00:45:30</td>
                    <td>Actif</td>
                    <td>
                        <button class="btn btn-sm btn-modify">Modification</button>
                        <button class="btn btn-sm btn-remove">Retrait</button>
                        <button class="btn btn-sm btn-deactivate">Désactiver</button>
                    </td>
                </tr>
           
            </tbody>
        </table>
    </main>

 
    <div class="modal fade" id="addAnnonceModal" tabindex="-1" aria-labelledby="addAnnonceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAnnonceModalLabel">Ajout d'une annonce</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAnnonceForm">
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select id="category" class="form-control">
                                <option value="">Sélectionner une catégorie</option>
                                <option value="1">Location</option>
                                <option value="2">Recherche</option>
                                <option value="3">À vendre</option>
                                <option value="4">À donner</option>
                                <option value="5">Service offert</option>
                                <option value="6">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="shortDescription">Petite description:</label>
                            <input type="text" id="shortDescription" class="form-control" placeholder="Petite description">
                        </div>
                        <div class="form-group">
                            <label for="longDescription">Description longue:</label>
                            <textarea id="longDescription" class="form-control" rows="3" placeholder="Description longue"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="price">Prix:</label>
                            <input type="text" id="price" class="form-control" placeholder="Prix">
                        </div>
                        <div class="form-group">
                            <label for="file">Sélectionnez l'image de l'annonce:</label>
                            <input type="file" id="file" class="form-control-file">
                        </div>
                        <div class="form-group">
                            <label for="status">Activé ?</label>
                            <select id="status" class="form-control">
                                <option value="1">Actif</option>
                                <option value="0">Inactif</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler l'ajout de l'annonce</button>
                    <button type="button" class="btn btn-primary" onclick="submitAnnonce()">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Les petites annonces GG</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function submitAnnonce() {
            
            alert("Annonce ajoutée avec succès !");
            $('#addAnnonceModal').modal('hide');
        }
    </script>
</body>
</html>
