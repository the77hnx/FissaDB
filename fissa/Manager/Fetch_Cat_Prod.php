<?php
include "../connect.php";

// Set the fixed Id_magasin value
$id_magasin = 5;

// Fetch categories
$categories_query = "SELECT * FROM categories WHERE Id_magasin = :id_magasin";
$categories_stmt = $con->prepare($categories_query);
$categories_stmt->bindValue(':id_magasin', $id_magasin, PDO::PARAM_INT);
$categories_stmt->execute();
$categories_result = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$products_query = "SELECT * FROM produits WHERE Id_magasin = :id_magasin";
$products_stmt = $con->prepare($products_query);
$products_stmt->bindValue(':id_magasin', $id_magasin, PDO::PARAM_INT);
$products_stmt->execute();
$products_result = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories and Products</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Categories and Products</h1>

    <h2>Categories</h2>
    <form method="post" action="">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories_result as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['Id_Cat']); ?></td>
                        <td><?php echo htmlspecialchars($category['Nom_Cat']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <h2>Products</h2>
    <form method="post" action="">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products_result as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['Id_Prod']); ?></td>
                        <td><?php echo htmlspecialchars($product['Nom_Prod']); ?></td>
                        <td><?php echo htmlspecialchars($product['Prix_prod']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <?php
    // Close the database connection
    $con = null;
    ?>
</body>
</html>
