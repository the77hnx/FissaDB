<?php
// Include the database connection
include('../connect.php');

// Set the restaurant ID to 5
$restaurant_id = 5;

// Query to get all products for the restaurant with Id_magasin = :restaurant_id
$query = "SELECT * FROM produits WHERE Id_magasin = :restaurant_id";
$stmt = $con->prepare($query);
$stmt->bindParam(':restaurant_id', $restaurant_id);
$stmt->execute();

// Fetch all products for the restaurant
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle discount status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Set discount status based on checkbox: Yes if checked, No if not checked
    $new_discount_status = isset($_POST['discount_status']) ? 'Yes' : 'No';

    // Update query to change discount status
    $update_query = "UPDATE produits SET Red = :new_discount_status WHERE Id_prod = :product_id";
    $update_stmt = $con->prepare($update_query);
    $update_stmt->bindParam(':new_discount_status', $new_discount_status);
    $update_stmt->bindParam(':product_id', $product_id);
    $update_stmt->execute();

    // Reload the page to see updated values
    header('Location: discounts.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discounts Page</title>
    <style>
        .product-container {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .product-details {
            flex: 1;
        }
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 15px;
        }
        .product-switch {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <h1>Discounts for Restaurant ID 5</h1>

    <?php if(!empty($products)): ?>
        <?php foreach($products as $product): ?>
            <div class="product-container">
                <div class="product-details">
                    <img src="<?php echo $product['Photo_Prod']; ?>" alt="Product Image" class="product-image">
                    <h3><?php echo htmlspecialchars($product['Nom_Prod']); ?></h3>
                    <p>Description: <?php echo htmlspecialchars($product['Desc_prod']); ?></p>
                    <p>Price: <?php echo htmlspecialchars($product['Prix_prod']); ?> DA</p>
                    <p>Discounted Price: <?php echo htmlspecialchars($product['Prix_reduit']); ?> DA</p>
                </div>
                <div class="product-switch">
                    <form method="POST">
                        <label for="discount_status">Discount:</label>
                        <input type="hidden" name="product_id" value="<?php echo $product['Id_Prod']; ?>">
                        <!-- Checkbox to represent discount status -->
                        <input type="checkbox" name="discount_status" value="Yes" <?php echo ($product['Red'] === 'Yes') ? 'checked' : ''; ?> onchange="this.form.submit()">
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products found for this restaurant.</p>
    <?php endif; ?>
</body>
</html>
