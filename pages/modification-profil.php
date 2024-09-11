<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise à jour du Profil</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 20px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-control[readonly] {
            background-color: #e9ecef;
        }
        .form-check-label {
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Mise à jour du profil</h2>
        <form>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" value="test@test.test" readonly>
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <a href="#" class="d-block mb-2">Accédez à la modification de mot de passe ici</a>
            </div>
            <div class="form-group">
                <label for="status">Statut</label>
                <select id="status" class="form-control">
                    <option value="Enseignant">Enseignant</option>
                    <option value="Administratif">Administratif</option>
                    <option value="Autre">Autre</option>
                </select>
            </div>
            <div class="form-group">
                <label for="employeeNumber">Numéro Emploi</label>
                <input type="text" class="form-control" id="employeeNumber" placeholder="1">
            </div>
            <div class="form-group">
                <label for="lastName">Nom</label>
                <input type="text" class="form-control" id="lastName" placeholder="NTest">
            </div>
            <div class="form-group">
                <label for="firstName">Prénom</label>
                <input type="text" class="form-control" id="firstName" placeholder="PTest">
            </div>
            <div class="form-group">
                <label for="homePhone">Numéro Téléphone Maison</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" id="homePhone" placeholder="(111) 111-1111">
                    <div class="form-check ml-2">
                        <input type="checkbox" class="form-check-input" id="homePrivate">
                        <label class="form-check-label" for="homePrivate">Privé ?</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="workPhone">Numéro Téléphone Bureau</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" id="workPhone" placeholder="(111) 111-1112">
                    <input type="text" class="form-control ml-2" id="workExtension" placeholder="Poste">
                    <div class="form-check ml-2">
                        <input type="checkbox" class="form-check-input" id="workPrivate">
                        <label class="form-check-label" for="workPrivate">Privé ?</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="cellPhone">Numéro Téléphone Cellulaire</label>
                <div class="d-flex align-items-center">
                    <input type="tel" class="form-control" id="cellPhone" placeholder="(111) 111-1113">
                    <div class="form-check ml-2">
                        <input type="checkbox" class="form-check-input" id="cellPrivate">
                        <label class="form-check-label" for="cellPrivate">Privé ?</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Enregistrer</button>
        </form>
        <a href="#" class="d-block text-center mt-3">Retour à la liste des annonces</a>
    </div>

   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
