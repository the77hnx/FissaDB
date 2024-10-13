<?php 
// Include database connection
include '../connect.php';

$id_magasin = 5;  // Fixed value for Id_magasin

// Check if form data is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get POST data and sanitize it
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';

    if (empty($name)) {
        echo "Category name is required.";
        exit;
    }

    try {
        // Prepare and execute SQL query with Id_magasin
        $sql = $con->prepare("INSERT INTO categories (Nom_Cat, Id_magasin) VALUES (:name, :id_magasin)");
        $sql->bindParam(':name', $name, PDO::PARAM_STR);
        $sql->bindParam(':id_magasin', $id_magasin, PDO::PARAM_INT);

        if ($sql->execute()) {
            echo "New category created successfully";
        } else {
            echo "Error: " . $sql->errorInfo()[2];
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Close the connection
    $con = null;
} else {
    echo "Invalid request method.";
}