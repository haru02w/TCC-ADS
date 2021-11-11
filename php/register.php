<?php
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
    require('./functions.php');

    date_default_timezone_set('America/Sao_Paulo'); 
    $yearphp = date('Y');

    $name = filter_input(INPUT_POST, 'NAME_REGISTER', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'CPF_REGISTER', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'EMAIL_REGISTER', FILTER_SANITIZE_EMAIL);
    $password1 = filter_input(INPUT_POST, 'PASSWORD1', FILTER_SANITIZE_STRING);
    $password2 = filter_input(INPUT_POST, 'PASSWORD2', FILTER_SANITIZE_STRING);
    $contact = filter_input(INPUT_POST, 'CONTACT', FILTER_SANITIZE_STRING);
    $birthdate = implode('-',array_reverse(explode('/',filter_input(INPUT_POST, 'BIRTH_DATE', FILTER_SANITIZE_STRING)),FALSE));
    $arraybirth = explode("-", $birthdate);
    $type = filter_input(INPUT_POST, 'TYPE_REGISTER', FILTER_SANITIZE_STRING);
    $hcapresponse = $_POST['h-captcha-response'];
    
    if(isCaptchaComplete($hcapresponse) === true) {
        echo "Por favor, complete a verificação do hCaptcha!";
        exit();
    }
    else if(isCaptchaComplete($hcapresponse) === false) {
        require('./connection.php');
        
        $hcaptchadata = array(
            'secret' => "0x1efB46BAf71d1877579b7e89c9909fde5F35e0cc",
            'response' => $hcapresponse
        );
        
        $verify = curl_init();
        curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
        curl_setopt($verify, CURLOPT_POST, true);
        curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($hcaptchadata));
        curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
        $verifyResponse = curl_exec($verify);
        $responseData = json_decode($verifyResponse);
        
        if(isEmptyInputRegister($name, $cpf, $email, $password1, $password2, $birthdate, $type, $contact)) {
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
    
        if($responseData->success) {
            if(!register($name, $cpf, $email, $password1, $birthdate, $contact, $type, $conn)) {
                echo "Sucesso";
            }
            else{
                echo "Falha ao enviar os dados! Por favor, tente novamente mais tarde!";
                exit();
            }
        }
        else {
            echo "Falha ao verificar o hCaptcha! Por favor, tente novamente!";
            exit();
        }
    }
