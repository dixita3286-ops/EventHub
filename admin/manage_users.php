<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "eventhub");
if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

/* ---------- Handle delete action ---------- */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    $check = mysqli_query($con, "SELECT role FROM users WHERE user_id = $delete_id LIMIT 1");
    if ($check && mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        if ($row['role'] === 'admin') {
            header("Location: manage_users.php?err=cannot_delete_admin");
            exit();
        } else {

            mysqli_query($con, "DELETE FROM registrations WHERE user_id = $delete_id");

            mysqli_query($con, "DELETE FROM users WHERE user_id = $delete_id");

            header("Location: manage_users.php?msg=deleted");
            exit();
        }
    } else {
        header("Location: manage_users.php?err=user_not_found");
        exit();
    }
}

/* ---------- Fetch users ---------- */
$organizers = mysqli_query($con, "SELECT * FROM users WHERE role='organizer' ORDER BY name ASC");
$students = mysqli_query($con, "SELECT * FROM users WHERE role='student' ORDER BY name ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - EventHub Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Parisienne&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
    background:#0d0d0d;
    color:white;
    min-height:100vh;
}

/* ---------------- MAIN ---------------- */
.main{
    padding:120px 30px 40px;
}

h1{
    text-align:center;
    color:#ffcc00;
    margin-bottom:35px;
    font-family:'Parisienne',cursive;
    font-size:48px;
    text-shadow:0 0 6px rgba(255,204,102,0.6);
}

/* MESSAGES */
.message{
    max-width:850px;
    margin:10px auto 20px;
    padding:12px;
    border-radius:8px;
    text-align:center;
    font-weight:600;
}
.message.success{background:#1b4d1b;color:#b9ffb9;border:1px solid #4aff4a;}
.message.error{background:#4d1b1b;color:#ffb9b9;border:1px solid #ff4a4a;}

/* TABS */
.tab-buttons{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:25px;
}

.tab-btn{
    padding:10px 25px;
    border:none;
    background:#181818;
    color:white;
    cursor:pointer;
    font-size:16px;
    border-radius:6px;
    transition:.3s;
}
.tab-btn.active{
    background:#ff9900;
    color:black;
    font-weight:600;
}

.tab-content{display:none;}
.tab-content.active{display:block;}

/* USER LIST CARD STYLE */
.user-list{
    max-width:900px;
    margin:auto;
    display:flex;
    flex-direction:column;
    gap:18px;
}

.user-row{
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,0.15);
    padding:22px;
    border-radius:14px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 8px 25px rgba(0,0,0,0.35);
    transition:.3s;
}
.user-row:hover{
    transform:translateY(-5px);
    border-color:#ff9900;
}

.user-info{
    font-size:16px;
    color:#ddd;
    line-height:26px;
}

.user-info b{color:#ffcc66;}

.user-actions{
    font-size:16px;
    display:flex;
    align-items:center;
    gap:8px;
}
.user-actions a{
    color:#ff9900;
    text-decoration:none;
    font-weight:600;
}
.user-actions a:hover{text-decoration:underline;}

@media(max-width:780px){
    .user-row{
        flex-direction:column;
        gap:15px;
        align-items:flex-start;
    }
}
</style>


</head>
<body>

<?php include "../public/navbar.php"; ?>

<div class="main">

    <h1>Manage Users</h1>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="message success">User deleted successfully.</div>
    <?php elseif (isset($_GET['err'])): ?>
        <div class="message error">
            <?php
                if ($_GET['err'] === 'cannot_delete_admin') echo "Cannot delete an admin user.";
                elseif ($_GET['err'] === 'user_not_found') echo "User not found.";
                else echo "An error occurred.";
            ?>
        </div>
    <?php endif; ?>

    <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab(event,'students')">Students</button>
        <button class="tab-btn" onclick="openTab(event,'organizers')">Organizers</button>
    </div>

    <div id="students" class="tab-content active">
        <div class="user-list">
            <?php while ($row = mysqli_fetch_assoc($students)): ?>
                <div class="user-row">
                    <div class="user-info">
                        <b>Name:</b> <?= htmlspecialchars($row['name']) ?><br>
                        <b>Email:</b> <?= htmlspecialchars($row['email']) ?><br>
                        <b>Password:</b> <?= htmlspecialchars($row['password']) ?>
                    </div>

                    <div class="user-actions">
                        <a href="edit_user.php?id=<?= $row['user_id'] ?>">Edit User Details</a>
                        <span>|</span>
                        <a href="manage_users.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Delete user?');">Remove User</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="organizers" class="tab-content">
        <div class="user-list">
            <?php while ($row = mysqli_fetch_assoc($organizers)): ?>
                <div class="user-row">
                    <div class="user-info">
                        <b>Name:</b> <?= htmlspecialchars($row['name']) ?><br>
                        <b>Email:</b> <?= htmlspecialchars($row['email']) ?><br>
                        <b>Password:</b> <?= htmlspecialchars($row['password']) ?>
                    </div>

                    <div class="user-actions">
                        <a href="edit_user.php?id=<?= $row['user_id'] ?>">Manage User Details</a>
                        <span>|</span>
                        <a href="manage_users.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Delete user?');">Remove User</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

<script>
// SLIDE MENU SCRIPT (same as Home Page)
const btn = document.getElementById("hamburgerBtn");
const menu = document.getElementById("sideMenu");
const closeBtn = document.getElementById("closeMenu");

btn.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.classList.add("show");
});
closeBtn.addEventListener("click", () => menu.classList.remove("show"));
document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.classList.remove("show");
    }
});

function openTab(evt, tab) {
    document.querySelectorAll(".tab-btn").forEach(b => b.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
    evt.currentTarget.classList.add("active");
    document.getElementById(tab).classList.add("active");
}
</script>

</body>
</html>

<?php mysqli_close($con); ?>
