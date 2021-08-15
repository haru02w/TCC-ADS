<?php

    require_once('vendor/autoload.php');
    require_once('mail/PHPMailer.php');
    require_once('mail/SMTP.php');
    require_once('mail/Exception.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use ZxcvbnPhp\Zxcvbn;
    
    function isEmptyInputRegister($name, $cpf, $email, $password1, $password2, $birthdate, $type) {
        if(empty($name) || empty($cpf) || empty($email) || empty($password1) || empty($password2) || empty($birthdate) || empty($type)) {
            return true;
        }
        else {
            return false;
        }
    }

    function isEmptyInputLogin($email, $password, $type) {
        if(empty($email) || empty($password) || empty($type)) {
            return true;
        }
        else {
            return false;
        }
    }

    function isValidEmail($email) {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        else {
            return false;
        }
    }

    function isPasswordMatch($password1, $password2) {
        if($password1 !== $password2) {
            return true;
        }
        else {
            return false;
        }
    }

    function passwordStrength($password1) {
        $zxcvbn = new Zxcvbn();
        $score = $zxcvbn->passwordStrength($password1);
        if($score < 2) {
            return true;
        }
        else {
            return false;
        }
    }

    function login($email, $password, $type, $conn) {
        if($type === "DEVELOPER" || $type === "CUSTOMER") {
            $result = searchEmailType($email, $type, $conn);
            if(($result) and ($result->num_rows != 0)) {
                if($row = mysqli_fetch_assoc($result)) {
                    $passwd = $row['PASSWORD'];
                }
                if(password_verify($password, $passwd)) {
                    if($row['VERIFIED'] === 0) {
                        $_SESSION['l'] = "notverified";
                        header("Location: /");
                        exit();
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return true;
                }
            } 
            else {
                return true;
            }
            mysqli_close($conn);
        }
        else {
            return true;
        }
    }

    function register($name, $cpf, $email, $password1, $birthdate, $type, $conn) {
        if(searchCpfEmail($email, $cpf, $conn) !== false) {
            $_SESSION['r'] = "foundfailurer";
            header("Location: /");
            exit();
        }
        if($type === "CUSTOMER" || $type === "DEVELOPER"){
            $token = bin2hex(random_bytes(50));
            $verified = '0';
            $image = "images/user.png";
            $password1 = password_hash($password1, PASSWORD_DEFAULT);
            $contact = null;
            if($type == "CUSTOMER") {
                $stmt = mysqli_prepare($conn, "INSERT INTO TB_CUSTOMER (NAME, EMAIL, BIRTH_DATE, PASSWORD, CPF, IMAGE, CONTACT, VERIFIED, TOKEN) VALUES (?,?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $birthdate, $password1, $cpf, $image, $contact, $verified, $token);
                $bool = mysqli_stmt_execute($stmt);
            }
            else {
                $stmt = mysqli_prepare($conn, "INSERT INTO TB_DEVELOPER (NAME, EMAIL, BIRTH_DATE, PASSWORD, CPF, IMAGE, VERIFIED, TOKEN) VALUES (?,?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($stmt, "ssssssss", $name, $email, $birthdate, $password1, $cpf, $image, $verified, $token);
                $bool = mysqli_stmt_execute($stmt);
            }
            if($bool) {
                $content = '<!DOCTYPE html>
                <html lang="pt-BR">
                
                <head>
                    <meta charset="UTF-8">
                    <style>
                        .wrapper {
                            padding: 20px;
                            color: #444;
                            font-size: 1.3em;
                        }
                
                        a {
                            background: #00FA9A;
                            text-decoration: none;
                            padding: 8px 15px;
                            border-radius: 5px;
                            color: #ffffff;
                        }
                    </style>
                </head>
                
                <body>
                    <div class="wrapper">
                        <p> Olá '. $name .'!</p>
                        <p> Obrigado por realizar o cadastro em nosso site! Para verificar a sua conta, por favor, clique no link abaixo! </p>
                        <a href="https://hatchfy.philadelpho.tk/verifyemail.php?token='. $token .'">Verificar conta!</a>
                    </div>
                </body>
                </html>';
                $subject = "Verificação de email!";
                $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
                sendEmail($email, $subject, $content);
                return false;
            }
            else {
                return true;
            }
            mysqli_close($conn);
        }
        else {
            return true; 
        }
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
        else {
            return false;
        }
        mysqli_close($conn);
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
        else {
            return true;
        }
    }

    function searchServices($ids, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_SERVICES WHERE ID_SERVICE = ?");
        mysqli_stmt_bind_param($stmt, "s", $ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    function searchInfoDev($iddev, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT DE.NAME, DE.EMAIL, DE.BIRTH_DATE, DE.IMAGE FROM TB_DEVELOPER DE JOIN TB_SERVICES SE ON (DE.ID_DEVELOPER = SE.COD_DEVELOPER AND SE.COD_DEVELOPER = ?)");
        mysqli_stmt_bind_param($stmt, "s", $iddev);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $infodev = mysqli_fetch_assoc($result);
        return $infodev;
    }

    function searchInfoCus($idcus, $conn) {
        $stmt = mysqli_prepare($conn, "SELECT CU.NAME, CU.EMAIL, CU.BIRTH_DATE, CU.IMAGE FROM TB_CUSTOMER CU JOIN TB_SERVICES SE ON (CU.ID_CUSTOMER = SE.COD_CUSTOMER AND SE.COD_CUSTOMER = ?)");
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
        }
        catch (Exception $e){
            echo "Erro ao enviar a mensagem: {$mail->ErrorInfo}";
            echo "\n".$e;
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
        setcookie("EMAIL", '', time()-86400*30);
        setcookie("TYPE", '', time()-86400*30);
        $_SESSION['s'] = "expired";
        header("Location: /");
        exit();
    }

?>
    
    
