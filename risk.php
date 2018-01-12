<?php
const F_JUGADORES = 0;
const F_ELEGIR = 1;
const F_AGREGAR = 2;
const F_JUEGO_REFUERZO = 3;
const F_JUEGO_BATALLA = 4;
const F_JUEGO_MOVIMIENTO = 5;

const VERSION = "0.1";
class risk {
	private $valido = false;
	private $conn;
	private $respuesta;
	private $fase;
	private $turno;
	private $id_juego;
	private $id_mapa;
	private $id_jugadores;
	private $i_actual;
	
	//ya no tengo constructor, porque hacia cosas raras...
	function inicializar($id,$jugador) {
		include "bd_info.php";
		
		$this->conn = new mysqli($servername, $username, $password, $dbname);
		if ($this->conn->connect_error) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Error de conexion: " . $this->conn->connect_error;
		}
		$sql = "SELECT fase, turno, j_1, j_2, j_3, j_4, j_5, j_6, id_mapa FROM juegos WHERE id=$id";
		$result = $this->conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$this->id_juego = $id;
			$this->fase = $row["fase"];
			$this->turno = (int)$row["turno"];
			$this->id_mapa = $row["id_mapa"];
			$this->id_jugadores[1] = $row["j_1"];
			$this->id_jugadores[2] = $row["j_2"];
			$this->id_jugadores[3] = $row["j_3"];
			$this->id_jugadores[4] = $row["j_4"];
			$this->id_jugadores[5] = $row["j_5"];
			$this->id_jugadores[6] = $row["j_6"];
			//hasta ahora tenemos los datos del juego, va bien
			//Ahora validamos al jugador
			$this->i_actual = array_search($jugador,$this->id_jugadores);
			if($this->i_actual != null) {
				$this->valido = true; //OK!
			} else {
				$this->respuesta["status"] = "ERROR";
				$this->respuesta["mensaje"] = "No existe el jugador en este juego";
				$this->valido = false;
			}
		} else {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Juego inexistente";
			$this->valido = false;
		}
	}
	
	//este pseudo-constructor solo toma el ID para ver quienes juegan
	function jugadores($id) {
		include "bd_info.php";
		
		$this->conn = new mysqli($servername, $username, $password, $dbname);
		if ($this->conn->connect_error) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Error de conexion: " . $this->conn->connect_error;
		}
		$sql = "SELECT
				j.j_1, j1.nombre as nombre_1, j1.color as color_1, j1.picurl as picurl_1,
				j.j_2, j2.nombre as nombre_2, j2.color as color_2, j2.picurl as picurl_2,
				j.j_3, j3.nombre as nombre_3, j3.color as color_3, j3.picurl as picurl_3,
				j.j_4, j4.nombre as nombre_4, j4.color as color_4, j4.picurl as picurl_4,
				j.j_5, j5.nombre as nombre_5, j5.color as color_5, j5.picurl as picurl_5,
				j.j_6, j6.nombre as nombre_6, j6.color as color_6, j6.picurl as picurl_6
			FROM
				juegos j
				LEFT OUTER JOIN jugadores j1
					ON j.j_1=j1.id
				LEFT OUTER JOIN jugadores j2
					ON j.j_2=j2.id
				LEFT OUTER JOIN jugadores j3
					ON j.j_3=j3.id
				LEFT OUTER JOIN jugadores j4
					ON j.j_4=j4.id
				LEFT OUTER JOIN jugadores j5
					ON j.j_5=j5.id
				LEFT OUTER JOIN jugadores j6
					ON j.j_6=j6.id
			WHERE
				j.id=$id";
		$result = $this->conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			for($i = 1; $i <= 6 ; $i++){
				if($row["j_$i"] != null) {
					$respuesta["jugadores"][$i]["id"] = $row["j_$i"];
					$respuesta["jugadores"][$i]["nombre"] = $row["nombre_$i"];
					$respuesta["jugadores"][$i]["color"] = $row["color_$i"];
					$respuesta["jugadores"][$i]["picurl"] = $row["picurl_$i"];
				}
			}
			//hasta ahora tenemos los datos del juego, ahi lo dejamos.
			$this->valido = false; //no se puede ver el juego, esto es solo para tener lista de jugadores
			$this->respuesta["status"] = "JUGADORES";
			$this->respuesta["mensaje"] = $respuesta;
		} else {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Juego inexistente";
			$this->valido = false;
		}
	}
	
	function is_valido() {
		return $this->valido;
	}
	
	function get_respuesta(){
		if($this->respuesta == null) {
			$this->respuesta["status"] = "VACIO";
		}
		return $this->respuesta;
	}
	
	//Enviar el estado actual del juego
	function sincronizar() {
		//info de:
		//juego
		$datos["juego"]["fase"] = $this->fase;
		$datos["juego"]["turno"] = $this->turno;
		$datos["juego"]["numjugadores"] = 0;
		$datos["juego"]["version"] = VERSION;
		
		//jugadores
		for($i = 1; $i <= 6; $i++) {
			if($this->id_jugadores[$i] != null) {
				$datos["juego"]["numjugadores"]++;
				$sql = "select j.id, j.nombre, j.color, j.picurl, sum(t.tropas) as tropas, count(t.id_pais) as territorios "
					. " from jugadores j left outer join juego_pais t on t.id_jugador=j.id "
					. " and t.id_juego=$this->id_juego"
					. " where j.id=" . $this->id_jugadores[$i]
					. " group by 1,2,3";
				$row = $this->conn->query($sql)->fetch_assoc();
				//numerizar
				$row["tropas"] = (int)$row["tropas"];
				$row["territorios"] = (int)$row["territorios"];
				$datos["jugadores"][$i] = $row;
			}
		}
		
		//mapa
		$sql = "select nombre, ancho, alto from mapas where id=$this->id_mapa";
		$row = $this->conn->query($sql)->fetch_assoc();
		//numerizar
		$row["ancho"] = (int)$row["ancho"];
		$row["alto"] = (int)$row["alto"];
		$datos["mapa"] = $row;
		
		//fronteras
		$sql = "SELECT id_pais_1, id_pais_2, p1.id_continente as id_continente_1, p2.id_continente as id_continente_2, "
			." round(p1.x+(p1.an/2),0) as x1, round(p1.y+(p1.al/2),0) as y1, "
			." round(p2.x+(p2.an/2),0) as x2, round(p2.y+(p2.al/2),0) as y2, "
			." camino FROM fronteras f, paises p1, paises p2, continentes c "
			." WHERE p1.id = f.id_pais_1 AND p2.id = f.id_pais_2 AND p1.id_continente = c.id AND c.id_mapa=$this->id_mapa";
		$fronteras = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
		//numerizar
		foreach($fronteras as &$frontera) {
			$frontera["x1"] = (int)$frontera["x1"];
			$frontera["y1"] = (int)$frontera["y1"];
			$frontera["x2"] = (int)$frontera["x2"];
			$frontera["y2"] = (int)$frontera["y2"];
		}
		$datos["mapa"]["fronteras"] = $fronteras;
		
		//continentes
		$sql="select c.id, c.nombre, c.bonus, c.color, pp.xmin, pp.xmax, pp.ymin, pp.ymax from continentes c, "
			." (select id_continente, min(x) as xmin, max(x+an) as xmax, min(y) as ymin, max(y+al) as ymax from paises group by 1) as pp "
			." where pp.id_continente = c.id and c.id_mapa=$this->id_mapa";
		$continentes = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
		foreach($continentes as $continente) {
			//numerizar
			$continente["bonus"] = (int)$continente["bonus"];
			$continente["xmin"] = (int)$continente["xmin"];
			$continente["xmax"] = (int)$continente["xmax"];
			$continente["ymin"] = (int)$continente["ymin"];
			$continente["ymax"] = (int)$continente["ymax"];
			$datos["mapa"]["continentes"][$continente["id"]] = array_slice($continente,1); //slice para quitar ID's redundantes
		}
		
		//paises
		$sql = "SELECT p.id_continente, p.id, p.nombre, p.x,p.y,p.an,p.al, p.puntos, t.id_jugador, t.tropas "
		." FROM continentes c, paises p "
		." LEFT OUTER JOIN juego_pais t ON t.id_pais=p.id "
		." WHERE p.id_continente=c.id AND c.id_mapa=$this->id_mapa";
		$paises = $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
		foreach($paises as $pais) {
			//numerizar
			$pais["x"] = (int)$pais["x"];
			$pais["y"] = (int)$pais["y"];
			$pais["an"] = (int)$pais["an"];
			$pais["al"] = (int)$pais["al"];
			$pais["tropas"] = (int)$pais["tropas"];
			$datos["mapa"]["continentes"][$pais["id_continente"]]["paises"][$pais["id"]] = array_slice($pais,2); //slice para quitar ID's redundantes
		}
		
		$this->respuesta["status"] = "OK";
		$this->respuesta["mensaje"] = $datos;
	}
	
	
	//elegir territorios iniciales
	function elegir($pais){
		//debemos de cumplir 3 cosas:
		//-fase de elegir
		//-turno de jugador
		//-pais valido
		if($this->fase != F_ELEGIR) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "El juego no esta en esa fase";
			return;
		}
		
		if($this->turno != $this->i_actual) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "No es tu turno";
			return;
		}
		
		//validar pais
		$sql = "SELECT p.nombre FROM paises p, continentes c WHERE p.id_continente = c.id AND c.id_mapa=$this->id_mapa AND p.id=$pais";
		$result = $this->conn->query($sql);
		if ($result == null or $result->num_rows == 0) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Pais invalido";
			return;
		}
		
		$sql = "SELECT tropas FROM juego_pais WHERE id_juego=$this->id_juego AND id_pais=$pais";
		$result = $this->conn->query($sql);
		if ($result == null or $result->num_rows == 0) {
			//insertar juego_pais
			$sql = "INSERT INTO juego_pais(id_juego, id_pais, id_jugador, tropas) "
			." values ($this->id_juego, $pais, " . $this->id_jugadores[$this->i_actual] . " , 1)";
			
			$this->conn->query($sql);
			
			//checar que turno sigue y actualizar
			if($this->turno == 6 or $this->id_jugadores[($this->turno+1)] == null) {
				$turno_siguiente = 1;
			} else {
				$turno_siguiente = ($this->turno+1);
			}
			$sql = "UPDATE juegos SET turno = $turno_siguiente WHERE id = $this->id_juego";
			$this->conn->query($sql);
			
			//Revisar si seguimos en esta fase
			$sql = "SELECT count(p.id) as faltantes "
			." FROM juegos j, continentes c, paises p "
			." left outer join juego_pais t on t.id_pais = p.id "
			." WHERE "
			." j.id = $this->id_juego "
			." and j.id_mapa = c.id_mapa "
			." and p.id_continente = c.id "
			." and t.tropas is null ";
			$result = $this->conn->query($sql);
			
			if($result->fetch_assoc()["faltantes"] == 0) {
				//Siguiente fase
				$sql = "UPDATE juegos SET fase = " . F_AGREGAR . " WHERE id = $this->id_juego";
				$this->conn->query($sql);
			}
			$this->respuesta["status"] = "OK";
			$this->respuesta["mensaje"] = "El pais se asigno con exito";
			return;
			
		} else {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Pais ocupado";
			return;
		}
	}
	
	//agregar tropas iniciales
	function agregar($pais) {
		//debemos de cumplir 3 cosas:
		//-fase de agregar
		//-turno de jugador
		//-pais del jugador
		if($this->fase != F_AGREGAR) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "El juego no esta en esa fase";
			return;
		}
		
		if($this->turno != $this->i_actual) {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "No es tu turno";
			return;
		}
		
		//validar pais
		$sql = "SELECT tropas FROM juego_pais WHERE id_juego=$this->id_juego AND id_pais=$pais AND id_jugador=" . $this->id_jugadores[$this->i_actual];
		$result = $this->conn->query($sql);
		if ($result->num_rows == 1) {
			//actualizar juego_pais
			$tropas = $result->fetch_assoc()["tropas"] + 1;
			
			$sql = "UPDATE juego_pais SET tropas=$tropas "
			." WHERE id_juego=$this->id_juego AND id_pais=$pais "
			." AND id_jugador=" . $this->id_jugadores[$this->i_actual];
			
			$this->conn->query($sql);
			
			//checar que turno sigue y actualizar
			if($this->turno == 6 or $this->id_jugadores[($this->turno+1)] == null) {
				$turno_siguiente = 1;
				
				//revisar ademas si hay cambio de fase
				$sql = "SELECT sum(tropas) as total FROM juego_pais WHERE id_juego=$this->id_juego "
				." and id_jugador=" . $this->id_jugadores[$this->i_actual];
				$result = $this->conn->query($sql);
				$actual = $result->fetch_assoc()["total"];
				
				//ver cuantos jugadores eran
				switch($this->turno) {
					case 6:
						$max = 20;
						break;
					case 5:
						$max = 25;
						break;
					case 4:
						$max = 30;
						break;
					case 3:
						$max = 35;
						break;
					default:
						$max = 40;
						break;
				}
				
				if($actual >= $max) {
					//Siguiente fase
					$sql = "UPDATE juegos SET fase = " . F_JUEGO . " WHERE id = $this->id_juego";
					$this->conn->query($sql);
				}
				
			} else {
				$turno_siguiente = ($this->turno+1);
			}
			
			$sql = "UPDATE juegos SET turno = $turno_siguiente WHERE id = $this->id_juego";
			$this->conn->query($sql);
			
			$this->respuesta["status"] = "OK";
			$this->respuesta["mensaje"] = "Tropas agregadas con exito";
			return;
			
		} else {
			$this->respuesta["status"] = "ERROR";
			$this->respuesta["mensaje"] = "Pais invalido o no es tuyo";
			return;
		}
	}

	//Agregar refuerzos de inicio de turno
	function reforzar($paises_cantidades){
		//TODO
	}
	
	//Agregar refuerzos de inicio de turno
	function atacar($pais_a,$pais_d,$t_a,$t_d){
		//TODO
	}
	
	//Agregar refuerzos de inicio de turno
	function mover($pais_de,$pais_a,$tropas){
		//TODO
	}
}
?>