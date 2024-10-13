<?php
include '../connect.php'; // Include database connection

// Function to fetch orders by status and Id_Magasin
function fetchOrdersByMagasinAndStatus($magasinId, $statusId) {
    global $con;
    
    // Query to fetch orders with required details from multiple tables
    $stmt = $con->prepare(query: "
        SELECT d.Id_Demandes, d.Date_commande, d.Heure_commande, d.info_mag, d.Prix_Demande, 
               c.Nom_Client, s.Nom_Statut, a.Nom_Article, a.Quantite, a.Prix, d.Id_Statut_Commande 
        FROM demandes d
        INNER JOIN client c ON d.Id_Client = c.Id_Client
        INNER JOIN stat_cmd s ON d.Id_Statut_Commande = s.Id_Statut_Commande
        INNER JOIN articles a ON d.Id_Demandes = a.Id_Demandes
        WHERE d.Id_Magasin = :magasinId AND d.Id_Statut_Commande = :statusId
    ");
    
    $stmt->bindValue(':magasinId', $magasinId, PDO::PARAM_INT);
    $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all results directly
}

// Example usage
$magasinId = 5;  // The Id_Magasin you are filtering by
$statusOrders = [
    'case0' => fetchOrdersByMagasinAndStatus($magasinId, 1),
    'case1' => fetchOrdersByMagasinAndStatus($magasinId, 2),
    'case2' => fetchOrdersByMagasinAndStatus($magasinId, 3),
    'case3' => fetchOrdersByMagasinAndStatus($magasinId,4),
    'case4' => fetchOrdersByMagasinAndStatus($magasinId,6),
    'case5' => fetchOrdersByMagasinAndStatus($magasinId, 5)
];

// Set header for JSON response
header('Content-Type: application/json');
echo json_encode($statusOrders); // Send all data in JSON format for the Java code to handle