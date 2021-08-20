<?php
    date_default_timezone_set('America/Sao_Paulo');
    
    if(isset($_POST["reset-request-submit"])) {
        
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(50);
        $url = "https://hatchfy.philadelpho.tk/create-new-password/". $selector ."/". bin2hex($token) ."/";
        $expires = date("U") + 1800;
        
        require("connection.php");
        require("functions.php");
        
        $email = filter_input(INPUT_POST, "EMAILPWD_RESET", FILTER_VALIDATE_EMAIL);
        
        $stmt = mysqli_prepare($conn, "DELETE FROM TB_PASSWORDRESET WHERE EMAIL = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        
        $hashed = password_hash($token, PASSWORD_DEFAULT);
        
        $stmt = mysqli_prepare($conn, "INSERT INTO TB_PASSWORDRESET (EMAIL, SELECTOR, TOKEN, EXPIRES) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssss", $email, $selector, $hashed, $expires);
        mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
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
                        <p> Para redefinir a sua senha, por favor clique no link abaixo! Se não foi você que solicitou essa mudança de senha, por favor, ignore este email!</p>
                        <a href="'. $url .'">Redefinir senha!</a>
                    </div>
                </body>
                </html>';
        
        
        $subject = "Redefinição de senha do HatchFy!";
        $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
        
        sendEmail($email, $subject, $content);
        
        session_name("HATIDS");
        session_start();
        $_SESSION['pwd'] = "success";
        
        header("Location: /resetpassword/");
    }
    else {
        header("Location: /index.php");
        exit();
    }

?>