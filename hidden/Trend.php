<?php

class Trend
{
    public string $name;
    //public array $variables;
    public array $variablesByYear = [];

    function __construct(string $trendName)
    {
        $this->name = $trendName;
    }

    /**
     * Organize variables by year
     * @param Variable[] $variables
     */
    public function addVariables(array $variables) {
        foreach ($variables as $variable) {
            $this->variablesByYear[$variable->year]  = $variable;
        }
    }
}