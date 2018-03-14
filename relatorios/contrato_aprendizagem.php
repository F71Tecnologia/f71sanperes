<?php

include('../conn.php');
include('../classes/global.php');
include('../wfunction.php');

$clt = ($_REQUEST['id_clt']) ? $_REQUEST['id_clt'] : '';
$id_reg = $_REQUEST['id_reg'];
$id_user = $_COOKIE['logado'];

$data = date('d/M/Y');

$qry_consulta = mysql_query("SELECT * FROM rh_doc_status WHERE id_clt='$clt' AND tipo='35'");
$cont_consulta = mysql_num_rows($qry_consulta);

if ($cont_consulta == 0 )
{
    $insert_doc = mysql_query("INSERT INTO rh_doc_status (tipo, id_clt, data, id_user) VALUES ('35', '$clt', NOW(), '$_COOKIE[logado]')");

}

$result_clt = mysql_query("
SELECT A.*,date_format(A.data_entrada, '%d/%m/%Y')as data_entrada,B.nome AS nome_escolaridade, date_format(date_add(A.data_entrada, interval 2 year), '%d/%m/%Y') AS dt_entradaY
FROM rh_clt AS A
LEFT JOIN escolaridade as B on (A.escolaridade = B.id)
where id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);

$result_horarios = mysql_query("SELECT nome, horas_semanais FROM rh_horarios where id_horario = '{$row_clt['rh_horario']}' LIMIT 1");
$row_horarios = mysql_fetch_array($result_horarios);

if($row_clt['prazoexp'] == 1){
    $prazoExp = '30';
    $prazoPro = 60;
    $prazoExpExt = 'trinta';
    $prazoProrrogado = 'um preriodo de 60 (sessenta) dias';
}else if($row_clt['prazoexp'] == 2 OR $row_clt['prazoexp'] == ''){
    $prazoExp = '45';
    $prazoPro = 45;
    $prazoExpExt = 'quarenta e cinco';
    $prazoProrrogado = 'igual período';
}else if($row_clt['prazoexp'] == 3){
    $prazoExp = '60';
    $prazoPro = 30;
    $prazoExpExt = 'sessenta';
    $prazoProrrogado = 'um preriodo de 30 (trinta) dias';
}

//PEGA O CURSO DO CONTRATADO
$sql_transf = mysql_fetch_assoc(mysql_query("SELECT id_curso_de FROM rh_transferencias WHERE id_clt = $row_clt[id_clt] ORDER BY data_proc ASC LIMIT 1"));
if(!empty($sql_transf['id_curso_de'])){
    $idCurso = $sql_transf['id_curso_de'];
}else{
    $idCurso = $row_clt['id_curso'];
}
//$idCurso = $row_clt['id_curso'];

$result_curso = mysql_query("Select * from  curso where id_curso = '$idCurso'");
$row_curso = mysql_fetch_array($result_curso);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'");
$row_reg = mysql_fetch_array($result_reg);

$result_proj = mysql_query("SELECT * FROM projeto WHERE id_projeto='$row_clt[id_projeto]' ");
$row_proj = mysql_fetch_assoc($result_proj);

$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);


$result_empresa = mysql_query("Select * from  rhempresa where id_empresa = '$row_clt[rh_vinculo]'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

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

$data_entrada = explode("/",$row_clt['data_entrada']);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
$data_final = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp - 1), $ano_entrada));
$data_final_ano = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + (365 - 1), $ano_entrada));
$data_incial_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp), $ano_entrada));
$data_final_pro = date("d/m/Y", mktime (0, 0, 0, $mes_entrada  , $dia_entrada + ($prazoExp+$prazoPro-1), $ano_entrada));
$data_final1 = explode("/",$data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime (0, 0, 0, $mes_final  , $dia_final + ($prazoExp - 1), $ano_final));

$id_curso = $row_curso['id_curso'];

