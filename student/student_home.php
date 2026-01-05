<?php
session_start();
$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EventHub - Student Home</title>

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: Arial, sans-serif;
        background-size: cover;
        color: white;
    }

    /* REST OF YOUR PAGE (UNCHANGED) */
    .banner {
        width: 100%;
        height: 400px;
        background: url('../uploads/images/bg10.jpg') no-repeat center center;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 36px;
        font-weight: bold;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.6);
        margin-top: 65px;
    }

    .events-container {
        padding: 40px;
        text-align: center;
        background: #111;
        height: 404px;
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
        transition: 0.3s;
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
        color: #fff;
    }

    .card-footer {
        padding: 12px;
        text-align: center;
    }

    .btn {
        background: #6e1e1e;
        padding: 8px 14px;
        border-radius: 6px;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
        text-decoration: none;
    }
    .btn:hover { opacity: 0.8; }

</style>
</head>
<body>
<?php include "../public/navbar.php"; ?>

<div class="banner"></div>

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

<!-- JS FOR SLIDE MENU -->
<script>
const btn = document.getElementById('hamburgerBtn');
const menu = document.getElementById('sideMenu');
const closeBtn = document.getElementById('closeMenu');

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
