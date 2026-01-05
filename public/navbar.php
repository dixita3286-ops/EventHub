<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
/* NAVBAR */
.navbar{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    padding:20px 60px;
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;
    z-index:100;
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:700;
    letter-spacing:1px;
    color:#fff;
}

/* CENTER MENU */
.menu{
    text-align:center;
}

.menu a{
    margin:0 18px;
    color:#ddd;
    text-decoration:none;
    font-weight:500;
}

.menu a:hover{
    color:#ff7a18;
}

/* RIGHT AREA */
.actions{
    position:relative;
}

/* LOGIN BUTTON */
.login-btn{
    padding:10px 22px;
    border-radius:25px;
    border:1px solid rgba(255,255,255,0.4);
    color:#fff;
    text-decoration:none;
    backdrop-filter: blur(8px);
}

/* HAMBURGER */
.hamburger{
    font-size:26px;
    cursor:pointer;
    color:#fff;
}

/* DROPDOWN */
.dropdown{
    display:none;
    position:absolute;
    right:0;
    top:40px;
    background:rgba(0,0,0,0.85);
    border-radius:12px;
    min-width:200px;
    padding:10px 0;
}

.dropdown a{
    display:block;
    padding:10px 18px;
    color:#fff;
    text-decoration:none;
    font-size:14px;
}

.dropdown a:hover{
    background:rgba(255,255,255,0.1);
}
</style>

<div class="navbar">

    <!-- LEFT -->
    <div class="logo">EventHub</div>

    <!-- CENTER -->
    <div class="menu">
        <a href="/EventHub/index.php">Home</a>
        <a href="/EventHub/events.php">Events</a>
        <a href="/EventHub/about.php">About</a>
    </div>

    <!-- RIGHT -->
    <div class="actions">

        <!-- GUEST -->
        <?php if(!isset($_SESSION['role'])){ ?>
            <a href="/EventHub/login.php" class="login-btn">Login</a>

        <?php } else { ?>

            <!-- LOGGED IN -->
            <div class="hamburger" onclick="toggleMenu()">☰</div>

            <div class="dropdown" id="menuBox">

                <?php if($_SESSION['role']=='student'){ ?>
                    <a href="/EventHub/student/student_home.php">Home</a>
                    <a href="/EventHub/student/my_registrations.php">My Registrations</a>
                <?php } ?>

                <?php if($_SESSION['role']=='organizer'){ ?>
                    <a href="/EventHub/organizer/organizer_home.php">Home</a>
                    <a href="/EventHub/organizer/create_event.php">Create Events</a>
                <?php } ?>

                <?php if($_SESSION['role']=='admin'){ ?>
                    <a href="/EventHub/admin/manage_users.php">Manage Users</a>
                    <a href="/EventHub/admin/manage_events.php">Event Proposals</a>
                <?php } ?>

                <a href="/EventHub/logout.php">Logout</a>
            </div>

        <?php } ?>

    </div>

</div>

<script>
function toggleMenu(){
    var box = document.getElementById("menuBox");
    box.style.display = (box.style.display === "block") ? "none" : "block";
}
</script>
