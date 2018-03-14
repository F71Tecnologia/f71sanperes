<?php

/* 
 * Criado em 16-05-2016 Ramon Lima
 * Arquivo para calcular e lançar o dissidio da categoria para as funções que estiverem vinculadas ao Sindicato
 */

/**
 * Itens apontados pelo Sabino para funcionamento da funcionalidade
 *  
1 - quais os cargos para cada sindicato **
2 - quais os valores de cada cargo **
3 - quais os pisos de cada cargo **
4 - qual o percentual a ser aumentado (valores de > ATE > )
5 - valor aumentado
6 - valor do piso aumentado
7 - diferença entre valor anterior e valor aumentado

OBS:
valor de 1000 a 2000 será aplicado a 7%
valor de 2001 a 6000 será aplicado a 5%
valor de 6001 a 9999 será aplicado a 2,5%
 */

if (empty($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}

include("../../conn.php");
include("../../classes/SindicatoClass.php");
include("../../classes/FuncoesClass.php");
include("../../wfunction.php");
include "../../classes/LogClass.php";
$log = new Log();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$objFuncoes = new FuncoesClass();
$sindicato = getSindicatoID($_REQUEST['sindicato']);

if(validate($_REQUEST['method']) && $_REQUEST['method'] == "calcular"){
    $arrayResult = array();
    
    /*echo "<pre>";
    print_r($_REQUEST);
    echo "<pre>";
    exit;*/
    
    $qntFaixas = count($_REQUEST['percent']);
    $funcoes = getFuncoesSindicato($_REQUEST['sindicato']);
    
    $percentuais = $_REQUEST['percent'];
    $valores_ini = $_REQUEST['valor_de'];
    $valores_fim = $_REQUEST['valor_ate'];
    $rhsalario = null;
    $rhcursoup = null;
    
    $arrayCursosNovo = array();
    while ($row_curso = mysql_fetch_assoc($funcoes)) {
        //VAMOS AS REGRAS
        if($qntFaixas == 1){
            $percent = str_replace(",",".",$percentuais[0]);
            if (!$percent) {
                echo "<p><span style='color:red'>Por favor preencha o campo 'Percentual'.</span></p>";
                exit;
            }
            $dif = $row_curso['salario'] * $percent;
            if (!$dif) {
                echo "<p><span style='color:red'>Não há diferença nos valores.</span></p>";
                exit;
            }
            $arrayResult[$row_curso['id_curso']]['diferenca'] = $dif;
            $arrayResult[$row_curso['id_curso']]['valor_old'] = $row_curso['salario'];
            $arrayResult[$row_curso['id_curso']]['novo_valor'] = $row_curso['salario'] + $dif;
            $arrayResult[$row_curso['id_curso']]['percentual'] = $percent;
            $row_curso['valor'] = $row_curso['valor'] + $dif;
            $row_curso['nome'] = utf8_encode($row_curso['nome']);
        }else{
            
            /*foreach($_REQUEST['percent'] as $i => $val){
                
                if($row_curso['valor'] >= $valores_ini[$i] && $row_curso['valor'] <= $valores_fim[$i]){
                    //PAREIA KI
                    //ESTOU RODANDO OS PERIODOS PARA SABER QUAL O VALOR SE ENCAIXA ESSA FUNÇÃO
                    //ACHANDO JA APLICO
                }
                
            }*/
            
        }
        
        $arrayCursosNovo[$row_curso['nome']][$row_curso['numero']] = $row_curso;
    }
    
    if($qntFaixas >= 1){
        $diaAlteracao = date('Y-m-d');
        $folder = dirname(__FILE__) . "/arquivos/";
        $fname = date("Ymd_His")."_".$usuario['id_funcionario'].".txt";
        $filename = $folder . $fname;
        
        $handle = fopen($filename, "w");
        
        $headerInsert = "INSERT INTO `rh_salario` (`id_curso`, `data`, `salario_antigo`, `salario_novo`, `diferenca`, `user_cad`, `motivo`) VALUES ";
        
        fwrite($handle, $headerInsert);
        //MONTANDO INSERT DAS ALTERAÇÕES E UPDATE DO CURSO
        foreach($arrayResult as $k => $valor){
            $n_valor = number_format($valor['novo_valor'], 2,".","");
            $rhsalario .= "({$k}, '{$diaAlteracao}', {$valor['valor_old']}, {$valor['novo_valor']}, {$valor['diferenca']}, {$usuario['id_funcionario']}, 'Dissidio'),\r\n";
            $rhcursoup .= " UPDATE curso SET valor='{$n_valor}', salario='{$n_valor}' WHERE id_curso = '{$k}';\r\n ";
        }
        //REMOVER ULTIMA VIRGULA
        $rhsalario = substr($rhsalario,0,-3);
        
        fwrite($handle, $rhsalario);
        fwrite($handle, ";\r\n");
        fwrite($handle, $rhcursoup);
        
        fclose($handle);
    }
    
    
    $tabela = $objFuncoes->montaTabelaFuncoesNiveis($arrayCursosNovo);
    
    $tabela .= "<div class='text-center'>
                <input  type='hidden' name='file_sql' id='file_sql' value='{$fname}' /> 
                <button type='button' href='javascript:;' name='processar' id='processar' class='btn btn-success'><i class='fa fa-refresh'></i> Processar</button>
                </div>";
    
    echo $tabela;
    $log->gravaLog('Sindicatos - Dissídio de Categoria', "Cálculo de Dissídio Efetuado");
    
    exit;
}

if(validate($_REQUEST['method']) && $_REQUEST['method'] == "executar"){
    $file = $_REQUEST['arquivo'];
    $erro = "";
    $file__ = str_replace(".txt","_exec_".date("Ymd").".txt",$file);
    $sql = file_get_contents(dirname(__FILE__)."/arquivos/".$file);
    
    $sqlEx = explode(";",$sql);
    foreach($sqlEx as $val){
        if(strlen($val) > 3){
            $result = mysql_query($val);
            if (!$result) {
                $erro .= "<p>".mysql_error(). strlen($val)."</p>";
            }
        }
    }
    if ($erro) {
        echo $erro;
    }
    rename(dirname(__FILE__)."/arquivos/".$file, dirname(__FILE__)."/arquivos/".$file__);
    $log->gravaLog('Sindicatos - Dissídio de Categoria', "Dissídio Aplicado");
    exit;
}

$funcoes = getFuncoesSindicato($_REQUEST['sindicato']);
$arrayCursosNovo = array();
while ($row_curso = mysql_fetch_assoc($funcoes)) {
    $arrayCursosNovo[$row_curso['nome']][$row_curso['numero']] = $row_curso;
}
$tabela = $objFuncoes->montaTabelaFuncoesNiveis($arrayCursosNovo);

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Sindicato - Dissidio</title>

        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">

    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>      
        <div class="container">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Dissidio da Categoria</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form">
                
                <div class="bs-callout bs-callout-warning" id="callout-helper-pull-navbar"> 
                    <input type="hidden" name="sindicato" id="sindicato" value="<?php echo $sindicato['id_sindicato'] ?>" />
                    <h4><?php echo $sindicato['nome'] ?></h4>
                    <p><strong>CNPJ:</strong> <?php echo mascara_string("##.###.###/####-##",$sindicato['cnpj']) ?></p>
                    <p><strong>Piso Salarial:</strong> R$ <?php echo number_format($sindicato['piso'],2,",",".") ?></p>
                    <p><strong>Mês Desconto:</strong> <?php echo str_pad($sindicato['mes_desconto'],2,"0",STR_PAD_LEFT)." - ".  mesesArray($sindicato['mes_desconto']) ?></p>
                    <p><strong>Mês Dissidio:</strong> <?php echo str_pad($sindicato['mes_dissidio'],2,"0",STR_PAD_LEFT)." - ".  mesesArray($sindicato['mes_dissidio']) ?></p>
                    <!--label for="mes_ini" class="col-sm-2 control-label hidden-print" >A partir de</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <?php echo montaSelect(mesesArray(), $sindicato['mes_dissidio'], array('name' => "mes_ini", 'id' => 'mes_ini', 'class' => 'form-control')); ?>
                            <span class="input-group-addon"> de </span>
                            <?php echo montaSelect(anosArray(), date("Y"), array('name' => "ano_ini", 'id' => 'ano_ini', 'class' => 'form-control')); ?>
                        </div>
                    </div-->
                </div>
                
                <div class="panel panel-default panel-alteracao">
                    <div class="panel-heading text-bold hidden-print">Alteração Salarial</div>
                    <div class="panel-body">
                        
                        <div class="row duplicate">
                            <div class="form-group">
                                <label for="percent" class="col-sm-2 control-label hidden-print" >Percentual</label>
                                <div class="col-sm-3">
                                    <div class="input-group">
                                        <input type="text" name="percent[]" id="percent[]" value="" placeholder="0,03" maxlength="4" class="form-control porcentagem" />
                                        <span class="input-group-addon"><i class="fa fa-percent"></i></span>
                                    </div>
                                </div>
                                
                                <label for="valor_de" class="col-sm-2 control-label hidden-print" >Faixa salarial de</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <input type="text" name="valor_de[]" id="valor_de[]" value="" placeholder="R$ 1.000,00" class="form-control valor" />
                                        <span class="input-group-addon"> até </span>
                                        <input type="text" name="valor_ate[]" id="valor_ate[]" value="" placeholder="R$ 2.000,00" class="form-control valor" />
                                    </div>
                                </div>
                                <!-- ESCONDENDO ESTE BOTÃO ATÉ O CÓDIGO SER FINALIZADO
                                <div class="col-sm-1 no-copy">
                                    <button type="button" name="dupl" id="dupl" class="btn btn-info"><i class="fa fa-plus"></i></button>
                                </div>
                                -->
                            </div>
                        </div>
                        
                        <hr class="panel-wide past-copy" style="display: none;">
                        
                    </div>
                    <div class="panel-footer text-right">
                        <button type="button" name="calcular" id="calcular" class="btn btn-success"><i class="fa fa-calculator"></i> Calcular</button>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Funções Atualizadas<a class="btn btn-primary btn-xs pull-right" data-toggle="collapse" href="#collapseExample2"><i class="fa fa-plus"></i></a></div>
                    <div class="panel-body collapse in f_atualizadas" id="collapseExample2">
                        <p>Calcule primeiro</p>
                    </div>
                </div>
                
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Funções<a class="btn btn-primary btn-xs pull-right" data-toggle="collapse" href="#collapseExample"><i class="fa fa-plus"></i></a></div>
                    <div class="panel-body collapse in" id="collapseExample">
                        <?php echo $tabela ?>
                    </div>
                </div>
                
                
            
            </form>
            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
            
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function(){
                $(".porcentagem").maskMoney({thousands: '', precision: 2, decimal: ','});
                $(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                $("#dupl").click(function(){
                    var $this = $(".duplicate");
                    var html = $this.html();
                    var d = document.createElement("div");
                    $(d).addClass("row");
                    $(d).html(html);
                    //$(".no-copy", d).remove();
                    $("button", d).attr("id","remov[]").attr("name","remov[]").addClass("removeObj").removeClass("btn-info").addClass("btn-danger");
                    $(".fa-plus", d).removeClass("fa-plus").addClass("fa-minus");
                    $("input,select", d).val("");
                    $(".past-copy").before(d);
                    
                    $(".valor").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                    $(".porcentagem").maskMoney({thousands: '', precision: 2, decimal: ','});
                });
                
                $(".panel-alteracao").on("click",".removeObj", function(){
                    var linha = $(this).parent().parent().parent();
                    linha.remove();
                });
                
                $("#collapseExample2").on("click","#processar", function(){
                    //executar
                    var url = "dissidio_calc.php";
                    var arquivo = $("#file_sql").val();
                    $.post(url,{method:"executar", arquivo: arquivo},function(data){
                        if (data) {
                            $("#collapseExample2").html("<span style='color:red'><p>Aplicação de dissídio falhou.</p>"+data+"</span>");
                        }
                        else {
                            $("#pannel-hide").removeClass("hidden");
                            $("#collapseExample2").html("<p><span style='color:green'>Dissídio aplicado com sucesso.</span></p>");
                        }
                    },"html");
                });
                
                $("#calcular").click(function(){
                    var ser = $("input",".panel-alteracao").serialize();
                    var id_sind = $("#sindicato").val();
                    var url = "dissidio_calc.php?method=calcular&"+ser+"&sindicato="+id_sind;
                    $.post(url,null,function(data){
                        $("#pannel-hide").removeClass("hidden");
                        $("#collapseExample2").html(data);
                    },"html");
                });
            });
        </script>
    </body>
</html>