<?php
include '../connect.php';

// Start the session to access the current user
session_start();

header('Content-Type: application/json'); // Set header for JSON response

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted status
    $userId = $_POST['user_id'];
    $statut_magasin = isset($_POST['statut_magasin']) ? $_POST['statut_magasin'] : 'مغلق';

    // Store the user ID in the session
    $_SESSION['userId'] = $userId;

    // Retrieve userId from the session
    $current_user_id = $_SESSION['userId'];

    // Fetch the current status from the database
    $stmt = $con->prepare("SELECT Statut_magasin FROM magasin WHERE Id_magasin = ?");
    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $currentStatus = $stmt->fetchColumn();

    // Check if the current status matches the posted value
    if ($currentStatus !== $statut_magasin) {
        // Update the status in the database
        $updateStmt = $con->prepare("UPDATE magasin SET Statut_magasin = ? WHERE Id_magasin = ?");
        $updateStmt->bindValue(1, $statut_magasin, PDO::PARAM_STR);
        $updateStmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    // Return success response
    echo json_encode(["success" => true]);
}