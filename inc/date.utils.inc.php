<?php
// namespace Utils;

class DateUtil {
  private $today;
  private $dow;
  var $wdays = Array(
    "th"=>Array(
      "long"=>Array(
        "อาทิตย์", "จันทร์", "อังคาร", "พุธ",
        "พฤหัสบดี", "ศุกร์", "เสาร์"
      ),
      "short"=>Array(
        "อา.", "จ.", "อัง.", "พ.",
        "พฤ.", "ศ.", "ส."
      )
    ),
    "en"=>Array(
      "long"=>Array(
        "Sunday", "Monday", "Tuesday", "Wednesday",
        "Thursday", "Friday", "Saturday"
      ),
      "short"=>Array(
        "Sun", "Mon", "Tue", "Wed",
        "Thu", "Fri", "Sat"
      ),
      "short_ex"=>Array(
        "Su", "M", "Tu", "W",
        "Th", "F", "Sa"
      )
    )

  );
  var $months = Array(
    "th"=>Array(
      "long"=>Array(
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
        "พฤษภาคม", "มิถุนายน", "กรกฏาคม", "สิงหาคม",
        "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
      ),
      "short"=>Array(
        "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.",
        "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.",
        "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."
      )
    ),
    "en"=>Array(
      "long"=>Array(
        "January", "February", "March", "April",
        "May", "June", "July", "August",
        "September", "October", "November", "December"
      ),
      "short"=>Array(
        "Jan", "Feb", "Mar", "Apr",
        "May", "Jun", "Jul", "Aug",
        "Sep", "Oct", "Nov", "Dec"
      )
    )
  );

  function __construct($date=null) {
    // [tm_sec] => 43 [tm_min] => 23 [tm_hour] => 15
    // [tm_mday] => 29 [tm_mon] => 5 [tm_year] => 121
    // [tm_wday] => 2 [tm_yday] => 179 [tm_isdst] => 0
    if(!$date){
      $dt = localtime(time(),true);
      $this->today = sprintf("%d-%02d-%02d", $dt["tm_year"]+1900, $dt["tm_mon"]+1, $dt["tm_mday"]);

    } else {
      $this->today = $date;
      $dt["tm_wday"] = date_format(date_create($date, timezone_open("Asia/Bangkok")), "w");
    }
    $this->dow = $dt["tm_wday"];
  }

  function format($format, $lang="en") {
    $lang = strtolower($lang);
    list($y, $m, $d) = explode("-", $this->today);
    $tokens = Array(
      Array(
        "YYYY"=>$y+($lang=="th"?543:0)." ",
        "YY"=>substr($y+($lang=="th"?543:0), 2, 2)." ",
      ),

      Array(
        "dddd"=>$this->wdays[$lang]["long"][$this->dow]." ",
        "dd"=>$this->wdays[$lang]["short"][$this->dow]." ",
        "d"=>$this->dow,
      ),

      Array(
        "DD"=>sprintf("%02d", $d),
        "D"=>($d*1)." ",
      ),
      Array(
        "MMMM"=>$this->months[$lang]["long"][$m*1-1]." ",
        "MMM"=>$this->months[$lang]["short"][$m*1-1]." ",
        "MM"=>sprintf("%02d", $m)." ",
        "M"=>$m*1,
      )
    );
    foreach($tokens as $value) {
      foreach($value as $pattern=>$replace){
        if(preg_match("/$pattern/i", $format)){
          $format = str_replace($pattern, $replace, $format);
          break;
        }
      }
    }
    return $format;
  }
}

?>
