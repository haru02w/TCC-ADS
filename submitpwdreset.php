<?php
    require("functions.php");
    date_default_timezone_set('America/Sao_Paulo');
    if(isset($_POST["reset-password-submit"])) {
        
        $selector = $_POST["selector"];
        $validator = $_POST["validator"];
        $password1 = $_POST["PASSWORD1"];
        $password2 = $_POST["PASSWORD2"];
        
        if(empty($password1) || empty($password2)) {
            session_name("HATIDS");
            session_start();
            $_SESSION['pwd'] = "failure";
            header("Location: /resetpassword/");
            exit();
        }
        else if ($password1 != $password2) {
            session_name("HATIDS");
            session_start();
            $_SESSION['pwd'] = "failure";
            header("Location: /resetpassword/");
            exit();
        }
        
        $cdate = date("U");
        require("connection.php");
        
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_PASSWORDRESET WHERE SELECTOR = ? AND EXPIRES >= ?");
        mysqli_stmt_bind_param($stmt, "si", $selector, $cdate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(!$row = mysqli_fetch_assoc($result)) {
            session_name("HATIDS");
            session_start();
            $_SESSION['pwd'] = "failure";
            header("Location: /resetpassword/");
            exit();
        }
        else {
            $tokenb = hex2bin($validator);
            $istoken = password_verify($tokenb, $row['TOKEN']);
            
            if($istoken === false) {
                session_name("HATIDS");
                session_start();
                $_SESSION['pwd'] = "failure";
                header("Location: /resetpassword/");
                exit();
            }
            else if ($istoken === true) {
                $tokenEmail = $row['EMAIL'];
                
                $result1 = searchEmailType($tokenEmail, "DEVELOPER", $conn);
                $result2 = searchEmailType($tokenEmail, "CUSTOMER", $conn);
                if(mysqli_fetch_assoc($result1) === null && mysqli_fetch_assoc($result2) === null) {
                    exit("Usuário não encontrado");
                }
                
                if($result1->num_rows >= 1) {
                    $type = "DEVELOPER";
                }
                else if($result2->num_rows >= 1) {
                    $type = "CUSTOMER";
                }
                $newpwdhash = password_hash($password1, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET PASSWORD = ? WHERE EMAIL = ?");
                mysqli_stmt_bind_param($stmt, "ss", $newpwdhash, $tokenEmail);
                mysqli_stmt_execute($stmt);
                
                $stmt = mysqli_prepare($conn, "DELETE FROM TB_PASSWORDRESET WHERE EMAIL = ?");
                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                mysqli_stmt_execute($stmt);
                
                session_name("HATIDS");
                session_start();
                $_SESSION['pwd'] = "updated";
                header("Location: /resetpassword/");
                exit();
            }
        }
        
    }
    else {
        header("Location : /index.php");
        exit();
    }
?>