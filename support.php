<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body class="backgroundTwo">
  <section class="section">
    <div class="container">
        <h1 class="title has-text-centered">Area de <b>suporte</b></h1>
        <h2 class="subtitle has-text-centered is-4">Se você teve ou tem problema com algo em nossa plataforma, você está no local certo.</h2>
        <div class="columns is-centered">
           <div class="column is-half">
             <form class="box" action="" method="post">
                 <h1 class="subtitle is-4">Preencha os campos a seguir</h1>
                 <hr class="divider">
                 <div class="field">
                   <label class="label">Email</label>
                   <div class="control has-icons-left has-icons-right">
                      <input class="input" type="email" placeholder="Digite seu email" required>
                      <span class="icon is-small is-left">
                        <i class="fas fa-envelope"></i>
                      </span>
                    </div> 
                  </div>
        
                  <div class="field">
                    <label class="label">Tipo de usuario</label>
                  </div>
                  <div class="control has-icons-left">
                    <div class="select">
                       <select name="" required>
                          <option value="" disabled selected>Selecione</option>
                          <option value="CUSTOMER">Cliente</option>
                          <option value="DEVELOPER">Desenvolvedor</option>
                       </select>
                        <div class="icon is-small is-left">
                          <i class="fas fa-users"></i>
                        </div>   
                    </div>
                 <div class="field">
                     <label class="label"><br> Coloque sua mensagem aqui</label>
                    <textarea class="textarea has-fixed-size" placeholder="Digite sua mensagem aqui"></textarea>
                 </div>
                 <div class="field">
                    <div class="control">
                    <button class="button is-link">Enviar</button>
                 </div>
             </form>
           </div>
        </div>
    </div>
 </section>
</body>
</html>
