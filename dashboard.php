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

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Find Blood - Dashboard</title>
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
        display: flex;
        flex-direction: column;
        min-height: 100vh;
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

      .dashboard {
        display: flex;
        padding: 40px;
        gap: 20px;
      }

      .profile-card {
        width: 300px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
      }

      .content-section {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: center;
      }

      .profilePreview {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        margin-bottom: 10px;
      }

      .btn {
        background: linear-gradient(to right, #d32f2f, #ff6659);
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
      }

      .btn:hover {
        background: linear-gradient(to right, #ff6659, #d32f2f);
      }

      .features {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding: 30px;
        background-color: #ffffff;
      }

      .feature {
        width: 30%;
        text-align: center;
        margin: 20px 0;
        
      }

      .feature img{
        width: 149px;
        height: 149px;
        margin-bottom: 15px; 
      }

      footer {
        text-align: center;
        padding: 10px;
        background-color: #f44336;
        color: white;
        margin-top: 30px;
      }

    </style>
    
  </head>
  <body>
    <header>
      <div class="logo" style="display: flex; align-items: center; gap: 10px;">
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

    <section class="dashboard">
      <div class="profile-card">
      <img id="profilePreview" class="profilePreview" src="<?php echo (!empty($user['profile_pic']) && file_exists($user['profile_pic'])) ? $user['profile_pic'] : 'default.jpg'; ?>" alt="Profile Picture" />
        <h3>User Profile</h3><br>
        <p><strong>Name:</strong> <span id="name">Loading...</span></p>
        <p><strong>Email:</strong> <span id="email">Loading...</span></p>
        <p><strong>Blood Group:</strong> <span id="blood_group">Loading...</span></p>
        <p><strong>Phone:</strong> <span id="phone">Loading...</span></p> <br>
        <button class="btn" onclick="window.location.href='profileview.php'">View Profile</button>
    </div>
    
    <script>
  document.addEventListener("DOMContentLoaded", function () {
    fetch("getprofile.php")
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                document.getElementById("name").innerText = data.user.name;
                document.getElementById("email").innerText = data.user.email;
                document.getElementById("blood_group").innerText = data.user.bloodgroup;
                document.getElementById("phone").innerText = data.user.phoneno;
                console.log("Profile Pic URL:", data.user.profile_pic);

                // Set profile picture
                let profileImg = document.getElementById("profile-img");
                profileImg.src = data.user.profile_pic ? data.user.profile_pic : "uploadfile/default.jpg";

                // Handle broken image (fallback to default)
                profileImg.onerror = function () {
                    this.onerror = null; // Prevent infinite loop
                    this.src = "uploadfile/default.jpg";
                };
            } else {
                alert(data.message);
                window.location.href = "login.html";
            }
        })
        .catch(error => console.error("Error fetching profile:", error));
});



    </script>
    

      <div class="content-section">
        <div class="card">
          <h3>Blood Requests</h3>
          <p>Recent requests for blood donations.</p>
          <button class="btn" onclick="window.location.href='viewRequest.html'">View Requests</button>
        </div>

        <div class="card">
          <h3>Donor Statistics</h3>
          <p>Total Available Donors: 120</p>
          <button class="btn" onclick="window.location.href='finddonors.html'">Find Donors</button>
        </div>
        <div class="card">
            <h3>About us</h3>
            <p> Information about the blood donations</p>
            <button class="btn" onclick="window.location.href='aboutus.html'">About us</button>
          </div>
      </div>
    </section>

    <section id="features" class="features">
        <div class="feature">
          <img src="find.jpg" alt="Find Donors" class="image2" />
          <h3>Find Donors</h3>
          <p>Locate blood donors nearby quickly and easily.</p>
        </div>
        <div class="feature">
          <img src="secureE.jpg" alt="Secure Platform" class="image2" />
          <h3>Secure Platform</h3>
          <p>Your data is safe with us, ensuring privacy and security.</p>
        </div>
        <div class="feature">
          <img src="save.jpg" alt="Save Lives" class="image2" />
          <h3>Save Lives</h3>
          <p>Be a hero by donating blood and saving lives.</p>
        </div>
      </section>

    <footer>&copy; 2025 Find Blood | All Rights Reserved</footer>
  </body>
</html>
