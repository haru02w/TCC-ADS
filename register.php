<?php
    session_name("HATIDS");
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

    if(isEmptyInputRegister($name, $cpf, $email, $password1, $password2, $birthdate, $type)) {
        echo "Por favor, preencha todos os campos!";
        exit();
    }

    if(isValidEmail($email) ) {
        echo "O email não é válido!";
        exit();
    }

    if(isPasswordMatch($password1, $password2)) {
        echo "As senhas não conferem!";
        exit();
    }

    if(passwordStrength($password1)) {
        echo "Por favor, insira uma senha mais forte!";
        exit();
    }

    if(validateCpf($cpf)) {
        echo "O CPF não é válido!";
        exit();
    }

    if(validateDate($arraybirth[1], $arraybirth[2], $arraybirth[0], $yearphp)) {
        echo "A data inserida não é válida!";
        exit();
    }
    
    if(searchCpfEmail($email, $cpf, $conn)) {
        echo "O CPF ou email inseridos já estão cadastrados no site!";
        exit();
    }

    if(!register($name, $cpf, $email, $password1, $birthdate, $type, $conn)) {
        echo "Sucesso";
    }
    else{
        echo "Falha ao enviar os dados! Por favor, tente novamente mais tarde!";
        exit();
    }

?>
