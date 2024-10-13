<?php
// Include the database connection file
include '../connect.php';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from POST request
    $nom_magasin = $_POST['etNameMag'];
    $email = $_POST['etemail'];
    $password = $_POST['etPassword'];
    $tel_magasin = $_POST['etnumber'];
    $nom_prop_magasin = $_POST['etName'];
    $descriptif_magasin = $_POST['etDescriptionres'];
    $address_magasin = $_POST['etplacesres'];
    $n_enregistrement = $_POST['etN0Enrg'];
    $n_id_national = $_POST['etIdNational'];
    $activite_magasin = "قيد المراجعة";

    
    // Prepare an SQL statement for execution
    $sql = "INSERT INTO magasin (
        Nom_magasin, E_mail, Password, Tel_magasin, Nom_prop_magasin, 
        Descriptif_magasin, Address_magasin, N_Enregistrement, N_Id_National, Activite_magasin) 
            VALUES (
        :nom_magasin, :email, :password, :tel_magasin, :nom_prop_magasin, 
        :descriptif_magasin, :address_magasin, :n_enregistrement, :n_id_national, :activite_magasin)";

    // Prepare the statement
    $stmt = $con->prepare($sql);

    // Bind the parameters
    $stmt->bindValue(':nom_magasin', $nom_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':tel_magasin', $tel_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':nom_prop_magasin', $nom_prop_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':descriptif_magasin', $descriptif_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':address_magasin', $address_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':n_enregistrement', $n_enregistrement, PDO::PARAM_STR);
    $stmt->bindValue(':n_id_national', $n_id_national, PDO::PARAM_STR);
    $stmt->bindValue(':activite_magasin', $activite_magasin, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record inserted successfully!";
    } else {
        echo "Error inserting record: " . $stmt->errorInfo()[2];
    }
}
?>
