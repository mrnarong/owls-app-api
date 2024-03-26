<?php
require __DIR__ . "/../../index.php";
// 
require PROJECT_ROOT_PATH . "/controllers/api/LeaveController.php";
$reqBody = json_decode(file_get_contents('php://input'), true);

//-------------- Req body validation --------------
$reqConfig = Array(
    "payload" => Array(
    //   "username"=>Array("string", "optional"),
    //   "employeeId"=>Array("string", "optional"),
      "company"=>Array("string", "required"),
        "condition" => Array("object", "required"), //"key"=>Array(
            // "employeeId"=>Array("string", "required"),
            // "monthYear"=>Array("string", "required"),
        // )),

        "sort"=>Array("array", "optional", "key"=>Array(
            "key"=>Array("string", "required"),
            "order"=>Array("string", "required"),
        )),
    //   "companyId"=>Array("string", "required"),
    //   "fullname"=>Array("string", "required"),
    //   "mobileNo"=>Array("number", "required"),
    //   "token"=>Array("string", "required"),
    //   "role"=>Array("string", "required"),
    //   "status"=>Array("string", "required"),
    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
    //   "ApiKey"=>Array("string", "required")
      )
);

$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if($vdResult["message"]!=="Ok"){
    responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$mapFileds = Array(
    "employeeId"=>"employee_id",
    "monthYear"=>"start_date",
    "status"=>"status",
);
$reqParams = Array(
    // "username"=>$reqBody["username"],
    // "employee_id"=>$reqBody["employeeId"],
    // "user_email"=>$reqBody["email"],
);

$conditions = Array();
if($reqBody["condition"]) {
    // echo "Condition";
    foreach ($reqBody["condition"] as $elCond) {
        $mappedEl = Array();
        foreach ($elCond as $key => $value) {
            if($mapFileds[$key]) {
                $mappedEl[$mapFileds[$key]] = $value;
            }
        }
        if($mappedEl) {
            array_push($conditions, $mappedEl);
        }
    }
}

// print_r($conditions);
$reqParams["condition"] = $conditions ? $conditions : [];

if($reqBody["sort"] && $mapFileds[$reqBody["sort"]["key"]] && (array_search(strtoupper($reqBody["sort"]["order"]), ["ASC", "DESC"])>-1)) {
    $reqParams["sort"]["key"] = $mapFileds[$reqBody["sort"]["key"]];
    $reqParams["sort"]["order"] = strtoupper($reqBody["sort"]["order"]);
}

$company = $reqBody["company"];
$apiController = new LeaveController();
$result = $apiController->{'getLeaveSummary'}($company, $reqParams);


if(!isset($result["error"])) {
    responseSuccess($result ? 200 : 404, $result ? "Success" : "Not found", $result);
} else {
    responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
}

/*
function getWorkingDays($startDate, $endDate, $holidays){
    // do strtotime calculations just once
    $endDate = strtotime($endDate);
    $startDate = strtotime($startDate);


    //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
    //We add one to inlude both dates in the interval.
    $days = ($endDate - $startDate) / 86400 + 1;

    $no_full_weeks = floor($days / 7);
    $no_remaining_days = fmod($days, 7);

    //It will return 1 if it's Monday,.. ,7 for Sunday
    $the_first_day_of_week = date("N", $startDate);
    $the_last_day_of_week = date("N", $endDate);

    //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
    //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
    if ($the_first_day_of_week <= $the_last_day_of_week) {
        if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
        if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
    }
    else {
        // (edit by Tokes to fix an edge case where the start day was a Sunday
        // and the end day was NOT a Saturday)

        // the day of the week for start is later than the day of the week for end
        if ($the_first_day_of_week == 7) {
            // if the start date is a Sunday, then we definitely subtract 1 day
            $no_remaining_days--;

            if ($the_last_day_of_week == 6) {
                // if the end date is a Saturday, then we subtract another day
                $no_remaining_days--;
            }
        }
        else {
            // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
            // so we skip an entire weekend and subtract 2 days
            $no_remaining_days -= 2;
        }
    }

    //The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
   $workingDays = $no_full_weeks * 5;
    if ($no_remaining_days > 0 )
    {
      $workingDays += $no_remaining_days;
    }

    //We subtract the holidays
    foreach($holidays as $holiday){
        $time_stamp=strtotime($holiday);
        //If the holiday doesn't fall in weekend
        if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
            $workingDays--;
    }

    return $workingDays;
}

//Example:

$holidays=array("2008-12-25","2008-12-26","2009-01-01");

echo getWorkingDays("2008-12-22","2009-01-02",$holidays)
*/

?>