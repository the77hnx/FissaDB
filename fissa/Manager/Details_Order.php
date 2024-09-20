<?php
include("../connect.php");

$orderId = isset($_POST['Id_Demandes']) ? $_POST['Id_Demandes'] : null;

function getOrderList($con) {
    $sql = "SELECT Id_Demandes, Date_commande, Heure_commande FROM demandes";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    
    // Fetching all orders
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($orders) > 0) {
        echo "<form method='POST' action=''>";
        echo "<select name='Id_Demandes' onchange='this.form.submit()'>";
        echo "<option value=''>Select an order</option>";
        foreach ($orders as $row) {
            echo "<option value='".$row['Id_Demandes']."'>Order #".$row['Id_Demandes']." - ".$row['Date_commande']." ".$row['Heure_commande']."</option>";
        }
        echo "</select>";
        echo "</form>";
    } else {
        echo "No orders found.";
    }
}

function getOrderDetails($con, $orderId) {
    if (!$orderId) {
        return;
    }

    // SQL to fetch order details
    $sql = "SELECT d.Id_Demandes, d.Date_commande, d.Heure_commande, d.Prix_Demande, d.Prix_Livraison, 
                   c.Nom_Client, c.Tel_Client, c.E_mail, c.Coordonnes, d.info_mag, s.Nom_Statut 
            FROM demandes d 
            JOIN client c ON d.Id_Client = c.Id_Client
            JOIN stat_cmd s ON d.Id_Statut_Commande = s.Id_Statut_Commande
            WHERE d.Id_Demandes = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo "<h2>Order Details for Order #".$order['Id_Demandes']."</h2>";
        echo "<p>Customer Name: ".$order['Nom_Client']."</p>";
        echo "<p>Order Date: ".$order['Date_commande']." ".$order['Heure_commande']."</p>";
        echo "<p>Customer Phone: ".$order['Tel_Client']."</p>";
        echo "<p>Customer Email: ".$order['E_mail']."</p>";
        echo "<p>Customer Coordinates: ".$order['Coordonnes']."</p>";
        echo "<p>Customer Message: ".$order['info_mag']."</p>";
        echo "<p>Order Price: ".$order['Prix_Demande']."</p>";
        echo "<p>Delivery Price: ".$order['Prix_Livraison']."</p>";
        echo "<p>Order Status: ".$order['Nom_Statut']."</p>";

        // Fetching order items (corrected table and column names)
        echo "<h3>Order Items</h3>";
        $sql_items = "SELECT * FROM articles WHERE Id_Demandes = ?"; // Adjust the column name if necessary
        $stmt_items = $con->prepare($sql_items);
        $stmt_items->execute([$orderId]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        if (count($items) > 0) {
            foreach ($items as $item) {
                echo "<p>Item: ".$item['Nom_Article']." - Quantity: ".$item['Quantite']."</p>";
            }
        } else {
            echo "<p>No items found for this order.</p>";
        }
    } else {
        echo "<p>Order details not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
</head>
<body>

<h1>Order List</h1>
<?php
// Display the list of orders
getOrderList($con);

// If an order ID is selected, display the order details
if ($orderId) {
    getOrderDetails($con, $orderId);
}
?>

</body>
</html>
