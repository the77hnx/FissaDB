<?php
// Include the database connection file
include "../connect.php";

// Start the session to access the current user
session_start();
$current_user_id = $_SESSION['user_id'] ?? 17; // Use 17 for testing if not set

header('Content-Type: application/json'); // Set header for JSON response

try {
    $stmt = $con->prepare("
        SELECT 
            o.Id_Demandes AS orderId,
            s.Nom_magasin AS storeName,
            o.Prix_Livraison AS deliveryPrice,
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

    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($orders); // Return JSON encoded orders
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]); // Return error as JSON
}
?>
