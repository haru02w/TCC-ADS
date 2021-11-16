<?php

    require_once('vendor/autoload.php');
    require_once('mail/PHPMailer.php');
    require_once('mail/SMTP.php');
    require_once('mail/Exception.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use ZxcvbnPhp\Zxcvbn;
    
    function isEmptyInputRegister($name, $cpf, $email, $password1, $password2, $birthdate, $type, $contact) {
        if(empty($name) || empty($cpf) || empty($email) || empty($password1) || empty($password2) || empty($birthdate) || empty($type) || empty($contact)) {
            return true;
        }
        return false;
    }

    function isEmptyInputLogin($email, $password, $type) {
        if(empty($email) || empty($password) || empty($type)) {
            return true;
        }
        return false;
    }

    function isValidEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

    function isPasswordMatch($password1, $password2) {
        if($password1 !== $password2) {
            return true;
        }
        return false;
    }

    function passwordStrength($password1) {
        $zxcvbn = new Zxcvbn();
        $score = $zxcvbn->passwordStrength($password1);
        if($score < 2) {
            return true;
        }
        return false;
    }

    function login($email, $password, $type, $remember, $conn) {
        if($type === "DEVELOPER" || $type === "CUSTOMER") {
            $result = searchEmailType($email, $type, $conn);
            if(($result) and ($result->num_rows != 0)) {
                $row = mysqli_fetch_assoc($result); 
                $passwd = $row['PASSWORD'];
                if(password_verify($password, $passwd)) {
                    $options = ['cost' => 12];
                    if($row['VERIFIED'] === 0) {
                        echo "A sua conta ainda não foi verificada! Para verificar, entre em seu email e clique no link de verificação!";
                        exit();
                    }

                    if($remember == true) {
                        $cookieopt = array ( 'expires' => time()+86400*30, 'path' => '/', 'domain' => '', 'secure' => true, 'httponly' => false, 'samesite' => 'None');
                        setcookie('EMAIL', $email, $cookieopt);
                        setcookie('TYPE', $type, $cookieopt);
                    }
                    else {
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
                        $_SESSION['EMAIL'] = $email;
                        $_SESSION['TYPE'] = $type;
                        $_SESSION['TOKENCHAT'] = generateNewChatToken($conn, $email, $type);
                    }
                    if(password_needs_rehash($passwd, PASSWORD_DEFAULT, $options)) {
                        $hash = password_hash($password, PASSWORD_DEFAULT, $options);
                        $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET PASSWORD = ? WHERE ID_$type = ?");
                        mysqli_stmt_bind_param($stmt, "ss", $hash, $row["ID_$type"]);
                        $bool = mysqli_stmt_execute($stmt);
                        
                        if($bool) {
                            return false;
                        }
                        return true;
                    }
                    return false;
                }
                return true;
            } 
            mysqli_close($conn);
            return true;
        }
        return true;
    }

    function register($name, $cpf, $email, $password1, $birthdate, $contact, $type, $conn) {
        if($type === "CUSTOMER" || $type === "DEVELOPER"){
            $token = bin2hex(random_bytes(50).date("h:i:sa"));
            $verified = '0';
            $image = "images/user.png";
            $options = ['cost' => 12];
            $password1 = password_hash($password1, PASSWORD_DEFAULT, $options);
            if($type == "CUSTOMER") {
                $stmt = mysqli_prepare($conn, "INSERT INTO TB_CUSTOMER (NAME, EMAIL, BIRTH_DATE, PASSWORD, CPF, IMAGE, CONTACT, VERIFIED, TOKEN) VALUES (?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $birthdate, $password1, $cpf, $image, $contact, $verified, $token);
                $bool = mysqli_stmt_execute($stmt);
            }
            else {
                $stmt = mysqli_prepare($conn, "INSERT INTO TB_DEVELOPER (NAME, EMAIL, BIRTH_DATE, PASSWORD, CPF, IMAGE, VERIFIED, TOKEN, CONTACT) VALUES (?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $birthdate, $password1, $cpf, $image, $verified, $token, $contact);
                $bool = mysqli_stmt_execute($stmt);
            }
            if($bool) {
                if($type == "CUSTOMER") {
                    $t = "c";
                }
                else {
                    $t = "d";
                }
                $content = '<!DOCTYPE html> <html lang="pt-BR"> <head> <meta http-equiv="content-type" content="text/html; charset=UTF-8"> <style> .wrapper { padding: 20px; color: #444; font-size: 1.3em; } .link { background: #00fa9a; text-decoration: none; padding: 8px 15px; border-radius: 5px; color: #ffffff; } </style> </head> <body> <div class="wrapper"> <p>Olá '. $name .'!</p> <p> Obrigado por realizar o cadastro em nosso site! Para verificar a sua conta, por favor, clique no botão abaixo! </p> <a class="link" href="https://hatchfy.philadelpho.tk/verifyemail/'. $token .'/'.$t.'/"> Verificar conta! </a> <p>Caso você não consiga clicar no botão, copie e cole esse link abaixo na barra de endereços do seu navegador! </p> <a href="https://hatchfy.philadelpho.tk/verifyemail/'. $token .'/'.$t.'/">https://hatchfy.philadelpho.tk/verifyemail/'. $token .'/'.$t.'/</a> </div> </body> </html>';
                $subject = "Verificação de email!";
                $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
                sendEmail($email, $subject, utf8_decode($content));
                return false;
            }
            mysqli_close($conn);
            return true;
        }
        return true;
    }

    function statusChanged($email, $subject, $content){
        $content = '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><style>.wrapper {padding: 20px;color: #444;font-size: 1.3em;}a {background: #00FA9A;text-decoration: none;padding: 8px 15px;border-radius: 5px;color: #ffffff;}</style></head><body><div class="wrapper"><p> Oi, tudo certo? </p><p> '.$content.' Clique no link abaixo e faça login para mais informações! </p><a href="https://hatchfy.philadelpho.tk/">Clique Aqui</a></div></body></html>';
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        if(!sendEmail($email, $subject, utf8_decode($content))){
            return true;
        }
        return false;
    }

    function searchCpfEmail($email, $cpf, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_CUSTOMER WHERE EMAIL= ? OR CPF = ?");
        mysqli_stmt_bind_param($stmt, "ss", $email, $cpf);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $rowstmt = mysqli_stmt_num_rows($stmt);

        $stmt2 = mysqli_prepare($conn, "SELECT * FROM TB_DEVELOPER WHERE EMAIL = ? OR CPF = ?");
        mysqli_stmt_bind_param($stmt2, "ss", $email, $cpf);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_store_result($stmt2);
        $rowstmt2 = mysqli_stmt_num_rows($stmt2);

        if($rowstmt != 0 || $rowstmt2 != 0) {
            return true;
        }
        return false;
    }

    function validateCpf($cpf) {
 
        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
         
        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return true;
        }
    
        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return true;
        }
    
        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return true;
            }
        }
        return false;
    }

    function validateDate($month, $day, $year, $yearphp){
        if($year < $yearphp - 100 || $year > $yearphp) {
            return true;
        }
        if(checkdate($month, $day, $year)) {
            return false;
        }
        return true;
    }

    function searchServices($ids, $title, $creationDate, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES S
        JOIN TB_CATEGORY C ON (C.ID_CATEGORY = S.COD_CATEGORY AND BINARY CLEANTITLE = ? AND ID_SERVICE = ? AND CREATIONDATE = ?)");
        mysqli_stmt_bind_param($stmt, "sss", $title, $ids, $creationDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    function searchInfoDev($iddev, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT DE.NAME, DE.EMAIL, DE.BIRTH_DATE, DE.IMAGE, DE.CONTACT FROM TB_DEVELOPER DE JOIN TB_SERVICES SE ON (DE.ID_DEVELOPER = SE.COD_DEVELOPER AND SE.COD_DEVELOPER = ?)");
        mysqli_stmt_bind_param($stmt, "s", $iddev);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $infodev = mysqli_fetch_assoc($result);
        return $infodev;
    }

    function searchInfoCus($idcus, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT CU.NAME, CU.EMAIL, CU.BIRTH_DATE, CU.IMAGE, CU.CONTACT FROM TB_CUSTOMER CU JOIN TB_SERVICES SE ON (CU.ID_CUSTOMER = SE.COD_CUSTOMER AND SE.COD_CUSTOMER = ?)");
        mysqli_stmt_bind_param($stmt, "s", $idcus);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $infocus = mysqli_fetch_assoc($result);
        return $infocus;
    }

    function sendEmail($to, $subject, $content) {
        try {
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->Mailer = "smtp";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;
            $mail->Host = 'smtp.gmail.com';
            $mail->Username = 'hatchfy@gmail.com';
            $mail->Password = 't*D-MQ@!g78fd]K';
            $mail->SetLanguage('pt-br', 'mail/phpmailer.lang-pt_br.php');
            $mail->SetFrom('hatchfy@gmail.com', 'HatchFy');
            $mail->AddAddress($to);
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->MsgHTML($content);
            if(!$mail->Send()){
              exit(var_dump($mail));
            }
            return false;
        }
        catch (Exception $e){
            echo "Erro ao enviar a mensagem: {$mail->ErrorInfo}";
            echo "\n".$e;
            return true;
        }
    }

    function searchEmailType($email, $type, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_$type WHERE EMAIL = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }
    
    function expiredReturn() {
        session_unset();
        $cookieopt = array ( 'expires' => 1, 'path' => '/', 'domain' => '', 'secure' => true, 'httponly' => false, 'samesite' => 'None'); 
        setcookie("EMAIL", '', $cookieopt);
        setcookie("TYPE", '', $cookieopt);
        $_SESSION['indexmsg'] = "A sua sessão expirou! Por favor, logue no sistema novamente!";
        $_SESSION['indexclass'] = "warning";
        header("Location: /");
        exit();
    }
    
    function isCaptchaComplete($hcapresponse) {
        if(empty($hcapresponse) OR $hcapresponse == "") {
            return true;
        }
        return false;
    }
    
    function searchRating($id, $conn) { 
        $stmt = mysqli_prepare($conn, "SELECT CU.NAME, CU.IMAGE, R.NOTE, R.REVIEW, S.TITLE FROM TB_RATING R
        JOIN TB_SERVICES S ON (S.COD_DEVELOPER = ? AND S.ID_SERVICE = R.COD_SERVICE)
        JOIN TB_CUSTOMER CU ON (S.COD_CUSTOMER = CU.ID_CUSTOMER)");
        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }
    
    function avgRating($id, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT AVG(R.NOTE) AS MEDIA FROM TB_RATING R
        JOIN TB_SERVICES S ON (S.COD_DEVELOPER = ? AND S.ID_SERVICE = R.COD_SERVICE)");
        mysqli_stmt_bind_param($stmt, "s", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    function publishedDate($timestamp) {
        $acttimestamp = time();
        $difftime = $acttimestamp - $timestamp;

        if($difftime < 60) {
            return "Publicado agora mesmo.";
        }
        switch($difftime) {
            case ($difftime >= 60 && $difftime < 3600):
                if($difftime < 120) {
                    return "Publicado: há 1 minuto";
                    break;
                }
                return "Publicado: há ". floor($difftime / 60) . " minutos";
                break;
            case ($difftime >= 3600 && $difftime < 86400):
                if($difftime < 7200) {
                    return "Publicado: há 1 hora";
                    break;
                }
                return "Publicado: há ". floor($difftime / 3600) . " horas";
                break;
            case ($difftime >= 86400 && $difftime < 604800):
                if($difftime < 172800) {
                    return "Publicado: Ontem";
                    break;
                }
                return "Publicado: há ". floor($difftime / 86400) . " dias";
                break;
            case ($difftime >= 604800):
                if($difftime < 1209600) {
                    return "Publicado: há 1 semana";
                    break;
                }
                return "Publicado: há ". floor($difftime / 604800) . " semanas";
                break;
        }
    }

    function generateNewChatToken($conn, $email, $type) {
        $token = bin2hex(random_bytes(10).date("h:i:sa"));
        $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET TOKENCHAT = ? WHERE EMAIL = ?");
        mysqli_stmt_bind_param($stmt, "ss", $token, $email);
        mysqli_stmt_execute($stmt);
        return $token;
    }
