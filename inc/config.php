<?php
// define("ENV", "prod");
// define("ENV", "sit");
// define("ENV", "uat");
// define("ENV", "local");
define("ENV", "debug");
define("TEST_MAIL", true);

define("API_VERSION", "2024-03-26-001");

if(ENV === "local") {
    define("API_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("SECRET_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("DB_HOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "1234");
    define("DB_DATABASE_NAME", "owlapp");

    if(TEST_MAIL) {
        define("MAILER_USER", "mrnarong@gmail.com");
        define("MAILER_PASS", "tddn pdcr bdao oeul ");
        define("LEAVE_APPROVER_MAIL", "mrnarong@gmail.com");
        define("CLIENT_WEB_URL", "http://localhost:3000");
        
    } else {
        define("MAILER_USER", "HR@owlswallpapers.com");
        define("MAILER_PASS", "juwd hpdd uvsg vumz");
        define("LEAVE_APPROVER_MAIL", "pattrawan@owlswallpapers.com");
        define("CLIENT_WEB_URL", "http://hr-app.owlswallpapers.com/owl-client");
    }

} else if(ENV === "debug") {
    define("API_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("SECRET_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("DB_HOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "1234");
    define("DB_DATABASE_NAME", "owls-app-prod");

    if(TEST_MAIL) {
        define("MAILER_USER", "mrnarong@gmail.com");
        define("MAILER_PASS", "tddn pdcr bdao oeul ");
        define("LEAVE_APPROVER_MAIL", "mrnarong@gmail.com");
        define("CLIENT_WEB_URL", "http://localhost:3000");
        
    } else {
        define("MAILER_USER", "HR@owlswallpapers.com");
        define("MAILER_PASS", "juwd hpdd uvsg vumz");
        define("LEAVE_APPROVER_MAIL", "pattrawan@owlswallpapers.com");
        define("CLIENT_WEB_URL", "http://hr-app.owlswallpapers.com/owl-client");
    }

}  else if(ENV === "uat") {
    define("API_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("SECRET_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("DB_HOST", "localhost");
    define("DB_USERNAME", "root");
    define("DB_PASSWORD", "1234");
    define("DB_DATABASE_NAME", "owlapp");

    define("MAILER_USER", "");
    define("MAILER_PASS", "");
    define("LEAVE_APPROVER_MAIL", "");
    define("CLIENT_WEB_URL", "");

}  else if(ENV === "sit") {
    define("API_KEY", "kD3l@gs56jsDG#s9@Fgj4839_sit");
    define("SECRET_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("DB_HOST", "localhost");
    define("DB_USERNAME", "owlswallpa_owl_app");
    define("DB_PASSWORD", "hyjAanj5aF8xdUP");
    define("DB_DATABASE_NAME", "owlswallpa_owl_app_sit");

    define("MAILER_USER", "");
    define("MAILER_PASS", "");
    define("LEAVE_APPROVER_MAIL", "");
    define("CLIENT_WEB_URL", "");
    // /home/owlswallpa/domains/hr-app.owlswallpapers.com/public_html/api/info.php

} else if(ENV === "prod") {
    define("API_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("SECRET_KEY", "kD3l@gs56jsDG#s9@Fgj4839");
    define("DB_HOST", "localhost");
    define("DB_USERNAME", "owlswallpa_owl_app");
    define("DB_PASSWORD", "hyjAanj5aF8xdUP");

    // define("DB_USERNAME", "owlswallpa_owl_app2");
    // define("DB_PASSWORD", "t4jAu3P8SSNybavBN8N5");

    
    define("DB_DATABASE_NAME", "owlswallpa_owl_app");

    define("MAILER_USER", "HR@owlswallpapers.com");
    define("MAILER_PASS", "juwd hpdd uvsg vumz");
    define("LEAVE_APPROVER_MAIL", "pattrawan@owlswallpapers.com");
    define("CLIENT_WEB_URL", "http://hr-app.owlswallpapers.com/owl-client");
}

?>

