<?php
    session_name("HATIDS");
    session_start();
    session_destroy();
    setcookie("EMAIL", '', 1, "/", "", true);
    setcookie("TYPE", '', 1, "/", "", true);
    header("Location: ../");
    exit();
?>
