<?php
include "../connect.php"; // Including the database connection file

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// If form is submitted, update the information
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting the data from the form
    $userId = $_POST['user_id'];
    $restaurantName = $_POST['Nom_magasin'];
    $description = $_POST['Descriptif_magasin'];
    $phoneNumber = $_POST['Tel_magasin'];
    $password = $_POST['Password'];
    $address = $_POST['Address_magasin'];
    $coordinates = $_POST['Coordonnes'];
   

    // تخزين ID المستخدم في الجلسة
    $_SESSION['userId'] = $userId;

    // استرجاع userId من الجلسة
    $userId = $_SESSION['userId'];
    
                // Handle file upload if exists
            $imagePath = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $imageTmpName = $_FILES['profile_image']['tmp_name'];
                $imageName = $_FILES['profile_image']['name'];
                $imageSize = $_FILES['profile_image']['size'];
                $imageType = $_FILES['profile_image']['type'];

                // Validating the image type
                $validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($imageType, $validImageTypes)) {
                    $uploadDirectory = "../uploads/profile_images/";
                    if (!file_exists($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true); // Create directory if it doesn't exist
                    }

                    // Generate a unique name for the uploaded file to avoid overwriting
                    $imagePath = $uploadDirectory . uniqid('profile_', true) . basename($imageName);
                    move_uploaded_file($imageTmpName, $imagePath);
                } else {
                    echo "Invalid image type.";
                    exit;
                }
            }

    // Update query
    $sql = "UPDATE magasin SET Nom_magasin = ?, Descriptif_magasin = ?, Tel_magasin = ?, Password = ?, Address_magasin = ?, Coordonnes = ?, Image_path = ? WHERE Id_magasin = ?";
    $params = [$restaurantName, $description, $phoneNumber, $password, $address, $coordinates, $imagePath, $userId];

    $stmt = $con->prepare($sql); // Prepare the statement
    if ($stmt->execute($params)) {
        if ($stmt->rowCount() > 0) {
            echo "User information updated successfully!";
        } else {
            echo "No changes were made."; // No rows affected
        }
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Failed to update user information. Error: " . $errorInfo[2];
    }
} else {
    echo "PHP script started.";
}

// Fetch the user information for display in the form
$stmt = $con->prepare("SELECT * FROM magasin WHERE Id_magasin = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);