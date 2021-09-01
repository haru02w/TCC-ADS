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
    date_default_timezone_set('America/Sao_Paulo');
    require('./functions.php');

    $email = filter_input(INPUT_POST, 'EMAIL_LOGIN', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'PASSWORD_LOGIN', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'TYPE_LOGIN', FILTER_SANITIZE_STRING);
    if(isset($_POST['remember'])) { $remember = true; } else { $remember = false;}
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
    
        if(isEmptyInputLogin($email, $password, $type)) {
            echo "Por favor, preencha todos os campos!";
            exit();
        }
    
        if(isValidEmail($email)) {
            echo "O email não é valido!";
            exit();
        }
        
        if($responseData->success) {
            if(!login($email, $password, $type, $conn)) {
                if($remember == true) {
                    $cookieopt = array ( 'expires' => time()+86400*30, 'path' => '/', 'domain' => '', 'secure' => true, 'httponly' => false, 'samesite' => 'None');
                    setcookie('EMAIL', $email, $cookieopt);
                    setcookie('TYPE', $type, $cookieopt);
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
        }
        else {
            echo "Falha ao verificar o hCaptcha! Por favor, tente novamente!";
            exit();
        }
    }
