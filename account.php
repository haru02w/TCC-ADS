<?php
    require("./php/hidephp.php");
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
    require('./php/connection.php');
    require('./php/functions.php');

    if (isset($_COOKIE['EMAIL']) && isset($_COOKIE['TYPE'])) {
        $email = $_COOKIE['EMAIL'];
        $type = $_COOKIE['TYPE'];
    } 
    else if (isset($_SESSION['EMAIL']) && isset($_SESSION['TYPE'])) {
        if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] > 60 * 30) {
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

    if (is_null($row)) {
        expiredReturn();
    }

    $id = $row["ID_$type"];
    $rowrat = searchRating($id, $conn);
    $avgrating = mysqli_fetch_assoc(avgRating($id, $conn));

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (isset($_POST['submit'])) {
            if ($_FILES['image']['error'] === 0 && $_FILES['image']['name'] != "") {
                $temp = $_FILES['image']['tmp_name'];
                if (file_exists($temp)) {
                    if (exif_imagetype($temp)) {

                        $filename = $_FILES['image']['name'];
                        $fileext = explode('.', $filename);
                        $fileext = strtolower(array_pop($fileext));
                        $alloext = array("png", "pjp", "jpg", "pjpeg", "jpeg", "jfif");

                        if (in_array($fileext, $alloext)) {

                            if ($row['IMAGE'] !== "images/user.png") {
                                unlink($row['IMAGE']);
                            }

                            $date = date("m/d/Yh:i:sa", time());
                            $rand = rand(0, 99999);
                            $encname = $date . $rand;
                            $filename = md5($encname) . '.' . $fileext;
                            $filepath = 'allimages/' . $filename;

                            try {
                                if (!move_uploaded_file($temp, $filepath)) {
                                    throw new Exception("Falha ao mover o arquivo para o servidor! Por favor, tente novamente mais tarde!");
                                }

                                $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET IMAGE = ? WHERE ID_$type = ?");
                                mysqli_stmt_bind_param($stmt, "ss", $filepath, $id);
                                $bool = mysqli_stmt_execute($stmt);

                                if (!$bool) {
                                    unlink($filepath);
                                    throw new Exception("Falha ao enviar o link da imagem ao banco de dados! Por favor, tente novamente mais tarde!");
                                }

                                $_SESSION['servicereturn'] = array("msg" => "A imagem foi inserida com sucesso!", "class" => "is-success");
                            } 
                            catch (Exception $e) {
                                $_SESSION['servicereturn'] = array("msg" => $e->getMessage(), "class" => "is-danger");
                            } 
                            finally {
                                header("Location: /account/");
                                exit();
                            }
                        } 
                        else {
                            $_SESSION['servicereturn'] = array("msg" => "Formato de imagem inválido! Formatos suportados: PNG, PJP, JPG, PJPEG, JPEG, e JFIF.", "class" => "is-danger");
                        }
                    } 
                    else {
                        $_SESSION['servicereturn'] = array("msg" => "A imagem selecionada está com problemas! Por favor, tente novamente ou selecione outra imagem!", "class" => "is-danger");
                    }
                }
            } 
            else {
                $_SESSION['servicereturn'] = array("msg" => "Ocorreu algum erro inesperado! Por favor, tente novamente mais tarde!", "class" => "is-danger");
            }
        }
        if (isset($_POST['delete'])) {
            if ($row['IMAGE'] != "images/user.png") {
                try {
                    $actualimage = $row['IMAGE'];
                    $filepath = "images/user.png";

                    if (!mysqli_query($conn, "UPDATE TB_$type SET IMAGE = '$filepath' WHERE ID_$type = '$id'")) {
                        throw new Exception("Falha ao definir a imagem padrão! Por favor, tente novamente mais tarde");
                    }

                    if (!unlink($row['IMAGE'])) {
                        mysqli_query($conn, "UPDATE TB_$type SET IMAGE = '$actualimage' WHERE ID_$type = '$id'");
                        throw new Exception("Falha ao remover a imagem! Por favor, tente novamente mais tarde");
                    }

                    $_SESSION['servicereturn'] = array("msg" => "A imagem foi removida com sucesso!", "class" => "is-success");
                } 
                catch (Exception $e) {
                    $_SESSION['servicereturn'] = array("msg" => $e->getMessage(), "class" => "is-danger");
                } 
                finally {
                    header("Location: /account/");
                    exit();
                }
            }
        }

        if(isset($_POST['update'])) {
            $email = filter_input(INPUT_POST, 'emailupdate', FILTER_SANITIZE_EMAIL);
            $tel = filter_input(INPUT_POST, 'telupdate', FILTER_SANITIZE_STRING);

            if(empty($email) || empty($tel)) {
                $_SESSION['servicereturn'] = array("msg" => "Para atualizar os dados da conta, por favor, preencha todos os campos!", "class" => "is-danger");
            }
            else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['servicereturn'] = array("msg" => "O email inserido não é válido!", "class" => "is-danger");
            }
            else {
                $stmt = mysqli_prepare($conn, "UPDATE TB_$type SET CONTACT = ?, EMAIL = ? WHERE ID_$type = ?");
                mysqli_stmt_bind_param($stmt, "sss", $tel, $email, $id);
                $bool = mysqli_stmt_execute($stmt);
                mysqli_close($conn);
        
                if($bool) {
                    $_SESSION['servicereturn'] = array("msg" => "Os dados da conta foram alterados com sucesso!","class" => "is-success");
                }
                else {
                    $_SESSION['servicereturn'] = array("msg" => "Falha ao alterar os dados da conta! Por favor, tente novamente mais tarde!","class" => "is-danger");
                }
            }
        }
        header("Location: /account/");
        exit();
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hatchfy - Meu Perfil</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com/css2?family=Baloo+2&family=Roboto&display=swap">
    <script src="/js/vue.js"></script>
    <script src="/js/bulma-toast.min.js"></script>
    <script src="/js/v-mask.min.js"></script>
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
                                        <figure class="image is-square">
                                            <img id="image" style="object-fit: cover;" class="is-rounded" src="../<?php echo $row['IMAGE']; ?>">
                                            <form method="post" enctype="multipart/form-data" action="">
                                                <?php if ($row['IMAGE'] != "images/user.png") { ?>
                                                    <div class="edit left">
                                                        <label id="lblRemove" class="has-tooltip-multiline has-tooltip-text-centered" data-tooltip="Remover foto de perfil" @click="onClickRemoveImage"></label>
                                                    </div>
                                                    <div class="modal" :class="{'is-active': isActiveRemoveImage}">
                                                        <div class="modal-background"></div>
                                                        <div class="modal-card">
                                                            <header class="modal-card-head">
                                                                <p class="modal-card-title">Remover imagem</p>
                                                                <button type="button" class="delete" aria-label="close" @click="onClickRemoveImage"></button>
                                                            </header>
                                                            <section class="modal-card-body">Você realmente deseja remover a sua foto de perfil?</section>
                                                            <footer class="modal-card-foot">
                                                                <button type="submit" name="delete" class="button is-danger is-fullwidth has-text-centered">Remover foto de perfil</button>
                                                            </footer>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <div class="edit">
                                                    <input @click="nameImage" type="file" id="imageUpload" name="image" accept="image/png, image/jpeg">
                                                    <label id="lblEdit" class="has-tooltip-multiline has-tooltip-text-centered" for="imageUpload" data-tooltip="Adicionar foto de perfil"></label>
                                                </div>
                                                <div class="modal" :class="{'is-active': isActiveButtonImage}">
                                                    <div class="modal-background"></div>
                                                    <div class="modal-card">
                                                        <header class="modal-card-head">
                                                            <p class="modal-card-title">Confirme a sua foto de perfil</p>
                                                            <button type="button" class="delete" aria-label="close" @click="onClickButtonImage"></button>
                                                        </header>
                                                        <section class="modal-card-body">
                                                            <figure class="image is-square">
                                                                <img id="imageConfirm" src="../images/user.png" style="object-fit: cover;" class="is-rounded">
                                                            </figure>
                                                        </section>
                                                        <footer class="modal-card-foot">
                                                            <button type="submit" name="submit" class="button is-success is-fullwidth has-text-centered">Enviar foto de perfil</button>
                                                        </footer>
                                                    </div>
                                                </div>
                                            </form>
                                        </figure>
                                    </div>
                                    <div class="column">
                                        <div class="box has-text-centered">
                                            <label class="label is-medium">Nome do Usuário</label>
                                            <p class="subtitle is-5"><?php echo $row['NAME']; ?></p>
                                            <label class="label is-medium">Email do Usuário</label>
                                            <p class="subtitle is-5"><?php echo $row['EMAIL']; ?></p>
                                            <label class="label is-medium">Data de Nascimento</label>
                                            <p class="subtitle is-5"><?php echo (implode('/', array_reverse(explode('-', $row['BIRTH_DATE']), FALSE))); ?></p>
                                            <label class="label is-medium"> Telefone</label>
                                            <p class="subtitle is-5"><?php echo $row['CONTACT']; ?></p>
                                        </div>
                                        <div class="buttons is-centered">
                                            <button class="button is-info" @click="onClickUpdate"> Atualizar dados da conta</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($type == "DEVELOPER") { ?>
                                <div class="box">
                                    <?php if ($avgrating['MEDIA'] == 0) { ?>
                                        <label class="label is-large"> Avaliações </label>
                                        <div class="box has-background-primary">
                                            <p class="title is-5 has-text-white">Você ainda não possui avaliações! <a href="/search/" class="is-link">Clique aqui</a> para procurar um serviço!</p>
                                        </div>
                                    <?php } else { ?>
                                        <label class="label is-large"> Avaliações <i class="fas fa-star" style="color:#FC0;"></i> <?php echo number_format($avgrating['MEDIA'], 1) ?> </label>
                                        <?php while ($rating = mysqli_fetch_assoc($rowrat)) { ?>
                                            <article class="media">
                                                <figure class="media-left">
                                                    <p class="image is-64x64 is-square">
                                                        <img class="is-rounded" style="object-fit: cover;" src="../<?php echo $rating['IMAGE']; ?>">
                                                    </p>
                                                </figure>
                                                <div class="media-content">
                                                    <div class="content">
                                                        <p class="title is-5"> <?php echo $rating['NAME']; ?> (<?php echo $rating['TITLE']; ?>)</p>
                                                        <p class="subtitle is-5">
                                                            <?php
                                                            for ($stars = 0; $stars < $rating['NOTE']; $stars++) {
                                                                echo '<i class="fas fa-star" style="color:#FC0;"></i>';
                                                            }
                                                            if ($stars < 5) {
                                                                for ($stars; $stars < 5; $stars++) {
                                                                    echo '<i class="fas fa-star"></i>';
                                                                }
                                                            }
                                                            ?>
                                                        </p>
                                                        <p class="subtitle is-5"> <?php echo $rating['REVIEW'] ?> </p>
                                                    </div>
                                                    <nav class="level is-mobile"></nav>
                                                </div>
                                            </article>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <form action="" method="POST">
                <div class="modal" :class="{'is-active': isActiveUpdate}">
                    <div class="modal-background"></div>
                    <div class="modal-card">
                        <header class="modal-card-head">
                            <p class="modal-card-title">Atualizar dados da conta</p>
                            <button type="button" class="delete" aria-label="Fechar" @click="onClickUpdate"></button>
                        </header>
                        <section class="modal-card-body">
                            <div class="field">
                                <label class="label">Telefone</label>
                                <div class="control">
                                    <input autocomplete="on" class="input" type="tel" placeholder="Digite aqui o telefone" v-mask="maskTel()" v-model="contact" name="telupdate">
                                </div>
                            </div>
                            <div class="field">
                                <label class="label">Email</label>
                                <div class="control">
                                    <input autocomplete="on" class="input" type="email" placeholder="Digite aqui o email" v-model='email' name="emailupdate">
                                </div>
                            </div>
                        </section>
                        <footer class="modal-card-foot">
                            <button type="submit" class="button is-success" name="update">Alterar dados</button>
                            <button type="button" class="button" @click="onClickUpdate">Cancelar alteração</button>
                        </footer>
                    </div>
                </div>
            </form>
        </section>
        <?php require "baseboard.php" ?>
    </div>
    <noscript>
        <style>
            .script {
                display: none;
            }
        </style>
        <section class="hero is-fullheight">
            <div class="hero-body">
                <div class="container has-text-centered">
                    <div class="box has-text-centered">
                        <p class="title font-face"> JavaScript não habilitado! </p> <br>
                        <p class="title is-5"> Por favor, habilite o JavaScript para a página funcionar! </p>
                    </div>
                </div>
            </div>
        </section>
    </noscript>
    <script>
        Vue.directive('mask', VueMask.VueMaskDirective);
        var vue = new Vue({
            el: '#app',
            data: {
                isActiveBurger: false,
                isActiveButtonImage: false,
                isActiveRemoveImage: false,
                isActiveUpdate: false,
                contact: "<?php echo $row['CONTACT']; ?>",
                email: "<?php echo $row['EMAIL']; ?>",
            },
            methods: {
                onClickBurger() {
                    this.isActiveBurger = !this.isActiveBurger;
                },
                onClickUpdate() {
                    this.isActiveUpdate = !this.isActiveUpdate;
                },
                onClickButtonImage() {
                    this.isActiveButtonImage = !this.isActiveButtonImage;
                },
                onClickRemoveImage() {
                    this.isActiveRemoveImage = !this.isActiveRemoveImage;
                },
                nameImage() {
                    const fileInput = document.querySelector('input[type=file]');
                    const profilePic = document.querySelector("#imageConfirm");

                    function resetNameImage() {
                        fileInput.value = null;
                        vue.$data.isActiveButtonImage = false;
                    }

                    fileInput.onchange = (evt) => {
                        if (fileInput.files.length > 0) {
                            if (fileInput.files[0].size <= 5242880) {
                                const fileInputFirst = fileInput.files[0];
                                let fileext = fileInputFirst.name.split('.');
                                fileext = fileext[fileext.length - 1];
                                fileext = fileext.toLowerCase();
                                let alloext = ["png", "pjp", "jpg", "pjpeg", "jpeg", "jfif"];

                                if (alloext.includes(fileext)) {
                                    var url = window.URL || window.webkitURL;
                                    const image = new Image();
                                    image.onload = function() {
                                        const fileReader = new FileReader();
                                        fileReader.onerror = function() {
                                            fileReader.abort();
                                            resetNameImage();
                                            vue.showMessage('Ocorreu um erro ao ler a imagem! Por favor, tente novamente!', 'is-danger', 'bottom-center');
                                        }
                                        fileReader.onload = function() {
                                            profilePic.src = fileReader.result;
                                            vue.$data.isActiveButtonImage = true;
                                        }
                                        fileReader.readAsDataURL(fileInputFirst);
                                    }
                                    image.onerror = function() {
                                        resetNameImage();
                                        vue.showMessage('A imagem selecionada está com problemas! Por favor, tente novamente ou selecione outra imagem!', 'is-danger', 'bottom-center');
                                    }
                                    image.src = url.createObjectURL(fileInputFirst);
                                } else {
                                    resetNameImage();
                                    vue.showMessage('Formato de imagem inválido! Formatos suportados: PNG, PJP, JPG, PJPEG, JPEG, e JFIF.', 'is-danger', 'bottom-center');
                                }
                            } else {
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
                        duration: 6000,
                        position: position,
                        dismissible: true,
                        pauseOnHover: true,
                        closeOnClick: false,
                        animate: {
                            in: 'fadeIn',
                            out: 'fadeOut'
                        },
                    })
                },
                maskTel() {
                    if (!!this.contact) {
                        return this.contact.length == 15 ? '(##) #####-####' : '(##) ####-#####'
                    } else {
                        return '(##) #####-####'
                    }
                }
            }
        })
    </script>
    <?php
    if (isset($_SESSION['servicereturn'])) {
        echo "<script>";
        $serviceclass = $_SESSION['servicereturn']['class'];
        $servicemsg = $_SESSION['servicereturn']['msg'];
        echo "vue.showMessage('$servicemsg', '$serviceclass', 'bottom-center')";
        unset($_SESSION['servicereturn']);
        echo "</script>";
    }
    ?>

</body>

</html>