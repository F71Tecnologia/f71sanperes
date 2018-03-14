<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/FolhaClass.php");

$usuario = carregaUsuario();

//PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "41", "area" => "Recursos Humanos", "ativo" => "Folha Digitada", "id_form" => "form1");
$breadcrumb_pages = array("Gestão de RH"=>"../../rh/principalrh.php");

if(isset($_REQUEST['salvar'])){
    
    $clts = $_REQUEST['id_clt'];
    $id_folha = $_REQUEST['id_folha'];
    
    $campos = array("inss","fgts","imprenda","salliquido","valor_pis");
    $camposPadrao = array(
                        "salbase"       =>  "salliquido",
                        "sallimpo"      =>  "salliquido",
                        "sallimpo_real" =>  "salliquido"
                        //"base_inss"     =>  "salliquido",
                        //"base_irrf"     =>  "salliquido"
                    );
    $valoresFixos= array("dias_trab"=>"30");
    $arrayInsert = array();
    
    foreach($clts as $val){
        $clt[$val]['id_clt'] = $val;
        
        foreach($campos as $campo){
            $clt[$val][$campo] = str_replace(",",".",str_replace(".","",$_REQUEST[$campo][$val]));
        }
        
        foreach($camposPadrao as $kCP => $cp){
            $clt[$val][$kCP] = str_replace(",",".",str_replace(".","",$_REQUEST[$cp][$val]));
        }
        
    }
    
    //MONTA QUERY UPDATE
    //UPDATE rh_folha_proc SET `dias_trab`='30', `salbase`=9000, `sallimpo`=9000, `sallimpo_real`=9000, `inss`=3000, `imprenda`=40000, `fgts`=800, `salliquido`=5676567, `base_inss`=80000 WHERE  `id_folha_proc`=24426;
    
    foreach($clt as $id_clt => $valor){
        $update = "UPDATE rh_folha_proc SET ";
        
        foreach($valor as $k => $campos){
            $update .= " {$k}='{$campos}', ";
        }
        
        $update = substr($update,0,-2);
        
        $update .= " WHERE `id_folha`={$id_folha} AND id_clt='{$id_clt}'; \r\n";
        
        mysql_query($update)or die("Erro Mysql {$update}: ".  mysql_error());
    }
    
    header("location: folha_digitada.php");
    exit;
}

if(isset($_REQUEST['finalizar'])){
    $id_folha = $_REQUEST['id_folha'];
    
    $sqlUpdateFolha = "UPDATE rh_folha SET `status` = 3 WHERE id_folha = {$id_folha}; \r\n";
    $sqlUpdateFolhaProc = "UPDATE rh_folha_proc SET `status`=3 WHERE id_folha = {$id_folha};";
    mysql_query($sqlUpdateFolha) or die("Erro ao fechar a folha (<!--{$sqlUpdateFolha}-->): ".  mysql_error());
    mysql_query($sqlUpdateFolhaProc) or die("Erro ao fechar a folha (<!--{$sqlUpdateFolha}-->): ".  mysql_error());
    
    header("location: folha_digitada.php");
    exit;
    
}

$id_folha = $_REQUEST['folha'];
$condicao = "id_folha = {$id_folha}";
$dadosFolha = montaQueryFirst("rh_folha", "*", $condicao);

$st = ($dadosFolha['status']==3)?3:1;

$sql_participante = "SELECT A.*, B.valor_hora, C.nome as funcao,C.valor
                        FROM rh_folha_proc AS A
                        LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                        LEFT JOIN curso AS C ON (B.id_curso = C.id_curso)

                        WHERE A.id_folha = {$id_folha} AND A.status = {$st};";

$dadosParticipantesFolha = execQuery($sql_participante);

$rly = ($dadosFolha['status']==3)?" readonly ":"";

//SETANDO VARIAVEIS DE TOTAIS
$totalLiq = 0;

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet ::</title>

        <link rel="shortcut icon" href="favicon.ico" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>

        <div class="container">
            <form action="" method="post" name="form1" id="form1">
                <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Folha de PGTO Digitada</small></h2></div>

                <div class="bs-callout bs-callout-danger" id="callout-helper-pull-navbar"> 
                    <h4>Folha de PGTO digitada - <?php echo $dadosFolha['mes']."/".$dadosFolha['ano']; ?></h4> 
                    <p>Folha Numero: <code><?php echo $dadosFolha['id_folha'] ?></code></p>
                    <input type="hidden" name="id_folha" value="<?php echo $dadosFolha['id_folha'] ?>" />
                </div>
                <!-- NSS / FGTS / IRRF / PIS / PENSAO -->
                <table id="tbRelatorio" class="table table-striped table-hover text-sm valign-middle">
                    <thead>
                        <tr>
                            <th>COD.</th>
                            <th>NOME</th>
                            <th>VALOR LIQUIDO</th>
                            <!--th>VALOR BASE(INSS)</th-->
                            <th>INSS</th>
                            <th>IRRF</th>
                            <th>FGTS</th>
                            <th>PIS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dadosParticipantesFolha as $clt){  $totalLiq += $clt['salliquido'];  ?>
                        <tr>
                            <td>
                                <?php echo $clt['id_clt'] ?>
                                <input type="hidden" name="id_clt[]" value="<?php echo $clt['id_clt'] ?>" />
                            </td>
                            <td><?php echo $clt['nome'] ?></td>
                            <td><input type="text" name="salliquido[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['salliquido'],2,",",".") ?>" class="form-control input-sm valor" <?php echo $rly ?> /></td>
                            <!--td><input type="text" name="base_inss[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['base_inss'],2,",",".") ?>" class="form-control input-sm valor b_inss" /></td-->
                            <td><input type="text" name="inss[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['inss'],2,",",".") ?>" class="form-control input-sm valor" <?php echo $rly ?> /></td>
                            <td><input type="text" name="imprenda[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['imprenda'],2,",",".") ?>" class="form-control input-sm valor" <?php echo $rly ?> /></td>
                            <td><input type="text" name="fgts[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['fgts'],2,",",".") ?>" class="form-control input-sm valor" <?php echo $rly ?> /></td>
                            <td><input type="text" name="valor_pis[<?php echo $clt['id_clt'] ?>]" value="<?php echo number_format($clt['valor_pis'],2,",",".") ?>" class="form-control input-sm valor" <?php echo $rly ?> /></td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>


                <p class="pull-right">
                    <button type="button" name="voltar" id="voltar" class="btn btn-danger" onclick="javascript:window.location='folha_digitada.php';"><i class="fa fa-arrow-left"></i> Voltar</button>
                    <?php if($dadosFolha['status']!=3){?>
                    <button type="submit" name="salvar" id="salvar" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <?php if($totalLiq>0){?>
                    <button type="submit" name="finalizar" id="finalizar" class="btn btn-warning"><i class="fa fa-lock"></i> FINALIZAR</button>
                    <?php } ?>
                    <?php } ?>
                </p>
            </form>
            <?php include('../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
            
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney.js"></script>
        <!--script src="../../resources/js/rh/folha_oo/main.js"></script-->
        <script>
            $(function() {
                $(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                $(".b_inss").blur(function(){
                    var base_inss = $(this).val().replace('.', '').replace(',', '.');
                    
                    //console.log(base_inss);
                    
                });
                
            });

            
        </script>
    </body>
</html>
