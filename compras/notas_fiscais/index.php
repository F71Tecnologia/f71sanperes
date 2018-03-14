<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include("../../classes_permissoes/regioes.class.php");

$usuarioW = carregaUsuario();
$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];

//CARREGA AS REGIÕES
$regioes = new Regioes;


//CARREGA GRUPOS
$grupo = array(
    "-1" => "« Selecione o Grupo »",
    "30" => "30 - SERVIÇOS DE TERCEIROS"
);

//CARREGA SUBGRUPO
$subgrupo = array("-1" => "Selecione o subgrupo");
$query_subgrupo = mysql_query("SELECT * FROM  entradaesaida_subgrupo WHERE entradaesaida_grupo IN(30) ORDER BY nome");
while ($rows_subgrupo = mysql_fetch_assoc($query_subgrupo)) {
    $subgrupo += array($rows_subgrupo['id_subgrupo'] => $rows_subgrupo['id_subgrupo'] . ' - ' . $rows_subgrupo['nome']);
}

//CARREGA LISTAGEM DE NOTAS
/**********************************LISTAGEM************************************/
$lista = "SELECT A.id_nota, B.nome AS projeto,C.nome_grupo AS grupo, D.nome AS subgrupo, A.numero_documento, DATE_FORMAT(A.data_emissao_nf,'%d/%m/%Y') AS data_emissao,
            A.mes_competencia, A.ano_competencia, A.valor_bruto_nf
            FROM notas_fiscais AS A
            LEFT JOIN (SELECT id_projeto,nome FROM projeto) AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN (SELECT * FROM entradaesaida_grupo) AS C ON(A.id_grupo = C.id_grupo)
            LEFT JOIN (SELECT * FROM entradaesaida_subgrupo) AS D ON(A.id_subgrupo = D.id_subgrupo)
            ORDER BY id_nota DESC";
$sql_lista = mysql_query($lista) or die("erro ao selecionar lista");
$dados = array();
if(mysql_num_rows($sql_lista) > 0){
    while($linhas = mysql_fetch_assoc($sql_lista)){
        $dados[] = array(
            "nota" => $linhas['id_nota'], 
            "projeto" => $linhas['projeto'],
            "grupo" => $linhas['grupo'],
            "subgrupo" => $linhas['subgrupo'],
            "num_doc" => $linhas['numero_documento'],
            "data_emissao" => $linhas['data_emissao'],
            "mes_competencia" => $linhas['mes_competencia'],
            "ano_competencia" => $linhas['ano_competencia'],
            "valor_bruto" => $linhas['valor_bruto_nf']
         );


    }
}


if(isset($_REQUEST['method'])){
    if($_REQUEST['method'] == "carregaTipo"){
        $retorno = array("status" => 0);
        $query_tipo = "SELECT * FROM entradaesaida WHERE LEFT(cod,5) = '{$_REQUEST['subgrupo']}'";
        $sql_tipo = mysql_query($query_tipo);
        if(mysql_num_rows($sql_tipo) > 0){
            $dados = array();
            while($rows_tipo = mysql_fetch_assoc($sql_tipo)){
                $dados[] = array($rows_tipo['id_entradasaida'] => $rows_tipo['id_entradasaida'] ." - ". $rows_tipo['cod'] ." - ". utf8_encode($rows_tipo['nome']));
            }
            
            $retorno = array("status" => 1, "dados" => $dados);
        }
        
        echo json_encode($retorno);
        exit();
    }
}

$meses = mesesArray(null);
$anos = anosArray(null, null);

$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

