<?php
    require("./hidephp.php");
    session_name("HATIDS");
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => "",
        'secure' => true,
        'httponly' => false,
        'samesite' => 'None'
      ]);
    session_start();
    session_destroy();
    $cookieopt = array ( 'expires' => 1, 'path' => '/', 'domain' => '', 'secure' => true, 'httponly' => false, 'samesite' => 'None'); 
    setcookie("EMAIL", '', $cookieopt);
    setcookie("TYPE", '', $cookieopt);
    header("Location: /");
    exit();
