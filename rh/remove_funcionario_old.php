<?php
#error_reporting(E_ALL);
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('../classes/pdf/fpdf.php');
include('../classes/mpdf54/mpdf.php');
include('../classes/imageToPdf.php');
require('../classes/fpdfi/fpdi.php');
include('PrestacaoContas.class.php');



$usuario = carregaUsuario();


///MASTER
$master = montaQuery('master', "id_master,razao", "status =1");
$optMaster = array();
foreach ($master as $valor) {
    $optMaster[$valor['id_master']] = $valor['id_master'] . ' - ' . $valor['razao'];
}
$masterSel = (isset($_REQUEST['master'])) ? $_REQUEST['master'] : $usuario['id_master'];
$optRegiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : null;
$optProjeto = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;

$master = $usuario['id_master'];
$mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos
$nome_meses = array("01" => "Janeiro", "02" => "Fevereiro", "03" => "MarÁo", "04" => "Abril", "05" => "Maio", "06" => "Junho", "07" => "Julho", "08" => "Agosto", "09" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
$erros = 0;
$idsErros = array();
$msg = "";

if (isset($_REQUEST) && !empty($_REQUEST) && $_REQUEST['buscar'] == "Buscar") {
    $projeto = $_REQUEST['projeto'];
    $regiao= $_REQUEST['regiao'];
    $tipoContratacao = $_REQUEST['tipoContratacao'];
    if ($tipoContratacao == 2 ) {
        $qr = mysql_query("SELECT A.*,B.nome AS projetos 
                        FROM rh_clt AS A 
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                        WHERE A.id_projeto = '{$projeto}' AND A.id_regiao = '{$regiao}' AND A.tipo_contratacao = '{$tipoContratacao}'
                        ORDER BY A.nome
                        ");                        
    } else {
        $qr = mysql_query("SELECT A.*,B.nome AS projetos 
                        FROM autonomo AS A 
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN regioes AS C ON(A.id_regiao = C.id_regiao)
                        WHERE A.id_projeto = '{$projeto}' AND A.id_regiao = '{$regiao}' AND A.tipo_contratacao = '{$tipoContratacao}'                        
                        ORDER BY A.nome
                        ");
                        
    }
}

class concat_pdf extends FPDI {

    var $files = array();

    function setFiles($files) {
        $this->files = $files;
    }

    function concat() {
        foreach ($this->files AS $file) {
            $ext = end(explode(".", $file));
            if (is_file($file) && $ext == "pdf") {
                $pagecount = $this->setSourceFile($file);
                for ($i = 1; $i <= $pagecount; $i++) {
                    $tplidx = $this->ImportPage($i);
                    $s = $this->getTemplatesize($tplidx);
                    $this->AddPage($s['w'] > $s['h'] ? 'L' : 'P', array($s['w'], $s['h']));
                    @$this->useTemplate($tplidx);
                }
            }
        }
    }

}

function normalizaNome($variavel) {
    $variavel = strtoupper($variavel);
    if (strlen($variavel) > 200) {
        $variavel = substr($variavel, 0, 200);
        $variavel = $variavel[0];
    }
    $nomearquivo = preg_replace("/ /", "_", $variavel);
    $nomearquivo = preg_replace("/[\/]/", "", $nomearquivo);
    $nomearquivo = preg_replace("/[¡¿¬√]/i", "A", $nomearquivo);
    $nomearquivo = preg_replace("/[·‡‚„™]/i", "a", $nomearquivo);
    $nomearquivo = preg_replace("/[…» ]/i", "E", $nomearquivo);
    $nomearquivo = preg_replace("/[ÈËÍ]/i", "e", $nomearquivo);
    $nomearquivo = preg_replace("/[ÕÃŒ]/i", "I", $nomearquivo);
    $nomearquivo = preg_replace("/[ÌÏÓ]/i", "i", $nomearquivo);
    $nomearquivo = preg_replace("/[”“‘’]/i", "O", $nomearquivo);
    $nomearquivo = preg_replace("/[ÛÚÙı∫]/i", "o", $nomearquivo);
    $nomearquivo = preg_replace("/[⁄Ÿ€]/i", "U", $nomearquivo);
    $nomearquivo = preg_replace("/[˙˘˚]/i", "u", $nomearquivo);
    $nomearquivo = str_replace("«", "C", $nomearquivo);
    $nomearquivo = str_replace("Á", "c", $nomearquivo);

    return $nomearquivo;
}

function copiarArquivo($file, $novoNome) {
    $folderSave = dirname(__FILE__) . "/arquivos/";
    $extAr = explode(".", $file);
    $ext = end($extAr);
    if (is_file($file)) {
        if (!copy($file, $folderSave . $novoNome . "." . $ext))
            echo "erro ao copiar o arquivo de: {$file} <br/> PARA: " . $folderSave . $novoNome . "." . $ext;exit;
    }else {
        echo "erro ao copiar o arquivo(n„o existe): {$file}";
        exit;
    }
    return true;
}

/**/

//----- CARREGA PROJETOS COM PRESTA«’ES FINALIZADAS NO MES SELECIONADO

$meses = mesesArray(null);
$anos = anosArray(null, null, array("-1" => "´ Selecione ª"));

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMUL¡RIO SELECIONADO */
$projR = (isset($_REQUEST['projetos'])) ? $_REQUEST['projetos'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
?>
<html>
    <head>
        <title>:: Intranet :: Remover Funcion·rio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
         $(function(){     
             
     
         $('#master_').change(function(){	
                var id_master = $(this).val();
                  $('#regiao_').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({                        
                        url : '../action.global.php?master='+id_master,
                      
                        success :function(resposta){			
                                        $('#regiao_').html(resposta);
                                        $('#regiao_').next().html('');
                                }		
                        });
                 
                  $('#regiao_').trigger('change')
                });	
       
        
        
        $('#regiao_').change(function(){	
                var id_regiao = $(this).val();
              
                $('#projeto_').next().html('<img src="../img_menu_principal/loader16.gif"/>');
                $.ajax({		
                        url : '../action.global.php?regiao='+id_regiao,                        
                        success :function(resposta){			
                                        $('#projeto_').html(resposta);	
                                        $('#projeto_').next().html('');        
                                    }		
                        });
                
                        
                });	
                
          $('#master_').trigger('change');      
             
        });  
        </script>
        <script>
jQuery

        $(document).ready(function(){

                $(".delete").click(function(){
                    var deletar = confirm("Deseja Excluir Realmente?");
                      if(deletar == true){
                          
                      var del_id = $(this).attr('id');
                                console.del_id;
                                $.ajax({
                                    type:'POST',
                                    url:'ajax-delete.php',
                                    data: 'delete_id='+del_id,
                                    success:function(data) {
                                        if(data) {} 
                                        else {}   
                                    }
                            }); 
                      }
        });
    });
</script>
        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
            .table_069{
                width: 100%;
                border: 1px solid #ccc;
            }
            .table_069 tr {
                border: 1px solid #333;
                background: #eee;
            }
            .table_069 th{
                padding: 7px;
                background: #ccc;
                color: #000;
                border: 0px solid #ccc;
            }
            .table_069 td{
                padding: 5px;
                font-family: Calibri;
                font-size: 11px;
                text-transform: uppercase;
            }
            .table_069 h3{
                font-family: Calibri;
                font-size: 22px;
                text-transform: uppercase;
                text-align: center;
                color: #666;
                margin: 0px; 
                padding: 10px ; 
            }
        </style>

        <script>
            $(function(){
                $("#form1").validationEngine();
                
            });
            
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="projSel" id="projSel" value="<?php echo $projR ?>" />
                <h2>Remover Funcion·rio CLTs / AutonÙmos</h2>
                <fieldset>
                    <legend>Dados</legend>
                    <p id="unidade">
                        <div class="fleft">
                            <p><label class="first">Master:</label> <?php echo montaSelect($optMaster, $masterSel, array('name' => "master", 'id' => 'master_')); ?></p>
                            <p><label class="first">Regi„o:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao_')); ?> <span class="loader"></span></p>                        
                            <p><label class="first">Projeto:</label> <?php echo montaSelect($optProjeto, $projetoSel, array('name' => "projeto", 'id' => 'projeto_')); ?><span class="loader"></span></p>
                            <p><label class="first">Tipo de ContrataÁ„o:</label> 
                                <select name="tipoContratacao" id="tipoContratacao">
                                    <option>Selecione</option>
                                    <?php
                                        $qrTipo = mysql_query("SELECT * FROM tipo_contratacao");
                                        while ($rsTipo = mysql_fetch_array($qrTipo)) { ?>
                                            <option value="<?php echo $rsTipo['tipo_contratacao_id'];  ?>">
                                                <?php echo $rsTipo['tipo_contratacao_nome'];  ?>
                                            </option>
                                        <?php }
                                    ?>
                                </select>
                                <span class="loader"></span></p>
                        </div>
                    </p>
                    <p class="controls">
                        <input type="submit" id="buscar" class="button" value="Buscar" name="buscar" />
                    </p>
                </fieldset>
                <?php //echo $msg; ?>
            </form>
            <?php if (mysql_num_rows($qr) > 0) { ?>
                <table  border='0' cellpadding="0" class="table_069" id="forcaTrabalho">
                    <thead>
                    <tr>
                        <th>NOME</th>
                        <th>CPF</th>
                        <th>PROJETO</th>
                        <th>A«√O</th>                   
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    
                    while ($linha_result = mysql_fetch_assoc($qr)) { 
                        if($tipoContratacao == 2){
                            $idFunc = $linha_result['id_clt'];
                        } else {
                            $idFunc = $linha_result['id_autonomo'];
                        }
                        
                        ?>     
                        <tr>
                            <td><?php echo $linha_result['nome']; ?><!--input type="hidden" value="<?php echo $linha_result['id_clt']; ?>"/--></td>
                            <td><?php echo $linha_result['cpf']; ?></td>
                            <td><?php echo $linha_result['projetos']; ?></td>
                            <td>
                                <img src="../imagens/deletar_usuario.gif" id="<?php echo $idFunc; ?>" class="deletar delete"/>
                                <input type="hidden" name="tipoContratacao" id="tipoContratacao" value="<?php echo $linha_result['tipo_contratacao']; ?>"/>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                    
                </table>
            <?php } else {
                echo "<p>Nenhum resultado encontrado</p>";
            } ?>  
        </div>
    </body>
</html>