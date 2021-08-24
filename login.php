<?php
    session_name("HATIDS");
    session_start();
    date_default_timezone_set('America/Sao_Paulo');
    require('connection.php');
    require('functions.php');

    $email = filter_input(INPUT_POST, 'EMAIL_LOGIN', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'PASSWORD_LOGIN', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'TYPE_LOGIN', FILTER_SANITIZE_STRING);
    if(isset($_POST['remember'])) { $remember = true; } else { $remember = false;}

    if(isEmptyInputLogin($email, $password, $type)) {
        echo "Por favor, preencha todos os campos!";
        exit();
    }

    if(isValidEmail($email)) {
        echo "O email não é valido!";
        exit();
    }

    if(!login($email, $password, $type, $conn)) {
        if($remember == true) {
            setcookie('EMAIL', $email, time()+86400*30, '/');
            setcookie('TYPE', $type, time()+86400*30, '/');
        }
        else {
            $_SESSION['EMAIL'] = $email;
            $_SESSION['TYPE'] = $type;  
        }
        
        echo $type;
        exit();
    }
    else {
        echo "O e-mail e a senha inseridos não correspondem aos nossos registros. Por favor, verifique os dados e tente novamente!";
        exit();
    }

?>