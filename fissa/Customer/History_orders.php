<?php
// Include the database connection file
include "../connect.php";

// Start the session to access the current user
session_start();

header('Content-Type: application/json'); // Set header for JSON response

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Store the user ID in the session
        $_SESSION['userId'] = $userId;

        try {
            // Check if the session is valid    
            if (!isset($_SESSION['userId'])) {
                throw new Exception('Session expired. Please log in again.');
            }
            // Retrieve userId from the session
            $current_user_id = $_SESSION['userId'];

            // Prepare the SQL statement
            $stmt = $con->prepare("
                SELECT 
                    o.Id_Demandes AS orderId,
                    s.Nom_magasin AS storeName,
                    IFNULL(o.Prix_Livraison, 0) AS deliveryPrice,
                    o.Prix_Demande AS orderPrice,
                    o.Date_commande AS orderDate,
                    o.Heure_commande AS orderTime,
                    o.info_mag AS additionalInfomag,
                    o.info_liv AS additionalInfoliv,
                    st.Nom_Statut AS statusName
                FROM demandes o
                JOIN magasin s ON o.Id_Magasin = s.Id_Magasin
                JOIN stat_cmd st ON o.Id_Statut_Commande = st.Id_Statut_Commande
                WHERE o.Id_Client = :user_id
                ORDER BY o.Date_commande DESC
            ");

            // Bind the user ID to the statement
            $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Fetch all orders
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return JSON encoded orders wrapped in an 'orders' key
            echo json_encode(['orders' => $orders]);
        } catch (PDOException $e) {
            // Log the error message
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['error' => 'Database error occurred.']);
        }
    } else {
        echo json_encode(['error' => 'User ID not provided.']);
    }
} else {
    echo json_encode(['error' => 'User ID not provided.']);
}