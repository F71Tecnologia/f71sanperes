<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";

$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();

$opt = array("2"=>"CLT","1"=>"Autônomo","3"=>"Cooperado","4"=>"Autônomo/PJ");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $nome_anterior = "";

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $tipo_contratacao = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    $contratacao = ($tipo_contratacao == "2")? "clt" : "autonomo";
    
    if($tipo_contratacao == 2) {
        $str_qr_relatorio = "SELECT A.nome, B.nome1, B.nome2, B.nome3,
                            B.nome4, B.nome5, B.nome6,
                            date_format(data1, '%d/%m/%Y') AS data1br, 
                            date_format(data2, '%d/%m/%Y') AS data2br, 
                            date_format(data3, '%d/%m/%Y') AS data3br, 
                            date_format(data4, '%d/%m/%Y') AS data4br, 
                            date_format(data5, '%d/%m/%Y') AS data5br,
                            date_format(data6, '%d/%m/%Y') AS data6br
                            FROM rh_clt AS A
                            LEFT JOIN dependentes AS B
                            ON B.id_bolsista = A.id_clt
                            WHERE A.id_regiao = '$id_regiao'
                            AND A.nome = B.nome
                            AND B.nome1 != '' ";
    }
    else {
        $str_qr_relatorio = "SELECT A.nome, B.nome1, B.nome2, B.nome3,
                            B.nome4, B.nome5, B.nome6,
                            date_format(data1, '%d/%m/%Y') AS data1br, 
                            date_format(data2, '%d/%m/%Y') AS data2br, 
                            date_format(data3, '%d/%m/%Y') AS data3br, 
                            date_format(data4, '%d/%m/%Y') AS data4br, 
                            date_format(data5, '%d/%m/%Y') AS data5br,
                            date_format(data6, '%d/%m/%Y') AS data6br
                            FROM autonomo AS A
                            LEFT JOIN dependentes AS B
                            ON B.id_bolsista = A.id_autonomo
                            WHERE A.id_regiao = '$id_regiao'
                            AND A.nome = B.nome
                            AND B.nome1 != '' 
                            AND A.tipo_contratacao = '$tipo_contratacao' ";
    }
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND A.id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "ORDER BY A.nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$optSel = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<html>
    <head>
        <title>:: Intranet :: Relatório de Salário Família</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $(".bt-image").on("click", function() {
                    var id = $(this).data("id");
                    var contratacao = $(this).data("contratacao");
                    var nome = $(this).parents("tr").find("td:first").html();
                    thickBoxIframe(nome, "relatorio_documentos_new.php", {id: id, contratacao: contratacao, method: "getList"}, "625-not", "500");
                });
            });
            $(function() {
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
        <style>
            .colEsq{
                width: auto;
                min-height: 0px;
                border-right: 0px;
                margin-right: 0px;
                float: none;
            }
            .bt-image{
                width: 18px;
                cursor: pointer;
            }
            h3 {text-align: center;}
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de Salário Família</h2>
                    </div>
                </div>
                <br class="clear">
                <br/>


                <fieldset class="noprint">
                    <legend>Relatório</legend>
                    <div class="fleft">
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?> </p>                        
                        <p><label class="first">Projeto:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), $projetoSel, array('name' => "projeto", 'id' => 'projeto')); ?> </p>
                        <p><label class="first">Tipo Contratação:</label> <?php echo montaSelect($opt, $optSel, array('name' => "tipo", 'id' => 'tipo')); ?> </p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <?php ///permissão para VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                       if($ACOES->verifica_permissoes(85)) { ?>
                            <input type="submit" name="todos_projetos" value="Gerar de Todos Projetos" id="todos_projetos"/>
                      <?php } ?>
                            
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>

                <?php if (!empty($qr_relatorio) && (isset($_POST['gerar'])) || isset($_REQUEST['todos_projetos'])){ ?>
                <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel"></p>    
                <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;"> 
                    <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { ?>
                        <?php if($nome_anterior != $row_rel['nome']) { $cont = 0; ?>
                            <thead>
                                <tr>
                                    <th align="center" colspan="2" style="background-color: #AAA;">
                                        <?php echo $row_rel['nome']; ?>
                                    </th>
                                </tr>
                                <tr>
                                    <th>NOME DEPENDENTE</th>
                                    <th>DATA NASCIMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($num_dependente = 1; !empty($row_rel['nome'.$num_dependente]); $num_dependente++) { $class = ($cont++ % 2 == 0)?"even":"odd"; ?>
                                <tr class="<?php echo $class ?>">
                                    <td><?php echo $row_rel['nome'.$num_dependente] ?></td>
                                    <td> <?php echo $row_rel['data'.$num_dependente.'br']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        <?php } ?>
                    <?php $nome_anterior = $row_rel['nome']; $num_dependente++; } ?>
                </table>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>