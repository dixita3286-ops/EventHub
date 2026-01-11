<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "eventhub");

$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

/* ================= NOTIFICATION COUNT (STUDENT ONLY) ================= */
$notifCount = 0;
if ($conn && $user_id && $role === 'student') {
    $q = mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total 
         FROM notifications 
         WHERE user_id=$user_id AND is_read=0"
    );
    if ($q) {
        $d = mysqli_fetch_assoc($q);
        $notifCount = (int)$d['total'];
    }
}
?>

<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

<!-- 🔊 NOTIFICATION SOUND -->
<audio id="notifSound" preload="auto">
    <source src="/EventHub/uploads/sounds/notification.wav" type="audio/mpeg">
</audio>

<style>
/* ================= NAVBAR ================= */
.navbar{
    position:absolute;
    top:0; left:0;
    width:100%;
    padding:22px 64px;
    display:grid;
    grid-template-columns:auto 1fr auto;
    align-items:center;
    z-index:100;
    background:linear-gradient(to bottom,rgba(0,0,0,.9),rgba(0,0,0,.7));
    backdrop-filter:blur(14px);
    box-shadow:
        0 10px 40px rgba(0,0,0,.6),
        inset 0 -1px rgba(255,183,77,.25);
}

/* LOGO */
.logo{
    font-size:22px;
    font-weight:700;
    letter-spacing:1.4px;
    color:#fff;
    text-shadow:0 0 22px rgba(255,183,77,.35);
}

