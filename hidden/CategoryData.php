<?php
require_once "HighlightGroup.php";
require_once "DataService.php";

function getHighlightGroup($cat, $year)
{
    if ($cat == 1) {
        $title = "Gambling Related Experiences";
        $qCodes = ['Q_exp_1', 'Q_exp_2', 'Q_exp_3', 'DSM5'];
    }
    else if ($cat == 2) {
        $title = "Gambling Problems";
        $qCodes = ['Q1','Q2','Q3','NODS'];
    }
    else if ($cat == 3) {
        $title = "Gambling Consequences";
        $qCodes = ['Q_consq_1','Q_consq_2','Q_consq_3','PGSI'];
    }
    else if ($cat == 4) {
        $title = "Services for Problem Gambling";
        $qCodes = ['Q_serv1','Q_serv3','Q_serv4'];
    }
    else if ($cat == 5) {
        $title = "Other Health Concerns";
        $qCodes = ['Q_health1_1','Q_health2_1','Q_health3_1','Q_health4_1'];
    }
    else {
        die("Category chosen is invalid.");
    }

    $var = new HighlightGroup();
    $var->title = $title;
    $var->codes = $qCodes;
    return $var;
}

