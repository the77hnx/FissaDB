<?php
include '../connect.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $statut_magasin = isset($_POST['statut_magasin']) ? $_POST['statut_magasin'] : 'غير متاح';

    $_SESSION['userId'] = $userId;

    $current_user_id = $_SESSION['userId'];
    
    $stmt = $con->prepare("SELECT Statut_Livreur FROM livreur WHERE Id_Livreur = ?");
    $stmt->bindParam(1, $current_user_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $currentStatus = $stmt->fetchColumn();

    if ($currentStatus !== $statut_magasin) {
        $updateStmt = $con->prepare("UPDATE livreur SET Statut_Livreur = ? WHERE Id_Livreur = ?");
        $updateStmt->bindParam(1, $statut_magasin, PDO::PARAM_STR);
        $updateStmt->bindParam(2, $current_user_id, PDO::PARAM_INT);
        $updateStmt->execute();
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
