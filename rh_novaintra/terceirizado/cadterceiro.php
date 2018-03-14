<?php
if(empty($_COOKIE['logado'])){
	print 'Efetue o Login<br><a href="../../login.php">Logar</a>';
	exit;
}

include('../../conn.php');

if(!empty($_REQUEST['uf'])){
    $uf = $_REQUEST['uf'];
    $qr_municipios = mysql_query("SELECT * FROM municipios WHERE sigla = '$uf' ORDER BY municipio");
    while($row_municipios = mysql_fetch_array($qr_municipios)){
        $retorno .= '<option value="'.utf8_encode($row_municipios['municipio']).'">'.utf8_encode($row_municipios['municipio']).'</option>';
    }
    echo $retorno; exit;
}

$id_user     = $_COOKIE['logado'];

$sql_user = "SELECT * FROM funcionario WHERE id_funcionario = '$id_user'";
$result_user = mysql_query($sql_user);
$row_user    = mysql_fetch_array($result_user);
$projeto   = $row_user['projeto'];

$id_regiao   = $_REQUEST['regiao'];
$projeto     = $_REQUEST['pro'];

$sql_projeto = "SELECT * FROM projeto WHERE id_projeto = '$projeto'";

$REPro       = mysql_query($sql_projeto);
$RowPro      = mysql_fetch_array($REPro);
$tipo_contratacao = $_GET['tipo'];

// Bloqueio Administração
echo bloqueio_administracao($id_regiao);

