<?php
require_once "Trend.php";

class Graph
{
    private DataService $ds;
    public int $year;
    public Variable $mainVariable;
    public ?Variable $groupingVariable;
    public ?int $gradeFilter;
    public ?int $raceFilter;
    public ?int $genderFilter;
    public ?int $regionFilter;
    public array $percentData; //the data structure used by AmCharts to generate a graph
    public int $graphHeight; //height of the graph in pixels
    public int $noResponse; //number of surveys that didn't answer this question
    public int $sumTotal; //number of surveys that did answer the question
    public array $sumPositives; //number of surveys that answered positively

    public string $title;
    public ?string $trendName = null;
    public ?int $trendGroup = null;
    public array $yearsInGraph;
    public array $labels;
    public string $notes;

    public array $sumTotals;
    public array $tooltips;

    /**
     * @param int $year
     */
    public function __construct(int $year)
    {
        $this->ds = DataService::getInstance($year);
        $this->year = $year;
    }

    private function getVariableAndLoadData(string $code) {
        $filter = 1;
        $variable = $this->ds->getCutoffVariable($code);
        $variable->initializeCounts($this->groupingVariable);
        $this->ds->getCutoffPositives($variable, $this->groupingVariable, $filter);
        $this->ds->getCutoffTotal($variable, $this->groupingVariable, $filter);
        $variable->calculatePercents();
    }

    public static function createHighlightsGraph(int $year, ?int $category, ?string $groupCode) : ?Graph
    {
        $graph = new Graph($year);
        $ds = DataService::getInstance($year);

        $graph->groupingVariable = $ds->getVariable($groupCode);
        $variablesInGraph = [];
        $filter = "1";

        $highlightGroup = getHighlightGroup($category, $year);

        //get data for each question
        for($i = 0; $i < count($highlightGroup->codes); $i++)
        {
            $variable = $ds->getCutoffVariable($highlightGroup->codes[$i]);

            //for the CTC scales questions, remove the "High risk from" or "Low protection from" text
            if($category === 11)
                $variable->cutoff_summary = ucfirst(str_replace('High risk from ','',$variable->cutoff_summary));
            if($category === 12)
                $variable->cutoff_summary = ucfirst(str_replace('Low protection from ','',$variable->cutoff_summary));

            $variable->initializeCounts($graph->groupingVariable);
            $ds->getCutoffPositives($variable, $graph->groupingVariable, $filter);
            $ds->getCutoffTotal($variable, $graph->groupingVariable, $filter);
            $variable->calculatePercents();
            $variablesInGraph[] = $variable;
        }

        //Create the data structure used by AmCharts for bar graphs
        //[['answer' => Var1 label, 'v0' => Group0 percent, 'v1' => Group1 percent, ...], ['answer' => Var 2 label, ...]]
        $graph->percentData = [];
        foreach ($variablesInGraph as $variable) {
            $percentArray['answer'] = $variable->cutoff_summary;
            for($i=0; $i<count($variable->counts); $i++) {
                $percentArray['v'.$i] = $variable->percents[$i];
            }
            $graph->percentData[] = $percentArray;
        }

        //Also create data for display in graph and table
        $graph->mainVariable = new Variable(); //create a dummy variable to store data
        $graph->labels = []; //labels for main variable
        $counts = []; //[[var1 counts], [var2 counts], ...] where [var1 counts] = [group1, group2, ...]
        $sumPositives = []; //sum positives/counts for a variable
        $variableTotals = []; //sum valid cases for a variable
        $tooltips = []; //mouse-over pop-ups to explain graph labels and bars

        foreach ($variablesInGraph as $variable) {
            $graph->mainVariable->labels[] = $variable->cutoff_summary;
            $graph->mainVariable->counts[] = $variable->counts;
            $graph->sumPositives[] = array_sum($variable->counts);
            $graph->sumTotals[] = array_sum($variable->totals);
            $graph->tooltips[] = $variable->cutoff_tooltip;
        }

        //height is (labels*(labels+spacing)*bar height + header height
        $numGroupLabels = ($graph->groupingVariable != null) ? count($graph->groupingVariable->labels) : 1;
        $graph->graphHeight = min(900,max(600,($numGroupLabels+1)*count($highlightGroup->codes)*30+100));
        return $graph;
    }

