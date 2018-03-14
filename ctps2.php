<?php
if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "conn.php";
include "wfunction.php";

$usuario = carregaUsuario();

$id = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : 1;
$id_user = $usuario['id_funcionario'];
$id_regiao = $usuario['id_regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$id_regiao'");
$row_local = mysql_fetch_array($result_local);

if(empty($_REQUEST['nome'])){

    if(empty($_REQUEST['clt'])){
	$clt = "0";
	$nome = $numero = $serie = $uf = "";
    }else{
	$clt = $_REQUEST['clt'];
	$result_clt = mysql_query("SELECT * FROM rh_clt WHERE id_clt = '$clt'");
	$row_clt = mysql_fetch_array($result_clt);
	$nome = "$row_clt[nome]";
	$numero = "$row_clt[campo1]";
	$serie = "$row_clt[serie_ctps]";
	$uf = "$row_clt[uf_ctps]";
    } 
    
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $breadcrumb_config = array("nivel"=>"../intranet", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Controle de Carteiras de Trabalho");
    if($_REQUEST[caminho] == 1){
        $breadcrumb_config = array("nivel"=>"../intranet", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Controle de Carteiras de Trabalho");
        $breadcrumb_pages = array("Lista Projetos" => "ver2.php", "Visualizar Projeto" => "javascript:void(0);", "Lista Participantes" => "javascript:void(0);", "Visualizar Participante" => "javascript:void(0);");
        $breadcrumb_attr = array(
            "Visualizar Projeto" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='ver2.php'",
            "Lista Participantes" => "class='link-sem-get' data-projeto='{$row['id_projeto']}' data-form='form1' data-url='bolsista2.php'",
            "Visualizar Participante" => "class='link-sem-get' data-pro='{$row['id_projeto']}' data-clt='$clt' data-form='form1' data-url='rh/ver_clt2.php'"
        );
    }?>
    
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: Controle de Carteiras de Trabalho</title>
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
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Controle de Carteiras de Trabalho</small></h2></div>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="note note-warning">
                            <h4>DADOS DA CARTEIRA RECEBIDA<br>
                                <strong><?=$row_local['regiao']?></strong> - Data de Recebimento <strong><?=$data?></strong>
                            </h4>
                        </div>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <form class="form-horizontal" action="ctps.php" enctype="multipart/form-data" method="post" name='form1' id="form1">
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Nome:</label>
                                <div class="col-lg-9">
                                    <input class="form-control" name="nome" type="text" id="nome" value='<?=$nome?>'>
                                </div>
                            </div><!-- /.form-group -->
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Número:</label>
                                <div class="col-lg-4">
                                    <input class="form-control" name="numero" type="text" id="numero" value='<?=$numero?>'>
                                </div>
                                <label class="col-lg-1 control-label">Série:</label>
                                <div class="col-lg-2">
                                    <input class="form-control" name="serie" type="text" id="serie" value='<?=$serie?>'>
                                </div>
                                <label class="col-lg-1 control-label">UF:</label>
                                <div class="col-lg-1">
                                    <input class="form-control" name="uf" type="text" id="uf" maxlength="2" value='<?=$uf?>'>
                                </div>
                            </div><!-- /.form-group -->
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Observações:</label>
                                <div class="col-lg-9">
                                    <input class="form-control" name="obs" type="text" id="obs">
                                </div>
                            </div><!-- /.form-group -->
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Preenchimento:</label>
                                <div class="col-lg-2">
                                    <div class="radio">
                                        <label>
                                            <input name="preenchimento" type="radio" id="preenchimento" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;" value="1" checked>
                                            Assinar
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="preenchimento" id="preenchimento2" value="2" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;"> 
                                            Dar Baixa</label>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="preenchimento" id="preenchimento3" value="3" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                                            Férias
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="preenchimento" id="preenchimento4" value="4" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                                            13º Salário
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="preenchimento" id="preenchimento5" value="5" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? 'none' : 'none' ;">
                                            Licença
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="preenchimento" id="preenchimento6" value="6" onClick="document.all.linha.style.display = (document.all.linha.style.display == 'none') ? '' : '' ;" > 
                                            Outros
                                        </label>
                                    </div>
                                </div>
                            </div><!-- /.form-group -->
                            <div class="form-group" id="linha" style="display:none">
                                <label class="col-lg-2 control-label">Descreva:</label>
                                <div class="col-lg-9">
                                    <input class="form-control" name="obs_preenchimento" type="text" id="obs_preenchimento">
                                </div>
                            </div><!-- /.form-group -->

                            <div class="form-group" style="display:none" id="tablearquivo">
                                <label class="col-lg-2 control-label">SELECIONE:</label>
                                <div class="col-lg-9">
                                    <input class="form-control" name="arquivo" type="file" id="arquivo" />
                                </div>
                            </div><!-- /.form-group -->
                            <hr>
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-1 text-center">
                                    <input type="hidden" name="home" id="home">
                                    <input type="hidden" value="<?=$id_regiao?>" name="regiao">
                                    <input type="submit" class="btn btn-success" name="gerar" id="gerar" value="GERAR PROTOCOLO DE RECEBIMENTO">
                                </div>
                            </div><!-- /.form-group -->
                        </form>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <hr>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="note note-warning">
                            <h4>CONTROLE DE CARTEIRAS A SEREM ENTREGUES</h4>
                        </div>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="14%">RECEBIMENTO</th>
                                    <th width="17%">NOME</th>
                                    <th width="15%">NÚMERO</th>
                                    <th width="15%">SERIE</th>
                                    <th width="10%">UF</th>
                                    <th width="15%">PREENCHIMENTO</th>
                                    <th width="14%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $result_carteiras = mysql_query("SELECT *,date_format(data_cad, '%d/%m/%Y')as data_cadas FROM controlectps where id_regiao = '$id_regiao' and acompanhamento = '1'");
                                if(mysql_num_rows($result_carteiras) > 0){
                                    while($row_carteiras = mysql_fetch_array($result_carteiras)){
                                        switch($row_carteiras['preenchimento']){
                                            case 1: $novafrase = "Assinar"; break;
                                            case 2: $novafrase = "Dar Baixa"; break;
                                            case 3: $novafrase = "Férias"; break;
                                            case 4: $novafrase = "13º Salário"; break;
                                            case 5: $novafrase = "Licança"; break;
                                            case 6: $novafrase = "Outros"; break;
                                        } ?>
                                        <tr>
                                            <td><?=$row_carteiras[data_cadas]?></td>
                                            <td><a href=ctps_receber.php?case=2&regiao=<?=$id_regiao?>&id=<?=$row_carteiras[0]?>><?=$row_carteiras[nome]?></a></td>
                                            <td><?=$row_carteiras[numero]?></td>
                                            <td><?=$row_carteiras[serie]?></td>
                                            <td><?=$row_carteiras[uf]?></td>
                                            <td><?=$novafrase?></td>
                                            <td><a class="btn btn-primary" href=ctps_entregar.php?case=1&regiao=<?=$id_regiao?>&id=<?=$row_carteiras[0]?>>Entregar</a></td>
                                        </tr>
                                    <?php } 
                                } else { ?>
                                    <tr class="danger">
                                        <td colspan="7">Nenhuma Carteira a Ser Entregue!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <a class="btn btn-default" href="ctps_entregues.php?regiao=<?=$id_regiao?>" target="_blank"><img src="imagens/ctpsentregues.gif" alt="" width="120" height="30" border="0"></a>
                    </div><!-- /.col-lg-12 -->
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
<?php
}else{ // AKI VAI RODAR O CADASTRO

    $id_regiao = $_REQUEST['regiao'];
    $nome = $_REQUEST['nome'];
    $numero = $_REQUEST['numero'];
    $serie = $_REQUEST['serie'];
    $uf = $_REQUEST['uf'];
    $obs = $_REQUEST['obs'];
    $preenchimento = $_REQUEST['preenchimento'];
    $obs_preenchimento = $_REQUEST['obs_preenchimento'];

    $id_user = $_COOKIE['logado'];
    $data_cad = date('Y-m-d');

    mysql_query("INSERT INTO controlectps(id_regiao,id_user_cad,nome,numero,serie,uf,obs,preenchimento,obs_preenchimento,data_cad) values
    ('$id_regiao','$id_user','$nome','$numero','$serie','$uf','$obs','$preenchimento','$obs_preenchimento','$data_cad')")or die ("<hr>Erro no insert<br><hr>".mysql_error());

    $row_id = mysql_insert_id();

    print "
    <script>
    alert (\"Informações gravadas com sucesso\");
    location.href=\"ctps_receber.php?regiao=$id_regiao&id=$row_id&case=1\"
    </script>
    ";
}
/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
//mysql_close($conn);
?>