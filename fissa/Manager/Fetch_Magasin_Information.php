<?php
ini_set('display_errors', 0); // Hide errors
ini_set('log_errors', 1);     // Log errors
error_reporting(E_ALL);       // Report all errors

include '../connect.php';

$storeId = 5; // Assuming the store ID is hardcoded or dynamically set

try {
    // Fetch the necessary information
    $query = "SELECT Statut_magasin, Nom_magasin as shop_name,
              (SELECT COUNT(*) FROM produits WHERE Id_magasin = :storeId) as num_products,
              (SELECT COUNT(*) FROM categories WHERE Id_magasin = :storeId) as num_cat,
              (SELECT COUNT(*) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande IN (1, 2, 3, 4, 6)) as accepted_orders,
              (SELECT COUNT(*) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 5) as cancelled_orders,
              (SELECT COUNT(*) FROM demandes WHERE Id_magasin = :storeId AND DATE(Date_commande) = CURDATE()) as todays_orders,
              (SELECT COUNT(*) FROM demandes WHERE Id_magasin = :storeId AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) as monthly_orders,

              (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 6 AND DATE(Date_commande) = CURDATE()) AS wallet_daily_value,
              (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 6 AND YEARWEEK(Date_commande, 1) = YEARWEEK(CURDATE(), 1)) AS wallet_weekly_value,
              (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 6 AND DATE_FORMAT(Date_commande, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')) AS wallet_monthly_value,
              (SELECT IFNULL(SUM(Prix_Demande), 0) FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 6 ) AS wallet_value,

              Evaluation FROM magasin WHERE Id_magasin = :storeId";

    $stmt = $con->prepare($query);
    $stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}