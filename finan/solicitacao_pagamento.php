<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include "../conn.php";
include('../funcoes.php');
include('../wfunction.php');
require("../classes/SaidaClass.php");
include("../classes/BorderoClass.php");
include("../classes_permissoes/acoes.class.php");

$objAcoes = new Acoes();
$objBordero = new BorderoClass();
$usuario = carregaUsuario();

$objSaida = new Saida();

if(!$_REQUEST['id']) { 
    $saidas = (is_array($_REQUEST['saidas'])) ? $_REQUEST['saidas'] : [$_REQUEST['saidas']];
    $saidas = array_filter($saidas);
    if(count($saidas) == 0){
        print_array("Nenhuma saída selecionada!");exit;
    }
    $auxSaidas = " AND A.id_saida IN (" . implode(', ', $saidas) . ")";
    $sqlEmBordero = "SELECT B.id_saida, B.id_bordero FROM bordero A INNER JOIN bordero_saidas B ON (A.id = B.id_bordero) WHERE A.status = 1 AND B.status = 1 AND A.pago = 1 AND B.id_saida IN (" . implode(', ', $saidas) . ")";
    $qryEmBordero = mysql_query($sqlEmBordero);
    while($rowEmBordero = mysql_fetch_assoc($qryEmBordero)) {
        $arrayEmBordero[$rowEmBordero['id_saida']] = $rowEmBordero['id_bordero'];
    }
} else {
    $id_bordero = $_REQUEST['id'];
    $condicao[] = ($id_bordero) ? "A.id = {$id_bordero}" : null;
    $arrayBordero = $objBordero->getBoredero($condicao)[$id_bordero];
    $data_criacao = new DateTime($arrayBordero['data_criacao']);
    
//    print_array($arrayBordero);
    $auxSaidas = " AND A.id_saida IN (" . implode(', ', array_keys($arrayBordero['saidas'])) . ")";
}
    
$sqlBanco = "SELECT B.* FROM saida A LEFT JOIN bancos B ON (A.id_banco = B.id_banco) WHERE 1 $auxSaidas LIMIT 1";
$rowBanco = mysql_fetch_assoc(mysql_query($sqlBanco));
//print_array($rowBanco);

if($_COOKIE['debug'] == 666){
    print_array($_REQUEST);
    print_array($auxSaidas);
}

