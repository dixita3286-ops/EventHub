<?php
session_start();

$error = "";
$msg   = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* ================= DB ================= */
    $con = mysqli_connect("localhost", "root", "", "eventhub");
    if (!$con) {
        die("Database connection failed");
    }

    /* ================= SAFE QUERY ================= */
    $stmt = mysqli_prepare($con, "SELECT user_id, name, email, password, role FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        /* ================= PASSWORD CHECK (PHP 5 SAFE) ================= */
        if ($password === $user['password']) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/admin_home.php");
            } elseif ($user['role'] === 'organizer') {
                header("Location: organizer/organizer_home.php");
            } else {
                header("Location: student/student_home.php");
            }
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "Invalid email!";
    }

    mysqli_stmt_close($stmt);
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
}
.login-box{
    width:380px;
    background:rgba(255,255,255,0.06);
    padding:45px 35px;
    border-radius:18px;
    text-align:center;
}
.login-box h2{
    font-family:'Parisienne',cursive;
    font-size:42px;
    color:#ffbb55;
    margin-bottom:25px;
}
label{display:block;text-align:left;color:#ddd;margin-bottom:6px;}
input{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border-radius:10px;
    border:none;
}
button{
    width:100%;
    padding:12px;
    border-radius:10px;
    background:#ff9900;
    border:none;
    font-weight:600;
}
.msg-box{
    background:#ffcc00;
    color:#000;
    padding:10px;
    margin-bottom:15px;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>User Login</h2>

    <?php if (!empty($msg)): ?>
        <div class="msg-box"><?php echo $msg; ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p style="color:#ff6363;margin-bottom:12px;font-weight:600;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>

    <a href="signup.php" style="color:#ffcc66">Don't have an account? Sign Up</a>
</div>

</body>
</html>
