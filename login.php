<?php
session_start();
$error = "";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

$msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $con = mysqli_connect("localhost", "root", "", "eventhub");
    if (!$con) die("Database connection failed: " . mysqli_connect_error());

    $res = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");

    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);

        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            if ($user['role'] == 'admin') header("Location: admin/admin_home.php");
            elseif ($user['role'] == 'organizer') header("Location: organizer/organizer_home.php");
            else header("Location: student/student_home.php");

            exit();
        } else $error = "Invalid Password!";
    } else $error = "Invalid Email!";

    mysqli_close($con);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Login - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Parisienne&display=swap" rel="stylesheet">

<style>
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

/* Background glow */
body::before,body::after{
    content:"";
    position:absolute;
    width:380px;
    height:380px;
    border-radius:50%;
    filter:blur(100px);
    opacity:0.35;
    animation:float 8s infinite alternate ease-in-out;
}
body::before{background:#ff9900;top:-80px;left:-50px;}
body::after{background:#ff5500;bottom:-80px;right:-50px;}
@keyframes float{0%{transform:translateY(0)}100%{transform:translateY(40px)}}

/* LOGIN BOX */
.login-box{
    width:380px;
    background:rgba(255,255,255,0.06);
    padding:45px 35px;
    border-radius:18px;
    backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,0.15);
    box-shadow:0 15px 35px rgba(0,0,0,0.4);
    position:relative;
    text-align:center;

    margin-top:30px; /* ↓ Reduced gap from navbar */
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.login-box h2{
    font-family:'Parisienne',cursive;
    font-size:42px;
    color:#ffbb55;
    margin-bottom:25px;
    text-shadow:0 0 10px rgba(255,170,70,0.5);
}

label{
    display:block;
    text-align:left;
    color:#ddd;
    margin-bottom:6px;
    font-size:14px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border-radius:10px;
    border:none;
    background:rgba(255,255,255,0.15);
    color:white;
    font-size:14px;
}
input:focus{
    background:rgba(255,255,255,0.22);
    box-shadow:0 0 8px #ff9900;
}

button{
    width:100%;
    padding:12px;
    border-radius:10px;
    background:#ff9900;
    border:none;
    color:#000;
    font-size:17px;
    font-weight:600;
    transition:0.3s;
}
button:hover{
    background:#ff7700;
    transform:translateY(-2px);
}

.signup-link{
    margin-top:14px;
    display:block;
    text-decoration:none;
    color:#ffcc66;
    font-weight:500;
}
.signup-link:hover{text-decoration:underline;}

.msg-box{
    background:#ffcc00;
    color:#000;
    padding:10px;
    margin-bottom:15px;
    border-radius:10px;
    font-weight:600;
    font-size:14px;
}
</style>
</head>

<body>
<!-- LOGIN CARD -->
<div class="login-box">
    <h2>User Login</h2>

    <?php if (!empty($msg)): ?>
        <div class="msg-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color:#ff6363;margin-bottom:10px;font-weight:600;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email">

        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter your password">

        <button type="submit">Login</button>
    </form>

    <a href="signup.php" class="signup-link">Don't have an account? Sign Up</a>
</div>

<script>
const hb=document.getElementById("hamburgerBtn");
const menu=document.getElementById("sideMenu");
const closeBtn=document.getElementById("closeMenu");

document.addEventListener("click",(e)=>{
    if(closeBtn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>