<?php
session_start();

// DB Connection (use your InfinityFree MySQL credentials here)
$servername = "sql108.infinityfree.com"; // Corrected server name
$username = "if0_37587887"; // Correct username
$password = "cTOvQVmKy5FMKU"; // Correct password
$dbname = "if0_37587887_mealmap"; // Correct DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle login and registration logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle login
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Sanitize user input to prevent SQL injection
        $email = $conn->real_escape_string($email);
        $password = $conn->real_escape_string($password);
        
        // Query to check if the email exists
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user; // Store user info in session
                header("Location: ".$_SERVER['PHP_SELF']); // Refresh page to show user info
                exit;
            } else {
                echo "<script>alert('Invalid email or password.');</script>";
            }
        } else {
            echo "<script>alert('Invalid email or password.');</script>";
        }
    }

    // Handle registration
    elseif (isset($_POST['register'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $fitness_goal = $_POST['fitness_goal'];
        $dob = $_POST['dob']; // Date of birth
        
        // Sanitize user input to prevent SQL injection
        $name = $conn->real_escape_string($name);
        $email = $conn->real_escape_string($email);
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
        $age = $conn->real_escape_string($age);
        $gender = $conn->real_escape_string($gender);
        $fitness_goal = $conn->real_escape_string($fitness_goal);
        $dob = $conn->real_escape_string($dob);
        
        // Query to check if the email already exists
        $sql_check_email = "SELECT * FROM users WHERE email='$email'";
        $result_check = $conn->query($sql_check_email);
        
        if ($result_check->num_rows > 0) {
            echo "<script>alert('Email already exists. Please use a different email.');</script>";
        } else {
            // Insert new user record into the database
            $sql = "INSERT INTO users (name, email, password, age, gender, fitness_goal, dob) 
                    VALUES ('$name', '$email', '$password', '$age', '$gender', '$fitness_goal', '$dob')";
            
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Registration successful! Please login now.');</script>";
            } else {
                echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
            }
        }
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ".$_SERVER['PHP_SELF']); // Refresh page to hide user info
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealMap</title>
    <style>
        /* General Reset */
        body, h1, h2, a, button, ul, li {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(90deg, #FF7F50, #FFD700, #32CD32);
            padding: 10px 20px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            display: flex;
            justify-content: center; /* Center the content */
            align-items: center;
            color: white;
            font-size: 18px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap; /* Allow wrapping of elements on smaller screens */
        }

        .top-bar .brand {
            font-size: 24px;
            font-weight: bold;
            margin-right: 20px;
        }

        /* Menu and Links */
        .top-bar .nav {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
        }

        .top-bar .nav ul {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
            flex-wrap: wrap;
            justify-content: center;
        }

        .top-bar .nav ul li {
            margin-right: 20px;
        }

        .top-bar .nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }

        .top-bar .nav ul li a:hover {
            text-decoration: underline;
        }

        /* Search bar */
        .top-bar .search-bar {
            display: flex;
            align-items: center;
            background-color: white;
            border-radius: 5px;
            padding: 5px 10px;
            margin-right: 20px;
            justify-content: center;
        }

        .top-bar .search-bar input {
            border: none;
            outline: none;
            padding: 5px;
            width: 200px;
            font-size: 14px;
        }

        .top-bar .search-bar button {
            border: none;
            background-color: #FF7F50;
            color: white;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .top-bar .search-bar button:hover {
            background-color: #FF6347;
        }

        /* User Menu */
        .top-bar .user-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
        }

        .top-bar .user-menu button {
            margin-left: 10px;
            background: #FF7F50;
            color: white;
            padding: 5px 10px;
            font-size: 16px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .top-bar .user-menu button:hover {
            background-color: #FF6347;
        }

        /* Overlay and Panel */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        .panel {
            background: white;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
            text-align: center;
        }

        .panel h2 {
            margin-bottom: 20px;
        }

        .panel form {
            display: flex;
            flex-direction: column;
        }

        .panel form input {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .panel form button {
            margin-top: 10px;
            padding: 10px;
            border: none;
            background: #FF7F50;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .panel form button:hover {
            background-color: #FF6347;
        }

        .panel .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: #FF6347;
            font-size: 20px;
            cursor: pointer;
        }

        .panel .close-btn:hover {
            color: #FF7F50;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                text-align: center;
            }

            .top-bar .nav ul {
                flex-direction: column;
                margin-bottom: 10px;
            }

            .top-bar .search-bar {
                width: 100%;
                margin: 10px 0;
            }

            .top-bar .user-menu {
                flex-direction: column;
                margin-top: 20px;
            }

            .top-bar .user-menu button {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="brand">MealMap</div>
        <div class="nav">
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Recipes</a></li>
                <li><a href="#">Meals</a></li>
                <li><a href="#">Diet Plans</a></li>
            </ul>
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <button>Search</button>
            </div>
        </div>
        <div class="user-menu">
            <?php
            if (isset($_SESSION['user'])) {
                // Display welcome message and logout button
                echo "<span>Welcome, " . $_SESSION['user']['name'] . "!</span>";
                echo "<a href='?logout=true'><button>Logout</button></a>";
            } else {
                // Display login and register buttons
                echo "<button onclick='toggleLoginPanel(1)'>Login</button>";
                echo "<button onclick='toggleLoginPanel(2)'>Register</button>";
            }
            ?>
        </div>
    </div>

    <!-- Login Panel -->
    <div class="overlay" id="login-panel">
        <div class="panel" id="login-form">
            <button class="close-btn" onclick="closeLoginPanel()">X</button>
            <h2>Login</h2>
            <form method="POST" action="">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <a class="forgot-password" href="#">Forgot Password?</a>
        </div>

        <!-- Registration Form -->
        <div class="panel" id="register-form" style="display: none;">
            <button class="close-btn" onclick="closeLoginPanel()">X</button>
            <h2>Register</h2>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="age" placeholder="Age" required>
                  <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <input type="text" name="fitness_goal" placeholder="Fitness Goal" required>
                <input type="date" name="dob" placeholder="Date of Birth" required>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
    </div>

    <script>
        // Toggle between login and registration forms
        function toggleLoginPanel(formType) {
            document.getElementById('login-form').style.display = (formType === 1) ? 'block' : 'none';
            document.getElementById('register-form').style.display = (formType === 2) ? 'block' : 'none';
            document.getElementById('login-panel').style.display = 'flex';
        }

        // Close the login panel
        function closeLoginPanel() {
            document.getElementById('login-panel').style.display = 'none';
        }
    </script>
</body>
</html>
