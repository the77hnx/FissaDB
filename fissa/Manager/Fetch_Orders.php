<?php
include '../connect.php'; // Include the database connection

// Function to fetch orders based on status
function fetchOrdersByStatus($statusId) {
    global $con; // Using global connection
    $stmt = $con->prepare("SELECT * FROM demandes WHERE Id_Statut_Commande = :statusId");
    $stmt->bindValue(':statusId', $statusId, PDO::PARAM_INT);
    $stmt->execute();
    $orders = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $orders[] = $row;
    }
    return $orders;
}

// Fetch orders for each status ID
$newOrders = fetchOrdersByStatus(1);
$preparationOrders = fetchOrdersByStatus(2);
$deliveryOrders = fetchOrdersByStatus(3);
$waitingOrders = fetchOrdersByStatus(4);
$cancelledOrders = fetchOrdersByStatus(5);
$completedOrders = fetchOrdersByStatus(6);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Viewer</title>
</head>
<body>
    <h1>Order Status Viewer</h1>

    <!-- Form to select and view orders by status -->
    <form method="GET" action="">
        <label for="status">Select Order Status:</label>
        <select id="status" name="status">
            <option value="1">New</option>
            <option value="2">In Preparation</option>
            <option value="3">In Delivery</option>
            <option value="4">Waiting for Delivery</option>
            <option value="5">Cancelled</option>
            <option value="6">Completed</option>
        </select>
        <button type="submit">View Orders</button>
    </form>

    <?php
    if (isset($_GET['status'])) {
        $status = intval($_GET['status']);
        $orders = fetchOrdersByStatus($status);

        echo "<h2>Orders with Status ID: $status</h2>";
        if (!empty($orders)) {
            echo "<ul>";
            foreach ($orders as $order) {
                // Adjust the fields based on your table schema
                echo "<li>Order ID: {$order['Id_Demandes']} - Details: {$order['Date_commande']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No orders found for this status.</p>";
        }
    }
    ?>
</body>
</html>
