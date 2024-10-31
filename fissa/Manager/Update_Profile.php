<?php
include "../connect.php"; // Including the database connection file

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

// If form is submitted, update the information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting the data from the form
    $restaurantName = $_POST['Nom_magasin'];
    $description = $_POST['Descriptif_magasin'];
    $phoneNumber = $_POST['Tel_magasin'];
    $password = $_POST['Password'];
    $address = $_POST['Address_magasin'];
    $coordinates = $_POST['Coordonnes'];
    $userId = $_POST['user_id'];

    // تخزين ID المستخدم في الجلسة
    $_SESSION['userId'] = $userId;

    // استرجاع userId من الجلسة
    $userId = $_SESSION['userId'];

    // Update query
    $sql = "UPDATE magasin SET Nom_magasin = ?, Descriptif_magasin = ?, Tel_magasin = ?, Password = ?, Address_magasin = ?, Coordonnes = ? WHERE Id_magasin = ?";
    $params = [$restaurantName, $description, $phoneNumber, $password, $address, $coordinates, $userId];

    $stmt = $con->prepare($sql); // Prepare the statement
    if ($stmt->execute($params)) {
        if ($stmt->rowCount() > 0) {
            echo "User information updated successfully!";
        } else {
            echo "No changes were made."; // No rows affected
        }
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Failed to update user information. Error: " . $errorInfo[2];
    }
} else {
    echo "PHP script started.";
}

// Fetch the user information for display in the form
$stmt = $con->prepare("SELECT * FROM magasin WHERE Id_magasin = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);