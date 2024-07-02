<?php
include_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Adult Gambling Behaviors Survey</title>
    <?php include_styles() ?>
</head>
<body class="has-banner has-page-title landmarks" id="home">
<?php include_header(); ?>
<div class="bg-midnight-dark section wide" id="content" style="margin-top: 0">
    <div class="row">
        <div class="layout">
            <div class="one-half float-left text-center">
                <h2 class="shadowdeep" style="color: white"><?= getCurrentYear(); ?> Survey Results</span></h2>
                <div class="callout" style="margin-top: 40px; color: white">
                    <p style="line-height: 1.5">Generate custom graphs and data tables on the questions you find most interesting!</p>
                    <p style="line-height: 1.5"><a href="highlights.php" class="button invert" style="font-size: 20px">Check Out the Highlights</a></p>
                    <p style="line-height: 1.5">Or build your own graphs at<br> <a href="graphs.php" class="button invert" style="font-size: 20px">Explore the Data</a></p>
                </div>
            </div>
            <div class="one-half float-right image text-center">
                <img src="img/sports_betting.jpg" alt="Sports betting">
            </div>
        </div>
    </div>
</div>
<div class="bg-none section wide">
    <div class="row">
        <div class="layout">
            <h2 class="section-title text-center">Learn More About the Survey and Data Explorer</h2>
            <div class="one-third float-left">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/tablet-graph.png" style="width: 100%">
                </div>
                <h3>Data Explorer Features</h3>
                <p><b><a href="highlights.php">Survey Highlights</a></b> shows selected results from various topics.</p>
                <p><b><a href="graphs.php">Explore the Data</a></b> lets you create a graph from any question in the survey.</p>
            </div>
            <div class="text one-third float-left">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/keyboard-survey.jpg" style="width: 100%">
                </div>
                <h3>Key Survey Items</h3>
                <p>The survey includes the 9-item Pathological Gambling Diagnostic Form (DSM-V), the 17-item NORC Diagnostic Screen for Gambling Problems (NODS),
                    and the 9-item Problem Gambling Severity Index (PGSI).</p>
            </div>
            <div class="text one-third float-right">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/calculator-graph.png" style="width: 100%">
                </div>
                <h3>Trends Coming Soon!</h3>
                <p>Trends data will be available as soon as the next year's survey is added. With trends, you can track data across multiple survey years.</p>
            </div>
        </div>
    </div>
</div>
<div class="bg-none section wide" style="padding-top: 0">
    <div class="row">
        <hr>
        <h2 style="color:#767676">About the Survey</h2>
        <div class="text">
            <p>The 2022 Adult Gambling Behaviors in Indiana survey is funded by the Indiana Division of Mental Health and Addiction and conducted by Prevention Insights at the Indiana University School of Public Health-Bloomington in fall 2022. The purpose of the survey was to assess the scope of gambling activities, the prevalence of problem gambling behaviors, and awareness of available problem gambling resources among Indiana adults.</p>
            <p>For more information, please see the <a href="https://ipgap.indiana.edu/resources-data/data-a-research.html" target="_blank">Survey Report</a> on the IPGAP Resources page.</p>
        </div>
    </div>
</div>
<?php include_footer();
include_js();
?>
</body>
</html>