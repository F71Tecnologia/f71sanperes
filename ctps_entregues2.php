<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "conn.php";
include "wfunction.php";

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao']; 

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../intranet", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Controle de Carteiras de Trabalho Entregues");
$breadcrumb_pages = array("Controle de Carteiras de Trabalho"=>"ctps2.php"); 
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Controle de Carteiras de Trabalho Entregues</title>
        <link href="favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="css/progress.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Controle de Carteiras de Trabalho Entregues</small></h2></div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="note note-warning">
                        <h4>CONTROLE DE CARTEIRAS  ENTREGUES</h4>
                    </div>
                </div><!-- /.col-lg-12 -->
            </div><!-- /.row -->
            <div class="row">
                <form class="form-horizontal" action="patrimonio.php" method="post" enctype="multipart/form-data" name='form1' onSubmit="return validaForm()" id="form2">
                    <div class="col-lg-12">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="21%">NOME</th>
                                    <th width="15%">RECEBIMENTO</th>
                                    <th width="16%">RECEBIDO POR</th>
                                    <th width="16%">ENTREGUE EM</th>
                                    <th width="17%">ENTREGUE POR</th>
                                    <th width="15%">PREENCHIMENTO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result_carteiras = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cadas, date_format(data_ent, '%d/%m/%Y')as data_ents FROM controlectps where id_regiao = '$id_regiao' and acompanhamento = '2'");
                                if(mysql_num_rows($result_carteiras) > 0){
                                    while ($row_carteiras = mysql_fetch_array($result_carteiras)) {

                                        $result_funci1 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_carteiras[id_user_cad]'");
                                        $row_funci1 = mysql_fetch_array($result_funci1);

                                        $result_funci2 = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row_carteiras[id_user_ent]'");
                                        $row_funci2 = mysql_fetch_array($result_funci2);

                                        switch ($row_carteiras['preenchimento']) {
                                            case 1: $novafrase = "Assinar"; break;
                                            case 2: $novafrase = "Dar Baixa"; break;
                                            case 3: $novafrase = "Férias"; break;
                                            case 4: $novafrase = "13º Salário"; break;
                                            case 5: $novafrase = "Licença"; break;
                                            case 6: $novafrase = "Outros"; break;
                                        } ?>

                                        <tr>
                                            <td><a href=ctps_receber.php?case=2&regiao=<?=$id_regiao?>&id=<?=$row_carteiras[0]?>><?=$row_carteiras[nome]?></a></td>
                                            <td><?=$row_carteiras[data_cadas]?></td>
                                            <td><?=$row_funci1[0]?></td>
                                            <td><?=$row_carteiras[data_ents]?></td>
                                            <td><?=$row_funci2[0]?></td>
                                            <td><?=$novafrase?></td>
                                        </tr>
                                    <?php } 
                                } else { ?>
                                    <tr class="danger">
                                        <td colspan="6">Nenhuma Carteira Entregue!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div><!-- /.col-lg-12 -->
                    <div class="col-lg-12 form-group" style="display:none" id="tablearquivo2">
                        <label class="col-lg-2 control-label">SELECIONE:</label>
                        <div class="col-lg-9">
                            <input name="arquivo2" class="form-control" type="file" id="arquivo2" />
                        </div>
                    </div>
                </form>
            </div><!-- /.row -->
            <?php include_once ('template/footer.php'); ?>
        </div>
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>

        <script language="javascript" src="designer_input.js"></script>
        <script language="javascript"> 
            //o parâmentro form é o formulario em questão e t é um booleano 
            function ticar(form, t) { 
                campos = form.elements; 
                for (x=0; x<campos.length; x++) 
                    if (campos[x].type == "checkbox") 
                        campos[x].checked = t; 
            } 
        </script> 
    </body>
</html>