<?php
// Include the database connection
include "../connect.php";

// Start the session
session_start();

header('Content-Type: application/json'); // Set header for JSON response

// Fetch categories where Id_magasin is 5
$query = "SELECT Id_Cat, Nom_Cat FROM categories WHERE Id_magasin = :Id_magasin";
$stmt = $con->prepare($query);
$stmt->bindParam(':Id_magasin', $magasinId);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all categories as associative array

// Create an array to store the categories in a format suitable for JSON encoding
$categoryData = [];
foreach ($categories as $category) {
    $categoryData[] = [
        'Id_Cat' => $category['Id_Cat'],
        'Nom_Cat' => $category['Nom_Cat']
    ];
}

// Return categories as JSON
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    header('Content-Type: application/json');
    echo json_encode($categoryData);
}

// Insert product into the 'produits' table when data is sent via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the data from POST request (sent by your Android app)
    $productName = $_POST['Nom_Prod'];
    $productPrice = $_POST['Prix_Prod'];
    $productDesc = $_POST['Desc_Prod'];
    $categoryId = $_POST['Id_Cat']; // This is the category id from the spinner
    $Disp = "Yes"; // Example for fetching categories where Id_magasin is 5
    $userId = $_POST['userId']; // This is the category id from the spinner
    
    $_SESSION['userId'] = $userId;


    // Insert product using PDO
    $insertQuery = "INSERT INTO produits (Nom_Prod, Prix_Prod, Desc_Prod, Id_Cat, Disp, Id_magasin) 
                    VALUES (:Nom_Prod, :Prix_Prod, :Desc_Prod, :Id_Cat, :Disp, :Id_magasin)";
    $stmt = $con->prepare($insertQuery);

    $id_magasin = $_SESSION['userId'];

    $stmt->bindParam(':Nom_Prod', $productName);
    $stmt->bindParam(':Prix_Prod', $productPrice);
    $stmt->bindParam(':Desc_Prod', $productDesc);
    $stmt->bindParam(':Id_Cat', $categoryId);
    $stmt->bindParam(':Disp', $Disp);
    $stmt->bindParam(':Id_magasin', $id_magasin);


    if ($stmt->execute()) {
        // Return success message
        echo json_encode(["message" => "Product added successfully!"]);
    } else {
        // Return error message
        echo json_encode(["message" => "Error adding product."]);
    }
}