<?php
if(!isset($_GET["id"])){
	header("Location: .");
}

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
    <link href="css/juego_mono.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Ver jugadores</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand">Risk Online</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="jugador active" id="div_1">
				<a>
					<img src="img/default.png" id="img_1">
					<span id="nombre_1">Jugador 1</span>
					&#x1F482;<span id="tropas_1">x</span>
					&#x1F4AA;<span id="refuerzos_1">x</span>
					&#x1F6A9;<span id="territorios_1">x</span>
					&#x1F0CF;<span id="cartas_1">x</span>
				</a>
			</li>
			<li class="jugador" id="div_2">
				<a>
					<img src="img/default.png" id="img_2">
					<span id="nombre_2">Jugador 2</span>
					&#x1F482;<span id="tropas_2">x</span>
					&#x1F4AA;<span id="refuerzos_2">x</span>
					&#x1F6A9;<span id="territorios_2">x</span>
					&#x1F0CF;<span id="cartas_2">x</span>
				</a>
			</li>
			<li class="jugador" id="div_3">
				<a>
					<img src="img/default.png" id="img_3">
					<span id="nombre_3">Jugador 3</span>
					&#x1F482;<span id="tropas_3">x</span>
					&#x1F4AA;<span id="refuerzos_3">x</span>
					&#x1F6A9;<span id="territorios_3">x</span>
					&#x1F0CF;<span id="cartas_3">x</span>
				</a>
			</li>
			<li class="jugador" id="div_4">
				<a>
					<img src="img/default.png" id="img_4">
					<span id="nombre_4">Jugador 4</span>
					&#x1F482;<span id="tropas_4">x</span>
					&#x1F4AA;<span id="refuerzos_4">x</span>
					&#x1F6A9;<span id="territorios_4">x</span>
					&#x1F0CF;<span id="cartas_4">x</span>
				</a>
			</li>
			<li class="jugador" id="div_5">
				<a>
					<img src="img/default.png" id="img_5">
					<span id="nombre_5">Jugador 5</span>
					&#x1F482;<span id="tropas_5">x</span>
					&#x1F4AA;<span id="refuerzos_5">x</span>
					&#x1F6A9;<span id="territorios_5">x</span>
					&#x1F0CF;<span id="cartas_5">x</span>
				</a>
			</li>
			<li class="jugador" id="div_6">
				<a>
					<img src="img/default.png" id="img_6">
					<span id="nombre_6">Jugador 6</span>
					&#x1F482;<span id="tropas_6">x</span>
					&#x1F4AA;<span id="refuerzos_6">x</span>
					&#x1F6A9;<span id="territorios_6">x</span>
					&#x1F0CF;<span id="cartas_6">x</span>
				</a>
			</li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->
	
	<div class="jumbotron">
		<canvas id="cvMap"> </canvas>
    </div>
	
    <div class="container">

		<div class="row">
			
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
	var juego_id = <?= $juego_id ?>;
	var juego = {};
	</script>
    <script src="js/risk_mono.js"></script>
  </body>
</html>