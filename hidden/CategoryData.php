<?php
require_once "HighlightGroup.php";
require_once "DataService.php";

function getHighlightGroup($cat, $dataset, $year)
{
    if ($cat == 1) {
        $title = "Tobacco";
            $qCodes = ['alcmo', 'smokmo', 'marmo'];
    }
    else if ($cat == 2) {
        $title = "Alcohol";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['alcmo','binge'];
        }
        else {
            $qCodes = ['alcmo'];
        }
    }
    else if ($cat == 3) {
        $title = "Marijuana";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['marmo','k2mo'];
        }
        else {
            $qCodes = ['marmo'];
        }
    }
    else if ($cat == 4) {
        $title = "Prescription Drugs";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            if($year > 2017)
                $qCodes = ['rxpkmo','rxsdmo','rxstmo','otcmo'];
            else
                $qCodes = ['rxmo','otcmo'];
        }
        else {
            $qCodes = ['rxmo'];
        }
    }
    else if ($cat == 5) {
        $title = "Other Drugs";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['cocmo','inhmo','methmo','hermo','lsdmo'];
        }
        else {
            $qCodes = ['inhmo'];
        }
    }
    else if ($cat == 6) {
        $title = "Consequences from Substance Use";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['misschl','poorwrk'];
        }
        else {
            //display message that the 6th grade survey doesn't ask about this
            $qCodes = [];
        }
    }
    else if ($cat == 7) {
        $title = "Access to Drugs";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['getalc','getcig','getdrug','getmar'];
        }
        else {
            $qCodes = [];
        }
    }
    else if ($cat == 8) {
        $title = "Gambling Behavior";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            if($year > 2018)
                $qCodes = ['casino','lottery','horse','cards','pools','fantasy','video','sports','online','esports','charity','gamoth'];
            else
                $qCodes = ['lottery','cards','bingo','chalng','games','online','sports','gamoth'];
        }
        else {
            $qCodes = [];
        }
    }
    else if ($cat == 9) {
        $title = "Gambling Consequences";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            if($year > 2018)
                $qCodes = ['nosleep','poorhyg','lostfrnd','issuewf','schprbm','mnprobm','feltbad','depress'];
            else
                $qCodes = ['acaprbm','poorhth','lglsys','issuewf','lostmn','feltbad'];
        }
        else {
            $qCodes = [];
        }
    }
    else if ($cat == 10) {
        $title = "Mental Health";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['sad','consui', 'plansui'];
        }
        else {
            $qCodes = ['sad','consui', 'plansui'];
        }
    }
    else if ($cat == 11) {
        $title = "CTC Risk Factors";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['crlnd_rs','crpad_rs','sracf_rs','srlcs_rs',
                'frpfm_rs','frfcn_rs','frpfd_rs','frpab_rs',
                'preid_rs','prfad_rs','prprd_rs','sracf_rs'];
        }
        else {
            $qCodes = ['sracf_rs',
                'frpfm_rs','frfcn_rs','frpfd_rs',
                'prprd_rs'];
        }
    }
    else if ($cat == 12) {
        $title = "CTC Protective Factors";
        if($dataset == DataService::SEVEN_TO_TWELVE) {
            $qCodes = ['cprpi_ps','fpopi_ps','fprpi_ps','spopi_ps','sprpi_ps','ppipp_ps'];
        }
        else {
            $qCodes = ['cprpi_ps','fpopi_ps','fprpi_ps','spopi_ps','sprpi_ps','ppipp_ps'];
        }
    }
    else {
        die("Category chosen is invalid.");
    }

    $var = new HighlightGroup();
    $var->title = $title;
    $var->codes = $qCodes;
    return $var;
}

