<?php
// 159357

// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
// header("Access-Control-Allow-Credentials: true");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Origin, X-Requested-With, Authorization, cache-control, Content-Type, Access-Control-Allow-Origin");
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

require __DIR__ . "/inc/bootstrap.php";
require __DIR__ . "/inc/authen.php";
require __DIR__ . "/inc/validation.inc.php";
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$logStartTime = floor(microtime(true) * 1000);
// echo SECRET_KEY;

$uri = explode( '/', $uri );
$uri = array_values(array_filter($uri));
ini_set('display_errors', 0);
// print_r($uri);
// Register api
$routes = Array(

    "version" => Array(
        "method"=>"GET",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/addActivateUser" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/setActivateStatus" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/login" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "common/getConfig" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    
    "common/approveRequest" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "common/getDocument" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "common/updateConfig" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "users/addUser" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/getUsers" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/getNewUsername" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    
    "users/updateUser" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/changePassword" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "users/getUserByResetToken" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "users/requestResetPassword" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/resetPassword" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "users/deleteUser" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    
    
    
    "employee/addEmployee" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/getEmployees" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/lookupEmployee" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/getNewEmployeeId" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "employee/updateEmployee" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/getSalaryHist" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/addSalary" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "employee/updateSalary" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    

    "leave/addLeave" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "leave/getLeaves" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "leave/updateLeave" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "leave/getLeaveSummary" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "expense/addExpense" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "expense/updateExpense" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "expense/getExpenses" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "jobRoute/getJobRoutes" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "jobRoute/addJobRoute" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "jobRoute/updateJobRoute" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "overtime/addOvertime" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "overtime/getOvertimes" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "overtime/updateOvertime" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "overtime/approveOvertime" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/getPayrolls" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/getPayrollSlip" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/generatePayrollItems" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/updatePayroll" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/undoGeneratePayrollItems" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/exportPayrollExcel" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/updatePayroll" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "payroll/approvePayroll" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "payroll/addPayrollAdditional" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),

    "commission/addCommission" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "commission/getCommissions" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "commission/updateCommission" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    "admin" => Array(
        "method"=>"POST",
        "Content-Type"=>"application/json",
        "auth"=>true
    ),
    // "version" => Array(
    //     "method"=>"POST",
    //     "Content-Type"=>"application/json",
    //     "auth"=>false
    // ),
    
);

// echo ENV;
// print_r($uri);
// $uriLenEnv = sizeof($uri);

if(sizeof($uri) == (ENV==="prod" ? 3 : 4)) {
    $apiIndex = $uri[count($uri)-2]."/".$uri[count($uri)-1];
} else {
    $apiIndex = $uri[count($uri)-1];
}
// $apiIndex = $uri[count($uri)-2].'_'.$uri[count($uri)-1];

// echo $uri[count($uri)-2].'_'.$uri[count($uri)-1];

if (!isset($routes[$apiIndex]) || $routes[$apiIndex] === null) {
    header("HTTP/1.1 404 Not Found");
    echo "HTTP/1.1 404 Not Found";
    exit();
}
// echo $_SERVER['REQUEST_METHOD'] ;

if ($_SERVER['REQUEST_METHOD'] != $routes[$apiIndex]["method"]) {
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
} 

if($routes[$apiIndex]["auth"]) {
    $headers = apache_request_headers();
    if(isset($headers["Authorization"])) {
        $token = $headers["Authorization"];
        $auth = new Authen();
        $authen = $auth->verifyRequest($token);

        // print_r($authen); //$authen['data']->company;
        // echo getTokenCompany();

    } else if(isset($headers["Apikey"])){
        $token = "Use Api-key instead";
        $authen = Array('resultCode'=>($headers["Apikey"]==API_KEY?200:401));
    }
    // echo "Use Api-key instead";
    // print_r($headers);
    if(!$token || ($authen['resultCode'] != 200)) {
        responseError(401, 401, "Unauthorized");
    }

}

//----------------------- common functions ------------------------------------
function getConfig($configName) {
    $model = new ConfigModel();
    $result = $model->getConfig(Array("config_name"=>"api.config"));
    if(sizeof($result)) {
        return json_decode($result[0]["data"], true);
    } else {
        return Array();
    }
}

function getTokenCompany() {
    $headers = apache_request_headers();
    $token = $headers["Authorization"];
    $auth = new Authen();
    $authen = $auth->verifyRequest($token);
    return $authen['data']->company;
}

function getTokenData($field) {
    $headers = apache_request_headers();
    $token = $headers["Authorization"];
    $auth = new Authen();
    $authen = $auth->verifyRequest($token);
    return $authen['data']->$field;
}


//----------------- Search helper functions -----------------
function getTransformField($mapFileType, $key, $value) {
    // echo $key." ". $value."\n";
    $result = Array();
    if($mapFileType[$key][1] == "upper") {
        $result["field"] = "UPPER(".$key.")";
        $result["value"] = strtoupper($value);
    } else if($mapFileType[$key][1] == "lower") {
        $result["field"] = "LOWER(".$key.")";
        $result["value"] = strtolower($value);
    } else if($mapFileType[$key][1] == "regex-begin") {
        $result["field"] = "LIKE ".$key."%";
        // $result["value"] = strtolower($value);
    // } else if($mapFileType[$key][1] == "DATE") {
    //     $result["field"] = $key;
    //     $result["value"] = $value;
    // } else if($mapFileType[$key][1] == "DATETIME") {
    //     $result["field"] = $key;
    //     $result["value"] = $value;
    } else  {
        $result["field"] = $key;
        $result["value"] = $value;
    }

    return $result;
}

// (issue_date between '2023-10-01 00:00:00' and '2023-10-01 23:59:59')
function getSerchOptions($key, $value) {
    $val = "";
    if($value["\$eq"]){
        $val = " = \"".$value["\$eq"]."\"";
    }
    if($value["\$ne"]){
        $val = " <> \"".$value["\$ne"]."\"";
    }
    if($value["\$gt"]){
        $val = " > \"".$value["\$gt"]."\"";
    }
    if($value["\$gte"]){
        $val = " >= \"".$value["\$gte"]."\"";
    }
    if($value["\$lt"]){
        $val = " < \"".$value["\$lt"]."\"";
    }
    if($value["\$lte"]){
        $val = " <= \"".$value["\$lte"]."\"";
    }
    if($value["\$in"]){
        $val = " IN (".implode(",", array_map(function ($str) { return "\"$str\""; }, $value["\$lte"])).")";
    }
    if($value["\$regex"]){
        if($value["\$regex"][0] == "^") {
            $val = " LIKE '".str_replace("^", "", $value["\$regex"])."%'";
        } else if($value["\$regex"][strlen($value["\$regex"])-1] == "$") {
            $val = " LIKE '%".str_replace("\$", "", $value["\$regex"])."'";
        } else {
            $val = " LIKE '%".$value["\$regex"]."%'";
        }
    }
    if($value["\$in"]){
        $val = " IN (\"".implode("\", \"", $value["\$in"])."\")";
    }
    if($value["\$nin"]){
        $val = " NOT IN (\"".implode("\", \"", $value["\$nin"])."\")";
    }
    return $key." ".$val;
}

function getQueryOptions($mapFileType, $condParams) {
    $result = Array();
    $condition = "";// WHERE ";
    $params = Array();
    $format = "";
    // print_r($condParams);
    foreach ($condParams as $elCond) {
        $cond = "";

        //    Array($regex: "abc")
        foreach ($elCond as $key => $value) {
            $transData = Array();
            if(is_array($value)) {
                try {
                $cond .= (($cond ? " OR " : "") . getSerchOptions($key, $value));
                } catch (Error $e) {
                    print_r($e);
                }
            } else {
                // print_r($mapFileType);
                $transform = strlen($mapFileType[$key][1]) > 0;
                if($transform) {
                    $transData = getTransformField($mapFileType, $key, $value);
                }
                $cond .= (($cond ? " OR " : "").($transform ? $transData["field"] : $key)."=? ");
                array_push($params, $transform ? $transData["value"] : $value);
                $format .= $mapFileType[$key][0];
            }
        }
        // echo "getQueryOptions -> ".$cond."\n";
        // print_r($params);
        if($cond) { 
            $condition .= (($condition  ? " AND ":"")."(".$cond.")");
        }
    }
    $condition = ($condition?" WHERE ": "").$condition;
    if(strlen($format)) {
        // echo $format." -- \n";
        array_unshift($params, $format);
    } else {
        // $condition .= "1";
    }
    // echo $condition."\n";
    $result["condition"] = $condition;
    $result["params"] = $params;
    
    return $result;
}
//----------------- Search helper functions -----------------

function responseSuccess($statusCode, $statusMessage, $data=Array()) {
    global $routes, $uri;
    header_remove('Set-Cookie');
    header("HTTP/1.1 200 OK"); // $httpResponseCode
    if($routes[$uri[count($uri)-1]]["Content-Type"]) {
        header("Content-Type:".$routes[$uri[count($uri)-1]]["Content-Type"]);
    } else {
        // Default is application/json
        header("Content-Type: application/json");
    }

    $response =  Array(
        "statusCode"=> $statusCode,
        "statusMessage"=>$statusMessage,
        // "error"=>$error
    );
    if(is_array($data)){
        $response["data"] = $data;
    }
    echo json_encode($response);

    writeLog($response);
    exit();
}

function responseError($httpResponseCode, $statusCode, $statusMessage, $error="") {
    global $routes, $uri;
    header_remove('Set-Cookie');
    header("HTTP/1.1 ".$httpResponseCode." ".$statusMessage);
    if($routes[$uri[count($uri)-1]]["Content-Type"]) {
        header("Content-Type:".$routes[$uri[count($uri)-1]]["Content-Type"]);
    } else {
        // Default is application/json
        header("Content-Type: application/json");
    }

    $response =  Array(
        "statusCode"=> $statusCode,
        "statusMessage"=>$statusMessage,
        // "error"=>$error
    );
    if((is_array($error) && !empty($error)) || $error){
        $response["error"] = $error;
    }
    echo json_encode($response);

    writeLog($response);
    exit();
}

function writeLog($response) {
    global $logStartTime;
    $reqBody = json_decode(file_get_contents('php://input'), true);
    if(isset($reqBody["password"])){
        $reqBody["password"] = "****";
    }

    if(isset($reqBody["expenseData"]) && isset($reqBody["expenseData"]["documents"])){
        $reqBody["expenseData"]["documents"] = [sizeof($reqBody["expenseData"]["documents"])." - documents"];
    }

    if(isset($reqBody["leaveData"]) && isset($reqBody["leaveData"]["documents"])){
        $reqBody["leaveData"]["documents"] = [sizeof($reqBody["leaveData"]["documents"])." - documents"];
    }

    if(isset($reqBody["jobRoute"]) && isset($reqBody["jobRoute"]["photo"])){
        $reqBody["jobRoute"]["photo"] = [sizeof($reqBody["jobRoute"]["photo"])."-documents"];
    }


    if(isset($response["data"])) {
        $response["data"] = ["Some data..."];
    }
    // if(isset($response["data"]) && isset($response["data"]["accessToken"])) {
    //     $response["data"]["accessToken"] = "TOKEN";
    // }
    // if(isset($response["data"]) && isset($response["data"]["refreshToken"])) {
    //     $response["data"]["refreshToken"] = "TOKEN";
    // }

    $hour = date("H");
    $logTimeSet = $hour <= 11 ? "000000" : "120000";

    $reqBody = date("Y-m-d H:i:s")."|".$_SERVER['REQUEST_URI']."|".json_encode($reqBody)."|".json_encode($response)."|".(floor(microtime(true) * 1000)-$logStartTime);
    $logFile = __DIR__."/../app_data/api/logs/service".date("Ymd")."_".$logTimeSet.".log";
    file_put_contents($logFile, $reqBody."\n", FILE_APPEND);
}

function genApiDoc($reqConfig) {
    echo json_encode($reqConfig);
}

function shutdownHandler(){
    $error = error_get_last();
    if (isset($error['type']) && $error['type'] == E_ERROR) {
        responseError(500, 500, "Internal Server Error", $error["message"]);
    }
}

register_shutdown_function('shutdownHandler');
?>
