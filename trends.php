<?php
require_once "config/config.php";
require_once 'hidden/DataService.php';
require_once 'hidden/TrendGroups.php';

//Get the YEAR and then instantiate the data service
$year = getInput('year') ? intval(getInput('year')) : getCurrentYear();
$ds = DataService::getInstance($year);
$graph = null;

if(getInput('group') != null || getInput('question') != null) {
    $graph = Graph::createTrendsGraph(getInput('group'), getInput('question'), getInput('grade'),
        getInput('race_eth'), getInput('gender'), getInput('region'));
}

//Get variables and categories
$cat = getInput('cat');
$trendGroup = getInput('group');
$variables = $ds->getTrendVariables();
$categories = $ds->getTrendCategories();
$old_category = new Category();
$old_category->name = "Questions from Past Years";
$old_category->id = 99;
$categories[] = $old_category;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trends - Adult Gambling Behaviors Survey</title>
    <?php include_styles();
    include_js(); ?>
</head>
<body>
<?php include_header(); ?>
<div class="container" id="main">
    <div class="row title">
        <form method="get" action="trends.php">
            <div class="searchbar">
                <label class="shadow" for="question">1. Select a question:</label>
                <select id="category" name="cat" style="width:160px" class="selector" title="Select category to filter primary question">
                    <option value="" selected="selected">All categories</option>
                    <?php foreach ($categories as $category) {
                        echo "<option value='$category->id'>$category->name</option>";
                    }?>
                </select>
                <select id="question" name="question" class="searchbox">
                    <option value="" selected="selected">Select a question</option>
                </select><br>
                <label class="shadow">2. (Optional) Filter data by:</label>
                <select id="filterGrade" name="grade" class="filter selector hide6" title="Grade">
                    <option value="">Grade</option>
                    <option value="1">7th</option>
                    <option value="2">8th</option>
                    <option value="3">9th</option>
                    <option value="4">10th</option>
                    <option value="5">11th</option>
                    <option value="6">12th</option>
                </select>
                <select id="filterGender" name="gender" class="filter selector" title="Gender">
                    <option value="">Gender</option>
                    <option value="0">Male</option>
                    <option value="1">Female</option>
                </select>
                <select id="filterRace" name="race_eth" class="filter selector" title="Race/Ethnicity">
                    <option value="">Race/Ethnicity</option>
                    <option value="0">White</option>
                    <option value="1">Black</option>
                    <option value="2">Hispanic</option>
                    <option value="3">Other</option>
                </select><br>
                <div style="text-align: center;">
                    <input type="submit" value="Generate Graph" class="btn">
                    <input type="button" value="Reset" class="btn" onclick="location.href = 'trends.php'">
                </div>
            </div>
        </form>
    </div>
    <div class="row" style="margin: 10px auto; max-width: 1400px">
        <?php if($graph == null):
            include "trends-instructions.php";
        else: ?>
            <div style="text-align: center;">
                <div id="graphTitle"></div>
                <div class="showIfOneYearData" style="font-size: 1.3em; margin-top: 20px; display: none">
                    This variable was added in <?= getCurrentYear()?>. Trends will not be available until the next survey's results are published.
                </div>
                <div class="showIfNoData" style="font-size: 1.3em; margin-top: 20px; display: none">
                    Trends are not available for this item currently.
                </div>
            </div>

            <div id="chartDiv" style="width100%; height:700px;"></div>
            <div style="width: 100%; text-align: center" class="hideIfNoGraph">
                <input type="button" onclick="exportGraph()" value="Export to PDF" class="btn btn-blue">
            </div>

        <?php if(strlen($graph->notes) > 0) {
            echo "<div style='text-align: center'>
                    <p><b>**Note:</b> $graph->notes</p>
                  </div>";
        }
        ?>

            <div style="text-align: center; margin-bottom: 20px;" class="hideIfNoGraph">
                <div class="h3">
                    Data Table
                    <div class="tipButton" data-toggle="tooltip" data-placement="top"
                         title="This table shows the number of people in each category. To save this data, click Export to CSV."></div>
                </div>
                <table id="datatable" class="datatable" style="margin: 0 auto; text-align: right; border:none">
                </table>
                <input type="button" onclick="exportCSV()" value="Export to CSV" class="btn btn-blue" style="margin-top: 10px">
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include_footer(); ?>
<script>
    let graph = {
        mainVariable: { code:null, question:null, summary:null, labels:null, counts:null, totals:null },
        groupingVariable: {},
        percentData: null, noResponse: null, sumTotal: null, sumPositives: null,
        gradeFilter: null, genderFilter: null, raceFilter: null, regionFilter: null,
        trendName: null, trendGroup: null, yearsInGraph: null
    }
    let filterString, year;

    $(function() {
        graph = <?= json_encode($graph); ?>;
        questions = <?= json_encode($variables); ?>;
        //get user inputs
        let category = <?= json_encode($cat); ?>;
        year = <?= json_encode($year); ?>;
        let questionSelect = $('#question');

        //persist user inputs in search form
        if(category != null)
            $('#category').val(category);
        enableSelect2(questions, "#category", "#question");

        if(graph != null) {
            if(graph.trendName != null) {
                questionSelect.val(graph.trendName);
                questionSelect.trigger('change');
            }
            else if(graph.trendGroup != null) {
                groupSelect.val(graph.trendGroup);
            }

            $('#filterGrade').val(graph.gradeFilter);
            $('#filterGender').val(graph.genderFilter);
            $('#filterRace').val(graph.raceFilter);
            $('#filterRegion').val(graph.regionFilter);

            if(graph.yearsInGraph.length === 1) {
                $(".hideIfNoGraph").hide();
                $(".showIfOneYearData").show();
            }
            else if(graph.labels.length === 0) {
                $(".hideIfNoGraph").hide();
                $(".showIfNoData").show();
            }
            else {
                createLineChart(graph.percentData, graph.labels);
            }

            filterString = makeFilterString(graph.gradeFilter, graph.genderFilter, graph.raceFilter, graph.regionFilter);
            let titleString = "<h4>"+graph.title+"</h4>";
            if(filterString != null)
                titleString += "<i>" + filterString + "</i>";
            $("#graphTitle").html(titleString);

            simpleTrendTable($('#datatable'), graph.labels, graph.yearsInGraph, graph.percentData, "Years");
        }

        $('[data-toggle="tooltip"]').tooltip();
    });
    function exportCSV() {
        let title = graph.trendGroup != null ? graph.title : "Trends: " + graph.title;
        simpleTrendCSV(title, graph.labels, graph.yearsInGraph, graph.percentData, graph.yearsInGraph[0]+' to '+graph.yearsInGraph[graph.yearsInGraph.length-1], filterString, "Years");
    }
    function exportGraph() {
        exportToPDF(chart, graph.title, null, graph.yearsInGraph[0]+' to '+graph.yearsInGraph[graph.yearsInGraph.length-1], filterString);
    }
</script>
</body>
</html>