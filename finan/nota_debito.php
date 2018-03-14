<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include "../conn.php";
include('../funcoes.php');
include('../wfunction.php');

$link_enc = $_REQUEST['link_enc'];
$id_saida = $_REQUEST['id'];

mysql_query("UPDATE saida SET impresso = 1, user_impresso = '$_COOKIE[logado]', data_impresso = NOW() WHERE id_saida = '$id_saida'");

$qr_funcionario = mysql_query("SELECT B.regiao as regiao,C.nome as nome_master,C.id_master, A.nome as nome_funcionario,
                               C.sigla,C.razao,C.endereco,C.cnpj, C.telefone, C.cep
                                FROM funcionario as A
                                INNER JOIN regioes as B
                                ON A.id_regiao = B.id_regiao
                                INNER JOIN master as C
                                ON C.id_master = B.id_master
                                WHERE id_funcionario = '$_COOKIE[logado]'
                                ") or die (mysql_error());
$row_func = mysql_fetch_assoc($qr_funcionario);

$qr_saida = mysql_query("SELECT *,(CAST(REPLACE(valor, ',', '.') as decimal(13,2)) + CAST(REPLACE(adicional, ',', '.') as decimal(13,2))) as valtotal FROM saida WHERE id_saida = '$id_saida'") or die(mysql_error());
$row_saida = mysql_fetch_assoc($qr_saida);

$qr_regiao  = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_saida[id_regiao]'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_tipos = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row_saida[tipo]'") or die(mysql_error());
$row_tipo = mysql_fetch_assoc($qr_tipos);
$grupo    = $row_tipo['grupo'];
//print_array([$row_func,$row_tipo, $row_saida[tipo]]);

$array_entradasaida_nomes = array(10,20,40,50,60,70,80);
if(in_array($grupo,$array_entradasaida_nomes)){
    $qr_entradasaida_nome = mysql_query("SELECT * FROM  `entradaesaida_nomes` WHERE id_nome = '$row_saida[id_nome]' ORDER BY nome") ;  
    $row_entradasaida_nome = mysql_fetch_assoc($qr_entradasaida_nome);   

    $nome = $row_entradasaida_nome['nome'];
    $cpf_cnpj =  $row_entradasaida_nome['cpfcnpj'];
    $campo_tipo = 1;
}

$sqlGrupo = "SELECT * FROM  `entradaesaida_grupo` WHERE 1";
$qryGrupo = mysql_query($sqlGrupo);
while($rowGrupo = mysql_fetch_assoc($qryGrupo)){
    $arrayGrupos[$rowGrupo['id_grupo']] = $rowGrupo['nome_grupo'];
}

//////////////////////
/////CREDOR//////////
/////////////////////
if($grupo == 30 or $row_saida['tipo'] == 32 or $row_saida['tipo'] == 132){
    if($row_saida['tipo_empresa'] == 1){
        $qr_prestador  = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = '$row_saida[id_prestador]'");
        $row_prestador = mysql_fetch_assoc($qr_prestador);   
        $nome =  $row_prestador['c_fantasia'];
        $cpf_cnpj =  $row_prestador['c_cnpj'];
        $campo_credor = 1;
    } elseif($row_saida['tipo_empresa'] == 2) {
        $qr_fornecedor  = mysql_query("SELECT * FROM fornecedores WHERE id_fornecedor = '$row_saida[id_fornecedor]'");
        $row_fornecedor = mysql_fetch_assoc($qr_fornecedor);   
        $nome           =  $row_fornecedor['razao'];
        $cpf_cnpj       =  $row_fornecedor['cnpj'];
        $campo_credor = 1;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <title>:: Intranet :: Relatório de Termo de Vale Transporte em Lote </title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="shortcut icon" href="../favicon.ico">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
    <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">-->
    <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="../resources/css/style-print.css" rel="stylesheet">
    <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
    <style type="text/css">
        body { font-size: 11px; }
        table{
            width: 100%;
            border: collapse;    
            margin-top: 10px;
            border-collapse: collapse;
        }
        table tr{
            border: 1px solid #000;
            height: 40px;
        }
        table td{
           border: 1px solid #000;
           padding: 1px;
        }
        table.sem_borda{ border: 0; margin-top: 40px; }
        table.sem_borda tr{ border: 0; }
        table.sem_borda td{ border: 0; }
        
        .pagina {
            padding: 1.5cm !important;
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
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn">Voltar</button>-->
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
                ?>
            </div>
            <table>
                <!--<h2><?=$row_func['razao']?></h2>-->
                <tr>
                    <?php if($row_saida['id_tipo_pag_saida'] == 8) { ?>
                    <td width="150"><strong>Nº DO CHEQUE:</strong></td>
                    <td width="150" align="left"><?=$row_saida['n_documento']?> </td>
                    <?php } else { ?>
                    <td width="150"><strong>NOTA DE DÉBITO:</strong></td>
                    <td width="150" align="left"><?=$id_saida?> </td>
                    <?php } ?>
                    <td width="150"><strong>DATA DE PAGAMENTO:</strong></td>
                    <td width="130" align="left"><?=implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])))?></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="150"><strong>UNIDADE</strong></td>
                    <td colspan="3" align="left"><?=$row_regiao['regiao']?></td>
                </tr>    
            </table>
            <?php if(!empty($campo_credor) ) { ?>   
                <table> 
                    <tr>
                        <td width="150"><strong>CREDOR:</strong></td>
                        <td align="left"><?=$nome?></td>
                        <td width="170"><strong>CPF/CNPJ:</strong></td>
                        <td align="left"><?=$cpf_cnpj?></td>
                    </tr>   
                </table>
            <?php } elseif(!empty ($campo_tipo)){ ?> 
                <table>  
                    <tr>
                        <td width="150" ><strong>TIPO:</strong></td>
                        <td align="left"><?=$nome?></td>        
                    </tr>   
                </table>
            <?php } ?>
            <table>
                <tr>
                    <td width="150" ><strong>VALOR</strong></td>    
                    <td colspan="4" align="left">R$ <?=number_format($row_saida['valtotal'],2,',','.')?></td>
                </tr>
            </table>

            <table>    
                <tr>
                    <td colspan="4" ><strong>DESCRIÇÃO DA DESPESA</strong></td>
                </tr>
                <tr height="100">
                    <td colspan="4" align="left" valign="top"><?= $row_saida['especifica'] ?></td> 
                </tr>
            </table>
            <table class="text-sm">
                <tr>
                    <td colspan="<?php echo count($arrayGrupos) ?>"><strong>CLASSIFICAÇÃO DE DESPESAS</strong></td>
                </tr>
                <tr>
                    <?php foreach ($arrayGrupos as $key => $value) { ?><td style="font-size: 9px;"><strong><?php echo str_replace('/',' / ',$value) ?></strong></td><?php } ?>
                </tr>
                <tr>
                    <?php foreach ($arrayGrupos as $key => $value) { ?><td><?php echo ($grupo == $key) ? $row_tipo['cod'].' <br> '.$row_tipo['nome'] : '' ?></td><?php } ?>
                </tr>
                <?php
//                $endereco = explode(',',$row_func['endereco']);
                $meses = array (1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");

                $hoje = getdate();
                $dia = $hoje["mday"];
                $mes = $hoje["mon"];
                $nomemes = $meses[$mes];
                $ano = $hoje["year"]; ?>
            </table>
            <table class="sem_borda">
                <tr>
                    <td width="280"></td>
                    <td><strong><?php echo $endereco[3];?>, <?php echo $dia.' de '.$nomemes.' de '.$ano; ?></strong></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center">____________________________________________________________</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-center"><strong><?php echo $row_func['razao']?></strong></td>
                </tr>
            </table>
            <table class="sem_borda">
                <tr>
                    <td align="center">
                        <strong>
                        <?= $row_func['endereco'] .',<br> CEP:'.formato_cep($row_func['cep']).', CNPJ: '.$row_func['cnpj']. ', Telefone: '.$row_func['telefone']; ?>
                        </strong>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>