<?php
require_once "config/config.php";
require_once 'hidden/DataService.php';

//Get the YEAR and then instantiate the data service
$year = getInput('year') ? intval(getInput('year')) : getCurrentYear();
$ds = DataService::getInstance($year);
$graph = null;

if(getInput('q1') != null) {
    $graph = Graph::createExploreGraph($year, getInput('q1'), getInput('grp'), getInput('age'),
        getInput('gender'), getInput('race'), getInput('ethnicity'));
}

//Persist the category selections in the form
$cat1 = getInput('cat1');
$cat2 = getInput('cat2');

//Get variables and categories
$variables = $ds->getVariables();
$categories = $ds->getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Explore the Data - Adult Gambling Behaviors Survey</title>
    <?php include_styles();
          include_js(); ?>
</head>
<body>
<?php include_header(); ?>
<div class="container" id="main">
    <div class="row title">
        <form method="get" action="graphs.php">
        <div class="shadow" style="font-size: 22px; margin-top: 15px; color: white; text-align: center">
            Using year
            <select id="filterYear" name="year" style="height: 28px; font-size: 18px; padding-top: 1px; margin-left: 5px" class="selector" onchange="changeYear()" title="Change year drop down">
                <?php foreach (getAllYears() as $yearOption) {
                    echo "<option value='$yearOption'>$yearOption</option>";
                }?>
            </select>
        </div>
        <div class="searchbar">
            <label class="shadow" for="question1">1. Select primary question:</label>
            <select id="category1" name="cat1" style="width:160px" class="selector" title="Select category to filter primary question">
                <option value="" selected="selected">All categories</option>
                <?php foreach ($categories as $category) {
                    echo "<option value='$category->id'>$category->name</option>";
                }?>
            </select>
            <select id="question1" name="q1" class="searchbox">
                <option value="" selected="selected">Select a question</option>
            </select><br>
            <label class="shadow" for="question2">2. (Optional) Separate data by another question:</label>
            <select id="category2" name="cat2" style="width:160px" class="selector" title="Select category to filter secondary question">
                <option value="" selected="selected">All categories</option>
                <?php foreach ($categories as $category) {
                    echo "<option value='$category->id'>$category->name</option>";
                }?>
            </select>
            <select id="question2" name="grp" class="searchbox">
                <option value="" selected="selected">Select a question</option>
            </select><br>
            <label class="shadow">3. (Optional) Filter data by:</label>
            <select id="filterAge" name="age" class="filter selector hide6" title="Age Range">
                <option value="">Age Range</option>
                <option value="0">18 - 24</option>
                <option value="1">25 - 34</option>
                <option value="2">35 - 44</option>
                <option value="3">45 - 54</option>
                <option value="4">55 - 64</option>
                <option value="5">54 - 74</option>
                <option value="6">75+</option>
            </select>
            <select id="filterGender" name="gender" class="filter selector" title="Gender">
                <option value="">Gender</option>
                <option value="0">Male</option>
                <option value="1">Female</option>
            </select>
            <select id="filterRace" name="race" class="filter selector" title="Race">
                <option value="">Race</option>
                <option value="0">White</option>
                <option value="1">Black</option>
                <option value="2">Asian</option>
                <option value="3">Native Hawaiian / Pacific Islander</option>
                <option value="4">American Indian / Alaska Native</option>
                <option value="5">Other</option>
                <option value="6">More than 1 race</option>
            </select>
            <select id="filterEthnicity" name="ethnicity" class="filter selector" title="Ethnicity">
                <option value="">Ethnicity</option>
                <option value="0">Hispanic</option>
                <option value="1">Non-Hispanic</option>
            </select><br>
            <div style="text-align: center;">
                <input type="submit" value="Generate Graph" class="btn">
                <input type="button" value="Reset" class="btn" onclick="location.href = 'graphs.php'">
            </div>
        </div>
        </form>
    </div>
    <div class="row" style="margin: 10px auto; max-width: 1400px">
        <?php if($graph == null):
            include "instructions.php";
        else: ?>
            <div style="text-align: center;">
                <div id="graphTitle"></div>
            </div>

            <div id="chartDiv" style="width100%; height:<?= $graph->graphHeight;?>px;"></div>
            <div style="width: 100%; text-align: center" class="hideIfNoGraph">
                <input type="button" onclick="exportGraph()" value="Export to PDF" class="btn btn-blue">
            </div>

            <div style="text-align: center; margin-bottom: 20px;">
                <div class="h3">
                    Data Table
                    <div class="tipButton" data-toggle="tooltip" data-placement="top"
                         title="This table shows the number of people in each category. To save this data, click Export to CSV."></div>
                </div>
                <table id="datatable" class="datatable" style="margin: 0 auto; text-align: right; border:none"></table>
                <div>No Response: <?= number_format($graph->noResponse);?></div>
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
        ageFilter: null, genderFilter: null, raceFilter: null, ethnicityFilter: null
    }
    let filterString, year;

    $(function() {
        graph = <?= json_encode($graph); ?>;
        questions = <?= json_encode($variables); ?>;
        //get user inputs
        let cat1 = <?= json_encode($cat1); ?>;
        let cat2 = <?= json_encode($cat2); ?>;
        year = <?= json_encode($year); ?>;

        //persist user inputs in search form
        $('#filterYear').val(year);
        if(cat1 != null)
            $('#category1').val(cat1);
        if(cat2 != null)
            $('#category2').val(cat2);
        enableSelect2(questions, "#category1", "#question1");
        enableSelect2(questions, "#category2", "#question2", true);


        if(graph != null) {
            $('#question1').val(graph.mainVariable.code);
            $("#question1").trigger('change');
            if(graph.groupingVariable != null) {
                $('#question2').val(graph.groupingVariable.code);
                $("#question2").trigger('change');
            }
            $('#filterAge').val(graph.ageFilter);
            $('#filterGender').val(graph.genderFilter);
            $('#filterRace').val(graph.raceFilter);
            $('#filterEthnicity').val(graph.ethnicityFilter);

            createBarGraph(graph.percentData, graph.mainVariable.question, graph.groupingVariable?.question,
                graph.groupingVariable?.labels || ['Total'], null, graph.mainVariable.summary);

            if(graph.groupingVariable == null)
                createSimpleExplorerTable($('#datatable'), graph.mainVariable.labels, graph.mainVariable.counts, graph.sumTotal);
            else
                createCrosstabExplorerTable($('#datatable'), graph.mainVariable.summary, graph.groupingVariable.summary,
                    graph.mainVariable.labels, graph.groupingVariable.labels, graph.mainVariable.counts,
                    graph.sumPositives, graph.mainVariable.totals, graph.sumTotal);

            filterString = makeFilterString(graph.ageFilter, graph.genderFilter, graph.raceFilter, graph.ethnicityFilter);
            let titleString = "<h4>"+graph.year+"</h4><h4>"+graph.mainVariable.question+"</h4>";
            if(graph.groupingVariable != null)
                titleString += "<i>compared to</i><h4>" + graph.groupingVariable.question + "</h4>";
            if(filterString != null)
                titleString += "<i>" + filterString + "</i>";
            $("#graphTitle").html(titleString);
        }

        $('[data-toggle="tooltip"]').tooltip();
    });
    function exportCSV() {
        if(graph.groupingVariable == null)
            simpleExplorerCSV(graph.mainVariable.question, graph.mainVariable.labels,
                graph.mainVariable.counts, graph.mainVariable.totals, year, filterString);
        else
            crosstabExplorerCSV(graph.mainVariable.question, graph.groupingVariable.question, graph.mainVariable.labels, graph.groupingVariable.labels,
                graph.mainVariable.counts, graph.sumPositives, graph.mainVariable.totals, graph.sumTotal,
                filterString, year);
    }
    function exportGraph() {
        exportToPDF(chart, graph.mainVariable.question, graph.groupingVariable?.question, year, filterString);
    }
    function changeYear() {
        window.location.href = "graphs.php?year="+$("#filterYear").val();
    }
</script>
</body>
</html>