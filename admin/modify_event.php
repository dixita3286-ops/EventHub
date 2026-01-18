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
    die("Database connection failed");
}

/* ================= VALIDATE EVENT ID ================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Event ID");
}

$event_id = (int)$_GET['id'];

/* ================= FETCH EVENT ================= */
$query = "SELECT title, description, category, event_date, venue 
          FROM events 
          WHERE event_id = $event_id";

$result = mysqli_query($con, $query);

if (!$result) {
    die("SQL Error: " . mysqli_error($con));
}

if (mysqli_num_rows($result) === 0) {
    die("Event not found");
}

$event = mysqli_fetch_assoc($result);

/* ================= UPDATE EVENT ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category    = mysqli_real_escape_string($con, $_POST['category']);
    $event_date  = mysqli_real_escape_string($con, $_POST['event_date']);
    $venue       = mysqli_real_escape_string($con, $_POST['venue']);

    $update = "
        UPDATE events 
        SET 
            title='$title',
            description='$description',
            category='$category',
            event_date='$event_date',
            venue='$venue'
        WHERE event_id=$event_id
    ";

    if (mysqli_query($con, $update)) {
        header("Location: admin_events.php");
        exit();
    } else {
        die("Update failed: " . mysqli_error($con));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Modify Event - EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Poppins',sans-serif;
    background:
        radial-gradient(circle at top left, #1a1a1a, #000),
        radial-gradient(circle at bottom right, #0f0f0f, #000);
    color:white;
    min-height:100vh;
    padding:90px;
}

/* ================= CARD ================= */
.form-container{
    position:relative;
    width:680px;
    margin:60px auto;
    padding:40px;
    border-radius:22px;
    background:linear-gradient(
        145deg,
        rgba(255,255,255,0.10),
        rgba(255,255,255,0.03)
    );
    backdrop-filter:blur(18px);
    box-shadow:
        0 0 40px rgba(255,153,0,0.25),
        inset 0 0 25px rgba(255,255,255,0.06);
    overflow:hidden;
    animation:floatIn .9s ease;
}

/* NEON BORDER */
.form-container::before{
    content:'';
    position:absolute;
    inset:0;
    padding:2px;
    border-radius:22px;
    background:linear-gradient(
        120deg,
        #ff9900,
        #ffcc66,
        #ff9900
    );
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite:xor;
    mask-composite:exclude;
    filter:drop-shadow(0 0 15px #ff9900);
    animation:borderGlow 4s linear infinite;
}

@keyframes borderGlow{
    0%{filter:drop-shadow(0 0 8px #ff9900);}
    50%{filter:drop-shadow(0 0 22px #ffcc66);}
    100%{filter:drop-shadow(0 0 8px #ff9900);}
}

/* FLOAT IN */
@keyframes floatIn{
    from{opacity:0;transform:translateY(35px) scale(.96);}
    to{opacity:1;transform:translateY(0) scale(1);}
}

/* ================= HEADING ================= */
.form-container h2{
    text-align:center;
    font-size:36px;
    font-weight:700;
    background:linear-gradient(90deg,#ff9900,#ffcc66);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
    text-shadow:0 0 18px rgba(255,153,0,.6);
    margin-bottom:30px;
}

/* ================= LABEL ================= */
label{
    display:block;
    margin-top:18px;
    font-size:14px;
    font-weight:600;
    letter-spacing:.5px;
    color:#ffd9a0;
}

/* ================= INPUTS ================= */
input, textarea{
    width:100%;
    padding:13px 14px;
    margin-top:7px;
    border-radius:14px;
    border:1px solid rgba(255,255,255,0.18);
    background:rgba(255,255,255,0.07);
    color:white;
    font-size:14px;
    transition:.35s ease;
}

input::placeholder, textarea::placeholder{
    color:#aaa;
}

input:focus, textarea:focus{
    outline:none;
    border-color:#ff9900;
    background:rgba(255,255,255,0.12);
    box-shadow:
        0 0 0 1px rgba(255,153,0,0.6),
        0 0 18px rgba(255,153,0,0.6);
    transform:scale(1.015);
}

/* ================= BUTTON ================= */
button{
    position:relative;
    width:100%;
    padding:15px;
    margin-top:30px;
    border:none;
    border-radius:14px;
    font-size:17px;
    font-weight:800;
    letter-spacing:.8px;
    cursor:pointer;
    color:#000;
    background:linear-gradient(135deg,#ff9900,#ffcc66);
    box-shadow:
        0 10px 30px rgba(255,153,0,0.55),
        inset 0 0 10px rgba(255,255,255,0.3);
    transition:.35s ease;
}

/* BUTTON SHINE */
button::after{
    content:'';
    position:absolute;
    inset:0;
    border-radius:14px;
    background:linear-gradient(
        120deg,
        transparent 30%,
        rgba(255,255,255,0.6),
        transparent 70%
    );
    opacity:0;
    transition:.4s;
}

button:hover{
    transform:translateY(-4px) scale(1.03);
    box-shadow:
        0 15px 45px rgba(255,153,0,0.8),
        0 0 35px rgba(255,204,102,0.7);
}

button:hover::after{
    opacity:1;
    animation:shine 1.2s linear infinite;
}

@keyframes shine{
    from{transform:translateX(-100%);}
    to{transform:translateX(100%);}
}

</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="form-container">
    <h2>Modify Event</h2>

    <form method="POST">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>

        <label>Category</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($event['category']); ?>" required>

        <label>Date</label>
        <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>

        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>

        <button type="submit">Update Event</button>
    </form>
</div>

</body>
</html>

<?php mysqli_close($con); ?>
