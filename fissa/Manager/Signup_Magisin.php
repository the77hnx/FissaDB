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

    // // Handle file upload (image upload handling in OkHttp POST request)
    // $image_magasin = null;
    // if (isset($_FILES['image.png']) && $_FILES['image.png']['error'] == 0) {
    //     $image_magasin = file_get_contents($_FILES['image.png']['tmp_name']);
    //     $image_type = $_FILES['image.png']['type'];

    //     // Validate image type
    //     $allowed_types = ['image/jpeg', 'image/png'];
    //     if (!in_array($image_type, $allowed_types)) {
    //         echo "Invalid image type.";
    //         exit;
    //     }
    // } else {
    //     echo "Please upload an image.";
    //     exit;
    // }

    // Prepare an SQL statement for execution
    $sql = "INSERT INTO magasin (
    -- Image_magasin,
     Nom_magasin, E_mail, Password, Tel_magasin, Nom_prop_magasin, Descriptif_magasin, Address_magasin, N_Enregistrement, N_Id_National) 
            VALUES (
            -- :image_magasin, 
            :nom_magasin, :email, :password, :tel_magasin, :nom_prop_magasin, :descriptif_magasin, :address_magasin, :n_enregistrement, :n_id_national)";

    // Prepare the statement
    $stmt = $con->prepare($sql);

    // Bind all the parameters including the image
    // $stmt->bindValue(':image_magasin', $image_magasin, PDO::PARAM_LOB);  // For binary image data
    $stmt->bindValue(':nom_magasin', $nom_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':tel_magasin', $tel_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':nom_prop_magasin', $nom_prop_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':descriptif_magasin', $descriptif_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':address_magasin', $address_magasin, PDO::PARAM_STR);
    $stmt->bindValue(':n_enregistrement', $n_enregistrement, PDO::PARAM_STR);
    $stmt->bindValue(':n_id_national', $n_id_national, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Record inserted successfully!";
    } else {
        echo "Error inserting record: " . $stmt->errorInfo()[2];
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Magasin Data</title>
</head>
<body>
    <h2>Insert Magasin Data</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <!-- <label for="image">Magasin Image:</label><br>
        <input type="file" id="image" name="image" accept="image/*" required><br><br> -->

        <label for="etNameMag">Magasin Name:</label><br>
        <input type="text" id="etNameMag" name="etNameMag" required><br><br>

        <label for="etemail">Email:</label><br>
        <input type="email" id="etemail" name="etemail" required><br><br>

        <label for="etPassword">Password:</label><br>
        <input type="password" id="etPassword" name="etPassword" required><br><br>

        <label for="etnumber">Phone Number:</label><br>
        <input type="text" id="etnumber" name="etnumber" required><br><br>

        <label for="etName">Owner Name:</label><br>
        <input type="text" id="etName" name="etName" required><br><br>

        <label for="etDescriptionres">Magasin Description:</label><br>
        <textarea id="etDescriptionres" name="etDescriptionres" required></textarea><br><br>

        <label for="etplacesres">Address:</label><br>
        <input type="text" id="etplacesres" name="etplacesres" required><br><br>

        <label for="etN0Enrg">Registration Number:</label><br>
        <input type="text" id="etN0Enrg" name="etN0Enrg" required><br><br>

        <label for="etIdNational">National ID Number:</label><br>
        <input type="text" id="etIdNational" name="etIdNational" required><br><br>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
