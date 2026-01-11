<?php
session_start();

/* ================= DB ================= */
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) {
    die("DB connection failed");
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    die("Invalid event ID");
}

/* ============================================================
   DOWNLOAD LOGIC (RUNS BEFORE HTML)
============================================================ */
if (isset($_GET['download'])) {

    $file = basename($_GET['download']);
    $filepath = "../uploads/files/" . $file;

    if (file_exists($filepath)) {

        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$file\"");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Pragma: public");
        header("Content-Length: " . filesize($filepath));

        readfile($filepath);
        exit;
    } else {
        die("File not found.");
    }
}

/* ============================================================
   FETCH EVENT DETAILS (FIXED)
============================================================ */
$query = "
    SELECT 
        title,
        description,
        category,
        event_date,
        venue,
        event_image,
        event_file,
        registration_fee
    FROM events
    WHERE event_id = $event_id
    LIMIT 1
";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("SQL ERROR: " . mysqli_error($conn));
}

$event = mysqli_fetch_assoc($result);
if (!$event) {
    die("Event not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Details</title>

<link href="https://fonts.googleapis.com/css2?family=Parisienne&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}

/* MAIN */
.main{
    padding-top:120px;
    padding-bottom:60px;
    display:flex;
    justify-content:center;
}

/* CARD */
.event-card{
    width:90%;
    max-width:720px;
    background:rgba(255,255,255,0.07);
    border-radius:20px;
    padding:35px;
    text-align:center;
    box-shadow:
        inset 0 0 25px rgba(255,204,102,.08),
        0 20px 60px rgba(0,0,0,.8);
    border:1px solid rgba(255,204,102,.25);
    backdrop-filter:blur(14px);
}

/* IMAGE */
.event-card img{
    width:300px;
    height:300px;
    object-fit:cover;
    border-radius:16px;
    margin-bottom:20px;
    box-shadow:0 0 25px rgba(255,204,102,.55);
}

/* TITLE */
.event-card h2{
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffcc66;
    margin-bottom:18px;
}

/* INFO */
.event-info{
    text-align:left;
    margin:auto;
    width:80%;
    font-size:16px;
    line-height:1.6;
}
.event-info p{margin:10px 0}
.event-info strong{color:#ffcc66}

/* DOWNLOAD */
.download-btn{
    display:inline-block;
    padding:12px 20px;
    border-radius:10px;
    background:linear-gradient(135deg,#ffcc66,#ff9900);
    color:black;
    text-decoration:none;
    font-size:16px;
    margin-top:25px;
    font-weight:600;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">
    <div class="event-card">

        <?php
        $imagePath = (!empty($event['event_image']) && file_exists("../".$event['event_image']))
            ? "../".$event['event_image']
            : "../uploads/images/default.jpg";
        ?>

        <img src="<?php echo $imagePath; ?>">

        <h2><?php echo htmlspecialchars($event['title']); ?></h2>

        <div class="event-info">
            <p><strong>Category:</strong> <?php echo htmlspecialchars($event['category']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
            <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?></p>
            <p><strong>Registration Fees:</strong> ₹<?php echo $event['registration_fee']; ?></p>
            <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
        </div>

        <?php if (!empty($event['event_file'])): ?>
            <a class="download-btn"
               href="event_details.php?id=<?php echo $event_id; ?>&download=<?php echo basename($event['event_file']); ?>">
               Download Event File
            </a>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

<?php mysqli_close($conn); ?>
