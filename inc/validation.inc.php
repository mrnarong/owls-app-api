<?php
class Validation{

  function isObject(array $arr) {
      if (array() === $arr) return false;
      return array_keys($arr) !== range(0, count($arr) - 1);
  }

  function checkRequried($vdConfig, $params, $isHeader=false){
    $modParam = Array();
    
    foreach($params as $key=>$value) {
        if($isHeader) {
            $key = ucfirst(strtolower($key));
        }
        $modParam[$key] = $value;
    }
    foreach($vdConfig as $key=>$config) {
        if(!isset($modParam[$key]) && array_search("required", $config)!==false){
            return $key;
      }
    }
    return false;
  }

  function checkHeader($vdConfig, $headers) {
    // print_r($vdConfig);
    // print_r($headers);

    foreach($headers as $key=>$pValue) {
        $key = ucfirst(strtolower($key));
        // echo $key."\n";
        $spec = isset($vdConfig[$key]) ? $vdConfig[$key] : false;
        if($spec) {
          if((array_search("number", $spec)!==false) && !is_numeric($pValue)) {
            return Array(
              "status"=>"442",
              "message"=>"Validation Error: '$key' is invalid type must be number"
            );
          }
          if((array_search("empty", $spec)===false) && !strlen($pValue)) {
            return Array(
              "status"=>"442",
              "message"=>"Validation Error: '$key' cannot be empty"
            );
          }
        }
      }

      $required = $this->checkRequried($vdConfig, $headers, true);
      if($required){
        return Array(
          "status"=>"442",
          "message"=>"Validation Error: '$required' is required"
        );
      }

      return Array(
        "status"=>"200",
        "message"=>"Ok"
      );
  }

  function checkPayload($vdConfig, &$params) {
    $result = Array(
      "status"=>"200",
      "message"=>"Ok"
    );

    foreach($params as $key=>$pValue) {
      if(!isset($vdConfig[$key])) {
        return Array(
          "status"=>"442",
          "message"=>"Validation Error: '$key' is not allowed"
        );
      } else {
        $spec = $vdConfig[$key];
        if((array_search("number", $spec)!==false) && !is_numeric($pValue)) {
          return Array(
              "status"=>"442",
              "message"=>"Validation Error: '$key' is invalid type must be number"
            );
        }
        if((array_search("string", $spec)!==false) && !strlen($pValue) && (array_search("empty", $spec)===false)) {
            return Array(
              "status"=>"442",
              "message"=>"Validation Error: '$key' cannot empty"
            );
        }
        if((array_search("boolean", $spec)!==false) && !is_bool($pValue) && (in_array(strtolower($pValue), Array("true","false"))===false)) {
            return Array(
              "status"=>"442",
              "message"=>"Validation Error: '$key' must be boolean"
            );
        }

        if((array_search("array", $spec)!==false) && !count($pValue)) {
          return Array(
            "status"=>"442",
            "message"=>"Validation Error: '$key' cannot empty"
          );
        } else if(array_search("array", $spec)!==false) {
          // print_r($pValue);
          // print_r($vdConfig[$key]["key"]);
          // echo $key." --- \n";
          if($this->isObject($params[$key])) {
            return $this->checkPayload($vdConfig[$key]["key"], $params[$key]);
          } else {
            foreach($params[$key] as $item) {
              $res = $this->checkPayload($vdConfig[$key]["key"], $item);
              if($res["status"] == "442") {
                return $res;
              }
            }
            return $result;
          }
        }
      }
    }

    $required = $this->checkRequried($vdConfig, $params, false);
    if($required){
      return Array(
        "status"=>"442",
        "message"=>"Validation Error: '$required' is required"
      );
    }

    // if(!$header) {
    //   foreach($vdConfig as $key=>$spec){
    //     if(!isset($params)){
    //       return "Validation Error: '$key' is not allowed";
    //     }
    //   }
    // }
    return Array(
      "status"=>"200",
      "message"=>"Ok"
    );
  }

  function parse($config, $params){
    $result = Array(
      "status"=>"200",
      "message"=>"Ok"
    );
    $vdResult = $this->checkHeader($config["headers"], getallheaders());
    if(strlen($vdResult)) {
      $result["status"] = "422";
      $result["message"] = $vdResult;
    } else {
      $vdResult = $this->checkPayload($config["payload"], $params);
      if(strlen($vdResult)) {
        $result["status"] = "422";
        $result["message"] = $vdResult;
      }
    }
    return $result;
  }
}


