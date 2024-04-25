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
<body>
<?php include_header(); ?>
<div class="container" id="main">
    <div class="row title" style="padding-top: 20px; padding-bottom: 20px">
        <div class="col-sm-6" style="color: white">
            <div style="text-align: center; margin: 0 0 0 auto">
                <h1 class="shadowdeep" style="color: white; margin-top: 40px">Adult Gambling Behaviors in Indiana</span></h1>
                <h1 class="shadowdeep" style="color: white"><?= getCurrentYear(); ?> Survey Results</span></h1>
                <div style="margin-top: 40px">
                    <h2 style="max-width: 700px; margin: 0 auto">Generate custom graphs and data tables on the questions you find most interesting!</h2>
                    <a href="highlights.php" class="button-link" style="margin: 30px auto">Check Out the Highlights</a>
                    <p style="font-size: 22px">Or build your own graphs at:<br> <a href="graphs.php" class="text-link">Explore the Data</a> and
                        <a href="trends.php" class="text-link">Trends Over Time</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div style="text-align: left">
                <img src="img/spots_betting.jpg" alt="Spots betting" style="height: 500px">
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: 20px; max-width: 1050px; margin: 0 auto; font-size: 16px">
        <h1 style="text-align: center">Learn More About the Survey and Data Explorer</h1>
        <div class="grid">
            <div class="col-sm-4">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/tablet-graph.png" style="width: 100%">
                </div>
                <h2>Data Explorer Features</h2>
                <p><b><a href="highlights.php">Survey Highlights</a></b> shows selected results from various topics.</p>
                <p><b><a href="graphs.php">Explore the Data</a></b> lets you create a graph from any question in the survey.</p>
                <p><b><a href="trends.php">Trends Over Time</a></b> shows how survey responses vary by year.</p>
            </div>
            <div class="col-sm-4">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/keyboard-survey.jpg" style="width: 100%">
                </div>
                <h2>TBD</h2>
                <p>Maybe introduce the survey here.</p>
            </div>
            <div class="col-sm-4">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/calculator-graph.png" style="width: 100%">
                </div>
                <h2>TBD</h2>
                <p>What are some key items?</p>
            </div>
        </div>
    </div>
    <div style="max-width: 1050px; margin: 20px auto;">
        <div class="row">
            <div class="col-md-12">
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <h2 style="color:#767676">About the Survey</h2>
            </div>
            <div class="col-md-9" style="font-size: 16px">
                <p>The 2022 Adult Gambling Behaviors in Indiana survey is funded by the Indiana Division of Mental Health and Addiction and conducted by Prevention Insights at the Indiana University School of Public Health-Bloomington in fall 2022. The purpose of the survey was to assess the scope of gambling activities, the prevalence of problem gambling behaviors, and awareness of available problem gambling resources among Indiana adults.</p>
                <p>For more information, please see the <a href="https://ipgap.indiana.edu/resources-data/data-a-research.html" target="_blank">Survey Report</a> on the IPGAP Resources page.</p>
            </div>
        </div>
    </div>
</div>
<?php include_footer(); ?>
</body>
</html>