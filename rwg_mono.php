<?php
session_start();
include "risk.php";
if(!isset($_SESSION["juego"]) or isset($_REQUEST["juego"])) {
	if(isset($_REQUEST["juego"])) {
		$juego = $_REQUEST["juego"];
		$risk = new risk();
		$risk->jugadores($juego);
		$respuesta = $risk->get_respuesta();
		//TMP cuenta como inicio de sesion
		$_SESSION["juego"] = $juego;
	} else {
		$respuesta["status"] = "ERROR";
		$respuesta["mensaje"] = "No hay llave de juego";
	}
} else {
	//Obtener los datos del request
	$juego = $_SESSION["juego"];
	$jugador = $_REQUEST["jugador"];
	$accion = $_REQUEST["accion"];
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
				$risk->agregar($pais);
				break;
			case "SINCRONIZAR":
				//Sin parametros, la respuesta tiene lo que queremos
				$risk->sincronizar();
				break;
		}
	}
	$respuesta = $risk->get_respuesta();
}
header('Content-Type: application/json');
?>
<?php echo json_encode($respuesta) ?>