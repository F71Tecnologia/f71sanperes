<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

function removeAspas($str) {
    $str = str_replace("'", "", $str);
    return str_replace('"', '', $str);
}

include('../conn.php');
include('../classes/regiao.php');
include('../wfunction.php');
include('../classes/SetorClass.php');
include('../classes/PlanoSaudeClass.php');

$setorObj = new SetorClass();
$objPlanoSaude = new PlanoSaudeClass();

$usuario = carregaUsuario();

$setorObj->setProjeto($_REQUEST['projeto']);
$setorObj->getSetor();
$arraySetor[''] = '--Selecione--';
while ($setorObj->getRowSetor()) {
    $arraySetor[$setorObj->getIdSetor()] = $setorObj->getNome();
}

if ($_COOKIE['logado'] == 276 && $_COOKIE['debug'] == 123) {
    echo '<pre>';
    print_r($arraySetor);
    echo '</pre>';
}

$objPlanoSaude->getPlanoSaude();
$arrayPlanoSaude[''] = '--Selecione o Plano de Saúde--';
while ($objPlanoSaude->getRowPlanoSaude()) {
    $arrayPlanoSaude[$objPlanoSaude->getIdPlanoSaude()] = $objPlanoSaude->getRazao();
}

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$REG = new regiao();

$qr_nacionalidade = mysql_query("select * from cod_pais_rais");

$sqlFolhaFechada = mysql_fetch_assoc(mysql_query("SELECT mes, ano FROM rh_folha WHERE status = 3 AND terceiro = 2 AND projeto = {$_REQUEST['projeto']} AND regiao = {$_REQUEST['regiao']} ORDER BY id_folha DESC LIMIT 1;"));

$arrayCboMedico = array(5433, 5410, 5489, 5462, 5494, 5436, 5425, 5426, 2987, 2894);

//SELECIONA TODOS OS TIPOS DE ADMISSAO
$tiposAdmi = montaQuery("rhstatus_admi", "*");
$arrayTipoAdmi = array("" => "« Selecione o tipo de admissão »");
foreach ($tiposAdmi as $tipoAdmi) {
    $arrayTipoAdmi[$tipoAdmi['id_status_admi']] = $tipoAdmi['codigo'] . " - " . $tipoAdmi['especifica'];
}

