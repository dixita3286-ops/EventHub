<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");
if (!$con) die("Database connection failed: " . mysqli_connect_error());

$user_id = intval($_GET['id']);
$query = "SELECT user_id, name, email, password FROM users WHERE user_id = $user_id";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email =  $_POST['email'];
    $password = $_POST['password'];

    $update_query = "UPDATE users SET 
        name='$name', 
        email='$email', 
        password='$password'
        WHERE user_id=$user_id";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('User updated successfully!'); window.location='manage_users.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit User - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: #0d0d0d;
    color: #fff;
    padding-top: 100px;
}

/* NAVBAR */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    background: #000;
    padding: 12px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px solid #ff9900;
    box-shadow: 0 3px 20px rgba(255,153,0,0.3);
    z-index: 2000;
}
.navbar-left img { height: 40px; border-radius: 6px; }
.navbar a { color: white; padding: 6px 10px; text-decoration: none; border-radius: 5px; }
.navbar a:hover, .navbar a.active { background: rgba(255,255,255,0.2); }

#hamburgerBtn {
    background:#000;
    border:1px solid rgba(255,255,255,0.2);
    padding:8px;
    border-radius:8px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* SLIDE MENU (copied EXACTLY from Home page) */
#sideMenu {
    position: fixed;
    top: 0;
    right: -300px;
    width: 300px;
    height: 100vh;
    background: rgba(0,0,0,0.97);
    z-index: 3000;
    transition: right 0.35s ease-in-out;
    box-shadow: -5px 0 20px rgba(0,0,0,0.4);
}

#sideMenu.show {
    right: 0;
}

.menu-header {
    padding: 18px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.25);
    display: flex;
    justify-content: flex-start;
    align-items: center;
}

.close-btn {
    background: none;
    border: none;
    cursor: pointer;
}

#sideMenu a {
    display: block;
    padding: 15px 22px;
    font-size: 17px;
    color: white;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    text-decoration: none;
}

#sideMenu a:hover {
    background: rgba(255,255,255,0.15);
}

/* FORM */
.form-container {
    width: 430px;
    margin: 40px auto;
    padding: 35px;
    border-radius: 16px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.15);
    box-shadow: 0 8px 26px rgba(0,0,0,0.45);
}

.form-container h1 {
    text-align: center;
    font-size: 36px;
    font-family: 'Parisienne', cursive;
    color: #ffcc66;
    text-shadow: 0 0 8px rgba(255,153,0,0.6);
    margin-bottom: 25px;
}

label {
    margin-top: 15px;
    color: #ffcc88;
    font-weight: 500;
    font-size: 15px;
}

input {
    width: 100%;
    padding: 12px;
    margin-top: 6px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.12);
    color: #fff;
    font-size: 15px;
}

.btn {
    width: 100%;
    padding: 12px;
    margin-top: 28px;
    background: #ff9900;
    border: none;
    border-radius: 8px;
    color: #111;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}
.btn:hover { background:#ffaa22; }

.back-btn {
    text-align:center;
    display:block;
    margin-top:18px;
    color:#ff9900;
    text-decoration:none;
}
.back-btn:hover { text-decoration:underline; }
</style>
</head>

<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="../uploads/images/logo.png">
    </div>

    
</div>

<!-- SLIDE MENU -->


<div class="form-container">
    <h1>Edit User</h1>

    <form method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label>Password</label>
        <input type="text" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>

        <button type="submit" class="btn">Update User</button>
    </form>

    <a href="manage_users.php" class="back-btn">Back</a>
</div>

<script>
// SLIDE MENU JS (EXACT COPY OF HOME PAGE)
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

btn.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click", () => {
    menu.classList.remove("show");
});

document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($con); ?>
