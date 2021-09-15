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
    require('./connection.php');
    require('./functions.php');
    
    if(isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
        $email = $_COOKIE['EMAIL'];
        $type = $_COOKIE['TYPE'];
    }
    else if(isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
        if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
            expiredReturn();
        }
        $_SESSION['LAST_ACTIVITY'] = time();
        $email = $_SESSION['EMAIL'];
        $type = $_SESSION['TYPE'];
    }
    else {
        expiredReturn();
    }
    
    $row = mysqli_fetch_assoc(searchEmailType($email, $type, $conn));
    
    if(is_null($row)) {
        expiredReturn();
    }
    
    $id = $row["ID_$type"];
    $rowrat = searchRating($id, $conn);
    $avgrating = mysqli_fetch_assoc(avgRating($id, $conn));
    
    if($_SERVER['REQUEST_METHOD'] === "POST") {
        if (isset($_POST['submit'])) {
            if($_FILES['image']['error'] === 0 && $_FILES['image']['name'] != "") {
                $temp = $_FILES['image']['tmp_name'];
                if(file_exists($temp)) {
                    if(exif_imagetype($temp)) {
                        
                        $filename = $_FILES['image']['name'];
                        $fileext = strtolower(array_pop(explode('.', $filename)));
                        $alloext = array("png", "pjp", "jpg", "pjpeg", "jpeg", "jfif");
                            
                        if(in_array($fileext, $alloext)) {
                                
                            if ($row['IMAGE'] !== "/images/user.png") {
                                unlink($row['IMAGE']);
                                $row['IMAGE'] = "/images/user.png";
                            }
                                
                            $date = date("m/d/Yh:i:sa", time());
                            $rand = rand(0, 99999);
                            $encname = $date . $rand;
                            $filename = md5($encname) . '.' . $fileext;
                            $filepath = 'allimages/' . $filename;
                                
                            try {
                                if(!move_uploaded_file($temp, $filepath)) {
                                    throw new RuntimeException("Falha ao mover o arquivo para o servidor! Por favor, tente novamente mais tarde!");
                                }
                                    
                                $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET IMAGE = ? WHERE ID_$type = ?");
                                mysqli_stmt_bind_param($stmt, "ss", $filepath, $id);
                                $bool = mysqli_stmt_execute($stmt);
                                    
                                if(!$bool) {
                                    unlink($filepath);
                                    throw new RuntimeException("Falha ao enviar o link da imagem ao banco de dados! Por favor, tente novamente mais tarde!");
                                }
                                    
                                $_SESSION['servicemsg'] = "A imagem foi inserida com sucesso!";
                                $_SESSION['serviceclass'] = "is-success";
                            } 
                            catch (RuntimeException $e) {
                                $_SESSION['servicemsg'] = $e->getMessage();
                                $_SESSION['serviceclass'] = "is-danger";
                            }            
                        }
                        else {
                            $_SESSION['servicemsg'] = "Formato de imagem inválido! Formatos suportados: PNG, PJP, JPG, PJPEG, JPEG, e JFIF.";
                            $_SESSION['serviceclass'] = "is-danger";
                        }
                        header("Location: /account/");
                        exit();
                    }
                    else {
                        $_SESSION['servicemsg'] = "A imagem selecionada está com problemas! Por favor, tente novamente ou selecione outra imagem!";
                        $_SESSION['serviceclass'] = "is-danger";
                    }
                }
            }
            else {
                $_SESSION['servicemsg'] = "Ocorreu algum erro inesperado! Por favor, tente novamente mais tarde!";
                $_SESSION['serviceclass'] = "is-danger";
            }
        }
        else if(isset($_POST['delete'])) {
            if($row['IMAGE'] != "/images/user.png") {
                try {
                    $actualimage = $row['IMAGE'];
                    $filepath = "/images/user.png";
                    
                    if(!mysqli_query($conn, "UPDATE TB_$type SET IMAGE = '$filepath' WHERE ID_$type = '$id'")) {
                        throw new RuntimeException("Falha ao definir a imagem padrão! Por favor, tente novamente mais tarde");
                    }
                    
                    if(!unlink($row['IMAGE'])){
                        mysqli_query($conn, "UPDATE TB_$type SET IMAGE = '$actualimage' WHERE ID_$type = '$id'");
                        throw new RuntimeException("Falha ao remover a imagem! Por favor, tente novamente mais tarde");
                    }
                    
                    $_SESSION['servicemsg'] = "A imagem foi removida com sucesso!";
                    $_SESSION['serviceclass'] = "is-success";
                    
                } catch (RuntimeException $e) {
                    $_SESSION['servicemsg'] = $e->getMessage();
                    $_SESSION['serviceclass'] = "is-danger";
                }

                header("Location: /account/");
                exit();
            }
        }
    }
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="/js/vue.js"></script>
   <script src="/js/bulma-toast.min.js"></script>
