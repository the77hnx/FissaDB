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

    // SQL to fetch order details, including restaurant and delivery worker information
    $sql = "SELECT d.Id_Demandes, d.Date_commande, d.Heure_commande, d.Prix_Demande, d.Prix_Livraison, 
                   c.Nom_Client, c.Tel_Client, c.E_mail, c.Coordonnes, d.info_mag, d.info_liv, s.Nom_Statut, 
                   r.Nom_magasin, r.Image_magasin, r.Address_magasin, r.Statut_magasin, r.Evaluation, 
                   l.Nom_Livreur, l.Photo_person, l.Coordonnes AS Livreur_Coordonnes, l.Tel_Livreur 
            FROM demandes d 
            JOIN client c ON d.Id_Client = c.Id_Client
            JOIN stat_cmd s ON d.Id_Statut_Commande = s.Id_Statut_Commande
            JOIN magasin r ON d.Id_magasin = r.Id_magasin
            JOIN livreur l ON d.Id_Livreur = l.Id_Livreur
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
        echo "<p>Additional Info for Restaurant: ".$order['info_mag']."</p>";
        echo "<p>Additional Info for Delivery Worker: ".$order['info_liv']."</p>";
        echo "<p>Order Price: ".$order['Prix_Demande']."</p>";
        echo "<p>Delivery Price: ".$order['Prix_Livraison']."</p>";
        echo "<p>Order Status: ".$order['Nom_Statut']."</p>";

        // Displaying restaurant information
        echo "<h3>Restaurant Information</h3>";
        echo "<p>Restaurant Name: ".$order['Nom_magasin']."</p>";
        echo "<p>Restaurant Location: ".$order['Address_magasin']."</p>";
        echo "<p>Restaurant Status: ".$order['Statut_magasin']."</p>";
        echo "<p>Restaurant Rating: ".$order['Evaluation']."</p>";
        echo "<img src='".$order['Image_magasin']."' alt='Restaurant Image' width='150' height='150'/>";

        // Displaying delivery worker information
        echo "<h3>Delivery Worker Information</h3>";
        echo "<p>Delivery Worker Name: ".$order['Nom_Livreur']."</p>";
        echo "<p>Delivery Worker Phone: ".$order['Tel_Livreur']."</p>";
        echo "<p>Delivery Worker Coordinates: ".$order['Livreur_Coordonnes']."</p>";
        echo "<img src='".$order['Photo_person']."' alt='Delivery Worker Photo' width='150' height='150'/>";

        // Fetching order items
        echo "<h3>Order Items</h3>";
        $sql_items = "SELECT Nom_Article, Quantite, Prix FROM articles WHERE Id_Demandes = ?";
        $stmt_items = $con->prepare($sql_items);
        $stmt_items->execute([$orderId]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        if (count($items) > 0) {
            foreach ($items as $item) {
                echo "<p>Item: ".$item['Nom_Article']." - Quantity: ".$item['Quantite']." - Price: ".$item['Prix']."</p>";
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
