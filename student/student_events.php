<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "eventhub_db");
$user_id = $_SESSION['user_id'];

/* Registered Events */
$myEventIds = [];
$res = mysqli_query($conn, "SELECT event_id FROM registrations WHERE user_id=$user_id AND status='registered'");
while ($r = mysqli_fetch_assoc($res)) {
    $myEventIds[$r['event_id']] = true;
}

/* AJAX Request */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
    $sort = isset($_POST['sort']) ? $_POST['sort'] : 'date_asc';

    $sql = "SELECT event_id, title, description, category, `date`, venue, event_image 
            FROM events WHERE status='approved' ";

    if (!empty($search)) {
        $search = mysqli_real_escape_string($conn, $search);
        $sql .= " AND title LIKE '%$search%' ";
    }

    if (!empty($category) && $category !== 'all') {
        $category = mysqli_real_escape_string($conn, $category);
        $sql .= " AND category='$category' ";
    }

    if ($sort === 'date_desc') {
        $sql .= " ORDER BY `date` DESC ";
    } else {
        $sql .= " ORDER BY `date` ASC ";
    }

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_assoc($result)) {

            $id = $row['event_id'];
            $registered = isset($myEventIds[$id]);
            $img = !empty($row['event_image']) ? $row['event_image'] : '../uploads/images/default.jpg';

            echo "
            <div class='event-card'>
                <img src='$img' alt='Event'>

                <div class='event-info'>
                    <h3>".htmlspecialchars($row['title'])."</h3>
                    <p><strong>Category:</strong> ".htmlspecialchars($row['category'])."</p>
                    <p><strong>Date:</strong> ".htmlspecialchars($row['date'])."</p>
                    <p><strong>Venue:</strong> ".htmlspecialchars($row['venue'])."</p>
                </div>

                <div class='event-actions'>
                    <a href='event_details.php?id=$id'>View Details</a>
                    <span>|</span>";

                if ($registered)
                    echo "<span class='registered'>Registered</span>";
                else
                    echo "<a href='payment.php?event_id=$id'>Register</a>";

            echo "</div></div>";
        }

    } else {
        echo "<p style='text-align:center;color:#aaa;'>No events found.</p>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Events - EventHub</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family: Arial, sans-serif;
    background:#0d0d0d;
    color:white;
}

/* ------------------ UPDATED NAVBAR (MATCHING INDEX) ------------------ */


.main{
    padding:120px 90px 90px;
}

.filters{
    display:flex;
    justify-content:center;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.filters input,
.filters select{
    padding:10px 14px;
    background:rgba(255,255,255,0.08);
    border:none;
    border-radius:10px;
    color:white;
    font-size:14px;
}

.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(300px,1fr));
    gap:25px;
}

.event-card{
    background:rgba(255,255,255,0.08);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(0,0,0,0.45);
    transition:0.25s ease;
}
.event-card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 40px rgba(0,0,0,0.6);
}

.event-card img{
    width:100%;
    aspect-ratio:1/1;
    object-fit:cover;
}

.event-info{padding:16px 18px;}
.event-info h3{color:#ff9900;margin-bottom:8px;}
.event-info p{color:#ddd;margin:4px 0;}

.event-actions{
    padding:12px 18px;
    background:rgba(255,255,255,0.02);
    border-top:1px solid rgba(255,255,255,0.03);
    display:flex;
    gap:10px;
}
.event-actions a{
    color:#ff8c00;
    font-weight:700;
    text-decoration:none;
}
.event-actions a:hover{text-decoration:underline;}

.registered{
    color:#888;
    font-weight:bold;
}

h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:25px;
    text-shadow:0 0 6px rgba(255,204,102,0.7),0 0 12px rgba(255,153,0,0.6);
}
select option{
    background:#000 !important;
    color:#fff !important;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

    <h1>Crafting Experiences, Not Just Events.</h1>

    <div class="filters">
        <input type="text" id="search" placeholder="Search events...">
        <select id="category">
            <option value="all">All Categories</option>
            <option value="Workshop">Workshop</option>
            <option value="Seminar">Seminar</option>
            <option value="Cultural">Cultural</option>
            <option value="Sports">Sports</option>
            <option value="Social">Social</option>
            <option value="Exhibition">Exhibition</option>
        </select>

        <select id="sort">
            <option value="date_asc">Date Ascending</option>
            <option value="date_desc">Date Descending</option>
        </select>
    </div>

    <div class="event-grid" id="result"></div>
</div>

<script>
$(document).ready(function(){

    function load(search, category, sort){
        $.post("student_events.php",
        {
            search: search,
            category: category,
            sort: sort
        },
        function(data){
            $("#result").html(data);
        });
    }

    load("", "all", "date_asc");

    $("#search, #category, #sort").on("keyup change", function(){
        load(
            $("#search").val(),
            $("#category").val(),
            $("#sort").val()
        );
    });
});

/* SLIDE MENU JS */
const btn = document.getElementById('hamburgerBtn');
const menu = document.getElementById('sideMenu');
const closeBtn = document.getElementById('closeMenu');

btn.addEventListener("click", (e)=>{
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click", ()=>{
    menu.classList.remove("show");
});

document.addEventListener("click", (e)=>{
    if(!menu.contains(e.target) && !btn.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>
