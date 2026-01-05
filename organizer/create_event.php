<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'organizer') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub_db");
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $venue = $_POST['venue'];
    $registrationFees = $_POST['registrationFees'];
    $created_by = $_SESSION['user_id'];

    $minDate = date('Y-m-d', strtotime('+5 days'));
    if ($date < $minDate) {
        $message = "Event date must be at least 5 days from today.";
    } else {

        $event_file = "";
        if (!empty($_FILES['event_file']['name'])) {
            $event_file = "../uploads/files/" . basename($_FILES['event_file']['name']);
            move_uploaded_file($_FILES['event_file']['tmp_name'], $event_file);
        }

        $event_image = "";
        if (!empty($_FILES['event_image']['name'])) {
            $event_image = "../uploads/images/" . basename($_FILES['event_image']['name']);
            move_uploaded_file($_FILES['event_image']['tmp_name'], $event_image);
        }

        $query = "INSERT INTO events (title, description, category, date, venue, registrationFees, event_file, event_image, created_by)
                  VALUES ('$title', '$description', '$category', '$date', '$venue', '$registrationFees', '$event_file', '$event_image', '$created_by')";

        if (mysqli_query($con, $query)) {
            $message = "Event created successfully! Please wait for admin approval.";
        } else {
            $message = "Error creating event: " . mysqli_error($con);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Event - EventHub</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Segoe UI',Tahoma,Verdana,sans-serif;
    color:#fff;
    background:#0d0d0d;
}

/* NAVBAR (Same as index) */
.navbar{
    position:sticky;
    top:0;
    width:100%;
    background:#000;
    padding:12px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #ff9900;
    box-shadow:0 3px 20px rgba(255,153,0,0.3);
    z-index:2000;
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
    padding:6px 10px;
    text-decoration:none;
    border-radius:5px;
}
.navbar-right a:hover{
    background:rgba(255,255,255,0.2);
}

/* Hamburger button */
#hamburgerBtn{
    background:#000;
    border:1px solid rgba(255,255,255,0.2);
    padding:8px;
    border-radius:8px;
    cursor:pointer;
}

/* FULL SLIDE MENU (same as index.php) */
#sideMenu{
    position:fixed;
    top:0;
    right:-300px;
    width:300px;
    height:100vh;
    background:rgba(0,0,0,0.97);
    z-index:3000;
    transition:right .35s ease-in-out;
    box-shadow:-5px 0 20px rgba(0,0,0,0.4);
}
#sideMenu.show{
    right:0;
}

.menu-header{
    width: 100%;
    padding: 18px 22px;
    border-bottom: 1px solid rgba(255,255,255,0.25);
    display: flex;
    justify-content: flex-start; 
    align-items: center;
}

.close-btn{
    background: none;
    border: none;
    cursor: pointer;
    display: flex;            /* IMPORTANT */
    justify-content: flex-start;  /* FORCE TO LEFT */
    align-items: center;
}

#sideMenu a{
    display:block;
    padding:15px 22px;
    font-size:17px;
    color:white;
    border-bottom:1px solid rgba(255,255,255,0.1);
    text-decoration:none;
}
#sideMenu a:hover{
    background:rgba(255,255,255,0.15);
}

/* MAIN */
.main{
    padding:40px 20px;
    display:flex;
    justify-content:center;
}
.form-box{
    width:100%;
    max-width:750px;
    background:rgba(255,255,255,0.08);
    border-radius:20px;
    padding:40px;
    border:1px solid rgba(255,255,255,0.2);
    box-shadow:0 8px 32px rgba(0,0,0,0.4);
}
h1{text-align:center;color:#ffcc00;margin-bottom:25px;}

.form-group{margin-bottom:18px;}
label{display:block;margin-bottom:6px;color:#ffcc00;}
input,textarea,select{
    width:100%;padding:12px;
    border-radius:10px;border:none;
    background:rgba(255,255,255,0.15);
    color:#fff;font-size:14px;
    border:1px solid rgba(255,255,255,0.3);
}

select option {
    background: #000 !important;
    color: #fff !important;
}

button{
    width:100%;padding:12px;
    background:linear-gradient(135deg,#ffb300,#ff7b00);
    border:none;border-radius:10px;
    color:#111;font-weight:bold;
    font-size:15px;
}

.message{text-align:center;margin-bottom:15px;color:#ffcc00;}

</style>

</head>
<body>

<div class="navbar">

    <div class="navbar-left">
        <img src="../uploads/images/logo.png" alt="logo">
    </div>

    <div class="navbar-right">
        <a href="organizer_home.php">Home</a>

        <!-- Hamburger button -->
        <button id="hamburgerBtn">
            <svg width="22" height="16">
                <rect width="22" height="3" fill="#ffffff"></rect>
                <rect y="6" width="22" height="3" fill="#ffffff"></rect>
                <rect y="12" width="22" height="3" fill="#ffffff"></rect>
            </svg>
        </button>
    </div>

</div>

<!-- FULL SLIDE MENU (same as index) -->
<div id="sideMenu">

    <div class="menu-header">
        <button id="closeMenu" class="close-btn">
            <svg width="28" height="28">
                <line x1="4" y1="4" x2="24" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
                <line x1="24" y1="4" x2="4" y2="24" stroke="#fff" stroke-width="3" stroke-linecap="round" />
            </svg>
        </button>
    </div>

    <a href="organizer_home.php">Home</a>
    <a href="my_events.php">My Events</a>
    <a href="../logout.php">Logout</a>

</div>


<div class="main">
    <div class="form-box">

        <h1>Create New Event</h1>

        <?php if(!empty($message)) echo "<div class='message'>$message</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Title:</label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Seminar">Seminar</option>
                    <option value="Cultural">Cultural</option>
                    <option value="Sports">Sports</option>
                    <option value="Social">Social</option>
                    <option value="Exhibition">Exhibition</option>
                </select>
            </div>

            <div class="form-group">
                <label>Event Date:</label>
                <input type="date" name="date" required>
            </div>

            <div class="form-group">
                <label>Venue:</label>
                <input type="text" name="venue" required>
            </div>

            <div class="form-group">
                <label>Registration Fees:</label>
                <input type="number" name="registrationFees" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label>Upload Event File:</label>
                <input type="file" name="event_file" accept=".pdf,.doc,.docx">
            </div>

            <div class="form-group">
                <label>Upload Event Image:</label>
                <input type="file" name="event_image" accept="image/*">
            </div>

            <button type="submit">Create Event</button>
        </form>

    </div>
</div>

<script>
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

btn.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.add("show");
});

closeBtn.addEventListener("click", () => {
    menu.classList.remove("show");
});

document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
    }
});
</script>

</body>
</html>

<?php mysqli_close($con); ?>
