<?php
session_start();
include 'db.php'; // Database connection file

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

$name = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$blood_group = $_POST['blood_group'];
$location = $_POST['location'];
$profile_pic = "";

// Handle File Upload
if (!empty($_FILES['profile_pic']['name'])) {
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_name = basename($_FILES['profile_pic']['name']);
    $file_size = $_FILES['profile_pic']['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG and PNG allowed."]);
        exit;
    }
    
    if ($file_size > 2 * 1024 * 1024) {
        echo json_encode(["status" => "error", "message" => "File size exceeds 2MB limit."]);
        exit;
    }

    $upload_dir = "uploadfile/";
    $new_file_name = "profile_" . $user_id . "." . $file_ext;
    $upload_path = $upload_dir . $new_file_name;
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $profile_pic = $upload_path;
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed."]);
        exit;
    }
}

// Prepare SQL Query
$sql = "UPDATE users SET name=?, email=?, phone=?, blood_group=?, location=?";
$params = [$name, $email, $phone, $blood_group, $location];

if (!empty($profile_pic)) {
    $sql .= ", profile_pic=?";
    $params[] = $profile_pic;
}

$sql .= " WHERE id=?";
$params[] = $user_id;

$stmt = $conn->prepare($sql);
$result = $stmt->execute($params);

if ($result) {
    echo json_encode(["status" => "success", "message" => "Profile updated successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update profile."]);
}
?>