?>
<html>
    <head>
        <title>:: Intranet :: NOTAS FICAIS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="css/style.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript"></script>

        <script>
            $(function() {
                
                //VALIDAÇÃO
                $("#form1").validationEngine();
                $("#data_emissao").datepicker();
                $("#valor_bruto").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                //CARREGA REGIAO
                $('#regiao').ajaxGetJson("../../methods.php", {method: "carregaProjetos"}, null, "cad_projeto");
                $('#cad_regiao_prestador').ajaxGetJson("../../methods.php", {method: "carregaProjetos", regiao:$('#cad_regiao_prestador').val()}, null, "cad_projeto_prestador");
                
                //CARREGA TIPO
                $("#cad_subgrupo").change(function(){
                   var subgrupo = $(this).val();
                   $.ajax({
                       url:"",
                       type:"POST",
                       dataType:"json",
                       data:{
                          method:"carregaTipo",
                          subgrupo:subgrupo
                       },
                       success: function(data){
                           if(data.status){
                               var html = "";
                               $.each(data.dados,function(k, v){
                                   $.each(v, function(key, value){
                                       html += "<option value='"+key+"'>"+value+"</option>";
                                   });
                               });
                               $("#cad_tipo").html(html);
                           }
                       }
                   });
                });
                
                $("#cadastrar").click(function(){
                    $("#acao").val("cadastrar");
                    $("#form1").attr({action:"controller/notas.php"}).submit();
                });
            });
        </script>
    </head>
    <body id="page-despesas" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <div id="head">
                    <img src="../../imagens/logomaster6.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>GERENCIADOR DE NOTAS FISCAIS</h2>
                    </div>
                </div>
                
                <!--- TELA DOS FILTROS --->
                
                <fieldset class="box-filtro">
                    <legend class="titulo">FILTRO</legend>
                    <div class="padding_50">
                        <div class="col-esq">
                            <p class="first_old"><label>Região:</label> <?php echo montaSelect(array("-1" => "« Selecione a Região »"), null, "id='regiao_filtro' name='regiao_filtro'") ?></p>
                            <p class="first_old"><label>Projeto:</label> <?php echo montaSelect(array("-1" => "« Aguardando Região »"), $projetoR,null, "id='projeto_filtro' name='projeto_filtro' class=''") ?></p>
                            <p class="first_old"><label>Prestador/Fornecedor:</label> <?php echo montaSelect(array("-1" => "« Aguardando Projeto »"), $prestadorR,null, "id='prestador' name='prestador'") ?></p>
                        </div>
                        <div class="col-dir">
                            <p class="first_old"><label>Mês Competência:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?></p>
                            <p class="first_old"><label>Ano Competência:</label> <?php echo montaSelect($anos, $mesR, "id='ano' name='ano' class='validate[custom[select]]'") ?></p>
                        </div>
                    </div>
                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                        <input type="submit" class="button" value="Novo" name="novo" /> 
                    </p>
                </fieldset>
                
                <!--LISTAGEM DE NOTAS FISCSAIS :) -->
                <?php if(count($dados) > 0){ ?>
                    <fieldset class="box-listagem">
                        <legend class="titulo">LISTAGEM DE NOTAS FISCAIS</legend>
                        <div class="padding_50" style="margin-bottom:30px; overflow: hidden;">
                            <table id="tbNotas" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" style="page-break-after:auto;">
                                <thead>
                                    <tr>
                                        <th>PROJETO</th>
                                        <th>GRUPO</th>
                                        <th>SUBGRUPO</th>
                                        <th>N° DOCUMENTO</th>
                                        <th>DATA EMISSÃO</th>
                                        <th>MÊS COMPETÊNCIA</th>
                                        <th>ANO COMPETÊNCIA</th>
                                        <th>VALOR BRUTO</th>
                                        <th colspan="2">AÇÃO</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dados as $notas){ ?>
                                        <tr data-key="<?php echo $notas['nota']; ?>">
                                            <td><?php echo $notas['projeto']; ?></td>
                                            <td><?php echo $notas['grupo']; ?></td>
                                            <td><?php echo $notas['subgrupo']; ?></td>
                                            <td><?php echo $notas['num_doc']; ?></td>
                                            <td><?php echo $notas['data_emissao']; ?></td>
                                            <td><?php echo $meses[$notas['mes_competencia']]; ?></td>
                                            <td><?php echo $notas['ano_competencia']; ?></td>
                                            <td><?php echo "R$ " . $notas['valor_bruto']; ?></td>
                                            <td><img src="../../imagens/icones/icon-edit.gif" data-key="<?php echo $notas['nota'] ?>" title="Editar" class="edit_nota"/></td>
                                            <td><img src="../../imagens/icones/icon-delete.gif" data-key="<?php echo $notas['nota'] ?>" title="Excluir" class="remove_nota"/></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </fieldset>    
                <?php } ?>
                <!--- TELA DE CADASTRO ---->
                <fieldset class="box-cadastro">
                    <legend class="titulo">CADASTRO DE NOTAS FISCAIS</legend>
                    <div class="padding_50" style="margin-bottom:30px; overflow: hidden;">
                        <div class="col-esq-cad">
                            <fieldset class="margin-top padding-bottom">
                                <legend>Saída</legend>
                                <div class="padding_50">
                                    <p class="first_old"><input type="hidden" name="acao" id="acao" value="" /></p>
                                    <p class="first_old"><input type="hidden" name="nota" id="nota" value="" /></p>
                                    <p class="first_old"><label>Região:</label> 
                                        <select name="regiao" id="regiao" class='validate[custom[select]]'>
                                            <?php echo $regioes->Preenhe_select_por_master($master, $regiao); ?>
                                        </select>
                                    </p>
                                    <p class="first_old"><label>Projeto:</label> <?php echo montaSelect(array("-1" => "« Aguardando Região »"), null, "id='cad_projeto' name='cad_projeto' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Grupo:</label> <?php echo montaSelect($grupo, null, "id='cad_grupo' name='cad_grupo' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Subgrupo:</label> <?php echo montaSelect($subgrupo, null, "id='cad_subgrupo' name='cad_subgrupo' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Tipo:</label> <?php echo montaSelect(array("-1" => "« Aguardando Subgrupo »"), null, "id='cad_tipo' name='cad_tipo' class='validate[custom[select]]'") ?></p>
                                </div>
                            </fieldset>
                            
                            <fieldset class="margin-top padding-bottom">
                                <legend>Prestador de Serviço</legend>
                                <div class="padding_50">
                                    <p class="first_old"><label>Região:</label> 
                                        <select name="cad_regiao_prestador" id="cad_regiao_prestador" class='validate[custom[select]]'>
                                            <?php echo $regioes->Preenhe_select_por_master($master, $regiao) ?>
                                        </select>
                                    </p>
                                    <p class="first_old"><label>Projeto:</label> <?php echo montaSelect(array("-1" => "« Aguardando Região »"), null, "id='cad_projeto_prestador' name='cad_projeto_prestador' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old">
                                        <label>Tipo da Empresa:</label> 
                                        <label for="prestador"><input type="radio" name="tipo_empresa" id="prestador" value="1" class="validate[required]" />PRESTADOR DE SERVIÇO</label>
                                        <label for="fornecedor"><input type="radio" name="tipo_empresa" id="fornecedor" value="2" class="validate[required]" />FORNECEDOR</label>
                                    </p>
                                    <p class="first_old">
                                        <label>Descrição:</label> 
                                        <textarea name="descricao" class="textarea"></textarea>
                                    </p>
                                    <p class="first_old">
                                        <label>Número do Documento:</label> 
                                        <input type="text" name="num_documento" id="num_documento" class="input validate[required]" />
                                    </p>
                                    <p class="first_old">
                                        <label>Data de Emissão da NF:</label> 
                                        <input type="text" name="data_emissao" id="data_emissao" class="input" />
                                    </p>
                                </div>
                            </fieldset>
                            
                            <fieldset class="margin-top padding-bottom">
                                <legend>COMPETÊNCIA</legend>
                                <div class="padding_50">
                                    <p class="first_old"><label>Mês:</label> <?php echo montaSelect($meses, null, "id='cad_mes' name='cad_mes', class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Ano:</label> <?php echo montaSelect($anos, date("Y"), "id='cad_ano' name='cad_ano', class='validate[custom[select]]'") ?></p>
                                    <p class="first_old">
                                        <label>Valor Bruto:</label> 
                                        <input type="text" name="valor_bruto" id="valor_bruto" class="input validate[required]"  />
                                    </p>
                                </div>
                            </fieldset>
                        </div>
                        <!--<div class="col-dir-cad">
                            <fieldset class="margin-top min-height">
                                <legend>ANEXOS</legend>
                                <div class="padding_50">
                                    * Selecione as Notas para serem anexadas a esta saída.
                                </div>
                            </fieldset>
                        </div>-->
                    </div>
                    <p class="controls"> <input type="button" class="button" value="cadastrar" name="cadastrar" id="cadastrar" /> </p>
                </fieldset>      
                
                <!--- TELA DE LISTAGEM ---->
                
                <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                <br/>                    
                    <p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('tbRelatorio', 'Equipe')" value="Exportar para Excel" class="exportarExcel"></p>    
                    <br/>
                    <table id="tbRelatorio" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th colspan="10">Unidade Gerenciada: <?php echo $projeto['nome'] ?></th>
                                <th><?php echo $mesShow ?></th>
                            </tr>
                            <tr>
                                <th colspan="11">O responsável: <?php echo $roMaster['nome'] ?></th>
                            </tr>
                            <tr>
                                <th colspan="11">EQUIPE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="titulo">
                                <td>NOME</td>
                                <td>CPF</td>
                                <td>RG</td>
                                <td>PIS</td>
                                <td>DATA NASCIMENTO</td>
                                <td>TIPO CONTRATAÇÃO</td>
                                <td>DATA ENTRADA</td>
                                <td>DATA SAÍDA</td>
                                <td>SEXO</td>
                                <td>FUNÇÃO</td>
                                <td>FORMA DE PGTO.</td>
                            </tr>
                            <?php while ($row = mysql_fetch_assoc($result)) {
                                $cpf = preg_replace('/[^[:digit:]]/', '', $row['cpf']); ?>                                
                                            <tr>
                                                <td><?php echo $row['nome']; ?></td>
                                                <td><?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf); ?></td>
                                                <td><?php echo $row['rg']; ?></td>
                                                <td><?php echo $row['pis']; ?></td>
                                                <td class="txcenter"><?php echo $row['data_nasciBr']; ?></td>
                                                <td><?php echo $row['tpcontrato']; ?></td>
                                                <td class="txcenter"><?php echo $row['data_entradaBr']; ?></td>
                                                <td class="txcenter"><?php echo ($row['data_saidaBr'] == "00/00/0000") ? "" : $row['data_saidaBr']; ?></td>
                                                <td><?php echo $row['sexo']; ?></td>
                                                <td><?php echo $row['funcao']; ?></td>
                                                <td><?php echo $row['tipopg']; ?></td>
                                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" class="txright">Total de participantes:</td>
                                <td colspan="2"> <?php echo $linhas ?></td>
                            </tr>
                        </tfoot>
                    </table>
                <?php } else { ?>
                    <?php if ($projetoR !== null) { ?>
                    <br/>
                    <div id='message-box' class='message-green'>
                        <p>Nenhum registro encontrado</p>
                    </div>
                    <?php } ?>
                <?php } ?>
             </form>
        </div>
    </body>
</html>