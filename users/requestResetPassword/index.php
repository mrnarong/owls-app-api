<?php
require __DIR__ . "/../../index.php";
require PROJECT_ROOT_PATH . "/controllers/api/UserController.php";

$reqBody = json_decode(file_get_contents('php://input'), true);
// print_r(file_get_contents('php://input'));
$reqConfig = array(
  "payload" => array(
    "username" => array("string", "required"),
  ),
  "headers" => array(
    // "Authorization"=>Array("string", "required"),
    "Apikey" => array("string", "required")
  )
);

$validation = new Validation;
$vdResult = $validation->checkHeader($reqConfig["headers"], getallheaders());
if ($vdResult["message"] !== "Ok") {
  responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$vdResult = $validation->checkPayload($reqConfig["payload"], $reqBody);
if ($vdResult["message"] !== "Ok") {
  responseError(442, $vdResult["status"], $vdResult["message"], null);
}

$apiController = new UserController();
$users = $apiController->getUser(array("condition" => array(array("username" => $reqBody["username"]))));
// print_r($users);
if (sizeof($users)) {
  $util = new Utils();
  $token = $util->guidv4(); // Temp token
  $result = $apiController->addActivateUser(array(
    "token" => $token,
    "email" => $users[0]["email"],
  ));

  if ($result) {
    // print_r($users[0]);
    $email = $users[0]["email"];
    $result = $util->sendMail(
      "hr@owlswallpapers.com",
      $email,
      "Reset Password Notification",
      " ระบบได้รับการร้องขอ Reset Password จากท่าน<br> หากท่านไม่ได้เป็นผู้ทำรายการ โปรดอย่าสนใจอีเมล์ฉบับนี้และจะไม่มีอะไรเกิดขึ้น<br><br>แต่ถ้าเป็นความต้องการของท่าน กรุณาคลิ๊กปุ่มด้านล่าง เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป<br><br><a 

        style=\"display: block;
          width: 115px;
          height: 20px;
          background: #4E9CAF;
          padding: 10px;
          text-align: center;
          border-radius: 5px;
          color: white;
          font-weight: bold;
          line-height: 25px;
          text-decoration: none\" 

        href='http://hr-app.owlswallpapers.com/owl-client/reset-password?id=$token&email=$email'>Reset Password</a> <br><br>
        HR Department",
      "ขณะนี้ทางบริษัทได้ทำการเพิ่มข้อมูลของท่านเข้าระบบแล้ว กรุณาเข้า http://hr-app.owlswallpapers.com/owl-client/login ด้วยข้อมูลต่อไปนี้\n Username: \n Password: \n เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป\n"
    );

    // Mock
    $result = array(
      "code" => 200,
      "message" => "Send mail success"
    );
  } else {
    $result = array(
      "code" => 500,
      "message" => "Internal server error"
    );
  }
} else {
  $result = array(
    "code" => 404,
    "message" => "User is not found"
  );
}


if (isset($result["code"])) {
  responseSuccess($result["code"], $result["message"]);
} else {
  responseError(500, 500, "Internal Server Error", array('error' => $result["error"]));
}

// if(!isset($result["error"])) {
//   responseSuccess($result ? 200:304, $result ? "Success" : "No data updated", $result);
// } else {
//     responseError(500, 500, "Internal Server Error", array('error' => $result["data"]));
// }
