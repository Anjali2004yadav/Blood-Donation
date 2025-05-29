<?php
header("Content-Type: application/json"); // Set response as JSON

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed!"]);
    exit();
}

// Validate and sanitize input
function clean_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$name = clean_input($_POST['name']);
$email = clean_input($_POST['email']);
$phoneno = clean_input($_POST['phone']);
$bloodgroup = clean_input($_POST['blood_group']);
$location = clean_input($_POST['location']);
$password = $_POST['password']; // Store password as plain text

// Basic validation
if (empty($name) || empty($email) || empty($phoneno) || empty($bloodgroup) || empty($location) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "All fields are required!"]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => "error", "message" => "Invalid email format!"]);
    exit();
}

if (!preg_match("/^[0-9]{10}$/", $phoneno)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number!"]);
    exit();
}

// Check if email already exists
$checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    echo json_encode([
        "status" => "error", 
        "message" => "Email already registered! Redirecting to login...",
        "redirect" => "login.html"
    ]);
    $checkEmail->close();
    $conn->close();
    exit();
}

// Insert new user (without password hashing)
$stmt = $conn->prepare("INSERT INTO users (name, email, phoneno, bloodgroup, location, password) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $name, $email, $phoneno, $bloodgroup, $location, $password);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success", 
        "message" => "Registration successful! Redirecting...",
        "redirect" => "login.html"
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Something went wrong! Please try again."]);
}

// Close statements and connection
$stmt->close();
$checkEmail->close();
$conn->close();
?>
