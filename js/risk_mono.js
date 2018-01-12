const VERSION = "0.1";

const F_JUGADORES = "0";
const F_ELEGIR = "1";
const F_AGREGAR = "2";
const F_JUEGO_REFUERZO = "3";
const F_JUEGO_BATALLA = "4";
const F_JUEGO_MOVIMIENTO = "5";

//Clase de juego
function Risk(juego, canvas) {
	this.risk = juego;
	this.canvas = canvas;
	this.ctx = canvas.getContext("2d");
	
	this.selContinente = "";
	this.selPais = "";
	
	
	//ajustar canvas
	this.canvas.width = this.risk.mapa.ancho;
	this.canvas.height = this.risk.mapa.alto;
	
	//Actualizar datos de la pagina con la info que bajamos
	this.actualizarHtml = function(){
		var i = 0;
		for (i; i < this.risk.juego.numjugadores; i++) {
			j = i+1;
			idj = Object.keys(this.risk.jugadores)[i];
			$("#nombre_"+j).html(this.risk.jugadores[idj].nombre);
			$("#img_"+j).css("border-color",this.risk.jugadores[idj].color)
				.attr("src",this.risk.jugadores[idj].picurl);
			$("#tropas_"+j).html(this.risk.jugadores[idj].tropas);
			$("#refuerzos_"+j).html("0"); //this.risk.jugadores[idj].refuerzos
			$("#territorios_"+j).html(this.risk.jugadores[idj].territorios);
			$("#cartas_"+j).html("0"); //this.risk.jugadores[idj].cartas
			
			if(this.risk.juego.turno==j){
				$("#div_"+j).addClass("active");
			} else {
				$("#div_"+j).removeClass("active");
			}
			$("#div_"+j).show();
		}
		//y ahora esconder el resto
		for (i; i < 6; i++) {
			j = i+1;
			$("#div_"+j).hide();
		}
	};
	
	
	//preparar los caminos de las fronteras cuando no hayan sido suministrados
	this.caminoFronteras = function(){
		for (var i = 0, len = this.risk.mapa.fronteras.length; i < len; i++) {
			if(this.risk.mapa.fronteras[i]["camino"]==null) {
				//no hay camino preparado, armar uno
				//revisar si nos salimos
				x1 = this.risk.mapa.fronteras[i]["x1"];
				y1 = this.risk.mapa.fronteras[i]["y1"];
				x2 = this.risk.mapa.fronteras[i]["x2"];
				y2 = this.risk.mapa.fronteras[i]["y2"];
			
				d11 = distancia(x1,y1,x2,y2);
				d12 = distancia(x1,y1,x2+this.risk.mapa.ancho,y2);
				d13 = distancia(x1,y1,x2,y2+this.risk.mapa.alto);
				d14 = distancia(x1,y1,x2+this.risk.mapa.ancho,y2+this.risk.mapa.alto);
				d21 = distancia(x1+this.risk.mapa.ancho,y1,x2,y2);
				d31 = distancia(x1,y1+this.risk.mapa.alto,x2,y2);
				d41 = distancia(x1+this.risk.mapa.ancho,y1+this.risk.mapa.alto,x2,y2);
				min = Math.min(d11,d12,d13,d14,d21,d31,d41);
				
				if(d11 == min) { //A-B
					this.risk.mapa.fronteras[i]["camino"] = "[["+x1+","+y1+","+x2+","+y2+"]]";
				} else if(d12 == min) { //A-B>
					this.risk.mapa.fronteras[i]["camino"] = 
						"[["+x1+","+y1+","+(x2+this.risk.mapa.ancho)+","+y2+"],"
						+"["+(x1-this.risk.mapa.ancho)+","+y1+","+x2+","+y2+"]]";
				} else if(d13 == min) { //A-BV
					this.risk.mapa.fronteras[i]["camino"] =
						"[["+x1+","+y1+","+x2+","+(y2+this.risk.mapa.alto)+"],"
						+"["+x1+","+(y1-this.risk.mapa.alto)+","+x2+","+y2+"]]";
				} else if(d14 == min) { //A-B\ 
					this.risk.mapa.fronteras[i]["camino"] =
						"[["+x1+","+y1+","+(x2+this.risk.mapa.ancho)+","+(y2+this.risk.mapa.alto)+"],"
						+"["+(x1-this.risk.mapa.ancho)+","+(y1-this.risk.mapa.alto)+","+x2+","+y2+"]]";
				} else if(d21 == min) { //B-A>
					this.risk.mapa.fronteras[i]["camino"] =
						"[["+(x1+this.risk.mapa.ancho)+","+y1+","+x2+","+y2+"],"
						+"["+x1+","+y1+","+(x2-this.risk.mapa.ancho)+","+y2+"]]";
				} else if(d31 == min) { //B-AV
					this.risk.mapa.fronteras[i]["camino"] = 
						"[["+x1+","+(y1+this.risk.mapa.alto)+","+x2+","+y2+"],"
						+"["+x1+","+y1+","+x2+","+(y2-this.risk.mapa.alto)+"]]";
				} else if(d41 == min) { //B-A\ 
					this.risk.mapa.fronteras[i]["camino"] =
						"[["+(x1+this.risk.mapa.ancho)+","+(y1+this.risk.mapa.alto)+","+x2+","+y2+"],"
						+"["+x1+","+y1+","+(x2-this.risk.mapa.ancho)+","+(y2-this.risk.mapa.alto)+"]]";
				} else {
					//What?? No debes llegar aqui!!!
					console.log("Error en fronteras!");
					this.risk.mapa.fronteras[i]["camino"] = "";
				}
			}
		}
	};
	
	//Dibujar el mapa en el canvas
	this.dibujarMapa = function(){
		//limpiar
		this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
		//Dibujar fronteras
		this.ctx.strokeStyle = "black";
		for (var i = 0, len = this.risk.mapa.fronteras.length; i < len; i++) {
			if(this.risk.mapa.continentes[this.risk.mapa.fronteras[i].id_continente_1]
				.paises[this.risk.mapa.fronteras[i].id_pais_1].seleccionado == true
				|| this.risk.mapa.continentes[this.risk.mapa.fronteras[i].id_continente_2]
				.paises[this.risk.mapa.fronteras[i].id_pais_2].seleccionado == true){
				this.ctx.lineWidth = 4;
			} else {
				this.ctx.lineWidth = 2;
			}
			camino = JSON.parse(this.risk.mapa.fronteras[i]["camino"]);
			
			for(var j = 0, lenj = camino.length; j < lenj; j++) {
				this.ctx.beginPath();
				this.ctx.moveTo(camino[j][0], camino[j][1]);
				this.ctx.lineTo(camino[j][2], camino[j][3]);
				this.ctx.stroke();
			}
		}
		
		//dibujar paises
		for (var c = 0, lenc = Object.keys(this.risk.mapa.continentes).length; c < lenc; c++) {
			idc = Object.keys(this.risk.mapa.continentes)[c];
			continente = this.risk.mapa.continentes[idc];
			for (var i = 0, lenp = Object.keys(continente.paises).length; i < lenp; i++) {
				idp = Object.keys(continente.paises)[i];
				
				
				//ctx.font="25px Calibri ";
				//ctx.fillStyle = "red";
				//ctx.textAlign = "center";
				//ctx.fillText("Hello World", canvas.width/2, canvas.height/2);
				
				relleno = "white";
				if(continente.paises[idp].id_jugador != null) {
					for(j = 1; j <= this.risk.juego.numjugadores; j++) {
						if(this.risk.jugadores[j].id == continente.paises[idp].id_jugador) { 
							relleno = this.risk.jugadores[j].color;
						}
					}
				}
				
				borde = 3;
				if(continente.paises[idp].seleccionado == true) {
					borde = 6;
				}
				this.ctx.lineWidth = borde;
				this.ctx.fillStyle = relleno;
				this.ctx.fillRect(continente.paises[idp]["x"],continente.paises[idp]["y"],continente.paises[idp]["an"],continente.paises[idp]["al"]);
				this.ctx.strokeStyle = continente["color"];
				this.ctx.strokeRect(continente.paises[idp]["x"]+(borde/2),continente.paises[idp]["y"]+(borde/2),continente.paises[idp]["an"]-(borde),continente.paises[idp]["al"]-(borde));
				
				
				
				this.ctx.font = "12px Calibri";
				if(continente.paises[idp].seleccionado == true) {
					this.ctx.font = "bold " + this.ctx.font;
				}
				this.ctx.fillStyle = "black";
				this.ctx.textAlign = "center";
				this.ctx.textBaseline = "top";
				this.ctx.fillText(continente.paises[idp].nombre,
					continente.paises[idp]["x"]+(continente.paises[idp]["an"]/2),
					continente.paises[idp]["y"]+(continente.paises[idp]["al"]/2));
				if(continente.paises[idp].tropas>0){
					this.ctx.font = "18px Calibri";
					if(continente.paises[idp].seleccionado == true) {
						this.ctx.font = "bold " + this.ctx.font;
					}
					this.ctx.textBaseline = "bottom";
					this.ctx.fillText(continente.paises[idp].tropas,
						continente.paises[idp]["x"]+(continente.paises[idp]["an"]/2),
						continente.paises[idp]["y"]+(continente.paises[idp]["al"]/2));
				}
				
			}
		}
	};
	
	//Ligar el click en canvas
	this.canvas.addEventListener('click', function(event) {
		var mousePos = getMousePos(event);
		//Revisar si seleccionamos algun pais
		cs = this.risk.mapa.continentes;
		clickEnAlgo = false;
		for (var c = 0, lenc = Object.keys(cs).length; c < lenc; c++) {
			idc = Object.keys(cs)[c];
			continente = cs[idc];
			if (mousePos.x >= continente["xmin"] && mousePos.x <= continente["xmax"] 
				&& mousePos.y >= continente["ymin"] && mousePos.y <= continente["ymax"] ) {
				for (var i = 0, lenp = Object.keys(continente.paises).length; i < lenp; i++) {
					idp = Object.keys(continente.paises)[i];
					pais = continente.paises[idp];
					if (mousePos.x >= pais["x"] && mousePos.x <= pais["x"]+pais["an"]
						&& mousePos.y >= pais["y"] && mousePos.y <= pais["y"]+pais["al"] ) {
							//listo, manejar el click al pais
							clickEnAlgo = true;
							this.clickPais(idp, idc);
							break;
					}
				}
			}
		}
		
		if(!clickEnAlgo) {
			if(this.selPais != "") {
				this.risk.mapa.continentes[this.selContinente].paises[this.selPais].seleccionado = false;
				this.selPais = "";
				this.selContinente = "";
				this.dibujarMapa();
			}
		}
	}.bind(this));
	
	//manejar el click en un pais
	this.clickPais = function(llavePais, llaveContinente){
		//revisar fase
		console.log("click - c"+llaveContinente+" p"+llavePais);
		switch(this.risk.juego.fase) {
			case F_ELEGIR:
				if(this.selPais != "") {
					//desseleccionar anterior
					this.risk.mapa.continentes[this.selContinente].paises[this.selPais].seleccionado = false;
					if(this.selPais == llavePais 
						&& this.risk.mapa.continentes[llaveContinente].paises[llavePais].id_jugador == null) {
						this.elegirPais(this.risk.jugadores[this.risk.juego.turno].id, llavePais, llaveContinente);
					}
				}
				//asignar
				this.selPais = llavePais;
				this.selContinente = llaveContinente;
				//ver info
				this.risk.mapa.continentes[llaveContinente].paises[llavePais].seleccionado = true;
				this.dibujarMapa();
				break;
			case F_AGREGAR:
				if(this.selPais != "") {
					//desseleccionar anterior
					this.risk.mapa.continentes[this.selContinente].paises[this.selPais].seleccionado = false;
					if(this.selPais == llavePais 
						&& this.risk.mapa.continentes[llaveContinente].paises[llavePais].id_jugador == this.risk.jugadores[this.risk.juego.turno].id) {
						this.fortalecerPais(this.risk.jugadores[this.risk.juego.turno].id, llavePais, llaveContinente);
					}
				}
				//asignar
				this.selPais = llavePais;
				this.selContinente = llaveContinente;
				//ver info
				this.risk.mapa.continentes[llaveContinente].paises[llavePais].seleccionado = true;
				this.dibujarMapa();
				break;
			case F_JUEGO_REFUERZO:
				break;
			case F_JUEGO_BATALLA:
				break;
			case F_JUEGO_MOVIMIENTO:
				break;
			default:
				console.log("Fase desconocida! "+this.risk.juego.fase);
		};
	};
	
	//elegir un pais en fase 1
	this.elegirPais = function(jugador, pais, continente){
		//agregar pais si es valido
		console.log("rwg_mono.php?jugador="+jugador+"&accion=ELEGIR&pais="+pais);
		$.get( "rwg_mono.php?jugador="+jugador+"&accion=ELEGIR&pais="+pais, function( data ) {
			if(data.status == "OK") {
				//Listo! actualizar el objeto localmente
				this.risk.mapa.continentes[continente].paises[pais].id_jugador = jugador;
				this.risk.mapa.continentes[continente].paises[pais].tropas = 1;
				this.risk.jugadores[this.risk.juego.turno].territorios++;
				this.risk.jugadores[this.risk.juego.turno].tropas++;
				this.risk.juego.turno = (this.risk.juego.turno % this.risk.juego.numjugadores) + 1;
				
				//y repintar
				this.actualizarHtml();
				this.dibujarMapa();
				
				//finalmente, revisar si hay cambio de fase
				cs = this.risk.mapa.continentes;
				cambiarFase = true;
				for (var c = 0, lenc = Object.keys(cs).length; c < lenc; c++) {
					idc = Object.keys(cs)[c];
					continente = cs[idc];
					for (var i = 0, lenp = Object.keys(continente.paises).length; i < lenp; i++) {
						idp = Object.keys(continente.paises)[i];
						pais = continente.paises[idp];
						if (pais.id_jugador != ""){
							//Pais vacio, seguimos en esta fase
							cambiarFase = false;
							break;
						}
					}
				}
				if(cambiarFase) {
					this.iniciarAgregar();
				}
				
			} else {
				//TODO error handling
				alert(data.status + " - " + data.mensaje);
			}
		}.bind(this));
	};
	
	//fortalecer un pais en fase 2
	this.fortalecerPais = function(jugador, pais, continente){
		//fortalecer pais si es valido
		console.log("rwg_mono.php?jugador="+jugador+"&accion=AGREGAR&pais="+pais);
		$.get( "rwg_mono.php?jugador="+jugador+"&accion=AGREGAR&pais="+pais, function( data ) {
			if(data.status == "OK") {
				//Listo! actualizar el objeto localmente
				this.risk.mapa.continentes[continente].paises[pais].tropas++;
				this.risk.jugadores[this.risk.juego.turno].tropas++;
				this.risk.juego.turno = (this.risk.juego.turno % this.risk.juego.numjugadores) + 1;
				
				//y repintar
				this.actualizarHtml();
				this.dibujarMapa();
				
				//finalmente, revisar si hay cambio de fase
				cambiarFase = false;
				//determinar maximo de tropas
				max = -1;
				
				switch(this.risk.juego.numjugadores) {
					case 6:
						max=20*6;
						break;
					case 5:
						max=25*5;
						break;
					case 4:
						max=30*4;
						break;
					case 3:
						max=35*3;
						break;
					default:
						max = 40*2;
				}
				
				//contar tropas actuales
				conteo = 0;
				for(i = 1; i <= this.risk.juego.numjugadores; i++) {
					conteo += this.risk.jugadores[i].tropas;
				}
				cambiarFase = (conteo >= max);
				
				if(cambiarFase) {
					this.iniciarJuego();
				}
				
			} else {
				//TODO error handling
				alert(data.status + " - " + data.mensaje);
			}
		}.bind(this));
	};
	
	this.iniciarAgregar = function(){
		//TODO poner mas intenso
		this.risk.juego.fase = F_AGREGAR;
	}
	
	this.iniciarJuego = function(){
		//TODO poner mas intenso
		this.risk.juego.fase = F_JUEGO_REFUERZO;
		alert("NYI");
	}
	
	//reforzar un pais en fase 3
	this.reforzarPais = function(llavePais){
	
	};
}


