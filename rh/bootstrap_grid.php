<html lang="en">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
  <style type="text/css">
      /*.row{ height:120px;}*/
      .topo, .rodape, .boxEsq, .boxDir{ border:1px solid #ccc; }
      .corpo{ border:1px solid #ccc;}
      .logo{ width: 100px; box-sizing: border-box; padding: 10px;}
      .logo img{ border: 6px double #C37C1E; box-shadow: 2px 3px 25px rgba(195, 124, 30, 0.55); }
  </style>
  
  <body>
  <div class="container">
      <!-- Stack the columns on mobile by making one full-width and the other half-width -->
    <div class="row">
      <div class="col-xs-12 col-md-12 topo">
          <div class="logo">
              <img src="https://scontent-gru.xx.fbcdn.net/hphotos-xap1/v/t1.0-9/q84/p720x720/10439523_733022673472625_933111599613995240_n.jpg?oh=836b7908a8c35c33d4a10e4948b4026c&oe=559C78D2" class="img-responsive img-circle">
          </div>
      </div>
    </div>

    <!-- Columns start at 50% wide on mobile and bump up to 33.3% wide on desktop -->
    <div class="row">
      <div class="col-xs-12 col-md-3 corpo"><del>Menu</del></div>
      <div class="col-xs-12 col-md-9 corpo">
        <div class="table-responsive">
          <table class="table">
              <tr class="active">
                  <td>Nome</td>
                  <td>Telefone</td>
                  <td>E-mail</td>
              </tr>
              <tr>
                  <td>Sinésio</td>
                  <td>(21) 98436-9158</td>
                  <td>nesio88@hotmail.com</td>
              </tr>
              <tr>
                  <td>Marcus</td>
                  <td>(21) 98436-9158</td>
                  <td>nesio88@hotmail.com</td>
              </tr>
              <tr>
                  <td>Leonardo</td>
                  <td>(21) 98436-9158</td>
                  <td>nesio88@hotmail.com</td>
              </tr>
          </table>
        </div>
        
        <div class="form-group has-success has-feedback">
            <label for="nome" class="control-label">Nome:</label>
            <input type="text" class="form-control" id="nome" aria-describedby="inputSuccess2Status"/>
            <span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>
            <span id="inputSuccess2Status" class="sr-only">(success)</span>
        </div>
        <div class="form-group">
            <label for="telefone" class="">Telefone:</label>
            <div class="input-group">
                <div class="input-group-addon"></div>
                <input type="tel" class="form-control" id="telefone" placeholder="Digite seu telefone" />
                <div class="input-group-addon"></div>
            </div>
        </div>
        <div class="form-group">
            <label for="email" class="">E-mail:</label>
            <div class="input-group">
                <div class="input-group-addon"></div>
                <input type="email" class="form-control" id="telefone" placeholder="Digite seu e-mail" />
                <div class="input-group-addon"></div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-1">
                <button type="submit" class="btn btn-default">Cadastrar</button>
            </div>
        </div>  
          
      </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-3">
          <div class="thumbnail">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjkzIiB5PSIxMDAiIHN0eWxlPSJmaWxsOiNBQUFBQUE7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6MTFwdDtkb21pbmFudC1iYXNlbGluZTpjZW50cmFsIj4yNDJ4MjAwPC90ZXh0PjwvZz48L3N2Zz4=" alt="...">
            <div class="caption">
              <h3>Thumbnail label</h3>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3">
          <div class="thumbnail">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjkzIiB5PSIxMDAiIHN0eWxlPSJmaWxsOiNBQUFBQUE7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6MTFwdDtkb21pbmFudC1iYXNlbGluZTpjZW50cmFsIj4yNDJ4MjAwPC90ZXh0PjwvZz48L3N2Zz4=" alt="...">
            <div class="caption">
              <h3>Thumbnail label</h3>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3">
          <div class="thumbnail">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjkzIiB5PSIxMDAiIHN0eWxlPSJmaWxsOiNBQUFBQUE7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6MTFwdDtkb21pbmFudC1iYXNlbGluZTpjZW50cmFsIj4yNDJ4MjAwPC90ZXh0PjwvZz48L3N2Zz4=" alt="...">
            <div class="caption">
              <h3>Thumbnail label</h3>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-3">
          <div class="thumbnail">
            <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9InllcyI/PjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDI0MiAyMDAiIHByZXNlcnZlQXNwZWN0UmF0aW89Im5vbmUiPjxkZWZzLz48cmVjdCB3aWR0aD0iMjQyIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI0VFRUVFRSIvPjxnPjx0ZXh0IHg9IjkzIiB5PSIxMDAiIHN0eWxlPSJmaWxsOiNBQUFBQUE7Zm9udC13ZWlnaHQ6Ym9sZDtmb250LWZhbWlseTpBcmlhbCwgSGVsdmV0aWNhLCBPcGVuIFNhbnMsIHNhbnMtc2VyaWYsIG1vbm9zcGFjZTtmb250LXNpemU6MTFwdDtkb21pbmFudC1iYXNlbGluZTpjZW50cmFsIj4yNDJ4MjAwPC90ZXh0PjwvZz48L3N2Zz4=" alt="...">
            <div class="caption">
              <h3>Thumbnail label</h3>
              <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
              <p><a href="#" class="btn btn-primary" role="button">Button</a> <a href="#" class="btn btn-default" role="button">Button</a></p>
            </div>
          </div>
        </div>
    </div>

    <!-- Columns are always 50% wide, on mobile and desktop -->
    <div class="row">
      <div class="col-xs-12 col-md-4 boxEsq">Coluna Esquerda</div>
      <div class="col-xs-12 col-md-4 col-md-offset-4 boxDir">Coluna <kbd>Direita</kbd></div>
    </div>
    
    <div class="row">
      <div class="col-md-12 rodape">
          Rodape
      </div>
    </div>    
  </div>
      
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="../resources/js/bootstrap.min.js"></script>    
  </body>
  
</html>