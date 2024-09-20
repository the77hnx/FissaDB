<?php 
// Include the database connection file
include "../connect.php";

// Check if the required parameter is received
if (isset($_POST['restaurantId'])) {
    $restaurantId = $_POST['restaurantId'];

    // Query to get restaurant information
    $restaurantQuery = "
    SELECT * 
    FROM magasin 
    WHERE Id_magasin = :id
    ";

    $restaurantStmt = $con->prepare($restaurantQuery);
    $restaurantStmt->bindParam(':id', $restaurantId, PDO::PARAM_INT);
    $restaurantStmt->execute();
    $restaurant = $restaurantStmt->fetch(PDO::FETCH_ASSOC);

    if ($restaurant) {
        // Query to get products for the restaurant
        $productsQuery = "SELECT * FROM produits WHERE Id_magasin = :id AND Disp = 'Yes'";
        $productsStmt = $con->prepare($productsQuery);
        $productsStmt->bindParam(':id', $restaurant['Id_magasin'], PDO::PARAM_INT);
        $productsStmt->execute();
        $products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'restaurant' => $restaurant,
            'products' => array_map(function($product) {
                return [
                    'productName' => $product['Nom_Prod'],
                    'price' => $product['Prix_prod'],
                    'count' => 0 // Add default count value, since it's not in the table
                ];
            }, $products)
        ]);
    } else {
        echo json_encode(['error' => 'Restaurant not found']);
    }
} else {
    echo json_encode(['error' => 'Required parameter not provided']);
}
