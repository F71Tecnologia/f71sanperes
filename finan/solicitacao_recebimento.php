<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include "../conn.php";
include('../funcoes.php');
include('../wfunction.php');
require("../classes/EntradaClass.php");
include("../classes/BorderoClass.php");

$objBordero = new BorderoClass();
$usuario = carregaUsuario();

$objEntrada = new Entrada();

if(!$_REQUEST['id']) { 
    $entradas = (is_array($_REQUEST['entradas'])) ? $_REQUEST['entradas'] : [$_REQUEST['entradas']];
    $entradas = array_filter($entradas);
    if(count($entradas) == 0){
        print_array("Nenhuma saída selecionada!");exit;
    }
    $auxEntrada = " AND A.id_entrada IN (" . implode(', ', $entradas) . ")";
} else {
    $id_bordero = $_REQUEST['id'];
    $condicao[] = ($id_bordero) ? "A.id = {$id_bordero}" : null;
    $arrayBordero = $objBordero->getBoredero($condicao)[$id_bordero];
//    print_array($arrayBordero);
    $auxEntrada = " AND A.id_entrada IN (" . implode(', ', array_keys($arrayBordero['entradas'])) . ")";
}


if($_COOKIE['debug'] == 666){
    print_array($_REQUEST);
    print_array($auxEntradas);
}

$sqlEntrada = "
SELECT A.id_entrada, CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor , A.adicional, A.tipo, A.nome,
A.numero_doc AS n_documento, 
A.data_vencimento, A.id_projeto, B.nome AS nomeProjeto, D.nome_grupo
FROM entrada A 
LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
LEFT JOIN entradaesaida AS C ON (A.tipo = C.id_entradasaida)
LEFT JOIN entradaesaida_grupo AS D ON (C.grupo = D.id_grupo)
WHERE 1 $auxEntrada
ORDER BY A.id_projeto, id_entrada";
$qryEntrada = mysql_query($sqlEntrada) or die(mysql_error());
if(mysql_num_rows($qryEntrada) > 0 && !$_REQUEST['id']){
    $insert = mysql_query("INSERT INTO bordero (id_funcionario) VALUES ('{$usuario['id_funcionario']}')");
    $id_bordero = mysql_insert_id();
}
$count = 0;
$sqlTotalEntrada = "SELECT SUM(valor) AS valorTotal FROM ($sqlEntrada) AS tot";
$qryTotalEntrada = mysql_query($sqlTotalEntrada) or die(mysql_error());
$rowTotalEntrada = mysql_fetch_assoc($qryTotalEntrada);
//print_array($rowTotalEntrada);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <title>:: Intranet :: Relatório de Termo de Vale Transporte em Lote </title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="shortcut icon" href="../favicon.ico">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/main.css" rel="stylesheet" type="text/css">
        
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        
    <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">-->
    <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="../resources/css/style-print.css" rel="stylesheet">
    <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
    <style type="text/css">
        input { width: 100%; } 
        @media print {
            input { border: none; } 
        }
        table { font-size: 12px; }
        table{
            width: 100%;
            border: collapse;    
            margin-top: 10px;
            border-collapse: collapse;
        }
        table tr{
            border: 1px solid #000;
            height: 25px;
        }
        table td {
           border: 1px solid #000;
           padding: 1px !important;
        }
        table th {
           padding: 1px !important;
        }
        table.sem_borda{ border: 0; margin-top: 40px; }
        table.sem_borda tr{ border: 0; }
        table.sem_borda td{ border: 0; }
        
        .pagina {
            padding: 1cm !important;
        }
    </style>
    <!--<link href="../adm/css/estrutura.css" rel="stylesheet" type="text/css" />-->
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
                            <?php if($arrayBordero['pago'] != 1) { ?><button type="button" id="pagar_bordero" class="btn btn-default navbar-btn"><i class="fa fa-money"></i> Pagar Borderô</button><?php } ?>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <?php
                include('../empresa.php');
                $img = new empresa();
                $img->imagem();
                $row_master = mysql_fetch_assoc($img->re);
