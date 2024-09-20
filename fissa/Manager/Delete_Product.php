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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            display: inline;
        }
    </style>
</head>
<body>
    <h1>Manage Products</h1>
    
    <?php if ($result->rowCount() > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Id_Prod']); ?></td>
                        <td><?php echo htmlspecialchars($row['Nom_Prod']); ?></td>
                        <td>
                            <form action="Delete_product.php" method="POST">
                                <input type="hidden" name="id_prod" value="<?php echo htmlspecialchars($row['Id_Prod']); ?>">
                                <input type="submit" name="delete" value="Delete">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>

</body>
</html>
