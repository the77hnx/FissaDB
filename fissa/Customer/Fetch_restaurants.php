<?php
// Include the database connection file
include("../connect.php");

try {
    // Query to get open restaurants
    $query_restaurants = "
        SELECT 
            Id_magasin AS restaurantId,
            Nom_magasin AS restaurantName, 
            Statut_magasin AS restaurantStatus, 
            Evaluation AS restaurantValue, 
            Address_magasin AS restaurantLocation 
        FROM magasin 
        WHERE Statut_magasin = 'مفتوح'";
    
    $stmt_restaurants = $con->prepare($query_restaurants);
    $stmt_restaurants->execute();
    $restaurants = $stmt_restaurants->fetchAll(PDO::FETCH_ASSOC);

    // Query to get categories/items
    $query_items = "
        SELECT Nom_Cat AS categoryName FROM categories";
    
    $stmt_items = $con->prepare($query_items);
    $stmt_items->execute();
    $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

    // Check if data was fetched successfully
    if (!$restaurants || !$items) {
        throw new Exception("Error fetching data from the database");
    }

    // Prepare combined JSON response
    $response = [
        'restaurants' => $restaurants,
        'items' => $items,
    ];

    // Output the response in JSON format
    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    // Handle exceptions and output error message
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 
