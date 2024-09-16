<?php include('./composants/header.php');?>

<?php 
//echo 'zer'. isset($_SESSION['Courriel']);

if(isset($_SESSION['Courriel'])){
    include('./pages/afficher_annonces.php');
}else{
    include('./pages/connexion.php');
}
;?>

<?php include('./composants/footer.php');?>

<?php 
include('./outils/DBInitialize.php');
?>