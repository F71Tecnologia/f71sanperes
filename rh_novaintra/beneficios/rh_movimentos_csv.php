<?php

if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
    exit;
}

/**
 * INCLUDES 
 */
include('../../conn.php');
include('../../wfunction.php');
include("../../classes/EmpresaClass.php");
include("../../classes/FolhaClass.php");
include('../../classes/valor_proporcional.php');

$Trab = new proporcional();
 
/**
 * REQUESTS 
 */
$mesSelect = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m');
$anoSelect = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y'); 

/**
 * METODS 
 */
$usuario 	 = carregaUsuario();
$dadosHeader     = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$meses		 = mesesArray(null);
$ano 		 = anosArray(null);
$dataAtual       = date("Y-m-d H:i:s"); 
$ultimaImportacao = "";
$disabled = "";
 
/**
 * VALIDAÇÃO DO ARQUIVO
 */
if (isset($_REQUEST['validar']) && $_REQUEST['validar'] == "validar") {
       
    /**
     * NOMR DO ARQUIVO
     */
    $arquivo = "upload_" . date('YmdHis') . "_" . basename($_FILES['arquivo']['name']);
    
    /**
     * UPLOAD DO ARQUIVO
     */
    if (move_uploaded_file($_FILES['arquivo']['tmp_name'], "excel_movimentos/" . $arquivo)) {

        /**
         * GRAVANDO O NOME 
         * DO ARQUIVO QUE ACABOU 
         * DE SER IMPORTADO
         */
        $queryInsert = "INSERT INTO movimentos_excel (mes,ano,arquivo) VALUES ('{$mesSelect}','{$anoSelect}','{$arquivo}')";
        $sqlInsert = mysql_query($queryInsert) or die("Erro ao Inserir informação");
        $ultimaImportacao = mysql_insert_id();

        /**
         * LENDO ARQUIVO
         * CSV QUE ACABOU DE SER
         * IMPORTADO
         */
        $delimitador = ';';
        $cerca = '"';
        $rows = 0;
        
        /**
         * ABRINDO ARQUIVO 
         * PARA LEITURA
         */
        $f = fopen("excel_movimentos/{$arquivo}", "r");

        if ($f) {
            
            $arrayDados = array();
            $cltDuplicados = array();
            $dadosCltDuplicados = array();
            $listaClts = array();
            $dadosCpfInexistentes = array();
            $erros = array();
                        
            while ($dados = fgetcsv($f, 0, $delimitador, $cerca)) {
//                print_array($dados);
                
                $rows++;
                /**
                 * ERROR
                 */
                error_reporting(E_ERROR);

                /**
                 * MES/ANO SELECIONADO
                 */
                $mes = str_pad($mesSelect, 2, 0, STR_PAD_LEFT);
                $mesAno = $mes . "/" . $anoSelect;

                /**
                 * VALIDANDO POSIÇÕES 
                 * DO ARQUIVO
                 * CSV 
                 */
                $validacao = true;
                
                if($rows == 1){
                    if ($dados[0] != "CLT_NOME") {
                        $validacao = false;
                    }
                    if ($dados[1] != "CPF") {
                        $validacao = false;
                    }
                    if ($dados[2] != "VALOR") {
                        $validacao = false;
                    }
                }

                if ($validacao) {

                    /**
                     * NOME
                     */
                    $nome = utf8_encode($dados[0]);

                    /**
                     * CPF
                     */
                    $criteriaClt = str_replace(".", "", $dados[1]);
                    $criteriaClt = str_replace("-", "", $criteriaClt);
                    
                    /**
                     * BUSCANDO DADOS DO CLT 
                     */
                    $queryBuscaClt = " SELECT *
                                        FROM (
                                            SELECT A.nome, A.id_clt, A.id_regiao, A.data_entrada, A.id_projeto, B.salario,
                                            REPLACE(REPLACE(A.cpf,'.',''),'-','') AS cpf_formatado
                                            FROM rh_clt AS A
                                            LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                                        ) AS tmp
                                        WHERE cpf_formatado = '{$criteriaClt}'";
                    $sqlBuscaClt = mysql_query($queryBuscaClt) or die("Erro ao selecionar participante");
                    
                    $nome = "";
                    $cpf = "";
                    $id_clt = "";
                    $id_regiao = "";
                    $id_projeto = "";
                    $salario = "";
                    $data_entrada = "";
                    
                    if (mysql_num_rows($sqlBuscaClt) > 0) {

                        while ($rowsClt = mysql_fetch_assoc($sqlBuscaClt)) {
                            
                            /**
                             * IDENTIFICANDO CPF DUPLICADOS
                             */
                            $cltDuplicados[$rowsClt['id_clt']] += 1;
                            
                            /**
                             * ARRAY DE DADOS DO CLT
                             */
                            $listaClts[$rowsClt['id_clt']] = $rowsClt['nome'];
                            
                            /**
                             * INFORMAÇÕES DO CLT
                             */
                            $nome = $rowsClt['nome'];
                            $cpf = $dados[1];
                            $id_clt = $rowsClt['id_clt'];
                            $id_regiao = $rowsClt['id_regiao'];
                            $id_projeto = $rowsClt['id_projeto'];
                            $salario = $rowsClt['salario'];
                            $data_entrada = $rowsClt['data_entrada'];
                        }

                        /**
                         * CODIGO MOVIMENTO
                         */
                        $cod = $dados[2];

                        /**
                         * BUSCANDO INFORMAÇÃO DO MOVIMENTO
                         */
                        $queryBuscaMov = "SELECT * FROM rh_movimentos AS A WHERE A.cod = '7001'";
                        $sqlBuscaMov = mysql_query($queryBuscaMov) or die("Erro ao selecionar participante");
                        $id_mov = "";
                        $cod_mov = "";
                        $tipo_mov = "";
                        $nome_mov = "";
                        if (mysql_num_rows($sqlBuscaMov) > 0) {
                            while ($rowsMov = mysql_fetch_assoc($sqlBuscaMov)) {
                                $id_mov = $rowsMov['id_mov'];
                                $cod_mov = $rowsMov['cod'];
                                $tipo_mov = $rowsMov['categoria'];
                                $nome_mov = $rowsMov['descicao'];
                                $incidencia_inss = $rowsMov['incidencia_inss'];
                                $incidencia_irrf = $rowsMov['incidencia_irrf'];
                                $incidencia_fgts = $rowsMov['incidencia_fgts'];
                                $campo_incidencia = ",,";


                                if ($incidencia_inss == 1 && $incidencia_irrf == 0 && $incidencia_fgts == 0) {
                                    $campo_incidencia = "5020,,";
                                } else if ($incidencia_inss == 1 && $incidencia_irrf == 1 && $incidencia_fgts == 0) {
                                    $campo_incidencia = "5020,5021,";
                                } else if ($incidencia_inss == 1 && $incidencia_irrf == 1 && $incidencia_fgts == 1) {
                                    $campo_incidencia = "5020,5021,5023";
                                } else if ($incidencia_irrf == 1 && $incidencia_inss == 0 && $incidencia_fgts == 0) {
                                    $campo_incidencia = ",5021,";
                                } else if ($incidencia_inss == 1 && $incidencia_fgts == 1) {
                                    $campo_incidencia = "5020,,5023";
                                } else if ($incidencia_fgts == 1) {
                                    $campo_incidencia = ",,5023";
                                } else if ($incidencia_irrf == 1) {
                                    $campo_incidencia = "5021";
                                }
                            }
                        }
                        
                        /**
                         * VALOR
                         */
                        $valor = str_replace("R$ ", "", $dados[4]);
                        $valor = str_replace(".", "", $valor);
                        $valor = str_replace(",", ".", $valor);
                        $valor = number_format($valor, '2', '.', '');
                        
                        /**
                         * VT RECEBIDO
                         */
                        $valor_vt = str_replace("R$", "", $dados[2]);
                        $valor_vt = str_replace(".", "", $valor_vt);
                        $valor_vt = str_replace(",", ".", $valor_vt);
                        $valor_vt = number_format($valor_vt, '2', '.', '');
                                           
                        $inicio = explode('-', $data_entrada);
                        $dias_trabalhados = 31 - $inicio[2];
                        
                        $Trab->calculo_proporcional($salario, $dias_trabalhados);                        
                        
                        /*
                        * CALCULO DO DESCONTO EM CIMA DO PROPORCIONAL
                        * PARA CASOS DE ADMISSÃO
                        */
                        if(($inicio[1] == $mesSelect) && ($inicio[0] == $anoSelect)){
                            $percentual_descVT = $Trab->valor_proporcional * 0.06;
                            $salario_proporcional = $Trab->valor_proporcional * $dias_trabalhados;
                        }else{
                            $percentual_descVT = $salario * 0.06;
                            $salario_proporcional = $salario;
                        }
                        
                        $percentual_salario = $salario_proporcional * 0.06;
                        
                        if($valor_vt > $percentual_descVT){
                            $valor_desc = $percentual_descVT;
                        }else{
                            $valor_desc = $valor_vt;
                        }
                        
                    }else{
                        
                        if($dados[1] != 'CPF' && $dados[1] != ''){
                            $dadosCpfInexistentes[$dados[1]] = $dados[0]; 
                        }
                        
                    }
                    
                    /**
                     * MONTANDO ARRAY
                     * DE DADOS
                     */          
                    
                    if($id_clt != ""){
                        $arrayDados[$id_clt] = array(
                            "nome" => $nome,
                            "cpf" => $cpf,
                            "id_regiao" => $id_regiao,
                            "id_projeto" => $id_projeto,
                            "mes_mov" => $mes,
                            "ano_mov" => $anoSelect,
                            "id_mov" => $id_mov,
                            "cod_movimento" => $cod_mov,
                            "tipo_movimento" => $tipo_mov,
                            "nome_movimento" => $nome_mov,
                            "data_movimento" => $dataAtual,
                            "valor_movimento" => $valor_desc,
                            "lancamento" => 1,
                            "status" => 1,
                            "status_reg" => 1,
                            "incidencia" => $campo_incidencia,
                            "valor_vt" => $valor_vt,
                            "sal_proporcional" => $salario_proporcional,
                            "percentual_salario" => $percentual_salario
                        );
                    }
                    
                } else {
                    $erros[] = " Layout do arquivo não corresponde ao modelo padrão";
                }
            }
        } else {
            $erros[] =  " Falha ao fazer upload!";
        }
    }
    
     
    /**
     * CRIANDO LISTA DE 
     * PARTICIPANTES DUPLICADOS
     */
    foreach($cltDuplicados AS $clts => $qnts){
        if($qnts > 1){
            $dadosCltDuplicados[$clts] = $listaClts[$clts];
        }
    }
     
}

