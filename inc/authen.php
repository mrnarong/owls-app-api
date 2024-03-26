<?php
// $path = substr(__FILE__, 0, strpos(__FILE__, 'inc/')+4);

// echo $path."\nauthen.php path ==> ".__DIR__ . "/../lib/php-jwt/JWT.inc.php"."\n";
require __DIR__ . "/../lib/php-jwt/JWT.inc.php";

// require_once($path . 'php-jwt/JWT.inc.php');

class Authen {
    private $myData;
    function login($username, $password, $secret_key, $db) {
      $jwt = new JWT();

      $account = $db->query('SELECT * FROM users WHERE LOWER(user_id) = ?', strtolower($username))->fetchArray(); // AND password = ?', $username, md5($password))->fetchArray();
        if(sizeof($account)) {

            if( $account["password"] == md5($password)) {
                $iat = time();
                $info = array(
                    "username"  => $username,
                    // "email"     => $username, // $account['first_name'], // "mrnarong@gmail.com",
                    "fullname"  => $account['fullname'],//$account['first_name']." ".$account['last_name'],
                    "role"      => $account['role'],
                    "iat"       => $iat,
                    "exp"       => $iat + 7 * 24 * 60 * 60 // 7 days
                );
                $access_token   = $jwt->encode($info, $secret_key);
                $info["exp"]    = $info["exp"] - 30 * 60; // 30 mins
                $refresh_token  = $jwt->encode($info, $secret_key);
            } else {
                $access_token = -1; // Invalid password
            }
        } else {
            $access_token = 0; // User not found
        }
        return json_encode(array("access-token"=>$access_token, "refresh-token"=>$refresh_token));
    }

    function logout($username) {

    }

    // return: 1 - Valid, 0 - Expire, -1 - Invalid token
    function verify($access_token, $secret_key) {
        $json = array(
            "resultCode" => 401,
            "resultData" => "",
            "data"       => Array()
        );

        try {
            $res = JWT::decode($access_token, $secret_key);
            $json['resultCode'] = $res->exp < time() ? 401 : 200;
            if(!$json['resultCode']) {
              $json['resultData'] = 'Token expired';
            } else {
              $json['data'] = $res;
            }
        } catch (Exception $e) {
            $json['resultCode'] = 401;
            $json['resultData'] = "Invalid token";
        }
        return $json;
        // return json_encode($json);
    }

    function verifyRequest($access_token) {
    //   $config = json_decode(file_get_contents("./config.json.php"));
      return $this->verify($access_token, SECRET_KEY, 'Api token', null);
    }
}
?>