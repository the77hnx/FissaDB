<?php
// Include the database connection
include "../connect.php";

// Fetch the categories from the categories table using PDO
$query = "SELECT * FROM categories";
$stmt = $con->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all categories as associative array

// When the form is submitted, insert product data into the produits table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['Nom_Prod'];
    $productPrice = $_POST['Prix_Prod'];
    $productDesc = $_POST['Desc_Prod'];
    $categoryId = $_POST['Id_Cat']; // This is the category id from the spinner

    // Insert product using PDO
    $insertQuery = "INSERT INTO produits (Nom_Prod, Prix_Prod, Desc_Prod, Id_Cat) 
                    VALUES (:Nom_Prod, :Prix_Prod, :Desc_Prod, :Id_Cat)";
    $stmt = $con->prepare($insertQuery);
    $stmt->bindParam(':Nom_Prod', $productName);
    $stmt->bindParam(':Prix_Prod', $productPrice);
    $stmt->bindParam(':Desc_Prod', $productDesc);
    $stmt->bindParam(':Id_Cat', $categoryId);

    if ($stmt->execute()) {
        echo "Product added successfully!";
    } else {
        echo "Error adding product.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
</head>
<body>
    <h2>Add New Product</h2>
    
    <form method="POST" action="add_product.php">
        <label for="Nom_Prod">Product Name:</label>
        <input type="text" id="Nom_Prod" name="Nom_Prod" required><br><br>

        <label for="Prix_Prod">Product Price:</label>
        <input type="number" id="Prix_Prod" name="Prix_Prod" required><br><br>

        <label for="Desc_Prod">Product Description:</label>
        <textarea id="Desc_Prod" name="Desc_Prod" required></textarea><br><br>

        <label for="Id_Cat">Category:</label>
        <select id="Id_Cat" name="Id_Cat" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['Id_Cat']; ?>">
                    <?php echo $category['Nom_Cat']; ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Add Product</button>
    </form>
</body>
</html>
