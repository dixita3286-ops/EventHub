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
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif}

body{
    background:#0d0d0d;
    color:#fff;
    min-height:100vh;
}

.main{
    max-width:1700px;
    margin:auto;
    padding:120px 60px 90px;
}

h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:30px;
}

.filters{
    display:flex;
    justify-content:center;
    gap:15px;
    margin-bottom:30px;
    flex-wrap:wrap;
}

.filters input,
.filters select{
    padding:12px 16px;
    background:rgba(255,255,255,0.08);
    border-radius:12px;
    border:1px solid rgba(255,204,102,.35);
    color:#fff;
}

.filters select option{
    background:#111;
}

.event-grid{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:26px;
}

.event-card{
    background:linear-gradient(160deg,rgba(255,255,255,.1),rgba(255,255,255,.02));
    border-radius:20px;
    padding:20px;
    box-shadow:0 12px 30px rgba(0,0,0,.55);
    transition:.35s;
}

.event-card:hover{
    transform:translateY(-8px) scale(1.04);
}

.event-img-wrapper img{
    width:100%;
    height:180px;
    object-fit:cover;
    border-radius:14px;
    margin-bottom:14px;
}

.event-info h3{
    color:#ffcc66;
    margin-bottom:8px;
}

.event-info p{
    font-size:13px;
    color:#ddd;
}

.event-actions{
    margin-top:12px;
    font-size:13px;
}

.event-actions a{
    color:#ff9900;
    font-weight:700;
    text-decoration:none;
}

.registered{
    color:#aaa;
    font-weight:700;
}

.empty{
    grid-column:1/-1;
    text-align:center;
    color:#aaa;
}

@media(max-width:1200px){.event-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:900px){.event-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:500px){.event-grid{grid-template-columns:1fr}}
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
