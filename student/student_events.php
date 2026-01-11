<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) {
    die("DB Connection Failed");
}

$user_id = (int)$_SESSION['user_id'];

/* ================= REGISTERED EVENTS ================= */
$myEventIds = [];
$regRes = mysqli_query(
    $conn,
    "SELECT event_id FROM registrations 
     WHERE user_id=$user_id AND status='registered'"
);

if ($regRes) {
    while ($r = mysqli_fetch_assoc($regRes)) {
        $myEventIds[$r['event_id']] = true;
    }
}

/* ================= AJAX REQUEST ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $search   = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $sort     = isset($_POST['sort']) ? $_POST['sort'] : 'date_asc';

    $sql = "SELECT event_id, title, category, event_date, venue, event_image
            FROM events
            WHERE status='approved'";

    if ($search !== '') {
        $sql .= " AND title LIKE '%$search%'";
    }

    if ($category !== '' && $category !== 'all') {
        $sql .= " AND category='$category'";
    }

    $sql .= ($sort === 'date_desc')
        ? " ORDER BY event_date DESC"
        : " ORDER BY event_date ASC";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) === 0) {
        echo "<p class='empty'>No events found.</p>";
        exit;
    }

    while ($row = mysqli_fetch_assoc($result)) {

        $id = (int)$row['event_id'];
        $registered = isset($myEventIds[$id]);

        /* ✅ IMAGE FIX (DB VALUE DIRECT) */
        $img = (!empty($row['event_image']))
            ? "../" . $row['event_image']
            : "../uploads/images/default.jpg";
        ?>

        <div class="event-card">

            <div class="event-img-wrapper">
                <img src="<?php echo $img; ?>" alt="Event">
            </div>

            <div class="event-info">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($row['event_date']); ?></p>
                <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
            </div>

            <div class="event-actions">
                <a href="../event_details.php?id=<?php echo $id; ?>">View Details</a>
                <span>|</span>

                <?php if ($registered): ?>
                    <span class="registered">Registered</span>
                <?php else: ?>
                    <a href="payment.php?event_id=<?php echo $id; ?>">Register</a>
                <?php endif; ?>
            </div>

        </div>

        <?php
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Events | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>

/* ================= GLOBAL ================= */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:#0b0b0b;
    color:#fff;
    min-height:100vh;
}

/* ================= MAIN ================= */
.main{
    max-width:1700px;
    margin:auto;
    padding:120px 60px 90px;
}

/* ================= TITLE ================= */
h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:50px;
    color:#ffcc66;
    margin-bottom:35px;
    text-shadow:
        0 0 12px rgba(255,204,102,.6),
        0 0 22px rgba(255,153,0,.4);
}

/* ================= FILTERS ================= */
.filters{
    display:flex;
    justify-content:center;
    gap:16px;
    margin-bottom:40px;
    flex-wrap:wrap;
}

.filters input,
.filters select{
    padding:12px 18px;
    background:rgba(255,255,255,0.06);
    border-radius:14px;
    border:1px solid rgba(255,204,102,.45);
    color:#fff;
    outline:none;
    transition:.3s;
}

.filters input:focus,
.filters select:focus{
    border-color:#ffb347;
    box-shadow:0 0 18px rgba(255,183,77,.5);
}

.filters select option{
    background:#111;
}

/* ================= GRID ================= */
.event-grid{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:28px;
}

/* ================= EVENT CARD ================= */
.event-card{
    position:relative;
    background:
        linear-gradient(
            160deg,
            rgba(255,255,255,.10),
            rgba(255,255,255,.02)
        );
    border-radius:22px;
    padding:20px;
    border:1px solid rgba(255,204,102,.35);
    box-shadow:
        0 15px 40px rgba(0,0,0,.65);
    transition:.45s ease;
    overflow:hidden;
}

/* GOLDEN GLOW BORDER */
.event-card::before{
    content:"";
    position:absolute;
    inset:-2px;
    border-radius:24px;
    background:linear-gradient(
        120deg,
        transparent,
        rgba(255,204,102,.55),
        transparent
    );
    opacity:0;
    transition:.45s;
    pointer-events:none;
}

.event-card:hover::before{
    opacity:1;
}

/* HOVER LIFT */
.event-card:hover{
    transform:translateY(-10px) scale(1.05);
    box-shadow:
        0 0 30px rgba(255,183,77,.45),
        0 25px 60px rgba(0,0,0,.8);
}

/* ================= IMAGE ================= */
.event-img-wrapper img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:16px;
    margin-bottom:14px;
    box-shadow:
        0 0 18px rgba(255,183,77,.25);
}

/* ================= INFO ================= */
.event-info h3{
    color:#ffcc66;
    margin-bottom:8px;
    font-size:17px;
}

.event-info p{
    font-size:13px;
    color:#ddd;
    margin:4px 0;
}

/* ================= ACTIONS ================= */
.event-actions{
    margin-top:14px;
    font-size:13px;
    display:flex;
    align-items:center;
    gap:8px;
}

.event-actions a{
    color:#ffb347;
    font-weight:700;
    text-decoration:none;
    position:relative;
}

.event-actions a:hover{
    text-shadow:0 0 8px rgba(255,183,77,.8);
}

.registered{
    color:#aaa;
    font-weight:700;
}

/* ================= EMPTY ================= */
.empty{
    grid-column:1/-1;
    text-align:center;
    color:#aaa;
    font-size:18px;
}

/* ================= RESPONSIVE ================= */
@media(max-width:1200px){
    .event-grid{grid-template-columns:repeat(3,1fr)}
}
@media(max-width:900px){
    .event-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:500px){
    .event-grid{grid-template-columns:1fr}
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
$(function(){

    function loadEvents(search, category, sort){
        $.post("student_events.php",{search,category,sort},function(data){
            $("#result").html(data);
        });
    }

    loadEvents("", "all", "date_asc");

    $("#search, #category, #sort").on("keyup change", function(){
        loadEvents(
            $("#search").val(),
            $("#category").val(),
            $("#sort").val()
        );
    });
});
</script>

</body>
</html>
