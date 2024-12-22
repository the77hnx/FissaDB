<?php

// Configure error reporting
ini_set('display_errors', 0); // Hide errors from being displayed
ini_set('log_errors', 1); // Log errors to the server's log
error_log("Script started."); // Log the start of the script

// Include the database connection
include '../connect.php';
session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user_id is provided in the POST request
    if (isset($_POST['user_id'])) {
        
        // Log the received user ID
        $userId = $_POST['user_id'];
        error_log("User ID received: " . $userId);

        // Store the user ID in the session for later use
        $_SESSION['userId'] = $userId;

        try {
            // Retrieve the current user ID from the session
            $current_user_id = $_SESSION['userId'];

            // Prepare the SQL query
            $query = "SELECT 
                l.Statut_Livreur, 
                l.Nom_Livreur as Livreur_name,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande IN (1, 2, 3, 4, 6)) as accepted_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 5) as cancelled_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND DATE(Date_commande) = CURDATE()) as todays_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) as monthly_orders,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND DATE(Date_commande) = CURDATE()) as wallet_daily_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND YEARWEEK(Date_commande, 1) = YEARWEEK(CURDATE(), 1)) as wallet_weekly_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) as wallet_monthly_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6) as wallet_value,
                l.Evaluation,
                d.Id_Demandes as Order_ID,
                d.Prix_Livraison as Delivery_Price,
                d.Prix_Demande as Order_Price,
                c.Nom_Client as Customer_Name,
                c.Image_path as Customer_Image,
                c.Coordonnes as Customer_Location,
                r.Coordonnes as Restaurant_Location
            FROM 
                livreur l
            LEFT JOIN 
                demandes d ON l.Id_Livreur = d.Id_Livreur
            LEFT JOIN 
                client c ON d.Id_Client = c.Id_Client
            LEFT JOIN 
                magasin r ON d.Id_magasin = r.Id_magasin
            WHERE
                l.Id_Livreur = :mandeliveryId
                AND d.Id_Statut_Commande IN (3, 4)";

            // Prepare the statement
            $stmt = $con->prepare($query);
            $stmt->bindValue(':mandeliveryId', $current_user_id, PDO::PARAM_INT);
            
            // Execute the statement
            if ($stmt->execute()) {
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Check if data is found
                if ($data) {
                    // Return the data as JSON
                    header('Content-Type: application/json');
                    echo json_encode($data);
                } else {
                    // Log no data found
                    error_log("No data found for user ID: " . $current_user_id);
                    echo json_encode(['error' => 'No data found']);
                }
            } else {
                // Log SQL error
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . implode(", ", $errorInfo));
                echo json_encode(['error' => 'SQL query execution failed']);
            }
        } catch (Exception $e) {
            // Log exception message
            error_log("Exception caught: " . $e->getMessage());
            echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    } else {
        // Log missing user ID error
        error_log("Missing user_id in POST data.");
        echo json_encode(['error' => 'Missing user_id']);
    }
} else {
    // Log invalid request method error
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['error' => 'Invalid request method']);
}
?>