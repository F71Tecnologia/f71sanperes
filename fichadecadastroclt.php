<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {

    include "conn.php";

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    $result_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_user[id_master]'");

    $row_master = mysql_fetch_array($result_master);

    $pro = $_REQUEST['pro'];
    $id_reg = $_REQUEST['id_reg'];
    $clt = $_REQUEST['clt'];

    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
    $row_regiao = mysql_fetch_assoc($qr_regiao);
    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
    $row_master = mysql_fetch_assoc($qr_master);



    $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad, '%d/%m/%Y')as data_cad,date_format(data_ctps, '%d/%m/%Y')as data_ctps,date_format(data_rg, '%d/%m/%Y')as data_rg,date_format(dada_pis, '%d/%m/%Y')as data_pis FROM rh_clt where id_clt = '$clt'");
    $row = mysql_fetch_array($result_bol);

    $result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = $row[id_curso]");
    $row_bol3 = mysql_fetch_array($result_bol3);

    $result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = $row[id_curso]");
    $row_bol2 = mysql_fetch_array($result_bol2);

    $result_reg = mysql_query("Select * from  regioes where id_regiao = $row[id_regiao]");
    $row_reg = mysql_fetch_array($result_reg);

    $result_curso = mysql_query("Select * from  curso where id_curso = '$row[id_curso]'");
    $row_curso = mysql_fetch_array($result_curso);

    $result_pro = mysql_query("Select * from  projeto where id_projeto = $pro");
    $row_pro = mysql_fetch_array($result_pro);

    $result_horario = mysql_query("Select * from rh_horarios where id_horario = '$row[rh_horario]'");
    $row_horario = mysql_fetch_array($result_horario);

    //$total = "$row_horario[horas_mes]" / "$row_horario[dias_semana]";
    $total = $row_horario[horas_semanais];

    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '2' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if ($num_row_verifica == "0") {
        mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('2','$clt','$data_cad', '$user_cad')");
    } else {
        mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '2'");
    }
    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS


    /*
      $result_abol = mysql_query("SELECT *,date_format(dada_pis, '%d/%m/%Y')as dada_pis ,date_format(data_ctps, '%d/%m/%Y')as data_ctps FROM a$tab where id_bolsista = '$id_bol'");
      $row_abol = mysql_fetch_array($result_abol);
     */

    $result_vale = mysql_query("Select * from vale where id_bolsista = '$row[id_antigo]' AND id_projeto = '$pro'");
    $row_vale = mysql_fetch_array($result_vale);

    $result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
    $row_banco = mysql_fetch_array($result_banco);

    if ($row['id_banco'] == "") {
        $banco = $row['nome_banco'];
    } else {
        $banco = $row_banco['nome'];
    }

    $result_depende = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$row[id_clt]' AND nome1 != '' AND id_regiao = '$id_reg' AND id_projeto = '$pro' ORDER BY nome");
    $row_depende = mysql_fetch_array($result_depende);

    $dia = date('d');
    $mes = date('n');
    $ano = date('Y');
    switch ($mes) {
        case 1:
            $mes = "Janeiro";
            break;
        case 2:
            $mes = "Fevereiro";
            break;
        case 3:
            $mes = "Março";
            break;
        case 4:
            $mes = "Abril";
            break;
        case 5:
            $mes = "Maio";
            break;
        case 6:
            $mes = "Junho";
            break;
        case 7:
            $mes = "Julho";
            break;
        case 8:
            $mes = "Agosto";
            break;
        case 9:
            $mes = "Setembro";
            break;
        case 10:
            $mes = "Outubro";
            break;
        case 11:
            $mes = "Novembro";
            break;
        case 12:
            $mes = "Dezembro";
            break;
    }
    ?>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
            <title>Ficha de Cadastro CLT</title>
            <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
            <link href="resources/css/bootstrap.css" rel="stylesheet" type="text/css">
            <link href="resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
            <link href="resources/css/font-awesome.min.css" rel="stylesheet">
            <link href="resources/css/style-print.css" rel="stylesheet">
            <script src="js/jquery-1.10.2.min.js" type="text/javascript"></script>
            <script src="resources/js/print.js" type="text/javascript"></script>
        </head>
        <style>
        @page {
            margin-top: 0px;
            margin-left: 70px;
            margin-right: 70px;
            margin-bottom: 0px;
        }
        </style>
        <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="text-center">
                    <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                    <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                </div>
            </div>
        </nav>
        <div class="pagina">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <img src='imagens/logomaster<?= $row_master['id_master'] ?>.gif' alt="" width='120' height='86' />
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
                        print "<img src='fotosclt/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>";
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
                                    <b><?php $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '{$row['id_projeto']}'");
                                        $projeto = mysql_fetch_assoc($qr_projeto);
                                        echo $projeto['nome']; ?></b>
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
                                <td colspan="3"><b><?php print $banco; ?></b></td>
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
        </div>

        </body>
    </html>
<?php } ?>