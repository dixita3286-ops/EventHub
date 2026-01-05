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
    .banner {
        width: 100%;
        height: 400px;
        background: url('uploads/images/bg10.jpg') no-repeat center center;
        background-size: cover;

    }

    .events-container {
        padding: 40px;
        text-align: center;
        background: #111;
        height: 404px;
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
    }

    .event-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }

    .btn {
        background: #6e1e1e;
        padding: 8px 14px;
        color: #fff;
        border-radius: 6px;
        font-size: 13px;
        text-decoration: none;
    }
</style>
</head>

<body>

<?php include "public/navbar.php"; ?>

<div class="banner"></div>

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
                <a class="btn" href="category_events.php?category=<?php echo urlencode($cat); ?>">
                    View Events
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
