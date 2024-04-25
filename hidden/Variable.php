<?php
/**
 * Parent class of CutoffVariable and MultiVariable.
 * A Variable is a question that has some number of answers.
 */
class Variable
{
	public int $id;
    public int $year;
	public string $code;        //Identifier that matches the code in the SPSS dataset
	public string $question;    //Full text of the question
	public string $summary;     //Short text to be displayed in dropdown menus
	public int $category;       //Category ID i.e. Alcohol=1
    public bool $has_cutoff;    //Can the question be transformed into a binary Yes/No by setting a cutoff point
    public ?int $low_cutoff;     //Answers that are this value or higher are considered "Yes" in the cutoff. Null means no low cutoff point. Anything below the high cutoff is "Yes".
    public ?int $high_cutoff;    //Answers that are this value or lower are considered "Yes" in the cutoff. Null means no high cutoff point. Anything above the low cutoff is "Yes".
    public ?int $total_cutoff;   //If null, all respondents are counted in the total (denominator). Otherwise, only students that answered this value or higher are included in the total.
    public ?string $cutoff_summary;
    public ?string $cutoff_tooltip;
    /*public ?string $answer1, $answer2;
    //public string $answer2;
    public string $answer3;
    public string $answer4;
    public string $answer5;
    public string $answer6;
    public string $answer7;
    public string $answer8;
    public string $answer9;
    public string $answer10;*/
    public array $labels = []; //array of all answers

    public array $counts;
    public array $percents;
    public array $totals;

    public function __construct()
    {
        $this->counts = array();
        $this->percents = array();
        $this->totals = array();
    }
}