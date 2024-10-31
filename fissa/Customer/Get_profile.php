<?php

include '../connect.php';

session_start(); // بدء الجلسة

header('Content-Type: application/json'); // تعيين نوع المحتوى إلى JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        // تخزين ID المستخدم في الجلسة
        $_SESSION['userId'] = $userId;

        try {
            // استرجاع userId من الجلسة
            $userId = $_SESSION['userId'];

            // إعداد وتنفيذ استعلام SQL
            $stmt = $con->prepare("SELECT Nom_Client AS fullName, E_mail AS email, Tel_Client AS phone, Password AS password, Coordonnes AS address FROM client WHERE Id_Client = ?");
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
            error_log("Error: " . $e->getMessage());
            echo json_encode(["error" => "Database error"]);
        }
    } else {
        echo json_encode(["error" => "User ID not provided"]);
    }
}