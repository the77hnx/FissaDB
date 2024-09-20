<?php
include "../connect.php";

// Set Id_Livreur to 1 initially (for the logged-in user)
$Id_Livreur = 1;

// Query to get all orders for the logged-in Livreur
$sql_orders = "SELECT Id_Demandes, Date_commande, Heure_commande 
               FROM demandes 
               WHERE Id_Livreur = :Id_Livreur";

$stmt_orders = $con->prepare($sql_orders);
$stmt_orders->bindParam(':Id_Livreur', $Id_Livreur, PDO::PARAM_INT);
$stmt_orders->execute();
$result_orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

// Prepare the order details for the HTML form below
$orders = [];
foreach($result_orders as $row_orders) {
    $Id_Demandes = $row_orders['Id_Demandes'];
    $Date_commande = $row_orders['Date_commande'];
    $Heure_commande = $row_orders['Heure_commande'];

    // Query to get the number of items for the current order
    $sql_items = "SELECT COUNT(*) AS num_items 
                  FROM articles 
                  WHERE Id_Demandes = :Id_Demandes";
    $stmt_items = $con->prepare($sql_items);
    $stmt_items->bindParam(':Id_Demandes', $Id_Demandes, PDO::PARAM_INT);
    $stmt_items->execute();
    $row_items = $stmt_items->fetch(PDO::FETCH_ASSOC);
    $num_items = $row_items['num_items'];

    // Store the order information
    $orders[] = [
        'Id_Demandes' => $Id_Demandes,
        'num_items' => $num_items,
        'Date_commande' => $Date_commande,
        'Heure_commande' => $Heure_commande
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
</head>
<body>
    <h1>Orders for Livreur: <?php echo $Id_Livreur; ?></h1>
    <table border="1">
        <thead>
            <tr>
                <th>Order Number (Id_Demandes)</th>
                <th>Number of Items</th>
                <th>Order Date</th>
                <th>Order Time</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Loop through the orders and display them
        foreach ($orders as $order) {
            echo "<tr>
                    <td>{$order['Id_Demandes']}</td>
                    <td>{$order['num_items']}</td>
                    <td>{$order['Date_commande']}</td>
                    <td>{$order['Heure_commande']}</td>
                </tr>";
        }
        ?>
        </tbody>
    </table>
</body>
</html>
