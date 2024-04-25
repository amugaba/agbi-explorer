<?php
/**
 * Parent class of CutoffVariable and MultiVariable.
 * A Variable is a question that has some number of answers.
 */
class HighlightGroup
{
    public $title;
	public $codes;
	public $labels;
	public $tooltips;
	public $explanation;
    public $connector;//word used in tooltip, "X% of respondents reported [connector] [question]"
}