/**
 * Validator class
 *
 * @package Validator
 * @author Ravi Kumar
 * @version 0.1.0
 * @copyright Copyright (c) 2014, Ravi Kumar
 * @license https://github.com/ravikumar8/PHP-Validator/blob/master/LICENSE MIT
 **/

 class Validator {

  /**
   * Reference to ErrorHandler Class
   *
   * @var ErrorHandler
   **/
  protected $errorHandler;

  /**
   * Reference to Database Class
   *
   * @var Database
   **/
  // protected $db;

  /**
   * holds $_POST data
   *
   * @var array
   **/
  protected $items;

  /**
   * Rules for the validator class
   *
   * @var array
   **/
  protected $rules = [ 'required', 'minlength', 'maxlength', 'email', 'activeemail', 'url', 'activeurl', 'ip', 'alpha', 'alphaupper', 'alphalower', 'alphadash', 'alphanum', 'hexadecimal', 'numeric', 'matches', 'unique' ];

  /**
   * messages for the rules
   *
   * @var array
   **/
  public $messages = [
      'required' => 'The :field field is required',
      'minlength' => 'The :field field must be a minimum of :satisfied length',
      'maxlength' => 'The :field field must be a maximum of :satisfied length',
      'email' => 'That is not a valid email address',
      'activeemail' => 'The :field field must be active email address',
      'url' => 'The :field field must be url',
      'activeurl' => 'The :field field must be activeurl',
      'ip' => 'The :field field must be valid ip',
      'alpha' => 'The :field field must be alphabetic',
      'alphaupper' => 'The :field field must be upper alpha',
      'alphalower' => 'The :field field must be lower alpha',
      'alphadash' => 'The :field field must be alpha with dash',
      'alphanum' => 'The :field field must be alphanumeirc',
      'hexadecimal' => 'The :field field must be hexadecimal',
      'numeric' => 'The :field field must be numeric',
      'matches' => 'The :field field must matches the :satisfied field',
      'unique' => 'That :field already taken'
  ];

  /**
   * Constructor
   *
   * @param Database
   * @param ErrorHandler
   **/
  public function __construct( ErrorHandler $errorHandler ) {

      // $this->db = $db;
      $this->errorHandler = $errorHandler;
  }

  /**
   * check
   *
   * @param array $_POST
   * @param array rules to check
   * @return Validator
   **/
  public function check($items, $rules) {

      $this->items = $items;
      foreach ($items as $key => $value) {
         
          if( in_array( $key, array_keys($rules) ) ) {

              $this->validate([
                  'field' => $key,
                  'value' => $value,
                  'rules' => $rules[$key]
              ]);
          }
      }

      return $this;
  }

  /**
   * fails
   *
   * @return boolean true if errors else false
   **/
  public function fails() {

      return $this->errorHandler->hasErrors();
  }

  /**
   * errors
   *
   * @return ErrorHandler
   **/
  public function errors() {

      return $this->errorHandler;
  }

  /**
   * validate
   *
   * @param mixed
   **/
  protected function validate($item) {

      $field = $item['field'];

      foreach ($item['rules'] as $rule => $satisfied) {
         
          if( in_array($rule, $this->rules) ) {
         
              if( !call_user_func_array( [$this, $rule], [$field, $item['value'], $satisfied] ) ) {
                 
                  $this->errorHandler->addError(
                      str_replace( [':field', ':satisfied'], [$field, $satisfied], $this->messages[$rule] ),
                      $field
                  );
              }
          }
      }
  }

  /**
   * required
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function required($field, $value, $satisfied) {
      return !empty(trim($value));
  }

  /**
   * minlength
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function minlength($field, $value, $satisfied) {
      return mb_strlen($value) >= $satisfied;
  }

  /**
   * maxlength
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function maxlength($field, $value, $satisfied) {
      return mb_strlen($value) <= $satisfied;
  }

  /**
   * email
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function email($field, $value, $satisfied) {
      return filter_var($value, FILTER_VALIDATE_EMAIL);
  }

  /**
   * active_email
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function active_email($field, $value, $satisfied) {

      if( $this->email($field, $value, $satisfied) ) {

          if(checkdnsrr(array_pop(explode("@",$value)),"MX")) {
              return true;
          } else {
              return false;
          }

      } else {

          return false;

      }
  }

  /**
   * url
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function url($field, $value, $satisfied) {
      return filter_var($value, FILTER_VALIDATE_URL);
  }

  /**
   * active_url
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function active_url($field, $value, $satisfied) {

      if( $this->email($field, $value, $satisfied) ) {

          if( checkdnsrr("www.goofdfsdfsgle.com", "ANY")) {
              return true;
          } else {
              return false;
          }

      } else {

          return false;

      }
  }

  /**
   * ip
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function ip($field, $value, $satisfied) {
      return filter_var($value, FILTER_VALIDATE_IP);
  }

  /**
   * alpha
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function alpha($field, $value, $satisfied) {
      return ctype_alpha($value);
  }

  /**
   * alphaupper
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function alphaupper($field, $value, $satisfied) {
      return ctype_upper($value);
  }

  /**
   * alphalower
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function alphalower($field, $value, $satisfied) {
      return ctype_lower($value);
  }

  /**
   * alphadash
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function alphadash($field, $value, $satisfied) {
      return preg_match('^[A-Za-z-]+$', $value);
  }

  /**
   * alphanum
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function alphanum($field, $value, $satisfied) {
      return ctype_alnum($value);
  }

  /**
   * hexadecimal
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function hexadecimal($field, $value, $satisfied) {
      return ctype_xdigit($value);
  }

  /**
   * numeric
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function numeric($field, $value, $satisfied) {
      return ctype_digit($value);
  }

  /**
   * matches
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function matches($field, $value, $satisfied) {
      return ( strcmp( $value, $this->items[$satisfied] ) == 0 ) ? true : false;
  }

  /**
   * unique
   *
   * @param string
   * @param string
   * @param string
   * @return boolean
   **/
  protected function unique($field, $value, $satisfied) {
      return ! $this->db->table($satisfied)->exists([$field => $value]);
  }

} // END class Validator


