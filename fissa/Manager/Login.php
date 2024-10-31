<?php

include '../connect.php';

session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = trim($_POST['password']);

    // Query to check if magasin exists
    $sql = "SELECT Id_magasin, Password, Activite_magasin, Nom_magasin, N_Enregistrement FROM magasin WHERE E_mail = :email";  // Ensure column 'E_mail' exists, otherwise replace with correct one
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stored_password = $user['Password'];
        $activite = $user['Activite_magasin'];
        $nom_magasin = $user['Nom_magasin'];
        $n_enregistrement = $user['N_Enregistrement'];
        $userId = $user['Id_magasin'];

        // Password check (consider using password_hash in a real app)
        if ($password === $stored_password) {
            if ($activite === "مقبول") {
                echo json_encode([
                    'success' => true,
                    'activite' => 'مقبول',
                    'message' => 'Login successful!',
                    'userId' => $userId // Include the user ID in the response
                ]);
                $_SESSION['userId'] = $userId; // Set the user ID in the session
            } elseif ($activite === "قيد المراجعة") {
                echo json_encode([
                    'success' => true,
                    'activite' => 'قيد المراجعة',
                    'nom_magasin' => $nom_magasin,
                    'n_enregistrement' => $n_enregistrement,
                    'message' => 'Your account is under review.'
                ]);
            }
            $_SESSION['userId'] = $userId; // Set the session
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