<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EventHub - Admin Home</title>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, sans-serif;
        background-size: cover;
        color: white;
    }


    /* BANNER */
    .banner {
        width: 100%;
        height: 400px;
        background: url('../uploads/images/bg10.jpg') no-repeat center center;
        background-size: cover;
        margin-top: 65px;
    }

    .events-container {
        padding: 40px;
        text-align: center;
        background: #111;
        height: 468px;
    }

    .events-container h3 {
        margin-bottom: 30px;
        color: #fff;
        font-size: 26px;
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
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        backdrop-filter: blur(12px);
        color: #fff;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .event-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 6px 25px rgba(0,0,0,0.3);
    }

    .event-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .event-card h4 {
        margin: 15px;
        font-size: 18px;
    }

    .card-footer {
        padding: 12px;
        text-align: center;
    }

    .btn {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 13px;
        background: #6e1e1e;
        color: white;
        text-decoration: none;
        font-weight: bold;
    }
    .btn:hover { opacity: 0.8; }
</style>
</head>

<body>
<?php include "../public/navbar.php"; ?>



<!-- BANNER -->
<div class="banner"></div>

<!-- CATEGORY GRID -->
<div class="events-container">
    <h3>Step Into the World of Opportunities & Celebrations</h3>
    <div class="event-grid">
        <?php 
        $categories = [
            "Workshop" => "../uploads/images/workshop.jpg",
            "Seminar" => "../uploads/images/seminar.jpg",
            "Cultural" => "../uploads/images/cultural.jpg",
            "Sports" => "../uploads/images/sports.jpg",
            "Social" => "../uploads/images/social.jpg",
            "Exhibition" => "../uploads/images/exhibition.jpg"
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
    btn.classList.add("hide");   // hide hamburger
});

closeBtn.addEventListener("click", () => {
    menu.classList.remove("show");
    btn.classList.remove("hide");  // show hamburger again
});

document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
        btn.classList.remove("hide");
    }
});
</script>

</body>
</html>