/*
ini_set('display_errors', 0);

$jsonBody = '{"test":true, "condition":[{"recId":true}],"updateData":{"username":"mrnarong"}}';
$reqBody = json_decode($jsonBody, true);

$config = Array(
    "payload" => Array(
        "test"=>Array("boolean", "required"),
        "condition"=>Array("array", "required", "keys"=>Array(
          "recId"=>Array("boolean", "optional")
        )),
        "updateData"=>Array("object", "optional", "keys"=>Array(
            "username"=>Array("string", "optional"),
            "email"=>Array("string", "required"),
            "employeeId"=>Array("string", "optional"),
            "password"=>Array("string", "optional"),
            "company"=>Array("string", "optional"),
        )),

    ),
    "headers" => Array(
      "Authorization"=>Array("string", "required"),
      )
);



function isObject($array){
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
}

function validateParams($vConfig, $vParams) {
    if(!isObject($vParams)) {
        return Array("code"=>422, "message"=>"Unprocessable Entity");
    }

    foreach($vParams as $key=>$value) {
        if(!isset($vConfig[$key])) {
            return Array("code"=>442, "message"=>"Validation Error: '{$key}' is not allow");
        }
    }

    foreach($vConfig as $key=>$value) {
        echo "\n{$key}<br>";
        if(!isset($vParams[$key]) && in_array("required", $vConfig[$key]) ) {
            return Array("code"=>442, "message"=>"Validation Error: '{$key}' is required");
        }

        $configType =  $vConfig[$key][0];
        switch($configType) {
            case "array":
                if(gettype($vParams[$key])=="array" && !isObject($vParams[$key])){
                    if(!in_array("empty", $vConfig[$key]) && sizeof($vParams[$key])==0) {
                        return Array("code"=>442, "message"=>"Validation Error: '{$key}' cannot be empty");
                    } else {
                        // echo sizeof($vParams[$key]);
                        foreach($vParams[$key] as $vItem) {
                            return validateParams($vConfig[$key]["keys"], $vItem);
                        }
                    }
                } else {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be array");
                }
            break;
            case "object":
                    echo "enter<br>";
                if(gettype($vParams[$key])=="array" && isObject($vParams[$key])){
                    return validateParams($vConfig[$key]["keys"], $vParams[$key]);
                } else {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be object");
                }
            break;
            case "number":
                $paramType = gettype($vParams[$key]);
                if($paramType != "integer" && $paramType != "double") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be number");
                }
            break;
            case "string":
                if(gettype($vParams[$key]) !== "string") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be string");
                }
            break;
            case "boolean":
                if(gettype($vParams[$key]) !== "boolean") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be boolean");
                }
            break;
        }

    }

    return Array("code"=>200, "message"=>"Success");
}

function validateParams0($vConfig, $vParams) {
    if(!isObject($vParams)) {
        return Array("code"=>422, "message"=>"Unprocessable Entity");
    }

    foreach($vParams as $key=>$value) {
        if(!$vConfig[$key]) {
            return Array("code"=>442, "message"=>"Validation Error: '{$key}' is not allowed");
        }
        $configType =  $vConfig[$key][0];
        switch($configType) {
            case "array":
                if(gettype($vParams[$key])=="array" && !isObject($vParams[$key])){
                    if(!in_array("empty", $vConfig[$key]) && sizeof($vParams[$key])==0) {
                        return Array("code"=>442, "message"=>"Validation Error: '{$key}' cannot be empty");
                    } else {
                        // echo sizeof($vParams[$key]);
                        foreach($vParams[$key] as $vItem) {
                            return validateParams($vConfig[$key]["keys"], $vItem);
                        }
                    }
                } else {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be array");
                }
            break;
            case "object":
                if(gettype($vParams[$key])=="array" && isObject($vParams[$key])){
                    return validateParams($vConfig[$key]["keys"], $vParams[$key]);
                } else {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be object");
                }
            break;
            case "number":
                $paramType = gettype($vParams[$key]);
                if($paramType != "integer" && $paramType != "double") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be number");
                }
            break;
            case "string":
                if(gettype($vParams[$key]) !== "string") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be string");
                }
            break;
            case "boolean":
                if(gettype($vParams[$key]) !== "boolean") {
                    return Array("code"=>442, "message"=>"Validation Error: '{$key}' must be boolean");
                }
            break;
        }
    }


    return Array("code"=>200, "message"=>"Success");
}

// echo gettype(true)."<br>";
print_r(validateParams($config["payload"], $reqBody));


*/
?>

