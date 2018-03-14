<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

require_once ("../conn.php");
require_once ('../wfunction.php');

$id = $_REQUEST['id'];

$sql = "SELECT 
	a.nome,
	a.cpf,
        DATE_FORMAT(a.data_entrada, '%d/%m/%Y') data_entrada,
	b.nome AS nome_funcao,
	b.hora_semana,
 	c.unidade AS nome_unidade	
            FROM rh_clt AS a
            INNER JOIN curso AS b ON a.id_curso = b.id_curso
            INNER JOIN unidade AS c ON a.id_unidade = c.id_unidade
            WHERE id_clt = '$id';";
$query = mysql_query($sql);
$clt = mysql_fetch_assoc($query);
$mesesArray = ["01" => "Janeiro", "02" => "Fevereiro", "03" => "Mar&ccedil;o", "04" => "Abril", "05" => "Maio", "06" => "Junho",
        "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro"];
$dia = date('d');
$mes = date('m');
$mes = $mesesArray[$mes];
$ano = date('Y');
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: F71 :: Impressão</title>

        <!-- Bootstrap -->
        <link href="/extranet/css/bootstrap.min.css" rel="stylesheet">
        <link href="/extranet/css/font-awesome.min.css" rel="stylesheet">
        <link href="/extranet/css/print.css" rel="stylesheet">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>


        <div class="print-pager print-v">
            <br>
            <br>
            <div class="text-center"><img src="/extranet/img/logo.gif" alt="logo"></div>
            <br>
            <br>
            <h4 class="text-center">DECLARAÇÃO DE DOMICILIO PROFISSIONAL</h4>

            <br>
            <br>
            <br>

            <p class="text-justify">
                Declaramos para os devidos fins que o(a) Sr(a) <strong><?= $clt['nome'] ?></strong>
                portador do CPF: <strong><?= $clt['cpf'] ?></strong>, é funcionário(a) desta Instituição. 
                IABAS - Instituto de Atenção Básica e Avançada à Saúde, CNPJ 
                09.652.823.0003/38, pelo regime CLT desde <strong><?= $clt['data_entrada'] ?></strong>, 
                exerce atualmente a função de <strong><?= $clt['nome_funcao'] ?></strong>, sob jornada de 
                trabalho de <strong><?= $clt['hora_semana'] ?></strong> horas semanais, 
                tendo como local de trabalho a unidade 
                <strong><?= $clt['nome_unidade'] ?></strong>. E não há nada em nossos arquivos que desabone.
            </p>

            <br>
            <br>
            <br>

            <p class="text-right">São Paulo <?= $dia ?> de <?= $mes ?> de <?= $ano ?>.</p>

            <br>
            <br>
            <br>

<!--    <p class="text-center">_____________________________________________________________</p>
<p class="text-center">Coordenação de Recursos</p>-->

            <div class="row">
                <!--                <div class="col-xs-6 text-center">
                                    <br>
                                    ________________________________<br>
                                    <span class="text-sm">Coordenação de Recursos</span>
                                </div>-->
                <div class="col-xs-12 text-center">
                    <img src="/extranet/img/assinatura.jpg" style="z-index: -1; width: 300px; margin: -200px 0 -200px;">  
                    <br>
                    ________________________________<br>
                    <span class="text-sm">INSTITUTO DE ATENÇÃO BÁSICA E AVANÇADA À SAÚDE-IABAS</span>
                </div>
            </div>

        </div>
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="/extranet//js/jquery-1.11.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="/extranet//js/bootstrap.min.js"></script>
        <script>
            $(document).ready(function () {
                $("#imprimir").click(function () {
                    window.print();
                });
            });
        </script>
    </body>
</html>