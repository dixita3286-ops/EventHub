<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$con = mysqli_connect("localhost", "root", "", "eventhub");
if (!$con) {
    die("DB Connection Failed");
}

/* ================= APPROVE / REJECT ================= */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    if ($_GET['action'] === 'approve') {
        $status = 'approved';
    } elseif ($_GET['action'] === 'reject') {
        $status = 'rejected';
    } else {
        $status = 'pending';
    }

    mysqli_query($con, "UPDATE events SET status='$status' WHERE event_id=$id");
    header("Location: manage_events.php");
    exit();
}

/* ================= FETCH PENDING EVENTS ================= */
/* 🔥 FIX: ORDER BY event_date (NOT date) */
$sql = "SELECT * FROM events WHERE status='pending' ORDER BY event_date ASC";
$result = mysqli_query($con, $sql);

/* SQL ERROR CHECK */
if (!$result) {
    die("SQL ERROR: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Event Proposals</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Poppins',sans-serif;background:#0d0d0d;color:white;min-height:100vh}

.main{padding:100px 40px 60px}
h1{
    text-align:center;
    font-family:'Parisienne',cursive;
    font-size:46px;
    font-weight:400;
    color:#ffcc66;
    margin-bottom:35px;
    text-shadow:
        0 0 6px rgba(255,204,102,0.7),
        0 0 12px rgba(255,153,0,0.6),
        0 0 18px rgba(255,153,0,0.4)
}

.event-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(260px,260px));
    justify-content:center;
    gap:25px
}

.event-card{
    width:260px;
    background:rgba(255,255,255,0.06);
    border-radius:14px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,0.12);
    box-shadow:0 6px 22px rgba(0,0,0,0.45);
    transition:.3s ease;
    display:flex;
    flex-direction:column;
    min-height:420px
}
.event-card:hover{
    transform:translateY(-6px);
    border-color:#ff9900;
    box-shadow:0 0 14px rgba(255,153,0,0.45)
}

.event-card img{
    width:100%;
    height:230px;
    object-fit:cover
}

.event-info{
    padding:12px;
    height:130px;
    overflow:hidden
}
.event-info h3{
    font-size:16px;
    color:#ffb84d;
    font-weight:600;
    margin-bottom:6px
}
.event-info p{
    font-size:12px;
    margin:3px 0;
    color:#ddd;
    line-height:1.3
}

.event-actions{
    text-align:center;
    padding:12px;
    margin-top:auto;
    display:flex;
    justify-content:center;
    gap:20px
}

.action-link{
    font-size:14px;
    font-weight:600;
    text-decoration:none;
    cursor:pointer
}
.approve{color:#00e676}
.reject{color:#ff5252}
.approve:hover{text-shadow:0 0 8px #00ff94}
.reject:hover{text-shadow:0 0 8px #ff7777}

@media(max-width:600px){
    .event-card{width:100%;min-height:auto}
    .event-card img{height:200px}
    .event-info{height:auto}
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

<h1>Pending Event Proposals</h1>

<div class="event-grid">

<?php
if (mysqli_num_rows($result) > 0) {

    while ($row = mysqli_fetch_assoc($result)) {

        /* 🔥 IMAGE PATH FIX (DB SAFE) */
        $img = (!empty($row['event_image']) && file_exists("../".$row['event_image']))
            ? "../".$row['event_image']
            : "../uploads/images/default.jpg";
?>
    <div class="event-card">
        <img src="<?php echo $img; ?>">

        <div class="event-info">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Category:</strong> <?php echo $row['category']; ?></p>
            <p><strong>Date:</strong> <?php echo $row['event_date']; ?></p>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($row['venue']); ?></p>
            <p><?php echo htmlspecialchars(substr($row['description'],0,70)); ?>...</p>
        </div>

        <div class="event-actions">
            <a class="action-link approve"
               href="manage_events.php?action=approve&id=<?php echo $row['event_id']; ?>">
               Approve
            </a>

            <a class="action-link reject"
               href="manage_events.php?action=reject&id=<?php echo $row['event_id']; ?>"
               onclick="return confirm('Reject this event?');">
               Reject
            </a>
        </div>
    </div>
<?php
    }

} else {
    echo '<div style="grid-column:1/-1;text-align:center;color:#ccc;padding:20px;">
            No pending proposals.
          </div>';
}
?>

</div>
</div>

</body>
</html>

<?php mysqli_close($con); ?>