//Funciones auxiliares
function distancia(x1,y1,x2,y2) {
	return(Math.sqrt(((x1-x2)**2) + ((y1-y2)**2)));
}

function getMousePos(event) {
	var rect = event.target.getBoundingClientRect();
	return {
		x: event.clientX - rect.left,
		y: event.clientY - rect.top
	};
}

function iniciarJuego(jugador_id){
	$.get( "rwg_mono.php?jugador="+jugador_id+"&accion=SINCRONIZAR", function( data ) {
		if(data.status == "OK") {
			if(data.mensaje.juego.version==VERSION){
				//risk = new Risk(data.mensaje, $("#cvMap"));
				risk = new Risk(data.mensaje, document.getElementById("cvMap"));
				risk.caminoFronteras();
				risk.dibujarMapa();
				risk.actualizarHtml();
			} else {
				//TODO error handling
				alert("Error en version: El servidor usa " + data.mensaje.juego.version+", este script usa " + VERSION);
			}
		} else {
			//TODO error handling
			alert(data.status + " - " + data.mensaje);
		}
	});
}

//Correr cuando cargue el documento
$(document).ready(function () {
	$.get( "rwg_mono.php?juego="+juego_id, function( data ) {
		//TODO tenemos los jugadores, y luego?
		//Idealmente, aqui abrimos la validacion, y una vez validado iniciamos
		if(data.status == "JUGADORES") {
			for(i = 1; i <= data.mensaje.jugadores.length; i++) {
				//TODO hacer algo con ellos
				console.log(data.mensaje.jugadores[i].id+":"+data.mensaje.jugadores[i].nombre);
			}
			//TODO esto va en una validacion, no aqui
			iniciarJuego(data.mensaje.jugadores[1].id);
		} else {
			//TODO error handling
			alert(data.status + " - " + data.mensaje);
		}
	});
});