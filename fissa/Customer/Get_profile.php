<?php

include '../connect.php';

session_start(); // Start the session

header('Content-Type: application/json'); // Set the content type to JSON

try {
    // Check if user ID is set in session
    if (!isset($_SESSION['userId'])) {
        echo json_encode(["error" => "User ID not found in session. Please log in."]);
        exit();
    }

    $userId = $_SESSION['userId'];

    // Prepare and execute SQL query
    $stmt = $con->prepare("SELECT Nom_Client AS fullName, E_mail AS email, Tel_Client AS phone, Password AS password, Coordonnes AS address FROM client WHERE Id_Client = ?");
    $stmt->execute([$userId]);

    // Fetch the result as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user data is found
    if (empty($user)) {
        echo json_encode(["error" => "User not found"]);
    } else {
        // Return user data as JSON
        echo json_encode($user);
    }
} catch (PDOException $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode(["error" => "Database error"]);
}
