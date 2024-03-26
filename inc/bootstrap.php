<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/..");
// include main configuration file
require_once PROJECT_ROOT_PATH . "/inc/config.php";
// include the base controller file
require_once PROJECT_ROOT_PATH . "/controllers/api/BaseController.php";

// include the use model file
require_once PROJECT_ROOT_PATH . "/models/CommonModel.php";
require_once PROJECT_ROOT_PATH . "/models/UserModel.php";
require_once PROJECT_ROOT_PATH . "/models/EmployeeModel.php";
require_once PROJECT_ROOT_PATH . "/models/ConfigModel.php";
require_once PROJECT_ROOT_PATH . "/models/LeaveModel.php";
require_once PROJECT_ROOT_PATH . "/models/ExpenseModel.php";
require_once PROJECT_ROOT_PATH . "/models/JobRouteModel.php";
require_once PROJECT_ROOT_PATH . "/models/OvertimeModel.php";
require_once PROJECT_ROOT_PATH . "/models/PayrollModel.php";
require_once PROJECT_ROOT_PATH . "/models/CommissionModel.php";
?>
