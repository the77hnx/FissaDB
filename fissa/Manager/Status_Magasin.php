<?php
include '../connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session to access the current user
session_start();

header('Content-Type: application/json'); // Set header for JSON response

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted status
    $userId = $_POST['user_id'];
    $statut_magasin = isset($_POST['statut_magasin']) ? $_POST['statut_magasin'] : '';


    error_log("POST Data: user_id = $userId, statut_magasin = $statut_magasin");

    // Check if user_id and statut_magasin are not empty
    if (!empty($userId) && !empty($statut_magasin)) {
        // Prepare an SQL query to update the store status in the database
        $query = "UPDATE magasin SET Statut_magasin = ? WHERE Id_magasin = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ss', $statut_magasin, $userId);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Store status updated successfully.']);
        } else {
            error_log("SQL Error: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update store status: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}