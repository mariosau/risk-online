<?php
session_start();
$error = False;

if(isset($_SESSION["id"])) {
	if(isset($_SESSION["redirect"])) {
		header("Location: {$_SESSION['redirect']}");
	} else {
		header("Location: ."); //default
	}
} else {
	if(isset($_POST["email"]) and isset($_POST["pw"])) {
		//Conectar a BD
		include "bd_info.php";
		
		// Crear conexion
		$conn = new mysqli($servername, $username, $password, $dbname);
		$email = $conn->real_escape_string($_POST["email"]);
		$pw = hash("sha256", $_POST["pw"]);

		// Checar conexion
		if ($conn->connect_error) {
			$error = True;
			$mensaje = "Hay un problema en el servidor, por favor intente mas tarde";
		} else {
			$result = $conn->query("SELECT id, email, nombre, color, picurl FROM jugadores WHERE email='$email' AND pwhash='$pw'");
			if ($result->num_rows == 1) {
				//Encontramos al jugador, agregar sus datos a la sesion
				$row = $result->fetch_assoc();
				$_SESSION["id"] = $row["id"];
				$_SESSION["nombre"] = $row["nombre"];
				$_SESSION["email"] = $row["email"];
				$_SESSION["color"] = $row["color"];
				$_SESSION["picurl"] = $row["picurl"];
				//redirect
				if(isset($_SESSION["redirect"])) {
					header("Location: {$_SESSION['redirect']}");
				} else {
					header("Location: ."); //default
				}
			} else {
				$error = True;
				$mensaje = "Correo/password incorrectos";
			}
		}

	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Risk Online - Iniciar sesion</title>
		<!-- Bootstrap -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery-3.2.1.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container">    
			
			<div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3"> 
				
				<div class="row">                
					<img src="img/Risk_logo.png">
				</div>
				
				<div class="panel panel-default" >
					<div class="panel-heading">
						<div class="panel-title text-center">Iniciar sesion</div>
					</div>     

					<div class="panel-body" >

						<form name="login" id="login" class="form-horizontal" method="POST">
						   
							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
								<input id="email" type="text" class="form-control" name="email" value="<?= (isset($_POST["email"])?$_POST["email"]:"") ?>" placeholder="Correo">                                        
							</div>

							<div class="input-group">
								<span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
								<input id="pw" type="password" class="form-control" name="pw" placeholder="Password">
							</div>                                                                  

							<div class="form-group">
								<!-- Button -->
								<div class="col-sm-12 controls">
									<button type="submit" href="#" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-log-in"></i> Entrar</button>                          
								</div>
							</div>

						</form>     

						<?php if($error) { ?>
						<div class="alert alert-danger" role="alert">
							<?= $mensaje ?>
						</div>
					</div>                     
				</div>  
			</div>
			
			<?php } ?>
		</div>
	</body>
</html>