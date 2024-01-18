<?php

require_once(__DIR__ . '/../Connexion/connexionBDD.php');
$query_result = null;
if (isset($_GET["recherche"])) {
    $query = $_GET["recherche"];
    var_dump($query).PHP_EOL;
    $sql = "SELECT p.id_produit, p.nom_produit, p.prix 
    FROM produits as p
    WHERE p.nom_produit LIKE :query
    OR p.description LIKE :query";
    $stmt = $mysqlClient->prepare($sql);
    $stmt->bindParam(':query', $param_query, PDO::PARAM_STR);
    $param_query = '%'.$query.'%';
    $stmt->execute();
    $query_result = $stmt->fetchAll();
    var_dump($query_result).PHP_EOL;
} 
// try {
//     $dbh = new PDO('mysql:host=127.0.0.1;dbname=maquettisme;port=3306;charset=utf8mb4', 'root', '');
//     $stmt = $dbh->query('SELECT * FROM produits');
//     $les_produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     $nb = count($les_produits);
//     $dbh = null;

// } catch (Exception $e) {
//     $message = $e->getMessage();
//     echo ''. $message .'';
// }
// // var_dump($les_produits).PHP_EOL;
// for ($i = 0; $i < count($les_produits); $i++) {
//     $tab_produit = $les_produits[$i];
// }

// echo "<br>";
// var_dump($tab_produit) . PHP_EOL;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat recherche</title>
    <?php require_once(__DIR__ . '/importCSS.php'); ?>
</head>
<body>
    <div class="container-page-grid">
        <header class="header-recherche">
            <?php require_once(__DIR__ . '/header.php'); ?>
        </header>
        <div class="sidebar">
            <ol>
                <li><a href="#">Maquettes de voiture</a></li>
                <li><a href="#">Maquettes de moto</a></li>
                <li><a href="#">Maquettes militaire</a>
                    <ul>
                        <li><a href="#">Maquettes militaire 1/16</a></li>
                        <li><a href="#">Maquettes militaire 1/35</a></li>
                        <li><a href="#">Maquettes militaire 1/48</a></li>
                    </ul>
                </li>
                <li><a href="#">Peintures</a></li>
                <li><a href="#">Accessoires</a></li>
            </ol>
        </div>
        <div class="main-body">
            <div class="product-detail-container">
                <?php if (is_null($query_result) || count($query_result)==0): ?>
                   <div class="no-result">Pas de résultat pour votre recherche : <?=$query?></div>
                <?php else : ?>
                   <?php foreach ($query_result as $value): ?>
                    <div class="product">
                        <div class="product__image">
                        <a class="product_main__link" quickfix="" href="/tamiya_fr/categories/maquettes-en-plastique/maquettes-de-voitures/maquettes-de-voitures-1-24/1-24-subaru-brz-td8-300024362-fr.html" title="1:24 Subaru BRZ (TD8)">
                            <img class="product__image-box" loading="lazy" src="/Lotra3/Ressources/assets_categories/maquette-char.jpg" title="1:24 Subaru BRZ (TD8)" alt="1:24 Subaru BRZ (TD8)">
                        </div>
                        <div class="product__content">
                            <p class="product__subtitle"><?=$value['nom_produit']?></p>
                            <h3 class="product__title"><?=$value['nom_produit']?></h3>
                            <small class="product__sku"><?=$value['id_produit']?></small>
                            <p class="product__price"><?=$value['prix']." €" ?></p>
                            <p class="product__text"></p> 
                        </div>
                        </a>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
            </div>
        </div>
    </div>  
</body>
</html>