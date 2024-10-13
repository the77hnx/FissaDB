<?php
include "../connect.php"; // Including the database connection file

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the user ID
$userId = 0; // Example user ID, replace with dynamic ID

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit;
}

// If form is submitted, update the information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting the data from the form
    $livreurName = $_POST['Nom_Livreur'];
    $vehiculeName = $_POST['Nom_Vehicule'];
    $phoneNumber = $_POST['Tel_Livreur'];
    $password = $_POST['Password'];
    $numberVehicule = $_POST['N_Vehicule'];
    $coordinates = $_POST['Coordonnes'];

    // Update query
    $sql = "UPDATE livreur SET Nom_Livreur = ?, Nom_Vehicule = ?, Tel_Livreur = ?, Password = ?, N_Vehicule = ?, Coordonnes = ? WHERE Id_Livreur = ?";
    $params = [$livreurName, $vehiculeName, $phoneNumber, $password, $numberVehicule, $coordinates, $userId];

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
$stmt = $con->prepare("SELECT * FROM livreur WHERE Id_Livreur = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);