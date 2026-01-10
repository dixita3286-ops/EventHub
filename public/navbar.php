<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
/* ===============================
   LUXURY PREMIUM NAVBAR
   =============================== */

.navbar{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    padding:22px 64px;
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;
    z-index:100;

    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.85),
        rgba(0,0,0,0.65)
    );
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);

    box-shadow:
        0 10px 30px rgba(0,0,0,0.45),
        inset 0 -1px rgba(255,183,77,0.15);
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:700;
    letter-spacing:1.2px;
    color:#fff;
    text-shadow: 0 0 18px rgba(255,183,77,0.25);
}

/* CENTER MENU */
.menu{
    text-align:center;
}

.menu a{
    margin:0 20px;
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    font-weight:500;
    font-size:15px;
    position:relative;
    transition:all 0.35s ease;
}

.menu a::after{
    content:"";
    position:absolute;
    left:50%;
    bottom:-6px;
    width:0;
    height:2px;
    background:linear-gradient(90deg,#ffb347,#ff7a18);
    transition:all 0.35s ease;
    transform:translateX(-50%);
    border-radius:2px;
}

.menu a:hover{
    color:#fff;
}

.menu a:hover::after{
    width:70%;
}

/* RIGHT */
.actions{
    position:relative;
}

/* LOGIN BUTTON */
.login-btn{
    padding:10px 26px;
    border-radius:30px;
    border:1px solid rgba(255,183,77,0.6);
    color:#ffb347;
    text-decoration:none;
    font-weight:600;

    background: linear-gradient(
        135deg,
        rgba(255,183,77,0.12),
        rgba(255,122,24,0.08)
    );

    backdrop-filter: blur(10px);
    transition:all 0.35s ease;

    box-shadow:
        0 0 18px rgba(255,183,77,0.25),
        inset 0 0 10px rgba(255,255,255,0.05);
}

.login-btn:hover{
    color:#000;
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    box-shadow:0 0 35px rgba(255,183,77,0.7);
}

/* ===============================
   HAMBURGER ICON (CSS BASED – FIXED)
   =============================== */

.hamburger{
    width:26px;
    height:20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    cursor:pointer;
}

.hamburger span{
    height:3px;
    width:100%;
    background:#fff;
    border-radius:2px;
    box-shadow:0 0 6px rgba(255,183,77,0.35);
    transition:0.3s;
}

.hamburger:hover span{
    background:#ffb347;
}

/* DROPDOWN */
.dropdown{
    display:none;
    position:absolute;
    right:0;
    top:46px;
    min-width:220px;
    padding:12px 0;

    background: linear-gradient(
        to bottom,
        rgba(0,0,0,0.92),
        rgba(15,15,15,0.88)
    );

    border-radius:16px;
    backdrop-filter: blur(16px);

    box-shadow:
        0 25px 60px rgba(0,0,0,0.7),
        inset 0 0 0 1px rgba(255,183,77,0.15);
}

.dropdown a{
    display:block;
    padding:12px 20px;
    color:#eee;
    text-decoration:none;
    font-size:14px;
    transition:all 0.3s ease;
}

.dropdown a:hover{
    background:linear-gradient(
        90deg,
        rgba(255,183,77,0.18),
        rgba(255,122,24,0.08)
    );
    color:#ffb347;
}
</style>

<div class="navbar">

    <!-- LEFT -->
    <div class="logo">EventHub</div>

    <!-- CENTER -->
    <div class="menu">
        <?php if(!isset($_SESSION['role'])){ ?>
            <a href="/EventHub/index.php">Home</a>
        <?php } elseif($_SESSION['role']=='student'){ ?>
            <a href="/EventHub/student/student_home.php">Home</a>
        <?php } elseif($_SESSION['role']=='organizer'){ ?>
            <a href="/EventHub/organizer/organizer_home.php">Home</a>
        <?php } elseif($_SESSION['role']=='admin'){ ?>
            <a href="/EventHub/admin/admin_home.php">Home</a>
        <?php } ?>

        <?php if(!isset($_SESSION['role'])){ ?>
            <a href="/EventHub/events.php">Events</a>
        <?php } elseif($_SESSION['role']=='student'){ ?>
            <a href="/EventHub/student/student_events.php">Events</a>
        <?php } elseif($_SESSION['role']=='organizer'){ ?>
            <a href="/EventHub/organizer/my_events.php">Events</a>
        <?php } elseif($_SESSION['role']=='admin'){ ?>
            <a href="/EventHub/admin/admin_events.php">Events</a>
        <?php } ?>

        <a href="/EventHub/about.php">About</a>
    </div>

    <!-- RIGHT -->
    <div class="actions">

        <?php if(!isset($_SESSION['role'])){ ?>
            <a href="/EventHub/login.php" class="login-btn">Login</a>
        <?php } else { ?>

            <!-- FIXED HAMBURGER -->
            <div class="hamburger" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>

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
