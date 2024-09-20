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
    $nom_vehicule = $_POST['etNameVehicule'];
    $n_vehicule = $_POST['etN0Vehicule'];
    $n_id_national = $_POST['etIdNational'];
    $coordonnes = $_POST['etCoordonnes'];

    // Prepare an SQL statement for execution
    $sql = "INSERT INTO livreur (
        Nom_Livreur, E_mail, Password, Tel_Livreur, Nom_Vehicule, N_Vehicule, N_Id_National, Coordonnes
    ) VALUES (
        :nom_livreur, :email, :password, :tel_livreur, :nom_vehicule, :n_vehicule, :n_id_national, :coordonnes
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

    // Execute the statement
    if ($stmt->execute()) {
        echo "<p>Record inserted successfully!</p>";
    } else {
        echo "<p>Error inserting record: " . $stmt->errorInfo()[2] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Livreur Data</title>
</head>
<body>
    <h2>Insert Livreur Data</h2>
    <form action="" method="POST">
        <label for="etNameLivreur">Livreur Name:</label><br>
        <input type="text" id="etNameLivreur" name="etNameLivreur" required><br><br>

        <label for="etemail">Email:</label><br>
        <input type="email" id="etemail" name="etemail" required><br><br>

        <label for="etPassword">Password:</label><br>
        <input type="password" id="etPassword" name="etPassword" required><br><br>

        <label for="etnumber">Phone Number:</label><br>
        <input type="text" id="etnumber" name="etnumber" required><br><br>

        <label for="etNameVehicule">Vehicle Name:</label><br>
        <input type="text" id="etNameVehicule" name="etNameVehicule" required><br><br>

        <label for="etN0Vehicule">Vehicle Number:</label><br>
        <input type="text" id="etN0Vehicule" name="etN0Vehicule" required><br><br>

        <label for="etIdNational">National ID Number:</label><br>
        <input type="text" id="etIdNational" name="etIdNational" required><br><br>

        <label for="etCoordonnes">Coordinates:</label><br>
        <input type="text" id="etCoordonnes" name="etCoordonnes" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
