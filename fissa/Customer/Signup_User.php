<?php
include "../connect.php";
session_start();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if (!empty($fullName) && !empty($email) && !empty($phone) && !empty($password)) {
        // Insert user with plain text password
        $stmt = $con->prepare("INSERT INTO client (Nom_Client, E_mail, Tel_Client, Password) 
                               VALUES (:full_name, :email, :phone, :password)");
        $stmt->bindParam(':full_name', $fullName);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo "User signed up successfully!";
            $_SESSION['userId'] = $userId; // Set the user ID in the session
        } else {
            echo "Error signing up user.";
        }
    } else {
        echo "All fields are required!";
    }
}
?>

<form method="post" action="signup.php">
    Full Name: <input type="text" name="full_name" required><br>
    Email: <input type="email" name="email" required><br>
    Phone: <input type="text" name="phone" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Sign Up">
</form>
