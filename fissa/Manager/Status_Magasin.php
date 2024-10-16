<?php
include '../connect.php';

$storeId = 5; // Hardcoded store ID for now

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted status
    $statut_magasin = isset($_POST['statut_magasin']) ? $_POST['statut_magasin'] : 'مغلق';

    // Fetch the current status from the database
    $stmt = $con->prepare("SELECT Statut_magasin FROM magasin WHERE Id_magasin = ?");
    $stmt->bindValue(1, $storeId, PDO::PARAM_INT);
    $stmt->execute();
    
    $currentStatus = $stmt->fetchColumn();

    // Check if the current status matches the posted value
    if ($currentStatus !== $statut_magasin) {
        // Update the status in the database
        $updateStmt = $con->prepare("UPDATE magasin SET Statut_magasin = ? WHERE Id_magasin = ?");
        $updateStmt->bindValue(1, $statut_magasin, PDO::PARAM_STR);
        $updateStmt->bindValue(2, $storeId, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    // Return success response
    echo json_encode(["success" => true]);
}