<title>FAF - Connexion</title>
</head>

<body>


    <div class="container-fluid my-4">
        <div id="divConnexion" class="col-4 m-auto">
            <h1 class="text-center" id="titreConnexion">Connexion</h1>
            <form id="formConnexion" action="https://424x.cgodin.qc.ca/faf/EnvoieConnexion.php" method="POST">

                <div class="form-group">
                    <label for="tbEmail">Email</label>
                    <input type="email" class="form-control" id="tbEmail" name="tbEmail" placeholder="Email">
                    <p id="errEmail" class="text-danger font-weight-bold"></p>
                </div>

                <div class="form-group">
                    <label for="tbMdp">Mot de passe</label>
                    <input type="password" class="form-control" id="tbMdp" name="tbMdp" placeholder="Mot de passe">
                    <p id="errMdp" class="text-danger font-weight-bold"></p>
                </div>

                <div class="my-1 text-right">
                    <a href="./pages/inscription.php">Créer un compte</a> |
                    <a href="">Mot de passe oublié</a>
                </div>

                <div class="d-flex">
                    <button type="submit" class="btn btn-primary" id="btnConnecter">Connexion</button>

                </div>
            </form>
        </div>
    </div>


    <?php
        //$passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Déboguer
        // echo "<pre>";
        // print_r($passwordHash);
        // echo "</pre>";

        // echo "<pre>";
        // print_r(password_verify("ADve321", "$2y$10$7dm3LbaH/u5ARnPwlVLLnuWNSFN53edXQMEojtjVSFyyCplT22hn2"));
        // echo "</pre>";/
?>