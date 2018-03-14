<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
} else {
include("../conn.php");
include("../classes/funcionario.php"); 
include("../wfunction.php");
include "../classes/regiao.php";
include('../classes/global.php');
include '../classes_permissoes/regioes.class.php';
include "../classes_permissoes/acoes.class.php";
            
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$nomemes = new regiao();
    
    $result_master = mysql_query("SELECT * FROM master WHERE id_master = '{$usuario['id_master']}'");
    $row_master = mysql_fetch_array($result_master);

    $id_reg = $usuario['id_regiao'];
    $tela = (isset($_REQUEST['tela'])) ? $_REQUEST['tela'] : 1;
    ?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
          <!-- Bootstrap -->
        <link href="css/estrutura.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" media="all" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">	

        <style>
            body {
                text-align: left!important;
            }
        </style>
        <title>:: Intranet :: Ficha de Cadastro de Participantes</title>
    </head>
     
    <body>
        <?php
        switch ($tela) {
            case 1:
                $projetosOp = array("-1" => "« Selecione »");
                $query = "SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$id_reg'";
                $result = mysql_query($query) or die(mysql_error());
                while ($row = mysql_fetch_assoc($result)) {
                    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
                }
                ?>
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório <small> - Ficha de Cadastro de Participantes</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                    <div class="panel-body">
                      
                        
                        <div class="form-group" >
                         <label for="select" class="col-sm-4 control-label hidden-print">Projeto</label>
                        <div class="col-sm-4">
                             <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'required[custom[select]] form-control')); ?><span class="loader"></span>
                        </div>
                        </div>
                        
                        <div class="form-group" >
                         <label for="select" class="col-sm-4 control-label hidden-print">Selecione o tipo de contrato</label>
                        <div class="col-sm-4">
                             <select name="tipo" id="tipo" class="campotexto form-control">
                                <option value="2">CLT</option>
                                <option value="3">Colaborador</option>
                                <option value="4">Autônomo / PJ</option>
                            </select>
                        </div>
                        </div>

                    <div class="panel-footer text-right hidden-print">
                        <input type="hidden" name="reg" id="reg" value="<?= $id_reg ?>">
                        <input type="hidden" name="tela" id="tela" value="2">
                        <button type="submit" name="button" id="button" value="Gerar Ficha" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar Ficha</button>
                    </div>               
                    </div>
                    
                   
                    <?php
                    
                    break;
                case 2:
                    $pro = $_REQUEST['projeto'];
                    ?>
                <body style="background-color:#FFF; margin-top:-60px;">

                    <?php
                    $tipo = $_REQUEST['tipo'];

                    if (empty($_REQUEST['pagina'])) {
                        $intervalo = "20";
                        $ini_atual = "0";
                        $fim_atual = $ini_atual + $intervalo;
                        $pagina = "1";
                    } else {
                        $pagina = $_REQUEST['pagina'];
                        $intervalo = "20";
                        $ini_atual = $intervalo * $pagina - $intervalo;
                        $fim_atual = $ini_atual + $intervalo;
                    }

                    if ($tipo == "1" or $tipo == "3" or $tipo == "4") {
                        $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad , '%d/%m/%Y')as sis_data_cadastro ,date_format(data_rg , '%d/%m/%Y')as data_rg, date_format(data_ctps , '%d/%m/%Y')as data_ctps, date_format(dada_pis , '%d/%m/%Y')as dada_pis FROM autonomo where tipo_contratacao != '2' AND id_projeto='$pro' AND status = '1'");
                        $result_bol_g = mysql_query("SELECT id_autonomo FROM autonomo WHERE tipo_contratacao != '2' AND id_projeto = '$pro' AND status = '1'");
                    } else {
                        $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad, '%d/%m/%Y')as sis_data_cadastro, date_format(data_rg , '%d/%m/%Y')as data_rg, date_format(data_ctps , '%d/%m/%Y')as data_ctps, date_format(dada_pis , '%d/%m/%Y')as dada_pis FROM rh_clt where id_projeto = '$pro' AND status > '60' ORDER BY nome");
                        $result_bol_g = mysql_query("SELECT id_clt FROM rh_clt WHERE id_projeto = '$pro' AND status > 60");
                    }

                    while ($row = mysql_fetch_array($result_bol)) {

                        $result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = $row[id_curso]", $conn);
                        $row_bol3 = mysql_fetch_array($result_bol3);

                        $result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]", $conn);
                        $row_bol2 = mysql_fetch_array($result_bol2);

                        $result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
                        $row_reg = mysql_fetch_array($result_reg);

                        $result_curso = mysql_query("Select * from  curso where id_curso = '$row[id_curso]'", $conn);
                        $row_curso = mysql_fetch_array($result_curso);

                        $result_pro = mysql_query("Select * from  projeto where id_projeto = '$pro'", $conn);
                        $row_pro = mysql_fetch_array($result_pro);

                        $result_vale = mysql_query("Select * from vale where id_bolsista = '$row[0]'", $conn);
                        $row_vale = mysql_fetch_array($result_vale);


                        $result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
                        $row_banco = mysql_fetch_array($result_banco);

                        $result_depende = mysql_query("SELECT *,date_format(data1, '%d/%m/%Y')as data1 ,date_format(data2, '%d/%m/%Y')as data2, date_format(data3, '%d/%m/%Y')as data3, date_format(data4, '%d/%m/%Y')as data4 ,date_format(data5, '%d/%m/%Y')as data5 FROM dependentes WHERE id_clt = '$row[0]' AND id_projeto = '$pro'", $conn);

                        $row_depende = mysql_fetch_array($result_depende);

                        echo $row_depende['nome'];

                        $dia = date('d');
                        $mes = date('m');
                        $ano = date('Y');

                        if ($row['tipo_contratacao'] == "1") {
                            $vinculo_cad = "Autônomo";
                        } elseif ($row['tipo_contratacao'] == "2") {
                            $vinculo_cad = "CLT";
                        } elseif ($row['tipo_contratacao'] == "3") {
                            $vinculo_cad = "Colaborador";
                        } elseif ($row['tipo_contratacao'] == "4") {
                            $vinculo_cad = "Autônomo / PJ";
                        }

                        if ($row['status'] == "1" or $row['status'] == "10") {
                            $status_bol = "Ativo";
                        } else {
                            $status_bol = "<font color=red>Desativado</font>";
                        }

                        $nomemes->MostraMes($mes);
                        $mes = $nomemes;
                        ?>
                    
                        <table cellspacing="0" cellpadding="0" class="relacao" style="page-break-after: always;width:720px; border:0px; margin-top:60px; margin-bottom:10px;">
                <tr>
                    <td>
                        <img src='../imagens/logomaster<?= $row_master['id_master'] ?>.gif' alt="" width='120' height='86' />
                    </td>
                    <td align="center">
                        <strong>FICHA DE CADASTRO</strong><br>
                        <?= $row_master['razao'] ?>
                        <table width="272" border="0" align="center" cellpadding="4" cellspacing="1" style="font-size:12px;">
                            <tr style="color:#FFF;">
                                <td width="103" height="22" class="top">C&Oacute;DIGO</td>
                                <td width="103" class="top">STATUS</td>
                                <td width="103" class="top">VINCULO</td>
                            </tr>
                            <tr style="color:#333; background-color:#efefef;">
                                <td height="20" align="center"><b><?php print "$row[campo3]"; ?></b></td>
                                <td align="center"><b><?php print "$status_bol"; ?></b></td>
                                <td align="center"><b><?php print "$vinculo_cad"; ?></b></td>
                            </tr>
                        </table>
                    </td>
                    <td align="right">
                        <?php
                        if ($row['foto'] == "1") {
                            $nome_imagem = $id_reg . "_" . $pro . "_" . $row['0'] . ".gif";
                        } else {
                            $nome_imagem = 'semimagem.gif';
                        }
                        print "<img src='../fotosclt/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>";
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table class="relacao" style="width:100%; margin-top:10px;">
                            <tr class="secao_pai">
                                <td colspan="6">
                                    <strong>DADOS DO PARTICIPANTE</strong></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="5">Participante</td>
                                <td width="18%">Data de Entrada</td>
                            </tr>
                            <tr>
                                <td colspan="5"><b><?php print "$row[nome]"; ?></b></td>
                                <td><b><?php print "$row[data_entrada]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="5">Endere&ccedil;o</td>
                                <td>CEP</td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <!--<b><?php print "$row[endereco]"; ?>, <?php print "$row[bairro]"; ?>, <?php print "$row[cidade]"; ?> - <?php print "$row[uf]"; ?></b>-->
                                    <b><?=$row['endereco'].", ".$row['numero'].", ".$row['complemento']." - ".$row['bairro']." - ".$row['cidade']." - ".$row['uf']; ?></b>
                                </td>
                                <td><b><?php print "$row[cep]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="2">Estado Civil</td>
                                <td>Naturalidade</td>
                                <td>Nacionalidade</td>
                                <td width="9%">Telefone</td>
                                <td>Data de Nascimento</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b><?php print "$row[civil]"; ?></b></td>
                                <td><b><?php print "$row[naturalidade]"; ?></b></td>
                                <td><b><?php print "$row[nacionalidade]"; ?></b></td>
                                <td><b><?php print "$row[tel_fixo]"; ?></b></td>
                                <td><b><?php print "$row[data_nasci]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="4">Escolaridade</td>
                                <td>PIS</td>
                                <td>PIS Cadastrado em:</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                        <?php
                                        $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row[escolaridade]' AND status = 'on'");
                                        $escolaridade = mysql_fetch_assoc($qr_escolaridade);
                                        print "$escolaridade[nome]";
                                        ?>
                                </td>
                                <td><b>
                                <?php
                                if ($tipo == "1" or $tipo == "3") {
                                    print "$row_abol[pis]";
                                } else {
                                    print "$row[pis]";
                                }
                                ?>
                                    </b></td>
                                <td><b>
                                <?php
                                if ($tipo == "1" or $tipo == "3") {
                                    print "$row_abol[dada_pis]";
                                } else {
                                    print "$row[dada_pis]";
                                }
                                ?>
                                    </b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="2">C&uacute;tis</td>
                                <td width="14%">Estatura</td>
                                <td width="14%">Peso</td>
                                <td>Cabelo</td>
                                <td>Olhos</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b><?php print "$row[defeito]"; ?></b></td>
                                <td><b><?php print "$row[altura]"; ?></b></td>
                                <td><b><?php print "$row[peso]"; ?></b></td>
                                <td><b><?php print "$row[cabelos]"; ?></b></td>
                                <td><b><?php print "$row[olhos]"; ?></b></td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <table cellpadding="0" cellspacing="0" border="0" style="font-size:12px; width:100%; margin-left:-5px;">
                                        <tr class="secao">
                                            <td width="10%">RG</td>
                                            <td width="10%">Expedi&ccedil;&atilde;o</td>
                                            <td width="10%">&Oacute;rg&atilde;o</td>
                                            <td colspan="2" width="20%">CTPS</td>
                                            <td width="10%">Reservista</td>
                                        </tr>
                                        <tr>
                                            <td><b><?php print "$row[rg]"; ?></b></td>
                                            <td><b><?php print "$row[data_rg]"; ?></b></td>
                                            <td><b><?php print "$row[orgao] / $row[uf_ctps]"; ?></b></td>
                                            <td colspan="2"><b><?php print "$row[campo1]"; ?> / <?= $row['serie_ctps'] ?> / <?= $row['uf_ctps'] ?> /
                                            <?php
                                            if ($tipo == "1" or $tipo == "3") {
                                                print "$row_abol[data_ctps]";
                                            } else {
                                                print "$row[data_ctps]";
                                            }
                                            ?></b></td>
                                            <td><b><?php print "$row[reservista]"; ?></b></td>
                                        </tr>
                                        
                                        <tr class="secao">
                                            <td width="20%">ÓRGAO REGU.</td>
                                            <td width="10%">CPF</td>
                                            <td width="10%">Habilita&ccedil;&atilde;o</td>
                                            <td width="10%">Titulo</td>
                                            <td width="5%">Zona</td>
                                            <td width="5%">Se&ccedil;&atilde;o</td>
                                        </tr>
                                        
                                        <tr>
                                            <td><b><?php print "$row[conselho]"; ?></b></td>
                                            <td><b><?php print "$row[cpf]"; ?></b></td>
                                            <td>&nbsp;</td>
                                            <td><b><?php print "$row[titulo]"; ?></b></td>
                                            <td><b><?php print "$row[zona]"; ?></b></td>
                                            <td><b><?php print "$row[secao]"; ?></b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="secao_pai">
                                <td colspan="6">FILIA&Ccedil;&Atilde;O</td>
                            </tr>
                            <tr class="secao">
                                <td colspan="5">Pai</td>
                                <td>Nacionalidade</td>
                            </tr>
                            <tr>
                                <td colspan="5"><b><?php print "$row[pai]"; ?></b></td>
                                <td><b><?php print "$row[nacionalidade_pai]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="5">M&atilde;e</td>
                                <td>Nacionalidade</td>
                            </tr>
                            <tr>
                                <td colspan="5"><b><?php print "$row[mae]"; ?></b></td>
                                <td><b><?php print "$row[nacionalidade_mae]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="6">Dependentes</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="secao"><b>
    <?php
    if (!empty($row_depende['nome1'])) {
        print "$row_depende[nome1] - $row_depende[data1]";
    }
    if (!empty($row_depende['nome2'])) {
        print " / $row_depende[nome2] - $row_depende[data2]";
    }
    if (!empty($row_depende['nome3'])) {
        print " / $row_depende[nome3] - $row_depende[data3]";
    }
    if (!empty($row_depende['nome4'])) {
        print " / $row_depende[nome4] - $row_depende[data4]";
    }
    if (!empty($row_depende['nome5'])) {
        print " / $row_depende[nome5] - $row_depende[data5]";
    }
    ?>
                                    </b></td>
                            </tr>
                            <tr class="secao_pai">
                                <td colspan="6">DADOS DA FUN&Ccedil;&Atilde;O E HOR&Aacute;RIOS</td>
                            </tr>
                            <tr class="secao">
                                <td colspan="6">Projeto</td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <b><?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$_GET[pro]'");
    $projeto = mysql_fetch_assoc($qr_projeto);
    echo "$projeto[nome]"; ?></b>
                                </td>
                            </tr>
                            <tr class="secao">
                                <td width="41%">Atividade</td>
                                <td width="4%">CBO</td>
                                <td>Bolsa</td>
                                <td>Mensal</td>
                                <td><span class="style1">Horas/Dia </span></td>
                                <td><span class="style1">Dias Trabalho</span></td>
                            </tr>
                            <tr>
                                <td><b><?php print "$row_curso[nome]"; ?></b></td>
                                <td>&nbsp;</td>
                                <td>R$ <b><?php print number_format("$row_curso[salario]", 2, ',', '.'); ?></b></td>
                                <td><b><?php print "$row_horario[horas_mes]"; ?></b></td>
                                <td><b><?php print "$total"; ?></b></td>
                                <td><b><?php print "$row_horario[dias_semana]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="2">Conta</td>
                                <td>Ag&ecirc;ncia</td>
                                <td colspan="3">Banco</td>
                            </tr>
                            <tr>
                                <td colspan="2"><b><?php print "$row[conta]"; ?></b></td>
                                <td><b><?php print "$row[agencia]"; ?></b></td>
                                <td colspan="3"><b><?php print "$row_banco[nome]"; ?></b></td>
                            </tr>
                            <tr class="secao">
                                <td colspan="2">Qtd. de &ocirc;nibus / Valor Transporte</td>
                                <td>Tipo</td>
                                <td colspan="3">Per&iacute;odo de Experi&ecirc;ncia</td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php print "$row_vale[qnt1] - R$ $row_vale[valor1] / $row_vale[qnt2] - R$ $row_vale[valor2] / $row_vale[qnt3] - R$ $row_vale[valor3]/ $row_vale[qnt4] - R$ $row_vale[valor4]"; ?></td>
                                <td><b>
    <?php
    if ($row_vale['tipo_vale'] == "1") {
        $tipovale = "Cart&atilde;o";
    } else {
        $tipovale = "Papel";
    }
    print "$tipovale";
    ?>
                                    </b></td>
                                <td colspan="3">30 trinta (&nbsp;&nbsp;&nbsp;) &nbsp;60 sessenta (&nbsp;&nbsp;&nbsp;) &nbsp;&nbsp;&nbsp;90 noventa (&nbsp;&nbsp;&nbsp;)</td>
                            </tr>
                            <tr class="secao">
                                <td colspan="6">Hor&aacute;rio de Trabalho</td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    DE SEGUNDA &Agrave; SEXTA DAS:&nbsp;______:_____ &Agrave;S ______:_____ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    HORAS&nbsp;/&nbsp;INTERVALO: ______:_____ &Agrave;S ______:_____ <br>
                                    HORAS S&Aacute;BADO DAS: ______:_____ &Agrave;S ______:_____               &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    HORAS&nbsp;/ INTERVALO: ______:_____ &Agrave;S ______:_____</td>
                            </tr>
                            <tr class="secao">
                                <td colspan="6">Observa&ccedil;&otilde;es</td>
                            </tr>
                            <tr>
                                <td colspan="6"><p align="center"><br>
                                        _________________________________________________________________________________<br><br>
                                        __________________________________________________________________________________
                                    </p></td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <table cellpadding="0" cellspacing="0" border="0" style="font-size:12px; text-align:center; width:100%;">
                                        <tr>
                                            <td width="15%">
                                                <br>_________________<br>DATA
                                            </td>
                                            <td width="35%">
                                                <br>__________________________________<br>ASSINATURA EMPREGADO
                                            </td>
                                            <td width="15%">
                                                <br>_________________<br>DATA
                                            </td>
                                            <td width="35%">
                                                <br>__________________________________<br>ASSINATURA EMPREGADOR 
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
<!--                            <tr class="secao_pai">
                                <td colspan="6">DADOS FINANCEIROS</td>
                            </tr>
                            <tr class="secao">
                                <td colspan="6">Férias</td>
                            </tr>
                            <tr>
                                <td colspan="6">

                                <?php
                                // Tela de Histórico
                                $qr_historico = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$clt' AND status = '1' ORDER BY id_ferias ASC");
                                $numero_historico = mysql_num_rows($qr_historico);
                                if (!empty($numero_historico)) {
                                    while ($historico = mysql_fetch_assoc($qr_historico)) {

                                        $data_aquisitivo_inicio = implode('/', array_reverse(explode('-', $historico['data_aquisitivo_ini'])));
                                        $data_aquisitivo_fim = implode('/', array_reverse(explode('-', $historico['data_aquisitivo_fim'])));
                                        $data_ferias_inicio = implode('/', array_reverse(explode('-', $historico['data_ini'])));
                                        $data_ferias_fim = implode('/', array_reverse(explode('-', $historico['data_fim'])));
                                        ?>

                                            <table cellspacing="0" cellpadding="2" style="width:100%; font-size:12px;">
                                                <tr>
                                                    <td width="110"><b>Período Aquisitivo:</b></td>
                                                    <td><?= $data_aquisitivo_inicio ?> <i>à</i> <?= $data_aquisitivo_fim ?></td>
                                                    <td width="110"><b>Período de Férias:</b></td>
                                                    <td><?= $data_ferias_inicio ?> <i>à</i> <?= $data_ferias_fim ?></td>
                                                </tr>
                                            </table>

                                    <?php }
                                }
                                ?>

                                </td>
                            </tr>
                            <tr class="secao">
                                <td colspan="3">Alterações Salariais </td>
                                <td colspan="3">Data da Alteração Salarial </td>
                            </tr>
                            <tr>
                                <?php
                                $qrAltSalarial = mysql_query("SELECT A.salario_novo, A.salario_antigo, A.data, B.data_cad, A.id_curso 
                                                    FROM rh_salario AS A
                                                    INNER JOIN curso AS B ON B.id_curso = A.id_curso
                                                    WHERE A.id_curso = '$row[id_curso]'");
                                while ($rsAltSalarial = mysql_fetch_assoc($qrAltSalarial)) {
                                    $salarioAnt = $rsAltSalarial['salario_antigo'];
                                    $salarioNew = $rsAltSalarial['salario_novo'];
                                    if ($salarioAnt == 0 or $salarioAnt == 1) {
                                        $salarioAnt = '';
                                        $salarioNew = '';
                                        $data = '';
                                    } else {
                                        $salarioAnt = "De: " . $rsAltSalarial['salario_antigo'] . " / ";
                                        $salarioNew = "Para: " . $rsAltSalarial['salario_novo'];
                                        $data = date("d/m/Y", strtotime(str_replace('-', '/', $rsAltSalarial['data'])));
                                    }
                                    ?>

                                    <td colspan="3"><?php echo $salarioAnt; ?><?php echo $salarioNew; ?></td>
                                    <td colspan="3"><?php echo $data; ?></td>
                                </tr>
                                <?php } ?>
                            <tr class="secao">
                                <td colspan="6">Contribuições Sindicais</td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <?php
                                    if (sizeof($contribuicao) != 0) {
                                        foreach ($contribuicao_sindical as $contribuicao) {
                                            echo $contribuicao;
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>-->
                        </table>
                    </td>
                </tr>
            </table>
                        <!---Aqui a página é quebrada-->
                    <?php } ?>
                </td>
            </tr>
            </table>
                </div>     
              <?php
//            $total_registros = mysql_num_rows($result_bol_g);
//            $total_registros2 = mysql_num_rows($result_bol);
//
//            $cauculo_paginas = $total_registros / $intervalo;
//            $qnt_paginas = round($cauculo_paginas);
//            $t = $qnt_paginas * $intervalo;
//
//            if ($total_registros > $t) {
//                $qnt_paginas = $qnt_paginas + 1;
//            }
//
//            $fim_atual_f = $fim_atual - 1;
//            $fim_atual_f2 = $ini_atual + $total_registros2;
//            
//            print "<div id='navegacao'>
//           <table class='table table-striped' width='720' border='0' cellspacing='0' cellpadding='0' align='center'>
//  <tr>
//    <td align='left'>Mostrando Registros de $ini_atual - $fim_atual_f2 no total de $total_registros em $qnt_paginas páginas</td>
//  </tr>
//  <tr>
//    <td align='left'>";
//
//            $resultado_pag = $pagina - 3;
//            $final_de_paginas = $qnt_paginas - 9;
//
//            if ($qnt_paginas <= 10) {
//
//                $pagina_i = 1;
//                $ultima_pagina = $qnt_paginas;
//            } else {
//
//                if ($resultado_pag >= $final_de_paginas) {
//                    $antes = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=1&tela=2&tipo=$tipo' title=\"Primeira Página\"><<</a>";
//                    $pagina_i = $final_de_paginas;
//                    $ultima_pagina = $pagina_i + 10 - 1;
//                } else {
//
//                    if ($pagina == "1" or $pagina == "2" or $pagina == "3" or $pagina == "4") {
//
//                        $pagina_i = 1;
//                        $ultima_pagina = $pagina_i + 10 - 1;
//                        $depois = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$qnt_paginas&tela=2&tipo=$tipo' title=\"Ultima Página\">>></a>";
//                    } elseif ($pagina >= 5) {
//
//                        $antes = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=1&tela=2&tipo=$tipo' title=\"Primeira Página\"><<</a>";
//                        $pagina_i = $pagina - 3;
//                        $ultima_pagina = $pagina_i + 10 - 1;
//                        $depois = "<a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$qnt_paginas&tela=2&tipo=$tipo' title=\"Ultima Página\">>></a>";
//                    }
//                }
//            }
//            $i = $pagina_i;
//            print "$antes";
//            for ($i = $i; $i <= $ultima_pagina; $i++) {
//
//                if ($i == $pagina) {
//                    print " $i ";
//                } else {
//                    print " <a href='fichadecadastro.php?reg=$id_reg&pro=$pro&pagina=$i&tela=2&tipo=$tipo'>$i</a> ";
//                }
//            }
//
//            print "$depois</td>
//  </tr>
//</table>
//</div>
//";
            ?>

            <?php
            break;
    }
}
?>
            
             </div>
            </form>
            
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
      
      
    </body>
</html>
