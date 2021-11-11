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
    require("./connection.php");

    if (isset($_GET['token']) && isset($_GET['t'])) {
        $token = $_GET['token'];

        if($_GET['t'] == "d") {
            $t = "DEVELOPER";
        }
        else if($_GET['t'] == "c") {
            $t = "CUSTOMER";
        }
    
        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_$t WHERE TOKEN = ?");
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if ($row['VERIFIED'] == 1) {
                session_unset();
                $_SESSION['indexmsg'] = "A sua conta já foi verificada!";
                $_SESSION['indexclass'] = "warning";
                header("Location: /");
                exit();
            }

            $query = "UPDATE TB_$t SET VERIFIED = 1 WHERE TOKEN = '$token'";
            if (mysqli_query($conn, $query)) {
                session_unset();
                $_SESSION['indexmsg'] = "A sua conta foi verificada com sucesso!";
                $_SESSION['indexclass'] = "success";
                header("Location: /");
                exit();
            }
        } 
        else {
            exit("Usuario não encontrado!");
        }
    } 
    else {
        exit("Não há nenhum token!");
    }
