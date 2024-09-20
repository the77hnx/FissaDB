<?php
// Include the database connection file
include("../connect.php");

// Function to get the ID of the logged-in store
function getStoreId() {
    // Return the hardcoded store ID
    return 5; // Setting Id_magasin to 5
}

// Get the ID of the store
$storeId = getStoreId();

// Initialize variables for statistics
$numProducts = 0;
$acceptedOrders = 0;
$cancelledOrders = 0;
$todaysOrders = 0;
$monthlyOrders = 0;
$storeEvaluation = 0;

// Fetch number of products
$query = "SELECT COUNT(*) as num_products FROM produits WHERE Id_magasin = :storeId";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$numProducts = $row['num_products'];

// Fetch accepted orders
$query = "SELECT COUNT(*) as accepted_orders FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande IN (1, 2, 3, 4, 6)";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$acceptedOrders = $row['accepted_orders'];

// Fetch cancelled orders
$query = "SELECT COUNT(*) as cancelled_orders FROM demandes WHERE Id_magasin = :storeId AND Id_Statut_Commande = 5";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$cancelledOrders = $row['cancelled_orders'];

// Fetch today's orders
$todayDate = date('Y-m-d');
$query = "SELECT COUNT(*) as todays_orders FROM demandes WHERE Id_magasin = :storeId AND DATE(Date_commande) = :todayDate";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->bindValue(':todayDate', $todayDate, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$todaysOrders = $row['todays_orders'];

// Fetch monthly orders
$thisMonth = date('Y-m');
$query = "SELECT COUNT(*) as monthly_orders FROM demandes WHERE Id_magasin = :storeId AND DATE_FORMAT(Date_commande, '%Y-%m') = :thisMonth";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->bindValue(':thisMonth', $thisMonth, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$monthlyOrders = $row['monthly_orders'];

// Fetch store evaluation
$query = "SELECT Evaluation FROM magasin WHERE Id_magasin = :storeId";
$stmt = $con->prepare($query);
$stmt->bindValue(':storeId', $storeId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$storeEvaluation = $row['Evaluation'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magasin Statistics</title>
</head>
<body>
    <h1>Magasin Statistics</h1>
    <form method="post">
        <h2>Statistics for Store ID: <?php echo htmlspecialchars($storeId); ?></h2>
        <p>Number of Products: <?php echo htmlspecialchars($numProducts); ?></p>
        <p>Accepted Orders: <?php echo htmlspecialchars($acceptedOrders); ?></p>
        <p>Cancelled Orders: <?php echo htmlspecialchars($cancelledOrders); ?></p>
        <p>Today's Orders: <?php echo htmlspecialchars($todaysOrders); ?></p>
        <p>This Month's Orders: <?php echo htmlspecialchars($monthlyOrders); ?></p>
        <p>Store Evaluation: <?php echo htmlspecialchars($storeEvaluation); ?></p>
    </form>
</body>
</html>