if(empty($_REQUEST['update'])) {
	
    include('../../classes/regiao.php');
    include('../../wfunction.php');
    $REG = new regiao();
    $resut_maior = mysql_query ("SELECT CAST(campo3 AS UNSIGNED) campo3, MAX(campo3) FROM terceirizado WHERE id_regiao= '$id_regiao' AND id_projeto ='$projeto' AND campo3 != 'INSERIR' GROUP BY campo3 DESC LIMIT 0,1");
    $row_maior = mysql_fetch_array ($resut_maior); 
    $codigo = $row_maior[0] + 1;
    $codigo = sprintf("%04d",$codigo);

    $usuario = carregaUsuario();
    
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
    $breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cadastrar Terceirizado");
    $breadcrumb_pages = array("Lista Projetos" => "../ver.php", "Visualizar Projeto" => "../ver.php?projeto=$projeto"); 
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: Cadastrar Terceirizado</title>
            <link href="../../favicon.png" rel="shortcut icon" />

            <!-- Bootstrap -->
            <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
            <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
            <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
            <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
            <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
            <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
            <link href="../../css/progress.css" rel="stylesheet" type="text/css">
            <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
            <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
            <link href="../../resources/css/add-ons.min.css" rel="stylesheet">
            <link rel="stylesheet" href="../../jquery/thickbox/thickbox.css" type="text/css" media="screen" />
        </head>
        <body>
            <?php include("../../template/navbar_default.php"); ?>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small> - Cadastrar Terceirizado</small></h2></div>
                    </div>
                </div>
                <form action="<?=$_SERVER['PHP_SELF']?>" method="post" name="form1" id="form1" class="form-horizontal" enctype="multipart/form-data" onsubmit="return validaForm()">
                    <div class="panel panel-default">
                        <div class="panel-heading text-bold">DADOS DO PROJETO</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-xs-2">C&oacute;digo:</label>
                                <label class="control-label col-xs-4 text-left"><?=$codigo?></label>
                                <label class="control-label col-xs-2">Projeto:</label>
                                <label class="control-label col-xs-4 text-left"><?=$RowPro['0']." - ".$RowPro['nome']?></label>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Atividade:</label>
                                <?php 
                                $sql_curso = "SELECT * FROM curso WHERE campo3 = '$projeto' AND tipo = '3' AND id_regiao = '$id_regiao' ORDER BY campo3 ASC";
                                $result_curso = mysql_query($sql_curso);
                                $verifica_curso = mysql_num_rows($result_curso);
                                if(!empty($verifica_curso)) {
                                    echo "<div class='col-xs-4'><select name='atividade' id='atividade' class='form-control'>";
                                    while($row_curso = mysql_fetch_array($result_curso)) {
                                        echo "<option value='$row_curso[0]'>$row_curso[0] - {$row_curso['nome']}</option>";
                                    }
                                    echo "</select></div>";
                                } else {
                                    echo "<label class='control-label col-xs-4 text-left'>Nenhuma Atividade Cadastrada</label>";
                                } ?>
                                <label class="control-label col-xs-2">Prestador:</label>
                                <?php $result_prestador = mysql_query("SELECT * FROM prestadorservico WHERE id_regiao = '$id_regiao' and id_projeto = '$projeto';"); // and prestador_tipo = 9
                                $verifica_prestador = mysql_num_rows($result_prestador);
                                if(!empty($verifica_prestador)) {
                                    echo "<div class='col-xs-4'><select name='id_prestador' id='id_prestador' class='form-control'>";
                                    while($row_prestador = mysql_fetch_array($result_prestador)) {
                                        echo "<option value='{$row_prestador['id_prestador']}'>{$row_prestador['id_prestador']} - {$row_prestador['c_fantasia']}</option>";
                                    }
                                    echo "</select></div>";
                                } else {
                                    echo "<label class='control-label col-xs-4 text-left'>Nenhuma Prestador Cadastrado</label>";
                                } ?>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Unidade:</label>
                                <?php $result_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
                                $verifica_unidade = mysql_num_rows($result_unidade);
                                if(!empty($verifica_unidade)) {
                                    echo "<div class='col-xs-4'><select name='locacao' id='locacao' class='form-control'>";
                                    while ($row_unidade = mysql_fetch_array($result_unidade)) {
                                        echo "<option value='$row_unidade[id_unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
                                    }
                                    echo "</select></div>";
                                } else {
                                    echo "<label class='control-label col-xs-4 text-left'>Nenhuma Unidade Cadastrada</label>";
                                } ?>
                                <?php if($tipo_contratacao == 4) { ?>
                                    <label class="control-label col-xs-2"></label>
                                    <label class="control-label col-xs-4 text-left"><input type="checkbox" name="contrato_medico" value="1"/> Necessita de contrato para médicos?</label>
                                <?php } ?>
                            </div>
                            <div class="form-group" style="display: none;">
                                <label class="control-label col-xs-2">Tipo de Contratação:</label>
                                <label class="control-label col-xs-2 text-left"><input name='contratacao' type='radio' class="reset" id='contratacao' value='3' <?=($_GET['tipo'] == "3") ? "checked" : '' ?> > Cooperado</label>
                                <label class="control-label col-xs-2 text-left"><input name='contratacao' type='radio' class="reset" id='contratacao' value='4' <?=($_GET['tipo'] == "4") ? "checked" : '' ?> > Autônomo / PJ</label>
                            </div>
                        </div>
                        <div class="panel-heading text-bold border-t">DADOS PESSOAIS</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-xs-2">Nome:</label>
                                <div class="col-xs-4"><input name="nome" type="text" id="nome" class="form-control" onchange="this.value=this.value.toUpperCase()"/></div>
                                <label class="control-label col-xs-2">Data de Nascimento:</label>
                                <div class="col-xs-4"><input name="data_nasci" type="text" id="data_nasci" class="data form-control" maxlength="10" onkeyup="mascara_data(this);"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Sexo:</label>
                                <label class="control-label col-xs-2 text-left"><input name="sexo" type="radio" class="reset" value="M" checked="checked" /> Masculino</label>
                                <label class="control-label col-xs-2 text-left"><input name="sexo" type="radio" class="reset" value="F" /> Feminino</label>
                                <label class="control-label col-xs-2">CEP:</label>
                                <div class="col-xs-4"><input name="cep" type="text" id="cep" class="form-control" maxlength="9" onkeypress="formatar('#####-###', this)" onkeyup="pula(9,this.id,naturalidade.id)" /></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Endereço:</label>
                                <div class="col-xs-4"><input name="endereco" type="text" id="endereco" class="form-control" onchange="this.value=this.value.toUpperCase()"/></div>
                                <label class="control-label col-xs-2">Bairro:</label>
                                <div class="col-xs-4"><input name="bairro" type="text" id="bairro" class="form-control" onchange="this.value=this.value.toUpperCase()"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">UF:</label>
                                <div class="col-xs-4"><?php $ajax = 'class="form-control"'; $REG -> SelectUFajax('uf',$ajax); ?></div>
                                <label class="control-label col-xs-2">Cidade:</label>
                                <div class="col-xs-4" id="dvcidade"><select name="cidade" type="text" class="form-control" id="cidade"></select></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Telefone Fixo:</label>
                                <div class="col-xs-4"><input name="tel_fixo" type="text" id="tel_fixo" class="form-control" onKeyPress="return(TelefoneFormat(this,event))" onkeyup="pula(13,this.id,tel_cel.id)" /></div>
                                <label class="control-label col-xs-2" id="dvcidade">Celular:</label>
                                <div class="col-xs-4"><input name="tel_cel" type="text" id="tel_cel" class="form-control" onKeyPress="return(TelefoneFormat(this,event))" onkeyup="pula(13,this.id,tel_rec.id)" /></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">E-mail:</label>
                                <div class="col-xs-4"><input name="email" type="text" id="email" class="form-control" /></div>
                                <label class="control-label col-xs-2" id="dvcidade">Data de Entrada:</label>
                                <div class="col-xs-4"><input name="data_entrada" type="text" id="data_entrada" class="data form-control" maxlength="10" onkeyup="mascara_data(this);"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Data Saída:</label>
                                <div class="col-xs-4"><input name="data_saida" type="text" id="data_saida" class="data form-control" maxlength="10" onkeyup="mascara_data(this);"/></div>
                            </div>
                        </div>
                        <div class="panel-heading text-bold border-t">DOCUMENTAÇÃO</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-xs-2">Nº do RG:</label>
                                <div class="col-xs-4"><input name="rg" type="text" id="rg" class="form-control" maxlength="14" OnKeyPress="formatar('##.###.###-###', this)" onkeyup="pula(14,this.id,orgao.id)"></div>
                                <label class="control-label col-xs-2">Orgão Expedidor:</label>
                                <div class="col-xs-4"><input name="orgao" type="text" id="orgao" class="form-control" onChange="this.value=this.value.toUpperCase()"/></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">UF:</label>
                                <div class="col-xs-4"><input name="uf_rg" type="text" id="uf_rg" class="form-control" maxlength="2" onKeyUp="pula(2,this.id,data_rg.id)" onChange="this.value=this.value.toUpperCase()"/></div>
                                <label class="control-label col-xs-2">Data Expedição:</label>
                                <div class="col-xs-4"><input name="data_rg" type="text" class="data form-control" maxlength="10" id="data_rg" onkeyup="mascara_data(this); pula(10,this.id,cpf.id)" /></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">CPF:</label>
                                <div class="col-xs-4"><input name="cpf" type="text" id="cpf" class="form-control" maxlength="14" onKeyPress="formatar('###.###.###-##', this)" onkeyup="pula(14,this.id,reservista.id)"/></div>
                                <label class="control-label col-xs-2">Carteira do Conselho:</label>
                                <div class="col-xs-4"><input name="conselho" type="text" id="conselho" class="form-control" /></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-xs-2">Data de Emissão:</label>
                                <div class="col-xs-4"><input name="data_emissao" type="text" class="data form-control" maxlength="10" id="data_emissao" onkeyup="mascara_data(this); pula(10,this.id,reservista.id)" /></div>
                                <label class="control-label col-xs-2">PIS:</label>
                                <div class="col-xs-4"><input name="pis" type="text" id="pis" class="form-control" maxlength="11" onkeyup="pula(11,this.id,data_pis.id)"/></div>
                            </div>
                        </div>
                        <div class="panel-heading text-bold border-t">HORÁRIOS</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-xs-2">Hora Retirada:</label>
                                <div class="col-xs-1"><input name="hora_retirada" type="text" id="hora_retirada" class="form-control no-padding-hr text-center" maxlength="8" OnKeyPress="formatar('##:##:##', this)" /></div>
                                <label class="control-label col-xs-2">Hora Almoço:</label>
                                <div class="col-xs-1"><input name="hora_almoco" type="text" id="hora_almoco" class="form-control no-padding-hr text-center" maxlength="8" OnKeyPress="formatar('##:##:##', this)" /></div>
                                <label class="control-label col-xs-2">Hora Retorno:</label>
                                <div class="col-xs-1"><input name="hora_retorno" type="text" id="hora_retorno" class="form-control no-padding-hr text-center" maxlength="8" OnKeyPress="formatar('##:##:##', this)" /></div>
                                <label class="control-label col-xs-2">Hora Saída:</label>
                                <div class="col-xs-1"><input name="hora_saida" type="text" id="hora_saida" class="form-control no-padding-hr text-center" maxlength="8" OnKeyPress="formatar('##:##:##', this)" /></div>
                            </div>
                        </div>
                        <div class="panel-heading text-bold border-t">FOTO</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="control-label col-xs-2">Arquivo:</label>
                                <div class="col-xs-4"><input type="file" name="arquivo" class="form-control" id="arquivo" /></div>
                            </div>
                        </div>
                        <div class="panel-footer text-center">
                            <div class="alert alert-warning">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                            <input type='hidden' name='regiao' value='<?=$id_regiao?>'/>
                            <input type='hidden' name='id_cadastro' value='4'>
                            <input type='hidden' name='projeto' value='<?=$projeto?>'>
                            <input type='hidden' name='user' value='<?=$id_user?>'>
                            <input type='hidden' name='update' value='1'>
                            <input type="submit" name="Submit" value="CADASTRAR" class="btn btn-primary" />
                        </div>
                    </div>
                </form>
                <?php include_once '../../template/footer.php'; ?>
            </div>
            <script src="../../js/jquery-1.10.2.min.js"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../../resources/js/bootstrap.min.js"></script>
            <script src="../../resources/js/bootstrap-dialog.min.js"></script>
            <script src="../../js/jquery.validationEngine-2.6.js"></script>
            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
            <script src="../../js/jquery.mask.min.js" type="text/javascript"></script>
            <script src="../../resources/js/main.js"></script>
            <script src="../../js/global.js"></script>
            <script src="../../uploadfy/scripts/jquery.uploadify.v2.1.0.js" type="text/javascript"></script>
            <script src="../../uploadfy/scripts/swfobject.js" type="text/javascript"></script>
            <script src="../../jquery/priceFormat.js" type="text/javascript"></script>
            <script type="text/javascript" src="../../js/valida_documento.js"></script>
            <script language="javascript" src="../../js/ramon.js"></script>
            <script type="text/javascript">
            $(function(){
                
                $('#uf').on('change', function(){
                    if($(this).val() != ''){
                        $.post("cadterceiro.php", {bugger:Math.random(), uf:$(this).val()}, function(resultado){
                            $("#cidade").html(resultado);
                        });
                    }
                });
    
                var tipoVerifica = 0;
                $("select[name*='banco']").change(function(){
                    function tipoPgCheque(){
			$("select[name='tipopg']").find('option').attr('disabled',false).attr('selected',false);
			$("select[name='tipopg']").find('option').each(function(){
                            if($(this).text() == "Cheque"){
                                $(this).attr('selected',true);
                            } else {
                                $(this).attr('disabled',true);
                            }
			});
                    }
		
                    function tipoPgConta(){
                        $("select[name='tipopg']").find('option').attr('disabled',false).attr('selected',false);
                        $("select[name='tipopg']").find('option').each(function(){
                            if($(this).text() == "Depósito em Conta Corrente"){
                                $(this).attr('selected',true);
                            }else{
                                $(this).attr('disabled',true);
                            }	
                        });
                    }
		
                    var valor = $(this).val();
                    if(valor == 0){
                        desabilita()
                        tipoPgCheque();
                        tipoVerifica = 1;
                    } else if(valor == 9999){
                        Ativa()
                        tipoPgCheque();
                        tipoVerifica = 2;
                    } else {
                        Ativa();
                        tipoPgConta();
                        tipoVerifica = 3;
                        $("input[name='nomebanco']").attr("disabled", true);
                    }
                });
	
                function desabilita(){
                    $("input[name*='conta']").attr("disabled", true);
                    $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", true);
                    $("input[name*='agencia']").attr("disabled", true);
                    $("input[name='nomebanco']").attr("disabled", true);
                }
	
                function Ativa(){
                    $("input[name*='conta']").attr("disabled", false);
                    $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", false);
                    $("input[name*='agencia']").attr("disabled", false);
                    $("input[name='nomebanco']").attr("disabled", false);
                }
	
                $("input[type*='button'][name*='Submit']").click(function(){
                    var indice = new Array();
                    if(tipoVerifica == 3){
			if($("input[name*='conta']").val() == ''){
                            indice.push("Conta");
			}
			if($("input[name*='agencia']").val() == ''){
                            indice.push("Agencia");
			}
			indiceRadio = 0;
			$("input[name*='radio_tipo_conta']").each(function(){
                            if($(this).is(':checked')){
                                indiceRadio = 1;
                            }
			});
			
			if(indiceRadio == 0){
                            indice.push("tipo de conta");
			}
			
                    } else if(tipoVerifica == 2){
			if($("input[name*='conta']").val() == ''){
                            indice.push("Conta");
			}
			if($("input[name*='agencia']").val() == ''){
                            indice.push("Agencia");
			}
			indiceRadio = 0;
			$("input[name*='radio_tipo_conta']").each(function(){
                            if($(this).is(':checked')){
                                indiceRadio = 1;
                            }
			});
			
			if(indiceRadio == 0){
                            indice.push("tipo de conta");
			}
			
			if($("input[name*='nomebanco']").val() == ""){
                            indice.push("Nome do banco");
			}
                    }
		
                    if(indice.length > 0){
                        alert("Preencha o(s) dado(s) "+indice.join(', '));
                    } else {
                        $('#form1').submit();
                    }
                });
            });
            </script>
            <script language="javascript"  type="text/javascript">
            function FuncaoInss(a) {
                d = document;
                if(a == 1) {
                    d.getElementById('divInss').style.display = '';
                    d.getElementById('p_inss').style.display = '';
                } else if(a == 2) {
                    d.getElementById('divInss').style.display = 'none';
                    d.getElementById('p_inss').style.display = 'none';
                    d.getElementById('p_inss').value = '';
                    d.getElementById('inss_recolher').value = 11;
                } else if(a == 3) {
                    porcentagem = d.getElementById('p_inss').value;
                    if(porcentagem <= 11) {
                        valor = 11 - porcentagem;
                    } else {
                        valor = 0;
                    }
                    d.getElementById('inss_recolher').value = valor;
                }
            }
            </script>
            <script>
            function validaForm(){
                d = document.form1;
                if (d.nome.value == "" ){
                    alert("O campo Nome deve ser preenchido!");
                    d.nome.focus();
                    return false;
                }
                if (d.endereco.value == "" ){
                    alert("O campo Endereço deve ser preenchido!");
                    d.endereco.focus();
                    return false;
                }
                if (d.data_nasci.value == "" ){
                    alert("O campo Data de Nascimento deve ser preenchido!");
                    d.data_nasci.focus();
                    return false;
                }
                if (d.rg.value == "" ){
                    alert("O campo RG deve ser preenchido!");
                    d.rg.focus();
                    return false;
                }

                var cpf = $('#cpf').val().replace('.','').replace('.','').replace('-','');             

                if (d.cpf.value == "" ){
                    alert("O campo CPF deve ser preenchido!");
                    d.cpf.focus();
                    return false;
                }
                if(!VerificaCPF(cpf)){
                    alert('Cpf Inválido');
                    d.cpf.focus();
                    return false;
                }

                if (d.inss[0].checked && d.p_inss.value == ""){
                    alert("Por Favor, digite a porcentagem INSS que ele recebe de terceiros!");
                    d.p_inss.focus();
                    return false;
                }
                if (d.localpagamento.value == "" ){
                    alert("O campo Local de Pagamento deve ser preenchido!");
                    d.localpagamento.focus();
                    return false;
                }
                return true;   
            }
            </script>
        </body>
    </html>
<?php 

        } else {
    
    // CADASTRO DE COOPERADO
    $regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    //DADOS CONTRATAÇÃO
    $vinculo = $_REQUEST['vinculo'];
    $id_curso = $_REQUEST['atividade'];
    $locacao = $_REQUEST['locacao'];
    $tipo_contratacao = $_REQUEST['contratacao'];
    $contrato_medico = ($_POST['contrato_medico']) ? $_POST['contrato_medico'] : '0';
    
    $matricula = $_POST['matricula'];
    $n_processo = $_POST['n_processo'];

    //DADOS CADASTRAIS
    $nome = mysql_real_escape_string($_REQUEST['nome']);
    $sexo = $_REQUEST['sexo'];
    $id_prestador = $_REQUEST['id_prestador'];
    
    
    $endereco = mysql_real_escape_string($_REQUEST['endereco']);
    $bairro = mysql_real_escape_string($_REQUEST['bairro']);
    $cidade =mysql_real_escape_string( $_REQUEST['cidade']);
    $uf = $_REQUEST['uf'];
    $cep = $_REQUEST['cep'];
    $tel_fixo = $_REQUEST['tel_fixo'];
    $tel_cel = $_REQUEST['tel_cel'];
    $tel_rec = $_REQUEST['tel_rec'];
    $data_nasci = $_REQUEST['data_nasci'];
    $naturalidade = $_REQUEST['naturalidade'];
    $nacionalidade = $_REQUEST['nacionalidade'];
    $civil = $_REQUEST['civil'];
    //DOCUMENTAÇÃO
    $rg = $_REQUEST['rg'];
    $uf_rg = $_REQUEST['uf_rg'];
    $secao = $_REQUEST['secao'];
    $data_rg = $_REQUEST['data_rg'];
    $cpf = $_REQUEST['cpf'];
    $conselho = $_REQUEST['conselho'];
    $titulo = $_REQUEST['titulo'];
    $zona = $_REQUEST['zona'];
    $orgao = $_REQUEST['orgao'];
    $inss_recolher = $_REQUEST['inss_recolher'];
    //Horário
    $hora_retirada = $_REQUEST['hora_retirada'];
    $hora_almoco = $_REQUEST['hora_almoco'];
    $hora_retorno = $_REQUEST['hora_retorno'];
    $hora_saida = $_REQUEST['hora_saida'];
    $pis = $_REQUEST['pis'];

    $email = $_POST['email'];


//Inicio Verificador CPF
//$qrCpf = mysql_query("SELECT COUNT(id_terceirizado) AS total FROM terceirizado WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND tipo_contratacao = '$tipo_contratacao'");
$qrCpf = mysql_query("SELECT COUNT(id_terceirizado) AS total FROM terceirizado WHERE cpf = '$cpf' AND id_projeto = '$id_projeto'");
$rsCpf = mysql_fetch_assoc($qrCpf);
$totalCpf = $rsCpf['total'];
if($totalCpf > 0){ ?>

<script type="text/javascript">
        alert("Esse CPF já existe para esse projeto");
        window.history.back();
</script>

<?php exit(); }
//Fim verificador CPF

//Inicio verificador PIS
    if(strlen($pis) != 11)
    {
    ?>
        <script type="text/javascript">
            //alert("PIS Inválido!");
            //window.history.back();
        </script>
    <?php
        //exit();
    }
//Fim verificador PIS

 
/* 
Função para converter a data
De formato nacional para formato americano.
Muito útil para você inserir data no mysql e visualizar depois data do mysql.
*/

function ConverteData($Data)
{
    if (strstr($Data, "/"))//verifica se tem a barra /
    {
	$d = explode ("/", $Data);//tira a barra
	$rstData = "$d[2]-$d[1]-$d[0]";//separa as datas $d[2] = ano $d[1] = mes etc...
	return $rstData;
    }elseif(strstr($Data, "-"))
    {
	$d = explode ("-", $Data);
	$rstData = "$d[2]/$d[1]/$d[0]"; 
	return $rstData;
    }else{
	return "Data invalida";
    }
}


$data_nasci   = ConverteData($data_nasci);
$data_rg      = ConverteData($data_rg);
$pis_data     = ConverteData($pis_data);
$exame_data   = ConverteData($exame_data);
$trabalho_data = ConverteData($trabalho_data);
$c_nascimento = ConverteData($c_nascimento);
$e_dataemissao = ConverteData($e_dataemissao);
$data_emissao = ConverteData($data_emissao);
$data_entrada = ConverteData($_REQUEST['data_entrada']);
$data_saida = ConverteData($_REQUEST['data_saida']);


$data_cadastro = date('Y-m-d');
//VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA AUTÔNOMO
$verificando_cooperado = mysql_query("SELECT nome FROM terceirizado WHERE nome = '$nome' AND data_nasci = '$data_nasci' AND rg = '$rg' AND status = '1'");
$row_verificando_cooperado = mysql_num_rows($verificando_cooperado);

if (!empty($row_verificando_cooperado)) 
    {
    print "
	<html>
	<head>
	<title>:: Intranet ::</title>
	</head>
	<body bgcolor='#D7E6D5'>
	<center>
	<br>ESTE FUNCIONÁRIO JA ESTÁ CADASTRADO: <font color=#FFFFFF><b>$row_verificando_cooperado[nome]</b></font>
	</center>
	</body>
	</html>
    ";
    exit; 
} else 
{ 
    //------------------------------
    //FAZENDO O UPLOAD DA FOTO
    $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

//    var_dump($_FILES);

    $sql = "
	    INSERT INTO ispv_netsorrindo.terceirizado (id_regiao, id_projeto, id_unidade, 
			id_curso, id_prestador, nome, cpf, rg, carteira_conselho, 
			uf_conselho, endereco, numero, complemento, bairro, cidade, 
			carteira_conselho_emissao, uf, status, 
			data_cad, user_cad, data_alter, user_alter, 
			obs, data_nasci, pis, 
			sexo, tel_cel, tel_fixo, cep, email, orgao, data_rg, uf_rg, 
			hora_retirada, hora_almoco, hora_retorno, hora_saida, data_entrada, data_saida, contrato_medico) 
	    VALUES ('$regiao', '$id_projeto', '$locacao', '$id_curso', '$id_prestador', '$nome', '$cpf', '$rg', '$conselho', 
	    '$uf_conselho', '$endereco', '$numero', '$complemento', '$bairro', '$cidade', '$carteira_conselho_emissao', '$uf', '$status',
	    '$data_cadastro', '$user_cad', '$data_alter', '$user_alter',
	    '$obs', '$data_nasci', '$pis', '$sexo', '$tel_cel', 
	    '$tel_fixo', '$cep', '$email', '$orgao', '$data_rg', '$uf_rg',
	    '$hora_retirada', '$hora_almoco', '$hora_retorno', '$hora_saida', '$data_entrada', '$data_saida', '$contrato_medico');";
	
//    var_dump($prestador_id);
//    echo '<br>';
//    echo "sql = [$sql]<br>\n";
//    exit();
    
    
    mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error());
    $row_id_participante = mysql_insert_id();
    $row_id_clt = $row_id_participante;
    
    if(!$arquivo)
    {
	$mensagem = "Não acesse esse arquivo diretamente!";
    }else
    {
	// Imagem foi enviada, então a move para o diretório desejado
	echo "nome = [{$arquivo['tmp_name']}]<br>\n";
	if(trim($arquivo['tmp_name']) != "")
	{
	    $nome_arq = str_replace(" ", "_", $nome);
	    $tipo_arquivo = ".gif";
		// Resolvendo o nome e para onde o arquivo será movido
	    $diretorio = "../../fotos/";
		$nome_tmp = $regiao."_".$id_projeto."_".$row_id_participante.$tipo_arquivo;
		$nome_arquivo = "$diretorio$nome_tmp" ;

		//echo "<br>\nnome_arquivo = [$nome_arquivo]<br>\n";

		move_uploaded_file($arquivo['tmp_name'], $nome_arquivo ) or die ("Erro ao enviar o Arquivo: $nome_arquivo");

		$sql = "update ispv_netsorrindo.terceirizado set foto = '$nome_arquivo' where id_terceirizado = $row_id_participante;";
		mysql_query ($sql) or die ("Ops! Erro<br>" . mysql_error());
	}
	    
    }    
} 
// AQUI TERMINA DE INSERIR OS DADOS DO COOPERADO


header("Location: ver_terceiro.php?reg=$regiao&id=$row_id_participante&pro=$id_projeto&sucesso=cadastro&tipo=$tipo_contratacao");
exit;
}
?>