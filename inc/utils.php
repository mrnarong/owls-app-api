<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// echo __DIR__;

require __DIR__.'/../lib/phpmailer/Exception.php';
require __DIR__.'/../lib/phpmailer/PHPMailer.php';
require __DIR__.'/../lib/phpmailer/SMTP.php';

class Utils {
    function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    function sendActivateMail($from, $to, $username, $password) {
        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->isSMTP();
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        // $mail->Username='mrnarong@gmail.com';
        // $mail->Password='tddn pdcr bdao oeul ';

        $mail->Username = MAILER_USER; // 'HR@owlswallpapers.com';
        $mail->Password = MAILER_PASS; // 'juwd hpdd uvsg vumz';
        $mail->From = "$from"; 

        $mail->FromName = "Full FromName"; //To address and name 
        // $mail->addAddress($to, "Recepient Name");//Recipient name is optional
        $mail->addAddress($to); //Address to which recipient will reply 
        // $mail->addReplyTo("reply@yourdomain.com", "Reply"); //CC and BCC 
        // $mail->addCC("cc@example.com"); 
        // $mail->addBCC("bcc@example.com"); //Send HTML or Plain Text email 
        $mail->isHTML(true); 
        $mail->Subject = "แจ้งเตือนการเข้าใช้งานระบบ"; 
        $mail->Body = "ขณะนี้ทางบริษัทได้ทำการเพิ่มข้อมูลของท่านเข้าระบบแล้ว กรุณาเข้า <a href='http://hr-app.owlswallpapers.com/owl-client/login'>หน้า login</a> ด้วยข้อมูลต่อไปนี้ <br><br> Username: $username<br> Password: $password<br><br>เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป<br>";
        $mail->AltBody = "ขณะนี้ทางบริษัทได้ทำการเพิ่มข้อมูลของท่านเข้าระบบแล้ว กรุณาเข้า http://hr-app.owlswallpapers.com/owl-client/login ด้วยข้อมูลต่อไปนี้\n Username: $username\n Password: $password\n เพื่อทำการกำหนดรหัสผ่านสำหรับเข้าใช้งานครั้งต่อไป\n"; 
        if(!$mail->send()) {
            // echo "Mailer Error: " . $mail->ErrorInfo; 
            return Array("code"=>500, "message"=>$mail->ErrorInfo);
        } else { 
            // echo "Message has been sent successfully";
            return Array("code"=>200, "message"=>"Send mail success");
        }
    }

    function sendMail($from, $to, $subject, $body, $alt="") {
        $mail = new PHPMailer();
        $mail->CharSet = "utf-8";
        $mail->isSMTP();
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;

        // $mail->Username='mrnarong@gmail.com';
        // $mail->Password='tddn pdcr bdao oeul';

        $mail->Username = MAILER_USER; // 'HR@owlswallpapers.com';
        $mail->Password = MAILER_PASS; // 'juwd hpdd uvsg vumz';
        $mail->From = $from; 

        $mail->FromName = "HR Department"; //To address and name 
        // $mail->addAddress($to, "Recepient Name");//Recipient name is optional
        $mail->addAddress($to); //Address to which recipient will reply 
        // $mail->addReplyTo("reply@yourdomain.com", "Reply"); //CC and BCC 
        // $mail->addCC("cc@example.com"); 
        // $mail->addBCC("bcc@example.com"); //Send HTML or Plain Text email 
        $mail->isHTML(true); 
        $mail->Subject = $subject; 
        $mail->Body = $body;
        if(strlen($alt)) {
            $mail->AltBody = $alt; 
        }
        if(!$mail->send()) {
            return Array("code"=>500, "message"=>$mail->ErrorInfo);
        } else { 
            // echo "Message has been sent successfully";
            return Array("code"=>200, "message"=>"Send mail success");
        }
    }

    function getImageBase64FileExt($base64) {
        // $base64 = 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAPYA…g4ODg4ODg4ODg4DB//P+x99hmgz+VBwAAAABJRU5ErkJggg==';
        $ftags = explode(';', $base64);
        $posType = strpos($ftags[0], "/");
        return substr($ftags[0], $posType+1);
    }

    function saveFile($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

}