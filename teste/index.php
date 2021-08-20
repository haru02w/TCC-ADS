<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h1> Avalie<h1>

    <form method="POST" action="processa.php" enctype="multipart/form-data">
    <div class="estrelas">
        <input type="radio" id="vazio" name="estrela" value="" checked>
        <label for="estrela_1"><i class="fa"></i></label>
        <input type="radio" id="estrela_1" name="estrela" value="1">

        <label for="estrela_2"><i class="fa"></i></label>
        <input type="radio" id="estrela_2" name="estrela" value="2">

        <label for="estrela_3><i class="fa"></i></label>
        <input type="radio" id="estrela_3" name="estrela" value="3">

        <label for="estrela_4"><i class="fa"></i></label>
        <input type="radio" id="estrela_4" name="estrela" value="4">

        <label for="estrela_5"><i class="fa"></i></label>
        <input type="radio" id="estrela_5" name="estrela" value="5">

        <label for="estrela_6"><i class="fa"></i></label>
        <input type="radio" id="estrela_6" name="estrela" value="6">
    </div>
</form>
    
</body>
</html>