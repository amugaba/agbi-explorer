<?php
include_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - Indiana Youth Survey</title>
    <?php include_styles() ?>
</head>
<body>
<?php include_header(); ?>
<div class="container" id="main">
    <div class="row title" style="padding-top: 20px; padding-bottom: 20px">
        <div class="col-sm-6" style="color: white">
            <div style="text-align: center; margin: 0 0 0 auto">
                <div style="margin-top: 30px;">
                    <img src="img/inys-logo.png"  alt="Indiana Youth Survey Logo">
                </div>
                <h1 class="shadowdeep" style="margin-top: 20px">2022 Interactive Data Explorer</h1>
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
                <img src="img/older-kids.jpg" alt="High school kids smiling">
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
                <h2>New 2022 Survey Items</h2>
                <p>The <b>7th-12th grade survey</b> differs from the 2020 survey in the following ways:</p>
                <ul>
                    <li>Questions on the perceived risk and wrongness of using heroin and methamphetamines have been added.</li>
                    <li>The survey now contains questions on how students acquired marijuana.</li>
                    <li>There are new response options for how students acquired alcohol.</li>
                </ul>
                <p>For the <b>6th grade survey</b>, all questions have remained the same.</p>
            </div>
            <div class="col-sm-4">
                <div class="figure" style="margin-bottom: 10px">
                    <img alt="Open books" src="img/younger-kids.jpg" style="width: 100%">
                </div>
                <h2>6th Grade Survey</h2>
                <p>The Indiana Youth Survey is administered in two forms: one for 7th to 12th grade students, and another for 6th grade students.</p>
                <p>You can access the <b>6th grade data set</b> by selecting '6th grade' at the top of
                    <b><a href="highlights.php?ds=6th">Survey Highlights</a></b> or <b><a href="graphs.php?ds=6th">Explore the Data</a></b>.</p>
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
                <p>The Indiana Youth Survey has been serving Indiana schools and communities since 1991. The survey is administered in the spring
                    of even-numbered years, free of charge, in any Indiana school that wishes to participate. The self-report survey asks students
                    in grades 6-12 a variety of questions about substance use, mental health, gambling, and potential risk and protective factors for these behaviors.</p>
                <p>For more information, please see the <a href="https://inys.indiana.edu/about-survey" target="_blank">About the INYS</a> page.</p>
            </div>
        </div>
    </div>
</div>
<?php include_footer(); ?>
</body>
</html>