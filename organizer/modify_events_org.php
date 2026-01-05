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
.navbar{
    position:fixed;
    top:0;
    width:100%;
    background:#000;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #ff9900;
    box-shadow:0 3px 20px rgba(255,153,0,0.3);
    z-index:3000;
}

.navbar-left img{
    height:40px;
    border-radius:6px;
}

.navbar-right{
    display:flex;
    align-items:center;
    gap:10px;
}

.navbar-right a{
    color:white;
    text-decoration:none;
    padding:6px 10px;
    border-radius:5px;
}
.navbar-right a:hover{
    background:rgba(255,255,255,0.2);
}

/* Hamburger */
#hamburgerBtn{
    padding:8px;
    background:rgba(255,255,255,0.12);
    border-radius:8px;
    cursor:pointer;
}
#hamburgerBtn:hover{
    background:rgba(255,255,255,0.22);
}

/* ---------------- SIDE MENU ---------------- */
#sideMenu{
    position:fixed;
    top:0;
    right:-300px;
    width:300px;
    height:100vh;
    background:rgba(0,0,0,0.97);
    box-shadow:-5px 0 20px rgba(0,0,0,0.4);
    transition:.35s;
    z-index:5000;
}
#sideMenu.show{
    right:0;
}

.menu-header{
    padding:18px 22px;
    border-bottom:1px solid rgba(255,255,255,0.25);
    display:flex;
    justify-content:flex-start;
}

.close-btn{
    background:none;
    border:none;
    cursor:pointer;
}

#sideMenu a{
    display:block;
    padding:16px 22px;
    font-size:17px;
    text-decoration:none;
    color:white;
    border-bottom:1px solid rgba(255,255,255,0.12);
}
#sideMenu a:hover{
    background:rgba(255,255,255,0.1);
}

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

<!-- NAVBAR -->
<div class="navbar">
    <div class="navbar-left">
        <img src="../uploads/images/logo.png" alt="logo">
    </div>

    <div class="navbar-right">
        <a href="organizer_home.php">Home</a>

        <div id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="3" fill="#fff"></rect>
                <rect y="6" width="22" height="3" fill="#fff"></rect>
                <rect y="12" width="22" height="3" fill="#fff"></rect>
            </svg>
        </div>
    </div>
</div>

<!-- SIDE MENU -->
<div id="sideMenu">
    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="26" height="26">
                <line x1="4" y1="4" x2="22" y2="22" stroke="#fff" stroke-width="3"/>
                <line x1="22" y1="4" x2="4" y2="22" stroke="#fff" stroke-width="3"/>
            </svg>
        </button>
    </div>

    <a href="my_events.php">My Events</a>
    <a href="../logout.php">Logout</a>
</div>

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
