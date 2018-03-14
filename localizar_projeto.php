<?php
if(isset($_POST['nome'])){
    echo "<hr>";
    include "conn.php";
    $nome = $_POST['nome'];
    $qry = mysql_query("SELECT id_clt, r.nome AS nome, P.nome AS projeto FROM projeto P
                        INNER JOIN rh_clt r ON P.id_projeto = r.id_projeto
                        WHERE r.nome LIKE '%{$nome}%'");
    
    $total = mysql_num_rows($qry);
//    exit($total);
    if($total != 0){
        echo "<table class='table table-bordered table-striped'>"
        . "<tr>"
                . "<th>ID</th>"
                . "<th>Nome</th>"
                . "<th>Projeto</th>"
        . "</tr>";
        while($row = mysql_fetch_assoc($qry)){
            $row['projeto'] = utf8_encode($row['projeto']);
            $row['nome'] = utf8_encode($row['nome']);
            echo "<tr>"
            . "<td>{$row['id_clt']}</td>"
            . "<td>{$row['nome']}</td>"
            . "<td>{$row['projeto']}</td>"
            .   "</tr>";
        }
        echo "</table>";
    }else{
        echo '<div class="well">Nenhum resultado encontrado</div>';
    }
    exit($total);
}
?>
<!DOCTYPE html>
<html lang="pt">
    
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Localizar Projeto de funcionário</title>

        <link rel="shortcut icon" href="favicon.png" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href='classes/responsive-calendar/0.9/css/responsive-calendar.css' rel='stylesheet'>
        <link rel='stylesheet' href='resources/css/bootstrap-dialog.min.css'>
    </head>  
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Localizar projeto de funcion&aacute;rios
                        </div>
                        <div class="panel-body">
                            
                            <div class="input-group">
                                <input id="nome" name="nome" type="text" class="form-control" placeholder="Nome do funcion&aacute;rio">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                    </span>
                                </div><!-- /input-group -->
                                <div id="resultado"></div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src='js/jquery-ui-1.9.2.custom.min.js'></script>
        <script src='resources/js/bootstrap-dialog.min.js'></script>
        <script src="resources/js/tooltip.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src='classes/responsive-calendar/0.9/js/responsive-calendar.js'></script>
        <script>
            var callback = function() {
                var nome = $("#nome").val();
               if(nome == ""){
                   alert( "Digite um nome" );
               }else{
                   $.post( "localizar_projeto.php", { nome: nome })
                        .done(function( data ) {
//                            console.log(data);
                          $("#resultado").html(data);
                        });
               }
            };
           $( "button" ).click(callback);
           $("input").keypress(function() {
                if (event.which == 13) callback();
            });
        </script>
    </body>
</html>

