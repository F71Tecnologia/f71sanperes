<?php
// Verificando se o usu�rio est� logado
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../../login.php">Logar</a>';
    exit;
}

// Incluindo Arquivos
require('../../conn.php');
include('../../funcoes.php');
include "../../wfunction.php";

$usuario = carregaUsuario();
if (!empty($_REQUEST['data_xls'])) {
    $dados = utf8_encode($_REQUEST['data_xls']);

    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=folha-analitica.xls");

    echo "\xEF\xBB\xBF";
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>Folha Anal�tica</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
}

// Buscando a Folha
if(isset($_REQUEST['enc'])){
    list($regiao, $folha) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));
} else {
    $regiao = $_REQUEST['id_regiao'];
    $folha = $_REQUEST['id_folha'];
}


// Consulta da Data
$data = mysql_result(mysql_query("SELECT data_proc FROM rh_folha WHERE id_folha = '$folha'"), 0);

// Se a Folha � nova...
if ($data > date('2010-06-09')) {

    // Incluindo Arquivos
    include('../../classes/calculos.php');
    include('../../classes/abreviacao.php');
    include('../../classes/formato_valor.php');
    include('../../classes/formato_data.php');
    include('../../classes/valor_proporcional.php');
    include('../../classes/FolhaClass.php');
    include('../../classes/regiao.php');

    $Regi = new regiao();
    $Trab = new proporcional();
    $objFolha = new Folha();
    // Consulta da Folha
    $qr_folha = mysql_query("SELECT *, date_format(data_inicio, '%d/%m/%Y') AS data_inicio_br,
									  date_format(data_fim, '%d/%m/%Y') AS data_fim_br,
									  date_format(data_proc, '%d/%m/%Y') AS data_proc_br 
							     FROM rh_folha WHERE id_folha = '$folha' AND status = '3'");
    $row_folha = mysql_fetch_array($qr_folha);
    $data_inicio = $row_folha['data_inicio'];
    $data_fim = $row_folha['data_fim'];
    $ano = $row_folha['ano'];
    $mes = $row_folha['mes'];
    $mes_int = (int) $mes;

    // Consulta do Usu�rio que gerou a Folha
    $qr_usuario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$row_folha[user]'");

    // Redefinindo Vari�veis de D�cimo Terceiro
    if ($row_folha['terceiro'] != 1) {
        $decimo_terceiro = NULL;
    } else {
        $decimo_terceiro = 1;
        $tipo_terceiro = $row_folha['tipo_terceiro'];
    }

    //empresa
    $qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$row_folha[regiao]' AND id_projeto = '$row_folha[projeto]]'");
    $row_empresa = mysql_fetch_assoc($qr_empresa);

    // Consulta da Regi�o
    $qr_regiao = mysql_query("SELECT id_regiao, regiao FROM regioes WHERE id_regiao = '$row_folha[regiao]'");
    $regiao = mysql_result($qr_regiao, 0, 0);

    // Consulta do Projeto
    $qr_projeto = mysql_query("SELECT id_projeto, nome, id_master FROM projeto WHERE id_projeto = '$row_folha[projeto]'");
    $projeto = mysql_result($qr_projeto, 0, 0);

    
    
    // Consulta dos Participantes da Folha
    $qr_participantes = mysql_query("SELECT folha.*,C.id_curso,C.nome as funcao FROM 
	(SELECT A.*, 
	  (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_inicio' ORDER BY id_transferencia ASC LIMIT 1) AS de,
	  (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_inicio' ORDER BY id_transferencia DESC LIMIT 1) AS para,
	                                           
	
	B.id_clt as id_curso2
	FROM rh_folha_proc AS A
	LEFT JOIN rh_clt AS B ON A.id_clt = B.id_clt
	WHERE A.id_folha = '$folha' AND A.status IN(3,4)
	ORDER BY A.nome ASC) as folha
LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso)))
      ");
    
    if($_GET['debug'] == 666){
        echo "<br>SELECT folha.*,C.id_curso,C.nome as funcao FROM 
	(SELECT A.*, 
	(SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '$data_inicio' ORDER BY id_transferencia ASC LIMIT 1) AS de,
	(SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '$data_inicio' ORDER BY id_transferencia DESC LIMIT 1) AS para,
	                                           
	
	B.id_clt as id_curso2
	FROM rh_folha_proc AS A
	LEFT JOIN rh_clt AS B ON A.id_clt = B.id_clt
	WHERE A.id_folha = '$folha' AND A.status IN(3,4)
	ORDER BY A.nome ASC) as folha
        LEFT JOIN curso AS C ON (IF(folha.para IS NOT NULL,C.id_curso=folha.para, IF(folha.de IS NOT NULL,C.id_curso=folha.de,C.id_curso=folha.id_curso)))
      <br>";
    }
    
    $total_participantes = mysql_num_rows($qr_participantes);

    // Definindo M�s da Folha
    $meses = array('Erro', 'Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

    if (!empty($decimo_terceiro)) {
        switch ($tipo_terceiro) {
            case 1:
                $mes_folha = '13� Primeira parcela';
                break;
            case 2:
                $mes_folha = '13� Segunda parcela';
                break;
            case 3:
                $mes_folha = '13� Integral';
                break;
        }
    } else {
        $mes_folha = "$meses[$mes_int] / $ano";
    }

    // Percentual RAT
//    if ($ano >= 2011) {
//        $percentual_rat = '0.01';
//    } else {
//        $percentual_rat = '0.03';
//    }
    // Percentual RAT
    if ($ano >= 2011 AND $ano <= 2014) {
        $percentual_rat = '0.01';
    }elseif($ano >= 2015){
        if($row_folha['projeto'] == 3305){ // VIAM�O
            $percentual_rat = '0.0129';
        } else if($row_folha['projeto'] == 3353 || $row_folha['projeto'] == 3331){ // BEBEDOURO
            $percentual_rat = '0.01';
        } else if($row_folha['projeto'] == 3309){ // ADM
            $percentual_rat = '0.005';
        } else if($row_folha['projeto'] == 3304){ 
            $percentual_rat = '0.0121';
        } else if($row_folha['projeto'] == 3320){ // NITEROI
            $percentual_rat = '0.0107';
        } else if($row_folha['projeto'] == 3316){ // MARECHAL
            $percentual_rat = '0.01';
        } else if($row_folha['projeto'] == 3318){ // RICARDO
            $percentual_rat = '0.0107';
        } else if($row_folha['projeto'] == 3315){ // BANGU
            $percentual_rat = '0.005';
        } else if($row_folha['projeto'] == 3303){ // SG I
            $percentual_rat = '0.005';
        } else if($row_folha['projeto'] == 3313){ // F71
            $percentual_rat = '0.005';
        } else if($row_folha['projeto'] == 3338){ // CAMPOS
            $percentual_rat = '0.01';
        } else if($row_folha['projeto'] == 3317){ // REALENDO
            $percentual_rat = '0.0105';
        } else if($row_folha['projeto'] == 3319){ // SAO PEDRO
            $percentual_rat = '0.00533';
        } else {
            $percentual_rat = '0.01';
        }
    } else {
        $percentual_rat = '0.03';
    }

    // Encriptografando Links
    $link_voltar = 'folha.php?enc=' . str_replace('+', '--', encrypt("$regiao&1")) . '&tela=1';
    $link_lista_banco = 'ver_lista_banco.php?enc=' . str_replace('+', '--', $_REQUEST['enc']) . '&tela=1';
    ?>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title>:: Intranet :: Folha Finalizada de CLT (<?= $folha ?>)</title>
            <link href="sintetica/folha.css" rel="stylesheet" type="text/css">
            <link href="../../favicon.ico" rel="shortcut icon">
            <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
            <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
            <script src="../../resources/js/main.js" type="text/javascript"></script>
            <script src="../../resources/js/tooltip.js" type="text/javascript"></script>
            <!--<script src="../../js/jquery-2.0.1.min.js" type="text/javascript"></script>-->
            <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
            <script src="../../js/global.js" type="text/javascript"></script>
            <!--<script src="./../js/jquery.btechco.excelexport.js"></script>-->
            <!--<script src="./../js/jquery.base64.js"></script>-->
            <script type="text/javascript">
                hs.graphicsDir = '../../images-box/graphics/';
                hs.outlineType = 'rounded-white';

                $(function() {
                    
                    $('#imprimirPdf').click(function() {
                            window.print();
                            return false;
                    });
                    
//                    $("#btnExport").click(function () {
//                        $("#tblExport").btechco_excelexport({
//                            containerid: "tblExport"
//                           , datatype: $datatype.Table
//                           , filename: 'sample'
//                        });
//                    }); 
                        
                    $("#btnExport").click(function () {
                        $("#relatorio_exp img:last-child").remove();

                        var html = $("#relatorio_exp").html();

                        $("#data_xls").val(html);
                        $("#form1").submit();
                    });

                    $('#filtrar').click(function() {
                        var id_funcao = $('#funcoes').val();
                        var nome = $('#pesquisa').val().toLowerCase();
                        if (id_funcao != '') {
                            $('.funcao').each(function(index) {
                                if ($(this).val() == id_funcao) {
                                    $(this).parent().parent().show();
                                    if (nome != '') {
                                        if ($(this).next().val().toLowerCase().search(nome) >= 0) {
                                            $(this).next().parent().parent().show();
                                        } else {
                                            $(this).next().parent().parent().hide();
                                        }
                                    }
                                } else {
                                    $(this).parent().parent().hide();
                                }
                            })
                        }
                        if (nome != '' && id_funcao == '') {
                            $('.nome').each(function(index) {
                                if ($(this).val().toLowerCase().search(nome) >= 0) {
                                    $(this).parent().parent().show();
                                } else {
                                    $(this).parent().parent().hide();
                                }
                            })
                        }
                    })

                    $('#mostrar_todos').click(function() {
                        $('.funcao').each(function(index) {
                            $(this).parent().parent().show();
                        })
                    })

                    /****************MOSTRA TODOS *****************************/
                    $(".legenda .mostrar_todos").click(function() {
                        history.go(0);
                    });

                    /*****************ESCONDE OS TOTAIS************************/
                    $(".legenda").click(function() {
                        $(".totais").hide();
                    });

                    /****************MOSTRA TODOS QUE ENTRARAM NA FOLHA********/
                    $(".legenda .entrada").click(function() {
                        $(".destaque").each(function() {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".entrada").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_entrada").show();
                        $("#estatisticas").hide();
                    });

                    /****************MOSTRA TODOS COM EVENTOS LAN�ADOS*********/
                    $(".legenda .evento").click(function() {
                        $(".destaque").each(function() {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".evento").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_linceca").show();
                        $("#estatisticas").hide();
                    });

                    /****************MOSTRA TODOS COM FALTAS*********/
                    $(".legenda .faltas").click(function() {
                        $(".destaque").each(function() {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".faltas").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_faltas").show();
                        $("#estatisticas").hide();
                    });

                    /****************MOSTRA TODOS COM FALTAS*********/
                    $(".legenda .ferias").click(function() {
                        $(".destaque").each(function() {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".ferias").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_ferias").show();
                        $("#estatisticas").hide();
                    });

                    /****************MOSTRA TODOS EM RESCISAO*********/
                    $(".legenda .rescisao").click(function() {
                        $(".destaque").each(function() {
                            $(this).find("span").parents("tr").show();
                            $(this).find("span").not(".rescisao").parents("tr").hide();
                        });
                        $(".esconde_geral").hide();
                        $(".totais_rescisao").show();
                        $("#estatisticas").hide();
                    });
                });

            </script>
            <style type="text/css">
                .highslide-html-content { width:600px; padding:0px; }
                .rendimentos{
                    background-color:  #033;	
                }
                #tabela tr{
                    font-size:10px;
                }	
                #folha .sem_borda td {
                    border:0;
                }
                .mostrar_todos{
                    background: #000;
                }
                .nota{
                    cursor: pointer;
                }
                .totais_entrada, .totais_linceca, .totais_faltas, .totais_ferias, .totais_rescisao{
                    display: none;
                    font-weight: bold;
                    text-align: center;
                }
                .esconde { display: none; }
                
            </style>
            <style type="text/css" media="print">
    
    body{
        font-size: 8px;
    }
    table{
        border-collapse: collapse;
       border: 1px solid   #ccccff;
        font-size: 8px;
    }
   table tr{
        
       border: 1px solid   #ccccff; font-size: 8px;
       
    }  
    
     table td{
        
       border: 1px solid   #ccccff; font-size: 8px;
    }  
    
    table.sem_borda{
        border: 0;
    }
table.sem_borda tr{
        border: 0;
    }
table.sem_borda td{
        border: 0;
        text-align: left;
    }

    .esconde { display: none; }
    
    @media print {
        .printDoc {
            display:none!important;
        }           
        .printDocBlock {
            display:block;
        }           
    }
    
</style>
        </head>
        <body>
            <div id="corpo">

                <table cellspacing="4" cellpadding="0" id="topo">
                    <tr height="30">
                        <td width="15%" rowspan="3" valign="middle" align="center">
                            <img src="../../imagens/logomaster<?= mysql_result($qr_projeto, 0, 2) ?>.gif" width="110" height="79">
                        </td>
                        <td  style="font-size:12px;">
                            <b><?= mysql_result($qr_projeto, 0, 1) . ' (' . $mes_folha . ')' ?></b>               
                        </td>
                        <td colspan="2">  <b>CNPJ: </b><?php echo $row_empresa['cnpj']; ?></td>
                    </tr>
                    <tr>
                        <td width="35%"><b>Data da Folha:</b> <?= $row_folha['data_inicio_br'] . ' &agrave; ' . $row_folha['data_fim_br'] ?></td>
                        <td width="30%"><b>Regi�o:</b> <?= $regiao . ' - ' . mysql_result($qr_regiao, 0, 1) ?></td>
                        <td width="20%"><b>Participantes:</b> <?= $total_participantes ?></td>
                    </tr>
                    <tr>
                        <td><b>Data de Processamento:</b> <?= $row_folha['data_proc_br'] ?></td>
                        <td><b>Gerado por:</b> <?= @abreviacao(mysql_result($qr_usuario, 0), 2) ?></td>
                        <td><b>Folha:</b> <?= $folha ?></td>
                    </tr>
                </table>
                <div style="margin-botton: 15px;margin-top: 10px"><p style="text-align: center" class="printDocBlock esconde"><?= 'Gerado em '.date('d/m/Y').' �s '.date('h:i:s').' por '.$usuario['nome'].' - F71 Sistemas Web' ?></p></div>
                <table cellpadding="0" cellspacing="1" id="folha">
                    <tr>
                        <td class="printDoc" colspan="2">
                            <a href="<?= $link_voltar ?>" class="voltar">Voltar</a>
                        </td>
                        <td class="printDoc" colspan="8">
                            <div style="float:right;">
                                <div class="legenda esconde"><div class="nota mostrar_todos"></div>Todos</div>
                                <div class="legenda"><div class="nota entrada"></div>Admiss�o</div>
                                <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                                <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                                <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                                <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                            </div>
                        </td>
                    </tr>

                </table>

                <div class="esconde">
                    <!--<?= ($_COOKIE['logado'] == 204) ? "SELECT C.nome, C.id_curso FROM rh_folha_proc as A
                                                INNER JOIN rh_clt As B
                                                ON B.id_clt = A.id_clt 
                                                INNER JOIN curso as C
                                                ON C.id_curso = B.id_curso
                                                WHERE A.id_folha = '$folha' AND A.status = 2
                                                GROUP BY C.id_curso" : ""; ?>-->
                    <strong>FUN��O:</strong>
                    <select name="funcoes" id="funcoes">
                        <option value="">Selecione...</option>
                        <?php
                        $cursos = mysql_fetch_assoc(mysql_query("select GROUP_CONCAT(rh_folha_proc.id_curso) AS cursos from rh_folha_proc left join curso on curso.id_curso = rh_folha_proc.id_curso where id_folha=1443"));
                        $qr_funcao = mysql_query("select * from curso where id_curso IN ({$cursos['cursos']})");
                        while ($row_funcao = mysql_fetch_assoc($qr_funcao)) {

                            echo '<option value="' . $row_funcao['id_curso'] . '">' . $row_funcao['nome'] . '</option>';
                        }
                        ?> 
                    </select>
                    <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome, Matricula, CPF" value="">
                    <input type="button" name="filtrar" id="filtrar" value="Filtrar"/>
                    <input type="button" name="mostrar_todos" id="mostrar_todos" value="Mostrar todos"/>
                </div>                

                <!--<p style="text-align: right;" clss="esconde"><input type="button" onclick="tableToExcel('tabela', 'Folha Analitica')" value="Exportar para Excel" class="esconde"></p>-->
                                                                                                                       <p class="printDoc" style="text-align: right;"><button class="" id="imprimirPdf">Gerar PDF</button>
<button type="button" value="Exportar" class="btn btn-success" id="btnExport"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button></p>
                 <form id="form1" action="" method="post">
                        <input type="hidden" id="data_xls" name="data_xls" value=''>
                 </form>
                 <div id="relatorio_exp">
                <table cellpadding="0" cellspacing="1" id="tabela" width="100%">       
                    <tr class="secao">
                        <td width="4%">COD</td>
                        <td width="25%" align="left" style="padding-left:5px;">NOME</td>
                        <td width="4%">FUN��O</td>
                        <!--<td width="4%"><?php if (isset($decimo_terceiro)) {
                        echo 'MESES';
                    } else {
                        echo 'DIAS';
                    } ?></td>-->
                        <td width="8%">BASE</td>
                        <td width="8%">VALOR/DIA</td>   
                        <td width="30%">RENDIMENTOS</td>
                        <td width="30%">DESCONTOS</td>

                        <td width="10%">L&Iacute;QUIDO</td>
                    </tr>
                            <?php while ($row_participante = mysql_fetch_array($qr_participantes)) { 
                            
                            $objFolha->getFichaFinanceira($row_participante['id_clt'], $row_folha['ano'], $row_folha['mes']);
                            $movimentosRendDesc = $objFolha->getDadosFicha();
                           
                    ?>
                    
                        <tr class="linha_<?php if ($linha++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
                            <td>
                                <?= $row_participante['id_clt'] ?>
                                <input type="hidden" name="id_funcao" class="funcao" value="<?php echo $row_participante['id_curso'];?>"/>
                                <input type="hidden" name="nome" class="nome" value="<?php echo $row_participante['nome'];?> <?php echo $row_participante['matricula'];?> <?php echo $row_participante['cpf'];?> <?php echo str_replace('-','',str_replace('.','',$row_participante['cpf']));?>"/>
                            </td>
                            <td  align="left">

        <?php
        $contracheque = str_replace('+', '--', encrypt("$regiao&$row_participante[id_clt]&$folha"));
        $data_entrada = @mysql_result(mysql_query("SELECT data_entrada FROM rh_clt WHERE id_clt = '" . $row_participante['id_clt'] . "'"), 0);
        $licensas = array('20', '30', '50', '51', '52', '80', '90', '100', '110');
        $ferias = array('40');
        $rescisao = array('60', '61', '62', '63', '64', '65', '81', '101');
        $faltas = mysql_num_rows(mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_clt = '$row_participante[id_clt]' AND id_mov IN(62,232) AND id_movimento IN(" . $row_folha['ids_movimentos_estatisticas'] . ")"));
        ?>

                                <a class="printDoc" href="../contracheque/geracontra_4.php?enc=<?= $contracheque ?>" target="_blank" class="participante" title="Gerar contracheque de <?= $row_participante['nome'] ?>">
                                    <span class="
                                    <?php
                                    if ($data_entrada > $data_inicio) {
                                        echo 'entrada';
                                    } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                        echo 'evento';
                                    } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                        echo 'ferias';
                                    } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                        echo 'rescisao';
                                    } elseif (!empty($faltas)) {
                                        echo 'faltas';
                                    } else {
                                        echo 'normal';
                                    }
                                    ?>
                                          "><?php echo abreviacao($row_participante['nome'], 4, 1); ?></span>
                                    <img src="sintetica/seta_<?php
                                         if ($seta++ % 2 == 0) {
                                             echo 'um';
                                         } else {
                                             echo 'dois';
                                         }
                                         ?>.gif">
                                </a>
                                <span class="printDocBlock esconde <?php
                                    if ($data_entrada > $data_inicio) {
                                        echo 'entrada';
                                    } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                        echo 'evento';
                                    } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                        echo 'ferias';
                                    } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                        echo 'rescisao';
                                    } elseif (!empty($faltas)) {
                                        echo 'faltas';
                                    } else {
                                        echo 'normal';
                                    }
                                    ?>
                                          "><?php echo abreviacao($row_participante['nome'], 4, 1); ?></span>
                            </td>

                            <td>
                                <?php
                                //CARREGANDO A CBO
                                if (isset($row_participante['id_curso']) && !empty($row_participante['id_curso'])) {
                                    $qrCurso = mysql_query("SELECT A.nome,B.id_cbo,B.cod,B.nome as nomecbo FROM curso AS A
                                                                    LEFT JOIN rh_cbo AS B ON (B.id_cbo = A.cbo_codigo)
                                                                    WHERE A.id_curso = {$row_participante['id_curso']} LIMIT 1");
                                    $rcurso = mysql_fetch_assoc($qrCurso);
                                    echo $rcurso['nome'];
                                } else {
                                    $dtApuracao = $row_participante['ano'] . "-" . $row_participante['mes'] . "-01";
                                    $qrCurso = mysql_query("SELECT C.id_curso,C.nome,D.cod,D.nome as nomecbo FROM (
                                                                SELECT A.id_clt,B.id_curso,B.rh_horario,
                                                                   (SELECT id_curso_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtApuracao}' ORDER BY id_transferencia ASC LIMIT 1) AS de,
                                                                   (SELECT id_curso_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtApuracao}' ORDER BY id_transferencia DESC LIMIT 1) AS para,
                                                                   (SELECT id_horario_de FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc >= '{$dtApuracao}' ORDER BY id_transferencia ASC LIMIT 1) AS horario_de,
                                                                   (SELECT id_horario_para FROM rh_transferencias WHERE id_clt=A.id_clt AND id_curso_de <> id_curso_para AND data_proc <= '{$dtApuracao}' ORDER BY id_transferencia DESC LIMIT 1) AS horario_para  
                                                                FROM rh_folha_proc AS A
                                                                LEFT JOIN rh_clt AS B ON (A.id_clt=B.id_clt)
                                                                WHERE id_folha IN ({$folha}) AND A.status = 3 
                                                                AND A.id_clt = {$row_participante['id_clt']}) as temp

                                                                LEFT JOIN curso AS C ON (IF(temp.para IS NOT NULL,C.id_curso=temp.para, IF(temp.de IS NOT NULL,C.id_curso=temp.de,C.id_curso=temp.id_curso)))
                                                                LEFT JOIN rh_horarios AS E ON (IF(temp.horario_para IS NOT NULL,E.id_horario=temp.horario_para, IF(temp.de IS NOT NULL,E.id_horario=temp.horario_de,E.id_horario=temp.rh_horario)))
                                                                LEFT JOIN rh_cbo AS D ON (D.id_cbo=C.cbo_codigo)");
                                    $rcurso = mysql_fetch_assoc($qrCurso);
                                    echo $rcurso['nome'];
                                }
                                ?>
                            </td>

        <!--<td><?php
                    if ($row_participante['valor_dt'] != '0.00') {
                        echo $row_participante['meses'];
                    } else {
                        echo $row_participante['dias_trab'];
                    }
                                ?></td>-->
                            <td><?= formato_real($row_participante['sallimpo_real'] + $row_participante['valor_dt']) ?></td>
                            <td>
        <?php
        if (!empty($row_participante['dias_trab'])) {

            echo 'R$ ' . formato_real($row_participante['sallimpo_real'] / $row_participante['dias_trab']) . ' x ' . $row_participante['dias_trab'] . ' dias.';
        }
        ?>
                            </td>

                            <td> 
                                <!----------------------- RENDIMENTOS-------------------------------->

                                <table width="100%"  class="sem_borda">                                    
                                    <?php
                                    if ($row_participante['valor_rescisao'] != 0) {
                                        /*
                                          echo '  <tr class="font" height="35">
                                          <td align="left" width="50%">RESCIS�O</td>
                                          <td align="left" width="50%">'.number_format($row_participante['valor_rescisao'],2,',','.').'
                                          </td></tr>';

                                          $total_rendimentos += $row_participante['valor_rescisao'];
                                         * 
                                         */
                                    } else {

                                        $ids_movimentos_array = explode(',', $row_participante['ids_movimentos']);

                                        //PEGA OS MOVIMENTOS DE CREDITO (RENDIMENTOS) DA TABELA RH_FOLHA_PROC(campos a5029, a5037, a4007,a5022)  E RH_MOVIMENTOS_CLT(apenas o campo '0001')
                                        $movimentos_rendimentos = array('a5029' => 5029, 'a5037' => 5037, 'a4007' => 4007, 'a5022' => 5022, 'a6005' => 6005);
                                        
                                        
                                        /***
                                         * FEITO POR: SINESIO LUIZ
                                         * FEITO EM: 15/19/2016
                                         * 
                                         * FOR�ANDO PARA APARECER O VALOR DE FERIAS QUE VEM 
                                         * DO METODO GETFICHAFINANCEIRA E N�O O QUE FOI GRAVADO 
                                         * NO FOLHA_PROC
                                         */
                                        $row_participante['a5037'] = $movimentosRendDesc[5037][$row_folha['mes']];
                                        
                                        
                                        foreach ($movimentos_rendimentos as $indice => $valor) {

                                            if ($row_participante[$indice] != 0) {

                                                $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$valor' ORDER BY cod ASC") or die(mysql_error());
                                                $row_rendimentos = mysql_fetch_assoc($qr_rendimentos);
                                                ?>
                                                <tr class="font" height="35">
                                                    <td align="left" width="33%"><?= $row_rendimentos['descicao']; ?></td>
                                                    <td align="center" width="33%">
                                                    <?php
                                                    if($valor == '5022'){
                                                        if(!empty($row_participante['a50222'])){echo $row_participante['a50222'];}
                                                    }else{
                                                        if(!empty($movimentosRendDesc["$valor"][ref])){echo $movimentosRendDesc["$valor"][ref];}
                                                    }
                                                    ?>
                                                    </td>
                                                    <td align="left" width="33%"><?php
                                                        echo 'R$ ' . formato_real($row_participante[$indice]);
                                                        $total_rendimentos += $row_participante[$indice];

                                                        if ($data_entrada > $data_inicio) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA ADMISS�O
                                                            $array_totais["entrada"]["rendimento"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA LICENCA
                                                            $array_totais["linceca"]["rendimento"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["ferias"]["rendimento"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["rescisao"]["rendimento"] += $row_participante[$indice];
                                                        } elseif (!empty($faltas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["faltas"]["rendimento"] += $row_participante[$indice];
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
            
                                        
                                        if (!empty($row_participante['ids_movimentos'])) {
                                            $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) AND tipo_movimento  = 'CREDITO' AND id_clt = '$row_participante[id_clt]'") or die(mysql_error());
                                            while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                                ?>
                                                <tr class="font" height="35">
                                                    <td align="left" width="33%"><?= $row_rendimentos['nome_movimento']; ?></td>
                                                    <td align="center" width="33%">
                                                    <?php
                                                    if($row_rendimentos[cod_movimento] == '5022'){
                                                        if(!empty($row_participante['a50222'])){echo $row_participante['a50222'];}
                                                    }else{
                                                        if(!empty($movimentosRendDesc["$row_rendimentos[cod_movimento]"][ref])){echo $movimentosRendDesc["$row_rendimentos[cod_movimento]"][ref];}
                                                    } ?>
                                                    </td>
                                                    <td align="left" width="33%">
                                                        <?php
                                                        echo 'R$ ' . formato_real($row_rendimentos['valor_movimento']);
                                                        $total_rendimentos += $row_rendimentos['valor_movimento'];

                                                        if ($data_entrada > $data_inicio) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA ADMISS�O
                                                            $array_totais["entrada"]["rendimento"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA LICENCA
                                                            $array_totais["linceca"]["rendimento"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["ferias"]["rendimento"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["rescisao"]["rendimento"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (!empty($faltas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["faltas"]["rendimento"] += $row_rendimentos['valor_movimento'];
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            endwhile;
                                        }
                                    }
                                    ?>
                                </table>   


                            </td>
                            <td>


                                <!----------------------- DESCONTOS-------------------------------->
                                <table width="100%"  class="sem_borda">
                                    <?php
                                    ///RESCIS�O
                                    if ($row_participante['valor_pago_rescisao'] != 0) {
                                        /*
                                          echo '  <tr class="font" height="35">
                                          <td align="left" width="50%">VALOR PAGO NA RESCIS�O</td>
                                          <td align="left" width="50%">'.number_format($row_participante['valor_pago_rescisao'],2,',','.').'
                                          </td></tr>';

                                          if($row_participante['inss_rescisao'] != 0){
                                          echo '  <tr class="font" height="35">
                                          <td align="left" width="50%">INSS RESCIS�O</td>
                                          <td align="left" width="50%">'.number_format($row_participante['inss_rescisao'],2,',','.').'
                                          </td></tr>';
                                          $total_descontos += $row_participante['inss_rescisao'];
                                          }

                                          if($row_participante['ir_rescisao'] != 0){
                                          echo '  <tr class="font" height="35">
                                          <td align="left" width="50%">IRRF RESCIS�O</td>
                                          <td align="left" width="50%">'.number_format($row_participante['ir_rescisao'],2,',','.').'
                                          </td></tr>';
                                          $total_descontos += $row_participante['ir_rescisao'];
                                          }

                                          if($_COOKIE['logado'] == 87){

                                          echo '  <tr class="font" height="35">
                                          <td align="left" width="50%">SALDO NEGATIVO</td>
                                          <td align="left" width="50%">'.number_format($row_participante['valor_rescisao'] - ($row_participante['valor_pago_rescisao'] +$row_participante['ir_rescisao'] +$row_participante['inss_rescisao']),2,',','.').'
                                          </td></tr>';
                                          }
                                          $total_descontos += $row_participante['valor_pago_rescisao'];
                                         */
                                    } else {


                                        $ids_movimentos_array = explode(',', $row_participante['ids_movimentos']);

                                        //PEGA OS MOVIMENTOS DE CREDITO (RENDIMENTOS) DA TABELA RH_FOLHA_PROC(campos a5029, a5037, a4007,a5022)  E RH_MOVIMENTOS_CLT(apenas o campo '0001')
                                        $movimentos_descontos = array('a5037' => 5037,
                                            'a4007' => 4007,
                                            'a5020' => 5020,
                                            'a5031' => 5031,
                                            'a5035' => 5035,
                                            'a4007' => 4007,
                                            'a5021' => 5021,
                                            'a5030' => 5030,
                                            'a5036' => 5036,
                                            'a4007' => 4007,
                                            'a5019' => 5019,
                                            'a7001' => 7001,
                                            'a8003' => 8003);
                                        
                                        /***
                                         * FEITO POR: SINESIO LUIZ
                                         * FEITO EM: 15/19/2016
                                         * 
                                         * FOR�ANDO PARA APARECER O VALOR DE FERIAS QUE VEM 
                                         * DO METODO GETFICHAFINANCEIRA E N�O O QUE FOI GRAVADO 
                                         * NO FOLHA_PROC
                                         */
                                        $row_participante['a5037'] = $movimentosRendDesc[50255][$row_folha['mes']];
                                        

                                        foreach ($movimentos_descontos as $indice => $valor) {

                                            if ($row_participante[$indice] != 0) {

                                                $qr_descontos = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$valor' ORDER BY cod ASC") or die(mysql_error());
                                                $row_descontos = mysql_fetch_assoc($qr_descontos);
                                                ?>
                                                <tr class="font" height="35">
                                                    <td align="left" width="33%"><?= $row_descontos['descicao']; ?></td>
                                                    <td align="center" width="33%"><?= $movimentosRendDesc["$valor"][ref]; ?></td>
                                                    <td align="left" width="33%"><?php
                                                        echo 'R$ ' . formato_real($row_participante[$indice]);
                                                        $total_descontos+= $row_participante[$indice];

                                                        if ($data_entrada > $data_inicio) {
                                                            //CALCULA TOTAL PARA ADMISS�O
                                                            $array_totais["entrada"]["desconto"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA ADMISS�O
                                                            $array_totais["linceca"]["desconto"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["ferias"]["desconto"] += $row_participante[$indice];
                                                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["rescisao"]["desconto"] += $row_participante[$indice];
                                                        } elseif (!empty($faltas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["faltas"]["desconto"] += $row_participante[$indice];
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }

                                        if (!empty($row_participante['ids_movimentos'])) {


                                            $qr_rendimentos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE id_movimento IN($row_folha[ids_movimentos_estatisticas]) 
                                                                                                        AND id_clt = '$row_participante[id_clt]'
													AND (tipo_movimento  = 'DEBITO' 
													OR tipo_movimento  = 'DESCONTO')") or die(mysql_error());


                                            while ($row_rendimentos = mysql_fetch_assoc($qr_rendimentos)):
                                                ?>
                                                <tr class="font" height="35">
                                                    <td align="left" width="33%"><?= $row_rendimentos['nome_movimento']; ?></td>
                                                    <td align="center" width="33%"><?= $movimentosRendDesc["$row_rendimentos[cod_movimento]"][ref]; ?></td>
                                                    <td align="left" width="33%"><?php
                                                        echo 'R$ ' . formato_real($row_rendimentos['valor_movimento']);
                                                        $total_descontos += $row_rendimentos['valor_movimento'];
                                                        if ($data_entrada > $data_inicio) {
                                                            //CALCULA TOTAL PARA ADMISS�O
                                                            $array_totais["entrada"]["desconto"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $licensas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA ADMISS�O
                                                            $array_totais["linceca"]["desconto"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["ferias"]["desconto"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["rescisao"]["desconto"] += $row_rendimentos['valor_movimento'];
                                                        } elseif (!empty($faltas)) {
                                                            //CALCULA TOTAL DE RENDIMENTO PARA F�RIAS
                                                            $array_totais["faltas"]["desconto"] += $row_rendimentos['valor_movimento'];
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            endwhile;
                                            
//                                            if($_COOKIE['logado'] == 179){
//                                                echo "<pre>";
//                                                    print_r($row_participante);
//                                                echo "</pre>";
//                                            }
                                        }
                                    }
                                    ?>
                                </table>      
                            </td>
                            <td width="10%"><?= formato_real($row_participante['salliquido']) ?></td>
                        </tr>

                        <?php
                        $totalizador_salario_maternidade += $row_participante['a6005'];
                        $ddir += $row_participante['a5049'];
                        if ($data_entrada > $data_inicio) {
                            //CALCULA TOTAL PARA ADMISS�O
                            $array_totais["entrada"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["entrada"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $licensas)) {

                            //CALCULA O TOTAL LICEN�A 
                            $array_totais["linceca"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["linceca"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $ferias) and $row_participante['dias_trab'] != 30) {

                            //CALCULA O TOTAL F�RIASa
                            $array_totais["ferias"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["ferias"]["liquido"] += $row_participante['salliquido'];
                        } elseif (in_array($row_participante['status_clt'], $rescisao)) {

                            //CALCULA O TOTAL RECISAO
                            $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["rescisao"]["liquido"] += $row_participante['salliquido'];
                        } elseif (!empty($faltas)) {

                            //CALCULA O TOTAL FALTAS
                            $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                            $array_totais["faltas"]["liquido"] += $row_participante['salliquido'];
                        }
                    } // Fim do Loop de Participantes 
                    ?>

                    <tr class="totais">
                        <td colspan="2">
    <?php if ($total_participantes > 10) { ?>
                                <a href="#corpo" class="printDoc ancora">Subir ao topo</a>
                            <?php } ?></td>
                        <td>TOTAIS:</td>
                        <td><?= formato_real($row_folha['total_limpo'] + $row_folha['valor_dt']) ?></td>
                        <td></td>
                        <td><?= formato_real($total_rendimentos) ?></td>
                        <td><?= formato_real($total_descontos) ?></td>
                        <td><?= formato_real($row_folha['total_liqui']) ?></td>
                    </tr>


    <?php foreach ($array_totais as $key => $total) { ?>

                        <tr class="totais_<?php echo $key; ?> esconde_geral">
                            <td class="printDoc" colspan="2">
        <?php if ($total_participantes > 10) { ?>
                                    <a href="#corpo" class="printDoc ancora">Subir ao topo</a>
                                <?php } ?>
                            </td>
                            <td>TOTAIS:</td>
                            <!-- ********************** TOTAIS DE ENTRADAS ***************************** -->
                            <td><?= formato_real($total["base"]) ?></td>
                            <td></td>
                            <td><?= formato_real($total["rendimento"]) ?></td>
                            <td><?= formato_real($total["desconto"]) ?></td>
                            <td><?= formato_real($total["liquido"]) ?></td>

                        </tr>

    <?php } ?>

                </table>
                     <div style="margin-top: 15px"><p style="text-align: center" class="printDocBlock esconde"><?= 'Gerado em '.date('d/m/Y').' �s '.date('h:i:s').' por '.$usuario['nome'].' - F71 Sistemas Web' ?></p></div>
                 </div>

                <div id="estatisticas">
    <?php
    // Resumo por Movimento
    $movimentos_codigo = array('0001',
        '5029',
        '5037', '5037',
        '4007', '4007',
        '5020', '5031', '4007',
        '5021', '5030', '5036', '4007',
        '5022', '5019',
        '7001', '8003',
        '6005');
    $movimentos_nome = array('SAL&Aacute;RIO',
        'D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO',
        'F&Eacute;RIAS', 'VALOR PAGO NAS F&Eacute;RIAS',
        'RESCIS&Atilde;O', 'VALOR PAGO NA RESCIS&Atilde;O',
        'INSS', 'INSS SOBRE D&Eacute;CIMO TERCEIRO', 'INSS SOBRE RESCIS&Atilde;O',
        'IRRF', 'IRRF SOBRE D&Eacute;CIMO TERCEIRO', 'IRRF SOBRE F&Eacute;RIAS', 'IRRF SOBRE RESCIS&Atilde;O',
        'SAL&Aacute;RIO FAMILIA', 'CONTRIBUI&Ccedil;&Atilde;O SINDICAL',
        'DESCONTO VALE TRANSPORTE', 'DESCONTO VALE REFEI&Ccedil;&Atilde;O',
        'SAL&Aacute;RIO MATERNIDADE');
    $movimentos_tipo = array('CREDITO',
        'CREDITO',
        'CREDITO', 'DEBITO',
        'CREDITO', 'DEBITO',
        'DEBITO', 'DEBITO', 'DEBITO',
        'DEBITO', 'DEBITO', 'DEBITO', 'DEBITO',
        'CREDITO', 'DEBITO',
        'DEBITO', 'DEBITO',
        'CREDITO');
    $movimentos_valor = array($row_folha['total_limpo'],
        $row_folha['valor_dt'],
        $row_folha['valor_ferias'], $row_folha['valor_pago_ferias'],
        $row_folha['valor_rescisao'], $row_folha['valor_pago_rescisao'],
        $row_folha['total_inss'], $row_folha['inss_dt'], $row_folha['inss_rescisao'],
        $row_folha['total_irrf'], $row_folha['ir_dt'], $row_folha['ir_ferias'], $row_folha['ir_rescisao'],
        $row_folha['total_familia'], $row_folha['total_sindical'],
        $row_folha['total_vt'], $row_folha['total_vr'],
        $totalizador_salario_maternidade);

    // Adicionando Mais Movimentos
    if (!empty($row_folha['ids_movimentos_estatisticas'])) {

        $chave = '18';
        $ids_movimentos_estatisticas = $row_folha['ids_movimentos_estatisticas'];
        settype($movimentos_listados, 'array');

        $qr_movimentos = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
			   						 		 FROM `rh_movimentos_clt`
											 WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                             AND id_mov != 62
											 GROUP BY id_mov");
        while ($movimento = mysql_fetch_array($qr_movimentos)) {
            $chave++;
            $movimentos_listados[] = $movimento['id_mov'];
            $movimentos_codigo[] = $movimento['cod_movimento'];
            $movimentos_nome[] = $movimento['nome_movimento'];
            $movimentos_tipo[] = $movimento['tipo_movimento'];

            $movimentos_valor[$chave] += $movimento['total'];
        }

        unset($chave);

        // Organizado as Arrays pelo C�digo
        array_multisort($movimentos_codigo, $movimentos_nome, $movimentos_tipo, $movimentos_valor);
    }
    ?>

<!--                    <div id="resumo">

                        -MODO ANTIGO 17/10/2014
                        <table cellspacing="1">
                            <tr>
                                <td colspan="4" class="secao_pai">Resumo por Movimento</td>
                            </tr>
                            <tr class="secao">
                                <td width="9%">COD</td>
                                <td width="53%" class="movimento">MOVIMENTO</td>
                                <td width="19%">RENDIMENTO</td>
                                <td width="19%">DESCONTO</td>
                            </tr>
    <?php
    foreach ($movimentos_valor as $chave => $valor) {
        if (!empty($valor) and $valor != 0.00) {
            ?>

                                    <tr class="linha_<?php
                        if ($linha3++ % 2 == 0) {
                            echo 'um';
                        } else {
                            echo 'dois';
                        }
            ?>">
                                        <td><?= $movimentos_codigo[$chave] ?></td>
                                        <td class="movimento"><?= $movimentos_nome[$chave] ?></td>

                                        <?php
                                        if ($movimentos_tipo[$chave] == 'CREDITO') {
                                            $movimentos_credito += $valor;
                                            ?>
                                            <td><?= formato_real($valor) ?></td>
                                            <td>&nbsp;</td>   
                                            <?php
                                        } elseif ($movimentos_tipo[$chave] == 'DEBITO' or $movimentos_tipo[$chave] == 'DESCONTO') {
                                            if ($movimentos_codigo[$chave] != '8000') {
                                                $movimentos_debito += $valor;
                                            }
                                            ?>     
                                            <td>&nbsp;</td>
                                            <td<?php if ($movimentos_codigo[$chave] == '8000') { ?> style="color:#999;"<?php } ?>><?= formato_real($valor) ?></td>       
            <?php } ?>

                                    </tr>

                                <?php }
                            }
                            ?>
                            <tr class="totais">
                                <td colspan="2" align="right">TOTAIS:</td>
                                <td><?= formato_real($movimentos_credito) ?></td>
                                <td><?= formato_real($movimentos_debito) ?></td>
                            </tr>
                            <tr class="totais">
                                <td colspan="2" align="right">L&Iacute;QUIDO:</td>
                                <td><?= formato_real($movimentos_credito - $movimentos_debito) ?></td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>



                     <table cellspacing="1" widht="50%">
                        <tr>
                          <td colspan="5" class="secao_pai">Resumo por Movimento 22</td>
                      </tr>
                      <tr class="secao">
                          <td>COD</td>
                          <td  class="movimento">MOVIMENTO</td>
                          <td  class="movimento"></td>
                          <td >RENDIMENTO</td>
                          <td >DESCONTO</td>
                      </tr>
                                    <?php

                                   $movimentos =  $objFolha->getResumoPorMovimento($folha);
                                    foreach($movimentos as $cod =>$valor){

                                      if($valor['tipo'] == 'CREDITO'){

                                          $rendimento = $valor['valor'];
                                          $desconto ='';
                                          $total_credito += $valor['valor'];
                                      }  else {
                                          $rendimento ='';
                                          $desconto = $valor['valor'];
                                          $total_debito += $valor['valor'];
                                      }
                                      if($valor['qnt'] != 0){
                                          $frequencia = $valor['qnt'];
                                      }elseif(!empty($valor['qnt_horas']) and $valor['qnt_horas'] != '00:00:00'){
                                           $frequencia = $valor['qnt_horas'];
                                      } else {
                                           $frequencia = $valor['percentual'];
                                      }

                                    $class =  ($linha++ % 2 == 0)? 'linha_um': 'linha_dois';


                                       echo '<tr class="'.$class.'">';
                                                echo '<td class="excelExp">'.$cod.'</td>';
                                                echo '<td class="excelExp" align="left" >'.$valor['nome'].'</td>';
                                                echo '<td class="excelExp" >'.$frequencia.'</td>';
                                                echo '<td class="excelExp">'.formato_real($rendimento).'</td>';
                                                echo '<td class="excelExp">'.formato_real($desconto).'</td>';
                                       echo '</tr>';

                                   }                        
                                    ?>
                        <tr class="totais">
                           <td colspan="2" align="right">TOTAIS:</td>
                           <td><?= formato_real($total_credito) ?></td>
                           <td><?= formato_real($total_debito) ?></td>
                       </tr>
                       <tr class="totais">
                           <td colspan="2" align="right">L&Iacute;QUIDO:</td>
                           <td><?= formato_real($total_credito - $total_debito) ?></td>
                           <td>&nbsp;</td>
                        </tr>
                    </table>
                    </div>-->

                    <?php
                    // Totalizadores
                    $base_fgts_sem_rescisao = "SELECT SUM(A.base_inss) AS total_fgts
                        FROM rh_folha_proc AS A
                        WHERE A.id_folha = '{$folha}' AND A.status_clt NOT IN(61,63,64,66)
                        GROUP BY A.id_folha";
                    $sql_fgts_sem_rescisao =  mysql_query($base_fgts_sem_rescisao);

                    $valor_fgts_sem_rescisao = mysql_result($sql_fgts_sem_rescisao, 0);
                    $totalizadores_nome = array('L&Iacute;QUIDO', 'BASE DE INSS', 'INSS', 'INSS (EMPRESA)', 'INSS (RAT)', 'INSS (TERCEIROS)'/* , 'INSS (RECOLHER)' */, 'BASE DE IRRF', 'IRRF', 'DDIR', 'BASE DE FGTS', 'BASE DE FGTS DE F&Eacute;RIAS', 'BASE DE FGTS TOTAL',
                        'FGTS'/* , 'FGTS' */
                    );
                    $totalizadores_valor = array(
                        $row_folha['total_liqui'],
                        $row_folha['base_inss'],
                        $row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'],
                        ($row_folha['base_inss'] * 0.2),
                        ($row_folha['base_inss'] * $percentual_rat),
                        ($row_folha['base_inss'] * 0.058),
                        //	 ((($row_folha['base_inss'] * 0.2) +
                        // ($row_folha['base_inss'] * $percentual_rat) +
                        //($row_folha['base_inss'] * 0.058) +
                        //($row_folha['total_inss'] + $row_folha['inss_dt'] + $row_folha['inss_rescisao'] + $row_folha['inss_ferias'])) - 
                        //$row_folha['total_familia']),
                        $row_folha['base_irrf'],
                        $row_folha['total_irrf'] + $row_folha['ir_dt'] + $row_folha['ir_rescisao'] + $row_folha['ir_ferias'],
                        $ddir,
                        $valor_fgts_sem_rescisao,
                        $row_folha['base_fgts_ferias'],
                        $valor_fgts_sem_rescisao + $row_folha['base_fgts_ferias'],
                        ($valor_fgts_sem_rescisao + $row_folha['base_fgts_ferias'])*0.08
                        

                            /* $row_folha['total_fgts'] + $row_folha['fgts_dt'] + $row_folha['fgts_rescisao'] + $row_folha['fgts_ferias'] */                            );
                    ?>

<!--                    <div id="totalizadores">
                        <table cellspacing="1">
                            <tr>
                                <td class="secao_pai" colspan="2">Totalizadores</td>
                            </tr>
                            <tr class="linha_um">
                                <td class="secao">PARTICIPANTES:</td>
                                <td class="valor"><?= $total_participantes ?></td>
                            </tr>
    <?php foreach ($totalizadores_valor as $chave => $valor) { ?>
                                <tr class="linha_<?php if ($linha2++ % 2 == 0) {
            echo 'dois';
        } else {
            echo 'um';
        } ?>">
                                    <td class="secao"><?= $totalizadores_nome[$chave] ?>:</td>
                                    <td class="valor"><?= formato_real($valor) ?></td>
                                </tr>
                    <?php } ?>
                        </table>
                    </div>-->
                    <?php
                    if(!empty($ids_movimentos_estatisticas)){
                    $qr_movimentos_faltas = mysql_query("SELECT cod_movimento, nome_movimento, SUM(valor_movimento) as total ,tipo_movimento, id_mov 
                                                                                                             FROM `rh_movimentos_clt`                                                                                         
                                                                                                             WHERE id_movimento IN($ids_movimentos_estatisticas) 
                                                                                                              AND id_mov  = 62
                                                                                                             GROUP BY id_mov") or die(mysql_error());
                    if (mysql_num_rows($qr_movimentos_faltas) != 0) {
                        ?>
                        <table cellspacing="1" width="300">
                            <tr>
                                <td colspan="2" class="secao_pai">Total de faltas</td>
                            </tr>
                            <tr class="secao">
                                <td width="9%">COD</td>
                                <td width="9%">TOTAL</td>
                            </tr>

        <?php
        while ($row_mov2 = mysql_fetch_assoc($qr_movimentos_faltas)):
            ?>  
                                <tr class="linha_dois">
                                    <td  align="right"  style="font-size:12px;"><?php echo $row_mov2['cod_movimento']; ?></td>
                                    <td  align="right" style="font-size:12px;" > <?php echo number_format($row_mov2['total'], 2, ',', '.'); ?></td>                
                                </tr>

                            <?php
                        endwhile;
                        ?>
                        </table>
                        <p style="font-style:italic; text-align: left; font-size: 10px; color: #ff6666; margin-left: 70px;">*As faltas s�o abatidas no sal�rio base.</p>


    <?php }
                    }?>


<!--                    <div id="resumo" style="width:100%; clear:both; margin-top:20px;">
                        <table cellspacing="1">
                            <tr>
                                <td class="secao_pai" colspan="5">Lista de Bancos</td>
                            </tr>

                            <?php
                            // Verificando os bancos envolvidos na folha de pagamento
                            $qr_bancos = mysql_query("SELECT DISTINCT(id_banco) FROM rh_folha_proc WHERE id_banco != '9999' AND id_banco != '0' AND id_folha = '$folha' AND status IN(3,4)");
                            while ($row_bancos = mysql_fetch_array($qr_bancos)) {

                                $numero_banco++;
                                $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_bancos[id_banco]'");
                                $row_banco = mysql_fetch_array($qr_banco);
                                ?>

                                <tr class="linha_<?php
                            if ($linha4++ % 2 == 0) {
                                echo 'um';
                            } else {
                                echo 'dois';
                            }
                                ?>">
                                    <td style="width:7%;"><img src="../../imagens/bancos/<?= $row_banco['id_nacional'] ?>.jpg" width="25" height="25"></td>
                                    <td style="width:35%; text-align:left; padding-left:5px;"><?= $row_banco['nome'] ?></td>		  

                                    <?php
                                    $total_finalizados = mysql_num_rows(mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' AND status = '4' AND id_banco = '$row_banco[id_banco]'"));

                                    if (!empty($total_finalizados)) {
                                        ?>

                                        <td>&nbsp;</td>
                                        <td><a href="finalizados.php?regiao=<?= $regiao ?>&folha=<?= $folha ?>&projeto=<?= $projeto ?>&banco=<?= $row_banco['id_banco'] ?>">FINALIZADO</a></td>
                                        <td align="center"><?= $total_finalizados ?> Participantes</td>

                                                    <?php
                                                } else {

                                                    $qr_banco = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_banco = '$row_bancos[0]' AND folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Dep�sito em Conta Corrente' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto'");
                                                    $total_banco = mysql_num_rows($qr_banco);
                                                    ?>

                                        <td style="width:30%; text-align:center;">
                                            <form id="form1" name="form1" method="post" action="folha_banco.php?enc=<?= str_replace('+', '--', encrypt("$regiao&$folha")) ?>">
                                                <select name="banco">
            <?php
            $qr_bancos_associados = mysql_query("SELECT * FROM bancos WHERE id_nacional = '$row_banco[id_nacional]' AND status_reg = '1' AND id_regiao != ''");
            while ($row_banco_associado = mysql_fetch_assoc($qr_bancos_associados)) {
                ?>
                                                        <option value="<?= $row_banco_associado['id_banco'] ?>" <?php
                if ($row_banco_associado['id_banco'] == $row_banco['id_banco']) {
                    echo 'selected';
                }
                ?>>
                <?php echo $row_banco_associado['id_banco'] . ' - ' . $row_banco_associado['nome'] . ' (' . @mysql_result(mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$row_banco_associado[id_regiao]'"), 0) . ')'; ?>
                                                        </option>
                                    <?php } ?>
                                                </select>
                                                <label id="data_pagamento<?= $numero_banco ?>" style="display:none;"> 
                                                    <input name="data" id="data[]" type="text" size="10" onKeyUp="mascara_data(this)" maxlength="10">
                                                    <input name="enviar" id="enviar[]" type="submit" value="Gerar">
                                                </label>
                                                <input type="hidden" name="banco_participante" value="<?= $row_banco['id_banco'] ?>">
                                            </form>
                                        </td>
                                        <td style="width:8%;"><a style="cursor:pointer;"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcion�rios por Banco" onClick="document.all.data_pagamento<?= $numero_banco ?>.style.display = (document.all.data_pagamento<?= $numero_banco ?>.style.display == 'none') ? '' : 'none';"></a></td>
                                        <td style="width:20%; text-align:center; padding-right:5px;"><?= $total_banco ?> Participantes</td>
                                    </tr>

                                    <?php
                                    }
                                }

                                $qr_cheque = mysql_query("SELECT * FROM rh_folha_proc folha INNER JOIN tipopg tipo ON folha.tipo_pg = tipo.id_tipopg WHERE folha.id_folha = '$folha' AND folha.status = '3' AND tipo.tipopg = 'Cheque' AND tipo.id_regiao = '$regiao' AND tipo.id_projeto = '$projeto' AND tipo.campo1 = '2'");
                                $total_cheque = mysql_num_rows($qr_cheque);
                                $linkcheque = str_replace('+', '--', encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]"));
                                ?>

                            <tr class="linha_<?php
                                if ($linha4++ % 2 == 0) {
                                    echo 'um';
                                } else {
                                    echo 'dois';
                                }
                                ?>">
                                <td style="width:7%;"><img src="../../imagens/bancos/cheque.jpg" width="25" height="25" border="0"></td>
                                <td style="width:35%; text-align:left; padding-left:5px;">Cheque</td>
                                <td style="width:30%;">&nbsp;</td>
                                <td style="width:8%;"><a href="ver_cheque.php?enc=<?= $linkcheque ?>"><img src="imagens/ver_banc.png" border="0" alt="Visualizar Funcion�rios por Cheque"></a></td>
                                <td style="width:20%; text-align:center; padding-right:5px;"><?= $total_cheque ?> Participantes</td>
                            </tr>
                            <tr>
                                <td colspan="5"><a href="<?= $link_lista_banco ?>" style="font-weight:bold; padding-left:5px;">Ver Lista por Banco</a></td>
                            </tr>
                        </table>
                    </div>-->

                </div>
                <div class="clear"></div>

            </div>
        </body>
    </html>




    <?php
    // se � folha antiga...
} else {
    ?>




    <?php
    if (!empty($_REQUEST['agencia'])) {

        $ag = $_REQUEST['agencia'];
        $cc = $_REQUEST['conta'];

        $clt = $_REQUEST['clt'];
        $tipo_conta = $_REQUEST['radio_tipo_conta'];

        $RE_clt = mysql_query("SELECT * FROM rh_folha_proc where id_folha_proc = '$clt' and status = 3 and tipo_pg = '0'") or die(mysql_error());
        $RowCLT = mysql_fetch_array($RE_clt);

        mysql_query("UPDATE rh_clt SET agencia='$ag', conta='$cc', tipo_conta='$tipo_conta' WHERE id_clt = '$RowCLT[id_clt]'") or die(mysql_error());
        mysql_query("UPDATE rh_folha_proc SET agencia='$ag', conta='$cc' WHERE id_folha_proc = '$clt'") or die(mysql_error());
    }

    include "../../classes/regiao.php";

    $Regi = new regiao();

    //RECEBENDO A VARIAVEL CRIPTOGRAFADA
    $enc = $_REQUEST['enc'];
    $enc = str_replace("--", "+", $enc);
    $link = decrypt($enc);

    $decript = explode("&", $link);

    $regiao = $decript[0];
    $folha = $decript[1];

    //RECEBENDO A VARIAVEL CRIPTOGRAFADA

    $id_user = $_COOKIE['logado'];


    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    $result_master = mysql_query("SELECT * FROM master where id_master = '$row_user[id_master]'");
    $row_master = mysql_fetch_array($result_master);

    $result_folha = mysql_query("SELECT *,date_format(data_proc, '%d/%m/%Y')as data_proc2,date_format(data_inicio, '%d/%m/%Y')as data_inicio,date_format(data_fim, '%d/%m/%Y')as data_fim FROM rh_folha where id_folha = '$folha'");
    $row_folha = mysql_fetch_array($result_folha);

    $result_projeto = mysql_query("SELECT * FROM projeto where id_projeto = '$row_folha[projeto]'");
    $row_projeto = mysql_fetch_array($result_projeto);

    $meses = array('Erro', 'Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
    $mesInt = (int) $row_folha['mes'];
    $mes_da_folha = $meses[$mesInt];

    $titulo = "Folha: Projeto $row_projeto[nome] m�s de $mes_da_folha";

    $ano = date("Y");
    $mes = date("m");
    $dia = date("d");

    $data = date("d/m/Y");

    $data_menor14 = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano - 14));
    $data_menor21 = date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano - 21));

    $result_codigos = mysql_query("SELECT distinct(cod) FROM rh_movimentos WHERE cod != '0001' ORDER BY cod");

    while ($row_codigos = mysql_fetch_array($result_codigos)) {
        $ar_codigos[] = $row_codigos['0'];
    }

    $RE_TipoDepo = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '1'");
    $row_TipoDepo = mysql_fetch_array($RE_TipoDepo);

    $RE_TIpoCheq = mysql_query("SELECT id_tipopg,tipopg FROM tipopg WHERE id_projeto = '$row_folha[projeto]' and campo1 = '2'");
    $row_TIpoCheq = mysql_fetch_array($RE_TIpoCheq);
    ?>
    <html>
        <head>
            <script type="text/javascript" src="../../js/prototype.js"></script>
            <script type="text/javascript" src="../../js/scriptaculous.js?load=effects,builder"></script>
            <script type="text/javascript" src="../../js/lightbox.js"></script>
            <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
            <link rel="stylesheet" href="../../js/lightbox.css" type="text/css" media="screen"/>
            <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />

            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <title><?= $titulo ?></title>

            <script type="text/javascript">
                                    hs.graphicsDir = '../../images-box/graphics/';
                                    hs.outlineType = 'rounded-white';
            </script>
            <style type="text/css">
                a:visited {font-size: 10px; color: #F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}
                a:link{font-size: 10px; color:#F00; text-decoration: none; font-weight: bold; font-family: Verdana, Arial, Helvetica, sans-serif;}


            </style>
            <link href="../../net1.css" rel="stylesheet" type="text/css">
        </head>

        <body>

            <table width="95%" border="0" align="center">
                <tr>
                    <td align="center" valign="middle" bgcolor="#FFFFFF"><div style="font-size:9px; text-align:left; color:#E2E2E2;"><b>ID:
    <?php
    echo $folha . ", regi�o: ";
    $Regi->MostraRegiao($row_folha['regiao']);
    echo $Regi->regiao;
    ?>
                            </b></div>
                        <table width="90%" border="0" align="center">
                            <tr>
                                <td width="100%" height="81" align="center" valign="middle" bgcolor="#003300" class="title"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="16%" align="center" valign="middle" bgcolor="#E2E2E2"><span class="style1"><img src="../../imagens/logomaster<?= $row_user['id_master'] ?>.gif" alt="" width="110" height="79" align="absmiddle" ></span></td>
                                            <td width="62%" bgcolor="#E2E2E2"><span class="Texto10">
    <?= $row_master['razao'] ?>
                                                    <br>
                                                    CNPJ : <?= $row_master['cnpj'] ?>
                                                </span><span class="style1"><br>
                                                </span></td>
                                            <td width="22%" bgcolor="#E2E2E2">
                                                <span class="Texto10">
                                                    Processamento: 
    <?= $row_folha['data_proc2'] ?>
                                                    <br>
                                                    Inicio da folha: 
    <?= $row_folha['data_inicio'] ?>
                                                    <br />
                                                    Fim da folha: 
    <?= $row_folha['data_fim'] ?>
                                                </span></td>
                                        </tr>
                                    </table></td>
                            </tr>
                        </table>
                        <br />
                        <span class="titulo_opcoes">Folha de Pagamento - <?= $mes_da_folha ?> / <?= $row_folha['ano'] ?> </span><br />
                        <br />
                        <table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tr style="font-weight:bold;">
                                <td width="7%" height="25" bgcolor="#CCCCCC">C&oacute;digo</td>
                                <td width="31%" bgcolor="#CCCCCC">Nome </td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">Sal&aacute;rio</td>
                                <td width="4%" align="center" bgcolor="#CCCCCC">Dias</td>
                                <td width="7%" align="center" bgcolor="#CCCCCC">Rendim.</td>
                                <td width="7%" align="center" bgcolor="#CCCCCC">Descontos</td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">Sal. Base</td>
                                <td width="5%" align="center" bgcolor="#CCCCCC">INSS</td>
                                <td width="8%" align="center" bgcolor="#CCCCCC">IRRF</td>
                                <td width="6%" align="center" bgcolor="#CCCCCC">Sal. Fam. </td>
                                <td width="9%" align="center" bgcolor="#CCCCCC">Sal. L&iacute;q.</td>
                            </tr>

                            <?php
                            $cont = "0";

                            $resultClt = mysql_query("SELECT * FROM rh_folha_proc WHERE id_folha = '$folha' and 
		  ( status = '2' or status = '3' or status = '4')  ORDER BY nome");
                            while ($row_clt = mysql_fetch_array($resultClt)) {


                                //DEFINIE QUE O FUNCION�RIO IR� RECEBER EM CHEQUE CASO ELE N�O TENHA UM NUMERO DE CONTA, AG�NCIA OU TIPO DE CONTA DEFINIDO.
                                $resultTipoConta = mysql_query("SELECT tipo_conta FROM rh_clt WHERE id_clt = $row_clt[id_clt]");
                                $rowP = mysql_fetch_array($resultTipoConta);

                                $tiposDePagamentos = mysql_query("SELECT * FROM tipopg WHERE id_regiao = '$regiao' and campo1 = '2' and id_projeto = '$row_projeto[0]'");
                                $rowTipoPg = mysql_fetch_array($tiposDePagamentos);
                                $pgEmCheque = $rowTipoPg[0];

                                if (($row_clt['conta'] == '') or ( $row_clt['conta'] == '0')) {
                                    mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]");
                                }
                                if (($row_clt['agencia'] == '') or ( $row_clt['agencia'] == '0')) {
                                    mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]");
                                }
                                if ($rowP['tipo_conta'] == '') {
                                    mysql_query("UPDATE rh_folha_proc SET tipo_pg = '$pgEmCheque' WHERE id_folha = '$folha' and id_clt = $row_clt[id_clt]") or die(mysql_error());
                                }


                                //----FORMATANDO OS VALORES------------------------
                                //$row_clt[cod]
                                $salario_brutoF = number_format($row_clt['salbase'], 2, ",", ".");
                                $total_rendiF = number_format($row_clt['rend'], 2, ",", ".");
                                $total_debitoF = number_format($row_clt['desco'], 2, ",", ".");
                                $valor_inssF = number_format($row_clt['a5020'], 2, ",", ".");
                                //$valor_IRF = number_format($row_clt['imprenda'],2,",",".");
                                $valor_IRF = number_format($row_clt['a5021'], 2, ",", ".");
                                $valor_familiaF = number_format($row_clt['a5022'], 2, ",", ".");

                                $valor_final_individualF = number_format($row_clt['salliquido'], 2, ",", ".");

                                //$valor_desconto_sindicatoF = number_format($valor_desconto_sindicato,2,",",".");
                                //$valor_deducao_irF = number_format($valor_deducao_ir,2,",",".");
                                //-------------------
                                //---- EMBELEZAMENTO DA PAGINA ----------------------------------
                                if ($cont % 2) {
                                    $color = "corfundo_um";
                                } else {
                                    $color = "corfundo_dois";
                                }
                                $nome = str_split($row_clt['nome'], 30);
                                $nomeT = sprintf("% -30s", $nome[0]);
                                if ($row_clt['status_clt'] == '50' or $row_clt['status_clt'] == '51') {
                                    $nomeT = "<span style='color:#693;'>$nomeT</span>";
                                }
                                $bord = "style='border-bottom:#000 solid 1px;'";
                                //-----------------
                                // colocando o valor livre de redimento (feito por jr 05-02-2010 as 14:49)	
                                //$salario = number_format($row_clt['salbase'] - $row_clt['rend'],2,",","."); ALTERADO JR 27/04/2010
                                $salario = number_format($row_clt['sallimpo'], 2, ",", ".");
                                $salario_final += $row_clt['sallimpo'];


                                echo "<tr class=\"novalinha $color\">";
                                echo "<td align='left' valign='middle'>" . $row_clt['cod'] . " </td>";
                                //echo "<td align='center' valign='middle' $bord>".$nomeT."</td>";
                                echo "<td align='left' valign='middle'>$nomeT</a> $divTT</td>";
                                echo "<td align='center' valign='middle'>" . $salario . "</td>";
                                echo "<td align='center' valign='middle'>" . $row_clt['dias_trab'] . "</td>";
                                echo "<td align='center' valign='middle'>" . $total_rendiF . "</td>";
                                echo "<td align='center' valign='middle'>" . $total_debitoF . "</td>";
                                echo "<td align='center' valign='middle'>" . $salario_brutoF . "</td>";
                                echo "<td align='center' valign='middle'>" . $valor_inssF . "</td>";
                                echo "<td align='center' valign='middle'>" . $valor_IRF . "</td>";
                                echo "<td align='center' valign='middle'>" . $valor_familiaF . "</td>";
                                echo "<td align='center' valign='middle'>" . $valor_final_individualF . "</td></tr>";


                                // AQUI TERMINA O LA�O ONDE MOSTRA E CALCULA OS VALORES REFERENTES A UM �NICO FUNCIONARIO
                                // FORMATANDO OS DADOS FINAIS

                                $cont++;
                            }

                            //---- FORMATANDO OS TOTAIS GERAIS DA FOLHA -----------
                            $salario_base_finalF = number_format($row_folha['total_salarios'], 2, ",", ".");
                            $rendi_indiviF = number_format($row_folha['rendi_indivi'], 2, ",", ".");
                            $rendi_finalF = number_format($row_folha['rendi_final'], 2, ",", ".");
                            $final_indiviF = number_format($row_folha['descon_indivi'], 2, ",", ".");
                            $final_INSSF = number_format($row_folha['total_inss'], 2, ",", ".");
                            $final_IRF = number_format($row_folha['total_irrf'], 2, ",", ".");
                            $final_familiaF = number_format($row_folha['total_familia'], 2, ",", ".");
                            ;
                            $valor_finalF = number_format($row_folha['total_liqui'], 2, ",", ".");
                            $totalDeFGTS = number_format($row_folha['total_fgts'], 2, ",", ".");

                            $base_INSS_TO = number_format($row_folha['valor_dt'] + $row_folha['base_inss'], 2, ",", ".");
                            $base_IRRFF = number_format($row_folha['base_irrf'], 2, ",", ".");
                            //-----------------------
                            //VERIFICANDO SE VAI MOSTRAR OU N�O OS DESCONTOS FIXOS (EX VALE, INSS, IR, FAMILIA)------------
                            $movimentos_fixos = array(0001, 7001, 5020, 5021, 5022, 5019, 5047);
                            $valores_movimentos_fixos = array($salario_base_finalF, $vale_transporte_finalF, $final_INSSF, $final_IRF, $final_familiaF, $final_sindicatoF, $final_deducaoIRF);

                            // colocando o valor livre de redimento (feito por jr 05-02-2010 as 14:49)
                            // (feito por jr 06-05-2010 as 16:16) $salariototal = number_format($row_folha['total_salarios'] - $row_folha['rendi_indivi'],2,",",".");
                            $salariototal = number_format($row_folha['total_salarios'], 2, ",", ".");
                            $salario_finalF = number_format($salario_final, 2, ",", ".");
                            ?>

                            <tr>
                                <td height="20" align="center" valign="middle" class="style23">&nbsp;</td>
                                <td height="20" align="right" valign="bottom" class="style23">TOTAIS:</td>
                                <td height="20" align="center" valign="bottom" class="style23"><?= $salario_finalF ?></td>
                                <td height="20" align="center" valign="bottom" class="style23">&nbsp;</td>
                                <td align="center" valign="bottom" class="style23"><?= $rendi_indiviF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_indiviF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $salario_base_finalF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= number_format($row_folha['total_inss'] + $row_folha['inss_dt'], 2, ",", "."); ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_IRF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $final_familiaF ?></td>
                                <td align="center" valign="bottom" class="style23"><?= $valor_finalF ?></td>
                            </tr>

                        </table>
                        <br />
                        <br>
                        <br>
                        <br>
                        <table width="97%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="39%" align="center" valign="top" bgcolor="#F8F8F8" style="border-right:solid 2px #FFF"><br>
                                    <table width="90%" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td height="24" colspan="2" align="center" valign="middle" class="show">TOTALIZADORES</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td width="53%" align="right">Sal&aacute;rios L&iacute;quidos:</td>
                                            <td width="47%" align="left"> &nbsp;&nbsp;<?= $valor_finalF ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de INSS:</td>
                                            <td align="left"> &nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Base de IRRF:</td>
                                            <td align="left">&nbsp;&nbsp;<?= $base_IRRFF ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de FGTS:</td>
                                            <td align="left">&nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Total de FGTS:</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $totalDeFGTS ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Base de FGTS (Sefip):</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $base_INSS_TO ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">FGTS a Recolher (Sefip):</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $totalDeFGTS ?></td>
                                        </tr>
                                        <tr class="novalinha corfundo_dois">
                                            <td align="right">Multa do FGTS:</span></td>
                                            <td align="left">&nbsp;&nbsp; 0,00</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="right">Funcion&aacute;rios Listados:</td>
                                            <td align="left" valign="middle">&nbsp;&nbsp;<?= $row_folha['clts'] ?></td>
                                        </tr>
                                    </table></td>
                                <td width="61%" align="center" valign="top" bgcolor="#F8F8F8" style="border-left:solid 2px #FFF"><br>
                                    <table width="95%" align="center" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td height="30" colspan="4" align="center" valign="middle" class="show">Resumo por Evento (R$)</td>
                                        </tr>
                                        <tr class="novo_tr_dois">
                                            <td width="11%" align="center" valign="middle" >Evento</td>
                                            <td width="45%" align="left" valign="middle" >Descri&ccedil;&atilde;o </td>
                                            <td width="21%" height="20" align="right" valign="middle" >Rendimentos </td>
                                            <td width="23%" align="right" valign="middle"  style='margin-right:5;'>Descontos</td>
                                        </tr>
                                        <tr class="novalinha corfundo_um">
                                            <td align="center">0001</td>
                                            <td align="left" >SALARIO BASE</td>
                                            <td align="right" ><b><?= $salario_base_finalF ?></b></td>
                                            <td align="right" >&nbsp;</td>
                                        </tr>
                                        <?php
                                        $qntd = count($ar_codigos);
                                        for ($i = 0; $i < $qntd; $i++) {
                                            $result_codNomes = mysql_query("SELECT descicao FROM rh_movimentos WHERE cod='$ar_codigos[$i]'");
                                            $row_codNome = mysql_fetch_array($result_codNomes);
                                            $campo = "a" . $ar_codigos[$i];

                                            $reult_soma = mysql_query("SELECT SUM($campo) FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3'");
                                            $row_soma = mysql_fetch_array($reult_soma);

                                            $debitos_tab = array('5019', '5020', '5021', '6004', '7003', '8000', '7009', '5020', '5020', '5021', '5021', '5021', '5020', '9500', '7001');
                                            $rendimentos_tab = array('5011', '5022', '6006', '6007', '9000', '5022');

                                            if (in_array($ar_codigos[$i], $debitos_tab)) {
                                                if ($ar_codigos[$i] == "5020") {
                                                    $debito = number_format($row_folha['total_inss'], 2, ",", ".");
                                                } else {
                                                    $debito = number_format($row_soma['0'], 2, ",", ".");
                                                }
                                            } else {
                                                $rendimento = number_format($row_soma['0'], 2, ",", ".");
                                            }

                                            if ($rendimento == "0,00" or $debito == "0,00") {
                                                $disable = "style='display:none'";
                                            } else {
                                                $disable = "style='display:'";
                                            }

                                            if ($campo == "a5049") {     //DDIR
                                                $disable = "style='display:none'";
                                            }

                                            print "<tr class=\"novalinha corfundo_um\" $disable>
	          <td height='18' align='center' valign='middle'>$ar_codigos[$i]</td>
	          <td align='left' valign='middle'>$row_codNome[0]</td>
	          <td align='right' valign='middle'><span style='margin-right:1;'><b>" . $rendimento . "&nbsp;</b></span></td>
	          <td align='right' valign='middle' ><span style='margin-right:5;'><b>" . $debito . "&nbsp;</b></span></td></tr>";

                                            $debito = "";
                                            $rendimento = "";
                                        }
                                        ?>
    <?php if ($row_folha['terceiro'] == 1) { ?>
                                            <tr class="novalinha corfundo_um">
                                                <td height="18" align="center" valign="middle">5029</td>
                                                <td align="left" valign="middle">D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle"><b>
                                            <?= number_format($row_folha['valor_dt'], 2, ",", ".") ?>
                                                    </b></td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                            </tr>
                                            <tr class="novalinha corfundo_dois">
                                                <td height="18" align="center" valign="middle">5030</td>
                                                <td align="left" valign="middle">IRRF D&Eacute;CIMO TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                                <td align="right" valign="middle"><span style="margin-right:5;"><b><?= number_format($row_folha['ir_dt'], 2, ",", ".") ?></b></span></td>
                                            </tr>
                                            <tr class="novalinha corfundo_um">
                                                <td height="18" align="center" valign="middle">5031</td>
                                                <td align="left" valign="middle">INSS TERCEIRO SAL&Aacute;RIO</td>
                                                <td align="right" valign="middle">&nbsp;</td>
                                                <td align="right" valign="middle"><span style="margin-right:5;"><b><?= number_format($row_folha['inss_dt'], 2, ",", ".") ?></b></span></td>
                                            </tr>
        <?php
    }


    //FORMATANDO TOTAIS POR EVENTO
    $re_tot_rendimentofimF = number_format($row_folha['rendi_final'], 2, ",", ".");
    $re_tot_descontoF = number_format($row_folha['descon_final'], 2, ",", ".");
    ?>
                                        <tr class="novo_tr_dois">
                                            <td colspan="2" align="center">TOTAIS</td>
                                            <td height="20" align="right" ><?= $re_tot_rendimentofimF ?></td>
                                            <td align="right" style="text-align:right"><span style="margin-right:5;">
                        <?= $re_tot_descontoF ?>
                                                </span></td>
                                        </tr>
                                    </table></td>
                            </tr>
                        </table>
                        <br>



                        <?php
                        //VERIFICANDO QUAIS BANCOS EST�O ENVOLVIDOS COM ESSA FOLHA DE PAGAMENTO

                        $RE_Bancs = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_banco != '9999' AND id_folha = '$folha' and id_banco != '0' and 
	  (status = '3' or status = '4') GROUP BY id_banco");
                        $num_Bancs = mysql_num_rows($RE_Bancs);

                        echo "<table border='0' width='50%' border='0' cellpadding='0' cellspacing='0'>";
                        echo "<tr><td colspan=5 align='center' $bord><div style='font-size: 17px;'><b>Lista de Bancos</b></div></td></tr>";
                        $contCol = 0;
                        while ($row_Bancs = mysql_fetch_array($RE_Bancs)) {

                            $RE_Bancos = mysql_query("SELECT * FROM bancos WHERE id_banco = '$row_Bancs[0]'");
                            $row_Bancos = mysql_fetch_array($RE_Bancos);
                            //-- ENCRIPTOGRAFANDO A VARIAVEL
                            $linkBanc = encrypt("$regiao&$row_Bancos[0]&$folha");
                            $linkBanc = str_replace("+", "--", $linkBanc);
                            // -----------------------------
                            $linkBank = "folha_banco.php?enc=$linkBanc";
                            $disable_form = "style='display:none'";
                            echo "<tr>";
                            echo "<td align='center' valign='middle' width='30' $bord><div style='font-size: 15px;'>";
                            echo "<img src=../../imagens/bancos/$row_Bancos[id_nacional].jpg  width='25' height='25' 
		  align='absmiddle' border='0'></td>";
                            echo "<td valign='middle' $bord>&nbsp;&nbsp;" . $row_Bancos['nome'] . "</div></a></td>";

                            $resultBancosFinalizados = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status='4' and id_banco = '$row_Bancs[0]' group by id_banco");
                            $numBancosFinalizados = mysql_affected_rows();
                            if ($numBancosFinalizados != 0) {
                                $rowBancosFinalizados = mysql_fetch_array($resultBancosFinalizados);
                                $resultPartFinalizados = mysql_query("SELECT id_clt FROM rh_folha_proc where id_folha = '$folha' and status = '4' and id_banco = '$rowBancosFinalizados[0]'");
                                $numPartFinalizados = mysql_num_rows($resultPartFinalizados);
                                print "<td $bord>&nbsp;</td>";
                                print "<td  align='right' $bord>";
                                print "&nbsp;&nbsp;<a href=finalizados.php?regiao=$regiao&folha=$folha&projeto=$row_projeto[0]&banco=$rowBancosFinalizados[0]>FINALIZADO</a>";
                                print "</td>";

                                echo "<td align='center' valign='middle' width='10%' $bord>$numPartFinalizados Participantes</td>";
                            } else {
                                $resultPorBanco = mysql_query("SELECT id_banco FROM rh_folha_proc WHERE id_folha = '$folha' and status = '3' and id_banco = '$row_Bancs[0]'");
                                $quant_por_banco = mysql_affected_rows();

                                if ($quant_por_banco != 0) {
                                    echo "<td valign='center' $bord><form id='form1' name='form1' method='post' action='$linkBank'>&nbsp;
							  <label id='data_pag$contCol' $disable_form> 
							  <input name='data' type='text' id='data[]' size='10' class='campotexto'
							  onKeyUp='mascara_data(this)' maxlength='10' onFocus=\"this.style.background='#CCFFCC'\"
							  onBlur=\"this.style.background='#FFFFFF'\" style='background:#FFFFFF' >
							  <input name='enviar' id='enviar[]' type='submit' value='Gerar'/></label>
							  </td>";
                                    echo "</form>";

                                    echo "<td align='right' valign='middle' width='15%' $bord><a style='TEXT-DECORATION: none;'>
						  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Banco' onClick=\"document.all.data_pag$contCol.style.display = (document.all.data_pag$contCol.style.display == 'none') ? '' : 'none' ;\"></a></td>";
                                    echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";
                                } else {
                                    echo "<td $bord>&nbsp;</td>";
                                    echo "<td $bord align='right'><span style='font-family:verdana, arial; font-size:9px; color:red'><strong>VERIFICAR</strong></span></td>";
                                    echo "<td align='center' valign='middle' width='15%' $bord>$quant_por_banco Participantes</td>";
                                }
                            }
                            $contCol++;
                        }

                        $RE_ToCheq = mysql_query("SELECT * FROM rh_folha_proc WHERE (id_folha = '$folha' and id_banco = '0' and status = '3') or (id_folha = '$folha' and agencia = '' and status = '3') or (id_folha = '$folha' and conta = '' and status = '3') or (id_folha = '$folha' and tipo_pg = '$rowTipoPg[0]' and status = '3')");
                        /// $num_ToCheq = mysql_num_rows($RE_ToCheq);
                        $num_ToCheq = mysql_affected_rows();

                        //-- ENCRIPTOGRAFANDO A VARIAVEL
                        $linkcheque = encrypt("$regiao&$folha&$row_TIpoCheq[0]&$row_TipoDepo[0]");
                        $linkcheque = str_replace("+", "--", $linkcheque);
                        // -----------------------------

                        echo "<tr>";
                        echo "<td align='center' valign='middle' width='30' $bord>";
                        echo "<img src=../../imagens/bancos/cheque.jpg  width='25' height='25' align='absmiddle' border='0'></td>";
                        echo "<td valign='middle' $bord><div style='font-size: 15px;'>&nbsp;&nbsp;Cheque</div></a></td>";
                        echo "<td valign='center' $bord>&nbsp;</td>";
                        echo "<td align='right' valign='middle' width='10%' $bord><a href='ver_cheque.php?enc=$linkcheque'>
		  <img src='imagens/ver_banc.png' border='0' alt='Visualizar Funcionarios por Cheque'></a></td>";

                        echo "<td align='center' valign='middle' width='15%' $bord>$num_ToCheq Participantes</td>";

                        echo "</tr></table>";
                        ?>


                        <br>
                        <br>
    <?php
//-- ENCRIPTOGRAFANDO A VARIAVEL
    $linkvolt = encrypt("$regiao&1");
    $linkvolt = str_replace("+", "--", $linkvolt);
// -----------------------------
    $enc2 = str_replace("+", "--", $enc);
    ?>
                        <br></td>
                </tr>
                <tr>
                    <td align="center" valign="middle" bgcolor="#CCCCCC">
                        <b><a href='folha.php?<?= "enc=" . $linkvolt . "&tela=1" ?>' class="botao">VOLTAR</a></b>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b><a href='ver_lista_banco.php?<?= "enc=" . $enc2 ?>' class="botao">VER LISTA POR BANCO</a></b>
                    </td>
                </tr>
            </table>
            <p>&nbsp;</p>
        </body>
    </html>




<?php } ?>


<script language='javascript'>

    function mascara_data(d) {
        var mydata = '';
        data = d.value;
        mydata = mydata + data;
        if (mydata.length == 2) {
            mydata = mydata + '/';
            d.value = mydata;
        }
        if (mydata.length == 5) {
            mydata = mydata + '/';
            d.value = mydata;
        }
        if (mydata.length == 10) {
            verifica_data(d);
        }
    }

    function verifica_data(d) {

        dia = (d.value.substring(0, 2));
        mes = (d.value.substring(3, 5));
        ano = (d.value.substring(6, 10));


        situacao = "";
        // verifica o dia valido para cada mes  
        if ((dia < 01) || (dia < 01 || dia > 30) && (mes == 04 || mes == 06 || mes == 09 || mes == 11) || dia > 31) {
            situacao = "falsa";
        }

        // verifica se o mes e valido  
        if (mes < 01 || mes > 12) {
            situacao = "falsa";
        }

        // verifica se e ano bissexto  
        if (mes == 2 && (dia < 01 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {
            situacao = "falsa";
        }

        if (d.value == "") {
            situacao = "falsa";
        }

        if (situacao == "falsa") {
            alert("Data digitada � inv�lida, digite novamente!");
            d.value = "";
            d.focus();
        }

    }
</script>