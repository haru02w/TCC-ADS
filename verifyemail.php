<?php
    session_name("HATIDS");
    session_start();
    require("./connection.php");

    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        $stmt = mysqli_prepare($conn, "SELECT * FROM TB_DEVELOPER WHERE TOKEN = ?");
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $stmt2 = mysqli_prepare($conn, "SELECT * FROM TB_CUSTOMER WHERE TOKEN = ?");
        mysqli_stmt_bind_param($stmt2, "s", $token);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if ($row['VERIFIED'] == 1) {
                session_unset();
                $_SESSION['v'] = "averified";
                header("Location: ../");
                exit();
            }

            $query = "UPDATE TB_DEVELOPER SET VERIFIED = 1 WHERE TOKEN = '$token'";
            if (mysqli_query($conn, $query)) {
                session_unset();
                $_SESSION['v'] = "verified";
                header("Location: ../");
                exit();
            }
        } else if (mysqli_num_rows($result2) > 0) {
            $row = mysqli_fetch_assoc($result2);

            if ($row['VERIFIED'] == 1) {
                session_unset();
                $_SESSION['v'] = "averified";
                header("Location: ../");
                exit();
            }

            $query = "UPDATE TB_CUSTOMER SET VERIFIED = 1 WHERE TOKEN = '$token'";
            if (mysqli_query($conn, $query)) {
                session_unset();
                $_SESSION['v'] = "verified";
                header("Location: ../");
                exit();
            }
        } else {
            exit("Usuario não encontrado!");
        }
    } else {
        exit("Não há nenhum token!");
    }
