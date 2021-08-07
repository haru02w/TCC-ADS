<?php
    session_start();
    require('connection.php');
    require('functions.php');

    date_default_timezone_set('America/Sao_Paulo'); 
    $yearphp = date('Y');

    $name = filter_input(INPUT_POST, 'NAME_REGISTER', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'CPF_REGISTER', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'EMAIL_REGISTER', FILTER_SANITIZE_EMAIL);
    $password1 = filter_input(INPUT_POST, 'PASSWORD1', FILTER_SANITIZE_STRING);
    $password2 = filter_input(INPUT_POST, 'PASSWORD2', FILTER_SANITIZE_STRING);
    $birthdate = implode('-',array_reverse(explode('/',filter_input(INPUT_POST, 'BIRTH_DATE', FILTER_SANITIZE_STRING)),FALSE));
    $arraybirth = explode("-", $birthdate);
    $type = filter_input(INPUT_POST, 'TYPE_REGISTER', FILTER_SANITIZE_STRING);

    if(isEmptyInputRegister($name, $cpf, $email, $password1, $password2, $birthdate, $type) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(isValidEmail($email) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(isPasswordMatch($password1, $password2) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(passwordStrength($password1) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(validateCpf($cpf) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(validateDate($arraybirth[1], $arraybirth[2], $arraybirth[0], $yearphp) !== false) {
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

    if(register($name, $cpf, $email, $password1, $birthdate, $type, $conn) === false) {
        $_SESSION['r'] = "successr";
        header("Location: /");
        exit();
    }else{
        $_SESSION['r'] = "failurer";
        header("Location: /");
        exit();
    }

?>
