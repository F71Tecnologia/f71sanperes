<?php
include('../../conn.php');
include('../../wfunction.php');

$id_clt = $_REQUEST['clt'];

$qry = "SELECT A.*, DATE_FORMAT(A.data_nasci, '%d/%m/%Y') AS data_nasci, B.nome_estado_civil, CONCAT(A.campo1, ' / ', A.serie_ctps) AS ctps,
    C.nome AS emp_nome, C.cnpj AS emp_cnpj, C.logradouro AS emp_logradouro, C.numero AS emp_numero, C.complemento AS emp_comp, C.bairro AS emp_bairro, C.cidade AS emp_cidade, C.uf AS emp_uf, C.cep AS emp_cep
    FROM rh_clt AS A
    LEFT JOIN estado_civil AS B ON(A.id_estado_civil = B.id_estado_civil)
    LEFT JOIN rhempresa AS C ON(A.id_projeto = C.id_projeto)
    WHERE A.id_clt = {$id_clt}";
$res = mysql_query($qry) or die(mysql_error());
$row = mysql_fetch_assoc($res);

$sexo = ($row['sexo'] == 'F') ? "Feminino" : "Masculino";
$numero = ($row['numero'] != '0') ? $row['numero'] : "";
$numero_emp = ($row['emp_numero'] != '0') ? $row['emp_numero'] : "";

$qry_evento = "SELECT A.*, DATE_FORMAT(DATE_ADD(A.data, INTERVAL -1 DAY), '%d/%m/%Y') AS ultimo_dia, DATE_FORMAT(A.data, '%d/%m/%Y') AS data
    FROM rh_eventos AS A
    WHERE A.id_clt = {$id_clt} AND A.cod_status = 20 AND A.status = 1 ORDER BY DATE_ADD(A.data, INTERVAL -1 DAY) DESC";
$res_evento = mysql_query($qry_evento) or die(mysql_error());
$row_evento = mysql_fetch_assoc($res_evento);

$qry_depend = "SELECT D.*, DATE_FORMAT(D.data1, '%d/%m/%Y') AS data1, DATE_FORMAT(D.data2, '%d/%m/%Y') AS data2, DATE_FORMAT(D.data3, '%d/%m/%Y') AS data3, DATE_FORMAT(D.data4, '%d/%m/%Y') AS data4, DATE_FORMAT(D.data5, '%d/%m/%Y') AS data5, DATE_FORMAT(D.data6, '%d/%m/%Y') AS data6
    FROM dependentes AS D
    WHERE D.id_bolsista = {$id_clt} AND D.contratacao = 2";
$res_depend = mysql_query($qry_depend) or die(mysql_error());
$row_depend = mysql_fetch_assoc($res_depend);

$qry_empres = "SELECT C.nome AS emp_nome
    FROM rhempresa AS C
    WHERE C.id_regiao = 44";
$res_empres = mysql_query($qry_empres) or die(mysql_error());
$row_empres = mysql_fetch_assoc($res_empres);

$nome1 = explode(" ", $row_depend['nome1']);
$nome1 = $nome1[0];
$nome2 = explode(" ", $row_depend['nome2']);
$nome2 = $nome2[0];
$nome3 = explode(" ", $row_depend['nome3']);
$nome3 = $nome3[0];
$nome4 = explode(" ", $row_depend['nome4']);
$nome4 = $nome4[0];
$nome5 = explode(" ", $row_depend['nome5']);
$nome5 = $nome5[0];
$nome6 = explode(" ", $row_depend['nome6']);
$nome6 = $nome6[0];

$data1 = ($row_depend['data1'] != "00/00/0000") ? $row_depend['data1'] : "";
$data2 = ($row_depend['data2'] != '00/00/0000') ? $row_depend['data2'] : '';
$data3 = ($row_depend['data3'] != '00/00/0000') ? $row_depend['data3'] : '';
$data4 = ($row_depend['data4'] != '00/00/0000') ? $row_depend['data4'] : '';
$data5 = ($row_depend['data5'] != '00/00/0000') ? $row_depend['data5'] : '';
$data6 = ($row_depend['data6'] != '00/00/0000') ? $row_depend['data6'] : '';

