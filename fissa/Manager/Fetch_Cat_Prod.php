<?php
include "../connect.php";

// Start the session to access the current user
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Store the user ID in the session
        $_SESSION['userId'] = $userId;


        $current_user_id = $_SESSION['userId'];
        // Fetch categories
        $query_categories = "
        SELECT Id_Cat, Nom_Cat 
        FROM categories 
        WHERE Id_magasin = :id_magasin
";

        $stmt_cat = $con->prepare($query_categories);
        $stmt_cat->bindValue(':id_magasin', $current_user_id, PDO::PARAM_INT);
        $stmt_cat->execute();
        $categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

        // Fetch products
        $query_products = "
    SELECT 
        Id_Prod, 
        Nom_Prod, 
        Prix_prod, 
        Desc_prod, 
        Id_Cat 
    FROM 
        produits 
    WHERE 
        Id_magasin = :id_magasin
";

        $stmt_prod = $con->prepare($query_products);
        $stmt_prod->bindValue(':id_magasin', $current_user_id, PDO::PARAM_INT);
        $stmt_prod->execute();
        $products = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

        // Prepare the response as an associative array
        $response = [
            'categories' => $categories,
            'products' => $products
        ];

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'User ID not provided.']);
    }
} else {
    echo json_encode(['error' => 'User ID not provided.']);
}