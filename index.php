<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>EventHub | College Event Management System</title>

    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Segoe UI', sans-serif;
}

body{
    min-height:100vh;
    color:#fff;
    background:#000;
}
/* HERO SECTION */
.hero{
    min-height:calc(110vh - 80px); /* navbar height subtract */
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:0;
    background:
        linear-gradient(to bottom, rgba(0,0,0,0.65), rgba(0,0,0,0.6)),
        url('uploads/images/bg1.jpg') center 42% / cover no-repeat;
}

.hero-content{
    max-width:900px;
    padding-top:10px; /* slight breathing space */
}

        .badge{
            display:inline-block;
            background:#111;
            padding:8px 16px;
            border-radius:20px;
            color:#ff7a18;
            font-size:14px;
            margin-bottom:20px;
        }

        .hero h1{
            font-size:54px;
            font-weight:700;
            line-height:1.2;
            margin-bottom:20px;
            padding:0;
        }

        .hero p{
            font-size:18px;
            color:#ccc;
            margin-bottom:35px;
        }

        .hero-buttons a{
            display:inline-block;
            margin:10px;
            padding:14px 28px;
            border-radius:30px;
            text-decoration:none;
            font-weight:600;
        }

        .btn-primary{
            background:linear-gradient(135deg, #ff7a18, #ffb347);
            color:#000;
        }

        .btn-secondary{
            border:1px solid #444;
            color:#fff;
        }

        @media(max-width:768px){
            .hero h1{font-size:36px;}
        }
    </style>
</head>
<body>
<?php include("public/navbar.php"); ?>


<section class="hero">
    <div class="hero-content">

        <div class="badge"> New events every week</div>

        <h1>
            Manage College Events <br>
            Smarter & Faster
        </h1>

        <p>
            EventHub helps students, organizers, and administrators
            create, manage, and participate in college events 
            all in one secure platform.
        </p>

        <div class="hero-buttons">
            <?php if(!isset($_SESSION['role'])) { ?>
                <a href="signup.php" class="btn-primary">Get Started</a>
                <a href="login.php" class="btn-secondary">Login</a>
            <?php } else { ?>
                <a href="<?=$_SESSION['role']?>/dashboard.php" class="btn-primary">Go to Dashboard</a>
            <?php } ?>
        </div>

    </div>
</section>

</body>
</html>
