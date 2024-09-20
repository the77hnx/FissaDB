<?php
// Include the database connection file
include "../connect.php";

// Start the session to access the current user
session_start();

// Assuming you store the user ID in the session after login
$current_user_id = $_SESSION['user_id']; 

// Fetch old orders of the current user from the database
try {
    // Prepare the SQL query to retrieve orders with store name and order status
    $stmt = $con->prepare("
        SELECT 
            o.Id_Demandes AS orderId,
            s.Nom_magasin AS storeName,
            o.Prix_Livraison AS deliveryPrice,
            o.Prix_Demande AS orderPrice,
            o.Date_commande AS orderDate,
            o.Heure_commande AS orderTime,
            st.Nom_Statut AS statusName
        FROM orders o
        JOIN store s ON o.Id_Magasin = s.Id_Magasin
        JOIN status st ON o.Id_Statut_Commande = st.Id_Statut_Commande
        WHERE o.Id_Client = :user_id
        ORDER BY o.Date_commande DESC
    ");

    // Bind the user ID to the query
    $stmt->bindParam(':user_id', $current_user_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch all the user's orders
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any orders were found
    if ($orders) {
        // Loop through the orders and display them
        foreach ($orders as $order) {
            echo "<div class='order'>";
            echo "<h3>Order #" . htmlspecialchars($order['orderId']) . "</h3>";
            echo "<p>Store Name: " . htmlspecialchars($order['storeName']) . "</p>";
            echo "<p>Order Price: " . htmlspecialchars($order['orderPrice']) . "</p>";
            echo "<p>Delivery Price: " . htmlspecialchars($order['deliveryPrice']) . "</p>";
            echo "<p>Date of Order: " . htmlspecialchars($order['orderDate']) . "</p>";
            echo "<p>Time of Order: " . htmlspecialchars($order['orderTime']) . "</p>";
            echo "<p>Status: " . htmlspecialchars($order['statusName']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No orders found for the current user.</p>";
    }

} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}
