<?php

include '../connect.php';
session_start();
header('Content-Type: application/json'); // Make sure the response is always JSON

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $password = trim($_POST['password']);

        // Query to check if the account exists
        $sql = "SELECT Id_Livreur, Password, Activite_livreur, Nom_Livreur, N_Vehicule FROM livreur WHERE E_mail = :email";
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $stored_password = $user['Password'];
            $activite = $user['Activite_livreur'];
            $nom_livreur = $user['Nom_Livreur'];
            $n_vehicule = $user['N_Vehicule'];
            $userId = $user['Id_Livreur'];

            // Password check (consider using password_hash in a real app)
            if ($password === $stored_password) {
                if ($activite === "مقبول") {
                    echo json_encode([
                        'success' => true,
                        'activite' => 'مقبول',
                        'message' => 'Login successful!'
                    ]);
                } elseif ($activite === "قيد المراجعة") {
                    echo json_encode([
                        'success' => true,
                        'activite' => 'قيد المراجعة',
                        'nom_livreur' => $nom_livreur,
                        'n_vehicule' => $n_vehicule,
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
} catch (Exception $e) {
    // Log errors but return JSON
    error_log($e->getMessage()); // Log error
    echo json_encode([
        'success' => false,
        'message' => 'Server error, please try again later.'
    ]);
}
