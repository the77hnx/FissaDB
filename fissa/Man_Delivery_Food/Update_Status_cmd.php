<?php
// Include the database connection file
include "../connect.php"; // This should define $con

// Set the response header to JSON
header('Content-Type: application/json');

// Retrieve the raw POST data
$jsonData = file_get_contents("php://input");

// Decode the JSON data
$data = json_decode($jsonData, true);

// Initialize an empty response array
$response = array();

// Check if the required parameters are present
if (isset($data['Id_Demandes']) && isset($data['Id_Statut_Commande'])) {
    $orderId = $data['Id_Demandes'];
    $statusId = $data['Id_Statut_Commande'];

    // Prepare the SQL update statement
    $sql = "UPDATE demandes SET Id_Statut_Commande = :Id_Statut_Commande WHERE Id_Demandes = :Id_Demandes";

    // Prepare the PDO statement
    $stmt = $con->prepare($sql); // Use $con instead of $pdo

    // Bind parameters
    $stmt->bindParam(':Id_Statut_Commande', $statusId, PDO::PARAM_INT);
    $stmt->bindParam(':Id_Demandes', $orderId, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Update was successful
        $response['success'] = true;
        $response['message'] = "Order status updated successfully.";
    } else {
        // Update failed
        $response['success'] = false;
        $response['error'] = "Failed to update order status.";
    }
} else {
    // Missing parameters
    $response['success'] = false;
    $response['error'] = "Missing required parameters.";
}

// Return the response in JSON format
echo json_encode($response);