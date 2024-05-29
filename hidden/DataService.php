<?php
/**
 * Provide service function to access data from database
 */
require_once 'ConnectionManager.php';
require_once 'CutoffVariable.php';
require_once 'MultiVariable.php';
require_once 'Category.php';
require_once 'Graph.php';

class DataService {

    public mysqli|null|false $connection;
    protected static DataService $instance;
    protected string $last_raw_statement;
    protected ?array $last_params;
    protected string $last_full_statement;

    private string $datatable;
    private int $year;

    protected function __construct ()
    {
        $cm = new ConnectionManager();
        $this->connection = mysqli_connect($cm->server, $cm->username, $cm->password, $cm->databasename, $cm->port);
        $this->connection->set_charset('utf8');
        $this->throwExceptionOnError();
    }

    /** @param $year int
     *  @param $grade string
     *  @return DataService */
    public static function getInstance(int $year): DataService
    {
        if(!isset(DataService::$instance))
            DataService::$instance = new DataService();
        DataService::$instance->datatable = 'data_'.$year;
        DataService::$instance->year = $year;
        return DataService::$instance;
    }

    /**
     * @param string $code
     * @return CutoffVariable|null
     * @throws Exception
     */
    public function getCutoffVariable(string $code): ?CutoffVariable
    {
        if($code == null)
            return null;

        $result = $this->query("SELECT id, year, code, question, cutoff_summary, cutoff_tooltip, category, low_cutoff, high_cutoff, total_cutoff 
            FROM variables WHERE year=? AND code=?",
            [$this->year, $code]);
        return $this->fetchObject($result, CutoffVariable::class);
    }

