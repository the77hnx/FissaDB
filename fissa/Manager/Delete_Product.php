<?php
// Include the database connection file
include "../connect.php";

// Handle deletion if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $id_prod = $_POST['id_prod'];
    
    if (!empty($id_prod)) {
        // Prepare the SQL query to delete the product
        $query = "DELETE FROM produits WHERE Id_Prod = :id_prod";
        
        if ($stmt = $con->prepare($query)) {
            // Bind the parameter
            $stmt->bindParam(':id_prod', $id_prod, PDO::PARAM_INT);
            
            // Execute the query
            if ($stmt->execute()) {
                echo "<p>Product deleted successfully.</p>";
            } else {
                echo "<p>Error deleting product: " . $stmt->errorInfo()[2] . "</p>";
            }
        } else {
            echo "<p>Error preparing statement: " . $con->errorInfo()[2] . "</p>";
        }
    } else {
        echo "<p>Product ID is required.</p>";
    }
}

// Fetch all products
$query = "SELECT * FROM produits";
$result = $con->query($query);
?>