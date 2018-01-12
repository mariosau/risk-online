<?php
include "sesion.php";
if(!isset($_GET["id"])){
	header("Location: .");
}

$jugador_id = $_SESSION["id"];
$juego_id = $_GET["id"];

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Risk - Juego</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/jumbotron.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Risk Online</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href=".">Inicio</a></li>
            <li class="active"><a href="<?= $_SERVER["REQUEST_URI"] ?>">Juego actual</a></li>
            <li><a href="#">Crear juego nuevo</a></li>
            <li><a href="#">Unirme a juego</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->
	
	<div class="jumbotron">
		<canvas id="cvMap"> </canvas>
    </div>
	
    <div class="container">

		<div class="row">
			<div class="col-sm-4 jugador" id="div_1">
				<img src="img/default.png" id="img_1">
				<h2 id="nombre_1">Jugador 1</h2>
				<ul>
					<li><span id="tropas_1">x</span> Tropas</li>
					<li><span id="refuerzos_1">x</span> Refuerzos por turno</li>
					<li><span id="territorios_1">x</span> Territorios</li>
					<li><span id="cartas_1">x</span> Cartas</li>
					
				</ul>
			</div>
			<div class="col-sm-4 jugador" id="div_2">
				<img src="img/default.png" id="img_2">
				<h2 id="nombre_2">Jugador 2</h2>
				<ul>
					<li><span id="tropas_2">x</span> Tropas</li>
					<li><span id="refuerzos_2">x</span> Refuerzos por turno</li>
					<li><span id="territorios_2">x</span> Territorios</li>
					<li><span id="cartas_2">x</span> Cartas</li>
					
				</ul>
			</div>
			<div class="col-sm-4 jugador" id="div_3">
				<img src="img/default.png" id="img_3">
				<h2 id="nombre_3">Jugador 3</h2>
				<ul>
					<li><span id="tropas_3">x</span> Tropas</li>
					<li><span id="refuerzos_3">x</span> Refuerzos por turno</li>
					<li><span id="territorios_3">x</span> Territorios</li>
					<li><span id="cartas_3">x</span> Cartas</li>
					
				</ul>
			</div>
			<div class="col-sm-4 jugador" id="div_4">
				<img src="img/default.png" id="img_4">
				<h2 id="nombre_4">Jugador 4</h2>
				<ul>
					<li><span id="tropas_4">x</span> Tropas</li>
					<li><span id="refuerzos_4">x</span> Refuerzos por turno</li>
					<li><span id="territorios_4">x</span> Territorios</li>
					<li><span id="cartas_4">x</span> Cartas</li>
					
				</ul>
			</div>
			<div class="col-sm-4 jugador" id="div_5">
				<img src="img/default.png" id="img_5">
				<h2 id="nombre_5">Jugador 5</h2>
				<ul>
					<li><span id="tropas_5">x</span> Tropas</li>
					<li><span id="refuerzos_5">x</span> Refuerzos por turno</li>
					<li><span id="territorios_5">x</span> Territorios</li>
					<li><span id="cartas_5">x</span> Cartas</li>
					
				</ul>
			</div>
			<div class="col-sm-4 jugador" id="div_6">
				<img src="img/default.png" id="img_6">
				<h2 id="nombre_6">Jugador 6</h2>
				<ul>
					<li><span id="tropas_6">x</span> Tropas</li>
					<li><span id="refuerzos_6">x</span> Refuerzos por turno</li>
					<li><span id="territorios_6">x</span> Territorios</li>
					<li><span id="cartas_6">x</span> Cartas</li>
					
				</ul>
			</div>
		</div><!--/row-->

		<hr>

      <!--footer>
        <p>&copy; 2016 Company, Inc.</p>
      </footer-->

    </div><!--/.container-->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script>
	//Traer las variables de PHP
	var jugador_id = <?= $jugador_id ?>;
	var juego_id = <?= $juego_id ?>;
	var juego = {};
	</script>
    <script src="js/risk.js"></script>
  </body>
</html>