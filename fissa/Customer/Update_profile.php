<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../connect.php"; // تأكد من مسار الاتصال بقاعدة البيانات
session_start();

// Check if the user ID is stored in the session
if (!isset($_SESSION['userId'])) {
    die("User ID not found in session. Please log in.");
} else {
    $userId = $_SESSION['userId']; // Get the user ID from the session
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات من الطلب
    $fullName = $_POST['fullName'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $password = $_POST['password'] ?? null;
    $coordonnees = $_POST['coordonnees'] ?? null;

    // التحقق من وجود جميع البيانات المطلوبة
    if (!$fullName || !$email || !$phone || !$password || !$coordonnees) {
        echo "Missing parameters";
    } else {
        try {
            // إعداد وتنفيذ استعلام SQL لتحديث بيانات المستخدم
            $stmt = $con->prepare("
                UPDATE client 
                SET Nom_Client = ?, Tel_Client = ?, E_mail = ?, Password = ?, Coordonnes = ?
                WHERE Id_Client = ?
            ");
            $result = $stmt->execute([$fullName, $phone, $email, $password, $coordonnees, $userId]);

            // التحقق مما إذا كانت هناك أي صفوف قد تم تحديثها
            if ($result) {
                echo "User data updated successfully";
            } else {
                echo "User not found or no changes made";
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            echo "Database error: " . $e->getMessage();
        }
    }

} else {

    // Display the form
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Client Data</title>
    </head>
    <body>
        <h1>Update Client Data</h1>
        <form action="" method="post">
            <label for="fullName">Full Name:</label>
            <input type="text" id="fullName" name="fullName" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="coordonnees">Coordonnees:</label>
            <input type="text" id="coordonnees" name="coordonnees" required><br><br>

            <input type="submit" value="Update Data">
        </form>
    </body>
    </html>
    ';
}
