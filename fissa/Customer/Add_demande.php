<?php
// Include the database connection
include "../connect.php";

// Start the session
session_start();

header('Content-Type: application/json'); // Set header for JSON response

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $restaurantName = htmlspecialchars($_POST['restaurantName']);
    $orderCount = htmlspecialchars($_POST['orderCount']);
    $totalPrice = htmlspecialchars($_POST['totalPrice']);
    $deliveryPrice = htmlspecialchars($_POST['deliveryPrice']);
    $orderStatus = htmlspecialchars($_POST['orderStatus']);
    $customerId = htmlspecialchars($_POST['customerId']); // Changed variable name for clarity

    // Additional information
    $additionalInfomag = htmlspecialchars($_POST['additionalInfomag']);
    $additionalInfoliv = htmlspecialchars($_POST['additionalInfoliv']);


    $_SESSION['userId'] = $customerId;

    // Prepare to handle multiple products
    $productNames = [];
    $productPrices = [];
    $productQuantities = [];

    for ($i = 0; isset($_POST['product_name_' . $i]); $i++) {
        $productNames[] = htmlspecialchars($_POST['product_name_' . $i]);
        $productPrices[] = (float)$_POST['product_price_' . $i];
        $productQuantities[] = (int)$_POST['product_quantity_' . $i];
    }

    // Start a transaction
    $con->beginTransaction();

    try {
        // Prepare and execute insertion into the demandes table
        $stmt = $con->prepare("INSERT INTO demandes (Id_magasin, Id_Client, Id_Livreur, ID_Statut_Commande, Prix_Demande, Prix_Livraison, Date_commande, Heure_commande, info_mag, info_liv) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)");

        // Fetch Id_magasin, Id_Client, Id_Livreur
        $id_magasin = getMagasinIdByName($restaurantName);
        $id_client = $_SESSION['userId'];
        $id_livreur = getLivreurId();
        $id_statut_commande = 1; // Default status
        $prix_demande = (float)$totalPrice;
        $prix_livraison = (float)$deliveryPrice;

        // Bind parameters
        $stmt->bindParam(1, $id_magasin, PDO::PARAM_INT);
        $stmt->bindParam(2, $id_client, PDO::PARAM_INT);
        $stmt->bindParam(3, $id_livreur, PDO::PARAM_INT);
        $stmt->bindParam(4, $id_statut_commande, PDO::PARAM_INT);
        $stmt->bindParam(5, $prix_demande, PDO::PARAM_STR);
        $stmt->bindParam(6, $prix_livraison, PDO::PARAM_STR);
        $stmt->bindParam(7, $additionalInfomag, PDO::PARAM_STR);
        $stmt->bindParam(8, $additionalInfoliv, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Get the newly inserted order ID
        $orderId = $con->lastInsertId();

        // Prepare to insert multiple products
        $sql = "INSERT INTO articles (Nom_Article, Prix, Quantite, Id_Demandes) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);

        // Loop through product arrays and insert each one
        for ($i = 0; $i < count($productNames); $i++) {
            $productName = $productNames[$i];
            $productPrice = $productPrices[$i];
            $productQuantity = $productQuantities[$i];

            // Bind parameters
            $stmt->bindParam(1, $productName, PDO::PARAM_STR);
            $stmt->bindParam(2, $productPrice, PDO::PARAM_STR);
            $stmt->bindParam(3, $productQuantity, PDO::PARAM_INT);
            $stmt->bindParam(4, $orderId, PDO::PARAM_INT);

            // Execute the statement for each product
            $stmt->execute();
        }

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
