<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include "../conn.php";
include('../funcoes.php');
include('../wfunction.php');
require("../classes/SaidaClass.php");

$usuario = carregaUsuario();

$objSaida = new Saida();


$saidas = (is_array($_REQUEST['saidas'])) ? $_REQUEST['saidas'] : [$_REQUEST['saidas']];
$saidas = array_filter($saidas);
if(count($saidas) == 0){
    print_array("Nenhuma saída selecionada!");exit;
}
$auxSaidas = " AND A.id_saida IN (" . implode(', ', $saidas) . ")";


if($_COOKIE['debug'] == 666){
    print_array($_REQUEST);
    print_array($auxSaidas);
}

$sqlSaidas = "
SELECT A.id_saida, A.n_documento, CAST(REPLACE(A.valor, ',', '.') AS DECIMAL(13,2)) AS valor, A.adicional, A.tipo, 
A.nome, A.data_vencimento, A.impresso, A.user_impresso, A.data_impresso, A.id_projeto, B.nome AS nomeProjeto,
C.c_razao nomePrestador, D.nome nomeClt, E.nome nomeOutros,
C.c_cnpj cnpjPrestador, D.rg rgClt, E.cpfcnpj documentoOutros
FROM saida AS A 
LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto)
LEFT JOIN prestadorservico AS C ON (A.id_prestador = C.id_prestador)
LEFT JOIN rh_clt AS D ON (A.id_clt = D.id_clt)
LEFT JOIN entradaesaida_nomes AS E ON (A.id_nome = E.id_nome)
WHERE 1 $auxSaidas
ORDER BY A.id_projeto, id_saida LIMIT 1;";
$qrySaidas = mysql_query($sqlSaidas) or die(mysql_error());
$row = mysql_fetch_assoc($qrySaidas);
//print_array($sqlSaidas);
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
                            <!--<button type="button" id="pagar_bordero" class="btn btn-default navbar-btn"><i class="fa fa-money"></i> Pagar Borero</button>-->
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
            <h2 class="text-center" style="margin-bottom: 50px;"><u>RECIBO DE PAGAMENTO</u></h2>
            <div class="text-left text-bold" style="margin-bottom: 10px;"><?php echo date('d').' de '. mesesArray(date('m')).' de '.date('Y'); ?>,</div>
            <div class="text-left" style="margin-bottom: 70px;">
                Eu, <u><?php echo ($row['nomeClt']) ? $row['nomeClt'] : (($row['nomePrestador']) ? $row['nomePrestador'] : $row['nomeOutros']) ?></u>, portador do ducumento de Nº. <u><?php echo ($row['rgClt']) ? $row['rgClt'] : (($row['cnpjPrestador']) ? $row['cnpjPrestador'] : $row['documentoOutros']) ?></u>, venho por meio desta declarar que recebi nesta data a quantia de <u>R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?> (<?php echo valor_extenso($row['valor']) ?>)</u> da empresa denominada <strong><?php echo $row_master['razao'] ?></strong>, sediada a <strong><?php echo $row_master['endereco'] ?>, <?php echo $row_master['sigla'] ?></strong>.
            </div>
            <h2 class="text-center">____________________________________________________________</h2>
            <h2 class="text-center" style="margin-bottom: 50px;">Assinatura</h2>
<!--            <div class="text-left">Eu, NOME DO PRSTADOR DO SERVIÇO, portador da Cédula de Identidade RG Nº. _____________, venho por meio desta declarar que recebi nesta data a quantia de R$_____________ (VALOR POR EXTENSO) da empresa denominada _________________________, sediada a Rua _____________________, Bairro ________________, Município de ___________________________, SP.
Declaro também que o valor recebido refere-se a serviços esporádicos que prestei a esta empresa, na condição de autônomo, sem habitualidade e freqüência, não caracterizando, em hipótese alguma, vínculo empregatício.
Dou plena e geral quitação pelos serviços que prestei e declaro que nada mais tenho a receber desta empresa, seja a qual título for.
Sem mais e para que esta seja interpretada como verdadeira, firmo.</div>-->
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
        <script>$(function() { });</script>
    </body>
</html>