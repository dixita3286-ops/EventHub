<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");

$organizer_id = $_SESSION['user_id'];
$event_id = $_GET['id'];

$query = "SELECT * FROM events WHERE event_id = '$event_id' AND created_by = '$organizer_id'";
$result = mysqli_query($con, $query);
$event = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];

    $updateQuery = "UPDATE events 
                    SET title='$title', description='$description', category='$category', date='$date', venue='$venue', status='pending'
                    WHERE event_id='$event_id' AND created_by='$organizer_id'";

    if (mysqli_query($con, $updateQuery)) {
        header("Location: my_events.php?msg=Event updated successfully");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Modify Event - Organizer</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    background:#0d0d0d;
    font-family:"Poppins",sans-serif;
    color:white;
    min-height:100vh;
}

/* ---------------- NAVBAR ---------------- */


/* ---------------- MAIN FORM CARD ---------------- */
.main{
    margin:100px auto 40px;
    width:600px;
    padding:35px;
    background:rgba(255,255,255,0.08);
    border-radius:20px;
    box-shadow:0 8px 30px rgba(0,0,0,0.5);
    backdrop-filter:blur(12px);
}

h2{
    text-align:center;
    color:#ffcc66;
    font-size:32px;
    margin-bottom:25px;
    text-shadow:0 0 8px rgba(255,204,102,0.5);
}

/* FORM ELEMENTS */
label{
    font-weight:600;
    margin-bottom:8px;
    display:block;
}

input,textarea,select{
    width:100%;
    padding:12px;
    margin-bottom:18px;
    border:none;
    border-radius:10px;
    background:rgba(255,255,255,0.12);
    color:white;
    font-size:15px;
}

select option{
    background:#111;
    color:white;
}

textarea{
    resize:vertical;
}

/* BUTTON */
button{
    width:100%;
    padding:14px;
    background:#ff9900;
    color:#111;
    border:none;
    border-radius:10px;
    font-size:17px;
    font-weight:700;
    cursor:pointer;
    letter-spacing:0.5px;
    box-shadow:0 0 10px rgba(255,153,0,0.5);
    transition:0.25s;
}

button:hover{
    background:#ffb84d;
    box-shadow:0 0 14px rgba(255,204,102,0.8);
}

/* RESPONSIVE */
@media(max-width:680px){
    .main{
        width:90%;
        padding:25px;
    }
}
</style>
</head>

<body>

<?php include "public/navbar.php"; ?>

<!-- MAIN FORM -->
<div class="main">
    <h2>Modify Event</h2>

    <form method="POST">

        <label>Event Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>

        <label>Description</label>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($event['description']); ?></textarea>

        <label>Category</label>
        <select name="category">
            <option value="Workshop" <?php if($event['category']=="Workshop") echo "selected"; ?>>Workshop</option>
            <option value="Seminar" <?php if($event['category']=="Seminar") echo "selected"; ?>>Seminar</option>
            <option value="Cultural" <?php if($event['category']=="Cultural") echo "selected"; ?>>Cultural</option>
            <option value="Sports" <?php if($event['category']=="Sports") echo "selected"; ?>>Sports</option>
            <option value="Social" <?php if($event['category']=="Social") echo "selected"; ?>>Social</option>
            <option value="Exhibition" <?php if($event['category']=="Exhibition") echo "selected"; ?>>Exhibition</option>
        </select>

        <label>Date</label>
        <input type="date" name="date" value="<?php echo $event['date']; ?>" required>

        <label>Venue</label>
        <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue']); ?>" required>

        <button type="submit">Update Event</button>

    </form>
</div>

<script>
const hb = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

hb.onclick = (e)=>{
    e.stopPropagation();
    menu.classList.add("show");
};
closeBtn.onclick = ()=> menu.classList.remove("show");

document.addEventListener("click",(e)=>{
    if(!menu.contains(e.target) && !hb.contains(e.target)){
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($con); ?>