$qrsalario = "select * from rh_salario where id_curso = '$id_curso' order by data desc limit 1";
$rssalario = mysql_query($qrsalario);
$salarioAntigo = mysql_fetch_array($rssalario);
$salario1 = $salarioAntigo['salario_novo'];
$totalHistorico = mysql_num_rows($rssalario);

if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1'){
    $salario1 = $salarioAntigo['salario_novo'];
} else {
    $salario1 = $salarioAntigo['salario_antigo'];
}
if($totalHistorico == 0){
    $salario1 = $row_curso['salario'];
}

if($_COOKIE['logado'] != 87 and $row_clt['status'] == 10){
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '3' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if($num_row_verifica == "0"){
        mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('3','$clt','$data_cad', '$user_cad')");
    }else{
        mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '3'");
    }
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}


?>
<html>
<head>
    <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
    <title>Documento de Cadastramento do NIS</title>
    <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
    <link href="../resources/css/style-print.css" rel="stylesheet">
    <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="../resources/js/print.js" type="text/javascript"></script>
    <style>
        table, td, tr{
            border:none!important;
        }
        #content {
            width: 800px;
            margin: 0 auto;
        }

        .logo {
            float: left;
            margin: 5px 0 0 5px;
        }

        #title {
            float: left;
            padding: 20px 0 0 25px;
        }

        hr {
            border: none !important;
        //    border-top: 1px solid #333;
        }

        .fleft {
            float: left;
        }

        .fright {
            float: right;
        }

        .box {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 10px;
        }

        .txright {
            text-align: right;
        }

        .txcenter {
            text-align: center;
        }

        .legenda {
            font-size: 10px;
            padding: 0;
            margin: 0;
            float: left;
        }

        .clear {
            clear: both;
            padding: 0;
            margin: 0;
            line-height: 16px;
        }

        .txleft {
            text-align: left;
        }

        table {
            width: 100%;
            border-left: 1px solid #333;
            margin-bottom: 10px !important;
        }

        td {
            padding: 1px 5px;
            border-right: 1px solid #333;
            border-bottom: 1px solid #333;
        }

        td.bl {
            border-left: none !important;
        }

        tr.bf td {
            border-bottom: none !important;
        }

        tr.bt td {
            border-top: none !important;
        }

        p {
            font-size: 13px;
            padding: 5px;
        }

        table thead tr th {
            font-size: 14px;
            font-weight: bold;
        }

        table tbody tr td {
            padding: 1px 5px;
            font-size: 13px !important;
        }

        table.grid {
            border-top: 1px solid #333;
            border-left: 1px solid #333;
        }

        table.grid tr td {
            border-bottom: 1px solid #333;
            border-right: 1px solid #333;
        }

        table.grid tr th {
            border-bottom: 1px solid #333;
            background: #F0F0F0;
        }

        table.grid tr th:last-child {
            border-right: 1px solid #333;
        }

        #sigilo {
            width: 200px !important;
            float: right;
        }

        span {
            color: red;
        }

        .dependentes {
            width:100%;
            /*border: none;*/
            border: 1px solid #000
        }

        .dependentes th {
            /*border: none;*/
            text-align: center;
            border: 1px solid #000;
        }

        .dependentes td {
            text-align: center;
            /*border: none;*/
            border: 1px solid #000
        }
    </style>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <div class="text-center">
            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i>
                Imprimir
            </button>
            <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
        </div>
    </div>
