<?php
#error_reporting(E_ALL);
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');


//ARRAY DE FUNCIONARIOS PARA VISUALIZAR APENAS SERVIÇOS DE TERCEIROS
//178 => MILTON
$funcionario_contabilidade = array(178);

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Desprocessar Prestação de Contas","id_form"=>"form1");

$objPrestacao = new PrestacaoContas();

if (isset($_POST['box']) && !empty($_POST['box']) && isset($_POST['desprocessar'])) {
    foreach ($_POST['box'] as $value) {
        $select = mysql_fetch_assoc(mysql_query("SELECT * FROM prestacoes_contas WHERE id_prestacao = $value LIMIT 1"));
        
        $insert = "INSERT INTO prestacoes_contas_desprocessada VALUES ('', NOW(), '$select[tipo]', '$select[data_referencia]', $select[valor_total], '$select[gerado_em]', $select[gerado_por], $select[id_projeto], $usuario[id_funcionario]);";
        mysql_query($insert);
        $delete = "DELETE FROM prestacoes_contas WHERE id_prestacao = $value LIMIT 1;";
        mysql_query($delete);
    }
    exit;
}

//----- CARREGA PROJETOS COM PRESTAÇÕES FINALIZADAS NO MES SELECIONADO
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    /*-- AND A.id_regiao = '{$_REQUEST['regiao']}' 
        -- AND A.id_projeto = '{$_REQUEST['projeto']}' */
    
    $qr_proFinalizado = "
        SELECT 
            A.id_prestacao,A.id_projeto,DATE_FORMAT(A.data_referencia, '%d/%m/%Y') data_referencia,A.tipo,A.valor_total,DATE_FORMAT(A.gerado_em, '%d/%m/%Y %T') gerado_em,
            B.nome nomeProjeto,C.nome nomeFuncionario
        FROM prestacoes_contas AS A
        LEFT JOIN projeto AS B ON (A.id_projeto = B.id_projeto),
        funcionario AS C
        WHERE   
            A.tipo = '{$_REQUEST['tipo']}' 
            AND Year(data_referencia) = '{$_REQUEST['ano']}' 
            AND Month(data_referencia) = '{$_REQUEST['mes']}'
            AND A.gerado_por = C.id_funcionario
        ORDER BY B.nome ASC";
//            print_r($qr_proFinalizado); exit;
    $qr_proFinalizado = mysql_query($qr_proFinalizado);
    $num_rows = mysql_num_rows($qr_proFinalizado);
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_proFinalizado)) {
            
            $tipo = $objPrestacao->getTiposPrestacoes($row['tipo']);
            
            $tabela .= '
            <tr class="">
                <td style="text-align: center;"><input name="box[]" id="check" class="box" type="checkbox" value="'.$row['id_prestacao'].'"></td>
                <td>'.$row['nomeProjeto'].'</td>
                <td>'.$tipo.'</td>
                <td>'.substr($row['data_referencia'],3).'</td>
                <td>'.number_format($row['valor_total'],2,',','.').'</td>
                <td>'.$row['gerado_em'].'</td>
                <td>'.$row['nomeFuncionario'].'</td>
            </tr>';
        }
    }
}

$meses = mesesArray(null,'');
$anos = anosArray(null, null, array("" => "« Selecione »"));
$tipos = array_merge(array(''=>"« Selecione o Tipo »"), $objPrestacao->getTiposPrestacoes());

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$tipoR = (isset($_REQUEST['tipo'])) ? $_REQUEST['tipo'] : null;

?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: DESPROCESSAR PRESTAÇÃO DE CONTAS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />                            
                
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">        
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />                        
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

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
            body.novaintra table.grid tbody tr:nth-child(even) td{background: #ECECEC;}
            body.novaintra table.grid tbody tr:nth-child(odd) td{background: #FFFFFF;}
        </style>
        <script>
            $(function(){
                $('#filtrar').click(function(){
                    $('.box').removeClass('validate[minCheckbox[1]]');
                    $(".ui-dialog").remove();
                    $("#form1").validationEngine('attach',{
                        onValidationComplete: function(form, status){
                            return status;
                        }  
                    });
                });
                
                $('#desprocessar').click(function(){
                    $('.box').addClass('validate[minCheckbox[1]]');
                    $("#form1").validationEngine('attach',{
                        onValidationComplete: function(form, status){
                            if(status == true){
                                //if(confirm('Deseja realmente desprocessar as prestações selecionadas?') == false)return false;
                                thickBoxConfirm('Desprocessar','Deseja realmente desprocessar a prestação selecionada?','auto','auto',function(data){
                                    if(data == true){
                                        var ar = new Array();
                                        $(".box:checked").each(function(i) {
                                            ar.push($(this).val());
                                        });
                                        $.post("desprocessar_prestacao.php", {bugger:Math.random(), desprocessar:'desprocessar', box:ar}, function(resultado){
                                            //$('#teste').html(resultado);
                                            alert("Prestações desprocessadas!");
                                            window.location.reload();
                                        });
                                    }
                                });
                            }
                            return false;
                        }  
                    });
                });
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
        
        <div id="content">
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                
                <input type="hidden" name="home" id="home" value="" />                                                                
                
                <fieldset>
                    <legend>DESPROCESSAR PRESTAÇÃO DE CONTAS</legend>                                        
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Tipo</label>
                        <div class="col-lg-4">                            
                            <?php if(in_array($_COOKIE['logado'], $funcionario_contabilidade)){ ?>
                                <select id='tipo' name='tipo' class='validate[required] form-control'>
                                    <option value="terceiro">Contrato de Terceiros</option>
                                </select>
                            <?php }else{ ?>
                                <?php echo montaSelect($tipos, $tipoR, "id='tipo' name='tipo' class='validate[required] form-control'") ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group" id="mensal">
                        <label for="select" class="col-lg-2 control-label">Mês</label>
                        <div class="col-lg-4">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'") ?>                                    
                                <span class="input-group-addon">Ano</span>
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'") ?>                                 
                            </div>
                            <p class="help-block">(Mês de Prestação Finalizada)</p>
                        </div>
                    </div>                    
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary" />                            
                        </div>
                    </div>
                </fieldset>
                
            <?php if($num_rows > 0){ ?><br><br>
                <table id="tbRelatorio" class="grid table table-hover table-striped" style="page-break-after:auto;"> 
                <thead>
                    <tr>
                        <th></th>
                        <th>Projeto</th>
                        <th>Tipo</th>
                        <th>Data Referência</th>
                        <th>Valor</th>
                        <th>Gerado Em</th>
                        <th>Gerado Por</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $tabela; ?>
                </tbody>
                </table><br>
                <p class="controls pull-right">
                    <input type="submit" id="desprocessar" class="button btn btn-primary" style="cursor: pointer;" value="DESPROCESSAR SELECIONADOS">                                               
                </p>
                <div style="clear: both;"></div>
            </form>
            <?php }elseif(isset($_REQUEST['filtrar'])){ ?>
                <br>
                <div class="alert alert-danger">
                    Não existe prestação de contas finalizada para o filtro selecionado!
                </p>               
            <?php } ?>
        </div>
        <div id="teste"></div>
        
        <?php include_once '../template/footer.php'; ?>
        
        </div>
    </body>
</html>