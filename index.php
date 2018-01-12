<?php
include "sesion.php";
$jugador_id = $_SESSION["id"];

//Conectar a BD
include "bd_info";

// Crear conexion
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	$error = True;
	$mensaje = "Hay un problema en el servidor, por favor intente mas tarde";
} else {
	//Obtener juegos
	
	$result = $conn->query("SELECT j.id, j.clave, m.nombre as mapa, j.turno, j.fase, "
		." j.j_1, j1.nombre as nombre_1, j1.color as color_1, j1.picurl as picurl_1,"
		." j.j_2, j2.nombre as nombre_2, j2.color as color_2, j2.picurl as picurl_2,"
		." j.j_3, j3.nombre as nombre_3, j3.color as color_3, j3.picurl as picurl_3,"
		." j.j_4, j4.nombre as nombre_4, j4.color as color_4, j4.picurl as picurl_4,"
		." j.j_5, j5.nombre as nombre_5, j5.color as color_5, j5.picurl as picurl_5,"
		." j.j_6, j6.nombre as nombre_6, j6.color as color_6, j6.picurl as picurl_6"
		." FROM mapas m, juegos j "
		." LEFT OUTER JOIN jugadores j1 on j.j_1=j1.id "
		." LEFT OUTER JOIN jugadores j2 on j.j_2=j2.id "
		." LEFT OUTER JOIN jugadores j3 on j.j_3=j3.id "
		." LEFT OUTER JOIN jugadores j4 on j.j_4=j4.id "
		." LEFT OUTER JOIN jugadores j5 on j.j_5=j5.id "
		." LEFT OUTER JOIN jugadores j6 on j.j_6=j6.id "
		." WHERE (j1.id=$jugador_id OR j2.id=$jugador_id OR j3.id=$jugador_id OR j4.id=$jugador_id OR j5.id=$jugador_id OR j6.id=$jugador_id)");
	if ($result->num_rows >= 1) {
		$tieneJuegos = True;
		$juegos = $result->fetch_all(MYSQLI_ASSOC);
	} else {
		$tieneJuegos = False;
	}
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Risk - Pagina de inicio</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="css/offcanvas.css" rel="stylesheet">
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
            <li class="active"><a href=".">Inicio</a></li>
            <li><a href="#">Crear juego nuevo</a></li>
            <li><a href="#">Unirme a juego</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->
    <div class="container">

      <div class="row row-offcanvas row-offcanvas-right">

        <div class="col-xs-12 col-sm-12">
          
          <div class="jumbotron" style="overflow: auto;">
			<img src="<?= ($_SESSION["picurl"] != ""? $_SESSION["picurl"]:"img/default.png") ?>" class="perfil" style="border-color:<?= $_SESSION["color"]?>">
            <h1><?= $_SESSION["nombre"] ?></h1>
            <p><?= $_SESSION["email"] ?></p>
          </div>
          <div class="row">
<?php 
if(!$tieneJuegos) {
?>
            <div class="col-xs-6 col-lg-4">
              <h2>Sin juegos</h2>
              <p>Usa el menu para crear un juego nuevo</p>
            </div>
<?php 
} else {
	foreach($juegos as $juego) {
	$numJugadores = ($juego["j_1"]==null?0:1)+($juego["j_2"]==null?0:1)+($juego["j_3"]==null?0:1)+($juego["j_4"]==null?0:1)+($juego["j_5"]==null?0:1)+($juego["j_6"]==null?0:1);
?>
			<div class="col-xs-6 col-lg-4">
				<h2><a href="juego.php?id=<?= $juego["id"] ?>"><?= $juego["mapa"] ?> - <?= $numJugadores ?> jugadores</a></h2>
<?php
	for($i=1; $i<= $numJugadores; $i++) {
?>
			<p>
				<img src="<?= ($juego["picurl_" . $i] != ""? $juego["picurl_" . $i]:"img/default.png") ?>" class="miniperfil" style="border-color:<?= $juego["color_" . $i]?>">
				<?= $juego["nombre_" . $i] ?>
				<?= ($juego["turno"]==$i?" - Turno actual":"") ?>
			</p>
<?php
	}
?>
					<p> 
<?php
	switch($juego["fase"]){
		case 0:
			echo "En fase de elegir jugadores";
			break;
		case 1:
			echo "En fase de elegir territorios";
			break;
		case 2:
			echo "En fase de agregar tropas iniciales";
			break;
		case 3:
			echo "En fase de colocar tropas";
			break;
		case 4:
			echo "En fase de atacar territorios";
			break;
		case 5:
			echo "En fase de mover tropas";
			break;
		default:
			echo "En fase numero ".$juego["fase"];
	}
?>
					</p>
              <p><a class="btn btn-default" href="juego.php?id=<?= $juego["id"] ?>" role="button">Jugar ahora &raquo;</a></p>
            </div><!--/.col-xs-6.col-lg-4-->
<?php
	}
}
?>
          </div><!--/row-->
        </div><!--/.col-xs-12.col-sm-9-->
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
    <script src="js/offcanvas.js"></script>
  </body>
</html>
