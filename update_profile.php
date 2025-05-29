<?php
session_start();
include 'db.php'; // db conn

header("Content-Type: application/json"); // json response
error_reporting(E_ALL);
ini_set('display_errors', 1);

// login user to verify user
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get form data
$name = isset($_POST['username']) ? trim($_POST['username']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$blood_group = isset($_POST['blood_group']) ? trim($_POST['blood_group']) : '';
$location = isset($_POST['location']) ? trim($_POST['location']) : '';
$profile_pic = "";

// upload file handling feature
if (!empty($_FILES['profile_pic']['name']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_name = basename($_FILES['profile_pic']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_ext, $allowed_ext)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type."]);
        exit;
    }

    if ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) { // Limit 2MB
        echo json_encode(["status" => "error", "message" => "File too large."]);
        exit;
    }

    // uld file
    $upload_dir = 'uploadfile/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $new_file_name = "profile_" . $user_id . "." . $file_ext;
    $upload_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $upload_path)) {
        $profile_pic = $upload_path;
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed."]);
        exit;
    }
}

// updated db using PDO (PHP data objects)
try {
    if ($profile_pic !== "") {
        $sql = "UPDATE users SET name=:name, email=:email, phone=:phone, blood_group=:blood_group, location=:location, profile_pic=:profile_pic WHERE id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':profile_pic', $profile_pic);
    } else {
        $sql = "UPDATE users SET name=:name, email=:email, phone=:phone, blood_group=:blood_group, location=:location WHERE id=:id";
        $stmt = $conn->prepare($sql);
    }

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':blood_group', $blood_group);
    $stmt->bindParam(':location', $location);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile."]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn = null; //conn closed
?>
