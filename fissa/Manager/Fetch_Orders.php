<?php
include '../connect.php'; // Include database connection

// Start the session to access the current user
session_start();

// Function to fetch orders by status and Id_Magasin
function fetchOrdersByMagasinAndStatus($magasinId, $statusId) {
    global $con;
    
    // Query to fetch orders with required details from multiple tables
    $stmt = $con->prepare("
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
    
    if ($stmt->execute()) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results
        return json_encode($results); // Convert results to JSON
    } else {
        error_log("Query execution failed: " . implode(", ", $stmt->errorInfo()));
        return json_encode([]); // Return empty JSON array if the query fails
    }
}

// Handle the POST request to fetch orders
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['case_number'])) {
        $userId = $_POST['user_id'];
        $caseNumber = $_POST['case_number'];

        // Store the user ID in the session and assign it as magasinId
        $_SESSION['userId'] = $userId;
        $magasinId = $_SESSION['userId'];

        // Debug: Log the retrieved values
        error_log("User ID: $userId, Case Number: $caseNumber, Magasin ID: $magasinId");

        // Map case number to status ID
        $statusIdMap = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            6 => 6,
            5 => 5
        ];

        if (array_key_exists($caseNumber, $statusIdMap)) {
            $statusId = $statusIdMap[$caseNumber];

            // Fetch orders for the corresponding status ID
            $orders = fetchOrdersByMagasinAndStatus($magasinId, $statusId);
            
            // Set header for JSON response
            header('Content-Type: application/json');
            
            // Output orders or message if no orders found
            $ordersArray = json_decode($orders, true); // Decode to check if empty
            if (empty($ordersArray)) {
                echo json_encode(['message' => "No orders found for magasin ID $magasinId and status ID $statusId"]);
            } else {
                echo $orders;
            }
        } else {
            // Invalid case number
            echo json_encode(['error' => 'Invalid case number']);
        }
    } else {
        // Handle missing parameters
        echo json_encode(['error' => 'Missing user_id or case_number']);
    }
} else {
    // Handle incorrect request method
    echo json_encode(['error' => 'Invalid request method']);
}
?>