$sqlSaidas = "
SELECT A.id_saida, CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor , A.adicional, A.tipo, A.nome,
IF(A.tipo = 327, CONCAT(TIMESTAMPDIFF(MONTH, E.contratado_em, A.data_vencimento), '/', TIMESTAMPDIFF(MONTH, E.contratado_em,E.encerrado_em)),A.n_documento) AS n_documento, 
A.data_vencimento, A.impresso, A.user_impresso, A.data_impresso, A.id_projeto, B.nome AS nomeProjeto, D.nome_grupo
FROM saida A 
LEFT JOIN projeto B ON (A.id_projeto = B.id_projeto)
LEFT JOIN entradaesaida AS C ON (A.tipo = C.id_entradasaida)
LEFT JOIN entradaesaida_grupo AS D ON (C.grupo = D.id_grupo)
LEFT JOIN prestadorservico E ON (A.id_prestador = E.id_prestador)
WHERE 1 $auxSaidas
ORDER BY A.id_projeto, id_saida";
$qrySaidas = mysql_query($sqlSaidas) or die(mysql_error());
if(mysql_num_rows($qrySaidas) > 0 && !$_REQUEST['id']){
    $insert = mysql_query("INSERT INTO bordero (id_funcionario) VALUES ('{$usuario['id_funcionario']}')");
    $id_bordero = mysql_insert_id();
}
$count = 0;
$sqlTotalSaida = "SELECT SUM(valor) AS valorTotal FROM ($sqlSaidas) AS tot";
$qryTotalSaida = mysql_query($sqlTotalSaida) or die(mysql_error());
$rowTotalSaida = mysql_fetch_assoc($qryTotalSaida);
//print_array($rowTotalSaida);
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
                            <?php if($arrayBordero['pago'] != 1 && $objAcoes->verifica_permissoes(118) && count($arrayEmBordero) == 0) { ?><button type="button" id="pagar_bordero" class="btn btn-default navbar-btn"><i class="fa fa-money"></i> Pagar Borderô</button><?php } ?>
                            <?php if($arrayBordero['pago'] == 1) { ?><button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button><?php } ?>
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
            <h2 class="text-center">RELATÓRIO DE AGRUPAMENTO (<?php echo str_pad($id_bordero, 6, "0", STR_PAD_LEFT) ?>)</h2>
            <?php if($arrayBordero['numero_cheque']) { ?>
            <h2 class="text-center">Valor pago no cheque de número: <?php echo $arrayBordero['numero_cheque'] ?></h2>
            <?php } ?>            
            <h3 class="text-center">Autorização de débito para pagamentos</h3>
            <?php if(count($arrayEmBordero) > 0) { ?>
                <div class="alert alert-warning">
                    <h4>As saidas abaixo já estão em um bordero!</h4>
                    <ul>
                        <?php foreach ($arrayEmBordero as $key => $value) { ?>
                        <li>Saída: <?php echo $key ?> => Bordero: <?php echo $value ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            <form action="" method="post" id="formulario">
            <table class="table table-bordered table-condensed valign-middle">
                <tr>
                    <?php if($rowBanco['id_banco'] == 3) { ?>
                        <th>CAIXA ROTATIVO</th>
                    <?php } else { ?>
                        <th>Agencia: <?php echo $rowBanco['agencia'] ?> | Conta: <?php echo $rowBanco['conta'] ?></th>
                    <?php } ?>
                    <th>Data Emissão: <?php echo ($arrayBordero['data_compensar']) ? $data_criacao->format('d/m/Y') : date('d/m/Y') ?></th>
                    <th>Data à Compensar: 
                        <?php echo (!$arrayBordero['id']) 
                        ? '<input type="type" name="data_compensar" class="data" style="width: 40%" value="'.date('d/m/Y').'" />'
                        : implode('/', array_reverse(explode('-', $arrayBordero['data_compensar']))) ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="2">Titular: <?php echo $row_master['razao'] ?></th>
                    <th>
                        <?php echo (!$arrayBordero['id']) 
                        ? '<input name="campo_livre" type="text" class="input" value="" />'
                        : $arrayBordero['campo_livre'] ?>
                    </th>
                </tr>
                <tr>
                    <th colspan="3">Valor: R$ <?php echo number_format($rowTotalSaida['valorTotal'], 2, ',', '.') ?></th>
                </tr>
            </table>
            <table class="table table-bordered valign-middle">
                <tr>
                    <th>#</th>
                    <th colspan="2">
                        <?php echo (!$arrayBordero['id']) 
                        ? '<input type="text" name="descricao" class="input" value="DESCRIÇÃO" />'
                        : $arrayBordero['descricao'] ?>
                        
                    </th>
                    <th>Nº</th>
                    <th>VALOR</th>
                    <th>VENCIMENTO</th>
                </tr>
                
                <?php while($row = mysql_fetch_assoc($qrySaidas)) { 
                    if(!$_REQUEST['id']) { 
                        $insertBS = mysql_query("INSERT INTO bordero_saidas (id_bordero, id_saida) VALUES ('{$id_bordero}', '{$row['id_saida']}')");
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
                    $totalProjeto += $row['valor']; ?>
                    <tr>
                        <td><?php echo $row['id_saida'] ?></td>
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
                    <td colspan="2" class="text-right">R$ <?php echo number_format($rowTotalSaida['valorTotal'], 2, ',', '.'); ?> </td>
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
                    <td class="text-center hide no-print text-cheque">
                        <input type="text" style="width: 50%" class="input" value="Valor pago no cheque de número:" /><input type="text" style="width: 50%" class="input" id="numero_cheque" name="numero_cheque" value="<?php // echo ($arrayBordero['numero_cheque']) ? $arrayBordero['numero_cheque'] : null ?>" />
                        <?php foreach ($saidas as $key => $value) { ?>
                        <input type="hidden" class="saidas_check" name="id[<?php echo $value ?>]" value="<?php echo $value ?>">
                        <?php } ?>
                        <input type="hidden" name="id_bordero" value="<?php echo $id_bordero ?>">
                        <input type="hidden" name="action" value="pagar_bordero">
                    </td>
                </tr>
                <tr>
                    <td class="text-center">
                        
                    </td>
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
                    bootAlert('Saida paga com sucesso!', 'Saida(s) Paga(s)!', function(){ window.location.href = "/intranet/finan/solicitacao_pagamento.php?id=<?php echo $id_bordero ?>" }, 'success');
                });
            });
        });
        </script>
    </body>
</html>