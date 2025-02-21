<?php
include ('../conexao/connectDB.php');
include ('menuSuperior.php');

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial - Cadastro de Ativos</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <style>
        body.carousel-body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('senacc.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .carousel-img {
    display: block !important;
    max-width: 800px !important; 
    height: 400px !important; 
    object-fit: cover !important;
    border-radius: 10px !important; 
    margin-right: 0 !important; 
    position: relative !important;
    top: 0px !important; 
    z-index: 999 !important; 
    opacity: 1 !important; 
}
        .carousel-container {
    width: 80%;
    max-width: 800px;
    margin: 50px auto; 
    overflow: hidden;
    background-color: white; 
    padding: 20px; 
    border-radius: 10px;
    object-fit: cover !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
}

.carousel-container .carousel-item img {
    display: block !important;
    width: 100% !important;
    height: 600px !important;
    object-fit: cover !important;
    border-radius: 10px !important;
    margin-right: 0 !important; 
}

.carousel-item {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative; 
}

.carousel-caption {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    padding: 10px 20px;
    border-radius: 5px;
    font-size: 1.2em;
    color: white;
    text-align: center;
    max-width: 90%;
}
    </style>
</head>
<body class="carousel-body">

    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-item">
                <img src="start.jpeg" alt="Foto 1" id="imgg" class="carousel-img">
                <div class="carousel-caption">Equipe trabalhando no projeto</div>
            </div>
            <div class="carousel-item">
                <img src="mid.jpeg" alt="Foto 2" id="imgg" class="carousel-img">
                <div class="carousel-caption">Reunião de planejamento</div>
            </div>
            <div class="carousel-item">
                <img src="mid-2.jpeg" alt="Foto 3" id="imgg" class="carousel-img">
                <div class="carousel-caption">Implementação dos ativos</div>
            </div>
            <div class="carousel-item">
                <img src="last.jpeg" alt="Foto 4" id="imgg" class="carousel-img">
                <div class="carousel-caption">Apresentação dos resultados</div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.carousel').slick({
                dots: true,
                infinite: true,
                speed: 500,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 3000,
            });
        });
    </script>

</body>
</html>