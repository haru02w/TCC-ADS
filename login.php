<?php
    session_start();
    require('connection.php');
    require('functions.php');

    $email = filter_input(INPUT_POST, 'EMAIL_LOGIN', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'PASSWORD_LOGIN', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'TYPE_LOGIN', FILTER_SANITIZE_STRING);

    if(isEmptyInputLogin($email, $password, $type) !== false) {
        $_SESSION['l'] = "failurel";
        header("Location: /");
        exit();
    }

    if(isValidEmail($email) !== false) {
        $_SESSION['l'] = "failurel";
        header("Location: /");
        exit();
    }

    if(login($email, $password, $type, $conn) !== true) {
        $_SESSION['EMAIL'] = $email;
        $_SESSION['PASSWORD'] = $password;
        $_SESSION['TYPE'] = $type;

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