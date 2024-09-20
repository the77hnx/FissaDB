<?php
include '../connect.php';

// Assuming you get the delivery worker's ID from a request or session.
$deliveryWorkerID = 5; // Set to 5 for this example

// Get delivery worker details (name, balance, accepted requests, rejected requests, evaluation)
$sql = "SELECT Nom_Livreur, Balance, Accept_Dem, Remove_Dem, Evaluation 
        FROM livreur 
        WHERE Id_Livreur = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$deliveryWorkerID]);
$worker = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if worker exists
if ($worker) {
    // No filtering by day, week, or month since you don't need it
    $balanceToday = $worker['Balance']; // Use total balance
    $balanceWeek = $worker['Balance']; // Use total balance
    $balanceMonth = $worker['Balance']; // Use total balance

    // Get today's accepted requests (assuming orders are stored in another table)
    $sqlAcceptToday = "SELECT COUNT(*) AS accepted_today 
                       FROM livreur 
                       WHERE Id_Livreur = ? AND Accept_Dem = 1";
    $stmt = $con->prepare($sqlAcceptToday);
    $stmt->execute([$deliveryWorkerID]);
    $acceptedToday = $stmt->fetch(PDO::FETCH_ASSOC)['accepted_today'];

    // Get this month's accepted requests (same as above, no date filtering)
    $acceptedMonth = $worker['Accept_Dem']; // Use total accepted requests
} else {
    echo "No delivery worker found with the provided ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Information</title>
</head>
<body>

    <h1>Worker Information</h1>
    
    <form>
        <label>Name: </label>
        <input type="text" value="<?php echo $worker['Nom_Livreur']; ?>" disabled><br>

        <label>Balance for Today: </label>
        <input type="text" value="<?php echo $balanceToday ?? 0; ?>" disabled><br>

        <label>Balance for This Week: </label>
        <input type="text" value="<?php echo $balanceWeek ?? 0; ?>" disabled><br>

        <label>Balance for This Month: </label>
        <input type="text" value="<?php echo $balanceMonth ?? 0; ?>" disabled><br>

        <label>Total Accepted Requests: </label>
        <input type="text" value="<?php echo $worker['Accept_Dem']; ?>" disabled><br>

        <label>Total Rejected Requests: </label>
        <input type="text" value="<?php echo $worker['Remove_Dem']; ?>" disabled><br>

        <label>Accepted Requests for Today: </label>
        <input type="text" value="<?php echo $acceptedToday ?? 0; ?>" disabled><br>

        <label>Accepted Requests for This Month: </label>
        <input type="text" value="<?php echo $acceptedMonth ?? 0; ?>" disabled><br>

        <label>Delivery Worker Evaluation: </label>
        <input type="text" value="<?php echo $worker['Evaluation']; ?>" disabled><br>
    </form>

</body>
</html>
