<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include_once('../../conn.php');
include_once('../../funcoes.php');
include_once('../../wfunction.php');
include_once("../../classes_permissoes/regioes.class.php");
include_once("../../classes/NotaFiscalClass.php");

$usuarioW = carregaUsuario();
$regiao = $usuarioW['id_regiao'];
$master = $usuarioW['id_master'];
$usuario = $usuarioW['id_funcionario'];

//CARREGA AS REGIÕES
$regioes = new Regioes;
$notas = new NotaFiscal();

//DADOS DA NOTA
if(isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])){
    $dados = $notas->getNotas($_REQUEST['nota']);
}

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

//CARREGADO NO AJAX
if(isset($_REQUEST['method'])){
    //CARREGA TIPO
    if($_REQUEST['method'] == "carregaTipo"){
        $retorno = array("status" => 0);
        $query_tipo = "SELECT * FROM entradaesaida WHERE LEFT(cod,5) = '{$_REQUEST['subgrupo']}'";
        try{
            $sql_tipo = mysql_query($query_tipo);
            if(mysql_num_rows($sql_tipo) > 0){
                $dados = array();
                while($rows_tipo = mysql_fetch_assoc($sql_tipo)){
                    $dados[] = array($rows_tipo['id_entradasaida'] => $rows_tipo['id_entradasaida'] ." - ". $rows_tipo['cod'] ." - ". utf8_encode($rows_tipo['nome']));
                }

                $retorno = array("status" => 1, "dados" => $dados);
            }
        }catch(Exception $e){
            echo $e->getMessage("Erro ao selecionar tipo");
        }
        
        echo json_encode($retorno);
        exit();
    }
    
    //CARREGA PRESTADOR
    if($_REQUEST['method'] == "carregaPrestador"){
        $retorno = array("status" => 0);
        $query_prestador = "SELECT id_prestador,c_razao FROM  prestadorservico WHERE id_regiao = '{$_REQUEST['regiao']}' AND id_projeto = '{$_REQUEST['projeto']}' AND status = '{$_REQUEST['status']}'";
        try{
            $sql_prestador = mysql_query($query_prestador);
            if(mysql_num_rows($sql_prestador) > 0){
                $dados = array();
                while($rows_prestador = mysql_fetch_assoc($sql_prestador)){
                    $dados[] = array("id_prestador" => $rows_prestador['id_prestador'], "nome" => utf8_encode($rows_prestador['c_razao']));
                }
            }else{
                $dados[] = array("id_prestador" => "-1", "nome" => "Nenhum resultado encontrado");
            }
            $retorno = array("status" => 1, "dados" => $dados);
        }catch(Exception $e){
            echo $e->getMessage("Erro ao selecionar prestador de serviço");
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
$regiaoSel = (isset($dados[0]['id_regiao']) && $dados[0]['id_regiao'] != "") ? $dados[0]['id_regiao'] : "";
$regiaoPrestadoSel = (isset($dados[0]['id_regiao_prestador']) && $dados[0]['id_regiao_prestador'] != "") ? $dados[0]['id_regiao_prestador'] : "";

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
        <script src="js/notas_fiscais.js" type="text/javascript"></script>

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
                
                <!--- TELA DE CADASTRO ---->
                <fieldset class="box-cadastro">
                    <legend class="titulo"><?php echo (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? "EDIÇÃO " : "CADASTRO "; ?> DE NOTAS FISCAIS</legend>
                    <div class="padding_50" style="margin-bottom:30px; overflow: hidden;">
                        <div class="col-esq-cad">
                            <fieldset class="margin-top padding-bottom">
                                <legend>DADOS FINANCEIRO</legend>
                                <div class="padding_50">
                                    <p class="first_old"><label>Região:</label> 
                                        <select name="regiao" id="regiao" class='validate[custom[select]]'>
                                            <?php echo $regioes->Preenhe_select_por_master($master,$regiaoSel); ?>
                                        </select>
                                    </p>
                                    <p class="first_old"><label>Projeto:</label> <?php echo montaSelect(array("-1" => "« Aguardando Região »"), null, "id='cad_projeto' name='cad_projeto' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Grupo:</label> <?php echo montaSelect($grupo, $dados[0]['id_grupo'], "id='cad_grupo' name='cad_grupo' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Subgrupo:</label> <?php echo montaSelect($subgrupo, $dados[0]['id_subgrupo'], "id='cad_subgrupo' name='cad_subgrupo' class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Tipo:</label> <?php echo montaSelect(array("-1" => "« Aguardando Subgrupo »"), null, "id='cad_tipo' name='cad_tipo' class='validate[custom[select]]'") ?></p>
                                </div>
                            </fieldset>

                            <fieldset class="margin-top padding-bottom">
                                <legend>DADOS DO PRESTADOR DE SERVIÇO</legend>
                                <div class="padding_50">
                                    <p class="first_old"><label>Região:</label> 
                                        <select name="cad_regiao_prestador" id="cad_regiao_prestador" class='validate[custom[select]]'>
                                            <?php echo $regioes->Preenhe_select_por_master($master,$regiaoPrestadoSel) ?>
                                        </select>
                                    </p>
                                    <p class="first_old"><label>Projeto:</label> <?php echo montaSelect(array("-1" => "« Aguardando Região »"), null, "id='cad_projeto_prestador' name='cad_projeto_prestador' class='validate[custom[select]]'") ?></p>
                                    <?php  $mostrar = (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? "mostrar" : "" ?>
                                    <div class="tipo_empresa <?php echo $mostrar; ?>">
                                        <p class="first_old">
                                            <label>Tipo da Empresa:</label> 
                                            <label for="prestador"><input type="radio" name="tipo_empresa" id="prestador" value="1" checked="true" class="validate[required]" />PRESTADOR DE SERVIÇO</label>
                                                <p class="box-status">
                                                    <?php if($dados[0]['tipo_empresa'] == 1){$selectAtivo = "checked='checked'"; } ?>
                                                    <input type="radio" name="status" id="status_ativo" value="1" <?php echo $selectAtivo; ?>/><label for="status_ativo">Ativo</label>
                                                    <?php if($dados[0]['tipo_empresa'] == 0){$selectDesativado = "checked='checked'"; } ?>
                                                    <input type="radio" name="status" id="status_desativado" value="0" <?php echo $selectDesativado; ?> /><label for="status_desativado">Desativado</label>
                                                </p>
                                            <select name="cad_prestador" id="cad_prestador" class="validate[custom[select]] <?php echo $mostrar; ?>">
                                                <option value="-1">« Selecione um prestador »</option>
                                            </select>
                                        </p>
                                    </div>
                                    <p class="first_old">
                                        <label>Descrição:</label> 
                                        <textarea name="descricao" class="textarea"><?php echo $dados[0]['descricao'] ?></textarea>
                                    </p>
                                    <p class="first_old">
                                        <label>Número do Documento:</label> 
                                        <input type="text" name="num_documento" id="num_documento" class="input validate[required]" value='<?php echo $dados[0]['num_doc'] ?>' />
                                    </p>
                                    <p class="first_old">
                                        <label>Série da Documento:</label> 
                                        <input type="text" name="serie_documento" id="serie_documento" class="input validate[required]" value='<?php echo $dados[0]['serie_doc'] ?>' />
                                    </p>
                                    <p class="first_old">
                                        <label>Data de Emissão da NF:</label> 
                                        <input type="text" name="data_emissao" id="data_emissao" class="input" value='<?php echo $dados[0]['data_emissao'] ?>' />
                                    </p>

                                </div>
                            </fieldset>

                            <fieldset class="margin-top padding-bottom">
                                <legend>COMPETÊNCIA DA NOTA</legend>
                                <div class="padding_50">
                                    <p class="first_old"><label>Mês:</label> <?php echo montaSelect($meses, $dados[0]['mes_competencia'], "id='cad_mes' name='cad_mes', class='validate[custom[select]]'") ?></p>
                                    <p class="first_old"><label>Ano:</label> <?php echo montaSelect($anos, (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? $dados[0]['ano_competencia'] : date("Y"), "id='cad_ano' name='cad_ano', class='validate[custom[select]]'") ?></p>
                                    <p class="first_old">
                                        <label>Valor Bruto:</label>
                                        <?php $valor_bruto = str_replace(".", ",", $dados[0]['valor_bruto']); ?>
                                        <input type="text" name="valor_bruto" id="valor_bruto" class="input validate[required]" value='<?php echo $valor_bruto; ?>' />
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
                    <p class="controls"> 
                        <input type="button" class="button" value="cancelar" name="cancelar" id="cancelar" /> 
                        <input type="button" class="button" value="<?php echo (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? "Editar" : "Cadastrar"; ?>" name="<?php echo (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? "editar" : "cadastrar"; ?>" id="<?php echo (isset($_REQUEST['nota']) && !empty($_REQUEST['nota'])) ? "editar" : "cadastrar"; ?>" /> 
                        <p class="first_old"><input type="hidden" name="acao" id="acao" value="<?php echo $_REQUEST['acao']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="nota" id="nota" value="<?php echo $_REQUEST['nota']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="regiao_selected" id="regiao_selected" value="<?php echo $dados[0]['id_regiao']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="projeto_selected" id="projeto_selected" value="<?php echo $dados[0]['id_projeto']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="regiao_prestador_selected" id="regiao_prestador_selected" value="<?php echo $dados[0]['id_regiao_prestador']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="projeto_prestador_selected" id="projeto_prestador_selected" value="<?php echo $dados[0]['id_projeto_prestador']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="subgrupo_selected" id="subgrupo_selected" value="<?php echo $dados[0]['id_subgrupo']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="tipo_selected" id="tipo_selected" value="<?php echo $dados[0]['cod_tipo']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="status_selected" id="status_selected" value="<?php echo $dados[0]['status_prestador']; ?>" /></p>
                        <p class="first_old"><input type="hidden" name="prestador_selected" id="prestador_selected" value="<?php echo $dados[0]['id_prestador']; ?>" /></p>
                    </p>
                </fieldset>      
             </form>
        </div>
    </body>
</html>