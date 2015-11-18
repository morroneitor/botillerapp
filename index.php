<?php
require("sesion.php");
?>
<html lang="en">
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<!--<meta charset="utf-8">-->
<title>BOTILLERAPP</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link href="css/fondoall.css" media="all" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="css/bootstrap.css">
<script rel="stylesheet" type="text/javascript" src="js/bootstrap.js" ></script>
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=true"></script>
<script type="text/javascript">
//Obtener Ubicacion Actual-----------------------------------
navigator.geolocation.getCurrentPosition(ubicacion,error,{enableHighAccuracy: true});
function ubicacion(posicion){
	var latitud = posicion.coords.latitude;
	var longitud = posicion.coords.longitude;
	var precision = posicion.coords.accuracy;
	load(latitud, longitud);
	var output = document.getElementById("map");
	var img = new Image();
	img.src = "http://maps.googleapis.com/maps/api/staticmap?center=" + latitud + "," + longitud + "&zoom=13&size=300x300&sensor=false";
	output.appendChild(img);
	//alert("Lat="+latitud+" - Long="+longitud+" - Precision="+precision);
}
function error(error){
	if(error.code == 0)
		alert("Error Desconocido");
	else if(error.code == 1)
		alert("No fue posible contactarte");
	else if(error.code == 2)
		alert("No hay una ubicacion disponible");
	else if(error.code == 3)
		alert("Tiempo agotado");
	else
		alert("Error Desconocido");
}
//<![CDATA[
var customIcons = {
	ABIERTO: {
		icon: 'img/botella_verde.png'
	},
	CERRADO: {
		icon: 'img/botella_roja.png'
	},
	SENSOR: {
		icon: 'img/sensor.ico'
	}
};
function load(latitud,longitud) {
	var map = new google.maps.Map(document.getElementById("map"), {
		//center: new google.maps.LatLng(-33.459894, -70.642656),
		center: new google.maps.LatLng(latitud,longitud),
		zoom: 15,
		disableDefaultUI: false,
		mapTypeId: 'roadmap'
});
var infoWindow = new google.maps.InfoWindow;
// Change this depending on the name of your PHP file
downloadUrl("maps_xml.php", function(data) {
	var xml = data.responseXML;
	var markers = xml.documentElement.getElementsByTagName("marker");
	for (var i = 0; i < markers.length; i++) {
		//Calcula Distancia----------------------------
		var lat1 = latitud;
		var lon1 = longitud;
		var lat2 = markers[i].getAttribute("lat");
		var lon2 = markers[i].getAttribute("lng");
		Distancia = Dist(lat1, lon1, lat2, lon2);   //Retorna numero en Km
		//Calcula Distancia----------------------------
		var cod = markers[i].getAttribute("cod");
		var name = markers[i].getAttribute("nombre");
		var address = markers[i].getAttribute("direccion");
		var type = markers[i].getAttribute("estado");
		var point = new google.maps.LatLng(
			parseFloat(markers[i].getAttribute("lat")),
			parseFloat(markers[i].getAttribute("lng")));
		var html = "<b>" + name + "</b>  <br/>" + Distancia + " KMS<br/>" + address + '<br><u><a href="descr.php?id='+cod+'">Ver Perfil</a></u>';
		var icon = customIcons[type] || {};
		var marker = new google.maps.Marker({
			map: map,
			position: point,
			icon: icon.icon
		});
		bindInfoWindow(marker, map, infoWindow, html);
	}
	//---------------------------------------------
	var type = "SENSOR";
	var point = new google.maps.LatLng(
		parseFloat(latitud),
		parseFloat(longitud));
	var icon = customIcons[type] || {};
	var marker = new google.maps.Marker({
		map: map,
		position: point,
		icon: icon.icon
	});
	var html = "";
	//bindInfoWindow(marker, map, infoWindow, html);
	//---------------------------------------------
});
}
function bindInfoWindow(marker, map, infoWindow, html) {
	google.maps.event.addListener(marker, 'click', function() {
		infoWindow.setContent(html);
		infoWindow.open(map, marker);
	});
}
function downloadUrl(url, callback) {
	var request = window.ActiveXObject ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest;
	request.onreadystatechange = function() {
		if (request.readyState == 4) {
			request.onreadystatechange = doNothing;
			callback(request, request.status);
		}
	};
	request.open('GET', url, true);
	request.send(null);
}
function doNothing() {}
function Dist(lat1, lon1, lat2, lon2) {
	rad = function(x) {return x*Math.PI/180;}
	var R     = 6378.137;                          //Radio de la tierra en km
	var dLat  = rad( lat2 - lat1 );
	var dLong = rad( lon2 - lon1 );
	var a = Math.sin(dLat/2) * Math.sin(dLat/2) + Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(dLong/2) * Math.sin(dLong/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	var d = R * c;
	return d.toFixed(1);                      //Retorna 1 decimal
}
//]]>
</script>
</head>
<body  onload="load()">
<div class="container">
	<nav class="navbar navbar-inverse" role="navigation">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">B</a>
		</div>
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="active"><a href="index.php">Inicio</a></li>
				<li><a href="botis.php">Botiller&iacute;as</a></li>
				<li><a href="productos.php">Productos</a></li>
				<li><a href="promos.php">Promociones</a></li>
				<li><a href="planes.php">Planes</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
			<form action="botis.php" class="navbar-form navbar-left" role="search">
			<div class="form-group">
				<input type="text" size="12" name="busk" class="form-control" placeholder="Botillería...">
			</div>
			<button type="submit" class="btn btn-default">Buscar</button>
			</form>
			<?php
			if(empty($_SESSION["us"])){
			?>
			<li><a href="inises.php">Iniciar Sesi&oacute;n</a></li>
			<li><a href="about.php">Acerca de</a></li>
			<?php
			}else{
			?>
			<li><a href="modif.php?submenu=1"><?php echo $_SESSION["us"]; ?></a></li>
			<li><a href="cerrar.php" data-toggle="tooltip" title="No te olvides de cerrar sesión">Cerrar Sesi&oacute;n</a></li>
			<?php
			}
			?>
			</ul>
		</div>
	</nav>
	<div class="row-fluid">
		<div id="map" style="width: 100%; height: 500px"></div>
		<p class="text-info">Toda la informaci&oacute;n proporcionada fue aprobada por cada botiller&iacute;a</p>
	</div>
	<?php include("pie.php"); ?>
</div>
</body>
</html>
