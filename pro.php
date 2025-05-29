<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "blood_donation");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch user data
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
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profile | Blood Donation</title>
  <style>
    * {
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
      display: flex;
      align-items: center;
      gap: 10px;
    }

    header .logo img {
      height: 50px;
      width: 70px;
      border-radius: 50%;
      border: 2px solid white;
      background-color: #e0f7fa;
      object-fit: cover;
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
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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

    .profile-container h1 {
      color: #ff4d4d;
      margin-bottom: 15px;
    }

    .custom-file-upload {
      display: inline-block;
      padding: 10px 20px;
      background-color: #ff4d4d;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      margin-top: 5px;
    }

    .custom-file-upload:hover {
      background-color: #d32f2f;
    }

    #profile-pic {
      display: none;
    }

    .file-name {
      font-size: 14px;
      color: #555;
      display: block;
      margin-top: 5px;
    }

    .profile-container input,
    .profile-container select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }

    .profile-container input:focus,
    .profile-container select:focus {
      border-color: #ff4d4d;
      outline: none;
      box-shadow: 0 0 5px rgba(255, 77, 77, 0.5);
    }

    .profile-container button {
      background: linear-gradient(to right, #ff4d4d, #d32f2f);
      font-size: 1rem;
      color: white;
      padding: 12px;
      border-radius: 5px;
      border: none;
      cursor: pointer;
      width: 100%;
      font-weight: bold;
      transition: all 0.3s ease-in-out;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
    }

    .profile-container button:hover {
      background: linear-gradient(to right, #d32f2f, #ff6659);
      transform: scale(1.05);
    }

    footer {
      text-align: center;
      padding: 15px;
      background: linear-gradient(to right, #d32f2f, #ff6659);
      color: white;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
      margin-top: auto;
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
    <h1>User Profile</h1>


    <form id="editProfileForm" action="edit_profile.php" method="POST" enctype="multipart/form-data">

      <img id="profilePreview" src="<?php echo (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? $user['profile_pic'] : 'default.jpg'; ?>" alt="Profile Picture" />
      <!-- <label for="profile-pic" class="custom-file-upload">Choose File</label> -->
      <input type="file" id="profile_pic" name="profile_pic" accept="image/*" onchange="previewImage(event)">

      <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

      <label for="name">Name:</label>
      <input type="text" id="name" name="username" value="<?php echo $user['name']; ?>" required />

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required />

      <label for="phone">Phone Number:</label>
      <input type="text" id="phone" name="phone" value="<?php echo $user['phoneno']; ?>" required />

      <label for="blood_group">Blood Group:</label>
      <select id="blood_group" name="blood_group" required>
        <option value="" disabled>Select Blood Group</option>
        <option value="A+" <?php echo ($user['bloodgroup'] == 'A+') ? 'selected' : ''; ?>>A+</option>
        <option value="A-" <?php echo ($user['bloodgroup'] == 'A-') ? 'selected' : ''; ?>>A-</option>
        <option value="B+" <?php echo ($user['bloodgroup'] == 'B+') ? 'selected' : ''; ?>>B+</option>
        <option value="B-" <?php echo ($user['bloodgroup'] == 'B-') ? 'selected' : ''; ?>>B-</option>
        <option value="AB+" <?php echo ($user['bloodgroup'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
        <option value="AB-" <?php echo ($user['bloodgroup'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
        <option value="O+" <?php echo ($user['bloodgroup'] == 'O+') ? 'selected' : ''; ?>>O+</option>
        <option value="O-" <?php echo ($user['bloodgroup'] == 'O-') ? 'selected' : ''; ?>>O-</option>
      </select>



      <label for="location">Location:</label>
      <input type="text" id="location" name="location" value="<?php echo $user['location']; ?>" required />

      <button type="submit">Update Profile</button>
    </form>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      fetch("edit_profile.php")
        .then(response => response.json())
        .then(data => {
          if (data.status === "error") {
            alert(data.message);
            return;
          }

          document.getElementById("name").value = data.name || "";
          document.getElementById("email").value = data.email || "";
          document.getElementById("phone").value = data.phoneno || "";
          document.getElementById("blood_group").value = data.bloodgroup || "";
          document.getElementById("location").value = data.location || "";

          if (data.profile_pic) {
            document.getElementById("profilePreview").src = data.profile_pic;
          }
        })
        .catch(error => console.error("Error fetching profile:", error));

      document.getElementById("editProfileForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);

        fetch("edit_profile.php", {
            method: "POST",
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            alert(data.message);
            if (data.status === "success") {
              location.reload();
            }
          })
          .catch(error => console.error("Error updating profile:", error));
      });
    });

    function previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        document.getElementById("profilePreview").src = URL.createObjectURL(file);
      }
    }
  </script>

</body>

</html>