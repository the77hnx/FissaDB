<?php
// Include the database connection
include "../connect.php";

// Start the session
session_start();

// Hardcode client ID for testing
$_SESSION['Id_Client'] = 17;

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $restaurantName = htmlspecialchars($_POST['restaurantName']);
    $orderCount = htmlspecialchars($_POST['orderCount']);
    $totalPrice = htmlspecialchars($_POST['totalPrice']);
    $deliveryPrice = htmlspecialchars($_POST['deliveryPrice']);
    $totalWithDelivery = htmlspecialchars($_POST['totalWithDelivery']);
    $orderStatus = htmlspecialchars($_POST['orderStatus']);
    $orderNumber = htmlspecialchars($_POST['orderNumber']);
    
    $productName = htmlspecialchars($_POST['product_name']);
    $productPrice = htmlspecialchars($_POST['product_price']);
    $productQuantity = htmlspecialchars($_POST['product_quantity']);

    // Start a transaction
    $con->beginTransaction();

    try {
        // Prepare and execute insertion into the demandes table
        $stmt = $con->prepare("INSERT INTO demandes (Id_magasin, Id_Client, Id_Livreur, ID_Statut_Commande, Prix_Demande, Prix_Livraison, Date_commande, Heure_commande) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");

        // Check if prepare was successful
        if ($stmt === false) {
            throw new Exception('Prepare failed: ' . htmlspecialchars($con->errorInfo()[2]));
        }

        // Fetch Id_magasin, Id_Client, Id_Livreur
        $id_magasin = getMagasinIdByName($restaurantName); // Fetch Id_magasin
        $id_client = $_SESSION['Id_Client']; // Use session Id_Client
        $id_livreur = getLivreurId(); // Fetch Id_Livreur
        $id_statut_commande = 1; // Default status
        $prix_demande = (float) $totalPrice;
        $prix_livraison = (float) $deliveryPrice;

        // Bind parameters
        $stmt->bindParam(1, $id_magasin, PDO::PARAM_INT);
        $stmt->bindParam(2, $id_client, PDO::PARAM_INT);
        $stmt->bindParam(3, $id_livreur, PDO::PARAM_INT);
        $stmt->bindParam(4, $id_statut_commande, PDO::PARAM_INT);
        $stmt->bindParam(5, $prix_demande, PDO::PARAM_STR);
        $stmt->bindParam(6, $prix_livraison, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Get the newly inserted order ID
        $orderId = $con->lastInsertId();

        // Prepare and execute insertion into the articles table
        $sql = "INSERT INTO articles (Nom_Article, Prix, Quantite, Id_Demandes) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(1, $productName, PDO::PARAM_STR);
        $stmt->bindParam(2, $productPrice, PDO::PARAM_STR);
        $stmt->bindParam(3, $productQuantity, PDO::PARAM_INT);
        $stmt->bindParam(4, $orderId, PDO::PARAM_INT);

        $stmt->execute();

        // Commit the transaction
        $con->commit();

        echo "Order and product information added successfully.";

    } catch (Exception $e) {
        // Roll back the transaction if something goes wrong
        $con->rollBack();
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
}

// Implement these functions as per your database design
function getMagasinIdByName($name) {
    global $con;
    $stmt = $con->prepare("SELECT Id_magasin FROM magasin WHERE Nom_magasin = ?");
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        throw new Exception('Magasin not found');
    }
    return $row['Id_magasin'];
}

function getClientIdByName($name) {
    global $con;
    $stmt = $con->prepare("SELECT Id_Client FROM client WHERE Nom_Client = ?");
    $stmt->bindParam(1, $name, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        throw new Exception('Client not found');
    }
    return $row['Id_Client'];
}

function getLivreurId() {
    global $con;
    $stmt = $con->prepare("SELECT Id_Livreur FROM livreur LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
        throw new Exception('Livreur not found');
    }
    return $row['Id_Livreur'];
}
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Page</title>
</head>
<body>
    <h1>Cart Page</h1>
    <form method="post" action="">
        <label for="restaurantName">Restaurant Name:</label>
        <input type="text" id="restaurantName" name="restaurantName" required><br><br>
        <label for="orderCount">Order Count:</label>
        <input type="number" id="orderCount" name="orderCount" required><br><br>
        <label for="totalPrice">Total Price:</label>
        <input type="number" step="0.01" id="totalPrice" name="totalPrice" required><br><br>
        <label for="deliveryPrice">Delivery Price:</label>
        <input type="number" step="0.01" id="deliveryPrice" name="deliveryPrice" required><br><br>
        <label for="totalWithDelivery">Total with Delivery:</label>
        <input type="number" step="0.01" id="totalWithDelivery" name="totalWithDelivery" required><br><br>
        <label for="orderStatus">Order Status:</label>
        <input type="number" id="orderStatus" name="orderStatus" required><br><br>
        <label for="orderNumber">Order Number:</label>
        <input type="text" id="orderNumber" name="orderNumber" required><br><br>
        
        <label for="product_name">Product Name:</label>
        <input type="text" id="product_name" name="product_name" required><br><br>
        <label for="product_price">Product Price:</label>
        <input type="number" step="0.01" id="product_price" name="product_price" required><br><br>
        <label for="product_quantity">Product Quantity:</label>
        <input type="number" id="product_quantity" name="product_quantity" required><br><br>
        <input type="submit" value="Buy">
    </form>
</body>
</html>