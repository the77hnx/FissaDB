<?php
// Include the database connection file
include '../connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $nom_livreur = $_POST['etNameLivreur'];
    $email = $_POST['etemail'];
    $password = $_POST['etPassword'];
    $tel_livreur = $_POST['etnumber'];
    $nom_vehicule = $_POST['etNameveh'];
    $n_vehicule = $_POST['etN0Enrgveh'];
    $n_id_national = $_POST['etIdNational'];
    $coordonnes = $_POST['etplacesres'];
    $activite_Livreur = "قيد المراجعة";

    // Prepare an SQL statement for execution
    $sql = "INSERT INTO livreur (
        Nom_Livreur, E_mail, Password, Tel_Livreur, Nom_Vehicule, N_Vehicule, N_Id_National, Coordonnes, Activite_livreur
    ) VALUES (
        :nom_livreur, :email, :password, :tel_livreur, :nom_vehicule, :n_vehicule, :n_id_national, :coordonnes, :activite_livreur
    )";

    // Prepare the statement
    $stmt = $con->prepare($sql);

    // Bind all the parameters
    $stmt->bindValue(':nom_livreur', $nom_livreur, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':tel_livreur', $tel_livreur, PDO::PARAM_STR);
    $stmt->bindValue(':nom_vehicule', $nom_vehicule, PDO::PARAM_STR);
    $stmt->bindValue(':n_vehicule', $n_vehicule, PDO::PARAM_STR);
    $stmt->bindValue(':n_id_national', $n_id_national, PDO::PARAM_STR);
    $stmt->bindValue(':coordonnes', $coordonnes, PDO::PARAM_STR);
    $stmt->bindValue(':activite_livreur', $activite_Livreur, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        echo "<p>Record inserted successfully!</p>";
    } else {
        echo "<p>Error inserting record: " . $stmt->errorInfo()[2] . "</p>";
    }
}