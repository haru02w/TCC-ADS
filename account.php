<?php
    session_name("HATIDS");
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

    if (isset($_POST['submit'])) {
        if (($_FILES['image']['name'] != "")) {
            if(file_exists($_FILES['image']['tmp_name']))  {
                
                $temp = $_FILES['image']['tmp_name'];
                $filename = $_FILES['image']['name'];
                $fileext = explode('.', $filename); $fileext = array_pop($fileext);
                $alloext = array("png", "pjp", "jpg", "pjpeg", "jpeg", "jfif");
                
                if(in_array(strtolower($fileext), $alloext)) {
                    
                    if ($row['IMAGE'] !== "./images/user.png") {
                        unlink($row['IMAGE']);
                        $row['IMAGE'] = "./images/user.png";
                    }
                    
                    $date = date("m/d/Yh:i:sa", time());
                    $rand = rand(0, 99999);
                    $encname = $date . $rand;
                    $filename = md5($encname) . '.' . $fileext;
                    $filepath = './allimages/' . $filename;
                    
                    if (move_uploaded_file($temp, $filepath)) {
                        
                        $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET IMAGE = ? WHERE ID_$type = ?");
                        mysqli_stmt_bind_param($stmt, "ss", $filepath, $id);
                        $bool = mysqli_stmt_execute($stmt);
                        if ($bool) {
                            $image = "success";
                        } else {
                            $image = "failure";
                        }
                    }
                    else {
                        $image = "failure";
                    }
                }
                else {
                    $image = "failuretype";
                }   
            }
        }
    }
    elseif(isset($_POST['delete'])) {
        if($row['IMAGE'] != "./images/user.png") {
            
            unlink($row['IMAGE']);
            $filepath = "./images/user.png";
            $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET IMAGE = ? WHERE ID_$type = ?");
            mysqli_stmt_bind_param($stmt, "ss", $filepath, $id);
            mysqli_stmt_execute($stmt);
            header("Location: ../account/");
            exit();
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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="../js/vue.js"></script>
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
                                            <img style="object-fit: cover;" class="is-rounded" src='../<?php echo $row['IMAGE']; ?>'>
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
                                            <form method="post" enctype="multipart/form-data" action="">
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
                                                        <span class="file-name"></span>
                                                    </label>
                                                </div>
                                                <br>
                                                <div v-show="isActiveButtonImage" class="field">
                                                    <button name="submit" class="button is-link"> Enviar foto de perfil </button>
                                                </div>
                                                <?php if ($row['IMAGE'] != "./images/user.png") { ?>
                                                    <div class="field">
                                                        <button name="delete" class="button is-danger"> Remover foto de perfil </button>
                                                    </div>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal" :class="topModalReturn">
            <div class="modal-background"></div>
            <div class="modal-content">
                <div class="box">
                    <article class="message" :class="messageModalReturn">
                        <div class="message-header">
                            <p v-if="isActiveReturn == 'success'">Sucesso</p>
                            <p v-else if="isActiveReturn == 'failure' || isActiveReturn == 'failuretype'">Falha</p>
                            <button class="delete" aria-label="close" @click="onClickButtonReturn"></button>
                        </div>
                        <div v-if="isActiveReturn == 'success'" class="message-body">
                            A imagem foi inserida com sucesso!
                        </div>
                        <div v-else-if="isActiveReturn == 'failure'" class="message-body">
                            Falha ao inserir a imagem! Por favor, tente novamente mais tarde!
                        </div>
                        <div v-else-if="isActiveReturn == 'failuretype'" class="message-body">
                            Formato de imagem inválido! Formatos suportados: PNG, PJP, JPG, PJPEG, JPEG, e JFIF.
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
    <noscript> <style> .script {display:none;}</style> <section class="hero is-fullheight"> <div class="hero-body"> <div class="container has-text-centered"> <div class="box has-text-centered"> <p class="title font-face"> JavaScript não habilitado! </p> <br> <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p> </div> </div> </div> </section> </noscript>
    <script>
        new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
                isActiveButtonImage: false,
                isActiveReturn: "<?php if (isset($image)) { echo $image; } ?>",
            },
            computed: {
                topModalReturn: function() {
                    return {
                        'is-active': this.isActiveReturn == 'success' || this.isActiveReturn == 'failure' || this.isActiveReturn == 'failuretype'
                    }
                },
                messageModalReturn: function() {
                    return {
                        'is-success': this.isActiveReturn == 'success',
                        'is-danger': this.isActiveReturn == 'failure' || this.isActiveReturn == "failuretype",
                    }
                }
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger
                },
                onClickLogout() {
                    window.location.replace("../logout/")
                },
                nameImage() {
                    const fileInput = document.querySelector('#file-image input[type=file]');
                    fileInput.onchange = () => {
                        if (fileInput.files.length > 0) {
                            const fileName = document.querySelector('#file-image .file-name');
                            fileName.textContent = fileInput.files[0].name;
                            this.isActiveButtonImage = true;
                        }
                    }
                },
                onClickButtonReturn() {
                    window.location.replace("../account/");
                }
            }
        })
    </script>
</body>
</html>