/**
 * 
 */
if (isset($_REQUEST['importar']) && $_REQUEST['importar'] == "importar") {
    
    $disabled = "disabled";
     
    /**
     * DADOS
     */
    $dados = isset($_REQUEST['dados'])?$_REQUEST['dados']:"";        
    
    /**
     * QUERY
     */
    $query = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,valor_movimento,lancamento,status,status_reg,incidencia, importado_manualmente) VALUES ";
    
    foreach ($dados as $key => $v){
        
        foreach ($v as $cod => $valor){
            
            /**
            * BUSCANDO DADOS DO CLT 
            */
            $queryBuscaClt = " SELECT *
                               FROM (
                                   SELECT A.nome, A.id_clt, A.id_regiao, A.id_projeto, B.salario,
                                   REPLACE(REPLACE(A.cpf,'.',''),'-','') AS cpf_formatado
                                   FROM rh_clt AS A
                                   LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                               ) AS tmp
                               WHERE id_clt = '{$key}'";
            $sqlBuscaClt = mysql_query($queryBuscaClt) or die("Erro ao selecionar participante");

            $id_regiao = "";
            $id_projeto = "";
            
            if (mysql_num_rows($sqlBuscaClt) > 0) {

                while ($rowsClt = mysql_fetch_assoc($sqlBuscaClt)) {

                    /**
                     * INFORMAÇÕES DO CLT
                     */
                    $id_regiao = $rowsClt['id_regiao'];
                    $id_projeto = $rowsClt['id_projeto'];
                    
                }
           }
            
            /**
             * BUSCANDO INFORMAÇÃO DO MOVIMENTO
             */
            $queryBuscaMov = "SELECT * FROM rh_movimentos AS A WHERE A.cod = '{$cod}'";
            $sqlBuscaMov = mysql_query($queryBuscaMov) or die("Erro ao selecionar participante");
            $id_mov = "";
            $cod_mov = "";
            $tipo_mov = "";
            $nome_mov = "";
            if (mysql_num_rows($sqlBuscaMov) > 0) {
                while ($rowsMov = mysql_fetch_assoc($sqlBuscaMov)) {
                    $id_mov = $rowsMov['id_mov'];
                    $cod_mov = $rowsMov['cod'];
                    $tipo_mov = $rowsMov['categoria'];
                    $nome_mov = $rowsMov['descicao'];
                    $incidencia_inss = $rowsMov['incidencia_inss'];
                    $incidencia_irrf = $rowsMov['incidencia_irrf'];
                    $incidencia_fgts = $rowsMov['incidencia_fgts'];
                    $campo_incidencia = ",,";


                    if ($incidencia_inss == 1 && $incidencia_irrf == 0 && $incidencia_fgts == 0) {
                        $campo_incidencia = "5020,,";
                    } else if ($incidencia_inss == 1 && $incidencia_irrf == 1 && $incidencia_fgts == 0) {
                        $campo_incidencia = "5020,5021,";
                    } else if ($incidencia_inss == 1 && $incidencia_irrf == 1 && $incidencia_fgts == 1) {
                        $campo_incidencia = "5020,5021,5023";
                    } else if ($incidencia_irrf == 1 && $incidencia_inss == 0 && $incidencia_fgts == 0) {
                        $campo_incidencia = ",5021,";
                    } else if ($incidencia_inss == 1 && $incidencia_fgts == 1) {
                        $campo_incidencia = "5020,,5023";
                    } else if ($incidencia_fgts == 1) {
                        $campo_incidencia = ",,5023";
                    } else if ($incidencia_irrf == 1) {
                        $campo_incidencia = "5021";
                    }
                }
            }

            /**
             * QUERY
             */
            $query .= "('{$key}','{$id_regiao}','{$id_projeto}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','{$id_mov}','{$cod_mov}','{$tipo_mov}','{$nome_mov}','{$dataAtual}','{$valor}',1,1,1,'{$campo_incidencia}','{$_REQUEST['ultimaImportacao']}'),\r\n";

        }    
    }
    
    $query = substr(trim($query), 0, -1);
    
    if($_COOKIE['logado'] != 179){
       if(mysql_query($query)){            
            echo "
                <div class='alert alert-success'>
                    <p>Importação realizada com sucesso.</p>
                </div>";
        } 
    }else{
        echo $query;
    }
    
    
}
 
