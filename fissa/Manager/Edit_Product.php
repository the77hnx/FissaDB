<?php
// Include the database connection file
include '../connect.php';

// Fetch products to populate the product dropdown
$productsQuery = "SELECT Nom_Prod FROM produits";
$productsResult = $con->query($productsQuery);
$products = $productsResult->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories to populate the category spinner
$categoriesQuery = "SELECT Id_Cat, Nom_Cat FROM categories";
$categoriesResult = $con->query($categoriesQuery);
$categories = $categoriesResult->fetchAll(PDO::FETCH_ASSOC);

// Handle fetching product details via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['product'])) {
    $productName = $_GET['product'];

    // Prepare a query to fetch the product details
    $query = "SELECT Nom_Prod, Prix_Prod, Desc_prod, Id_Cat FROM produits WHERE Nom_Prod = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$productName]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("No product found with the given name.");
    }

    // Return product data and categories for the form
    echo json_encode(['product' => $product, 'categories' => $categories]);
    exit;
}

// Handle updating product details via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['Nom_Prod'];
    $productPrice = $_POST['Prix_Prod'];
    $productDescription = $_POST['Desc_prod'];
    $categoryId = $_POST['Id_Cat'];

    // Prepare query to update the product in the database
    $updateQuery = "UPDATE produits SET Prix_Prod = ?, Desc_prod = ?, Id_Cat = ? WHERE Nom_Prod = ?";
    $stmt = $con->prepare($updateQuery);
    if ($stmt->execute([$productPrice, $productDescription, $categoryId, $productName])) {
        echo "Product updated successfully!";
    } else {
        echo "Error updating product.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <script>
        function fetchProductData(productName) {
            // Fetch product data using AJAX
            fetch('?product=' + productName)
            .then(response => response.json())
            .then(data => {
                // Populate the form fields with the product data
                document.getElementById('Nom_Prod').value = data.product.Nom_Prod;
                document.getElementById('Prix_Prod').value = data.product.Prix_Prod;
                document.getElementById('Desc_prod').value = data.product.Desc_prod;
                
                const categorySpinner = document.getElementById('itemCategorySpinner');
                categorySpinner.innerHTML = '';
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.Id_Cat;
                    option.text = category.Nom_Cat;
                    if (category.Id_Cat === data.product.Id_Cat) {
                        option.selected = true;
                    }
                    categorySpinner.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching product data:', error));
        }

        function saveProductData(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('editProductForm'));

            // Send the form data using AJAX
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                alert(result);
            })
            .catch(error => console.error('Error updating product:', error));
        }
    </script>
</head>
<body>

<!-- Form to edit the product -->
<form id="editProductForm" onsubmit="saveProductData(event)">
    <label for="Nom_Prod">Product Name:</label>
    <input type="text" id="Nom_Prod" name="Nom_Prod" value="" required readonly><br>

    <label for="Prix_Prod">Product Price:</label>
    <input type="text" id="Prix_Prod" name="Prix_Prod" value="" required><br>

    <label for="Desc_prod">Product Description:</label>
    <textarea id="Desc_prod" name="Desc_prod" required></textarea><br>

    <label for="itemCategorySpinner">Category:</label>
    <select id="itemCategorySpinner" name="Id_Cat" required>
        <!-- Categories will be populated from the database -->
    </select><br>

    <button type="submit">Save</button>
</form>

<!-- Dropdown to choose a product for editing -->
<select id="productDropdown" onchange="fetchProductData(this.value)">
    <option value="">Select Product</option>
    <?php foreach ($products as $product): ?>
        <option value="<?= $product['Nom_Prod'] ?>"><?= $product['Nom_Prod'] ?></option>
    <?php endforeach; ?>
</select>

</body>
</html>