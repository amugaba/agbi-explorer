<?php
/**
 * Class Answer
 *
 * Each Variable has multiple Answers. An answer is a single response option to a question.
 * For example: "8th grade" is an answer to "What grade are you in?"
 *
 * In that case, the object will be structured like:
 * label = "8th grade"
 * code = 1 (b/c it is the first answer)
 * lowCutoff = null (the cutoffs for the Question are stored here for some reason)
 * counts = [~13000]
 * percents = [33.3]
 * totals = (is this even used?)
 *
 * If the Variable was crosstabulated by a grouping question, like "What grade are you in?" by "Gender", the data would be:
 * counts = [6500, 6500]
 * percents = [16.7, 16.7] (since there is a value for each group)
 */
class Answer
{
    public $label;
    public $code;
    public $lowCutoff;
    public $highCutoff;
    public $totalCutoff;

    public $counts;
    public $percents;
    public $totals;

    public function __construct()
    {
        $this->counts = array();
        $this->percents = array();
        $this->totals = array();
    }

    public function getCount($groupCode){
        return $this->counts[intval($groupCode)];
    }
    public function getPercent($groupCode){
        return $this->percents[intval($groupCode)];
    }

    public function addCount($groupCode, $num){
        $this->counts[intval($groupCode)] = floatval($num);
    }
    public function addPercent($groupCode, $num){
        $this->percents[intval($groupCode)] = floatval($num);
    }
    public function addTotal($groupCode, $num){
        $this->totals[intval($groupCode)] = floatval($num);
    }

    public function getCountArray(){
        $arr = ['answer' => $this->label];
        foreach ($this->counts as $key => $count)
            $arr["v$key"] = $count;
        return $arr;
    }
    public function getPercentArray(){
        $arr = ['answer' => $this->label];
        foreach ($this->percents as $key => $percent)
            $arr["v$key"] = $percent*100;
        return $arr;
    }
}