$breadcrumb_config = array("nivel" => "../../", "key_btn" => "3", "area" => "Recursos Humanos", "id_form" => "form1", "ativo" => "Importação de VT");
$breadcrumb_pages = array("Gestão de RH" => "../../rh/principalrh.php", "Benefícios" => "../beneficios");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Importação de VT</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                $('#regiao').ajaxGetJson('../../methods.php', {method: 'carregaProjetos'}, null, 'projeto');
                
                /**
                 * AÇÃO DE UPLOAD DE
                 * ARQUIVOS
                 */
                $("#gerar").click(function(){
                    var formdata = new FormData($("#form"));
                    var link = "";
                    $.ajax({
                        type: 'POST',
                        url: link,
                        data: formdata,                         
                        processData: false,
                        contentType: false

                    }).done(function (data) {
                        $("div.container-fluid").html(data);
                    });
                });
                
            });
        </script>
        <style>
            .messageError p{ font-family: arial; font-size: 12px; color: #d88080; }
            .messageError span{ font-weight: bold; }
            .boxCpfInexistentes h4{font-family: arial; text-transform: uppercase; font-size: 12px; color: #dab062; }
        </style>
    </head>
    <body>
        <?php include('../../template/navbar_default.php'); ?>      
        <div class="container">
            <div class="page-header box-rh-header">
            	<h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Importação de VT</small></h2>
            </div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form" id="form"  enctype="multipart/form-data">  
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Dados</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-sm-2 control-label hidden-print" >Mês</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($meses, $mesSelect, "id='mes' name='mes' class='required[custom[select]] validate[required] form-control'") ?><span class="loader"></span>
                            </div>
                            
                            <label for="select" class="col-sm-2 control-label hidden-print" >Ano</label>
                            <div class="col-sm-2">
                                <?php echo montaSelect($ano, $anoSelect, "id='ano' name='ano' class='required[custom[select]] validate[required] form-control'") ?><span class="loader"></span> 
                            </div> 
                            
                            <br><br><br>
                            
                            <label for="select" class="col-sm-2 control-label hidden-print" >Arquivo:</label>
                            <div class="col-sm-10">
                                <input class="form-control" type="file" id="arquivo" name="arquivo" />
                                <span style="margin-top: 5px; display: block; font-style: italic; color: #b7b6b6; float: right; ">O arquivo precisa ser um CSV</span>
                            </div>
                        </div>
                    <div class="panel-footer text-right hidden-print controls">
                        <span class="glyphicon glyphicon-cloud-download" aria-hidden="true"></span>
                        <span style="margin-right: 40px;"><a href='excel_movimentos/PADRAO_LAYOUT_IMPORT_MOVIMENTOS.csv'>Download Modelo</a></span>
                        <button type="submit" name="validar" id="validar" value="validar" class="btn btn-success"><span class="glyphicon glyphicon-cloud-upload"></span> Validar CSV</button>
                    </div>
                    </div> 

            <?php if(count($arrayDados) > 0) { $count = 0; ?>  
                <table class="table table-striped table-hover text-sm valign-middle" id="tbRelatorio">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>CPF</th>
                            <th>SALÁRIO</th>
                            <th>6% SAL.</th>
                            <th>VT RECEBIDO</th>                            
                            <th colspan="2">DESCONTO</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($arrayDados AS $key => $values){ ?>
                        
                        <input type="hidden" name="ultimaImportacao" value="<?php echo $ultimaImportacao; ?>" />
                        <input type="hidden" name="mes" value="<?php echo $values['mes_mov'] ?>" />
                        <input type="hidden" name="ano" value="<?php echo $values['ano_mov'] ?>" />
                        <input type="hidden" name="dados[<?php echo $key; ?>][<?php echo $values['cod_movimento']; ?>]" value="<?php echo $values['valor_movimento']; ?>" />
                        <tr class="<?php echo ($count++ % 2 == 0) ? 'odd' : 'even'; ?>">
                            <td><?php echo $key; ?></td>
                            <td><?php echo $values['nome']; ?></td>
                            <td><?php echo $values['cpf']; ?></td>
                            <td><?php echo 'R$ '.number_format($values['sal_proporcional'], 2, ',', '.'); ?></td>
                            <td><?php echo 'R$ '.number_format($values['percentual_salario'], 2, ',', '.'); ?></td> 
                            <td><?php echo 'R$ '.number_format($values['valor_vt'], 2, ',', '.'); ?></td>                                                                                   
                            <td><?php echo 'R$ '.number_format($values['valor_movimento'], 2, ',', '.'); ?></td>
                            <td>
                                <?php if(array_key_exists($key, $dadosCltDuplicados)){
                                    echo "<span style='color: red' class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span> Duplicado"; } 
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php } else { ?>
                        <br/>
                        <div class='alert alert-warning'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                        <?php
                    }
                ?>
            </div>
            
            <?php if(count($dadosCpfInexistentes) > 0){ ?>     
                <div class="boxCpfInexistentes alert alert-warning" >
                    <h4>Participantes com CPF inválido ou não encontrado.</h4>
                    <ul>
                        <?php foreach ($dadosCpfInexistentes as $cpf => $clt){ ?>
                            <li style="list-style: none; font-size: 12px;"><?php echo "<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>" . $cpf ." - ".  $clt; ?></li>
                        <?php } ?>
                    </ul>    
                </div>
            <?php } ?>
            
            <?php if(count($dadosCpfInexistentes) > 0 || count($dadosCltDuplicados) > 0 || count($erros) > 0){ ?>     
                
                <?php if(count($erros) > 0){ ?>     
                    <div class="boxCpfInexistentes alert alert-warning" >
                        <h4>Erros no arquivo.</h4>
                        <ul>
                            <?php foreach ($erros as $key => $mensagens){ ?>
                                <li style="list-style: none; font-size: 12px;"><?php echo "<span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>" . $mensagens; ?></li>
                            <?php } ?>
                        </ul>    
                    </div>
                <?php } ?> 
                
                <div class="messageError alert alert-danger">
                    <p><span>IMPORTANTE:</span> PARA REALIZAR A IMPORTAÇÃO É NECESSÁRIO CORRIGIR AS DIVERGÊNCIAS NO ARQUIVO CSV E SUBIR O ARQUIVO NOVAMENTE.</p>
                </div>    
            <?php }else{ ?>    
            
                <div class="panel-body">
                    <div class="panel-footer text-right hidden-print controls">
                        <button type="submit" name="importar" id="importar" value="importar" <?php echo $disabled; ?> class="btn btn-success"><span class="fa fa-save"></span> Importar CSV</button>
                    </div>
                </div>
                
            <?php } ?>    
                
            </form>
            <?php include('../../template/footer.php'); ?>
            <div class="clear"></div>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
    </body>
</html>