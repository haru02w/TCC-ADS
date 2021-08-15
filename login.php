<?php
    session_name("HATIDS");
    session_start();
    require('connection.php');
    require('functions.php');

    $email = filter_input(INPUT_POST, 'EMAIL_LOGIN', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'PASSWORD_LOGIN', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'TYPE_LOGIN', FILTER_SANITIZE_STRING);
    if(isset($_POST['remember'])) { $remember = true; } else { $remember = false;}

    if(isEmptyInputLogin($email, $password, $type)) {
        $_SESSION['l'] = "failurel";
        header("Location: /");
        exit();
    }

    if(isValidEmail($email)) {
        $_SESSION['l'] = "failurel";
        header("Location: /");
        exit();
    }

    if(!login($email, $password, $type, $conn)) {
        if($remember) {
            setcookie('EMAIL', $email, time()+86400*30);
            setcookie('TYPE', $type, time()+86400*30);
        }
        else {
            $_SESSION['EMAIL'] = $email;
            $_SESSION['TYPE'] = $type;
        }
        if($type === "CUSTOMER") {
            header("Location: /customermenu.php");
            exit();
        }
        else {
            header("Location: /developermenu.php");
            exit();
        }
    }
    else {
        $_SESSION['l'] = "failurel";
        header("Location: /?email=$email");
        exit();
    }

?>