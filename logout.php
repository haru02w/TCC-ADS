<?php
    session_name("HATIDS");
    session_start();
    session_unset();
    session_destroy();
    setcookie("EMAIL", '', time()-86400*30);
    setcookie("TYPE", '', time()-86400*30);
    header("Location: /");
    exit();
?>