//echo "<pre>";
//print_r($row_depend);
//echo "<pre>";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>:: Intranet :: Eventos ::</title>
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/ramon.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="edit_evento.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>
        
        <style type="text/css">
            .secao {
                text-align:right !important; padding-right:3px; font-weight:bold;
            }
            
            .ui-datepicker{
                font-size: 12px;
            }
            
            .hidden, #row_retorno_final, #row_retorno, #row_dias, #row_data, #row_obs{
                display:none;
            }
            
            .show{
                display:block;
            }
            
            .data{
                width:7em;
            }
            
            #corpo_print{
                font-size: 12px;
            }
            
            .f_bold{
                font-weight: bold;
            }
            
            td{
                padding: 0 113px 13px 0;
            }
            
            .text-center{
                text-align: center;
            }
            
            .b-bottom{
                border-bottom: 1px solid #CCC;
            }
            
            .f-size-10{
                font-size: 10px;
            }
            
            .m-b-0{
                margin-bottom: 0;
            }
            
            .m-t-0{
                margin-top: 0;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 720px;">
            <div id="head" style="height: 75px;">
                <img src="../../imagens/logo_previdencia.jpg" class="fleft" style="margin-right: 25px;" alt="">
                <div class="fleft">
                    <h3>PREVIDÊNCIA SOCIAL</h3>                    
                    <h5>INSTITUTO NACIONAL DO SEGURO SOCIAL</h5>
                </div>
            </div>
            <br class="clear" />
            
            <h5 class="m-b-0 m-t-0">REQUERIMENTO DE BENEFÍCIO POR INCAPACIDADE</h5>
            
            <br />
            
            <table>
                <tbody>
                    <tr>
                        <td>
                            <strong>Nome</strong><br />
                            <?php echo $row['nome']; ?>
                        </td>
                        <td>
                            <strong>Data de Nascimento</strong><br />
                            <?php echo $row['data_nasci']; ?>
                        </td>
                        <td>
                            <strong>Nacionalidade</strong><br />
                            <?php echo $row['nacionalidade']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Rua/Av.</strong><br />
                            <?php echo $row['endereco']; ?>
                        </td>
                        <td>
                            <strong>Nº</strong><br />
                            <?php echo $numero; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Complemento</strong><br />
                            <?php echo $row['complemento']; ?>
                        </td>
                        <td>
                            <strong>Bairro</strong><br />
                            <?php echo $row['bairro']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Cidade</strong><br />
                            <?php echo $row['cidade']; ?>
                        </td>
                        <td>
                            <strong>Estado</strong><br />
                            <?php echo $row['uf']; ?>
                        </td>
                        <td>
                            <strong>CEP</strong><br />
                            <?php echo $row['cep']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Sexo</strong><br />
                            <?php echo $sexo; ?>
                        </td>
                        <td>
                            <strong>DOC Inscrição (Nº e Série)</strong><br />
                            <?php echo $row['ctps']; ?>
                        </td>
                        <td>
                            <strong>Estado Civil</strong><br />
                            <?php echo $row['nome_estado_civil']; ?>
                        </td>
                    </tr>  
                    <tr>
                        <td colspan="3">
                            Tem outra atividade com vinculação a Previdência Social?
                            <input type="radio" name="vinc" />Sim
                            <input type="radio" name="vinc" />Não
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">Assinatura ____________________________________________</td>                        
                    </tr>                    
                    <tr>
                        <td colspan="3"><h5 class="b-bottom">ATESTADO DE AFASTAMENTO DO TRABALHO</h5></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Empresa</strong><br />
                            <?php echo $row_empres['emp_nome']; ?>
                        </td>
                        <td>
                            <strong>CNPJ</strong><br />
                            <?php echo $row['emp_cnpj']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Rua/Av.</strong><br />
                            <?php echo $row['emp_logradouro']; ?>
                        </td>
                        <td>
                            <strong>Nº</strong><br />
                            <?php echo $numero_emp; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Complemento</strong><br />
                            <?php echo $row['emp_comp']; ?>
                        </td>
                        <td colspan="2">
                            <strong>Bairro</strong><br />
                            <?php echo $row['emp_bairro']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Cidade</strong><br />
                            <?php echo $row['emp_cidade']; ?>
                        </td>
                        <td>
                            <strong>Estado</strong><br />
                            <?php echo $row['emp_uf']; ?>
                        </td>
                        <td>
                            <strong>CEP</strong><br />
                            <?php echo $row['emp_cep']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong>Último dia de trabalho do segurado</strong><br />
                            <?php echo $row_evento['ultimo_dia']; ?>
                        </td>                        
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong>Afastado por</strong>
                            <input type="radio" name="afast" checked="checked" />Doença
                            <input type="radio" name="afast" />Acidente de Trabalho
                            <input type="radio" name="afast" />Férias
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"><h5 class="b-bottom">DEPENDENTES PARA SALÁRIO FAMÍLIA</h5></td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table>
                                <tbody>
                                    <tr>
                                        <td><strong>Prenome</strong></td>
                                        <td><strong>Nasc.</strong></td>
                                        <td><strong>Prenome</strong></td>
                                        <td><strong>Nasc.</strong></td>
                                    </tr>
                                    <?php if($nome1 != ''){ ?>
                                    <tr>
                                        <td><?php echo $nome1; ?></td>
                                        <td><?php echo $data1; ?></td>
                                        <td><?php echo $nome2; ?></td>
                                        <td><?php echo $data2; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if($nome3 != ''){ ?>
                                    <tr>
                                        <td><?php echo $nome3; ?></td>
                                        <td><?php echo $data3; ?></td>
                                        <td><?php echo $nome4; ?></td>
                                        <td><?php echo $data4; ?></td>
                                    </tr>
                                    <?php } ?>
                                    <?php if($nome5 != ''){ ?>
                                    <tr>
                                        <td><?php echo $nome5; ?></td>
                                        <td><?php echo $data5; ?></td>
                                        <td><?php echo $nome6; ?></td>
                                        <td><?php echo $data6; ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Localidade</strong><br />
                            RIO DE JANEIRO
                        </td>
                        <td>
                            <strong>DATA</strong><br />
                            <?php echo date('d/m/Y'); ?>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-center">
                            ______________________________________________________________<br />
                            Assinatura do responsável e carimbo do CGC da empresa
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <h5 class="b-bottom">INSTRUÇÕES</h5>
                            <span class="f-size-10">
                            1 - O requerimento deve ser sem rasuras e preenchido de preferência à máquina.<br />
                            2 - No caso de segurado empregado, a empresa é responsável pelo preenchimento Atestado de Afastamento do Trabalho<br />
                            3 - No mês do afastamento do trabalho a empresa efetuará o pagamento integral do Salário - Família, e o INSS fará o mesmo no mês da cessação do benefício, evitando-se assim, cálculo de valores fracionados.
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>