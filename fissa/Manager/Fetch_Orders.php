<?php
include '../connect.php'; // Include database connection

// Start the session to access the current user
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id']) && isset($_POST['case_number'])) {
        $userId = $_POST['user_id'];
        $caseNumber = $_POST['case_number'];

        // Store the user ID in the session
        $_SESSION['userId'] = $userId;
        $magasinId = $_SESSION['userId'];

        // Determine status ID based on the case number
        $statusId = null;
        switch ($caseNumber) {
            case 0:
                $statusId = 1;
                break;
            case 1:
                $statusId = 2;
                break;
            case 2:
                $statusId = 3;
                break;
            case 3:
                $statusId = 4;
                break;
            case 4:
                $statusId = 6;
                break;
            case 5:
                $statusId = 5;
                break;
            default:
                // Handle unknown case
                echo json_encode(['error' => 'Invalid case number']);
                exit;
        }

        // Fetch orders for the corresponding status ID
        $orders = fetchOrdersByMagasinAndStatus($magasinId, $statusId);
        header('Content-Type: application/json');
        echo json_encode($orders);
    } else {
        // Handle missing parameters
        echo json_encode(['error' => 'Missing user_id or case_number']);
    }
}