</nav>
<div class="pagina" style="text-justify: inter-word!important;">
    <TABLE BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="">
        <TR>
            <TD colspan="3" align="center" valign="middle" class="titulo_documento">
                <img src="../imagens/logomaster1.gif">
                <hr style="margin-top: 1px; margin-bottom: 11px;" />
            </TD>
        </tr>
        <tr VALIGN=TOP>
            <TD colspan="3" class="titulo_documento" STYLE="font-weight: bold;">
                CONTRATO DE APRENDIZAGEM
                <br/><br/>
            </TD>
        </tr>
    </TABLE>

    <div align="right" style="font-size: 12px;"><strong>CONTRATO DE APRENDIZAGEM QUE <br>
            ENTRE SI FAZEM, DE UM LADO, O <?=$row_master['razao'];?>, <br>
            E DE OUTRO LADO, NA QUALIDADE <br>
            DE APRENDIZ, O <?php echo $row_clt['nome']; ?>. <br></strong>
    </div>

    <TABLE WIDTH=794 BORDER=0 align="center" CELLPADDING=0 CELLSPACING=0 bgcolor="#FFFFFF" class="">

        <TR VALIGN=TOP>
            <TD WIDTH=677 HEIGHT=876><DIV>

                </DIV><DIV ALIGN=LEFT class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 10pt;">
                    <div align="justify" class="textoprint">
                        <p>
                        <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">


                                A <?=$row_master['razao'];?>, com sede  <?= $row_proj['endereco'] ?>, Bairro <?= $row_proj['bairro'] ?>, Cidade <?= $row_proj['cidade'] ?>,
                                Estado <?= $row_proj['estado'] ?>, inscrito no CNPJ sob o nº <?= $row_empresa['cnpj'] ?>, neste ato Representada por
                                <?= $row_empresa['responsavel'] ?>,<?= $row_empresa['nacionalidade'] ?>,
                                (nome do preposto, nacionalidade, estado civil, profissão, Residência, nº da carteira de identidade e inscrição do CPF nº<?= $row_empresa['cpf'] ?>
                                doravante denominada  CONTRATANTE, e de  outro lado, na qualidade de  EMPREGADO APRENDIZ,
                                <?php echo $row_clt['nome']; ?>, <?php echo $row_clt['nacionalidade']; ?>, <?php echo $row_clt['civil']; ?>, <?php echo $row_clt['nome_escolaridade']; ?>,
                                <?php echo $row_clt['endereco']; ?>, nº da carteira de identidade <?php echo $row_clt['cpf']; ?> ou <?php echo $row_clt['campo1']; ?>
                                <!--(nome do Jovem, nacionalidade, estado civil, estudante, residência, nº da carteira de identidade  __ ou ____   CTPS-->
                                doravante CONTRATADO, firmam o presente contrato mediante as seguintes cláusulas e condições:


                                <br><br>

                                <h3><strong>CLÁUSULA PRIMEIRA - DO OBJETO</strong></h3>
                                <strong>O CONTRATADO</strong>, na qualidade de empregado aprendiz se compromete a freqüentar o curso de <strong>APRENDIZAGEM COMERCIAL EM SERVIÇOS ADMINISTRATIVOS</strong>
                                ministrado pelo Serviço Nacional de Aprendizagem Comercial - Departamento Regional do Estado do Rio de Janeiro - SENAC/RJ,
                                de acordo comercial com o programa previamente estabelecido pela Entidade.
                                Parágrafo único - <strong>O CONTRATADO</strong> sujeitar-se-á quanto aos aspectos técnicos da Aprendizagem às normas e metodologias adotadas pelo SENAC/RJ.

                                <br><br>

                                <strong>Parágrafo único</strong> -  <strong>O CONTRATADO</strong> sujeitar-se-á quanto aos aspectos técnicos da Aprendizagem às normas e metodologias adotadas pelo SENAC/RJ.

                                <br><br>

                                <h3><strong>CLÁUSULA SEGUNDA - DAS OBRIGAÇÕES DO CONTRATANTE</strong></h3>

                                I - O <strong>CONTRATANTE</strong> a seu exclusivo critério, obriga-se a fornecer ao <strong>CONTRATADO</strong>
                                todos os meios materiais, para que possa haver um perfeito desenvolvimento do objeto do presente <strong>CONTRATO</strong>,
                                e via de conseqüência, da formação técnico-profissional metódica do aprendiz, compatível com o seu desenvolvimento físico, moral e psicológico.

                                <br><br>

                                II - Formalizar por escrito o contrato de aprendizagem, determinando o início e o final de sua vigência, por ser um contrato de trabalho especial.
                                III - Conceder as férias ao empregado aprendiz coincidindo com o período de férias escolares, vedado o parcelamento.
                                IV - O CONTRATANTE deverá oferecer condições de segurança e saúde, conforme o disposto no art. 405 da CLT, e nas Normas Regulamentadoras, aprovadas pela Portaria nº 3.214/78.

                                <h3><strong>CLÁUSULA TERCEIRA - DAS OBRIGAÇÕES DO CONTRATADO</strong></h3>

                                I - Cumprir fielmente as obrigações assumidas na cláusula primeira;
                                II - Executar com zelo e diligência as tarefas necessárias à formação objeto do contrato
                                III - Cumprir as metas do aprendizado estabelecidas pelo SENAC/RJ.
                                IV - Estar matriculado e freqüentar escola de ensino regular, caso o aprendiz não tenha concluído o ensino médio.

                                <br><br>

                                <h3><strong>CLÁUSULA QUARTA - DAS CONDIÇÕES DE VALIDADE DO CONTRATO</strong></h3>

                                I - registro e anotação na Carteira de Trabalho e Previdência Social.
                                II - matrícula e freqüência do aprendiz à escola regular, caso não tenha concluído o ensino médio.
                                III - inscrição do aprendiz em curso de aprendizagem desenvolvido sob a orientação do SENAC/RJ nos moldes do art. 430 da CLT.
                                IV - definição de programa de aprendizagem, desenvolvido através de atividades teóricas e práticas, contendo os objetivos do curso, conteúdos a serem ministrados e a carga horária.
                                V - Possuir o empregado aprendiz entre 18 e 24 anos de idade.

                                <br><br>

                                <h3><strong>CLÁUSULA QUINTA - DA REMUNERAÇÃO</strong></h3>

                                O CONTRATADO pelas atividades, objeto do Contrato, receberá a título de salário, a quantia de R$ <?=$row_curso['salario']?>   (<?php echo valor_extenso(number_format($row_curso['salario'],2,',',''));  ?>) mensal.
                                § 1º - A alíquota do depósito ao Fundo de Garantia por Tempo de Serviço ? FGTS -será de 2 %(dois por cento) da remuneração paga ou devida ao empregado aprendiz, de acordo com o § 7° do art. 15 da Lei nº 8.036/90.
                                § 2º - O aprendiz terá direito ao salário mínimo-hora, observando-se, caso exista, piso estadual e condição mais favorável. A existência de convenção ou o acordo coletivo da categoria poderá garantir ao aprendiz salário maior que o mínimo (art. 428, § 2º, da CLT e art. 17, parágrafo único do Decreto nº 5.598/05).
                                § 3º - Além das horas destinadas às atividades práticas, serão computadas no salário também as horas destinadas às aulas teóricas, o descanso semanal remunerado e feriados.

                                <br><br>

                                <h3><strong>CLÁUSULA SEXTA - DA JORNADA</strong></h3>

                                A duração da jornada do empregado aprendiz será de <?= $row_horarios['nome'] ?>  horas diárias, perfazendo <?= $row_horarios['horas_semanais'] ?>  horas semanais,
                                nelas incluídas as atividades teóricas e/ou práticas, vedadas a prorrogação e a compensação da jornada, inclusive as hipóteses previstas nos incisos I e II do art. 413 da CLT.

                                <br><br>

                                <h3><strong>CLÁUSULA SÉTIMA - DA VIGÊNCIA (2 anos)<strong></h3>

                                Este contrato terá sua vigência no período de <?php echo $row_clt['data_entrada'] . ' à ' . $row_clt['dt_entradaY']?>  .



                                <h3><strong>CLÁUSULA OITAVA - DA RESCISÃO</strong></h3>

                                O presente contrato extinguir-se-á no seu termo ou quando o aprendiz completar 24 (vinte e quatro) anos.  E, nas hipóteses de rescisão antecipada do contrato de aprendizagem não se aplicam os artigos 479 e 480 da CLT, que tratam da indenização, por metade, da remuneração a que teria direito até o termo do contrato.
                                § 1º - São hipóteses de rescisão antecipada do contrato de aprendizagem: 1 - desempenho insuficiente ou inadaptação do aprendiz; 2 - falta disciplinar grave nos termos do art. 482 da CLT; 3 - ausência injustificada à escola regular que implique perda do ano letivo; 4 - e, a pedido do aprendiz.
                                § 2º - Na incidência da primeira hipótese caberá a entidade executora da aprendizagem prestar a declaração e na ocorrência da terceira hipótese a declaração deverá ser expedida pelo estabelecimento de ensino regular.


                                <h3><strong>CLÁUSULA NONA - DO FORO</strong></h3>

                                As partes elegem o foro da Comarca da cidade do Rio de Janeiro para dirimir quaisquer conflitos oriundos do presente contrato, com expressa renúncia de qualquer outro, por mais privilegiado que seja.
                                E assim, por se acharem justas e contratadas, as partes firmam o presente instrumento em 03 (três) vias de igual teor e forma, na presença das testemunhas abaixo nomeadas, para que produza seus efeitos jurídicos e legais.


                        <p>&nbsp;</p>
                        <p><FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
                            </font></p>
                        <FONT STYLE=" font-family: Arial; color: #000000; font-size: 8pt;">
                            <p align="center">
                                <?php list($dia_entrada,$mes_entrada,$ano_entrada) = explode('/',$row_clt['data_entrada']); print "$row_proj[nome], $dia_entrada de ".$meses_pt[(int)$mes_entrada]." de $ano_entrada."; //print "$row_reg[regiao], $dia de $mes de $ano."; ?></p>
                        </font>
                        <table width="100%" border="0" >
                            <tr>
                                <td align="center">____________________________________</td>
                                <td align="center">____________________________________</td>
                            </tr>
                            <tr class="linha">
                                <!--            <td align="center" class="linha"><strong><?= $row_master['razao'];
                                ?></strong></td>-->
                                <td align="center" class="linha"><strong>INSTITUTO LAGOS RIO</strong></td> <!-- &nbsp; row_master['razao'] = Substituido Direto Por Instituto Lagos Rio-->
                                <td align="center" class="linha"><strong><?php echo $row_clt['nome']; ?></strong></td>
                            </tr>
                            <tr>
                                <td align="center">&nbsp;</td>
                                <td align="center">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center">&nbsp;</td>
                                <td align="center">&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="center">&nbsp;</td>
                                <td class="linha" style="font-size: 10px;"><hr style="border-top: 1px solid #333;width: 262px; margin: 0px auto 5px;"><p style="text-align: center"><strong>Responsável pelo Jovem Aprendiz  Menor de 18 anos<br></strong></p></td>
                            </tr>
                            <tr>
                                <td align="center"><strong>____________________________________</strong></td>
                                <td align="center"><strong>____________________________________</strong></td>
                            </tr>
                            <tr>
                                <td class="linha text-center" ><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                                <td class="linha text-center"><strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Testemunha<br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
                            </tr>
                        </table>
                        <p align="center" class="linha">&nbsp;</p>
                    </DIV>
                    <p>&nbsp;</p>
                    <p><span class="linha"><BR>
		  </span></p>
                </div>
</DIV><DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
</DIV>
<DIV ALIGN=LEFT  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"></DIV><DIV ALIGN=CENTER class="linha"  STYLE=" font-family: Arial; color: #000000; font-size: 8pt;"><BR>
</DIV></TD>
</TABLE>
</div>
</body>
</html>