    /**
     * @param ?string $code
     * @return MultiVariable|null
     * @throws Exception
     */
    public function getVariable(?string $code): ?MultiVariable
    {
        if($code == null)
            return null;

        $result = $this->query("SELECT * FROM variables WHERE year=? AND code=?",
            [$this->year, $code]);
        $variable = $this->fetchObject($result, MultiVariable::class);

        //Get Answers to the Question
        $result = $this->query("SELECT answer0, answer1,answer2,answer3,answer4,answer5,answer6,answer7,answer8,answer9,
        answer10,answer11,answer12,answer13,answer14,answer15 FROM variables WHERE id=?", [$variable->id]);

        //add answer labels to Question
        $labels = $result->fetch_row();
        foreach ($labels as $label) {
            if ($label != null && $label != '')
                $variable->labels[] = $label;
        }

        return $variable;
    }

    /**
     * Get all variables for the currently selected year
     * @return array
     * @throws Exception
     */
    public function getVariables() : array
    {
        $result = $this->query("SELECT code, question, summary, category FROM variables
            WHERE year=? ORDER BY id", [$this->year]);
        return $this->fetchAllObjects($result, MultiVariable::class);
    }

    /**
     * Get all variables for the currently selected year
     * @return array
     * @throws Exception
     */
    public function getTrendVariables() : array
    {
        $result = $this->query("SELECT code, question, summary, category FROM variables
            WHERE has_cutoff=1 AND year=? ORDER BY id", [$this->year]);
        return $this->fetchAllObjects($result, MultiVariable::class);
    }

    /**
     * Get all variables in this trend, one for each year
     * @param string $var_code
     * @return CutoffVariable[]
     * @throws Exception
     */
    public function getVariablesInTrend(string $var_code): array
    {
        $result = $this->query("SELECT id, year, code, question, cutoff_summary, cutoff_tooltip, category, low_cutoff, high_cutoff, total_cutoff 
            FROM variables WHERE code=? ORDER BY year",
            [$var_code]);
        return $this->fetchAllObjects($result, CutoffVariable::class);
    }

    /**
     * Was this variable collected this survey year?
     * @param $code string
     * @return bool
     * @throws Exception
     */
    public function isVariableInData(string $code): bool
    {
        $result = $this->query("SELECT 1 FROM variables WHERE year=? AND code=?",[$this->year, $code]);
        if($result->fetch_row())
            return true;
        return false;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function isUnweighted(string $code): bool
    {
        return false;
        //return in_array($code, ['Q_pers1','Q_pers2','Q_pers3','Q_pers4','Q_pers5','Q_pers6','Q_pers7','Q_pers8','Q_pers9','Q_pers10','Race','race_merge']);
    }

    /**
     * Get the categories that appear in the current year
     * @return Category[]
     * @throws Exception
     */
    public function getCategories(): array {
        $result = $this->query("SELECT * FROM categories 
            WHERE id IN (SELECT category FROM variables WHERE year=? GROUP BY category) 
            ORDER BY display_order", [$this->year]);
        return $this->fetchAllObjects($result, Category::class);
    }

    /**
     * Get the categories that appear in the current year
     * @return Category[]
     * @throws Exception
     */
    public function getTrendCategories(): array {
        $result = $this->query("SELECT * FROM categories 
            WHERE id IN (SELECT category FROM variables WHERE year=? AND has_cutoff=1 GROUP BY category) 
            ORDER BY display_order", [$this->year]);
        return $this->fetchAllObjects($result, Category::class);
    }

    /**
     * Get the weighted count of respondents that chose each answer for the given question.     *
     * @param MultiVariable $mainVar
     * @param MultiVariable $groupVar
     * @param string $filter
     */
    public function getMultiPositives($mainVar, $groupVar, $filter)
    {
        $varcode = $mainVar->code;

        //don't use weighting for demographics questions
        if($this->isUnweighted($mainVar->code))
            $counter = "COUNT(1)";
        else
            $counter = "COALESCE(SUM(Wgt),0)";

        if ($groupVar != null) {
            $groupcode = $groupVar->code;
            $stmt = $this->query("SELECT $counter as num, $varcode as answer, $groupcode as subgroup 
            FROM $this->datatable 
            WHERE $varcode IS NOT NULL AND $groupcode IS NOT NULL AND $filter 
            GROUP BY $varcode, $groupcode");
        } else {
            $stmt = $this->query("SELECT $counter as num, $varcode as answer 
            FROM $this->datatable 
            WHERE $varcode IS NOT NULL AND $filter 
            GROUP BY $varcode");
        }

        while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
            $subgroup = $groupVar == null ? 0 : $row['subgroup'];
            $mainVar->addCount($row['answer'], $subgroup, $row['num']);
        }
    }

    /**
     * Get the total number of respondents that answered the given question (non-null response).
     * @param MultiVariable $mainVar
     * @param MultiVariable $groupVar
     * @param string $filter
     */
    public function getMultiTotals($mainVar, $groupVar, $filter)
    {
        $varcode = $mainVar->code;

        //don't use weighting for demographics questions
        if($this->isUnweighted($mainVar->code))
            $counter = "COUNT(1)";
        else
            $counter = "COALESCE(SUM(Wgt),0)";

        if($groupVar != null)
        {
            $groupcode = $groupVar->code;
            $stmt = $this->connection->query("SELECT $counter as num, $groupcode as subgroup 
                FROM $this->datatable 
                WHERE $groupcode IS NOT NULL AND $filter AND $varcode IS NOT NULL 
                GROUP BY $groupcode");
        }
        else {
            $stmt = $this->connection->query("SELECT $counter as num 
                FROM $this->datatable 
                WHERE $filter AND $varcode IS NOT NULL");
        }
        $this->throwExceptionOnError();

        while($row = $stmt->fetch_array(MYSQLI_ASSOC)){
            $subgroup = $groupVar == null ? 0 : $row['subgroup'];
            $mainVar->addTotal($subgroup, $row['num']);
        }
    }

    /**Get the number of respondents that selected an answer within the cutoff points.
     * @param CutoffVariable $variable
     * @param Variable|null $groupVar
     * @param string $filter    */
    public function getCutoffPositives(CutoffVariable $variable, ?Variable $groupVar, string $filter)
    {
        $cutoffQuery = "1";
        if($variable->low_cutoff !== null) {
            $cutoffQuery .= " AND $variable->code >= $variable->low_cutoff";
        }
        if($variable->high_cutoff !== null) {
            $cutoffQuery .= " AND $variable->code <= $variable->high_cutoff";
        }

        if($groupVar != null) {
            $result = $this->query("SELECT COALESCE(SUM(Wgt),0) as num, $groupVar->code as subgroup
                FROM $this->datatable 
                WHERE $groupVar->code IS NOT NULL AND $cutoffQuery AND $filter
                GROUP BY $groupVar->code");
        }
        else {
            $result = $this->query("SELECT COALESCE(SUM(Wgt),0) as num
                FROM $this->datatable 
                WHERE $cutoffQuery AND $filter");
        }

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $subgroup = $groupVar == null ? 0 : $row['subgroup'];
            $variable->addCount($subgroup, $row['num']);
        }
    }

    /**Get the total number of respondents, subject to the total cutoff.
     * @param CutoffVariable $variable
     * @param Variable|null $groupVar
     * @param string $filter    */
    public function getCutoffTotal(CutoffVariable $variable, ?Variable $groupVar, string $filter)
    {
        $cutoffQuery = "1";
        if($variable->total_cutoff !== null) {
            $cutoffQuery .= " AND $variable->code >= $variable->total_cutoff";
        }

        if($groupVar != null) {
            $result = $this->query("SELECT COALESCE(SUM(Wgt),0) as num, $groupVar->code as subgroup
                FROM $this->datatable 
                WHERE $variable->code IS NOT NULL AND $groupVar->code IS NOT NULL AND $cutoffQuery AND $filter
                GROUP BY $groupVar->code");
        }
        else {
            $result = $this->query("SELECT COALESCE(SUM(Wgt),0) as num
                FROM $this->datatable 
                WHERE $variable->code IS NOT NULL AND $cutoffQuery AND $filter");
        }

        while($row = $result->fetch_array(MYSQLI_ASSOC)){
            $subgroup = $groupVar == null ? 0 : $row['subgroup'];
            $variable->addTotal($subgroup, $row['num']);
        }
    }

    /**
     * Get the total number of respondents that did not answer one of the questions (null response).
     * @param MultiVariable $mainVar
     * @param MultiVariable $groupVar
     * @param string $filter
     */
    public function getNoResponseCount($mainVar, $groupVar, $filter)
    {
        $varcode = $mainVar->code;

        //don't use weighting for demographics questions
        if($this->isUnweighted($mainVar->code))
            $counter = "COUNT(1)";
        else
            $counter = "COALESCE(SUM(Wgt),0)";

        if($groupVar != null)
        {
            $groupcode = $groupVar->code;
            $stmt = $this->connection->query("SELECT $counter as num FROM $this->datatable 
                WHERE ($varcode IS NULL OR $groupcode IS NULL) AND $filter");
        }
        else {
            $stmt = $this->connection->query("SELECT $counter as num FROM $this->datatable 
                WHERE ($varcode IS NULL) AND $filter");
        }
        $this->throwExceptionOnError();

        return $stmt->fetch_row()[0];
    }

    /**
     * @param int|null $age
     * @param int|null $gender
     * @param int|null $race
     * @param int|null $income
     * @return string
     */
    public function createFilterString(?int $age, ?int $gender, ?int $race, ?int $income): string
    {
        $filter = " 1 ";
        if ($age !== null)
            $filter .= " AND Q_pers3 = ".$this->connection->real_escape_string($age);
        if ($gender !== null)
            $filter .= " AND Q_pers9 = ".$this->connection->real_escape_string($gender);
        if ($race !== null)
            $filter .= " AND race_merge = ".$this->connection->real_escape_string($race);
        if ($income !== null)
            $filter .= " AND Q_pers7 = ".$this->connection->real_escape_string($income);
        return $filter;
    }

    /**Run mysql query after escaping input
     * @param string $stmt
     * @param array|null $params
     * @return mysqli_result
     * @throws Exception
     */
    protected function query(string $stmt, array $params = null): mysqli_result
    {
        $this->last_raw_statement = $stmt;
        $this->last_params = $params;
        $this->last_full_statement = 'Unassigned';

        if($params != null) {
            for($i=0; $i<count($params); $i++) {
                $val = $params[$i];
                if($val === null)
                    $val = 'NULL';
                if($val === true)
                    $val = 1;
                if($val === false)
                    $val = 0;
                $params[$i] = $this->connection->real_escape_string($val);
            }
            $this->last_params = $params;
            $positions = array();
            $lastPos = 0;

            while (($lastPos = strpos($stmt, '?', $lastPos))!== false) {
                $positions[] = $lastPos;
                $lastPos = $lastPos + 1;
            }
            if(count($positions) != count($params))
                throw new Exception("Unequal number of paramaters in Query: $stmt ||| ".count($positions)." expected, ".count($params)." received");

            //replace all ? marks starting from the end of the string
            for($i=count($positions)-1; $i>=0; $i--) {
                if($params[$i] === 'NULL')
                    $stmt = substr($stmt, 0, $positions[$i]) . 'NULL' . substr($stmt, $positions[$i] + 1);
                else
                    $stmt = substr($stmt, 0, $positions[$i]) ."'". $params[$i] ."'". substr($stmt, $positions[$i] + 1);
            }
        }

        $this->last_full_statement = $stmt;
        $result = $this->connection->query($stmt);
        $this->throwExceptionOnError();
        return $result;
    }

    /**
     * Fetch all rows from the query and map its values to the fields in the given class
     * @param mysqli_result $result
     * @param string $class
     * @return array
     */
    protected function fetchAllObjects(mysqli_result $result, string $class): array
    {
        $objs = [];
        $type_map = $this->getTypeMap($result);
        while($row = $result->fetch_object()) {
            $obj = new $class;
            foreach($row as $key => $value) {
                $obj->$key = $this->convertDataType($value, $type_map[$key]);
            }
            $objs[] = $obj;
        }
        $result->free_result();
        return $objs;
    }

    /**
     * Fetch one row from the query and map its values to the fields in the given class
     * @param mysqli_result $result
     * @param string $class
     * @return mixed Returns null if no rows in result set.
     */
    protected function fetchObject(mysqli_result $result, string $class): mixed
    {
        $type_map = $this->getTypeMap($result);
        if($row = $result->fetch_object()) {
            $obj = new $class;
            foreach($row as $key => $value) {
                $obj->$key = $this->convertDataType($value, $type_map[$key]);
            }
            $result->free_result();
            return $obj;
        }
        $result->free_result();
        return null;
    }

    /**
     * Get the types of the columns in the query
     * @param $result mysqli_result
     * @return array
     */
    protected function getTypeMap(mysqli_result $result): array
    {
        $map = [];
        $fields = $result->fetch_fields();
        foreach($fields as $field) {
            $map[$field->name] = $field->type;
        }
        return $map;
    }

    /**
     * Convert data returned by a MySQL query from a string to the type defined by the MySQL column
     * @param $val string|null
     * @param $type int
     * @return float|int|string|null
     */
    protected function convertDataType(?string $val, int $type): float|int|string|null
    {
        if($val == null)
            return null;
        if(in_array($type, [1,2,3,8,9,16])) //tinyint, smallint, int, bigint, mediumint
            return intval($val);
        if(in_array($type, [4,5,246])) //float, double, decimal
            return floatval($val);
        return $val;
    }

    /**
     * Utility function to throw an exception if an error occurs while running a mysql command.
     * @throws Exception
     */
    protected function throwExceptionOnError ()
    {
        if (mysqli_error($this->connection)) {
            $msg = '<b>MySQL Error ' . mysqli_errno($this->connection) . ":</b> " . mysqli_error($this->connection) . '<br>';
            if(isset($this->last_full_statement)) {
                $msg .= '<b>Full statement:</b> ' . $this->last_full_statement . '<br>'
                    . '<b>Raw statement:</b> ' . $this->last_raw_statement . '<br>'
                    . '<b>Parameters:</b> [' . ($this->last_params==null ? '' : implode(', ', $this->last_params)) . ']';
            }
            throw new Exception($msg);
        }
    }
}