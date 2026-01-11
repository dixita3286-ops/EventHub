<?php
$conn = mysqli_connect("localhost", "root", "", "eventhub");
if (!$conn) {
    die("DB Connection Failed");
}

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/* -------------------------------------------------------
   DOWNLOAD LOGIC (MUST BE ON TOP)
-------------------------------------------------------- */
if (isset($_GET['download'])) {

    $file = basename($_GET['download']);
    $filepath = "uploads/files/" . $file;   // adjust if needed

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

/* -------------------------------------------------------
   FETCH EVENT DETAILS (FIXED FIELDS)
-------------------------------------------------------- */
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
      AND status = 'approved'
    LIMIT 1
";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Event not found.");
}

$event = mysqli_fetch_assoc($result);
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

.main{
    padding-top:110px;
    padding-bottom:60px;
    display:flex;
    justify-content:center;
}

.event-card{
    width:90%;
    max-width:720px;
    background:rgba(255,255,255,0.07);
    border-radius:20px;
    padding:35px;
    text-align:center;
    box-shadow:0 8px 35px rgba(0,0,0,0.5);
    border:1px solid rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    animation:fadeIn .6s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(20px);}
    to{opacity:1;transform:translateY(0);}
}

.event-card img{
    width:300px;
    height:300px;
    object-fit:cover;
    border-radius:16px;
    margin-bottom:20px;
    box-shadow:0 0 18px rgba(255,153,0,0.45);
}

.event-card h2{
    font-family:'Parisienne',cursive;
    font-size:48px;
    color:#ffbb55;
    margin-bottom:18px;
}

.event-info{
    text-align:left;
    width:85%;
    margin:auto;
    font-size:16px;
    line-height:1.6;
}

.event-info p{
    margin:10px 0;
    color:#eee;
}

.event-info strong{
    color:#ffcc66;
}

.download-btn{
    display:inline-block;
    padding:12px 22px;
    border-radius:10px;
    margin-top:24px;
    background:#ff9900;
    color:black;
    font-size:16px;
    font-weight:600;
    text-decoration:none;
    transition:.3s;
}
.download-btn:hover{
    background:#e68900;
    transform:translateY(-2px);
}
</style>
</head>

<body>

<?php include "public/navbar.php"; ?>

<div class="main">
    <div class="event-card">

        <!-- ✅ IMAGE (DIRECT DB PATH) -->
        <?php if (!empty($event['event_image'])): ?>
            <img src="<?php echo $event['event_image']; ?>" alt="Event Image">
        <?php endif; ?>

        <h2><?php echo htmlspecialchars($event['title']); ?></h2>

        <div class="event-info">
            <p><strong>Category:</strong> <?php echo $event['category']; ?></p>
            <p><strong>Date:</strong> <?php echo $event['event_date']; ?></p>
            <p><strong>Venue:</strong> <?php echo $event['venue']; ?></p>
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
