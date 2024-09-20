<?php
include "../connect.php"; // Including the database connection file

// Get the user ID (you can get it from session or query parameter)
$userId = 4; // Example user ID, you may replace this dynamically with session or GET method

// If form is submitted, update the information
if (isset($_POST['submit'])) {
    // Getting the data from the form
    $vehicleName = $_POST['Nom_Vehicule'];
    $deliveryPersonName = $_POST['Nom_Livreur'];
    $phoneNumber = $_POST['Tel_Livreur'];
    $password = $_POST['Password'];

    // Handling image file upload (if required for delivery person, you can remove if not needed)
    $image = null;
    if ($_FILES['Image_livreur']['tmp_name']) {
        $image = file_get_contents($_FILES['Image_livreur']['tmp_name']); // Get the image content
    }

    // Update query
    $sql = "UPDATE livreur SET Nom_Vehicule = ?, Nom_Livreur = ?, Tel_Livreur = ?, Password = ?";
    $params = [$vehicleName, $deliveryPersonName, $phoneNumber, $password];

    if ($image) {
        $sql .= ", Image_livreur = ?"; // Add image update if a new one is uploaded
        $params[] = $image;
    }

    $sql .= " WHERE Id_livreur = ?";
    $params[] = $userId;

    $stmt = $con->prepare($sql); // Prepare the statement
    $stmt->execute($params); // Execute the statement with parameters

    echo "Delivery person information updated successfully!";
}

// Fetch the user information for display in the form
$stmt = $con->prepare("SELECT * FROM livreur WHERE Id_livreur = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Delivery Person Profile</title>
</head>
<body>
    <h2>Edit Delivery Person Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="Nom_Vehicule">Vehicle Name:</label><br>
        <input type="text" id="Nom_Vehicule" name="Nom_Vehicule" value="<?php echo htmlspecialchars($user['Nom_Vehicule']); ?>" required><br><br>

        <label for="Nom_Livreur">Delivery Person Name:</label><br>
        <input type="text" id="Nom_Livreur" name="Nom_Livreur" value="<?php echo htmlspecialchars($user['Nom_Livreur']); ?>" required><br><br>

        <label for="Tel_Livreur">Phone Number:</label><br>
        <input type="tel" id="Tel_Livreur" name="Tel_Livreur" value="<?php echo htmlspecialchars($user['Tel_Livreur']); ?>" required><br><br>

        <label for="Password">Password:</label><br>
        <input type="password" id="Password" name="Password" value="<?php echo htmlspecialchars($user['Password']); ?>" required><br><br>

        <label for="Image_livreur">Delivery Person Image:</label><br>
        <input type="file" id="Image_livreur" name="Image_livreur"><br><br>

        <input type="submit" name="submit" value="Update Profile">
    </form>
</body>
</html>