/* MENU */
.menu{text-align:center;}
.menu a{
    margin:0 22px;
    color:rgba(255,255,255,.75);
    text-decoration:none;
    font-weight:500;
    font-size:15px;
    position:relative;
    transition:.3s;
}
.menu a::after{
    content:"";
    position:absolute;
    left:50%;
    bottom:-6px;
    width:0;
    height:2px;
    background:linear-gradient(90deg,#ffb347,#ff7a18);
    transform:translateX(-50%);
    transition:.3s;
}
.menu a:hover{color:#fff}
.menu a:hover::after{width:70%}
/* ================= HAMBURGER MENU ================= */
.hamburger{
    width:42px;
    height:42px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:6px;
    cursor:pointer;
    border-radius:50%;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,183,77,.55);
    box-shadow:0 0 18px rgba(255,183,77,.25);
    transition:.3s;
}

.hamburger span{
    width:20px;
    height:2px;
    background:#ffb347;
    border-radius:2px;
    transition:.3s;
}

.hamburger:hover{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    box-shadow:0 0 35px rgba(255,183,77,.9);
}

.hamburger:hover span{
    background:#000;
}

/* RIGHT */
.actions{
    display:flex;
    align-items:center;
    gap:18px;
    position:relative; /* 🔥 ADD THIS */
}

/* ICONS */
.nav-icon{
    position:relative;
    width:42px;
    height:42px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,183,77,.55);
    cursor:pointer;
    transition:.3s;
    box-shadow:0 0 18px rgba(255,183,77,.25);
}
.nav-icon i{
    color:#ffb347;
    font-size:17px;
}
.nav-icon:hover{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    box-shadow:0 0 35px rgba(255,183,77,.9);
}
.nav-icon:hover i{color:#000}

/* 🔔 SHAKE ANIMATION */
@keyframes bellShake{
    0%{transform:rotate(0)}
    25%{transform:rotate(10deg)}
    50%{transform:rotate(-10deg)}
    75%{transform:rotate(6deg)}
    100%{transform:rotate(0)}
}
.bell-animate i{
    animation:bellShake .8s ease-in-out infinite;
}
/* 🔐 LOGIN BUTTON (PILL STYLE) */
.login-btn{
    padding:12px 36px;
    border-radius:30px;
    text-decoration:none;
    font-size:16px;
    font-weight:600;
    color:#fff;
    background:linear-gradient(
        145deg,
        rgba(255,183,77,0.15),
        rgba(0,0,0,0.6)
    );
    border:1.5px solid rgba(255,183,77,.6);
    box-shadow:
        0 0 25px rgba(255,183,77,.35),
        inset 0 0 15px rgba(255,183,77,.15);
    transition:.35s ease;
    backdrop-filter:blur(6px);
}

.login-btn:hover{
    background:linear-gradient(135deg,#ffb347,#ff7a18);
    color:#000;
    box-shadow:0 0 45px rgba(255,183,77,.9);
    transform:translateY(-1px);
}

/* BADGE */
.badge{
    position:absolute;
    top:-6px;
    right:-6px;
    width:18px;
    height:18px;
    border-radius:50%;
    background:#ff3b3b;
    color:#fff;
    font-size:11px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 0 10px rgba(255,59,59,.9);
}

/* PROFILE RING */
.profile-glow{
    box-shadow:
        0 0 18px rgba(255,183,77,.6),
        0 0 35px rgba(255,183,77,.4);
}

/* DROPDOWN */
.dropdown{
    display:none;
    position:absolute;
    right:0;
    top:70px; /* 🔥 navbar ni niche */
    min-width:220px;
    background:rgba(0,0,0,.96);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 25px 70px rgba(0,0,0,.8);
    border:1px solid rgba(255,183,77,.35);
    z-index:9999;
}

.dropdown a{
    display:block;
    padding:14px 22px;
    color:#eee;
    text-decoration:none;
    font-size:14px;
}
.dropdown a:hover{
    background:rgba(255,183,77,.2);
    color:#ffb347;
}

/* 📱 TOAST */
.toast{
    position:fixed;
    bottom:30px;
    left:30px;
    background:rgba(0,0,0,.9);
    color:#fff;
    padding:16px 22px;
    border-radius:16px;
    border:1px solid rgba(255,183,77,.5);
    box-shadow:0 0 35px rgba(255,183,77,.7);
    display:none;
    z-index:999;
    animation:slideIn .6s ease;
}
@keyframes slideIn{
    from{transform:translateX(-30px);opacity:0}
    to{transform:translateX(0);opacity:1}
}
</style>

<div class="navbar">

<div class="logo">EventHub</div>

<div class="menu">
    
<?php
if (!$role) {
   
    echo '<a href="/EventHub/index.php">Home</a>';
    echo '<a href="/EventHub/events.php">Events</a>';
}
if ($role === 'student') {
    echo '<a href="/EventHub/student/student_home.php">Home</a>';
    echo '<a href="/EventHub/student/student_events.php">Events</a>';
}
if ($role === 'organizer') {
    echo '<a href="/EventHub/organizer/organizer_home.php">Home</a>';
    echo '<a href="/EventHub/organizer/my_events.php">Events</a>';
}
if ($role === 'admin') {
    echo '<a href="/EventHub/admin/admin_home.php">Home</a>';
    echo '<a href="/EventHub/admin/admin_events.php">Events</a>';
}
?>
<a href="/EventHub/about.php">About</a>
</div>

<div class="actions">

<!-- 🔐 LOGIN (ONLY WHEN LOGGED OUT) -->
<?php if(!$role){ ?>
    <a href="/EventHub/login.php" class="login-btn">Login</a>
<?php } ?>

<!-- 👩‍🎓 STUDENT -->
<?php if($role === 'student'){ ?>
    <a href="/EventHub/student/notification.php" class="nav-icon" id="bellIcon">
        <i class="fa-solid fa-bell"></i>
        <?php if($notifCount>0){ ?>
            <span class="badge"><?php echo $notifCount; ?></span>
        <?php } ?>
    </a>

    <a href="/EventHub/student/profile.php" class="nav-icon">
        <i class="fa-solid fa-user"></i>
    </a>
<?php } ?>

<!-- ☰ MENU (ONLY WHEN LOGGED IN) -->
<?php if($role){ ?>
    <div class="hamburger" onclick="toggleMenu()">
        <span></span><span></span><span></span>
    </div>

    <div class="dropdown" id="menuBox">
        <?php if($role==='student'){ ?>
            <a href="/EventHub/student/student_dashboard.php">Dashboard</a>
            <a href="/EventHub/student/my_registrations.php">My Registrations</a>
        <?php } ?>
        <?php if($role==='organizer'){ ?>
            <a href="/EventHub/organizer/create_event.php">Create Event</a>
        <?php } ?>
        <?php if($role==='admin'){ ?>
            <a href="/EventHub/admin/manage_users.php">Manage Users</a>
            <a href="/EventHub/admin/manage_events.php">Manage Events</a>
        <?php } ?>
        <a href="/EventHub/logout.php">Logout</a>
    </div>
<?php } ?>

</div>
</div>

<div class="toast" id="toast">🔔 New notification received</div>

<script>
function toggleMenu(){
    const box=document.getElementById("menuBox");
    box.style.display = (box.style.display==="block") ? "none" : "block";
}

/* 🔔 PLAY SOUND + TOAST */
<?php if($notifCount>0 && $role==='student'){ ?>
window.addEventListener("load",()=>{
    const sound=document.getElementById("notifSound");
    sound.play().catch(()=>{});
    const toast=document.getElementById("toast");
    toast.style.display="block";
    setTimeout(()=>toast.style.display="none",3500);
});
<?php } ?>
</script>
