<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include "../../conn.php";
include "../../wfunction.php";

$usuario = carregaUsuario();
$optMeses = mesesArray();
$optAnos = anosArray();
$optRegiao = getRegioes();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$mesSel = isset($_POST['mes']) ? str_pad($_POST['mes'],2,'0', STR_PAD_LEFT) : date('m');
$anoSel = isset($_POST['ano']) ? $_POST['ano'] : date('Y');
$regiaoSel = isset($_POST['regiao']) ? $_POST['regiao'] : $usuario['id_regiao'];
$projetoSel = isset($_POST['projeto']) ? $_POST['projeto'] : FALSE;

if(isset($_POST['acao']) && $_POST['acao']=='relatorio'){ 

    $sql = 'SELECT A.id_clt, A.nome, DATE_FORMAT(data_adm,"%d/%m/%Y") AS data_adm_f, DATE_FORMAT(data_demi,"%d/%m/%Y") AS data_demi_f, 
            B.nome AS nome_projeto, A.`status` FROM rh_recisao AS A LEFT JOIN projeto AS B ON(A.id_projeto=B.id_projeto)
            WHERE  A.motivo IN(61,66) AND A.id_projeto="'.$projetoSel.'"  AND A.id_regiao="'.$regiaoSel.'" AND DATE_FORMAT(A.data_demi,"%Y-%m")="'.$anoSel.'-'.$mesSel.'" AND A.status=1;';
    
    
    $result = mysql_query($sql);
    $dados = array();
    while ($row = mysql_fetch_array($result)){
        $dados[] = $row;
    }

}
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Seguro Desemprego");
$breadcrumb_pages = array("Gest�o de RH"=>"../");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Seguro Desemprego</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Seguro Desemprego</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            
            <div class="row">
                <div class="col-lg-12">
                    <form class="form-horizontal" action="" method="post" name="form1" id="form1">
                        <fieldset>
                            <div class="form-group">
                                <label for="regiao" class="col-lg-1 control-label">Regi�o:</label>
                                <div class="col-lg-11">
                                    <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="projeto" class="col-lg-1 control-label">Projeto:</label>
                                <div class="col-lg-11">
                                    <?php echo montaSelect(array("-1" => "� Selecione a Regi�o �"), $projetoSel, array('name' => "projeto", 'id' => 'projeto', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mes" class="col-lg-1 control-label">M�s:</label>
                                <div class="col-lg-11">
                                    <?php echo montaSelect($optMeses, $mesSel, array('name' => 'mes', 'id' => 'mes', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ano" class="col-lg-1 control-label">Ano:</label>
                                <div class="col-lg-11">
                                    <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'class' => 'form-control')); ?>
                                </div>
                            </div>
                            <div class="col-lg-1 col-lg-offset-11">
                                <input type="hidden" name="home" id="home" value="" />
                                <input type="hidden" name="acao" value="relatorio"/>
                                <input type="submit" class="btn btn-primary" name="filtrar" value="Filtrar" id="filtrar"/>
                            </div>
                        </fieldset>  
                    </form>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-12">
                    <?php if(isset($dados) && !empty($dados)) { ?>
                    <table class="table table-striped table-hover" id="folha">
                        <thead>
                            <tr>
                                <th>COD</th>
                                <th>NOME</th>
                                <th>DATA DE ADMISS�O</th>
                                <th>DADE DE DEMISS�O</th>
                                <th>Projeto</th>
                                <th>Imprimir</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($dados as $funcionario) { ?>
                            <tr>
                                <td><?= $funcionario['id_clt']; ?></td>
                                <td><?= $funcionario['nome']; ?></td>
                                <td class="center"><?= $funcionario['data_adm_f']; ?></td>
                                <td class="center"><?= $funcionario['data_demi_f']; ?></td>
                                <td><?= $funcionario['nome_projeto']; ?></td>
                                <td><a href="form_sd.php?id=<?= $funcionario['id_clt']; ?>" target="_blank" >Imprimir</a></td>
                            </tr>
                        <?php  } ?>
                        </tbody>
                    </table>
                    <?php }else{ ?>
                    <div class="alert alert-dismissable alert-warning">
                        <!--button type="button" class="close" data-dismiss="alert">�</button-->
                        <strong>N�o h� registros de rescis�es nesta compet�ncia!</strong>
                    </div>
                    <?php } ?>
                </div>
            </div><!-- /.row -->
            <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.container -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <script src="../../js/jquery.maskedinput.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>

        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function() {
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "projeto");
            });
        </script>
    </body>
</html>