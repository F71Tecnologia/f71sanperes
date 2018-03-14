<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
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
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)$ACOES = new Acoes();

$breadcrumb_config = array("nivel" => "../index.php", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Relatório de Transferência Desprocessadas");
$breadcrumb_pages = array("Principal" => "../listaLogRelatorios_novo.php?regiao=");

$id_regiao = $_REQUEST['reg'];

if (isset($_REQUEST['gerar']) || isset($_REQUEST['todos_projetos'])) {

    $id_projeto = $_REQUEST['projeto'];
    
    $cond_funcao = (isset($_REQUEST['funcao']) && !empty($_REQUEST['funcao']) && $_REQUEST['funcao'] != '-1')?" AND E.id_curso= '{$_REQUEST['funcao']}' ":"";
    
    $projeto = montaQueryFirst("projeto", "nome", array('id_projeto'=>$id_projeto));
    
    $arrcondicao = array();
    
    if (isset($_REQUEST['projeto']) && !empty($_REQUEST['projeto']) && $_REQUEST['projeto'] != '-1'){
        $arrcondicao[] .= " id_projeto_de = '{$_REQUEST['projeto']}' OR id_projeto_para = '{$_REQUEST['projeto']}'";
    }
    if (isset($_REQUEST['cpf']) && !empty($_REQUEST['cpf'])){
        $arrcondicao[] .= " a.cpf = '{$_REQUEST['cpf']}' ";
    }
    $condicao = (!empty($arrcondicao)) ? "WHERE ".implode(' AND ', $arrcondicao) : '';
    
    
    $sql = "select a.id_clt,a.nome,a.cpf,b.unidade_de,b.unidade_para,b.id_unidade_de,b.id_unidade_para,b.motivo, 
                DATE_FORMAT( b.data_proc , '%m/%Y' ) AS `data_proc`, b.id_transferencia,
                DATE_FORMAT( b.criado_em , '%d/%m/%Y %T') AS `criado_em`,
                (select nome from curso where id_curso = b.id_curso_de) as curso_de,
                (select nome from curso where id_curso = b.id_curso_para) as curso_para ,
                c.nome as usuario,
                DATE_FORMAT(b.data_exclusao, '%d/%m/%Y %T') as data_exclusao
                from rh_clt as a
                inner join rh_transferencias_log as b on (b.id_clt = a.id_clt)
                inner join funcionario as c on (b.id_usuario_exclusao = c.id_funcionario) $condicao";
    echo "<!-- {$sql} -->";
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    $num_rows = mysql_num_rows($qr_relatorio);
}

$regiaoSel = (isset($_REQUEST['reg'])) ? $_REQUEST['reg'] : $usuario['id_regiao'];
$projetoSel = (isset($_REQUEST['pro'])) ? $_REQUEST['pro'] : null;
$funcaoSel = (isset($_REQUEST['funcao'])) ? $_REQUEST['funcao'] : null;

$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $regiaoSel;
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : $projetoSel;

//$projetosOp = array("-1" => "« Selecione »");
//$query = "SELECT id_projeto, nome FROM projeto WHERE id_regiao = '$id_regiao'";
//echo $query;
$query = "SELECT id_projeto, nome FROM projeto WHERE id_master = {$usuario['id_master']};";
$result = mysql_query($query) or die(mysql_error());
$projetosOp["-1"] = "« Selecione a Região »";
while ($row = mysql_fetch_assoc($result)) {
    $projetosOp[$row['id_projeto']] = $row['id_projeto'] . " - " . $row['nome'];
}

//echo "<pre>";
//print_r($projetosOp);
//echo "<pre>";

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Relatório de Transferências Desprocessadas</title>

        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->

    </head>
    <body>
<?php include("../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - Relatório de Transferências Desprocessadas</h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form1">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Relatório</div>
                        <input type="hidden" name="hide_regiao" id="hide_regiao" value="<?php echo $regiaoSel ?>" />
                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                        
                    <div class="panel-body">
                       <div class="form-group">
                            <label class="control-label col-sm-2">Projeto</label>
                            <div class="col-sm-3">
                                <?php echo montaSelect($projetosOp, $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                            </div>
                                         
                            <label class="control-label col-sm-2">CPF do CLT</label>
                            <div class="col-sm-3">
                                <input type="text" name="cpf" id="cpf" class="form-control">
                            </div>
                        </div>    
                                              
                        <div class="panel-footer text-right hidden-print controls">
                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                            <?php
                        ///PERMISSÃO PARA VISUALIZAR TODOS OS PROJETOS AO MESMO TEMPO
                        if ($ACOES->verifica_permissoes(85)) {
                            ?>
                            <button type="submit" name="todos_projetos" value="Gerar de Todos Projetos" class="btn btn-primary" id="todos_projetos"><span class="fa fa-filter"></span> Todos Os Projetos</button>
                            <?php } ?>
                            <button type="submit" name="gerar" id="gerar" value="Gerar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                        </div>
                    </div> 
               </div>
                
                <?php if (!empty($qr_relatorio) && isset($_POST['gerar']) || isset($_REQUEST['todos_projetos'])) { ?>
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Participantes Ativos')" value="Exportar para Excel" class="exportarExcel btn btn-success"></p>    
                    <table id="tbRelatorio" class="table table-hover table-striped table-bordered text-sm valign-middle"> 
                        <thead>
                            <tr>
                                <th colspan="8"><?= (!isset($_REQUEST['todos_projetos']))? $projeto['nome'] : 'TODOS OS PROJETOS' ?></th>
                            </tr>
                            <tr class="bg-primary">
                                <th>NOME</th>
                                <th>CPF</th>
                                <th>UNIDADE DE ORIGEM</th>
                                <th>FUNÇÃO DE ORIGEM</th>
                                <th>UNIDADE DE DESTINO</th>
                                <th>FUNÇÃO DE DESTINO</th>
                                <th>MOTIVO</th>
                                <th style="width: 10%;">COMPETÊNCIA</th>   
                                <th style="width: 10%;">DATA DE CRIAÇÃO</th>   
                                <th>USUÁRIO RESPONSÁVEL</th>   
                                <th>DATA DE EXCLUSÃO</th>   
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                while ($row_rel = mysql_fetch_assoc($qr_relatorio)) {
                                    $class = ($cont++ % 2 == 0) ? "even" : "odd"
                                    ?>
                                    <tr class="<?php echo $class ?>">
                                        <td><?php echo $row_rel['nome'] ?></td>
                                        <td align="center"> <?php echo $row_rel['cpf']; ?></td>
                                        <td align="center"> <?php echo $row_rel['unidade_de']; ?></td>
                                        <td> <?php echo $row_rel['curso_de']; ?></td>
                                        <td align="center"> <?php echo $row_rel['unidade_para']; ?></td>
                                        <td> <?php echo $row_rel['curso_para']; ?></td>
                                        <td align="center"><?php echo $row_rel['motivo']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['data_proc']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['criado_em']; ?></td>                       
                                        <td align="center"><?php echo $row_rel['usuario']; ?></td>                       
                                        <td align="center"><?= $row_rel['data_exclusao']; ?></td>                       
                                    </tr>                                
                                <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="bg-primary">
                                <td colspan="2">Total:</td>
                                <td align="center"><?php echo $num_rows ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } ?>
                
            </form>
            
            <!--<div class="clear"></div>-->
             <?php include('../template/footer.php'); ?>
        </div>
        
        <!--<script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>-->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <!--<script src="../js/tableExport.jquery.plugin-master/tableExport.js" type="text/javascript"></script>-->
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
            
         <script>
            $(function() {
                var id_destination = "projeto";
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function(data) {
                    removeLoading();
                    $("#" + id_destination).html(data);
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    $('#projeto').trigger('change');
                }, "projeto");


                $('#projeto').ajaxGetJson("../methods.php", {method: "carregaFuncoes", default: "2"}, null, "funcao");
                    
                    
               $('#cpf').mask('999.999.999-99');     
            });
        </script>

    </body>
</html>

<!-- A -->