<?php

include '../connect.php';

session_start(); // بدء الجلسة

header('Content-Type: application/json'); // تعيين نوع المحتوى إلى JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method"]);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // تخزين ID المستخدم في الجلسة
        $_SESSION['userId'] = $userId;

        try {
            // استرجاع userId من الجلسة
            $userId = $_SESSION['userId'];

            // إعداد وتنفيذ استعلام SQL
            $stmt = $con->prepare("SELECT Nom_magasin AS Nom_magasin, Descriptif_magasin AS Descriptif_magasin, Tel_magasin AS Tel_magasin, Coordonnes AS Coordonnes, Address_magasin AS Address_magasin, Password AS Password , Image_path AS imagePath  FROM magasin WHERE Id_magasin = ?");
            $stmt->execute([$userId]);

            // استرجاع النتيجة كمصفوفة ترابطية
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // التحقق مما إذا كانت بيانات المستخدم موجودة
            if (empty($user)) {
                echo json_encode(["error" => "User not found"]);
            } else {
                // إعادة بيانات المستخدم بصيغة JSON
                echo json_encode($user);
            }
        } catch (PDOException $e) {
               error_log("Database error: " . $e->getMessage());
    echo json_encode(["error" => "Internal server error"]);
    http_response_code(500); // Optional for clarity
        }
    } else {
        echo json_encode(["error" => "User ID not provided"]);
    }
}