    public static function createTrendsGraph(?int $trendGroup, ?string $trendName, ?int $gradeFilter,
                                              ?int $raceFilter, ?int $genderFilter, ?int $regionFilter) : ?Graph
    {
        $graph = new Graph(getCurrentYear());
        $ds = DataService::getInstance(getCurrentYear());

        //Set up variables (either single question or group)
        $trendsInGraph = [];
        if($trendName != null) {
            $trend = new Trend($trendName);
            $graph->trendName = $trendName;
            $trend->addVariables($ds->getVariablesInTrend($trendName));
            $graph->title = end($trend->variablesByYear)->cutoff_summary; //use most recent year's info for title
            $trendsInGraph[] = $trend;
        }
        else {
            $groupCodes = getGroupCodes($trendGroup);
            $graph->trendGroup = $trendGroup;
            $graph->title = "Trend Group: " . getGroupName($trendGroup);
            foreach ($groupCodes as $code) {
                $trend = new Trend($code);
                $trend->addVariables($ds->getVariablesInTrend($code));
                $trendsInGraph[] = $trend;
            }
        }

        //Get data for each year
        $years = getAllYears(); //from config.php
        $graph->yearsInGraph = [];
        $graph->percentData = [];
        $filter = $graph->addFilter($gradeFilter, $raceFilter, $genderFilter, $regionFilter);

        //for each year, for each var
        foreach ($years as $year) {
            $ds = DataService::getInstance($year);
            $anyVariableUsed = false; //track if any trend in the graph has a variable this year. If not, don't include this year in the graph
            $yearData = ["answer" => $year];

            foreach ($trendsInGraph as $i => $trend) {
                //if this trend doesn't have a variable for this year, set its value to null
                if(!array_key_exists($year, $trend->variablesByYear))
                    $yearData['v'.$i] = null;
                //otherwise, get this year's variable, and calculate its values
                else {
                    $variable = $trend->variablesByYear[$year];
                    $ds->getCutoffPositives($variable, null, $filter);
                    $ds->getCutoffTotal($variable, null, $filter);
                    $yearData['v' . $i] = round($variable->getPercent(0) * 100, 1);
                    $anyVariableUsed = true;
                }
            }
            if($anyVariableUsed) {
                $graph->yearsInGraph[] = $year;
                $graph->percentData[] = $yearData;
            }
        }

        //get labels and counts for data table
        $graph->labels = [];
        $graph->notes = "";
        $tooltips = [];
        foreach ($trendsInGraph as $trend) {
            $graph->labels[] = end($trend->variablesByYear)->cutoff_summary;
            $graph->notes .= getQuestionNote($variable->code);
        }
        return $graph;
    }


    /**
     * @param int $year
     * @param string $mainVarCode
     * @param string|null $groupVarCode
     * @param int|null $gradeFilter
     * @param int|null $raceFilter
     * @param int|null $genderFilter
     * @param int|null $regionFilter
     * @return Graph|null
     * @throws Exception
     */
    public static function createExploreGraph(int $year, string $mainVarCode, ?string $groupVarCode, ?int $gradeFilter,
                                              ?int $raceFilter, ?int $genderFilter, ?int $regionFilter) : ?Graph
    {
        $graph = new Graph($year);
        //check if those variables are part of this year's dataset
        if(!($graph->ds->isVariableInData($mainVarCode) && ($groupVarCode == null || $graph->ds->getVariable($groupVarCode))))
            return null;

        $graph->mainVariable = $graph->ds->getVariable($mainVarCode);
        $graph->groupingVariable = $graph->ds->getVariable($groupVarCode);
        $filter = $graph->addFilter($gradeFilter, $raceFilter, $genderFilter, $regionFilter);
        $graph->mainVariable->initializeCounts($graph->groupingVariable);

        //Load data into main Variable
        $graph->ds->getMultiPositives($graph->mainVariable, $graph->groupingVariable, $filter);
        $graph->ds->getMultiTotals($graph->mainVariable, $graph->groupingVariable, $filter);
        $graph->mainVariable->calculatePercents();

        //Create the data structure used by AmCharts for bar graphs
        //[['answer' => Var1 label, 'v0' => Group0 percent, 'v1' => Group1 percent, ...], ['answer' => Var 2 label, ...]]
        $graph->percentData = [];
        $numGroupLabels = ($graph->groupingVariable != null) ? count($graph->groupingVariable->labels) : 1;
        for ($i=0; $i < count($graph->mainVariable->labels); $i++) {
            $percentArray['answer'] = $graph->mainVariable->labels[$i];
            for($j=0; $j < $numGroupLabels; $j++) {
                $percentArray['v'.$j] = $graph->mainVariable->percents[$i][$j];
            }
            $graph->percentData[] = $percentArray;
        }

        //Calculate other values for graph and data table
        $graph->graphHeight = min(900, max(600, ($numGroupLabels + 1) * count($graph->mainVariable->labels) * 30 + 100)); //height is (labels*(labels+spacing)*bar height + header height
        $graph->noResponse = $graph->ds->getNoResponseCount($graph->mainVariable, $graph->groupingVariable, $filter);
        $graph->sumTotal = $graph->mainVariable->getSumTotal();
        $graph->sumPositives = $graph->mainVariable->getSumPositives();

        return $graph;
    }

    private function addFilter(?int $gradeFilter, ?int $raceFilter, ?int $genderFilter, ?int $regionFilter) : string
    {
        $this->gradeFilter = $gradeFilter;
        $this->raceFilter = $raceFilter;
        $this->genderFilter = $genderFilter;
        $this->regionFilter = $regionFilter;
        return $this->ds->createFilterString($gradeFilter, $genderFilter, $raceFilter, $regionFilter);
    }
}