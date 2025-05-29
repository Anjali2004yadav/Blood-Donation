<?php
// Database connection
session_start();

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "blood_donation");

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    *  {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
      }

      body {
        background: linear-gradient(to bottom, #e0f7fa, #ffffff);
        overflow-x: hidden;
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
      max-width: 500px;
      margin: 50px auto;
      padding: 30px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
      text-align: center;
      margin-bottom: 50px;
      
    }
    .profile-container img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 3px solid #ff4d4d;
      margin-bottom: 15px;
      object-fit: cover;
    }
    .profile-container input {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
    
      margin-bottom: 20px;
      background-color: #f8f8f8;
    }
    .edit-icon {
      font-size: 20px;
      color: #f44336;
      margin-left: 10px;
      cursor: pointer;
      text-decoration: none;
    }
    .edit-icon:hover {
      color: #b71c1c;
    }
    .update-btn {
    display: inline-block;
    margin-top: 15px;
   padding: 12px 25px;
   background: #d60000;
    color: white;
   text-decoration: none;
   border-radius: 6px;
    font-weight: bold;
     transition: background 0.3s, transform 0.2s;
    }
   
    .update-btn:hover {
      background: #b30000;
       transform: scale(1.1);
    }

    footer {
        text-align: center;
        padding: 10px;
        background-color: #f44336;
        color: white;
        margin-top: 20px;
      }
  </style>
</head>
<body>

<header>
<div class="logo" style="display: flex; align-items: center; gap: 10px">
        <img
          src="logo.webp"
          alt="Blood Donation Logo"
          style="
            height: 50px;
            width: 70px;
            border-radius: 50%;
            border: 2px solid white;
            background-color: #e0f7fa;
            object-fit: cover;
          "
        />
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

  <br/>
  <label>Name:</label>
  <input type="text" value="<?php echo $user['name']; ?>" readonly/>

  <label>Email:</label>
  <input type="email" value="<?php echo $user['email']; ?>" readonly/>

  <label>Phone Number:</label>
  <input type="text" value="<?php echo $user['phoneno']; ?>" readonly/>

  <label>Blood Group:</label>
  <input type="text" value="<?php echo $user['bloodgroup']; ?>" readonly/>

  <label>Location:</label>
  <input type="text" value="<?php echo $user['location']; ?>" readonly/>

  <button type="button" class="update-btn" onclick="window.location.href='pro.php'">
    <i class="fa fa-pencil-alt"></i> Click To Update
</button>

</div>
<footer>&copy; 2025 Blood Donation | All Rights Reserved</footer>
</body>
</html>
