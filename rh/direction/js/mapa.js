var map;
var directionsDisplay;
var directionsService = new google.maps.DirectionsService();
function initialize() {	
	directionsDisplay = new google.maps.DirectionsRenderer();
	var latlng = new google.maps.LatLng(-18.8800397, -47.05878999999999);	
        //var latlng = new google.maps.LatLng(-43.373424, 22.815999);
    var options = {
        zoom: 5,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("mapa"), options);
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById("trajeto"))        
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
        pontoPadrao = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
        map.setCenter(pontoPadrao);
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            "location": new google.maps.LatLng(position.coords.latitude, position.coords.longitude)
        });
//        function(results, status) {
//            if (status == google.maps.GeocoderStatus.OK) {
//                //$("#txtEnderecoPartida").val(results[0].formatted_address);
//            }
//        }                
        });
    }
}

initialize();

function getRota(partida, destino, rota){
    var enderecoPartida = partida;
    var enderecoChegada = destino;
    var _rota = google.maps.TravelMode.TRANSIT;
    
    if (rota === "D") {
        _rota = google.maps.TravelMode.DRIVING;
    }
    
    var request = {
        origin: enderecoPartida,
        destination: enderecoChegada,
        travelMode: _rota  //TRANSIT|DRIVING
    };        
    
    directionsService.route(request, function(result, status){
        if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);                
        }else{
            $(".rota").css({display: 'block', margin: '30px 0 0 0', 'text-align': 'center'});
            $("#dados_mapa").hide();
        }
    });        
}
/*
function getRota2(partida, destino){
    var enderecoPartida = partida;
    var enderecoChegada = destino;
    
    var request = {
        origin: enderecoPartida,
        destination: enderecoChegada,
        travelMode: google.maps.TravelMode.DRIVING //TRANSIT|DRIVING
    };        
    
    directionsService.route(request, function(result, status){
        if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);                
        }else{
            $(".rota2").css({display: 'block', margin: '30px 0 0 0', 'text-align': 'center'});
            $("#dados_mapa").hide();
        }
    });        
}*/

//google.maps.event.addDomListener(window, 'load', initialize);