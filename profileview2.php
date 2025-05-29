<?php
// Database connection
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "izumi", "blood_donation");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch logged-in user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email, phoneno, bloodgroup, location, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile | Blood Donation</title>
  <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        header {
            background-color: #ff4d4d;
            color: white;
            padding: 1rem 0;
            text-align: center;
        }

        header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 6px;
        background-color: #f44336;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      }

      header .logo {
        font-size: 24px;
        font-weight: bold;
      }

      nav {
        display: flex;
        gap: 15px;
      }

      nav a {
        text-decoration: none;
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        transition: background-color 0.3s;
      }

      nav a:hover {
        background-color: #b71c1c;
      }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #ff4d4d;
            margin-bottom: 20px;
        }

        .profile-container h1 {
            color: #ff4d4d;
            margin-bottom: 10px;
        }

        .profile-container p {
            font-size: 1.2rem;
            color: #333;
            margin: 5px 0;
        }

        .profile-container .edit-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #ff4d4d;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .profile-container .edit-btn:hover {
            background-color: #cc0000;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }

        footer p {
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<header>
  <div class="logo">
    <img src="logo.webp" alt="Blood Donation Logo" />
    <span>Blood Donation</span>
  </div>
  <nav>
    <a href="dashboard.php">Home</a>
    <a href="contact.html">Contact</a>
    <a href="aboutus.html">About</a>
  </nav>
</header>

<div class="profile-container">
<h1>My Profile</h1>
  
  <img src="<?php echo (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? $user['profile_pic'] : 'default.jpg'; ?>" alt="Profile Picture"/>

  <label class="label">Name:</label>
  <p><?php echo htmlspecialchars($user['name']); ?></p>

  <label class="label">Email:</label>
  <p><?php echo htmlspecialchars($user['email']); ?></p>

  <label class="label">Phone Number:</label>
  <p><?php echo htmlspecialchars($user['phoneno']); ?></p>

  <label class="label">Blood Group:</label>
  <p><?php echo htmlspecialchars($user['bloodgroup']); ?></p>

  <label class="label">Location:</label>
  <p><?php echo htmlspecialchars($user['location']); ?></p>

  <button type="button" class="update-btn" onclick="window.location.href='pro.php'">
    <i class="fa fa-pencil-alt"></i> Click To Update
  </button>
    </div>

</body>
</html>
