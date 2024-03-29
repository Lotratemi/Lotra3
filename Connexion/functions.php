<?php
require_once(__DIR__ . "/connexionBDD.php");

/**
 * Retourne l'ensemble des couples id_marque et nom_marque depuis la base de données
 */
function getAllBrands($mysqlClient) {
    $sql = "SELECT id_marque, nom_marque FROM marques;";
    $stmt = $mysqlClient->prepare($sql);
    $stmt->execute();
    $brands = $stmt->fetchAll(PDO::FETCH_NUM);  
    return $brands;
}

/**
 * Retourne l'ensemble des couples id_categorie et nom_categorie depuis la base de données
 */
function getAllCategories($mysqlClient) {
    $sql = "SELECT id_categorie, nom_categorie FROM categories;";
    $stmt = $mysqlClient->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_NUM);  
    return $categories;
}

/*
La fonction permet, à partir d'un résultat de requete SQL $content, par exemple un ensemble de couples id_marque et nom_marque, de récupérer les deux premiers éléments 
de chaque tableau pour créer des balises <options> à placer dans une balise <selector>, soit l'id et et le nom.

Return : une chaine de caractères contenant les balises options créées
*/
function selectorFromContent($content, $selected) {
    $html_selector="";
    foreach ($content as $value) {
        if ($value[1] == $selected) {
            $html_selector .= "<option value=\"$value[0]\" selected>$value[1]</option>";
        } else $html_selector .= "<option value=\"$value[0]\">$value[1]</option>";
    }
    return $html_selector;
}

/**
 * A partir d'un id role, issue de la connexion d'un employé, recupère les accès autorisés pour ne proposer sur l'interface 
 * uniquement les catégories auquel il peut accéder
 * 
 * Return : un bloc HTML affichant les catégories autorisées
 */
function getEmployeeCategoryAccess($mysqlClient, $role) {
    $sql = "SELECT l_c_a.page_php as page, l_c_a.nom_cat_admin as nom
            FROM roles as r INNER JOIN role_cat_admin as r_c_a ON r.id_role = r_c_a.id_role
            INNER JOIN liste_cat_admin as l_c_a ON l_c_a.id_cat_admin = r_c_a.id_cat_admin
            WHERE r.id_role = :id";
    $stmt = $mysqlClient->prepare($sql);
    $stmt->bindValue(":id", $role, PDO::PARAM_INT);
    $stmt->execute();
    $admin_access = $stmt->fetchAll(PDO::FETCH_NUM);

    $div_admin_access="";

    foreach ($admin_access as $nom_categorie) {
            $div_admin_access .= "<a href=\"./$nom_categorie[0]\" class=\"btn btn-info\">$nom_categorie[1]</a>";
    }
    return $div_admin_access;
}


/**
 * En fonction du rôle de l'employé renvoie ou non des boutons permettant la modification ou le suppression d'un élément listé sur la page
 */
function defineEmployeeActions($session, $produit){
    $modify_button = "<td>
    <form action=\"./modifier_produit.php\" method=\"get\">
        <input type=\"hidden\" name=\"id_produit\" value=\"".$produit["id"]."\">
        <button type=\"submit\" class=\"btn btn-sm btn-info\">Modifier</a>
    </form>
</td>";

    $delete_button = "<td>
    <form action=\"./supprimer_produit.php\" method=\"get\">
        <input type=\"hidden\" name=\"id_produit\" value=\"".$produit["id"]."\">
        <input type=\"hidden\" name=\"nom\" value=\"".$produit["nom"]."\">
        <button type=\"submit\" class=\"btn btn-sm btn-danger\">Supprimer</a>
    </form>
</td>";
    if ($session['role']==1) return $modify_button . $delete_button;
    else if ($session['role']==2) return $modify_button;
    else return "<td></td><td></td>";
}


/**
 * Vérifie d'après la variable globale $_SESSION, si l'employé est connecté à l'admin. Si ce n'est pas le cas, alors il est renvoyé vers la page de connexion
 */
function checkRoleAdmin($session){
    echo 'checkRoleAdmin' . PHP_EOL;
    if (!isset($session['role']) || (isset($session["role"]) && ($session["role"] == 0 ))) {
        echo 'Renvoie vers la page connexion_admin.php' . PHP_EOL ;
        header("Location: connexion_admin.php");
        exit;
    }
}

/**
 * Renvoie la page web sur laquelle se trouve l'utilisateur
 */
function getCurrentPage($server){
    $path = $server['REQUEST_URI'];
    $file = basename ($path);
    return $file; 
}

/**
 * Vérifie si la session en cours contient un panier. Dans le cas contraire, un panier vide est créé.
 */
function checkIfSessionHasPanier($session){
    if (!isset($session["panier"])) {
        $session["panier"] = new Panier();
    }
    return $session;
}

/**
 * Vérifie si produit ajouté au panier y est déjà. Le cas échéant, la quantité du produit est modifiée.
 */
function checkIfDuplicate($produit): bool {
    if (count($_SESSION['panier']->getContenuPanier()) == 0) {
        echo "FALSE";
        return false ; 
    }
    
    foreach($_SESSION['panier']->getContenuPanier() as $item) {
        if ($item->getId() == $produit->getId()) {
            $item->ajouterQuantite($produit->getQuantite());
            return true;
        }
    }
    return false;
}
