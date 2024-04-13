<?php
/*
require_once 'DataService.php';
$year = 9999; //chang
$ds = DataService::getInstance(2016, DataService::SIXTH);

$variables = $ds->getVariables();
$years = [2015,2016,2017,2018,2019,2021];

foreach ($years as $year)
{
    $ds = DataService::getInstance($year, DataService::SIXTH);
    foreach ($variables as $variable)
    {
        if($ds->isVariableInData($variable->code)) {
            $ds->addVariableYear($variable->code, $year, 0);
        }
    }
}*/