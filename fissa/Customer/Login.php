<?php

include '../connect.php';

session_start(); // Start the session

header('Content-Type: application/json'); // Set the content type to JSON

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = trim($_POST['password']);

    // Check user credentials
    $sql = "SELECT Id_Client, Password FROM client WHERE E_mail = :email";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_password = $user['Password'];
        $userId = $user['Id_Client'];

        // Directly compare passwords (for demonstration purposes only)
        if ($password === $stored_password) {
            echo json_encode([
                'success' => true,
                'message' => 'Login successful!'
            ]);
            $_SESSION['userId'] = $userId; // Set the user ID in the session
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid password.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No account found with that email.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}

