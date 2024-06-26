<?php
include_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact - Adult Gambling Behaviors Survey</title>
    <?php include_styles() ?>
</head>
<body>
<?php include_header(); ?>
<div class="container" id="main">
    <div class="row title text-center" style="padding-top: 20px; padding-bottom: 20px">
        <h1 class="shadowdeep" style="color: white">Adult Gambling Behaviors in Indiana</span></h1>
        <h1 class="shadowdeep" style="color: white"><?= getCurrentYear(); ?> Survey Results</span></h1>
    </div>
    <div style="max-width: 1000px; margin: 20px auto;">
        <div class="row">
            <div class="col-md-3">
                <h2 style="color:#767676">Contact Us</h2>
            </div>
            <div class="col-md-9">
                <p style="font-size: 1.5em">For inquiries regarding the Adult Gambling Behaviors Survey or the Data Explorer website, please contact
                    <a href="mailto:ipgap@indiana.edu">ipgap@indiana.edu</a></p>
            </div>
        </div>
    </div>
</div>
<?php include_footer(); ?>
</body>
</html>