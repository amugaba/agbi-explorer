<?php
require_once "config/config.php";
require_once 'hidden/DataService.php';
require_once 'hidden/CategoryData.php';

//Get the YEAR and then instantiate the data service
$year = getInput('year') ? intval(getInput('year')) : getCurrentYear();
$ds = DataService::getInstance($year);

$category = getInput('cat') ?? 1;
$group = getInput('grp');
$highlightGroup = getHighlightGroup($category, $year);

$graph = Graph::createHighlightsGraph($year, $category, $group);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Highlights - Adult Gambling Behaviors Survey</title>
    <?php include_styles(); ?>
</head>
<body class="has-banner has-page-title landmarks" id="home">
<?php include_header(); ?>
<div class="bg-midnight-dark section wide" id="content">
    <div class="row">
        <div class="layout text-center">
            <h3 style="color: white">Select a Year and Category to See Highlights</h3>
            <select id="yearSelect" class="selector" onchange="changeYear(this.value)" title="Change year drop down">
                <?php foreach (getAllYears() as $yearOption) {
                    echo "<option value='$yearOption'>$yearOption</option>";
                }?>
            </select>
            <select id="categorySelect" class="selector" onchange="changeCategory(this.value)" style="margin-left: 5px">
                <option value="1">Gambling Related Experiences</option>
                <option value="2">Gambling Problems</option>
                <option value="3">Gambling Consequences</option>
                <option value="4">Services for Problem Gambling</option>
                <option value="5">Other Health Concerns</option>
            </select>
        </div>
    </div>
</div>
<div class="bg-none section wide">
    <div class="row">
        <div class="layout">
            <div style="text-align: center;">
                <h2 id="graphTitle"></h2>
                <p class="hideIfNoGraph"><b>Mouse over</b> the graph's labels and bars to see in more detail what each element represents.</p>
                <div class="showIfNoGraph" style="font-size: 1.3em; margin-top: 20px; display: none">
                    The survey did not ask about this topic in <?php echo $year ?>. Please select a different year or different topic.
                </div>
            </div>

            <div class="groupbox hideIfNoGraph" style="width:600px; margin: 20px auto 0">
                <span style="font-weight: bold; margin-right: 10px">Group data by:</span>
                <div id="grouping">
                    <input id="none" name="grouping" type="radio" value="" checked="checked"/><label for="none">None</label>
                    <input id="Q_pers3" name="grouping" type="radio" value="Q_pers3"/><label for="Q_pers3">Age Range</label>
                    <input id="Q_pers9" name="grouping" type="radio" value="Q_pers9"/><label for="Q_pers9">Gender</label>
                    <input id="race_merge" name="grouping" type="radio" value="race_merge"/><label for="race_merge">Race</label>
                    <input id="Q_pers7" name="grouping" type="radio" value="Q_pers7"/><label for="Q_pers7">Income</label>
                </div>
                <div class="tipButton" style="margin:0 0 -5px 17px"  data-toggle="tooltip" data-placement="top" title="You can separate respondents by age, gender, race, or income to see how each group answered."></div>
            </div>
            <div id="chartDiv" style="width100%; height:<?php echo $graph->graphHeight;?>px;"></div>
            <div style="width: 100%; text-align: center" class="hideIfNoGraph">
                <input type="button" onclick="exportGraph()" value="Export to PDF" class="button invert">
            </div>

            <div style="text-align: center; margin-bottom: 20px;" class="hideIfNoGraph">
                <div class="h3">
                    Data Table
                    <div class="tipButton" data-toggle="tooltip" data-placement="top"
                         title="This table shows the number of people in each category. To save this data, click Export to CSV."></div>
                </div>
                <table id="datatable" class="datatable" style="margin: 0 auto; text-align: right; border:none">
                </table>
                <input type="button" onclick="exportCSV()" class="button invert" value="Export to CSV" style="margin-top: 10px">
            </div>
        </div>
    </div>
</div>
<?php include_footer();
include_js(); ?>
<script>
    //Inputs, used to set links
    let year = <?php echo json_encode($year); ?>;
    let category = <?php echo json_encode($category); ?>;
    let group = <?php echo json_encode($group); ?>;

    $(function() {
        graph = <?= json_encode($graph); ?>;
        mainTitle = <?php echo json_encode($highlightGroup->title); ?>;

        if(graph.percentData.length > 0) {
            createBarGraph(graph.percentData, mainTitle, graph.groupingVariable?.summary, graph.groupingVariable?.labels || ['Total'], graph.tooltips, null, true, group === 'grade');

            if (graph.groupingVariable == null)
                createSimpleHighlightTable($('#datatable'), graph.mainVariable.labels, graph.mainVariable.counts, graph.sumTotals);
            else
                createCrosstabHighlightTable($('#datatable'), mainTitle, graph.groupingVariable.summary, graph.mainVariable.labels, graph.groupingVariable.labels, graph.mainVariable.counts, graph.sumPositives, graph.sumTotals, group === 'grade');
        }
        else {
            $(".hideIfNoGraph").hide();
            $(".showIfNoGraph").show();
        }

        $("#graphTitle").html(year + " Highlights: " + mainTitle);
        $('#grouping :input[value='+group+']').prop("checked",true);
        $('#yearSelect').val(year);
        $('#categorySelect').val(category);
        $('#grouping').buttonset();
        $('#grouping :input').click(function() {
            window.location = generateHighlightLink(year, category, this.value);
        });
        $('[data-toggle="tooltip"]').tooltip();

        //set category links, preserve year, reset grouping
        $('.categories li a').each(function(){
            $(this).attr('href', generateHighlightLink(year, $(this).data('category'), null));
        });
    });
    function changeYear(yr) {
        window.location = generateHighlightLink(yr, category, group);
    }
    function changeCategory(cat) {
        window.location = generateHighlightLink(year, cat, group);
    }
    function exportCSV() {
        if(graph.groupingVariable == null)
            simpleHighlightCSV(mainTitle, graph.mainVariable.labels, graph.mainVariable.counts, graph.sumTotals, year);
        else
            crosstabHighlightCSV(mainTitle, graph.groupingVariable.summary, graph.mainVariable.labels, graph.groupingVariable.labels, graph.mainVariable.counts, graph.sumPositives, graph.sumTotals, year);
    }
    function exportGraph() {
        exportToPDF(chart, mainTitle, graph.groupingVariable?.summary, year, null);
    }
    //create a link to highlights page based on current year, category, and group variables
    function generateHighlightLink(yr, cat, grp){
        return "highlights.php?year="+yr+"&cat="+(cat ?? '')+"&grp="+(grp ?? '');
    }
</script>
</body>
</html>