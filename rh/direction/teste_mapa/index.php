<form method="post" action="index.html">
    <fieldset>
        <legend>Criar rotas</legend>

        <div>
           <label for="txtEnderecoPartida">Endere�o de partida:</label>
           <input type="text" id="txtEnderecoPartida" name="txtEnderecoPartida" />
        </div>

        <div>
           <label for="txtEnderecoChegada">Endere�o de chegada:</label>
           <input type="text" id="txtEnderecoChegada" name="txtEnderecoChegada" />
        </div>

        <div>
           <input type="submit" id="btnEnviar" name="btnEnviar" value="Enviar" />
        </div>
    </fieldset>
</form>

<div id="mapa" style="height: 500px; width: 700px"></div>
        
<div id="trajeto-texto" style="height: 300px; width: 200px"></div> // Elemento para exibi��o

<script src="js/jquery.min.js"></script>

<!-- Maps API Javascript -->
<script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>

<!-- Arquivo de inicializa��o do mapa -->
<script src="js/mapa.js"></script>