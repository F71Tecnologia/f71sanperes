<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../../../conn.php";
include "../../../classes/funcionario.php";
include '../../../classes_permissoes/regioes.class.php';
include("../../../wfunction.php");

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();
exit();
$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

$regiao   = $_GET['id_reg'];
$projeto  = $_GET['pro'];


//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

/*
///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$id_master'");
$optRegiao = array();
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : '';
*/


if(isset($_POST['historico']) ){
    
$ano_calendario = $_POST['ano'];
$master         = $_POST['id_master'];
$qr_historico   = mysql_query("SELECT * FROM dirf WHERE id_master = '$master'  AND status = 1");
$verifica_historico = mysql_num_rows($qr_historico);
}

?>
<html>
    <head>
        <title>Gerar IRRF</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../../js/global.js" type="text/javascript" ></script>
        <script>
            $(function(){
                alert();
                
                /*$('#form').submit(function(){
                       
                 // var checkbox = $('input[name=tipo_arquivo]:checked');
                  alert(checkbox);
                    return false;
                });*/
                    
                
            });
            
        </script>


    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>DIRF</h2>
                    <p>Gerar arquivo de DIRF</p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>DIRF</legend>
                    <div class="fleft">
                        <p><label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                        <p><label class="first">Tipo de arquivo:</label> 
                            <input type="checkbox" name="tipo_arquivo" value="1" <?php echo $checked_clt; ?>/>CLT 
                            <input type="checkbox" name="tipo_arquivo" value="2" <?php echo $checked_autonomo; ?>/>AUTÔNOMO
                            <input type="checkbox" name="tipo_arquivo" value="3" <?php echo $checked_prestador; ?>/>PRESTADOR DE SERVIÇO
                            <span class="erro"><?php if (empty($tipo_arquivo)) {  echo '*Selecione os tipo de arquivo.';  } ?></span>
                        </p>
                    </div>
  
                    <br class="clear"/>
                
                    <p class="controls" style="margin-top: 10px;">
                      <span class="fleft erro"><?php if($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                      <input type="hidden" name="id_master" value="<?php echo $id_master;?>"/>
                        <input type="submit" name="historico" value="Exibir histórico" id="historico"/>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
        </form>
             <?php
                if(!empty($verifica_historico) and isset($_POST['historico'])){
                 
                    while($row_historico = mysql_fetch_assoc($qr_historico)){
                    ?> 
                    <span class="box_download fleft ">
                        <a href="arquivos/<?php echo $row_historico['id_master'].'_'.$row_historico['ano_calendario'].'.txt';?>" style="text-decoration:none;">
                            <img src="../../../imagens/download.png"/>
                            <br>
                            DIRF <?php echo $row_historico['ano_calendario'];?>
                        </a>
                    </span>
                    <?php
                    }                    
                } else {
                    echo '<div class="txcenter">Não existem arquivos de DIRF.</div>';
                    
                }
                ?>  
            <div class="clear"></div>
        </div>
  

</body>
</html>