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

$opt = array("0"=>"Todos","1"=>"Funcionários com Sindicato","2"=>"Funcionários sem Sindicato");

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {
    $cont = 0;
    $arrayStatus = array(10, 40, 50, 51, 52);
    $status = implode(",", $arrayStatus);

    $id_regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $sindicato = $_REQUEST['tipo'];
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
    $str_qr_relatorio = "SELECT MAX(id_clt), nome, DATE_FORMAT(data_nasci,'%d/%m/%Y') as data_nascibr, campo3, locacao
                        FROM rh_clt 
                        WHERE id_regiao = '$id_regiao' AND status < '60' ";
    
    if(!isset($_REQUEST['todos_projetos'])) {
        $str_qr_relatorio .= "AND id_projeto = '$id_projeto' ";
    }
    
    $str_qr_relatorio .= "GROUP BY cpf
                            ORDER BY nome";
    
    $qr_relatorio = mysql_query($str_qr_relatorio) or die(mysql_error());
    $qtd_clt = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
?>
<html>
    <head>
        <title>:: Intranet :: Relatório de CLTs para Impressão de Etiqueta em lote</title>
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
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, null, "projeto");
                $('#gerar').click(function() {
                    $("#form").attr('action', '');
                    $("#form").submit();
                });
            });
            
            // função do botão que seleciona todos os check box
            function MarcarTodosCheckbox(){
            $("input[name='check_list[]']").each(function(){
            $(this).attr("checked","checked");
            });}
                //função que desmarca todos
            function Desmarcar(){
            $("input[name='check_list[]']").each(function(){
            $(this).removeAttr("checked");});}
        </script>
    </head>
    <body class="novaintra" >        
        <div id="content">
            <form  name="form" action="etiquetaLote.php?reg=<?php echo $id_regiao;?>&pro=<?php echo $id_projeto;?>" method="post" id="form">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Relatório de CLTs para Impressão de Etiqueta em lote</h2>
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
                        <thead>
                            <tr>
                                <th colspan="5">
                                    <input type="radio" value="Marcar Todos" name="marca"  onClick="MarcarTodosCheckbox();"/> 
                                    <span>Selecionar todos</span>
                                    <input type="radio" value="Desmarcar" name="marca" onClick="Desmarcar();"  checked=""/> 
                                    <span>Desmarcar todos</span>
                                </th>
                            </tr>
                            <tr>
                                <?php if(!isset($_REQUEST['todos_projetos'])) {echo "<th>SELECIONE</th>";} ?>
                                <th>COD.</th>
                                <th>NOME</th>
                                <th>UNIDADE</th>
                                <th>DATA NASCIMENTO</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row_rel = mysql_fetch_assoc($qr_relatorio)) { $class = ($cont++ % 2 == 0)?"even":"odd"?>
                            <tr class="<?php echo $class ?>">
                                <?php if(!isset($_REQUEST['todos_projetos'])) { echo "<td align=\"center\"><input type=\"checkbox\" name=\"check_list[]\" value=\"{$row_rel['id_clt']}\" /></td>"; }?>
                                <td><?php echo $row_rel['campo3'] ?></td>
                                <td> <?php echo $row_rel['nome']; ?></td>
                                <td> <?php echo $row_rel['locacao']; ?></td>
                                <td><?php echo $row_rel['data_nascibr']; ?></td>
                            </tr>                                
                        <?php } ?>
                    </tbody>
                </table>
                <p class="controls" style="margin-top: 10px;">
                    <input type="submit" name="gerar_lote" value="Gerar Lote" id="gerar_lote" />
                </p>
                <?php  } ?>
            </form>
        </div>
    </body>
</html>