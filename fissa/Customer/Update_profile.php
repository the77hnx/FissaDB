// <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../connect.php"; // تأكد من مسار الاتصال بقاعدة البيانات
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استلام البيانات من الطلب
    $userId = $_POST['user_id'];
    $fullName = $_POST['fullName'] ?? null;
    $email = $_POST['email'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $password = $_POST['password'] ?? null;
    $coordonnees = $_POST['coordonnees'] ?? null;

    // تخزين ID المستخدم في الجلسة
    $_SESSION['userId'] = $userId;

    // التحقق من وجود جميع البيانات المطلوبة
    if (!$fullName || !$email || !$phone || !$password || !$coordonnees) {
        echo "Missing parameters";
    } else {
        try {
            // استرجاع userId من الجلسة
            $userId = $_SESSION['userId'];

            // Handle file upload if exists
            $imagePath = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $imageTmpName = $_FILES['profile_image']['tmp_name'];
                $imageName = $_FILES['profile_image']['name'];
                $imageSize = $_FILES['profile_image']['size'];
                $imageType = $_FILES['profile_image']['type'];

                // Validating the image type
                $validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($imageType, $validImageTypes)) {
                    $uploadDirectory = "../uploads/profile_images/";
                    if (!file_exists($uploadDirectory)) {
                        mkdir($uploadDirectory, 0777, true); // Create directory if it doesn't exist
                    }

                    // Generate a unique name for the uploaded file to avoid overwriting
                    $imagePath = $uploadDirectory . uniqid('profile_', true) . basename($imageName);
                    move_uploaded_file($imageTmpName, $imagePath);
                } else {
                    echo "Invalid image type.";
                    exit;
                }
            }

            // إعداد وتنفيذ استعلام SQL لتحديث بيانات المستخدم
            $stmt = $con->prepare("UPDATE client SET Nom_Client = ?, Tel_Client = ?, E_mail = ?, Password = ?, Coordonnes = ?, Image_path = ? WHERE Id_Client = ?");
            $result = $stmt->execute([$fullName, $phone, $email, $password, $coordonnees, $imagePath, $userId]);

            // التحقق مما إذا كانت هناك أي صفوف قد تم تحديثها
            if ($result) {
                echo "User data updated successfully";
            } else {
                echo "User not found or no changes made";
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            echo "Database error: " . $e->getMessage();
        }
    }
}
