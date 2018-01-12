<?php
session_start();
include "risk.php";
//Obtener los datos del request
$juego = $_REQUEST["juego"];
$accion = $_REQUEST["accion"];
if(!isset($_SESSION["id"])) {
	$respuesta["status"] = "ERROR";
	$respuesta["mensaje"] = "No has iniciado sesion";
} else {
	$jugador = $_SESSION["id"];
	$risk = new risk();
	$risk->inicializar($juego, $jugador);

	//Enviar peticion
	if($risk->is_valido()) {
		//revisar que accion quieren
		switch($accion) {
			case "ELEGIR":
				//el parametro es solo el pais
				$pais = $_REQUEST["pais"];
				$risk->elegir($pais);
				break;
			case "AGREGAR":
				//el parametro es solo el pais
				$pais = $_REQUEST["pais"];
				$risk->elegir($pais);
				break;
			case "SINCRONIZAR":
				//Sin parametros, la respuesta tiene lo que queremos
				$risk->sincronizar();
				break;
		}
		$respuesta = $risk->get_respuesta();
	}
}
header('Content-Type: application/json');
?>
<?php echo json_encode($respuesta) ?>