<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EventHub - Home</title>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, sans-serif;
        background-size: cover;
        color: white;
    }

    .navbar {
        position: fixed;
        top: 0;
        width: 100%;
        background: #000;
        padding: 12px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 2000;
        border-bottom: 2px solid #ff9900;
        box-shadow: 0 3px 20px rgba(255,153,0,0.3);
    }

    .navbar-left img {
        height: 40px;
        border-radius: 6px;
    }

    .navbar-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        padding: 6px 10px;
        border-radius: 5px;
        font-size: 14px;
    }

    .navbar a.active,
    .navbar a:hover {
        background: rgba(255,255,255,0.2);
    }

    /* NAV HAMBURGER BUTTON */
    #hamburgerBtn {
        background: #000;
        border: 1px solid rgba(255,255,255,0.2);
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #hamburgerBtn:hover {
        background: rgba(255,255,255,0.1);
    }

    /* FULL HEIGHT SLIDE MENU */
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

    /* MENU HEADER WITH CROSS BUTTON */
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

    .close-btn svg:hover {
        opacity: 0.7;
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

    /* BANNER */
    .banner {
        width: 100%;
        height: 400px;
        background: url('uploads/images/bg10.jpg') no-repeat center center;
        background-size: cover;
        margin-top: 65px;
    }

    /* CATEGORY GRID */
    .events-container {
        padding: 40px;
        text-align: center;
        background: #111;
        height: 404px;
    }

    .events-container h3 {
        margin-bottom: 30px;
        font-size: 26px;
        color: #fff;
    }

    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .event-card {
        background: rgba(255,255,255,0.15);
        border-radius: 12px;
        overflow: hidden;
        transition: 0.3s;
    }

    .event-card:hover {
        transform: translateY(-6px);
    }

    .event-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .event-card h4 {
        margin: 15px;
        color: #fff;
        font-size: 18px;
    }

    .card-footer {
        padding: 12px;
    }

    .btn {
        background: #6e1e1e;
        padding: 8px 14px;
        color: #fff;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
        font-weight: bold;
    }

    .btn:hover {
        opacity: 0.8;
    }
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="uploads/images/logo.png" alt="logo">
    </div>

    <div class="navbar-right">
        <a href="index.php" class="active">Home</a>

        <button id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="2.6" fill="#ffffff"></rect>
                <rect y="6.7" width="22" height="2.6" fill="#ffffff"></rect>
                <rect y="13.4" width="22" height="2.6" fill="#ffffff"></rect>
            </svg>
        </button>
    </div>
</div>

<!-- FULL HEIGHT SLIDE MENU -->
<div id="sideMenu">

    <div class="menu-header">
        <!-- CROSS BUTTON INSIDE -->
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="4" y1="4" x2="24" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                <line x1="24" y1="4" x2="4" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <a href="events.php">Events</a>
    <a href="login.php">Login</a>


</div>

<!-- BANNER -->
<div class="banner"></div>

<!-- CATEGORY GRID -->
<div class="events-container">
    <h3>Step Into the World of Opportunities & Celebrations</h3>

    <div class="event-grid">
        <?php
        $categories = [
            "Workshop" => "uploads/images/workshop.jpg",
            "Seminar" => "uploads/images/seminar.jpg",
            "Cultural" => "uploads/images/cultural.jpg",
            "Sports" => "uploads/images/sports.jpg",
            "Social" => "uploads/images/social.jpg",
            "Exhibition" => "uploads/images/exhibition.jpg"
        ];

        foreach ($categories as $cat => $img): ?>
            <div class="event-card">
                <img src="<?php echo $img; ?>">
                <h4><?php echo $cat; ?></h4>
                <div class="card-footer">
                    <a class="btn" href="category_events.php?category=<?php echo urlencode($cat); ?>">View Events</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- JS -->
<script>
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
