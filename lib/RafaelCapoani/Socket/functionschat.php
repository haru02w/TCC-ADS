<?php
    date_default_timezone_set('America/Sao_Paulo');
    function searchConnectedUser($connmysqli, $ustoken, $sql) {
        $stmt = mysqli_prepare($connmysqli, $sql);
        mysqli_stmt_bind_param($stmt, "s", $ustoken);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    function searchServiceByToken($connmysqli, $setoken) {
        $stmt = mysqli_prepare($connmysqli, "SELECT * FROM TB_SERVICES WHERE TOKENCHAT = ?");
        mysqli_stmt_bind_param($stmt, "s", $setoken);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result;
    }

    function searchInfoDevByService($iddev, $connmysqli) {
        $stmt = mysqli_prepare($connmysqli, "SELECT * FROM TB_DEVELOPER DE JOIN TB_SERVICES SE ON (DE.ID_DEVELOPER = SE.COD_DEVELOPER AND SE.COD_DEVELOPER = ?)");
        mysqli_stmt_bind_param($stmt, "s", $iddev);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $infodev = mysqli_fetch_assoc($result);
        return $infodev;
    }

    function searchInfoCusByService($idcus, $connmysqli) {
        $stmt = mysqli_prepare($connmysqli, "SELECT * FROM TB_CUSTOMER CU JOIN TB_SERVICES SE ON (CU.ID_CUSTOMER = SE.COD_CUSTOMER AND SE.COD_CUSTOMER = ?)");
        mysqli_stmt_bind_param($stmt, "s", $idcus);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $infocus = mysqli_fetch_assoc($result);
        return $infocus;
    }

    function storeMessage($sender, $receiver, $service, $message, $connmysqli) {
        $postdate = time();
        $stmt = mysqli_prepare($connmysqli, "INSERT INTO TB_CHATMESSAGES (SENDER, RECEIVER, SERVICE, POSTDATE, MESSAGE) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $sender, $receiver, $service, $postdate, $message);
        mysqli_stmt_execute($stmt);
    }
