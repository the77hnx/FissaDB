<?php
ini_set('display_errors', 0); // Hide errors
ini_set('log_errors', 1);     // Log errors
error_reporting(E_ALL);       // Report all errors

include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // Store the user ID in the session
        $_SESSION['userId'] = $userId;

        try {
            $current_user_id = $_SESSION['userId'];

            // Fetch the necessary information
            $query = "SELECT 
                l.Statut_Livreur, 
                l.Nom_Livreur AS Livreur_name,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande IN (1, 2, 3, 4, 6)) AS accepted_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 5) AS cancelled_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND DATE(Date_commande) = CURDATE()) AS todays_orders,
                (SELECT COUNT(*) FROM demandes WHERE Id_Livreur = :mandeliveryId AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) AS monthly_orders,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND DATE(Date_commande) = CURDATE()) AS wallet_daily_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND YEARWEEK(Date_commande, 1) = YEARWEEK(CURDATE(), 1)) AS wallet_weekly_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6 AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) AS wallet_monthly_value,
                (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_Livreur = :mandeliveryId AND Id_Statut_Commande = 6) AS wallet_value,
                l.Evaluation,
                d.Id_Demandes AS Order_ID,
                d.Prix_Livraison AS Delivery_Price,
                d.Prix_Demande AS Order_Price,
                c.Nom_Client AS Customer_Name,
                c.Coordonnes AS Customer_Location,
                r.Coordonnes AS Restaurant_Location
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

            $stmt = $con->prepare($query);
            $stmt->bindValue(':mandeliveryId', $ManDeliveryId, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the data as JSON
            header('Content-Type: application/json');
            echo json_encode($data);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }

    } else {
        echo json_encode(['error' => 'Missing user_id']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}