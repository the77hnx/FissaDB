<?php
include "../connect.php"; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = $_POST['orderId'];
    $newStatus = $_POST['status'];

    if (isset($orderId) && isset($newStatus)) {
        // Prepare the SQL statement
        $sql = "UPDATE demandes SET Id_Statut_Commande = :status WHERE Id_Demandes = :orderId";
        $stmt = $con->prepare($sql);
        
        // Bind parameters
        $stmt->bindParam(':status', $newStatus, PDO::PARAM_INT);
        $stmt->bindParam(':orderId', $orderId, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Return a success message
            echo json_encode(["message" => "Order status updated successfully"]);
        } else {
            // Return an error message
            echo json_encode(["error" => "Failed to update order status"]);
        }
    } else {
        // Missing parameters
        echo json_encode(["error" => "Invalid input parameters"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}