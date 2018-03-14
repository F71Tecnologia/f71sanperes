<!DOCTYPE html>
<html lang="en" dir="ltr" xmlns:fb="http://ogp.me/ns/fb#">
<head>	<title>Camera and Video Control with HTML5 Example</title>
<script type="text/javascript" src="../js/jquery-1.11.1.min.js"></script>
<style>
.demo-frame header {
	display: none;
}

.demo-wrapper {
	min-height: 500px;
}

.bsap_1280449 {
	position: absolute;
	top: 0;
	right: 0;
}

.popup {
    height: 120px;
    width: 500px;
    position: absolute;
    top: 50%;
    left: 50%;
    margin: -60px 0 0 -250px; /* [-(height/2)px 0 0 -(width/2)px] */
    display: none;
}
#container {
    height: 120px;
    width: 500px;
    border: 1px solid #c30;
    position: relative;
    display:table; 
}
#container p {
    *position: absolute; 
    top: 50%; 
    display: table-cell; 
    vertical-align: middle;
}
#container span {
    display:block; 
    *position: relative; 
    top: -50%;
}

</style>	<style>
		video { border: 1px solid #ccc; display: block; margin: 0 0 20px 0; }
		#canvas { margin-top: 20px; border: 1px solid #ccc; display: block; }
	</style>
</head>
<body>
    <div class='popup' id="msg" style='color: #0088AA; background-color: #ccc; text-align: center;font-size: 40px; border: 2px solid #0088AA; font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;' >
	<div id='container'><p><span id='conteudo'></span></p></div>
    </div >
    <div class='popup' id="msgErro" style='color: #F40000; background-color:#ccc; text-align: center;font-size: 40px; border: 2px solid #F40000; font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;' >
	<div id='container'><p><span>Código não cadastrado!</span></p></div>
    </div >    
<center>
    <p style='color: #0088AA; font-size: 40px; font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;'>Controle de Acesso Restrito</p>
    <video id="video" width="533" height="400" autoplay></video>
    <canvas id="canvas" width="533" height="400" style="display: none"></canvas>
    <input type="text" id="barcode" name="barcode" style='font-size: 40px; font-family: "Trebuchet MS",Arial,Helvetica,sans-serif;' />
</center>
    <script>
	    // Evitando problema do Ctrl+J que abre a janela de download
	    $(document).ready(function() {	
		$("#barcode").keydown(function(e){
		    if(e.which==17 || e.which==74){
			e.preventDefault();
		    }
		});
		$("#barcode").focus();
	    });
   
	    // Put event listeners into place
	    window.addEventListener("DOMContentLoaded", function() {
		    // Grab elements, create settings, etc.
		    var canvas = document.getElementById("canvas"),
			    context = canvas.getContext("2d"),
			    video = document.getElementById("video"),
			    videoObj = { "video": true },
			    errBack = function(error) {
				    console.log("Video capture error: ", error.code); 
			    };

		    // Put video listeners into place
		    if(navigator.getUserMedia) { // Standard
			    navigator.getUserMedia(videoObj, function(stream) {
				    video.src = stream;
				    video.play();
			    }, errBack);
		    } else if(navigator.webkitGetUserMedia) { // WebKit-prefixed
			    navigator.webkitGetUserMedia(videoObj, function(stream){
				    video.src = window.webkitURL.createObjectURL(stream);
				    video.play();
			    }, errBack);
		    } else if(navigator.mozGetUserMedia) { // WebKit-prefixed
			    navigator.mozGetUserMedia(videoObj, function(stream){
				    video.src = window.URL.createObjectURL(stream);
				    video.play();
			    }, errBack);
		    }

		    $('#barcode').bind('keypress', function(e) {
			    if(e.keyCode===13 && (!e.ctrlKey))
			    {
				context.drawImage(video, 0, 0, 640, 480);
				var dataURL = canvas.toDataURL();
				var barcode = $("#barcode").val();
				$.ajax({
				    type: "POST",
				    url: "savepicture.php",
				    dataType : "json",
				    data: { 
				       imgBase64: dataURL,
				       barcode: barcode
				    }
				}).done(function(data) {
				    if(data[0].resp == '0'){
					$("#conteudo").html('Captura efetuada! '+ data[0].dataHora);
					$("#video").css("display", "none");
					$("#canvas").css("display", "block");
					$("#barcode").css("display", "none");
					$("#msg").css("display", "block");
				    }else
				    {
					$("#video").css("display", "none");
					$("#canvas").css("display", "block");
					$("#barcode").css("display", "none");
					$("#msgErro").css("display", "block");
				    }
				    //console.log(data);
				    setTimeout(function(){
					$("#video").css("display", "block");
					$("#canvas").css("display", "none");
					$("#barcode").css("display", "block");
					$("#msg").css("display", "none");
					$("#msgErro").css("display", "none");
					$("#barcode").val("");
					$("#barcode").focus();
				    }, 3000);

				});
			    }
		    });
	    }, false);

    </script>
</body>
</html>\