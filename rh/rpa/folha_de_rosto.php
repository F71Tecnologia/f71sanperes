<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

require_once ("../../conn.php");
require_once ('../../wfunction.php');

$id = $_REQUEST['id_rpa'];

$sql = "SELECT DATE_FORMAT(data_geracao, '%d/%m/%Y') dt_geracao,
        A.*, B.nome autonomo, B.cpf, B.tipo_pagamento, E.tipopg pagamento,B.*,C.unidade, D.nome projeto
        FROM rpa_autonomo A
        LEFT JOIN autonomo B ON A.id_autonomo = B.id_autonomo
        LEFT JOIN unidade C ON A.id_unidade_pag = C.id_unidade
        LEFT JOIN projeto D ON A.id_projeto_pag = D.id_projeto
        LEFT JOIN tipopg E ON B.tipo_pagamento = E.id_tipopg
        WHERE id_rpa = $id";
$query = mysql_query($sql);
$aut = mysql_fetch_assoc($query);
$mesesArray = ["01" => "Janeiro", "02" => "Fevereiro", "03" => "Mar&ccedil;o", "04" => "Abril", "05" => "Maio", "06" => "Junho",
    "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro"];

$dataGeracao = explode('/',$aut['dt_geracao']);
$dia = $dataGeracao[0];
$mes = $dataGeracao[1];
$ano = $dataGeracao[2];

if ($mes == 12) {
    $mesRef = 1;
    $anoRef = $ano + 1;
} else {
    $mesRef = $mes + 1;
    $anoRef = $ano;
}

$count = 0;

$sqlFeriados = "SELECT DATE_FORMAT(data, '%d-%m-%Y') data FROM rhferiados WHERE YEAR(data) = '$anoRef' AND MONTH(data) = '$mesRef' AND status = 1";
$queryFeriados = mysql_query($sqlFeriados);

while ($rowFeriados = mysql_fetch_assoc($queryFeriados)) {
    $arrFeriados[] = $rowFeriados['data'];
}

for ($i = 1; $i <= 10; $i++) {
    $diaVer = str_pad($i, 2, "0", STR_PAD_LEFT) . "-" . str_pad($mesRef, 2, "0", STR_PAD_LEFT) . "-$anoRef";
    $diaLoop = date('w', strtotime("$i-$mesRef-$anoRef"));
    if ($diaLoop != 0 && $diaLoop != 6 && !in_array($diaVer,$arrFeriados) == 1) {
        $count++;
        if ($count == 5) {
            $quintoDiaUtil = str_pad($i, 2, "0", STR_PAD_LEFT) . "/" . str_pad($mesRef, 2, "0", STR_PAD_LEFT) . "/$anoRef";
        }
    }
    
}

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: F71 :: Impress�o</title>

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
            <h4 class="text-left">FORMUL�RIO PARA SOLICITA��O</h4>
            <div style="width:100%;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <p style="font-size: 9px;margin:0">SETOR SOLICITANTE:</p>
                <p style="font-size: 15px;text-align:center;margin:0"><strong>RH - FOLHA DE PAGAMENTO</strong></p>
            </div>
            <div class="col-sm-5" style="float:left;width:41.67%;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <p style="font-size: 9px;margin:0">DATA DE EMISS�O:</p>
                <p style="font-size: 15px;text-align:center;margin:0"><strong><?=$aut['dt_geracao']?></strong></p>
            </div>
            <div class="col-sm-5 col-sm-offset-2" style="float:left;width:41.67%;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px;margin-left:16.66%">
                <p style="font-size: 9px;margin:0">DATA DE VENCIMENTO:</p>
                <p style="font-size: 15px;text-align:center;margin:0"><strong><?=$quintoDiaUtil?></strong></p>
            </div>
            <div style="width:100%;float:left;clear: both;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <div style="width:33.33%;float:left">
                    <p>BENEFICI�RIO:</p>
                    <p>CNPJ / CPF:</p>
                    <p>FORMA DE PAGAMENTO:</p>
                    <p>BANCO:</p>
                    <p>AG�NCIA:</p>
                    <p>CONTA:</p>
                    <p>VALOR:</p>
                </div>
                <div style="width:33.33%;float:left">
                    <p><strong><?=  ucwords(strtolower($aut['nome'])) ?></strong></p>
                    <p><strong><?=$aut['cpf']?></strong></p>
                    <p><strong><?=$aut['pagamento']?></strong></p>
                    <p><strong><?=$aut['nome_banco']?></strong></p>
                    <p><strong><?=$aut['agencia']?></strong></p>
                    <p><strong><?=$aut['conta']?></strong></p>
                    <p><strong><?='R$ ' . number_format($aut['valor_liquido'],2,',','.')?></strong></p>
                </div>
                <div style="width:16.66%;float:left">
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p>DIG.:</p>
                    <p>DIG.:</p>
                </div>
                <div style="width:16.66%;float:left">
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p style="color:white">.</p>
                    <p><strong><?=$aut['agencia_dv']?></strong></p>
                    <p><strong><?=$aut['conta_dv']?></strong></p>
                </div>
            </div>
            <div style="clear:both;width:100%;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <p style="font-size: 15px;text-align:center;margin:0">JUSTIFICATIVA</p>
            </div>
            <div style="float:left;clear:both;width:100%;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <div style="width:100%;">
                    <p><strong>PAGAMENTO DE PLANT�O RPA</strong></p>
                </div>
                <div style="width:33.33%;float:left">
                    <p>UNIDADE:</p>
                    <p>CONTRATO DE GEST�O:</p>
                </div>
                <div style="width:66.66%;float:left">
                    <p><strong><?=ucwords(strtolower($aut['unidade']))?></strong></p>
                    <p><strong><?=ucwords(strtolower($aut['projeto']))?></strong></p>
                </div>
            </div>
            <div style="clear:both;border: 1px solid #555;border-radius: 5px;padding:5px;margin-bottom: 10px">
                <p style="font-size: 9px;margin:0">ASSINATURA / CARIMBO DO CHEFE DO SETOR</p>
                <br>
                <br>
            </div>
            <div class="col-sm-12">
                <hr />
            </div>
            <div class="col-sm-12">
                <div style="width:33.33%;float:left">
                    <p style="text-align: center;font-size:8px"><strong>SEDE</strong></p>
                    <p style="text-align: center;font-size:8px">Av. Luis Carlos Prestes, 350 - Loja C, Salas 111 a 115, Barra Trade II<br>Barra da Tijuca - Rio de Janeiro - RJ - CEP: 22775-055<br>Telefone: (21) 3550-3300</p>
                </div>
                <div style="width:33.33%;float:left">
                    <p style="text-align: center;font-size:7px"><strong>FILIAIS</strong></p>
                    <p style="text-align: center;font-size:8px">Rua Diogo de Faria, 66 - Vila Mariana<br/>S�o Paulo - SP - CEP: 04037-000<br>Telefone: (11) 5904-6505</p>
                </div>
                <div style="width:33.33%;float:left">
                    <p style="text-align: center;font-size:7px;color:white"><strong>.</strong></p>
                    <p style="text-align: center;font-size:8px">Av. Paulista, 1.294, 11� andar - conjunto 111<br> Bela Vista - S�o Paulo / Cep 01310-100<br>Telefones: (11) 3053-4100 / (11) 3251-0487</p>
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