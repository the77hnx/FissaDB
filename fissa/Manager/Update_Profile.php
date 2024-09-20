<?php
include "../connect.php"; // Including the database connection file

// Get the user ID (you can get it from session or query parameter)
$userId = 5; // Example user ID, you may replace this dynamically with session or GET method

// If form is submitted, update the information
if (isset($_POST['submit'])) {
    // Getting the data from the form
    $restaurantName = $_POST['Nom_magasin'];
    $description = $_POST['Descriptif_magasin'];
    $phoneNumber = $_POST['Tel_magasin'];
    $password = $_POST['Password'];
    $address = $_POST['Address_magasin'];
    $coordinates = $_POST['Coordonnes'];

    // Handling image file upload
    $image = null;
    if ($_FILES['Image_magasin']['tmp_name']) {
        $image = file_get_contents($_FILES['Image_magasin']['tmp_name']); // Get the image content
    }

    // Update query
    $sql = "UPDATE magasin SET Nom_magasin = ?, Descriptif_magasin = ?, Tel_magasin = ?, Password = ?, Address_magasin = ?, Coordonnes = ?";
    $params = [$restaurantName, $description, $phoneNumber, $password, $address, $coordinates];

    if ($image) {
        $sql .= ", Image_magasin = ?"; // Add image update if a new one is uploaded
        $params[] = $image;
    }

    $sql .= " WHERE Id_magasin = ?";
    $params[] = $userId;

    $stmt = $con->prepare($sql); // Prepare the statement
    $stmt->execute($params); // Execute the statement with parameters

    echo "User information updated successfully!";
}

// Fetch the user information for display in the form
$stmt = $con->prepare("SELECT * FROM magasin WHERE Id_magasin = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Restaurant Profile</title>
</head>
<body>
    <h2>Edit Restaurant Profile</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="Nom_magasin">Restaurant Name:</label><br>
        <input type="text" id="Nom_magasin" name="Nom_magasin" value="<?php echo htmlspecialchars($user['Nom_magasin']); ?>" required><br><br>

        <label for="Descriptif_magasin">Description:</label><br>
        <textarea id="Descriptif_magasin" name="Descriptif_magasin" required><?php echo htmlspecialchars($user['Descriptif_magasin']); ?></textarea><br><br>

        <label for="Tel_magasin">Phone Number:</label><br>
        <input type="tel" id="Tel_magasin" name="Tel_magasin" value="<?php echo htmlspecialchars($user['Tel_magasin']); ?>" required><br><br>

        <label for="Password">Password:</label><br>
        <input type="password" id="Password" name="Password" value="<?php echo htmlspecialchars($user['Password']); ?>" required><br><br>

        <label for="Address_magasin">Address:</label><br>
        <input type="text" id="Address_magasin" name="Address_magasin" value="<?php echo htmlspecialchars($user['Address_magasin']); ?>" required><br><br>

        <label for="Coordonnes">Coordinates:</label><br>
        <input type="text" id="Coordonnes" name="Coordonnes" value="<?php echo htmlspecialchars($user['Coordonnes']); ?>" required><br><br>

        <label for="Image_magasin">Restaurant Image:</label><br>
        <input type="file" id="Image_magasin" name="Image_magasin"><br><br>

        <input type="submit" name="submit" value="Update Profile">
    </form>
</body>
</html>
