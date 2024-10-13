<?php
// Include the database connection file
include("../connect.php");

try {
    // Get the categoryId from the request (if any)
    $categoryId = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

    // Query to get all open restaurants or filter by category
    if ($categoryId) {
        // Query to get open restaurants for the selected category
        $query_restaurants = "
            SELECT 
                m.Id_magasin AS restaurantId,
                m.Nom_magasin AS restaurantName, 
                m.Statut_magasin AS restaurantStatus, 
                m.Evaluation AS restaurantValue, 
                m.Address_magasin AS restaurantLocation 
            FROM magasin m
            JOIN restaurant_categories rc ON m.Id_magasin = rc.Id_magasin
            WHERE m.Statut_magasin = 'مفتوح' AND rc.Id_Cat = :categoryId";
        
        $stmt_restaurants = $con->prepare($query_restaurants);
        $stmt_restaurants->bindParam(':categoryId', $categoryId);
    } else {
        // Query to get all open restaurants if no category is selected
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
    }

    $stmt_restaurants->execute();
    $restaurants = $stmt_restaurants->fetchAll(PDO::FETCH_ASSOC);

    // Query to get categories/items
    $query_items = "
    SELECT Id_Cat AS categoryId, 
           Nom_Cat AS categoryName 
    FROM categories";
    
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
