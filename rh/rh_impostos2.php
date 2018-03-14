<?php
if(empty($_COOKIE['logado'])) {
	print 'Efetue o Login<br><a href="../login.php">Logar</a>';
	exit;
}

include('../conn.php');
include('../wfunction.php');
include('../classes/global.php');

$usuario = carregaUsuario();
$regiao  = $usuario['id_regiao'];
$id_user = $_COOKIE['logado'];
$mes     = date('m');

// Primeira fase do script, Selecionando as informa��oes do select pricipal
$result_select = mysql_query("SELECT distinct(descicao), cod FROM rh_movimentos WHERE valor = 'imposto' AND incidencia = 'folha'");

// Segunda fase do script, Recebendo as vari�veis que foram retiradas do banco de dados e atualizadas
$cod        = $_REQUEST['cod'];
$id_mov     = $_REQUEST['id_mov'];
$faixa      = $_REQUEST['faixa'];
$v_ini      = $_REQUEST['v_ini'];
$v_fim      = $_REQUEST['v_fim'];
$percentual = $_REQUEST['percentual'];
$fixo       = $_REQUEST['fixo'];
$valor      = $_REQUEST['valor'];
$descicao   = $_REQUEST['descicao'];
$categoria  = $_REQUEST['categoria'];
$incidencia = $_REQUEST['incidencia'];
$ano_base   = $_REQUEST['ano_base'];

// Vari�vel que autoriza a atualiza��o no banco de dados.
$status = $_REQUEST['gravar'];
$opcao  = $_REQUEST['opcao'];

if($status == 'gravar') {
    $status = NULL;
    mysql_query("UPDATE rh_movimentos SET descicao = '$descicao', valor = '$valor', categoria = '$categoria', incidencia = '$incidencia', faixa = '$faixa', v_ini = '$v_ini', v_fim = '$v_fim', percentual = '$percentual', fixo = '$fixo', user_alter = '$id_user', anobase = '$ano_base', ultima_alter = CURDATE() WHERE id_mov = '$id_mov'");
    echo "<script>alert('Altera��o realizada com sucesso!');</script>";	
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Impostos");
$breadcrumb_pages = array("Gest�o de RH"=>"../rh");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Impostos</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
    </head>
    <body>
    <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Impostos</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form class="form-group" method="post" id="form1">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <input type="hidden" name="home" id="home" >
                                <div class="col-xs-offset-1 col-xs-10">
                                    <select name="tipo" class="form-control" onChange="location.href=this.value;">
                                        <option>SELECIONE</option>
                                        <?php 
                                        $result = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '$opcao'");
                                        while($row_opcao = mysql_fetch_array($result_select)) { ?>
                                            <option value="rh_impostos2.php?opcao=<?=$row_opcao['cod']?>"
                                                <?php if($opcao == $row_opcao['cod']) {echo 'selected';}?>>
                                                <?=$row_opcao['cod']?> - <?=$row_opcao['descicao']?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-striped table-hover secao">
                        <?php if(!empty($opcao)) { ?>
                            <thead>
                                <tr>
                                    <th class="valign-middle" style="width:4%">Faixa</th>
                                    <th class="valign-middle" style="width:12%">Valor Inicial (R$)</th>
                                    <th class="valign-middle" style="width:11%">Valor Final (R$)</th>
                                    <th class="valign-middle" style="width:11%">Percentual (%)</th>
                                    <th class="valign-middle" style="width:9%">Fixo (R$)</th>
                                    <th class="valign-middle" style="width:9%">Valor</th>
                                    <th class="valign-middle" style="width:19%">Descri��o</th>
                                    <th class="valign-middle" style="width:10%">Categoria</th>
                                    <th class="valign-middle" style="width:6%">Incid�ncia</th>
                                    <th class="valign-middle" style="width:8%">Ano Base</th>
                                    <th class="valign-middle" style="width:1%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="11"></td></tr>
                                <?php $cont = NULL;
                                while($row = mysql_fetch_array($result)) {
                                    $cont++; ?>
                                    <tr>
                                        <td class="valign-middle"><?=$row['faixa']?></td>
                                        <td class="valign-middle"><?=number_format($row['v_ini'], '2', ',', '.')?></td>
                                        <td class="valign-middle"><?=number_format($row['v_fim'], '2', ',', '.')?></td>
                                        <td class="valign-middle"><?=$row['percentual']?></td>
                                        <td class="valign-middle"><?=number_format($row['fixo'], '2', ',', '.')?></td>
                                        <td class="valign-middle"><?=$row['valor']?></td>
                                        <td class="valign-middle"><?=$row['descicao']?></td>
                                        <td class="valign-middle"><?=$row['categoria']?></td>
                                        <td class="valign-middle"><?=$row['incidencia']?></td>
                                        <td class="valign-middle"><?=$row['anobase']?></td>
                                        <td class="valign-middle"><a href="#" id="editar" class="btn btn-sm btn-warning fa fa-pencil" onClick="document.all.linha<?=$cont?>.style.display = (document.all.linha<?=$cont?>.style.display == 'none') ? '' : 'none' ; " title="Editar"></a></td>
                                    </tr>
                                    <tr class="warning" style="display:none;" id="linha<?=$cont?>">
                                        <form action="<?php echo $_SERVER['PHP_SELF'].'?opcao='.$opcao.'&regiao='.$regiao; ?>" method="post">
                                            <td class="valign-middle"><input name="faixa" class="form-control no-padding-hr text-center" id="faixa" type="text" value="<?=$row['faixa']?>"></td>
                                            <td class="valign-middle"><input name="v_ini" class="form-control no-padding-hr text-center" id="v_ini" type="text" value="<?=$row['v_ini']?>"></td>
                                            <td class="valign-middle"><input name="v_fim" class="form-control no-padding-hr text-center" id="v_fim" type="text" value="<?=$row['v_fim']?>"></td>
                                            <td class="valign-middle"><input name="percentual" class="form-control no-padding-hr text-center" id="percentual" type="text" value="<?=$row['percentual']?>"></td>
                                            <td class="valign-middle"><input name="fixo" class="form-control no-padding-hr text-center" id="fixo" type="text" value="<?=$row['fixo']?>"></td>
                                            <td class="valign-middle"><input name="valor" class="form-control no-padding-hr text-center" id="valor" type="text" value="<?=$row['valor']?>"></td>
                                            <td class="valign-middle"><textarea name="descicao" class="form-control no-padding-hr" id="descicao" cols="20" rows="0"><?=$row['descicao']?></textarea></td>
                                            <td class="valign-middle"><input name="categoria" class="form-control no-padding-hr text-center" id="categoria" type="text" value="<?=$row['categoria']?>"></td>
                                            <td class="valign-middle"><input name="incidencia" class="form-control no-padding-hr text-center" id="incidencia" type="text" value="<?=$row['incidencia']?>"></td>
                                            <td class="valign-middle"><input name="ano_base" class="form-control no-padding-hr text-center" id="ano_base" type="text" value="<?=$row['anobase']?>"></td>
                                            <td class="valign-middle text-center"><button type="submit" class="btn btn-sm btn-success" alt="Atualizar" title="Atualizar"><i class="fa fa-check-circle"></i></button></td>
                                            <input type="hidden" name="id_mov" id="id_mov" value="<?=$row['id_mov']?>">
                                            <input type="hidden" name="gravar" value="gravar">
                                        </form>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <?php include_once '../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        
        <script>
            function alterar(f) {
                    document.getElementById(f).style.display = '';
            }
        </script>
    </body>
</html>