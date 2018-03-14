<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
}

if (isset($_REQUEST['fechados'])) {
    print_r($_REQUEST['fechados']);
}

include("../conn.php");
include("../wfunction.php");
include("../classes/global.php");

$id_user = $_COOKIE['logado'];

if (empty($_REQUEST['tela'])) {
    $tela = '1';
} else {
    $tela = $_REQUEST['tela'];
}

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$global = new GlobalClass();

function suporte($status, $prioridade) {
    switch ($status) {
        case 1:
//            $imagem = '<img src="imgsuporte/aberto.png" alt="Aberto" title="Aberto" width="18" height="18" />';
            $imagem = '<div href="#" class="btn btn-primary btn-sm bt disabled" data-key="1">ABERTO</div>';
            break;
        case 2:
//            $imagem = '<img src="imgsuporte/respondido.png" alt="Respondido" title="Respondido" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-info btn-sm bt disabled" data-key="2">RESPONDIDO</a>';
            break;
        case 3:
//            $imagem = '<img src="imgsuporte/replicado.png" alt="Replicado" title="Replicado" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-warning btn-sm bt disabled" data-key="3">REPLICADO</a>';
            break;
        case 4:
//            $imagem = '<img src="imgsuporte/finalizado.png" alt="Fechado" title="Fechado" width="18" height="18" />';
            $imagem = '<a href="#" class="btn btn-danger btn-sm bt disabled" >FECHADO</a>';
            break;
    }

    switch ($prioridade) {
        case 1:
            $prioridade_cor = '#5cb85c';
            $prioridade_nome = 'Baixa';
            break;
        case 2:
            $prioridade_cor = '#f0ad4e';
            $prioridade_nome = 'Média';
            break;
        case 3:
            $prioridade_cor = '#ffc266';
            $prioridade_nome = 'Alta';
            break;
        case 4:
            $prioridade_cor = '#d9534f';
            $prioridade_nome = 'Urgente';
            break;
    }

    $retorno = array($prioridade_cor, $prioridade_nome, $imagem);
    return $retorno;
}

$tipos_suporte = array('1' => 'INFORMAÇÃO', '2' => 'RECLAMAÇÃO', '3' => 'INCLUSÃO', '4' => 'EXCLUSÃO', '5' => 'ERRO', '6' => 'SUGESTÃO', '7' => 'ALTERAÇÃO');

$breadcrumb_config = array("nivel" => "../", "key_btn" => "6", "area" => "Sistema", "id_form" => "form1", "ativo" => "Suporte");
$breadcrumb_pages = array();

