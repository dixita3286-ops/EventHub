<?php
session_start();

/* ================= AUTH ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: ../login.php");
    exit();
}

/* ================= DB ================= */
$con = mysqli_connect("localhost", "root", "", "eventhub");
if (!$con) die("DB Error");

mysqli_set_charset($con,"utf8mb4");

$message = "";

/* ================= SUBMIT ================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $title       = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category    = mysqli_real_escape_string($con, $_POST['category']);
    $event_date  = $_POST['event_date'];
    $venue       = mysqli_real_escape_string($con, $_POST['venue']);
    $fees        = (int)$_POST['registration_fees'];
    $created_by  = (int)$_SESSION['user_id'];

    /* DATE VALIDATION */
    $minDate = date('Y-m-d', strtotime('+5 days'));
    if ($event_date < $minDate) {
        $message = "❌ Event date must be at least 5 days from today.";
    } else {

        /* FILE UPLOAD */
        $event_file = "";
        if (!empty($_FILES['event_file']['name'])) {
            $fileName = time().'_'.basename($_FILES['event_file']['name']);
            $target   = "../uploads/files/".$fileName;
            if (move_uploaded_file($_FILES['event_file']['tmp_name'], $target)) {
                $event_file = "uploads/files/".$fileName;
            }
        }

        /* IMAGE UPLOAD */
        $event_image = "";
        if (!empty($_FILES['event_image']['name'])) {
            $imgName = time().'_'.basename($_FILES['event_image']['name']);
            $target  = "../uploads/images/".$imgName;
            if (move_uploaded_file($_FILES['event_image']['tmp_name'], $target)) {
                $event_image = "uploads/images/".$imgName;
            }
        }

        /* ================= INSERT EVENT (PENDING) ================= */
        $query = "
            INSERT INTO events
            (title, description, category, event_date, venue, registration_fee, event_file, event_image, created_by, status)
            VALUES
            ('$title','$description','$category','$event_date','$venue','$fees','$event_file','$event_image','$created_by','pending')
        ";

        if (mysqli_query($con, $query)) {
            $message = "✅ Event created successfully! Waiting for admin approval.";
        } else {
            $message = "❌ Error: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Event | EventHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:'Poppins',sans-serif;
    background:#0d0d0d;
    color:#fff;
}

/* MAIN */
.main{
    padding:110px 20px 60px;
    display:flex;
    justify-content:center;
}

.form-box{
    width:100%;
    max-width:560px;
    background:rgba(255,255,255,.08);
    border-radius:18px;
    padding:30px;
    border:1px solid rgba(255,204,102,.35);
    box-shadow:
        inset 0 0 25px rgba(255,204,102,.08),
        0 18px 45px rgba(0,0,0,.85);
}

h1{
    text-align:center;
    color:#ffcc66;
    margin-bottom:22px;
}

.message{
    text-align:center;
    margin-bottom:16px;
    color:#ffcc66;
    font-weight:600;
}

.form-group{margin-bottom:14px}
label{
    display:block;
    margin-bottom:6px;
    color:#ffcc66;
    font-size:14px;
}

input,textarea,select{
    width:100%;
    padding:11px;
    border-radius:10px;
    border:1px solid rgba(255,255,255,.25);
    background:rgba(255,255,255,.12);
    color:#fff;
    font-size:14px;
}

select option{background:#111}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#ffcc66,#ff9900);
    color:#111;
    font-weight:700;
    font-size:15px;
    margin-top:10px;
    cursor:pointer;
}
</style>
</head>

<body>

<?php include "../public/navbar.php"; ?>

<div class="main">
<div class="form-box">

<h1>Create New Event</h1>

<?php if($message!="") echo "<div class='message'>$message</div>"; ?>

<form method="POST" enctype="multipart/form-data">

<div class="form-group">
<label>Event Title</label>
<input type="text" name="title" required>
</div>

<div class="form-group">
<label>Description</label>
<textarea name="description" rows="3"></textarea>
</div>

<div class="form-group">
<label>Category</label>
<select name="category" required>
<option value="">Select Category</option>
<option>Workshop</option>
<option>Seminar</option>
<option>Cultural</option>
<option>Sports</option>
<option>Social</option>
<option>Exhibition</option>
</select>
</div>

<div class="form-group">
<label>Event Date</label>
<input type="date" name="event_date" required>
</div>

<div class="form-group">
<label>Venue</label>
<input type="text" name="venue" required>
</div>

<div class="form-group">
<label>Registration Fees (₹)</label>
<input type="number" name="registration_fees" min="0" step="1" required>
</div>

<div class="form-group">
<label>Upload Event File</label>
<input type="file" name="event_file" accept=".pdf,.doc,.docx">
</div>

<div class="form-group">
<label>Upload Event Image</label>
<input type="file" name="event_image" accept="image/*">
</div>

<button type="submit">Create Event</button>

</form>
</div>
</div>

</body>
</html>

<?php mysqli_close($con); ?>
