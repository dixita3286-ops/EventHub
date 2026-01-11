<?php
session_start();
$message = "";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $con = mysqli_connect("localhost", "root", "", "eventhub");
    if (!$con) die("Database connection failed.");

    // Escape inputs
    $name = mysqli_real_escape_string($con, trim($_POST['name']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));
    $roleInput = mysqli_real_escape_string($con, trim($_POST['role']));

    /* ---------------- BACK-END VALIDATION ---------------- */

    // Name validation
    if (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Invalid name. Only letters allowed.</div>";
    }
    // Email validation
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Invalid email format!</div>";
    }
    // Password validation
    elseif (strlen($password) < 6 || !preg_match("/[0-9]/", $password) || !preg_match("/[A-Za-z]/", $password)) {
        $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Password must be at least 6 characters and contain letters & numbers.</div>";
    }
    // Role validation
    elseif (!in_array($roleInput, ['student','organizer'])) {
        $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Invalid role selected.</div>";
    } 
    else {
        // Check if email exists
        $check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

        if ($check && mysqli_num_rows($check) > 0) {
            $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Email already exists!</div>";
        } else {
            // Insert data
            $q = "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$roleInput')";
            if (mysqli_query($con, $q)) {
                $message = "<div class='msg-box' style='background:#4cd137;color:#000;'>Registered Successfully! 
                            <a href='login.php' style='color:black;text-decoration:underline;'>Login</a></div>";
            } else {
                $message = "<div class='msg-box' style='background:#ff5555;color:#000;'>Error occurred.</div>";
            }
        }
    }

    mysqli_close($con);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>User Signup - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Parisienne&display=swap" rel="stylesheet">

<style>
/* ---------- BODY + BACKGROUND ---------- */
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    position:relative;
    overflow:hidden;
}

/* Floating circles */
body::before,body::after{
    content:"";
    position:absolute;
    width:380px;height:380px;border-radius:50%;
    filter:blur(100px);opacity:0.35;
    animation:float 8s infinite alternate ease-in-out;
}
body::before{background:#ff9900;top:-80px;left:-50px;}
body::after{background:#ff5500;bottom:-80px;right:-50px;}

@keyframes float{0%{transform:translateY(0);}100%{transform:translateY(40px);}}

/* NAVBAR */
.navbar{
    position:fixed;top:0;width:100%;
    background:rgba(0,0,0,0.88);
    padding:10px 25px;
    border-bottom:2px solid #ff9900;
    backdrop-filter:blur(8px);
    z-index:2000;
}
.navbar-left img{height:45px;border-radius:6px;}

.signup-box{
    width:380px;background:rgba(255,255,255,0.06);
    padding:45px 35px;border-radius:18px;
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 15px 35px rgba(0,0,0,0.4);
    text-align:center;margin-top:30px;
    animation:fadeIn .6s ease;
}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}

/* Heading */
.signup-box h2{
    font-family:'Parisienne',cursive;
    font-size:42px;color:#ffbb55;margin-bottom:25px;
    text-shadow:0 0 10px rgba(255,170,70,0.5);
}

/* Inputs */
label{display:block;text-align:left;color:#ddd;margin-bottom:6px;font-size:14px;}
input,select{
    width:100%;padding:12px;margin-bottom:18px;
    border-radius:10px;border:none;
    background:rgba(255,255,255,0.15);
    color:white;font-size:14px;
}
select option{
    background:#000 !important;
    color:#fff !important;
}
input:focus,select:focus{
    background:rgba(255,255,255,0.22);
    box-shadow:0 0 8px #ff9900;
}

.error-text{
    color:#ff5555;
    font-size:13px;
    margin-top:-14px;
    margin-bottom:10px;
    text-align:left;
}

/* Button */
button{
    width:100%;padding:12px;border-radius:10px;
    background:#ff9900;border:none;color:#000;
    font-size:17px;font-weight:600;
}
button:hover{background:#ff7700;}

/* Links */
.login-link{
    margin-top:14px;display:block;color:#ffcc66;
    font-weight:500;text-decoration:none;
}
.login-link:hover{text-decoration:underline;}

.msg-box{
    background:#ffcc00;color:#000;padding:10px;
    border-radius:10px;margin-bottom:15px;font-weight:600;
}
</style>
</head>

<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="uploads/images/logo.png">
    </div>
</div>

<div class="signup-box">
    <h2>User Signup</h2>

    <?php if(!empty($message)) echo $message; ?>

    <form method="post" onsubmit="return validateForm()">

        <label>Full Name</label>
        <input type="text" name="name" id="name">
        <div class="error-text" id="nameError"></div>

        <label>Email</label>
        <input type="email" name="email" id="email">
        <div class="error-text" id="emailError"></div>

        <label>Password</label>
        <input type="password" name="password" id="password">
        <div class="error-text" id="passError"></div>

        <label>Select Role</label>
        <select name="role" id="role">
            <option value="" disabled selected>-- Select Role --</option>
            <option value="student">Student</option>
            <option value="organizer">Organizer</option>
        </select>
        <div class="error-text" id="roleError"></div>

        <button type="submit">Sign Up</button>
    </form>

    <a href="login.php" class="login-link">Already have an account? Login</a>
</div>

<script>
function validateForm(){
    let valid = true;

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();
    let role = document.getElementById("role").value;

    // Clear errors
    document.getElementById("nameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("passError").innerHTML = "";
    document.getElementById("roleError").innerHTML = "";

    // Name validation
    if(!/^[A-Za-z ]+$/.test(name)){
        document.getElementById("nameError").innerHTML = "Enter a valid name (letters only).";
        valid = false;
    }

    // Email validation
    if(!/^\S+@\S+\.\S+$/.test(email)){
        document.getElementById("emailError").innerHTML = "Enter a valid email.";
        valid = false;
    }

    // Password validation
    if(password.length < 6 || !/[0-9]/.test(password) || !/[A-Za-z]/.test(password)){
        document.getElementById("passError").innerHTML = "Password must be 6+ characters with letters & numbers.";
        valid = false;
    }

    // Role validation
    if(role === ""){
        document.getElementById("roleError").innerHTML = "Please select a role.";
        valid = false;
    }

    return valid;
}
</script>

</body>
</html>
