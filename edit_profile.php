<?php
session_start();

include 'db.php'; // Database connection
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data (including profile picture)
$stmt = $conn->prepare("SELECT profile_pic FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$current_profile_pic = $user['profile_pic'] ?? 'uploadfile/default.jpg';

$name = !empty($_POST['username']) ? trim($_POST['username']) : null;
$email = !empty($_POST['email']) ? trim($_POST['email']) : null;
$phoneno = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
$bloodgroup = !empty($_POST['blood_group']) ? trim($_POST['blood_group']) : null;
$location = !empty($_POST['location']) ? trim($_POST['location']) : null;
$profile_pic = $current_profile_pic; // Default to existing profile picture

// Handle profile picture upload
if (!empty($_FILES['profile_pic']['name']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_name = basename($_FILES['profile_pic']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Allowed: JPG, JPEG, PNG"]);
        exit;
    }

    if ($_FILES['profile_pic']['size'] > 4 * 1024 * 1024) { 
        echo json_encode(["status" => "error", "message" => "File too large. Max size: 4MB"]);
        exit;
    }

    $upload_dir = 'uploadfile/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Only delete old profile picture if it's NOT the default one
    if ($current_profile_pic !== 'uploadfile/default.jpg' && file_exists($current_profile_pic)) {
        unlink($current_profile_pic);
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

// Prepare update query
$update_fields = [];
$params = [];

if ($name !== null) {
    $update_fields[] = "name = :name";
    $params[':name'] = $name;
}
if ($email !== null) {
    $update_fields[] = "email = :email";
    $params[':email'] = $email;
}
if ($phoneno !== null) {
    $update_fields[] = "phoneno = :phoneno";
    $params[':phoneno'] = $phoneno;
}
if ($bloodgroup !== null) {
    $update_fields[] = "bloodgroup = :bloodgroup";
    $params[':bloodgroup'] = $bloodgroup;
}
if ($location !== null) {
    $update_fields[] = "location = :location";
    $params[':location'] = $location;
}
if ($profile_pic !== $current_profile_pic) {
    $update_fields[] = "profile_pic = :profile_pic";
    $params[':profile_pic'] = $profile_pic;
}

// Check if any updates were made
if (empty($update_fields)) {
    echo json_encode(["status" => "error", "message" => "No changes detected."]);
    exit;
}

// Execute update query
$query = "UPDATE users SET " . implode(", ", $update_fields) . " WHERE id = :id";
$params[':id'] = $user_id;

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully!", "profile_pic" => $profile_pic]);
    } else {
        echo json_encode(["status" => "error", "message" => "Profile update failed. No changes were made."]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}

$conn = null;
?>
