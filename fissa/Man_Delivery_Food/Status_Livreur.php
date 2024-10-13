<?php
include '../connect.php';

$storeId = 0; // Hardcoded store ID for now

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted status
    $statut_magasin = isset($_POST['statut_livreur']) ? $_POST['statut_livreur'] : 'غير متاح';

    // Fetch the current status from the database
    $stmt = $con->prepare("SELECT Statut_Livreur FROM livreur WHERE Id_Livreur = ?");
    $stmt->bindValue(1, $storeId, PDO::PARAM_INT);
    $stmt->execute();
    
    $currentStatus = $stmt->fetchColumn();

    // Check if the current status matches the posted value
    if ($currentStatus !== $statut_magasin) {
        // Update the status in the database
        $updateStmt = $con->prepare("UPDATE livreur SET Statut_Livreur = ? WHERE Id_Livreur = ?");
        $updateStmt->bindValue(1, $statut_magasin, PDO::PARAM_STR);
        $updateStmt->bindValue(2, $storeId, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    // Return success response
    echo json_encode(["success" => true]);
}