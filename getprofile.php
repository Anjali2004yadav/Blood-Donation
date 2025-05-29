<?php
session_start();
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details including profile picture
$sql = "SELECT name, email, phoneno, bloodgroup, location, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    // Set default profile picture if not set
    if (empty($user['profile_pic']) || !file_exists("uploads/" . $user['profile_pic'])) {
        $user['profile_pic'] = "uploads/default.jpg";  // Fallback if missing
    } else {
        $user['profile_pic'] = "uploads/" . $user['profile_pic'];
    }

    // Log the profile picture path for debugging
    error_log("Profile Pic Path: " . $user['profile_pic']);

    echo json_encode(["status" => "success", "user" => $user]);
} else {
    error_log("User not found for ID: " . $user_id); // Log if user not found
    echo json_encode(["status" => "error", "message" => "User not found."]);
}


$stmt->close();
$conn->close();
?>
