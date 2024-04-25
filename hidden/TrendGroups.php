<?php
require_once "DataService.php";

/**
 * @param int $group
 * @return array|null
 */
function getGroupCodes(int $group): ?array
{
    if($group == 1) {
        return ['A2B', 'A3B'];
    }
    if($group == 2) {
        return ['T3', 'T4B'];
    }
    return null;
}
function getGroupName($group) {
    if($group == 1)
        return "Alcohol";
    if($group == 2)
        return "Tobacco";
    if($group == 3)
        return "Drugs";
    if($group == 4)
        return "Sexual Health";
    if($group == 5)
        return "Vehicle  (12th Graders Only)";
    if($group == 6)
        return "Bullying and Cyberbullying";
    if($group == 7)
        return "Dating Aggression";
    if($group == 8)
        return "Harassment and Aggressive Behaviors";
    if($group == 10)
        return "Nutrition and Physical Activity";
    if($group == 11)
        return "Mental Health";
    if($group == 12)
        return "Civic Engagement and Time Use";
    if($group == 13)
        return "Assets that Build Resiliency";
    if($group == 20)
        return "Vaping";
    return null;
}

function getQuestionNote($question) {
    if($question == 'M5A')
        return "The question and answer options were changed in 2021 and direct comparison between 2021 and previous years' data is not recommended";
    return null;
}

