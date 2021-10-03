<?php
    $requesteduri = explode('.', $_SERVER["REQUEST_URI"]);
    if(strtolower(array_pop($requesteduri)) == "php") {
        http_response_code(300);
        include("./errors/404.html");
        die();
    }
    
    if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on'))) || (isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == 443)) {
        $protocol = "https://";
    }
    else {
        $protocol = "http://";
    }
?>