if (empty($_REQUEST['update'])) {

    $id_regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $id_setor = $_REQUEST['id_setor'];
    $idcurso = $_REQUEST['idcursos'];

    // id do departamento / setor do clt para select setor
    $query_x = "SELECT id_departamento FROM curso WHERE id_curso= $idcurso";
    $result_x = mysql_query($query_x);
    $row_x = mysql_fetch_assoc($result_x);
    $id_departamento = $row_x['id_departamento'];


    if ($id_regiao == '28') {
        $qr_maior = mysql_query("SELECT MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR'");
        $codigo = mysql_result($qr_maior, 0) + 1;
    } else {
        $resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
        $row_maior = mysql_num_rows($resut_maior);
        $codigo = $row_maior + 1;
    }

    if (isset($_REQUEST['id'])) {
        $id_clt = $_REQUEST['id'];
        $query_clt = "SELECT *, date_format(data_nasci, '%d/%m/%Y') AS data_nasci, 
								 date_format(data_rg, '%d/%m/%Y') AS data_rg, 
								 date_format(data_escola, '%d/%m/%Y') AS data_escola, 
								 date_format(data_entrada, '%d/%m/%Y') AS data_entrada, 
								 date_format(data_exame, '%d/%m/%Y') AS data_exame, 
								 date_format(data_saida, '%d/%m/%Y') AS data_saida, 
								 date_format(data_ctps, '%d/%m/%Y') AS data_ctps, 
								 date_format(data_nasc_pai, '%d/%m/%Y') AS data_nasc_pai, 
								 date_format(data_nasc_mae, '%d/%m/%Y') AS data_nasc_mae, 
								 date_format(data_nasc_conjuge, '%d/%m/%Y') AS data_nasc_conjuge, 
								 date_format(data_nasc_avo_h, '%d/%m/%Y') AS data_nasc_avo_h, 
								 date_format(data_nasc_avo_m, '%d/%m/%Y') AS data_nasc_avo_m, 
								 date_format(data_nasc_bisavo_h, '%d/%m/%Y') AS data_nasc_bisavo_h, 
								 date_format(data_nasc_bisavo_m, '%d/%m/%Y') AS data_nasc_bisavo_m, 
								 date_format(dada_pis, '%d/%m/%Y') AS dada_pis,
                                                                 date_format(data_emissao, '%d/%m/%Y') AS data_emissao 
								 FROM rh_clt WHERE id_clt = '$id_clt'";
        $result_clt = mysql_query($query_clt);
        $clt = mysql_fetch_assoc($result_clt);

        $result_depe = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS datas1,
									  date_format(data2, '%d/%m/%Y') AS datas2,
									  date_format(data3, '%d/%m/%Y') AS datas3,
									  date_format(data4, '%d/%m/%Y') AS datas4,
									  date_format(data5, '%d/%m/%Y') AS datas5,
									  date_format(data6, '%d/%m/%Y') AS datas6
							     FROM dependentes WHERE id_bolsista = '$referencia' AND id_projeto = '$id_projeto' AND contratacao = '$clt[tipo_contratacao]'");
        $row_depe = mysql_fetch_array($result_depe);



        $checked_pai = ($row_depe['ddir_pai'] == 1) ? 'checked="checked"' : '';
        $checked_mae = ($row_depe['ddir_mae'] == 1) ? 'checked="checked"' : '';
        $checked_conjuge = ($row_depe['ddir_conjuge'] == 1) ? 'checked="checked"' : '';
        $checked_avo_h = ($row_depe['ddir_avo_h'] == 1) ? 'checked="checked"' : '';
        $checked_avo_m = ($row_depe['ddir_avo_m'] == 1) ? 'checked="checked"' : '';
        $checked_bisavo_h = ($row_depe['ddir_bisavo_h'] == 1) ? 'checked="checked"' : '';
        $checked_bisavo_m = ($row_depe['ddir_bisavo_m'] == 1) ? 'checked="checked"' : '';

        $checked_portador1 = ($row_depe['portador_def1'] == 1) ? 'checked="checked"' : '';
        $checked_portador2 = ($row_depe['portador_def2'] == 1) ? 'checked="checked"' : '';
        $checked_portador3 = ($row_depe['portador_def3'] == 1) ? 'checked="checked"' : '';
        $checked_portador4 = ($row_depe['portador_def4'] == 1) ? 'checked="checked"' : '';
        $checked_portador5 = ($row_depe['portador_def5'] == 1) ? 'checked="checked"' : '';
        $checked_portador6 = ($row_depe['portador_def6'] == 1) ? 'checked="checked"' : '';

        $checked_dep1_cur_fac_ou_tec = ($row_depe['dep1_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
        $checked_dep2_cur_fac_ou_tec = ($row_depe['dep2_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
        $checked_dep3_cur_fac_ou_tec = ($row_depe['dep3_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
        $checked_dep4_cur_fac_ou_tec = ($row_depe['dep4_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
        $checked_dep5_cur_fac_ou_tec = ($row_depe['dep5_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
        $checked_dep6_cur_fac_ou_tec = ($row_depe['dep6_cur_fac_ou_tec'] == 1) ? 'checked="checked"' : '';
    }


    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'verificaCpf') {

        $query = "SELECT A.id_clt,A.nome,B.nome AS projeto, B.id_projeto, C.especifica
                    FROM rh_clt AS A
                    LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                    LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo)
                    WHERE A.cpf = '{$_REQUEST['cpf']}' AND (A.`status` != 10 || A.status != 200 || A.status != 40) AND B.id_master = {$usuario['id_master']};";
        $sql = mysql_query($query) or die('Erro ao selecionar ex funcionario');
        $dados = array("status" => 0);
        $d = array();
        if (mysql_num_rows($sql)) {
            while ($linha = mysql_fetch_assoc($sql)) {
                $d['id'] = $linha['id_clt'];
                $d['nome'] = $linha['nome'];
                $d['projeto'] = $linha['projeto'];
                $d['idprojeto'] = $linha['id_projeto'];
                $d['status'] = $linha['especifica'];
            }

            $dados = array("status" => 1, "dados" => $d);
        }

        echo json_encode($dados);
        exit();
    }
    
    /**
     * 
     */
    if(isset($_REQUEST) && !empty($_REQUEST)){
        if($_REQUEST['method'] == "carregaFuncaoPorSetor"){
            
            $dados = array();
            
            try{
                
                $setor = $_REQUEST['idSetor'];
                
                $query = "SELECT B.id_curso, B.nome, B.salario
                            FROM setor_curso_assoc AS A
                                    LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                            WHERE A.id_setor = '{$setor}' AND A.id_projeto = '{$projeto}'"; 
                $sql = mysql_query($query);
                if ($_COOKIE['logado'] == 299 && $_COOKIE['debug'] == 666) {
                    echo '<pre>';
                    print_r($query);
                    echo '</pre>';
                }
   
                while($rows = mysql_fetch_assoc($sql)){
                    $dados[$rows['id_curso']] = array(
                                                    "nome" => utf8_encode($rows['nome']),
                                                    "salario" => $rows['salario']
                                                );
                }
                
            }  catch (Exception $e){
                echo $e->getMessage();
            }     
            
            //print_r($dados);
            
            echo json_encode($dados);
            exit();
        }
        
        if($_REQUEST['method'] == "carregaHorarioPorFuncao"){
            
            $dados = array();
            
            try{
                
                $curso = $_REQUEST['idCurso'];
                
                $query = "SELECT * FROM rh_horarios WHERE funcao = '$curso' AND id_regiao = '$id_regiao'";
                $sql = mysql_query($query);
                $tot = mysql_num_rows($sql);
                
//                if ($_COOKIE['logado'] == 260 && $_COOKIE['debug'] == 666) {
//                    echo '<pre>';
//                    print_r($query);
//                    echo '</pre>';
//                }
   
                while($rows = mysql_fetch_assoc($sql)){
                    $dados[$rows['id_horario']] = array(
                        "nome" => utf8_encode($rows['nome']),
                        "entrada_1" => $rows['entrada_1'],
                        "saida_1" => $rows['saida_1'],
                        "entrada_2" => $rows['entrada_2'],
                        "saida_2" => $rows['saida_2']
                    );
                }
                
            }  catch (Exception $e){
                echo $e->getMessage();
            }     
            
            $dados['tot'] = $tot;
            
            //print_r($dados);
            
            echo json_encode($dados);
            exit();
        }
    }
    ?>

    <html>
        <head>
            <title>:: Intranet ::</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link rel="shortcut icon" href="../favicon.ico">
            <link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
            <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="consulta.js"></script>
            <script src="../js/ramon.js" type="text/javascript" language="javascript"></script>
            <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
            <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<!--            <script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>-->
            <script type="text/javascript" src="../jquery/priceFormat.js"></script>
            <script type="text/javascript" src="../js/valida_documento.js"></script>
            <script type="text/javascript" src="../js/jquery.maskedinput.min.js"></script>
            <script type="text/javascript" src="../js/jquery.maskMoney_3.0.2.js"></script>
            <script src="../js/jquery.validationEngine-2.6.js"></script>
            <script src="../js/jquery.validationEngine-pt.js"></script>
            <script src="../js/global.js" type="text/javascript"></script>
            <script type="text/javascript">

                function mostraInss(display){
                    if(display == 'none'){
                        $('.outra_empresa').css('display', 'none');
                    }else{
                        $('.outra_empresa').css('display', '');
                    }
                }

                /*
                 * Função com mascara para telefone
                 * Autor: Leonardo
                 * data: 30/04/2014
                 * @returns {undefined}
                 */
                jQuery.fn.brTelMask = function () {

                    return this.each(function () {
                        var el = this;
                        $(el).focus(function () {
                            $(el).mask("(99) 9999-9999?9", {placeholder: " "});
                        });

                        $(el).focusout(function () {
                            var phone, element;
                            element = $(el);
                            element.unmask();
                            phone = element.val().replace(/\D/g, '');
                            if (phone.length > 10) {
                                element.mask("(99) 99999-999?9");
                            } else {
                                element.mask("(99) 9999-9999?9");
                            }
                        });
                    });
                };


                /*
                 * Função para validar CPF
                 * Autor: Leonardo
                 * data: 30/04/2014
                 * @param {type} field
                 * @returns {String}
                 */
                var verificaCPF = function (field) {

                    var value = field.val();

                    value = value.replace('.', '');
                    value = value.replace('.', '');
                    var cpf = value.replace('-', '');

                    if (!VerificaCPF(cpf)) {
                        return "CPF inválido";
                    }
                };

                /*
                 * Função para validar PIS
                 * Autor: Leonardo
                 * data: 30/04/2014
                 * @param {type} field
                 * @returns {String}
                 */
                var verificaPIS = function (field) {
                    var value = field.val();

                    value = value.replace('.', '');
                    value = value.replace('.', '');
                    var pis = value.replace('-', '');
                    if (ChecaPIS(pis) == false) {
                        return 'PIS inválido';
                    }
                };

                $(document).ready(function () {

                    //$("#data_entrada").datepicker({minDate: new Date(2014, 1 - 1, 1)});
//                    $("#data_entrada").datepicker({minDate: new Date(<?= ($sqlFolhaFechada['ano']) ? $sqlFolhaFechada['ano'] : date('Y') - 1 ?>, <?= ($sqlFolhaFechada['mes']) ? $sqlFolhaFechada['mes'] : date('m') - 1 ?>, 1)});
//                    $("#data_entrada").datepicker({showMonthAfterYear: true});
                    //$("#data_entrada").datepicker({dateFormat: "dd-mm-yy"});
                   
                    // máscaras
                    $("#cpf").mask("999.999.999-99", {placeholder: " "});
                    //                    $("#rg").mask("99.999.999-9", {placeholder: " "});
                    $("#cep").mask("99999-999", {placeholder: " "});
                    $(".tel").brTelMask();


                    $("#uf_nasc_text").hide();

                    $("#nacionalidade").change(function () {
                        if ($("#nacionalidade").val() != '10')
                        {
                            $("#uf_nasc_select").hide();
                            $("#uf_nasc_text").show();
                        }
                        else
                        {
                            $("#uf_nasc_text").hide();
                            $("#uf_nasc_select").show();
                        }
                    });

                    /*
                     *    INÍCIO DAS ALTERAÇÕES FEITAS POR PV PARA  BUSCA CEP E E-SOCIAL
                     */

                    /* carrega municípios para o campo cidade */
                    $('#uf').change(function () {
                        var uf = $('#uf').val();
                        $('#cidade').val('');
                        $('#cod_cidade').val('');
                        $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                            $("#cidade").autocomplete({source: data.municipios,
                                change: function (event, ui) {
                                    if (event.type == 'autocompletechange') {
                                        var valor_municipio = ui.item.value.split(')-');
                                        $('#cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                                        $('#cidade').val(valor_municipio[1].trim());
                                    }
                                }
                            });

                        }, 'json');
                    });
                    /* carrega municípios para o campo município de nascimento */
                    $('#uf_nasc_select').change(function () {
                        var uf = $('#uf_nasc_select').val();
                        $('#municipio_nasc').val('');
                        $('#cod_municipio_nasc').val('');
                        $.post('../busca_cep.php', {uf: uf, municipios: 1}, function (data) {

                            $("#municipio_nasc").autocomplete({source: data.municipios,
                                change: function (event, ui) {
                                    if (event.type == 'autocompletechange') {
                                        var valor_municipio = ui.item.value.split(')-');
                                        $('#cod_municipio_nasc').val(valor_municipio[0].trim().substring(1, 5));
                                        $('#municipio_nasc').val(valor_municipio[1].trim());
                                    }
                                }
                            });

                        }, 'json');
                    });

                    /* busca dados pelo cep */
                    var cep_atual = $('#cep').val().replace("-", "").replace(".", "");
                    var numero_atual = $('#numero').val();
                    var complemento_atual = $('#complemento').val();
		    //INICO VIA CEP
			//INICIO CEP

	 function limpa_formulario_cep() {
		// Limpa valores do formulÃ¡rio de cep.
		$("#endereco").val("");
		$("#bairro").val("");
		$("#cidade").val("");
		$("#uf").val("");
		//$("#ibge").val("");
	    }
			 //Quando o campo cep perde o foco.
	    $("#cep").blur(function() {
		//alert('dentro');return false;

		//Nova variÃ¡vel "cep" somente com dÃ­gitos.
		var cep = $(this).val().replace(/\D/g, '');

		//Verifica se campo cep possui valor informado.
		if (cep != "") {

				//ExpressÃ£o regular para validar o CEP.
		    var validacep = /^[0-9]{8}$/;

		    //Valida o formato do CEP.
		    if(validacep.test(cep)) {
			$('#cod_tp_logradouro').val("...");
                                    $('#endereco').val("...");
                                    $('#bairro').val("...");
                                    $('#uf').val("...");
                                    $('#cidade').val("...");
                                    $('#cod_cidade').val("...");
			//Preenche os campos com "..." enquanto consulta webservice.
		
			//Consulta o webservice viacep.com.br/

			$.getJSON("//viacep.com.br/ws/"+ cep +"/json/?callback=?", function(data) {
			    if (!("erro" in data)) {
				    $('#cod_tp_logradouro').val(data.cod_tp_logradouro);
                                    $('#endereco').val(data.logradouro);
                                    $('#bairro').val(data.bairro);
                                    $('#uf').val(data.uf);
                                    $('#cidade').val(data.localidade);
                                    $('#cod_cidade').val(data.id_municipio);
				//Atualiza os campos com os valores da consulta.	
			    } //end if.
			    else {
				//CEP pesquisado nÃ£o foi encontrado.
				    limpa_formulario_cep();
				alert("CEP não encontrado.");
				//bootAlert('CEP não encontrado!', 'Alerta', "Null", 'danger');
			    }
			});
		    } //end if.
		    else {
			//cep Ã© invÃ¡lido.
			limpa_formulario_cep();
			alert("Formato de CEP inválido!");
			//bootAlert('Formato de CEP inválido!', 'Alerta', "Null", 'danger');
		    }
		} //end if.
		else {
		    //cep sem valor, limpa formulÃ¡rio.
		    limpa_formulario_cep();
		}
	    });
	//FIM CEP 
	//FIM VIA CEP
			
		    /*
                    $('#cep').blur(function () {

                        $this = $(this);
                        $this.after('<img src="../img_menu_principal/loader_pequeno.gif" alt="buncando endereço..." style="position: absolute; margin-top: -7px;" id="img_load_cep" />');
                        $('#cod_tp_logradouro').attr('disabled', 'disabled');
                        $('#endereco').attr('disabled', 'disabled');
                        $('#bairro').attr('disabled', 'disabled');
                        $('#uf').attr('disabled', 'disabled');
                        $('#cidade').attr('disabled', 'disabled');

                        var cep = $this.val();
                        $.post('../busca_cep.php', {cep: cep, id_municipio: 1, municipios: 1}, function (data) {

                            $('#cod_tp_logradouro').removeAttr('disabled');
                            $('#endereco').removeAttr('disabled');
                            $('#bairro').removeAttr('disabled');
                            $('#uf').removeAttr('disabled');
                            $('#cidade').removeAttr('disabled');
                            $('#img_load_cep').remove();

                            if (data.cep == '') {
                                alert('Cep não encontrado!');
                            } else {
                                $("#cidade").autocomplete({source: data.municipios,
                                    change: function (event, ui) {
                                        if (event.type == 'autocompletechange') {
                                            var valor_municipio = ui.item.value.split(')-');
                                            $('#cod_cidade').val(valor_municipio[0].trim().substring(1, 5));
                                            $('#cidade').val(valor_municipio[1].trim());
                                        }
                                    }
                                });
                                $('#cod_tp_logradouro').val(data.cod_tp_logradouro);
                                $('#endereco').val(data.logradouro);
                                $('#bairro').val(data.bairro);
                                $('#uf').val(data.uf);
                                $('#cidade').val(data.cidade);
                                $('#cod_cidade').val(data.id_municipio);

                                if (data.cep == cep_atual) {
                                    $('#numero').val(numero_atual);
                                    $('#complemento').val(complemento_atual);
                                } else {
                                    $('#numero').val('');
                                    $('#complemento').val('');
                                }
                            }

                        }, 'json');

                    });*/
                    /*
                     *    FIM DAS ALTERAÇÕES FEITAS POR PV PARA  BUSCA CEP E E-SOCIAL
                     */

                    $('#horario').change(function () {
                        if ($('#horario').val() != '') {
                            $('#horas_semanais').val($('#horario option:selected').attr('name'));
                        }
                    });


                    $('.formata_valor').priceFormat({
                        prefix: '',
                        centsSeparator: ',',
                        thousandsSeparator: '.'
                    });



                    var tipoVerifica = 0;
                    $("select[name*='banco']").change(function () {
                        function tipoPgCheque() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function () {
                                if ($(this).text() == "Cheque") {
                                    $(this).attr('selected', true);
                                } else {
                                    $(this).attr('disabled', true);
                                }

                            });
                        }

                        function tipoPgConta() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function () {
                                if ($(this).text() == "Depósito em Conta Corrente") {
                                    $(this).attr('selected', true);
                                } else {
                                    $(this).attr('disabled', true);
                                }

                            });
                        }

                        var valor = $(this).val();
                        if (valor == 0) {
                            desabilita()
                            tipoPgCheque();
                            tipoVerifica = 1;

                        } else if (valor == 9999) {
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

                    function desabilita() {

                        $("input[name*='conta']").attr("disabled", true);
                        $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", true);
                        $("input[name*='agencia']").attr("disabled", true);
                        $("input[name='nomebanco']").attr("disabled", true);
                    }

                    function Ativa() {
                        $("input[name*='conta']").attr("disabled", false);
                        $("input[type*='radio'][name*='radio_tipo_conta']").attr("disabled", false);
                        $("input[name*='agencia']").attr("disabled", false);
                        $("input[name='nomebanco']").attr("disabled", false);
                    }

                    $("input[type*='button'][name*='Submit']").click(function () {
                        var indice = new Array();
                        if (tipoVerifica == 3) {
                            if ($("input[name*='conta']").val() == '') {
                                indice.push("Conta");
                            }
                            if ($("input[name*='agencia']").val() == '') {
                                indice.push("Agencia");
                            }
                            indiceRadio = 0;
                            $("input[name*='radio_tipo_conta']").each(function () {
                                if ($(this).is(':checked')) {
                                    indiceRadio = 1;
                                }
                            });

                            if (indiceRadio == 0) {
                                indice.push("tipo de conta");
                            }


                        } else if (tipoVerifica == 2) {
                            if ($("input[name*='conta']").val() == '') {
                                indice.push("Conta");
                            }
                            if ($("input[name*='agencia']").val() == '') {
                                indice.push("Agencia");
                            }
                            indiceRadio = 0;
                            $("input[name*='radio_tipo_conta']").each(function () {
                                if ($(this).is(':checked')) {
                                    indiceRadio = 1;
                                }
                            });

                            if (indiceRadio == 0) {
                                indice.push("tipo de conta");
                            }

                            if ($("input[name*='nomebanco']").val() == "") {
                                indice.push("Nome do banco");
                            }
                        }

                        if (indice.length > 0) {
                            alert("Preencha o(s) dado(s) " + indice.join(', '));
                        } else {

                            $('#form1').submit();
                        }
                    });

                    // instancia o validation engine no formulário
                    $("#form1").validationEngine();

                    // add class do validation engine
                    $("#pis").change(function () {
                        // verifica se o campo não está vazio 
                        if ($("#pis").val() != '') {
                            $("#pis").addClass('validate[required,funcCall[verificaPIS]]');    // adiciona classe
                        }
                        else {
                            $("#pis").removeClass('validate[required,funcCall[verificaPIS]]'); // remove classe
                        }
                    });

                    //Plano de saude
                    $("input[type='radio'][name='medica']").click(function () {
                        var valor = $(this).val();
                        if (valor == 1) { //Adiciona a classe validade
                            $("#planosaude").css('display', '');
                            $("#id_plano_saude").attr('class', "validate[required]");
                        } else {
                            $("#planosaude").css('display', 'none');
                            $("#id_plano_saude").removeAttr("class").val(''); // remove a classe
                        }
                    });

                    //Amanda
                    //Possui sindicato?
                    $("input[type='radio'][name='radio_sindicato']").click(function () {
                        var valor = $(this).val();
                        if (valor === 'sim') { //Adiciona a classe validade
                            $("#trsindicato").css('display', '');
                            $("#sindicato").attr('class', "validate[required]");
                        } else {
                            $("#trsindicato").css('display', 'none');
                            $("#sindicato").removeAttr("class").val(''); // remove a classe
                        }
                    });

                    //Isento de Contribuição?
                    $("input[type='radio'][name='radio_contribuicao']").click(function () {
                        var valor = $(this).val();
                        if (valor === 'sim') {//Adiciona a classe validade
                            $("#trcontribuicao").css('display', '');
                            $("#ano_contribuicao").attr('class', "validate[required]");
                        } else {
                            $("#trcontribuicao").css('display', 'none');
                            $("#ano_contribuicao").removeAttr("class").val('');// remove a classe

                        }
                    });
                    //FIM


                    $('#ano-chegada').hide();

                    $('#nacionalidade').change(function () {
                        var valor = $(this).val();
                        if (valor == 10) {
                            $('#ano-chegada').hide();
                            $("#pais_nacionalidade").val('Brasil');
                            $("#cod_pais_nacionalidade").val('1');
                            $("#pais_nasc").val('Brasil');
                            $("#cod_pais_nasc").val('1');
                            //                                $('.pais').removeAttr('value');
                            //                                $( "input[name^='cod_pais_']").removeAttr('value');
                            $("#ano_chegada_pais").removeAttr('value');
                        } else {
                            $('.pais').removeAttr('value');
                            $("input[name^='cod_pais_']").removeAttr('value');
                            $('#ano-chegada').show();
                            $(".pais").focus(function () {
                                var tipo = "#" + $(this).data('tipo');
                                $.post('../methods.php', {method: 'carregaPais'}, function (data) {
                                    $(tipo).autocomplete({source: data.pais});
                                }, 'json');
                            });
                            $(".pais").focusout(function () {
                                var pais = $(this).val();
                                var tipo = "#cod_" + $(this).data('tipo');
                                if (pais !== '') {
                                    $.post('../methods.php', {method: 'carregaCodPais', pais: pais}, function (data) {
                                        $(tipo).val(data.id_pais);
                                    }, 'json');
                                }
                            });
                        }
                    });

                    /*******************PENSÃO ALIMENTICIA*******************/
                    var valor = "";
                    $("input:radio[name='pensao_alimenticia']").each(function () {
                        if ($(this).is(':checked')) {
                            valor = parseFloat($(this).val());
                        }
                    });

                    if (valor == 0) {
                        $("select[name='pensao_percentual']").hide();
                    } else {
                        $("select[name='pensao_percentual']").show();
                    }

                    $("input:radio[name='pensao_alimenticia']").click(function () {
                        if ($(this).val() == 0) {
                            $("select[name='pensao_percentual']").hide();
                        } else {
                            $("select[name='pensao_percentual']").show();
                        }
                    });
                    /********************************************************/


                    $("#nacionalidade").trigger("change");


                    $(".verificaCpfFunDemitidos").change(function () {
                        var cpf = $(this).val();
                        $.ajax({
                            url: "",
                            type: "POST",
                            dataType: "json",
                            data: {
                                method: "verificaCpf",
                                cpf: cpf
                            },
                            success: function (data) {
                                if (data.status) {
                                    $("#participanteDesativado").css({display: "block"});
                                    $(data.dados).each(function (d, i) {
                                        $("#part").html(i.nome + " | <b>PROJETO:</b> " + i.projeto + " | <b>MOTIVO:</b> " + i.status + "<br><a href='http://f71lagos.com/intranet/registrodeempregado.php?bol=0&pro=" + i.idprojeto + "&clt=" + i.id + "'>Visualizar Ficha</a>");
                                    });

                                    $("input[name='Submit']").css({display: "none"});
                                    $('html, body').animate({
                                        scrollTop: $("#part").offset().top
                                    }, 2000).trigger('click');
                                }
                            }
                        });
                    });

                    $("body").on("click", "input[name='cadSim']", function () {
                        $("input[name='Submit']").css({display: "block"});

                    });
                    
                    /***
                     * FEITO POR SINESIO - 24/03/2016 - 656
                     */
                    $("body").on("change","input[name='data_entrada']",function(){
                        
                        /**
                         * RECUPERANDO DATA DE ENTRADA
                         */
                        var dataEntrada = $(this).val(); /**24/03/2016**/
                        
                        /**
                         * EXPLODE DE DATA DE ENTRADA
                         */
                        var explode = dataEntrada.split("/");
                        
                        /**
                         * PREENCHENDO VARIAVEIS
                         */
                        var dia = parseInt(explode[0]);
                        //var mes = parseInt(explode[1]);
                        var mes = parseInt(explode[1]) - 1;
                        var ano = parseInt(explode[2]);
                        
                        /**
                         * OBJETO
                         */
                        var data = new Date(ano,mes,dia);
                            data.setDate(data.getDate() + 90);
                            
                        var novaData = str_pad(data.getDate(),2,'0','STR_PAD_LEFT') + "/" + str_pad(data.getMonth()+1,2,'0','STR_PAD_LEFT') + "/" + data.getFullYear();
                        
                        $("#dataFinalExperiencia").html("Termino da Experiência: " + novaData);
                        
                    });
                    
                     
                    /**
                     * 
                     */
                    $("#id_setor").change(function(){
                        
                        var idSetor = $(this).val();
                        
                        $.ajax({
                           url:"",
                           type:"POST",
                           dataType:"json",
                           data:{
                               idSetor:idSetor,
                               method:"carregaFuncaoPorSetor"
                           },
                           success: function(data){
                                var html = "";
                                
                                html += "<option value='-1'>--Selecione--</option>";
                                
                                $.each(data, function(key, funcao){                                    
                                    html += "<option value='" + key + "'>" + key + " - " + funcao.nome + " ( Valor: " + funcao.salario + " ) " + "</option>";
                                });
                                
                                $("#idcursos").html(html);
                           }
                        });
                    });
                    
                    $("#idcursos").change(function(){
                        
                        var idCurso = $(this).val();
                        
                        $.ajax({
                           url:"",
                           type:"POST",
                           dataType:"json",
                           data:{
                               idCurso:idCurso,
                               method:"carregaHorarioPorFuncao"
                           },
                           success: function(data){                               
                                var html = "";
                                
                                html += "<option value='-1'>--Selecione--</option>";
                                
                                if(data.tot > 0){
                                    $.each(data, function(key, horario){
                                        if(horario.nome != undefined){
                                            html += "<option value='" + key + "'>" + key + " - " + horario.nome + " ( " + horario.entrada_1 + " - " + horario.saida_1 + " - " + horario.entrada_2 + " - " + horario.saida_2 + " ) " + "</option>";
                                        }
                                    });
                                    
                                    $("#resp_horario").hide();
                                    $("#idhorarios").show();
                                }else{
                                    $("#resp_horario").show();
                                    $("#idhorarios").hide();
                                }
                                
                                $("#idhorarios").html(html);
                           }
                        });
                    });


                });
                
                
            </script>
            <style>
                #participanteDesativado{
                    display: none;
                    margin: 20px 0px 20px 0px;
                    background-color: #FFCCD6;
                    border: 1px solid #F00;
                    padding: 4px;
                    font-size: 14px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div id="corpo">
                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">

                    <tr>
                        <td>	<span style="float:right"><?php include('../reportar_erro.php'); ?></span>
                            <span style="clear:right"></span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                <h2 style="float:left; font-size:18px;">
                                    CADASTRAR <span class="clt">CLT</span>
                                </h2>
                                <p style="float:right;">
    <?php if ($_GET['pagina'] == 'clt') { ?>
                                        <a href="clt.php?regiao=<?= $id_regiao ?>">&laquo; Voltar</a>
                                    <?php } else { ?>
                                        <a href="../ver.php?regiao=<?= $id_regiao ?>&projeto=<?= $projeto ?>">&laquo; Voltar</a>
                                    <?php } ?>
                                </p>
                                <div class="clear"></div>
                            </div>
                            <p>&nbsp;</p>
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form1" id="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
                                <table cellpadding="0" cellspacing="1" class="secao">


                                    <tr>
                                        <td colspan="3" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Matrícula no Projeto</td>
                                        <td colspan="2"><?= $codigo ?> <input name="codigo" size="3" type="hidden" id="codigo" value="<?= $codigo ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td width="25%" class="secao">Projeto:</td>
                                        <td width="75%" colspan="2">   
    <?php
    if (!empty($projeto)) {
        $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
        echo $projeto . ' - ' . mysql_result($qr_projeto, 0);
    } else {
        ?>

                                                <select name="projetos" id="projetos" onChange="location.href = this.value;">
                                                    <option selected disabled>--Selecione--</option>
        <?php
        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao' AND status_reg = '1'");
        while ($row_projeto = mysql_fetch_array($qr_projeto)) {
            ?>
                                                        <option value="<?= $_SERVER['PHP_SELF'] ?>?regiao=<?= $id_regiao ?>&projeto=<?= $row_projeto['id_projeto'] ?>"><?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] ?></option>

        <?php } ?>
                                                </select>

    <?php } ?>
                                        </td>
                                    </tr>
                                    <tr style="display:none;">
                                        <td class="secao">Vínculo:</td>
                                        <td colspan="2">
                                            <select name="vinculo" id="vinculo">
    <?php
    $result_vinculo = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$id_regiao'");
    while ($row_vinculo = mysql_fetch_array($result_vinculo)) {
        print "<option value='$row_vinculo[0]'>$row_vinculo[id_empresa] - $row_vinculo[razao]</option>";
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr style="display:none;">
                                        <td class="secao">Tipo Contrata&ccedil;&atilde;o:</td>
                                        <td colspan="2"><label><input name="contratacao" type="radio" class="reset" id="contratacao" value="2" checked="checked"> CLT</label></td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Setor</td>
                                        <td colspan="2"><?= montaSelect($arraySetor, $id_departamento, 'name="id_setor" id="id_setor" class="validate[required]"') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Função:</td>
                                        <td colspan="2">
    <?php
    $qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = '$id_regiao' AND campo3 = '$projeto' AND tipo = '2' AND status = '1' AND status_reg = '1' ORDER BY nome ASC");
    $verifica_curso = mysql_num_rows($qr_curso);

//                                            echo $verifica_curso;
    if (!empty($verifica_curso)) {

        if (!empty($idcurso)) {
            $var_disabled = 'display:none;';
        }
        $par_id_clt = (isset($id_clt)) ? "&id=$id_clt" : "";
        print "<select name='idcursos' id='idcursos'  class='validate[required]'>
       				    <option style='margin-bottom:3px;$var_disabled' value='' selected disabled>--Selecione um setor--</option>";

//        while ($row_curso = mysql_fetch_array($qr_curso)) {
//
//            $margem++;
//
//            if ($margem != $verifica_curso) {
//                $var_margem = ' style="margin-bottom:3px;"';
//            } else {
//                $var_margem = NULL;
//            }
//
//            $salario = number_format($row_curso['salario'], 2, ',', '.');

//            if ($row_curso['0'] == $idcurso) {
                
//                print "<option value='-1'> « Selecione um Setor » </option>";
//            } else {
//                 
//                print "<option value='{$_SERVER['PHP_SELF']}?regiao={$id_regiao}&projeto={$projeto}&cbo={$row_curso['cbo_codigo']}&idcursos={$row_curso[0]}{$par_id_clt}'$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
//            }
//        }

        print '</select>';
    } else {

        if (empty($projeto)) {
            print 'Selecione um Projeto';
        } else {
            print 'Nenhum Curso Cadastrado para o Projeto';
        }
    }
    ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td colspan="2"><input type="checkbox" name="contrato_medico" value="1"/> Necessita de contrato para médicos?</td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Horário:</td>
                                        <td colspan="2">
    <?php    
    print '<select name="horario" id="idhorarios" class="validate[required]">
				   <option style="margin-bottom:3px;" value="" selected disabled>--Selecione uma função--</option>';
    print '</select>';
    
//    $qr_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$idcurso' AND id_regiao = '$id_regiao'");
//    $verifica_horario = mysql_num_rows($qr_horarios);
//    if (!empty($verifica_horario)) {
//        $idHorario = '';
//        print '<select name="horario" id="horario" class="validate[required]">
//				   <option style="margin-bottom:3px;" value="" selected disabled>--Selecione--</option>';
//
//        while ($row_horarios = mysql_fetch_array($qr_horarios)) {
//
//            $margem2++;
//
//            if ($margem2 != $verifica_horario) {
//                $var_margem2 = ' style="margin-bottom:3px;"';
//            } else {
//                $var_margem2 = NULL;
//            }
//
//            print "<option name = '$row_horarios[horas_semanais]' value='$row_horarios[0]'$var_margem2>$row_horarios[0] - $row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
//            $idHorario = $row_horarios['id_horario'];
//        }
//
//        print '</select>';
//    } else {
//
//        if (empty($projeto)) {
//            print 'Selecione um Projeto';
//        } elseif (empty($idcurso) and ! empty($verifica_curso)) {
//            print 'Selecione um Curso';
//        } else {//Amanda
//            echo '<a href="../adm/adm_curso/index.php" target="_blank"><label style=" cursor: default; cursor: pointer; ">Clique aqui para cadastrar um horário</label></a>';
//            //FIM
//        }
//    }
    ?>
                                            <div id="resp_horario" style="display:none;">Nenhum horário cadastrado para essa função</div>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Horas Semanais:</td>
                                        <td colspan="2">
                                            <input type="text" id="horas_semanais" name ="horas_semanais" value="" disabled="disabled" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                                            <!--Amanda-->
                                            <a href="../adm/adm_curso/index.php" target="_blank"><label style=" cursor: default; cursor: pointer; ">EDITAR</label></a>   
                                            <!--FIM-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Unidade:</td>
                                        <td>
                                            <select name="locacao" id="locacao" class="validate[required]">
                                                <?php
                                                $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
                                                $verifica_unidade = mysql_num_rows($qr_unidade);
                                                if (!empty($verifica_unidade)) {
                                                    print '<option style="margin-bottom:3px;" value="" selected disabled>--Selecione--</option>';

                                                    while ($row_unidade = mysql_fetch_array($qr_unidade)) {

                                                        $margem3++;

                                                        if ($margem3 != $verifica_unidade) {
                                                            $var_margem3 = ' style="margin-bottom:3px;"';
                                                        } else {
                                                            $var_margem3 = NULL;
                                                        }
                                                        print "<option value='" . $row_unidade[unidade] . " // " . $row_unidade[id_unidade] . "'$var_margem3>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
                                                    }

                                                    print '';
                                                } else {

                                                    if (empty($projeto)) {
                                                        print '<option disabled>Selecione um Projeto</option>';
                                                    } else {
                                                        print '<option disabled>Nenhuma unidade Cadastrada para o Projeto</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </td>   
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td class="secao_pai" colspan="6">DADOS PESSOAIS</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td colspan="5">
                                            <input name="nome" type="text" id="nome" size="75" onChange="this.value = this.value.toUpperCase();" onKeyPress="return(verificanome(this, event));" class="validate[required]" value="<?= $clt['nome'] ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome Social:</td>
                                        <td colspan="5">
                                            <input name="nomesocial" type="text" id="nomesocial" size="75" onChange="this.value = this.value.toUpperCase();" onKeyPress="return(verificanome(this, event));" value="<?= $clt['nomesocial'] ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Data de Nascimento:</td>
                                        <td>
                                            <input name="data_nasc" type="text" id="data_nasc" size="15" maxlength="10" class="validate[required]"  value="<?= $clt['data_nasci'] ?>"
                                                   onkeyup="mascara_data(this);"/>
                                        </td>
                                        <td class="secao">UF de Nascimento:</td>
                                        <td>
                                            <select name="uf_nasc_select" id="uf_nasc_select" data-tipo="municipio_nasc" class="uf_select">
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        if (isset($clt['uf_nasc']) && $clt['uf_nasc'] == $row_uf['uf_sigla']) {
            echo '<option value="' . $row_uf['uf_sigla'] . '" selected>' . $row_uf['uf_sigla'] . '</option>';
        } else {
            echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
        }
    }
    ?>    
                                            </select>
                                            <input name="uf_nasc_text" type="text" id="uf_nasc_text" size="16"  onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Município de Nascimento:</td>
                                        <td>
                                            <input name="municipio_nasc" type="text" id="municipio_nasc" size="15" class="municipio"  value="<?= $clt['municipio_nasc'] ?>" style="width:130px;"/>
                                            <input type="text" readonly="readonly" name="cod_municipio_nasc" id="cod_municipio_nasc" size="4"  style="width:40px;"/>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="secao" width="16%">Estado Civil:</td>
                                        <td width="16%">
                                            <select name="civil" id="civil">
    <?php
    $qr_estCivil = mysql_query("SELECT * FROM estado_civil");
    while ($row_estCivil = mysql_fetch_assoc($qr_estCivil)) {
        $selected = ($clt['civil'] == $row_estCivil['nome_estado_civil']) ? "selected" : "";
        echo '<option value="' . $row_estCivil['id_estado_civil'] . '|' . $row_estCivil['nome_estado_civil'] . '" ' . $selected . '>' . $row_estCivil['nome_estado_civil'] . '</option>';
    }
    ?>   
                                            </select>
                                        </td>
                                        <td class="secao" width="16%">Sexo:</td>
                                        <td width="16%">
                                            <label><input name="sexo" type="radio" class="reset" value="M" <?= (isset($clt['sexo']) && $clt['sexo'] == 'M') ? "checked" : "" ?> /> Masculino</label><br>    		
                                            <label><input name="sexo" type="radio" class="reset" value="F" <?= (isset($clt['sexo']) && $clt['sexo'] == 'F') ? "checked" : "" ?> /> Feminino</label>
                                        </td>
                                        <td class="secao" width="16%">Nacionalidade:</td>
                                        <td width="16%">
                                        <!--<input name="nacionalidade" type="text" id="nacionalidade" size="15" 
                                                   onchange="this.value=this.value.toUpperCase()"/>-->
                                            <select name="nacionalidade" id="nacionalidade">
    <?php
    while ($row_nacionalidade = mysql_fetch_assoc($qr_nacionalidade)) {
        $selected = ($clt['nacionalidade'] == $row_nacionalidade['codigo']) ? "selected" : "";
        echo '<option value="' . $row_nacionalidade['codigo'] . '">' . $row_nacionalidade['nome'] . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="ano-chegada">
                                        <td class="secao">Data de chegada ao país:</td>
                                        <td>
                                            <input name="ano_chegada_pais" id="ano_chegada_pais" type="text" size="15" maxlength="10" class="validate[required]"  value="<?= $clt['ano_chegada_pais'] ?>"
                                                   onkeyup="mascara_data(this);"/>
                                        </td>
                                        <td class="secao">Pais de Nascimento</td>
                                        <td>
                                            <input name="pais_nasc" type="text" id="pais_nasc" data-tipo = "pais_nasc" size="15" class="pais"  />
                                            <input type="text" readonly="readonly" name="cod_pais_nasc" id="cod_pais_nasc" size="4"/>
                                        </td>
                                        <td class="secao">País de Nacionalidade</td>
                                        <td>
                                            <input name="pais_nacionalidade" type="text" id="pais_nacionalidade" data-tipo = "pais_nacionalidade" size="15" class="pais"  />
                                            <input type="text" readonly="readonly" name="cod_pais_nacionalidade" id="cod_pais_nacionalidade" size="4"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">CEP:</td>
                                        <td colspan="5"><input name="cep" type="text" id="cep" maxlength="9" class="validate[required]"  value="<?= $clt['cep'] ?>"> <span></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Endereço:</td>
                                        <td>
                                            <input name="cod_tp_logradouro" id="cod_tp_logradouro" type="hidden" value="<?= $clt['tipo_endereco'] ?>"  />
                                            <input name="endereco" type="text" id="endereco" size="32" class="validate[required]"  value="<?= $clt['endereco'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">N&uacute;mero:</td>
                                        <td>
                                            <input name="numero" type="text" id="numero" size="10"  value="<?= $clt['numero'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Complemento:</td>
                                        <td>
                                            <input name="complemento" type="text" id="complemento" size="15"  value="<?= $clt['complemento'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Bairro:</td>
                                        <td>
                                            <input name="bairro" type="text" id="bairro" size="16" class="validate[required]"  value="<?= $clt['bairro'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>

                                        <td class="secao">UF:</td>
                                        <td>   <select name="uf" id="uf" class="validate[required] uf_select" data-tipo="cidade">
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        $selected = ($clt['uf'] == $row_uf['uf_sigla']) ? "selected" : "";
        echo '<option value="' . $row_uf['uf_sigla'] . '" ' . $selected . '>' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select>
                                        </td>
                                        <td class="secao">Cidade:</td>
                                        <td>
                                            <input name="cidade" type="text" id="cidade" size="16"  onchange="this.value = this.value.toUpperCase()" class="validate[required] municipio"  value="<?= $clt['cidade'] ?>"  style="width:130px;"/>
                                            <input type="text" readonly="readonly" name="cod_cidade" id="cod_cidade" size="4"  style="width:40px;"/>
                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="secao">Estuda Atualmente?</td>
                                        <td>
                                            <label><input name="estuda" type="radio" class="reset" value="sim" <?= (isset($clt['estuda']) && $clt['estuda'] == "sim") ? 'checked="checked"' : "" ?> /> SIM</label>
                                            <label><input name="estuda" type="radio" class="reset" value="não" <?= (isset($clt['estuda']) && $clt['estuda'] == "nao") ? 'checked="checked"' : "" ?> /> NÃO</label>
                                        </td>
                                        <td class="secao">Término em:</td>
                                        <td colspan="3">
                                            <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10" value="<?= $clt['data_escola'] ?>"
                                                   onkeyup="mascara_data(this);" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Escolaridade:</td>
                                        <td>
                                            <select name="escolaridade" >
    <?php
    $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
    while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
        $selected = ($clt['escolaridade'] == $escolaridade['cod']) ? "selected" : "";
        echo '<option value="' . $escolaridade['id'] . '" ' . $selected . '>' . $escolaridade['cod'] . ' - ' . $escolaridade['nome'] . '</option>';
    }
    ?> 
                                            </select>
                                        </td>
                                        <td class="secao">Curso:</td>
                                        <td>
                                            <input name="curso" type="text" id="zona" size="16" value="<?= $clt['curso'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Instituição:</td>
                                        <td>
                                            <input name="instituicao" type="text" id="titulo" size="15" value="<?= $clt['instituicao'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    
                                    
                                    <tr>
                                        <td class="secao">Curso (Aprendiz)</td>
                                        <!-- retirado por não conseguir fazer cadastro sem preencher o campo Curso (Aprendiz) class="validate[required]"-->
                                        <td colspan="5"><input name="curso_aprendiz" type="text" id="curso_aprendiz"  value="<?= $clt['curso_aprendiz'] ?>"> <span></span>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td class="secao">Telefone Fixo:</td>
                                        <td><input name="tel_fixo" type="text" id="tel_fixo" class="tel" value="<?= $clt['tel'] ?>"/>
                                        </td>
                                        <td class="secao">Celular:</td>
                                        <td><input name="tel_cel" class="tel" type="text" id="tel_cel" value="<?= $clt['tel_cel'] ?>" />
                                        </td>
                                        <td class="secao">Recado:</td>
                                        <td><input name="tel_rec" type="text" class="tel" id="tel_rec" value="<?= $clt['tel_rec'] ?>" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">E-mail:</td>
                                        <td colspan="5">
                                            <input name="email" type="text" id="email" size="35" value='<?= $clt['email'] ?>' />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Tipo Sanguíneo</td>
                                        <td colspan="5">
                                            <select name="tiposanguineo">
                                                <option value="">Selecione</option>
    <?php
    $query = "select * from tipo_sanguineo";
    $rsquery = mysql_query($query);
    while ($i = mysql_fetch_assoc($rsquery)) {
        $selected = ($clt['tiposanguineo'] == $i['nome']) ? "selected" : "";
        ?>
                                                    <option value="<?php echo $i["nome"] ?>" <?= $selected ?> ><?php echo $i["nome"] ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td class="secao_pai" colspan="4">DADOS DA FAMÍLIA</td>
                                    </tr>

                                    <tr> 
                                        <td class="secao">Filiação - Pai:</td>
                                        <td colspan="3">
                                            <input name="pai" type="text" id="pai" size="45" value="<?= $row['pai'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_pai" id="ddir_pai" value="1" <?php echo $checked_pai; ?>/> Dependente de IRRF
                                        </td>
                                    </tr>

                                    <tr> 
                                        <td class="secao"> Nacionalidade Pai:</td>
                                        <td>
                                            <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" value="<?= $row['nacionalidade_pai'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"/>	
                                        </td>

                                        <td class="secao">Data de nascimento do Pai:</td>
                                        <td><input type="text" name="data_nasc_pai" id="data_nasc_pai" value="<?php echo $row['data_nasc_pai']; ?>" onkeyup="mascara_data(this);" /></td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Filiação - Mãe:</td>
                                        <td colspan="3">
                                            <input name="mae" type="text" id="mae" size="45" value="<?= $row['mae'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_mae" id="ddir_mae" value="1" <?php echo $checked_mae; ?> /> Dependente de IRRF
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">
                                            Nacionalidade Mãe:
                                        </td>
                                        <td>
                                            <input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" value="<?= $row['nacionalidade_mae'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"/>	
                                        </td>

                                        <td class="secao">Data de nascimento da Mãe:</td>
                                        <td><input type="text" name="data_nasc_mae" id="data_nasc_mae" value="<?php echo $row['data_nasc_mae']; ?>" onkeyup="mascara_data(this);" /> </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Conjuge:</td>
                                        <td colspan="3">
                                            <input name="conjuge" type="text" id="conjuge" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_conjuge'] ?>"/>
                                            <input type="checkbox" name="ddir_conjuge" id="ddir_conjuge" value="1" <?php echo $checked_conjuge; ?>/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Data de nascimento do Conjuge:</td>
                                        <td colspan="3">
                                            <input name="data_nasc_conjuge" type="text" id="data_nasc_conjuge" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['data_nasc_conjuge']; ?>" onkeyup="mascara_data(this);" />	
                                        </td>
                                    </tr> 
    <?php
    if ($_COOKIE['logado'] == 87) {
        ?>
                                        <tr>
                                            <td class="secao">Avô:</td>
                                            <td colspan="4">
                                                <input name="avo_h" type="text" id="avo_h" size="45" 
                                                       onchange="this.value = this.value.toUpperCase()"  value="<?php echo $row['nome_avo_h']; ?>"/>
                                                <input type="checkbox" name="ddir_avo_h" id="ddir_avo_h" value="1" <?php echo $checked_avo_h; ?> /> Dependente de IRRF
                                            </td>
                                        </tr>
                                        <tr>            
                                            <td class="secao">Data de nascimento do Avô:</td>
                                            <td colspan="3"><input type="text" name="data_nasc_avo_h" id="data_nasc_avo_h" value="<?php echo $row['data_nasc_avo_h']; ?>" onkeyup="mascara_data(this);" /> </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Avó:</td>
                                            <td colspan="4">
                                                <input name="avo_m" type="text" id="avo_m" size="45" 
                                                       onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_avo_m']; ?>"/>
                                                <input type="checkbox" name="ddir_avo_m" id="ddir_avo_m" value="1" <?php echo $checked_avo_m; ?>/> Dependente de IRRF
                                            </td>
                                        </tr>
                                        <tr>             
                                            <td class="secao">Data de nascimento da Avó:</td>
                                            <td colspan="3"><input type="text" name="data_nasc_avo_m" id="data_nasc_avo_m" value="<?php echo $row['data_nasc_avo_m']; ?>" onkeyup="mascara_data(this);"/> </td>
                                        </tr>


                                        <tr>
                                            <td class="secao">Bisavô:</td>
                                            <td colspan="4">
                                                <input name="bisavo_h" type="text" id="bisavo_h" size="45" 
                                                       onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_bisavo_h']; ?>"/>
                                                <input type="checkbox" name="ddir_bisavo_h" id="ddir_bisavo_h" value="1" <?php echo $checked_bisavo_h; ?>/> Dependente de IRRF
                                            </td>
                                        </tr>
                                        <tr>             
                                            <td class="secao">Data de nascimento do Bisavô:</td>
                                            <td colspan="3"><input type="text" name="data_nasc_bisavo_h" id="data_nasc_bisavo_h" value="<?php echo $row['data_nasc_bisavo_h']; ?>" onkeyup="mascara_data(this);" /> </td>
                                        </tr>

                                        <tr>
                                            <td class="secao">Bisavó:</td>
                                            <td colspan="4">
                                                <input name="bisavo_m" type="text" id="bisavo_m" size="45" 
                                                       onchange="this.value = this.value.toUpperCase()" value="<?php echo $row['nome_bisavo_m']; ?>"/>
                                                <input type="checkbox" name="ddir_bisavo_m" id="ddir_bisavo_m" value="1" <?php echo $checked_bisavo_m; ?>/> Dependente de IRRF
                                            </td>
                                        </tr>
                                        <tr>           
                                            <td class="secao">Data de nascimento da Bisavó:</td>
                                            <td colspan="3"><input type="text" name="data_nasc_bisavo_m" id="data_nasc_bisavo_m" value="<?php echo $row['data_nasc_bisavo_m']; ?>" onkeyup="mascara_data(this);"/> </td>
                                        </tr>

        <?php
    }
    ?>
                                    <tr>
                                        <td class="secao">Número de Filhos:</td>
                                        <td colspan="3">
                                            <input name="filhos" type="text" id="filhos" size="2" value="<?= $row['num_filhos'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_1" type="text" id="filho_1" size="50" value="<?= $row_depe['nome1'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1" value="<?= ($row_depe['datas1'] != '00/00/0000') ? $row_depe['datas1'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);
                                                               pula(10, this.id, filho_2.id)"
                                                   onChange="this.value = this.value.toUpperCase()"  class="data_filho"/>
                                            <br/>
                                            <input name="portador1" id="portador1" value="1"  type="checkbox" <?php echo $checked_portador1; ?>/> Portador de deficiência</br>
                                            <input name="dep1_cur_fac_ou_tec" id="dep1_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep1_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_2" type="text" id="filho_2" size="50" value="<?= $row_depe['nome2'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2" value="<?= ($row_depe['datas2'] != '00/00/0000') ? $row_depe['datas2'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);
                                                               pula(10, this.id, filho_3.id)"        
                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br/>
                                            <input name="portador2" id="portador2" value="1"  type="checkbox" <?php echo $checked_portador2; ?>/> Portador de deficiência</br>
                                            <input name="dep2_cur_fac_ou_tec" id="dep2_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep2_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_3" type="text" id="filho_3" size="50" value="<?= $row_depe['nome3'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3" value="<?= ($row_depe['datas3'] != '00/00/0000') ? $row_depe['datas3'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);
                                                               pula(10, this.id, filho_4.id)"
                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br/>
                                            <input name="portador3" id="portador3" value="1"  type="checkbox" <?php echo $checked_portador3; ?>/> Portador de deficiência</br>
                                            <input name="dep3_cur_fac_ou_tec" id="dep3_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep3_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_4" type="text" id="filho_4" size="50" value="<?= $row_depe['nome4'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4" value="<?= ($row_depe['datas4'] != '00/00/0000') ? $row_depe['datas4'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);
                                                               pula(10, this.id, filho_5.id)"
                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br/>
                                            <input name="portador4" id="portador4" value="1"  type="checkbox" <?php echo $checked_portador4; ?>/> Portador de deficiência</br>
                                            <input name="dep4_cur_fac_ou_tec" id="dep4_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep4_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_5" type="text" id="filho_5" size="50" value="<?= $row_depe['nome5'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5" value="<?= ($row_depe['datas5'] != '00/00/0000') ? $row_depe['datas5'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);"
                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br/>
                                            <input name="portador5" id="portador5" value="1"  type="checkbox" <?php echo $checked_portador5; ?>/> Portador de deficiência</br>
                                            <input name="dep5_cur_fac_ou_tec" id="dep5_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep5_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td>
                                            <input name="filho_6" type="text" id="filho_6" size="50" value="<?= $row_depe['nome6'] ?>"
                                                   onChange="this.value = this.value.toUpperCase()"  class="nome_filho"/>

                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td>
                                            <input name="data_filho_6" type="text" size="12" maxlength="10" id="data_filho_6" value="<?= ($row_depe['datas6'] != '00/00/0000') ? $row_depe['datas6'] : ''; ?>"
                                                   onKeyUp="mascara_data(this);"
                                                   onChange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br/>
                                            <input name="portador6" id="portador6" value="1"  type="checkbox" <?php echo $checked_portador6; ?>/> Portador de deficiência</br>
                                            <input name="dep6_cur_fac_ou_tec" id="dep6_cur_fac_ou_tec" value="1"  type="checkbox" <?php echo $checked_dep6_cur_fac_ou_tec; ?>/> Cursando escola técnica ou faculdade
                                        </td>
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="6" class="secao_pai">APARÊNCIA</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Cabelos:</td>
                                        <td>
                                            <select name="cabelos" id="cabelos">
                                                <option>Não informado</option>
    <?php
    $qr_cabelos = mysql_query("SELECT * FROM tipos WHERE tipo = '1' AND status = '1'");
    while ($row_cabelos = mysql_fetch_array($qr_cabelos)) {
        $selected = ($clt['cabelos'] == $row_cabelos['nome']) ? "selected" : "";
        print "<option $selected>$row_cabelos[nome]</option>";
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Olhos:</td>
                                        <td>
                                            <select name="olhos" id="olhos">
                                                <option>Não informado</option>
    <?php
    $qr_olhos = mysql_query("SELECT * FROM tipos WHERE tipo = '2' AND status = '1'");
    while ($row_olhos = mysql_fetch_array($qr_olhos)) {
        $selected = ($clt['olhos'] == $row_cabelos['nome']) ? "selected" : "";
        print "<option $selected>$row_olhos[nome]</option>";
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Peso:</td>
                                        <td>
                                            <input name="peso" type="text" id="peso" size="5" value="<?= $row_depe['peso'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Altura:</td>
                                        <td>
                                            <input name="altura" type="text" id="altura" size="5" value="<?= $row_depe['altura'] ?>" />
                                        </td>
                                        <td class="secao">Etnia:</td>
                                        <td>
                                            <select name="etnia">
    <?php
    $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' ORDER BY id DESC");
    while ($etnia = mysql_fetch_assoc($qr_etnias)) {
        $selected = ($clt['etinia'] == $etnia['id']) ? "selected" : "";
        echo '<option value="' . $etnia['id'] . '">' . $etnia['nome'] . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Marcas ou Cicatriz:</td>
                                        <td>
                                            <input name="defeito" type="text" id="defeito" size="18" value="<?= $row_depe['defeito'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Deficiência:</td>
                                        <td colspan="5">
                                            <select name="deficiencia">
                                                <option value="">Não é portador de deficiência</option>
    <?php
    $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
    while ($deficiencia = mysql_fetch_assoc($qr_deficiencias)) {
        $selected = ($clt['deficiencia'] == $deficiencia['id']) ? "selected" : "";
        echo '<option value="' . $deficiencia['id'] . '">' . $deficiencia['nome'] . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Enviar Foto:</td>
                                        <td colspan="5">
                                            <input name="foto" type="checkbox" class="reset" id="foto" onClick="document.getElementById('arquivo').style.display = (document.getElementById('arquivo').style.display == 'none') ? '' : 'none';" value='1'/>
                                            <input name="arquivo" type="file" id="arquivo" size="60" style="display:none"/>
                                        </td>
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="8" class="secao_pai">DOCUMENTAÇÃO</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nº do RG:</td>
                                        <td>
                                            <input name="rg" type="text" id="rg" size="13" maxlength="14" class="validate[required]" value="<?= $clt['rg'] ?>"
                                                   onkeyup="pula(14, this.id, orgao.id)">
                                        </td>
                                        <td class="secao">Orgão Expedidor:</td>
                                        <td>
                                            <input name="orgao" type="text" id="orgao" size="8" value="<?= $clt['orgao'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">UF:</td>
                                        <td>
                                            <select name="uf_rg" id="uf_rg" >
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        $selected = ($clt['uf_rg'] == $row_uf['uf_sigla']) ? "selected" : "";
        echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select></td>
                                        <td class="secao">Data Expedição:</td>
                                        <td>
                                            <input name="data_rg" type="text" size="12" maxlength="10" id="data_rg" value="<?= $clt['data_rg'] ?>"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, cpf.id)" />
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="secao">CPF:</td>
                                        <td>
                                            <input name="cpf" type="text" id="cpf" size="17" maxlength="14" class="validate[required,funcCall[verificaCPF]] verificaCpfFunDemitidos" value="<?= $clt['cpf'] ?>" />
                                        </td>


                                        <td class="secao">&Oacute;rg&atilde;o Regulamentador:</td>
                                        <td colspan="3">
                                            <input name="conselho" class="<?= (in_array($_REQUEST['cbo'], $arrayCboMedico)) ? 'validate[required]' : '' ?>" type="text" id="conselho" size="17" value="<?= $clt['conselho'] ?>" /><br><br>
                                            <input type="checkbox" name="verifica_orgao" value="1" /> Verificado?
                                        </td>
                                        <td class="secao">Data de emissão:</td>
                                        <td>
                                            <input name="data_emissao" type="text" size="12" maxlength="10" id="data_emissao"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, reservista.id)" />    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nº Carteira de Trabalho:</td>
                                        <td>
                                            <input name="trabalho" type="text" id="trabalho" size="15" class="validate[required]" value="<?= $clt['trabalho'] ?>" />
                                        </td>
                                        <td class="secao">Série:</td>
                                        <td>
                                            <input name="serie_ctps" type="text" id="serie_ctps" size="10" class="validate[required]" value="<?= $clt['serie_ctps'] ?>" />		  
                                        </td>
                                        <td class="secao">UF:</td>
                                        <td>
                                            <select name="uf_ctps" id="uf_ctps" class="validate[required]" value="<?= $clt['uf_ctps'] ?>" >
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select>
                                        </td>
                                        <td class="secao">Data carteira de Trabalho:</td>
                                        <td>
                                            <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps" value="<?= $clt['data_ctps'] ?>"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, titulo2.id)" />    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nº Título de Eleitor:</td>
                                        <td>
                                            <input name="titulo" type="text" id="titulo2" size="10" value="<?= $clt['titulo'] ?>" />
                                        </td>
                                        <td class="secao">Zona:</td>
                                        <td colspan="3">
                                            <input name="zona" type="text" id="zona2" size="3" value="<?= $clt['zona'] ?>" />
                                        </td>
                                        <td class="secao">Seção:</td>
                                        <td>
                                            <input name="secao" type="text" id="secao" size="3" value="<?= $clt['secao'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">PIS:</td>
                                        <td>

                                            <input name="pis" type="text" id="pis" size="12" value="<?= $clt['pis'] ?>">

                                        </td>
                                        <td class="secao">Data Pis:</td>
                                        <td colspan="3">
                                            <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis" value="<?= $clt['data_pis'] ?>"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, fgts.id)" />
                                        </td>
                                        <td class="secao">FGTS:</td>
                                        <td>
                                            <input name="fgts" type="text" id="fgts" size="10" value="<?= $clt['fgts'] ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Certificado de Reservista:</td>
                                        <td colspan="7">
                                            <input name="reservista" type="text" id="reservista" size="18" value="<?= $clt['reservista'] ?>" />
                                        </td>
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="6" class="secao_pai">BENEFÍCIOS</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Assistência Médica:</td>
                                        <td>
                                            <label><input name="medica" type="radio" class="reset" value="1" <?= $chek_medi1 ?>>Sim</label> 
                                            <label><input name="medica" type="radio" class="reset" value="0" <?= $chek_medi0 ?>>Não</label> 
    <?= $mensagem_medi ?>
                                        </td>
                                        <td class="secao">Tipo de Plano:</td>
                                        <td>
                                            <select name="plano_medico" id="plano_medico">
                                                <option value="1" <?= $selected_planoF ?>>Familiar</option>
                                                <option value="2" <?= $selected_planoI ?>>Individual</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="planosaude" style="display:none;">
                                        <td class="secao">Plano de Saúde</td>
                                        <td colspan="3"><?= montaSelect($arrayPlanoSaude, $row['id_plano_saude'], 'name="id_plano_saude" id="id_plano_saude"') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Seguro, Apólice:</td>
                                        <td>
                                            <select name="apolice" id="apolice">
                                                <option value="0">Não Possui</option>
    <?php
    $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $id_regiao");
    while ($row_ap = mysql_fetch_array($result_ap)) {
        if ($row_ap['id_apolice'] == $row[apolice]) {
            print "<option value='$row_ap[id_apolice]' selected>$row_ap[razao]</option>";
        } else {
            print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
        }
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Dependente:</td>
                                        <td>
                                            <input name="dependente" type="text" id="dependente" size="20"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Insalubridade:</td>
                                        <td><input name="insalubridade" type="checkbox" class="reset" id="insalubridade2" value="1" /></td>  
                                        <td class="secao">Adicional Noturno:</td>
                                        <td>	
                                            <label><input name="ad_noturno" type="radio" class="reset" value="1">Sim</label>
                                            <label><input name="ad_noturno" type="radio" class="reset" value="0">Não</label>
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td class="secao">Desconto de INSS:</td>
                                        <td><label><input name="desconto_inss" type="checkbox" class="reset" value="1"
                                                          onClick="document.getElementById('desconto_inss').style.display = (document.getElementById('desconto_inss').style.display == 'none') ? '' : 'none';" /></label>
                                        </td>
                                        <td class="secao">Integrante do CIPA:</td>
                                        <td>
                                            <label><input name="cipa" type="radio" class="reset" value="1">Sim</label>
                                            <label><input name="cipa" type="radio" class="reset" value="0">Não</label>	
                                        </td>
                                    </tr>
                                    <tr>

                                        <td class="secao">Pensão Alimentícia:</td>
                                        <td>
                                            <label><input name="pensao_alimenticia" type="radio" class="reset" value="1" <?php echo ($flag_pensao == 1) ? "checked='checked'" : ""; ?> />Sim</label>
                                            <label><input name="pensao_alimenticia" type="radio" class="reset" value="0" <?php echo ($flag_pensao == 0) ? "checked='checked'" : ""; ?> />Não</label>
                                            <select name="pensao_percentual" style="">
                                                <option value="">Selecione uma faixa</option>
                                                <option value="0.10" <?php echo ($valor_pensao == '0.10') ? "selected='selected'" : ""; ?>>10%</option>
                                                <option value="0.13" <?php echo ($valor_pensao == '0.13') ? "selected='selected'" : ""; ?>>13%</option>
                                                <option value="0.15" <?php echo ($valor_pensao == '0.15') ? "selected='selected'" : ""; ?>>15%</option>
                                                <option value="0.175" <?php echo ($valor_pensao == '0.175') ? "selected='selected'" : ""; ?>>17,5%</option>
                                                <option value="0.20" <?php echo ($valor_pensao == '0.20') ? "selected='selected'" : ""; ?>>20%</option>
                                                <option value="0.24" <?php echo ($valor_pensao == '0.24') ? "selected='selected'" : ""; ?>>24%</option>
                                                <option value="0.25" <?php echo ($valor_pensao == '0.25') ? "selected='selected'" : ""; ?>>25%</option>
                                                <option value="0.30" <?php echo ($valor_pensao == '0.30') ? "selected='selected'" : ""; ?>>30%</option>
                                                <option value="0.3333" <?php echo ($valor_pensao == '0.3333') ? "selected='selected'" : ""; ?>>33,33%</option>
                                                <option value="0.35" <?php echo ($valor_pensao == '0.35') ? "selected='selected'" : ""; ?>>35%</option>
                                                <option value="0.40" <?php echo ($valor_pensao == '0.40') ? "selected='selected'" : ""; ?>>40%</option>
                                                <option value="2" <?php echo ($valor_pensao == '2') ? "selected='selected'" : ""; ?>>1 Salário Mínimo</option>
                                            </select>
                                        </td>
<td class="secao"></td>
                                        <td></td>
                                    </tr>

    <?php
//                                    if($_COOKIE['logado']==202){ 

    $sql_va = "SELECT A.*,B.*, A.`status` AS status_tipo, B.`status` AS status_categoria
                                                    FROM rh_va_tipos AS A
                                                    LEFT JOIN rh_va_categorias AS B ON(A.id_va_categoria=B.id_va_categoria)
                                                    WHERE A.`status`=1 AND B.`status`=1";

    $result_va = mysql_query($sql_va);

    $arr_va = array();

    while ($row_va = mysql_fetch_array($result_va)) {
        $arr_va[$row_va['id_va_categoria']] = $row_va['nome_categoria'];
        $arr_va_campo[$row_va['id_va_categoria']] = $row_va['campo_clt'];
        $arr_va_tipos[$row_va['id_va_categoria']][$row_va['id_va_tipos']] = $row_va['nome_tipo'];
    }
    foreach ($arr_va as $k => $row_va) {
        ?>
                                        <tr>
                                            <td class="secao">Vale <?= $row_va; ?>:</td>
                                            <td colspan="3">
                                                <select name="<?= $arr_va_campo[$k]; ?>">
                                                    <option value="0" selected="selected">NÃO POSSUI</option>
        <?php foreach ($arr_va_tipos[$k] as $i => $row_tipo) { ?>
                                                        <option value="<?= $i; ?>"><?= $row_tipo; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
        <?php
        //  } 
    }
    ?>

                                    <tr>
                                        <td class="secao">Vale Transporte:</td>
                                        <td colspan="3">
                                            <input name="transporte" type="checkbox" class="reset" id="transporte2" onClick="document.getElementById('tablevale').style.display = (document.getElementById('tablevale').style.display == 'none') ? '' : 'none';" value="1" />
                                        </td>
                                    </tr>



                                </table>









                                <table cellpadding="0" cellspacing="1" class="secao" id="desconto_inss" style="display:none;">
                                    <tr>
                                        <td colspan="4" class="secao_pai">DESCONTO DE INSS</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Tipo de Desconto:</td>
                                        <td>
                                            <label><input name="tipo_desconto_inss" type="radio" class="reset" value="isento" checked>Suspenção de Recolhimento</label><br>
                                            <label><input name="tipo_desconto_inss" type="radio" class="reset" value="parcial">Parcial</label>
                                        </td>
                                        <td class="secao">Trabalha em outra empresa?</td>
                                        <td>
                                            <label><input name="trabalha_outra_empresa" type="radio" class="reset" onClick="mostraInss(false)" value="sim">Sim</label>
                                            <label><input name="trabalha_outra_empresa" type="radio" class="reset" onClick="mostraInss('none')" value="nao" checked>Não</label>
                                        </td>
                                    </tr>
                                    <tr id="outra_empresa" style="display:none;" class="outra_empresa">
                                        <td class="secao">Salário da outra empresa:</td>
                                        <td>
                                            <input name="salario_outra_empresa" type="text" size="12" class="formata_valor">
                                        </td>
                                        <td class="secao">Desconto da outra empresa:</td>
                                        <td>
                                            <input name="desconto_outra_empresa" type="text" size="12" class="formata_valor">
                                        </td>
                                    </tr>

                                    <tr class="outra_empresa" style="display:none">
                                        <td class="secao">Início:</td>
                                        <td>
                                            <input type="text" name="inicio_inss" class="data_inss" id="data_inicio_inss"><br>
                                        </td>
                                        <td class="secao">Fim:</td>
                                        <td>
                                            <input type="text" name="fim_inss" class="data_inss" id="data_fim_inss"><br>
                                        </td>
                                    </tr>

                                    <tr class="outra_empresa" style="display:none">
                                        <td class="secao">CNPJ Outro Vinculo:</td>
                                        <td>
                                            <input name='cnpj' type='text' id='cnpj' size="19" maxlength='18' OnKeyPress="formatar('##.###.###/####-##', this)" onkeyup="pula(18,this.id,e_endereco.id)" />
                                        </td>
                                        <td class="secao"></td>
                                        <td></td>
                                    </tr>
                                </table>

                                <table cellpadding="0" cellspacing="1" class="secao" id="tablevale" style="display:none;">
                                    <tr>
                                        <td colspan="6" class="secao_pai">VALE TRANSPORTE</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Valor Diário:</td>
                                        <td colspan="4">
                                            <input name="vt_valor_diario" type="text" size="12" class="formata_valor" value="<?= str_replace('.', ',', $row['vt_valor_diario']) ?>" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Selecione 1:</td>
                                        <td colspan="4">
                                            <select name="vale1" id="vale1">
                                                <option value="0">Não Tem</option>

    <?php
    $resul_vale_trans = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");

    while ($row_vale_trans = mysql_fetch_array($resul_vale_trans)) {
        $result_conce = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans[id_concessionaria]'");
        $row_conce = mysql_fetch_array($result_conce);

        if ($row_vale['id_tarifa1'] == "$row_vale_trans[0]") {
            print "<option value='$row_vale_trans[0]' selected>$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - 
		$row_conce[nome]</option>";
        } else {
            print "<option value='$row_vale_trans[0]'>$row_vale_trans[valor] - $row_vale_trans[tipo] [$row_vale_trans[itinerario]] - $row_conce[nome]
		</option>";
        }
    }
    ?>
                                            </select>  
                                        </td>
                                    </tr>  
                                    <tr>
                                        <td class="secao">Selecione 2:</td>
                                        <td colspan="4">
                                            <select name="vale2" id="vale2">
                                                <option value="0">Não Tem</option>
    <?php
    $resul_vale_trans2 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
    while ($row_vale_trans2 = mysql_fetch_array($resul_vale_trans2)) {
        $result_conce2 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans2[id_concessionaria]'");
        $row_conce2 = mysql_fetch_array($result_conce2);
        if ($row_vale['id_tarifa2'] == "$row_vale_trans2[0]") {
            print "<option value='$row_vale_trans2[0]' selected>$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]</option>";
        } else {
            print "<option value='$row_vale_trans2[0]'>$row_vale_trans2[valor] - $row_vale_trans2[tipo] [$row_vale_trans2[itinerario]] - $row_conce2[nome]</option>";
        }
    }
    ?></select>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Selecione 3:</td>
                                        <td colspan="4">
                                            <select name="vale3" id="vale3">
                                                <option value="0">Não Tem</option>
    <?php
    $resul_vale_trans3 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
    while ($row_vale_trans3 = mysql_fetch_array($resul_vale_trans3)) {
        $result_conce3 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans3[id_concessionaria]'");
        $row_conce3 = mysql_fetch_array($result_conce3);
        if ($row_vale['id_tarifa3'] == "$row_vale_trans3[0]") {
            print "<option value='$row_vale_trans3[0]' selected>$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]</option>";
        } else {
            print "<option value='$row_vale_trans3[0]'>$row_vale_trans3[valor] - $row_vale_trans3[tipo] [$row_vale_trans3[itinerario]] - $row_conce3[nome]</option>";
        }
    }
    ?>
                                            </select>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Selecione 4:</td>
                                        <td colspan="4">
                                            <select name="vale4" id="vale4">
                                                <option value="0">Não Tem</option>
    <?php
    $resul_vale_trans4 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
    while ($row_vale_trans4 = mysql_fetch_array($resul_vale_trans4)) {
        $result_conce4 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans4[id_concessionaria]'");
        $row_conce4 = mysql_fetch_array($result_conce4);
        if ($row_vale['id_tarifa4'] == "$row_vale_trans4[0]") {
            print "<option value='$row_vale_trans4[0]' selected>$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]</option>";
        } else {
            print "<option value='$row_vale_trans4[0]'>$row_vale_trans4[valor] - $row_vale_trans4[tipo] [$row_vale_trans4[itinerario]] - $row_conce4[nome]</option>";
        }
    }
    ?>
                                            </select>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Selecione 5:</td>
                                        <td colspan="4">
                                            <select name="vale5" id="vale5">
                                                <option value="0">Não Tem</option>
    <?php
    $resul_vale_trans5 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
    while ($row_vale_trans5 = mysql_fetch_array($resul_vale_trans5)) {
        $result_conce5 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans5[id_concessionaria]'");
        $row_conce5 = mysql_fetch_array($result_conce5);
        if ($row_vale['id_tarifa5'] == "$row_vale_trans5[0]") {
            print "<option value='$row_vale_trans5[0]' selected>$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]</option>";
        } else {
            print "<option value='$row_vale_trans5[0]'>$row_vale_trans5[valor] - $row_vale_trans5[tipo] [$row_vale_trans5[itinerario]] - $row_conce5[nome]</option>";
        }
    }
    ?>
                                            </select>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Selecione 6:</td>
                                        <td colspan="4">
                                            <select name="vale6" id="vale6">
                                                <option value="0">Não Tem</option>

    <?php
    $resul_vale_trans6 = mysql_query("SELECT * FROM rh_tarifas WHERE id_regiao = '$id_regiao' and status_reg = '1'");
    while ($row_vale_trans6 = mysql_fetch_array($resul_vale_trans6)) {
        $result_conce6 = mysql_query("SELECT * FROM rh_concessionarias WHERE id_concessionaria = '$row_vale_trans6[id_concessionaria]'");
        $row_conce6 = mysql_fetch_array($result_conce6);
        if ($row_vale['id_tarifa6'] == "$row_vale_trans6[0]") {
            print "<option value='$row_vale_trans6[0]' selected>$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]</option>";
        } else {
            print "<option value='$row_vale_trans6[0]'>$row_vale_trans6[valor] - $row_vale_trans6[tipo] [$row_vale_trans6[itinerario]] - $row_conce6[nome]</option>";
        }
    }
    ?>
                                            </select>  
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Numero Cartão 1:</td>
                                        <td>
                                            <input name="num_cartao" type="text" id="num_cartao" size="20" value="<?= $row_vale['cartao1'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Numero Cartão 2:</td>
                                        <td>
                                            <input name="num_cartao2" type="text" id="num_cartao2" size="20" value="<?= $row_vale['cartao2'] ?>"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                </table>

                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="2" class="secao_pai">SINDICATO</td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="secao">Possui Sindicato:</td>
                                        <td width="80%">
                                            <!--AMANDA-->
                                            <label><input name="radio_sindicato" type="radio" class="reset" value="sim" >Sim</label>
                                            <label><input name='radio_sindicato' type='radio' class="reset" value='nao' checked='checked' >Não</label>
                                            <!--FIM-->
                                        </td>
                                    </tr>
                                    <tr style="display:none" id="trsindicato">
                                        <td class="secao">Selecionar:</td>
                                        <td>
                                            <select name="sindicato" id="sindicato" >
                                                <option value="">Selecione</option>
    <?php
    $re_sindicato = mysql_query("SELECT * FROM rhsindicato WHERE id_regiao = '$id_regiao'");
    while ($row_sindi = mysql_fetch_array($re_sindicato)) {
        echo "<option value='" . $row_sindi['id_sindicato'] . "'>" . substr($row_sindi['nome'], 0, 80) . "</option>";
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="secao">Isento de Contribuição:</td>
                                        <td width="80%">
                                            <!--AMANDA-->
                                            <label><input name="radio_contribuicao" type="radio" class="reset"  value="sim" >Sim</label>
                                            <label><input name='radio_contribuicao' type='radio' class="reset"  value='nao' checked='checked' >Não</label>
                                            <!--FIM-->
                                        </td>
                                    </tr>
                                    <tr style="display:none" id="trcontribuicao">
                                        <td class="secao">Ano:</td>
                                        <td>
                                            <select name="ano_contribuicao" id="ano_contribuicao" >
                                                <option value="">Selecione</option>
    <?php
    for ($ano = intval(date("Y")); $ano != 1999; $ano--) {
        echo '<option value="' . $ano . '">' . $ano . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table> 
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="4" class="secao_pai">DADOS BANCÁRIOS</td>
                                    </tr>
                                    <tr>
                                        <td width="15%" class="secao">Banco:</td>
                                        <td width="30%">
                                            <select name="banco" id="banco">
                                                <option value="0">Sem Banco</option>
    <?php
    $result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' AND status_reg = '1'");
    while ($row_banco = mysql_fetch_array($result_banco)) {
        print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
    }
    ?>
                                                <option value="9999">Outro Banco</option>
                                            </select>
                                        </td>
                                        <td width="25%" class="secao">Agência:</td>
                                        <td width="30%">
                                            <input name="agencia" type="text" id="agencia" size="12" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Conta:</td>
                                        <td>
                                            <input name="conta" type="text" id="conta" size="12" /><br />
                                            <label><input name="radio_tipo_conta" type="radio" class="reset" value="salario">Conta Salário </label>
                                            <label><input name="radio_tipo_conta" type="radio" class="reset" value="corrente">Conta Corrente </label>
                                        </td>
                                        <td class="secao">Nome do Banco:<br />(caso não esteja na lista acima)</td>
                                        <td><input name="nomebanco" type="text" id="nomebanco" size="25" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td colspan="4" class="secao_pai">DADOS FINANCEIROS E DE CONTRATO</td>
                                    </tr>
                                    <tr>
                                        <td  class="secao">Data de Entrada:</td>
                                        <td colspan="3">
                                            <input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada" readonly="readonly" class="validate[required]"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, data_exame.id)" />
                                        
                                            <span id="dataFinalExperiencia" style="margin-left: 20px;"></span>
                                        </td>
                                    </tr>
                                    
                                    <!-- FEITO POR SINESIO - 24/03/2016 - 2027 -->
                                    <tr>
                                        <td class="secao">Data do Exame Admissional:</td>
                                        <td colspan="3">
                                            <input name="data_exame" type="text" size="12" maxlength="10" id="data_exame"
                                                   onkeyup="mascara_data(this);
                                                               pula(10, this.id, localpagamento.id)" />
                                        </td>                                       
                                    </tr>
                                    
                                    <tr>
                                        <td class="secao">Local de Pagamento:</td>
                                        <td width="20%">
                                            <input name="localpagamento" type="text" id="localpagamento" size="25" class="validate[required]"  
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td width="19%" class="secao">Tipo de admiss&atilde;o</td>
                                        <td width="38%">
    <?php echo montaSelect($arrayTipoAdmi, null, "name='tipo_admissao' id='tipo_admissao' class='validate[required]' style='width: 300px;'"); ?>     
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Tipo de Pagamento:</td>
                                        <td colspan="3">
                                            <select name="tipopg" id="tipopg" class="validate[required]">
                                                <option value="">Selecione...</option>
    <?php
    $result_pg = mysql_query("SELECT * FROM tipopg WHERE id_projeto = '$projeto'");
    while ($row_pg = mysql_fetch_array($result_pg)) {
        print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>                    
                                        <td  class="secao">Prazo de Experiência:</td>
                                        <td colspan="5" align="left">
                                            <input type="radio" name="prazoExp" value="4" />30
                                            <input type="radio" name="prazoExp" value="5" />45
                                            <input type="radio" name="prazoExp" value="6" />60
                                            <input type="radio" name="prazoExp" value="1" />30 + 60
                                            <input type="radio" name="prazoExp" value="2" checked="checked" />45 + 45
                                            <input type="radio" name="prazoExp" value="3" />60 + 30
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Tipo de Contrato:</td>
                                        <td colspan="3">
                                            <select name="tipo_contrato" id="tipo_contrato">
    <?php
    $result_tpContrato = mysql_query("SELECT id_categoria_trab, descricao FROM categorias_trabalhadores WHERE grupo = 'Empregado e Trabalhador T';");
    while ($row_tpContrato = mysql_fetch_array($result_tpContrato)) {
        print "<option value='{$row_tpContrato['id_categoria_trab']}'>{$row_tpContrato['descricao']}</option>";
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Observações:</td>
                                        <td colspan="3">
                                            <textarea name="observacoes" id="observacoes" cols="55" rows="4"  onchange="this.value = this.value.toUpperCase()"></textarea></td>
                                    </tr>
                                </table>
                                <!--                                <div id="finalizacao">
                                                                    O Contrato foi <strong>assinado</strong>?
                                                                    <input name="impressos2" type="checkbox" class="reset" id="impressos2" value="1" />
                                                                    <p>&nbsp;</p>
                                                                    <p>Outros documentos foram <strong>assinados</strong>?<br>
                                                                        <label>
                                                                            <input name='assinatura3' type='radio' class="reset" id='assinatura3' value='1'> 
                                                                            Sim </label>
                                                                        <label>
                                                                            <input name='assinatura3' type='radio' class="reset" id='assinatura3' value='0'> 
                                                                            N&atilde;o</label>
    <?= $mensagem_ass ?>                 
                                                                    </p>
                                                                </div>-->
                                <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                                <div id='participanteDesativado'>
                                    O PARTICIPANTE JÁ PARTICIPOU DO GRUPO DE TRABALHO, DESEJA CONTINUAR? 
                                    <input type="button" name="cadSim" value="Sim" /><br />
                                    <span id='part'></span>
                                </div>
                                <div align="center">
                                    <input type="submit" name="Submit" value="CADASTRAR" class="botao" /></div> 
                                <input type="hidden" name="regiao" value="<?= $id_regiao ?>"/>
                                <input type="hidden" name="projeto" value="<?= $projeto ?>" />
                                <input type="hidden" name="user" value="<?= $id_user ?>" />
    <?php if ($id_clt) { ?>
                                    <input type="hidden" name="clonar" value="<?= $id_clt ?>" />
                                <?php } ?>
                                <input type="hidden" name="update" value="1" />
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
            <script language="javascript" type="text/javascript">
 
                function validaForm() {

                    if ($("#pis").val() == '') {
                        alert('O campo de PIS foi deixado em branco, mas precisa ser preenchido no futuro.');
                    }
                    //AMANDA
//                    if ($("#horario").val() === "" || !($("#horario").length)) {
//                        alert('É necessário cadastrar um horário para este funcionário.');
//                        $("#idcursos").focus();
//                        return false;
                  //  }//FIM

                    return true;
                }

                $(function () {
                    $('#data_nasc, #data_nasc_conjuge, #data_nasc_pai, #data_nasc_mae, #data_escola, #data_filho_1,#data_filho_2, #data_filho_3,#data_filho_4,#data_filho_5, #data_filho_6,#data_rg,\n\
            #data_ctps, #data_pis, #data_entrada,#data_exame, #data_nasc_avo_h, #data_nasc_avo_m, #data_nasc_bisavo_h, #data_nasc_bisavo_m, #data_emissao,#ano_chegada_pais, #data_fim_inss, #data_inicio_inss').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "1950:<?php echo date('Y') ?>",
                        dateFormat: 'dd/mm/yy'
                    });
                    $(".porcentagem").maskMoney({suffix: '%', thousands: '', precision: 2});

                    $('#portador1,#portador2,#portador3, #portador4, #portador5, #portador6').change(function () {

                        var elemento = $(this);
                        var linha = elemento.parent().parent();
                        var nome = linha.find('.nome_filho').val();
                        var data = linha.find('.data_filho').val();


                        if (nome == '') {
                            alert("Preencha o nome do filho.");
                            linha.find('.nome_filho').focus();
                            elemento.attr('checked', false);
                            $(this).attr('checked', false);
                        }
                        if (data == '') {
                            alert("Preencha a data de nascimento do filho.");
                            linha.find('.data_filho').focus();
                            elemento.attr('checked', false);
                            $(this).attr('checked', false);
                        }


                    });

                    $('#ddir_pai').change(function () {

                        var linha = $(this).parent().parent();
                        var pai = linha.find('#pai')

                        if (pai.val() == '') {
                            alert('Preencha o nome do pai.');
                            pai.focus();
                            $(this).attr('checked', false);
                        }


                    });


                    $('#ddir_mae').change(function () {

                        var linha = $(this).parent().parent();
                        var mae = linha.find('#mae')

                        if (mae.val() == '') {
                            alert('Preencha o nome da mãe.');
                            mae.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_conjuge').change(function () {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#conjuge')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do conjuge.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_avo_h').change(function () {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#avo_h')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Avô.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_avo_m').change(function () {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#avo_m')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Avó.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_bisavo_h').change(function () {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#bisavo_h')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Bisavô.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });
                    $('#ddir_bisavo_m').change(function () {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#bisavo_m')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Bisavó.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });
                });
            </script>
        </body>
    </html>
    <?php
} else { // CADASTRO DE CLT
    $dataEntrada = $_REQUEST['data_entrada'];
    $ano_entrada = date("Y", strtotime(str_replace("/", "-", $dataEntrada)));

    if ($ano_entrada < 2009) {

        print "<html>
                     <head>
                     <title>:: Intranet ::</title>
                     </head>
                     <body>
                     <script type='text/javascript'>
                     alert('Digite uma data de entrada Valida');
                     history.back();
                     </script>
                     </body>
                     </html>";

        exit;
    }
    $query = "select cpf from rh_clt where cpf ='{$_REQUEST['cpf']}' and id_projeto = '{$_REQUEST['projeto']}' AND status < 60";
    $result = mysql_query($query);

    $num_cpf = mysql_num_rows($result);
    if ($num_cpf > 0) {
        echo "
        <html>
            <head>
                <title>:: Intranet ::</title>
            </head>
            <body>
                <script type='text/javascript'>
                    alert('Já existe um registro com este CPF para este Projeto. Só é permitido um CPF por projeto.');
                    history.back();
                </script>
            </body>
        </html>";
        exit();
    }

    $regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
    $id_setor = $_REQUEST['id_setor'];
    $id_plano_saude = $_REQUEST['id_plano_saude'];
// Dados de Contratação
    $vinculo = $_REQUEST['vinculo'];
    $tipo_contratacao = $_REQUEST['contratacao'];
    $id_curso = $_REQUEST['idcursos'];
    $horario = $_REQUEST['horario'];

    //trata unidade
    $locacao = explode("//", $_REQUEST['locacao']);
    $locacao_nome = $locacao[0];
    $locacao_id = $locacao[1];

// Dados Pessoais
    $nome = mysql_real_escape_string(trim($_REQUEST['nome']));
    $nome_social = mysql_real_escape_string(trim($_REQUEST['nomesocial']));
    $sexo = $_REQUEST['sexo'];
    $endereco = mysql_real_escape_string(trim($_REQUEST['endereco']));

    $numero = mysql_real_escape_string(trim($_REQUEST['numero']));
    $complemento = $_REQUEST['complemento'];
    $bairro = mysql_real_escape_string(trim($_REQUEST['bairro']));
    $cidade = mysql_real_escape_string(trim($_REQUEST['cidade']));
    $uf = $_REQUEST['uf'];
    $cep = $_REQUEST['cep'];
    $tel_fixo = $_REQUEST['tel_fixo'];
    $tel_cel = $_REQUEST['tel_cel'];
    $tel_rec = $_REQUEST['tel_rec'];
    $data_nasc = $_REQUEST['data_nasc'];
    $municipio_nasc = $_REQUEST['municipio_nasc'];
    $uf_nasc = $_REQUEST['uf_nasc_select'];
//Verifica se o uf_nasc foi digitado ou selecionado
    if (empty($uf_nasc)) {
        $uf_nasc = $_REQUEST['uf_nasc_text'];
    }
    $naturalidade = $_REQUEST['naturalidade'];
    $cod_nacionalidade = $_REQUEST['nacionalidade'];

//NOME DA NACIONALIDADE
    $qr_nome_nacionalidade = mysql_query("select nome from cod_pais_rais where codigo = $cod_nacionalidade");
    $row_nome_nacionalidade = mysql_fetch_row($qr_nome_nacionalidade);
    $nome_nacionalidade = $row_nome_nacionalidade[0];

    $civil = $_REQUEST['civil'];
    $tiposanguineo = $_REQUEST['tiposanguineo'];

    $ano_chegada_pais = $_REQUEST['ano_chegada_pais'];



// Documentação
    $rg = removeAspas($_REQUEST['rg']);
    $uf_rg = $_REQUEST['uf_rg'];
    $secao = removeAspas($_REQUEST['secao']);
    $data_rg = $_REQUEST['data_rg'];
    $cpf = $_REQUEST['cpf'];
    $conselho = removeAspas($_REQUEST['conselho']);
    $titulo = removeAspas($_REQUEST['titulo']);
    $zona = removeAspas($_REQUEST['zona']);
    $orgao = removeAspas($_REQUEST['orgao']);

// Mais
    $pai = mysql_real_escape_string(trim($_REQUEST['pai']));
    $mae = mysql_real_escape_string(trim($_REQUEST['mae']));
    $conjuge = mysql_real_escape_string(trim($_REQUEST['conjuge']));
    $avo_h = mysql_real_escape_string(trim($_REQUEST['avo_h']));
    $avo_m = mysql_real_escape_string(trim($_REQUEST['avo_m']));
    $bisavo_h = mysql_real_escape_string(trim($_REQUEST['bisavo_h']));
    $bisavo_m = mysql_real_escape_string(trim($_REQUEST['bisavo_m']));
    $nacionalidade_pai = mysql_real_escape_string(trim($_REQUEST['nacionalidade_pai']));
    $nacionalidade_mae = mysql_real_escape_string(trim($_REQUEST['nacionalidade_mae']));

    $data_nasc_pai = $_REQUEST['data_nasc_pai'];
    $data_nasc_mae = $_REQUEST['data_nasc_mae'];
    $data_nasc_conjuge = $_REQUEST['data_nasc_conjuge'];
    $data_nasc_avo_h = $_REQUEST['data_nasc_avo_h'];
    $data_nasc_avo_m = $_REQUEST['data_nasc_avo_m'];
    $data_nasc_bisavo_h = $_REQUEST['data_nasc_bisavo_h'];
    $data_nasc_bisavo_m = $_REQUEST['data_nasc_bisavo_m'];

    $ddir_pai = $_REQUEST['ddir_pai'];
    $ddir_mae = $_REQUEST['ddir_mae'];
    $ddir_conjuge = $_REQUEST['ddir_conjuge'];
    $ddir_avo_h = $_REQUEST['ddir_avo_h'];
    $ddir_avo_m = $_REQUEST['ddir_avo_m'];
    $ddir_bisavo_h = $_REQUEST['ddir_bisavo_h'];
    $ddir_bisavo_m = $_REQUEST['ddir_bisavo_m'];

    $estuda = $_REQUEST['estuda'];
    $data_escola = $_REQUEST['data_escola'];
    $escolaridade = $_REQUEST['escolaridade'];
    $instituicao = $_REQUEST['instituicao'];
    $curso_aprendiz = $_REQUEST['curso_aprendiz'];
    $curso = $_REQUEST['curso'];

// Dados Financeiros
    $data_entrada = $_REQUEST['data_entrada'];

    $banco = $_REQUEST['banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $nomebanco = $_REQUEST['nomebanco'];
    $tipoDeConta = $_REQUEST['radio_tipo_conta'];
    $localpagamento = mysql_real_escape_string(trim($_REQUEST['localpagamento']));
    $apolice = $_REQUEST['apolice'];
    $campo1 = removeAspas($_REQUEST['trabalho']);
    $campo2 = $_REQUEST['dependente'];
    $campo3 = $_REQUEST['codigo'];
    $data_cadastro = date('Y-m-d');
    $pis = str_replace('.', '', str_replace('-', '', $_REQUEST['pis']));
    $fgts = removeAspas($_REQUEST['fgts']);
    $tipopg = $_REQUEST['tipopg'];
    $filhos = $_REQUEST['filhos'];
    $observacoes = $_REQUEST['observacoes'];
    $medica = $_REQUEST['medica'];
    $assinatura2 = $_REQUEST['assinatura2'];
    $assinatura3 = $_REQUEST['assinatura3'];
    $plano_medico = $_REQUEST['plano_medico'];
    $serie_ctps = $_REQUEST['serie_ctps'];
    $uf_ctps = $_REQUEST['uf_ctps'];
    $pis_data = $_REQUEST['data_pis'];
    $verifica_orgao = $_REQUEST['verifica_orgao'];
    $vt_valor_diario = str_replace(",", ".", str_replace(".","",$_REQUEST['vt_valor_diario']));
    $pensao_percentual = $_REQUEST['pensao_percentual'];

    $contrato_medico = $_POST['contrato_medico'];

    // tipo de contrato
    $tipo_contrato = $_REQUEST['tipo_contrato'];

    $prazoExp = $_REQUEST['prazoExp'];

    // NOVOS CAMPOS PARA O E-SOCIAL
    $cod_pais_nacionalidade = $_REQUEST['cod_pais_nacionalidade'];
    $cod_pais_nasc = $_REQUEST['cod_pais_nasc'];
    $tipo_endereco = $_REQUEST['cod_tp_logradouro'];
    $cod_muni_nasc = $_REQUEST['cod_municipio_nasc'];
    $cod_cidade = $_REQUEST['cod_cidade'];

    // VALES
    $vale_refeicao = isset($_REQUEST['vale_refeicao']) ? $_REQUEST['vale_refeicao'] : NULL;
    $vale_alimentacao = isset($_REQUEST['vale_alimentacao']) ? $_REQUEST['vale_alimentacao'] : NULL;

    $cnpj = $_REQUEST['cnpj'];
    $data_inicio = $_REQUEST['inicio_inss'];
    $data_fim = $_REQUEST['fim_inss'];
    $data_inicio = (!empty($data_inicio)) ? "'" . ConverteData($data_inicio) . "'" : 'null';
    $data_fim = (!empty($data_fim)) ? "'" . ConverteData($data_fim) . "'" : 'null';

//Inicio Verificador CPF
    $qrCpf = mysql_query("SELECT COUNT(id_clt) AS total FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND id_regiao = '$id_regiao' AND (status < 60) ");
    $rsCpf = mysql_fetch_assoc($qrCpf);
    $totalCpf = $rsCpf['total'];
    if ($totalCpf > 0) {
        ?>

        <script type="text/javascript">
            alert("Esse CPF já existe para esse projeto");
            window.history.back();
        </script>

        <?php
        exit();
    }
//Fim verificador CPF
//Inicio verificador PIS
    /*
      if(strlen($pis) != 11)
      {
      ?>
      <script type="text/javascript">
      alert("PIS Inválido!");
      window.history.back();
      </script>
      <?php
      exit();
      }
     */
//Fim verificador PIS    
///GERANDO NÚMERO DE MATRICULA E O NÚMERO DO PROCESSO
    $verifica_matricula = mysql_result(mysql_query("SELECT MAX(matricula) FROM rh_clt WHERE id_regiao = '$regiao'"), 0);
    $matricula = $verifica_matricula + 1;

    $n_processo = $matricula;



    if (empty($_REQUEST['insalubridade'])) {
        $insalubridade = '0';
    } else {
        $insalubridade = $_REQUEST['insalubridade'];
    }
    if (empty($_REQUEST['transporte'])) {
        $transporte = '0';
    } else {
        $transporte = $_REQUEST['transporte'];
    }
    if (empty($_REQUEST['impressos2'])) {
        $impressos = '0';
    } else {
        $impressos = $_REQUEST['impressos2'];
    }

// Desconto INSS
    if (empty($_REQUEST['desconto_inss'])) {
        $desconto_inss = 0;
        $tipo_desconto_inss = 0;
        $valor_desconto_inss = 0;
        $trabalha_outra_empresa = 0;
        $salario_outra_empresa = 0;
        $desconto_outra_empresa = 0;
    } else {
        $desconto_inss = 1;
        $tipo_desconto_inss = $_REQUEST['tipo_desconto_inss'];

        if ($tipo_desconto_inss == 'isento') {
            $valor_desconto_inss = 0;
        } elseif ($tipo_desconto_inss == 'parcial') {
            $valor_desconto_inss = str_replace(',', '.', str_replace('.', '', $_REQUEST['valor_desconto_inss']));
        }

        $trabalha_outra_empresa = $_REQUEST['trabalha_outra_empresa'];

        if ($trabalha_outra_empresa == 'sim') {
            $salario_outra_empresa = str_replace(',', '.', str_replace('.', '', $_REQUEST['salario_outra_empresa']));
            $desconto_outra_empresa = str_replace(',', '.', str_replace('.', '', $_REQUEST['desconto_outra_empresa']));
        } elseif ($trabalha_outra_empresa == 'nao') {
            $salario_outra_empresa = 0;
            $desconto_outra_empresa = 0;
        }
    }
//

    $tipo_vale = $_REQUEST['tipo_vale'];
    $num_cartao = $_REQUEST['num_cartao'];
    $num_cartao2 = $_REQUEST['num_cartao2'];
    $vale1 = $_REQUEST['vale1'];
    $vale2 = $_REQUEST['vale2'];
    $vale3 = $_REQUEST['vale3'];
    $vale4 = $_REQUEST['vale4'];
    $vale5 = $_REQUEST['vale5'];
    $vale6 = $_REQUEST['vale6'];
    $ad_noturno = $_REQUEST['ad_noturno'];
    $exame_data = $_REQUEST['data_exame'];
    $trabalho_data = $_REQUEST['data_ctps'];
    $reservista = removeAspas($_REQUEST['reservista']);
    $cabelos = $_REQUEST['cabelos'];
    $peso = $_REQUEST['peso'];
    $altura = $_REQUEST['altura'];
    $olhos = $_REQUEST['olhos'];
    $defeito = $_REQUEST['defeito'];
    $cipa = $_REQUEST['cipa'];
    $etnia = $_REQUEST['etnia'];
    $deficiencia = $_REQUEST['deficiencia'];
    $tipo_admissao = $_REQUEST['tipo_admissao'];
    $filho_1 = mysql_real_escape_string(trim($_REQUEST['filho_1']));
    $filho_2 = mysql_real_escape_string(trim($_REQUEST['filho_2']));
    $filho_3 = mysql_real_escape_string(trim($_REQUEST['filho_3']));
    $filho_4 = mysql_real_escape_string(trim($_REQUEST['filho_4']));
    $filho_5 = mysql_real_escape_string(trim($_REQUEST['filho_5']));
    $filho_6 = mysql_real_escape_string(trim($_REQUEST['filho_6']));
    $data_filho_1 = $_REQUEST['data_filho_1'];
    $data_filho_2 = $_REQUEST['data_filho_2'];
    $data_filho_3 = $_REQUEST['data_filho_3'];
    $data_filho_4 = $_REQUEST['data_filho_4'];
    $data_filho_5 = $_REQUEST['data_filho_5'];
    $data_filho_6 = $_REQUEST['data_filho_6'];
    $portador1 = $_REQUEST['portador1'];
    $portador2 = $_REQUEST['portador2'];
    $portador3 = $_REQUEST['portador3'];
    $portador4 = $_REQUEST['portador4'];
    $portador5 = $_REQUEST['portador5'];
    $portador6 = $_REQUEST['portador6'];
    $dep1_cur_fac_ou_tec = $_REQUEST['dep1_cur_fac_ou_tec'];
    $dep2_cur_fac_ou_tec = $_REQUEST['dep2_cur_fac_ou_tec'];
    $dep3_cur_fac_ou_tec = $_REQUEST['dep3_cur_fac_ou_tec'];
    $dep4_cur_fac_ou_tec = $_REQUEST['dep4_cur_fac_ou_tec'];
    $dep5_cur_fac_ou_tec = $_REQUEST['dep5_cur_fac_ou_tec'];
    $dep6_cur_fac_ou_tec = $_REQUEST['dep6_cur_fac_ou_tec'];




// Request referente ao sindicato do funcionário
    $SINDICATO = $_REQUEST['sindicato'];
    $ano_contribuicao = $_REQUEST['ano_contribuicao'];

    if (empty($_REQUEST['foto'])) {
        $foto = '0';
    } else {
        $foto = $_REQUEST['foto'];
    }
    if ($foto == "1") {
        $foto_banco = '1';
        $foto_up = '1';
    } else {
        $foto_banco = '0';
        $foto_up = '0';
    }


    $data_filho_1 = (!empty($data_filho_1)) ? "'" . ConverteData($data_filho_1) . "'" : 'null';
    $data_filho_2 = (!empty($data_filho_2)) ? "'" . ConverteData($data_filho_2) . "'" : 'null';
    $data_filho_3 = (!empty($data_filho_3)) ? "'" . ConverteData($data_filho_3) . "'" : 'null';
    $data_filho_4 = (!empty($data_filho_4)) ? "'" . ConverteData($data_filho_4) . "'" : 'null';
    $data_filho_5 = (!empty($data_filho_5)) ? "'" . ConverteData($data_filho_5) . "'" : 'null';
    $data_filho_6 = (!empty($data_filho_6)) ? "'" . ConverteData($data_filho_6) . "'" : 'null';
    $data_nasc = (!empty($data_nasc)) ? "'" . ConverteData($data_nasc) . "'" : 'null';
    $data_rg = (!empty($data_rg)) ? "'" . ConverteData($data_rg) . "'" : 'null';
    $data_escola = (!empty($data_escola)) ? "'" . ConverteData($data_escola) . "'" : 'null';
    $data_entrada = (!empty($data_entrada)) ? "'" . ConverteData($data_entrada) . "'" : 'null';
    $pis_data = (!empty($pis_data)) ? "'" . ConverteData($pis_data) . "'" : 'null';
    $exame_data = (!empty($exame_data)) ? "'" . ConverteData($exame_data) . "'" : 'null';
    $trabalho_data = (!empty($trabalho_data)) ? "'" . ConverteData($trabalho_data) . "'" : 'null';

    $data_nasc_pai = (!empty($data_nasc_pai)) ? "'" . ConverteData($data_nasc_pai) . "'" : 'null';
    $data_nasc_mae = (!empty($data_nasc_mae)) ? "'" . ConverteData($data_nasc_mae) . "'" : 'null';
    $data_nasc_conjuge = (!empty($data_nasc_conjuge)) ? "'" . ConverteData($data_nasc_conjuge) . "'" : 'null';
    $data_nasc_avo_h = (!empty($data_nasc_avo_h)) ? "'" . ConverteData($data_nasc_avo_h) . "'" : 'null';
    $data_nasc_avo_m = (!empty($data_nasc_avo_m)) ? "'" . ConverteData($data_nasc_avo_m) . "'" : 'null';
    $data_nasc_bisavo_h = (!empty($data_nasc_bisavo_h)) ? "'" . ConverteData($data_nasc_bisavo_h) . "'" : 'null';
    $data_nasc_bisavo_m = (!empty($data_nasc_bisavo_m)) ? "'" . ConverteData($data_nasc_bisavo_m) . "'" : 'null';
    $data_emissao = (!empty($data_emissao)) ? "'" . ConverteData($data_emissao) . "'" : 'null';
    $ano_chegada_pais = (!empty($ano_chegada_pais)) ? "'" . ConverteData($ano_chegada_pais) . "'" : 'null';

    $email = $_POST['email'];

// VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA CLT
//    $verificando_clt = mysql_query("SELECT nome,id_clt FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND status IN(10,200)");
    $verificando_clt = mysql_query("SELECT nome,id_clt FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND status < 60"); // alterado por pv a pedido do anderson
    $row_verificando_clt = mysql_fetch_assoc($verificando_clt);
    $verifica_clt = mysql_num_rows($verificando_clt);
///////VERIFICA SE A QUANTIDADE LIMITE DE TRABALHADORES FOI ATINGIDA NO INSTITUO LAGOS
    $qr_curso_max = mysql_query("SELECT * FROM curso WHERE id_curso = '$id_curso' ");
    $row_curso_max = mysql_fetch_assoc($qr_curso_max);

    $total_clt_curso = mysql_result(mysql_query("SELECT COUNT(id_curso) as total_curso FROM rh_clt  WHERE id_curso = '$id_curso' AND id_regiao = '$regiao' AND id_projeto = '$id_projeto'  AND status < 60;"), 0);
//    $total_clt_curso = mysql_result(mysql_query("SELECT COUNT(id_curso) as total_curso FROM rh_clt  WHERE id_curso = '$id_curso' AND id_regiao = '$regiao' AND id_projeto = '$id_projeto' and status IN(10,200)"), 0);


    if ($total_clt_curso == $row_curso_max['qnt_maxima'] and $row_curso_max['qnt_maxima'] != 0) {


        $menssagem = 'O número máximo de vagas para esta atividade (' . $row_curso_max['nome'] . ') foi atingido.<br><br>
			 Para mais informações, entrar em contato com o setor de TI.';

        $link_voltar = "../ver.php?regiao=$regiao&projeto=$id_projeto";
//        echo 'teste achei';
        include('../pagina_alerta.php');

        exit();
    }

    if (date("Ymt", mktime(0, 0, 0, $sqlFolhaFechada['mes'], '01', $sqlFolhaFechada['ano'])) >= str_replace('\'', '', str_replace('-', '', $data_entrada))) {
        print "
    <html>
        <head>
            <title>:: Intranet ::</title>
        </head>
        <body>
            EXISTE UMA FOLHA FECHADA NESTA DATA DE ENTRADA
        </body>
    </html>
    ";
        exit;
    } else if ($verifica_clt != 0) {
        print "
<html>
<head>
<title>:: Intranet ::</title>
</head>
<body>
ESTE PARTICIPANTE JA ESTÁ CADASTRADO: <b>$row_verificando_clt[id_clt]</b>
</body>
</html>
";
        exit;
    } else { // CASO O FUNCIONÁRIO NÃO ESTEJA CADASTRADO VAI RODAR O INSERT
        $result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$id_projeto'");
        $row_projeto = mysql_fetch_array($result_projeto);
        $data_cadastro = date('Y-m-d');
        $civil = explode('|', $civil);
        $estCivilId = $civil[0];
        $estCivilNome = $civil[1];
        $salario_outra_empresa = $_REQUEST['salario_outra_empresa'];
        $desconto_outra_empresa = $_REQUEST['desconto_outra_empresa'];
        //locacao,id_unidade,'$locacao_nome','$locacao_id',
        
        $ad_transferencia_tipo = $_REQUEST['ad_transferencia_tipo'];
        $ad_transferencia_valor = $_REQUEST['ad_transferencia_valor'];

        $query = "INSERT INTO rh_clt
(ad_transferencia_valor,ad_transferencia_tipo,id_projeto,id_setor,id_plano_saude,id_regiao,localpagamento,locacao,id_unidade,nome,nome_social,sexo,endereco, tipo_endereco,numero,complemento,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,conselho,titulo,zona,secao,pai,nacionalidade_pai,mae,nacionalidade_mae,
estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,status,data_entrada,data_saida,
campo1,campo2,campo3,data_exame,reservista,etnia,deficiencia,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo,status_admi,
desconto_inss,tipo_desconto_inss,trabalha_outra_empresa,salario_outra_empresa,desconto_outra_empresa,matricula,n_processo, contrato_medico, email,
data_nasc_pai, data_nasc_mae, data_nasc_conjuge, nome_conjuge, nome_avo_h,nome_avo_m, nome_bisavo_h, nome_bisavo_m, 
data_nasc_avo_h, data_nasc_avo_m, data_nasc_bisavo_h, data_nasc_bisavo_m,municipio_nasc,uf_nasc,data_emissao, verifica_orgao, vt_valor_diario, tipo_sanguineo, ano_contribuicao, dtChegadaPais, cod_pais_rais, tipo_contrato, prazoexp, 
id_estado_civil, id_municipio_nasc, id_municipio_end, id_pais_nasc, id_pais_nacionalidade, vale_refeicao, vale_alimentacao, pensao_alimenticia, curso_aprendiz, cnpj, inicio, fim) 
VALUES
('$ad_transferencia_valor','$ad_transferencia_tipo','$id_projeto','$id_setor','$id_plano_saude','$regiao','$localpagamento','$locacao_nome','$locacao_id','$nome','$nome_social','$sexo','$endereco','$tipo_endereco', '$numero','$complemento','$bairro','$cidade','$uf',
'$cep','$tel_fixo','$tel_cel','$tel_rec',$data_nasc,'$naturalidade','$nome_nacionalidade','$estCivilNome','$rg',
'$orgao',$data_rg,'$cpf','$conselho','$titulo','$zona','$secao','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
$data_escola,'$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
'10',$data_entrada,'0000-00-00','$campo1','$campo2','$campo3',$exame_data,'$reservista','$etnia','$deficiencia','$cabelos','$altura','$olhos',
'$peso','$defeito',
'$cipa','$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis',$pis_data,$trabalho_data,'$serie_ctps',
'$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nomebanco','$filhos','$observacoes','$impressos',
'$_COOKIE[logado]','$data_cadastro','$foto_banco','$vinculo','$rh_status','$horario','$SINDICATO','$rh_cbo','$tipo_admissao',
'$desconto_inss','$tipo_desconto_inss','$trabalha_outra_empresa','$salario_outra_empresa','$desconto_outra_empresa','$matricula','$n_processo','$contrato_medico', '$email',
$data_nasc_pai, $data_nasc_mae,$data_nasc_conjuge,'$conjuge', '$avo_h', '$avo_m', '$bisavo_h', '$bisavo_m', $data_nasc_avo_h,$data_nasc_avo_m, $data_nasc_bisavo_h, $data_nasc_bisavo_m,
    '$municipio_nasc','$uf_nasc', $data_emissao, '$verifica_orgao','$vt_valor_diario' ,'$tiposanguineo', '$ano_contribuicao', $ano_chegada_pais,'$cod_nacionalidade', '$tipo_contrato', '$prazoExp', '$estCivilId', '$cod_muni_nasc','$cod_cidade', '$cod_pais_nasc', '$cod_pais_nacionalidade','$vale_refeicao', '$vale_alimentacao', '$pensao_percentual', '$curso_aprendiz',
    '$cnpj', $data_inicio, $data_fim)";

//echo $query; die();
        mysql_query($query) or die("$mensagem_erro<br> $query <BR>" . mysql_error());

        $row_id_participante = mysql_insert_id();
        $row_id_clt = $row_id_participante;

        $insert_inss = "INSERT INTO rh_inss_outras_empresas (id_clt,salario,desconto,inicio,fim,status,data_cad, cnpj_outro_vinculo) VALUES ('$row_id_clt','$salario_outra_empresa','$desconto_outra_empresa',$data_inicio,$data_fim,1,NOW(),'$cnpj');";
       // echo $insert_inss;die();
        mysql_query($insert_inss);
    } // AQUI TERMINA DE INSERIR OS DADOS DO CLT

    $id_bolsista = $row_id_participante;




    if ($transporte == '1') {
        /* mysql_query("INSERT INTO rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
          id_tarifa5,id_tarifa6,cartao1,cartao2) VALUES
          ('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die("$mensagem_erro - 2.3<br><br>" . mysql_error()); */
    }

    if ($filho_1 == '' and $filho_2 == '' and $filho_3 == '' and $filho_4 == '' and $filho_5 == '' and $filho_6 == '') {

        $naa = '0';
    } else {
        mysql_query("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5,data6,nome6, ddir_pai, ddir_mae, ddir_conjuge, portador_def1, portador_def2, portador_def3, portador_def4, portador_def5, portador_def6, dep1_cur_fac_ou_tec, dep2_cur_fac_ou_tec, dep3_cur_fac_ou_tec, dep4_cur_fac_ou_tec, dep5_cur_fac_ou_tec, dep6_cur_fac_ou_tec, ddir_avo_h, ddir_avo_m, ddir_bisavo_m,ddir_bisavo_h) VALUES
          ('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome',$data_filho_1,'$filho_1',$data_filho_2,'$filho_2',$data_filho_3,'$filho_3',$data_filho_4,'$filho_4',$data_filho_5,'$filho_5',$data_filho_6,'$filho_6','$ddir_pai', '$ddir_mae', '$ddir_conjuge', '$portador1','$portador2', '$portador3', '$portador4', '$portador5', '$portador6','$dep1_cur_fac_ou_tec','$dep2_cur_fac_ou_tec','$dep3_cur_fac_ou_tec','$dep4_cur_fac_ou_tec','$dep5_cur_fac_ou_tec','$dep6_cur_fac_ou_tec' , '$ddir_avo_h', '$ddir_avo_m', '$ddir_bisavo_m', '$ddir_bisavo_h')") or die("$mensagem_erro 2.4<br><br>" . mysql_error());
        $naa = "2";
    }


// Log
    if ($_REQUEST['clonar']) {
        $qr_funcionario = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
        $funcionario = mysql_fetch_array($qr_funcionario);
        $ip = $_SERVER['REMOTE_ADDR'];
        $local_banco = "Edição de CLT";
        $acao_banco = "Clonando o CLT id_clt ={$_REQUEST['clonar']} - $nome. Novo id_clt=$row_id_participante";
        mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao) 
        VALUES ('$funcionario[0]', '$funcionario[id_regiao]', '$funcionario[tipo_usuario]', '$funcionario[grupo_usuario]', '$local_banco', NOW(), '$ip', '$acao_banco')") or die("Erro Inesperado<br/><br/>" . mysql_error());
    }

    $n_id_curso = sprintf("%04d", $id_curso);
    $n_regiao = sprintf("%04d", $regiao);
    $n_id_bolsista = sprintf("%04d", $row_id_participante);
    $cpf2 = str_replace(".", "", $cpf);
    $cpf2 = str_replace("-", "", $cpf2);

// GERANDO A SENHA ALEATÓRIA
    $target = "%%%%%%";
    $senha = "";
    $dig = "";
    $consoantes = "bcdfghjkmnpqrstvwxyz1234567890bcdfghjkmnpqrstvwxyz123456789";
    $vogais = "aeiou";
    $numeros = "123456789bcdfghjkmnpqrstvwxyzaeiou";
    $a = strlen($consoantes) - 1;
    $b = strlen($vogais) - 1;
    $c = strlen($numeros) - 1;
    for ($x = 0; $x <= strlen($target) - 1; $x++) {
        if (substr($target, $x, 1) == "@") {
            $rand = mt_rand(0, $c);
            $senha .= substr($numeros, $rand, 1);
        } elseif (substr($target, $x, 1) == "%") {
            $rand = mt_rand(0, $a);
            $senha .= substr($consoantes, $rand, 1);
        } elseif (substr($target, $x, 1) == "&") {
            $rand = mt_rand(0, $b);
            $senha .= substr($vogais, $rand, 1);
        } else {
            die("<b>Erro!</b><br><i>$target</i> é uma expressão inválida!<br><i>" . substr($target, $x, 1) . "</i> é um caractér inválido.<br>");
        }
    }
    $matricula = "$n_id_curso.$n_regiao.$n_id_bolsista-00";
    /* mysql_query("insert into tvsorrindo(id_clt,id_projeto,nome,cpf,matricula,senha,inicio) values
      ('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die("$mensagem_erro<br><Br>"); */

//FAZENDO O UPLOAD DA FOTO
    $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;
    if ($foto_up == '1') {
        if (!$arquivo) {
            $mensagem = "Não acesse esse arquivo diretamente!";
        }
// Imagem foi enviada, então a move para o diretório desejado
        else {
            $nome_arq = str_replace(" ", "_", $nome);
            $tipo_arquivo = ".gif";
            // Resolvendo o nome e para onde o arquivo será movido
            $diretorio = "../fotosclt/";
            $nome_tmp = $regiao . "_" . $id_projeto . "_" . $row_id_participante . $tipo_arquivo;
            $nome_arquivo = "$diretorio$nome_tmp";

            move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
        }
    }
    header("Location: ver_clt.php?reg=$regiao&clt=$row_id_participante&pro=$id_projeto&sucesso=cadastro");
    exit;
}
?>