</head>
<body class="background">
    <div id="app" class="script">
        <?php if ($type == "CUSTOMER") {
            require("./headercustomer.php");
        } else {
            require("./headerdeveloper.php");
        } ?>
        <br>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container">
                    <section class="hero is-dark">
                        <div class="hero-body is-dark">
                            <p class="title">
                                Minha Conta 
                            </p>
                        </div>
                    </section>
                    <div class="section">
                        <div class="container">
                            <div class="box has-background-grey">
                                <div class="columns is-vcentered">
                                    <div class="column">
                                        <figure class="image is-square ">
                                            <img id="image" style="object-fit: cover;" class="is-rounded" src="../<?php echo $row['IMAGE']; ?>">
                                        </figure>
                                    </div>
                                    <div class="column">
                                        <div class="box has-text-centered">
                                            <label class="label is-medium">Nome do Usuário</label>
                                            <p class="subtitle is-5"><?php echo $row['NAME']; ?></p>
                                            <label class="label is-medium">Email do Usuário</label>
                                            <p class="subtitle is-5"><?php echo $row['EMAIL']; ?></p>
                                            <label class="label is-medium">Data de Nascimento</label>
                                            <p class="subtitle is-5"><?php echo(implode('/',array_reverse(explode('-',$row['BIRTH_DATE']),FALSE)));?></p>
                                            <form id="formFile" method="post" enctype="multipart/form-data" action="">
                                                <label class="label is-medium">Foto de perfil (Max 5MB)</label>
                                                <div class="box has-text-centered" id="imgpreview" style="display: none;">
                                                    <div class="title">
                                                        <p class="title is-5 has-text-danger"> Você está vendo apenas uma prévia da imagem! Para aplica-la, por favor, clique no botão "Enviar foto de perfil"!</p>
                                                    </div>
                                                </div>
                                                <div id="file-image" class="file has-name is-boxed is-centered">
                                                    <label class="file-label">
                                                        <input class="file-input" @click="nameImage" type="file" name="image" accept="image/png, image/jpeg">
                                                        <span class="file-cta">
                                                            <span class="file-icon">
                                                                <i class="fas fa-upload"></i>
                                                            </span>
                                                            <span class="file-label">
                                                                Selecionar<br>imagem...
                                                            </span>
                                                        </span>
                                                        <span id="fileN" class="file-name"></span>
                                                    </label>
                                                </div>
                                                <br>
                                                <div v-show="isActiveButtonImage" class="field">
                                                    <button type="submit" name="submit" class="button is-link"> Enviar foto de perfil </button>
                                                </div>
                                                <?php if ($row['IMAGE'] != "/images/user.png") { ?>
                                                    <div class="field">
                                                        <button type="submit" name="delete" class="button is-danger"> Remover foto de perfil </button>
                                                    </div>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if($type == "DEVELOPER") { ?>
                                <div class="box">     
                                    <label class="label is-large">Avaliações<i class="fas fa-star" style="color:#FC0;"></i> <?php echo number_format($avgrating['MEDIA'], 1)?></label>
                                    <?php if($avgrating['MEDIA'] == 0) { ?>
                                        <div class="box has-background-primary">
                                            <p class="title is-5 has-text-white">Você ainda não possui avaliações! <a href="/search/" class="is-link">Clique aqui</a> para procurar um serviço!</p>
                                        </div> 
                                    <?php } 
                                    else { ?>
                                        <?php while($rating = mysqli_fetch_assoc($rowrat)) { ?> 
                                            <div class="box has-background-info">
                                                <p class="title is-5 has-text-white"> <?php echo $rating['NAME']?> Nota: <?php echo $rating['NOTE'] ?></p>
                                                <p class="subtitle is-5 has-text-white"> <?php echo $rating['REVIEW']?> </p>
                                            </div>
                                        <?php }?>
                                    <?php } ?>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <?php require "baseboard.php"?>
        </section>
    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
    var profilePicSrc = document.querySelector("#image").src;
    var vue = new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
                isActiveButtonImage: false,
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickLogout() {
                    window.location.replace("/logout/")
                },
                nameImage() {
                    const fileInput = document.querySelector('input[type=file]');
                    const fileName = document.querySelector('#fileN');
                    const profilePic = document.querySelector("#image");
                    const previewWarning = document.querySelector("#imgpreview");
                    
                    function resetNameImage() {
                        fileInput.value = null;
                        previewWarning.style = "display: none;";
                        vue.$data.isActiveButtonImage = false;
                        profilePic.src = profilePicSrc;
                        fileName.textContent = "";
                    }   
                    
                    fileInput.onchange = (evt) => {
                        if (fileInput.files.length > 0) {
                            if(fileInput.files[0].size <= 5242880) {
                                const fileInputFirst = fileInput.files[0];
                                let fileext = fileInputFirst.name.split('.');
                                fileext = fileext[fileext.length - 1]; fileext = fileext.toLowerCase();
                                let alloext = ["png", "pjp", "jpg", "pjpeg", "jpeg", "jfif"];
                                
                                if(alloext.includes(fileext)) {
                                    var url = window.URL || window.webkitURL;
                                    const image = new Image();
                                    image.onload = function () {
                                        const fileReader = new FileReader();
                                        fileReader.onerror = function () {
                                            fileReader.abort();
                                            resetNameImage();
                                            vue.showMessage('Ocorreu um erro ao ler a imagem! Por favor, tente novamente!', 'is-danger', 'bottom-center');
                                        }
                                        fileReader.onload = function() {
                                            profilePic.src = fileReader.result;
                                            previewWarning.style = "display: block;";
                                            fileName.textContent = fileInputFirst.name;
                                            vue.$data.isActiveButtonImage = true;
                                        }
                                        fileReader.readAsDataURL(fileInputFirst);
                                    }
                                    image.onerror = function () {
                                        resetNameImage();
                                        vue.showMessage('A imagem selecionada está com problemas! Por favor, tente novamente ou selecione outra imagem!', 'is-danger', 'bottom-center');
                                    }
                                    image.src = url.createObjectURL(fileInputFirst);
                                }
                                else {
                                    resetNameImage();
                                    vue.showMessage('Formato de imagem inválido! Formatos suportados: PNG, PJP, JPG, PJPEG, JPEG, e JFIF.', 'is-danger', 'bottom-center');
                                }
                            }
                            else {
                                resetNameImage();
                                vue.showMessage('O tamanho máximo permitido para a foto de perfil é de 5MB! Por favor, insira outra foto!', 'is-danger', 'bottom-center');
                            }
                        }
                    }
                },
                showMessage(message, messageclass, position) {
                    bulmaToast.toast({ 
                        message: message,
                        type: messageclass, 
                        duration: 5000, 
                        position: position, 
                        dismissible: true, 
                        pauseOnHover: true, 
                        closeOnClick: false,
                        animate: { in: 'fadeIn', out: 'fadeOut' }, 
                    })
                }
            }
        })
    </script>
    <?php if (isset($_SESSION['servicemsg'])) {
        echo "<script>";
        $serviceclass = $_SESSION['serviceclass'];
        $servicemsg = $_SESSION['servicemsg'];
        echo "vue.showMessage('$servicemsg', '$serviceclass', 'bottom-center')";
        unset($_SESSION['servicemsg']);
        unset($_SESSION['serviceclass']);
        echo "</script>";
    } ?>
</body>
</html>