//$ObjFunc = new FuncionarioClass();
//$funcionarios = $ObjFunc->listFuncionariosAtivos();
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Suporte</title>
        <link href="../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <!--<link href="../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">-->
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--<link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />-->
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>

        <div class="container">
            <div class="page-header box-sistema-header"><h2><span class="glyphicon glyphicon-phone"></span> - Sistema<small> - Suporte</small></h2></div>
            
            <div class="legenda margin_b20" >
                <?php if ($tela == '1') { ?>
<!--                <div class="col-sm-4">
                    <select class="form-control" id="chamados" name="chamados">
                        <option value="1">ABERTO</option>
                        <option value="2">RESPONDIDO</option>
                        <option value="3">REPLICADO</option>
                        <option value="4">FECHADO</option>
                        <option value="0">TODOS OS CHAMADOS FECHADOS</option> 
                   </select>
                </div>-->
                <div class="col-sm-2">
                    <a href="admsuporte.php" class="btn btn-info btn btn-sm">Voltar Para Suporte</a>
<!--                    <a href="javascript:;" class="j_visualizarFechado btn btn-danger btn btn-sm" target="_blank">Chamados Fechados</a>-->
                </div>
                    <?php
//                       if(isset($_GET['dev'])){
//                        $total_aberto = 0;
//                        $total_respondido = 0;
//                        $total_replicado = 0;
                        $total_fechado = 0;
                        $sql_sup = "SELECT * FROM suporte WHERE status = 4";
                        $query_suporte = mysql_query($sql_sup);
                        while($row_suporte = mysql_fetch_array($query_suporte)){
                            if($row_suporte['status']==4){
                                $total_fechado++;
                            }
                        } 
                       ?>
                       <div class="margin_t10">
<!--                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total abertos: <span class="badge"><?= $total_aberto; ?> </span></a></div>
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total respondidos: <span class="badge"> <?= $total_respondido; ?> </span></a></div>
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total replicados: <span class="badge"><?= $total_replicado; ?> </span></a></div>-->
                            <div class="bt btn btn-default btn-xs"><a href="javascript:;">Total fechados: <span class="badge"> <?= $total_fechado; ?> </span></a></div>
                       </div> 
                <?php  } ?>
            </div>
            
                <div class="panel panel-default">
                    <div class="panel-heading">Painel de Suporte</div>
                        <div class="panel-body">
                          <?php if ($tela == '1') { //LISTAGEM DE CHAMADOS?>
                            
                     <table class="table table-bordered text-sm valign-middle j_mostrar" >

                    <?php
                    foreach ($tipos_suporte as $tipo_id => $tipo_nome) {
                       

                            $filtro1 = "WHERE sup.status = '4'";


                        $qr_suporte = mysql_query("SELECT sup.id_suporte, sup.assunto, sup.prioridade, sup.status, func.nome1, reg.regiao, date_format(sup.data_cad, '%d/%m/%Y às %H:%i') AS data_cad
							  	   FROM suporte sup
						     INNER JOIN funcionario func ON func.id_funcionario = sup.user_cad
						      LEFT JOIN regioes reg      ON reg.id_regiao = sup.id_regiao
							            $filtro1");
                        $total_suporte = mysql_num_rows($qr_suporte);


                        if (!empty($total_suporte)) {
                            ?>
                            <tr> 
                                <td colspan="7"> <?= $tipo_nome ?> (<?= $total_suporte ?> chamados)  </td>
                            </tr>
                            <tr>
                                <td >Chamado</td>
                                <td >Assunto</td>
                                <td >Aberto Por</td>
                                <td >Data de Abertura</td>
                                <td >Prioridade</td>
                                <td >Situação</td>
                            </tr>

                            <?php
                            while ($row_suporte = mysql_fetch_array($qr_suporte)) {

                                $suporte = suporte($row_suporte['status'], $row_suporte['prioridade']);
                                ?>

                                <tr class="mutacao linha_<?php
//                                if ($linha++ % 2 == 0) {
//                                    echo 'um';
//                                } else {
//                                    echo 'dois';
//                                }
                                ?>" data-type="<?= $row_suporte['status'] ?>">
                                    <td ><a href="admsuporte.php?tela=2&chamado=<?= $row_suporte['id_suporte'] ?>" title="Abrir o chamado"><?= sprintf('%04d', $row_suporte['id_suporte']) ?></a></td>
                                    <td><?= $row_suporte['assunto'] ?></td>
                                    <td><?= $row_suporte['nome1'] ?></td>
                                    <td><?= $row_suporte['data_cad'] ?></td>
                                    <td style="background-color:<?= $suporte[0] ?>;"><?= $suporte[1] ?></td>
                                    <td><?= $suporte[2] ?></td>
                                </tr>
                                

                                <?php
//                               
                                
                                }
                        }
                    }
                    ?>

                </table>
                
                          <?php } ?>
                            
   
                            
                    </div> <!--fechamento panel-body-->
                </div> <!--fechamento panel-default-->
         
            
            <?php include("../template/footer.php"); ?>
        </div><!-- /.container -->

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../resources/dropzone/dropzone.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <!--<script src="../resources/js/sistema/gestao_portal/index.js" type="text/javascript"></script>-->
        
        <script>
            jQuery(document).ready(function(){
                jQuery("#chamados").change(function(){
                    var status = jQuery(this).val();
                    if(status!="todos"){
                        jQuery(".mutacao").addClass("hidden");
                        jQuery(".mutacao[data-type="+status+"]").removeClass("hidden");
                    }else{
                        jQuery(".mutacao").removeClass("hidden");
                    }
                });
                
//                jQuery(".fechado").hide();                
                //jQuery(".j_mostrar").hide();              
                
//                jQuery(".j_visualizarFechado").click(function(){
//                    jQuery(this).text("Fechar Visualização");
//                    jQuery(".j_mostrar").show(1,function(){
//                        jQuery(".fechado").toggle(1, function(){
//                            jQuery("body").scrollTop(1350);
//                        }); 
//                        exit;
//                    });  
//                });                
            });
            
        </script>

    </body>
</html>