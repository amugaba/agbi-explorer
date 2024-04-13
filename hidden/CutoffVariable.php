<?php
/**
 * A CutoffVariable is a single question like "Lifetime Alcohol Use" where there are only two answers Positive or Negative
 * Cutoff points are used to determine which answers should be summed under Positive and which under Negative
 * The data for that variable would look like:
 * code = "A2A"
 * question = "On how many occasions have you had beer, wine, or hard liquor in your lifetime?"
 * summary = "Alcohol use, lifetime"
 * category = 1 (Alcohol)
 * lowCutoff = 2 (The answer "2", meaning "1-2 occasions", or higher constitutes a positive response)
 * highCutoff = null (All answers beyond "2" are Positive. Don't stop.)
 * totalCutoff = null (All answers should be included when summing the total.)
 */
require_once 'Variable.php';

class CutoffVariable extends Variable
{
    public function getCount($groupCode){
        return $this->counts[intval($groupCode)];
    }
    public function getPercent($groupCode){
        if($this->getTotal($groupCode) == 0)
            return 0;
        return $this->getCount($groupCode) / $this->getTotal($groupCode);
    }
    public function getTotal($groupCode){
        return $this->totals[intval($groupCode)];
    }
    public function getSumTotal(){
        return array_sum($this->totals);
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

    public function initializeCounts($groupVar){
        $groupLength = $groupVar == null ? 1 : count($groupVar->labels);
        for($j=0; $j < $groupLength; $j++) {
            $this->counts[$j] = 0;
            $this->totals[$j] = 0;
        }
    }

    public function calculatePercents(){
        for($i=0; $i < count($this->counts); $i++) {
            if($this->totals[$i] == 0)
                $this->percents[] = 0;
            else
                $this->percents[] = round($this->counts[$i] / $this->totals[$i] * 100, 1);
        }
    }
}