//                print_array($row_master);
                ?>
            </div>
            <h2 class="text-center">BORDERO DE REMESSA DE DE DOCUMENTOS (<?php echo str_pad($id_bordero, 6, "0", STR_PAD_LEFT) ?>)</h2>
            <h3 class="text-center">Autorização de débito para pagamentos</h3>
            <form action="" method="post" id="formulario">
            <table class="table table-bordered table-condensed valign-middle">
                <tr>
                    <th>Agencia: 1550 | Conta: 30811</th>
                    <th>Data Emissão: <?php echo date('d/m/Y') ?></th>
                    <th>Data à Compensar: <input type="type" name="data_compensar" class="data" style="width: 40%" value="<?php echo ($arrayBordero['data_compensar']) ? implode('/', array_reverse(explode('-', $arrayBordero['data_compensar']))) : date('d/m/Y') ?>" /></th>
                </tr>
                <tr>
                    <th colspan="2">Titular: <?php echo $row_master['razao'] ?></th>
                    <th><input name="campo_livre" type="text" class="input" value="<?php echo ($arrayBordero['campo_livre']) ? $arrayBordero['campo_livre'] : null ?>" /></th>
                </tr>
                <tr>
                    <th colspan="3">Valor: R$ <?php echo number_format($rowTotalEntrada['valorTotal'], 2, ',', '.') ?></th>
                </tr>
            </table>
            <table class="table table-bordered valign-middle">
                <tr>
                    <th>#</th>
                    <th colspan="2"><input type="text" name="descricao" class="input" value="<?php echo ($arrayBordero['descricao']) ? $arrayBordero['descricao'] : 'DESCRIÇÃO' ?>" /></th>
                    <th>Nº</th>
                    <th>VALOR</th>
                    <th>VENCIMENTO</th>
                </tr>
                <?php while($row = mysql_fetch_assoc($qryEntrada)) { 
                    if(!$_REQUEST['id']) { 
                        $insertBS = mysql_query("INSERT INTO bordero_entradas (id_bordero, id_entrada) VALUES ('{$id_bordero}', '{$row['id_entrada']}')");
                    }
                    if($auxProjeto != $row['id_projeto']) { 
                        if($count > 0) {
                            echo 
                            "<tr>
                                <td colspan='6' class='text-right'>Total Unidade: ".number_format($totalProjeto, 2, ',', '.')."</td>
                            </tr>";
                        }
                        echo 
                        "<tr>
                            <td colspan='6'>{$row['id_projeto']} - {$row['nomeProjeto']}</td>
                        </tr>";
                        $auxProjeto = $row['id_projeto'];
                        $totalProjeto = 0;
                    }
                    $count++;
                    $totalProjeto += $row['valor']; 
                ?>
                <tr>
                    <td><?php echo $row['id_entrada'] ?></td>
                    <td><?php echo $row['nome_grupo'] ?></td>
                    <td><?php echo $row['nome'] ?> </td>
                    <td><?php echo $row['n_documento'] ?></td>
                    <td><?php $total += $row['valor']; echo number_format($row['valor'], 2, ',', '.'); ?></td>
                    <td class="text-center"><?php echo implode('/',array_reverse(explode('-',$row['data_vencimento']))) ?></td>
                </tr>
                <?php } ?>
                <tr>
                    <td colspan='6' class='text-right'>Total Unidade: <?php echo number_format($totalProjeto, 2, ',', '.')?></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right">TOTAL:</td>
                    <td colspan="2" class="text-right">R$ <?php echo number_format($rowTotalEntrada['valorTotal'], 2, ',', '.'); ?> </td>
                </tr>
                <?php
                $meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

                $hoje = getdate();
                $dia = $hoje["mday"];
                $mes = $hoje["mon"];
                $nomemes = $meses[$mes];
                $ano = $hoje["year"]; ?>
            </table>
            <table class="sem_borda">
                <tr>
                    <td class="text-center no-print"><input type="checkbox" id="pagar_cheque" <?php echo ($arrayBordero['numero_cheque']) ? 'CHECKED' : null ?> > Pagar em cheque?</td>
                </tr>
                <tr>
                    <td class="text-center <?php echo (!$arrayBordero['numero_cheque']) ? 'hide  no-print' : null ?> text-cheque">
                        <input type="text" style="width: 50%" class="input" value="Valor pago no cheque de número:" /><input type="text" style="width: 50%" class="input" id="numero_cheque" name="numero_cheque" value="<?php echo ($arrayBordero['numero_cheque']) ? $arrayBordero['numero_cheque'] : null ?>" />
                        <?php foreach ($entradas as $key => $value) { ?>
                        <input type="hidden" class="entradas_check" name="id[<?php echo $value ?>]" value="<?php echo $value ?>">
                        <?php } ?>
                        <input type="hidden" name="id_bordero" value="<?php echo $id_bordero ?>">
                        <input type="hidden" name="action" value="pagar_bordero">
                    </td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <tr class="text-center">
                    <td><strong><?php echo $row_master['municipio'] ?>, <?php echo $dia.' de '.$nomemes.' de '.$ano; ?></strong></td>
                </tr>
                <tr>
                    <td class="text-center">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-center">____________________________________________________________</td>
                </tr>
                <tr>
                    <td class="text-center"><strong><?php echo $row_master['razao']?></strong></td>
                </tr>
                <tr>
                    <td align="center">
                        <strong>
                        <?php echo $row_master['endereco'] .',<br> CEP:'.formato_cep($row_master['cep']).', CNPJ: '.$row_master['cnpj']. ', Telefone: '.$row_master['telefone']; ?>
                        </strong>
                    </td>
                </tr>
            </table>
            </form>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../resources/js/financeiro/index.js"></script>
        <script src="../resources/js/date.js"></script>
        <script src="../resources/js/dataTables.1.10.16.min.js"></script>
        <script src="../resources/js/dataTables.bootstrap.1.10.16.min.js"></script>
        <script>
        $(function(){
            $('body').on('change', '#pagar_cheque', function(){
                if($(this).prop('checked')) {
                    $('.text-cheque').removeClass('hide no-print');
                } else {
                    $('.text-cheque').addClass('hide no-print');
                }
            });
            
//            $('body').on('keypress', '#isNumberKey', function(evt) { 
//                event.preventDefault();
//                var charCode = (evt.which) ? evt.which : event.keyCode;
//                console.log(charCode);
//                if (charCode > 31 && (charCode < 48 || charCode > 57))
//                    return false;
//                return true;
//            });

            $('body').on('click', '#pagar_bordero', function() { 
                $.post("actions/action.saida.php", $('#formulario').serialize(), function(resultado){
                    bootAlert('Entrada paga com sucesso!', 'Entrada(s) Paga(s)!', function(){ window.location.href = "/intranet/finan/solicitacao_pagamento.php?id=<?php echo $id_bordero ?>" }, 'success');
                });
            });
        });
        </script>
    </body>
</html>