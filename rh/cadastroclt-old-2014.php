<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../classes/regiao.php');

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$REG = new regiao();

$qr_nacionalidade = mysql_query("select * from cod_pais_rais");

if (empty($_REQUEST['update'])) {

    $id_regiao = $_REQUEST['regiao'];
    $projeto = $_REQUEST['projeto'];
    $idcurso = $_REQUEST['idcursos'];

    if ($id_regiao == '28') {
        $qr_maior = mysql_query("SELECT MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR'");
        $codigo = mysql_result($qr_maior, 0) + 1;
    } else {
        $resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30, MAX(campo3) FROM rh_clt WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto' AND campo3 != 'INSERIR' GROUP BY campo30 ASC");
        $row_maior = mysql_num_rows($resut_maior);
        $codigo = $row_maior + 1;
    }
    ?>

    <html>
        <head>
            <title>:: Intranet ::</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link rel="shortcut icon" href="../favicon.ico">
            <link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="consulta.js"></script>
            <script src="../js/ramon.js" type="text/javascript" language="javascript"></script>
            <link href="../js/jquery.ui.theme.css" rel="stylesheet" type="text/css" />
            <link href="../js/jquery.ui.datepicker.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="../js/jquery-1.3.2.js"></script>
            <script type="text/javascript" src="../js/jquery.ui.core.js"></script>
            <script type="text/javascript" src="../js/jquery.ui.widget.js"></script>
            <script type="text/javascript" src="../js/jquery.ui.datepicker.js"></script>
            <script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>
            <script type="text/javascript" src="../jquery/priceFormat.js"></script>
            <script type="text/javascript" src="../jquery/priceFormat.js"></script>
            <script type="text/javascript" src="../js/valida_documento.js"></script>
            <script type="text/javascript" src="../js/jquery.maskedinput.min.js"></script>
            <script type="text/javascript">
                $(function() {


                    $("#tel_cel").focus(function() {
                        $(this).mask("(99)9999-9999?9");
                    });
                    $(".celmask").focus(function() {
                        $(this).mask("(99) 9999-9999?9");
                    });

                    $("#data_entrada").datepicker({minDate: new Date(2009, 1 - 1, 1)});
                    $("#data_entrada").datepicker({showMonthAfterYear: true});


                    $("#uf_nasc_text").hide();

                    $("#nacionalidade").change(function() {
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

                    $('#cep').blur(function() {

                        var cep = $(this).val();
                        var img = $(this).next();

                        img.html('<img src="../img_menu_principal/loader_pequeno.gif" />');

                        $.ajax({
                            url: 'buscarendereco.php?cep=' + cep,
                            dataType: 'json',
                            success: function(resposta) {

                                if (resposta.endereco == '') {

                                    img.html('CEP não encontrado.')

                                } else {

                                    img.html('');

                                    $('#endereco').val(resposta.endereco);
                                    $('#bairro').val(resposta.bairro);
                                    $('#cidade').val(resposta.cidade);
                                    $('#uf').val(resposta.uf)

                                }
                                ;

                            }

                        });
                    });
                    $('#horario').change(function () {
                        if ($('#horario').val() != ''){
                            $('#horas_semanais').val($('#horario option:selected').attr('name'));                           
                        }
                    });







                    $('.formata_valor').priceFormat({
                        prefix: '',
                        centsSeparator: ',',
                        thousandsSeparator: '.'
                    });



                    var tipoVerifica = 0;
                    $("select[name*='banco']").change(function() {
                        function tipoPgCheque() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function() {
                                if ($(this).text() == "Cheque") {
                                    $(this).attr('selected', true);
                                } else {
                                    $(this).attr('disabled', true);
                                }

                            });
                        }

                        function tipoPgConta() {
                            $("select[name='tipopg']").find('option').attr('disabled', false).attr('selected', false);
                            $("select[name='tipopg']").find('option').each(function() {
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

                    $("input[type*='button'][name*='Submit']").click(function() {
                        var indice = new Array();
                        if (tipoVerifica == 3) {
                            if ($("input[name*='conta']").val() == '') {
                                indice.push("Conta");
                            }
                            if ($("input[name*='agencia']").val() == '') {
                                indice.push("Agencia");
                            }
                            indiceRadio = 0;
                            $("input[name*='radio_tipo_conta']").each(function() {
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
                            $("input[name*='radio_tipo_conta']").each(function() {
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

                });
                $(document).ready(function() {

                    //    $(".celmask").focusout(function(){
                    //        var phone, element; 
                    //        element = $(this); 
                    //        element.unmask(); 
                    //        phone = element.val().replace(/\D/g, ''); 
                    //        if (phone.length > 10) { 
                    //            element.mask("(99) 99999-999?9"); 
                    //        } else { 
                    //            element.mask("(99) 9999-9999?9"); 
                    //        } 
                    //    });
                });
            </script>
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
                            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form1" enctype="multipart/form-data" onSubmit="return validaForm()">
                                <table cellpadding="0" cellspacing="1" class="secao">


                                    <tr>
                                        <td colspan="2" class="secao_pai" style="border-top:1px solid #777;">DADOS DO PROJETO</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">C&oacute;digo:</td>
                                        <td><?= $codigo ?> <input name="codigo" size="3" type="hidden" id="codigo" value="<?= $codigo ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td width="25%" class="secao">Projeto:</td>
                                        <td width="75%">   
    <?php
    if (!empty($projeto)) {
        $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$projeto'");
        echo $projeto . ' - ' . mysql_result($qr_projeto, 0);
    } else {
        ?>

                                                <select name="projetos" id="projetos" onChange="location.href = this.value;">
                                                    <option selected disabled>--Selecione--</option>
                                                <?php $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$id_regiao' AND status_reg = '1'");
                                                while ($row_projeto = mysql_fetch_array($qr_projeto)) {
                                                    ?>
                                                        <option value="cadastroclt.php?regiao=<?= $id_regiao ?>&projeto=<?= $row_projeto['id_projeto'] ?>"><?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'] ?></option>

                                                    <?php } ?>
                                                </select>

                                                <?php } ?>
                                        </td>
                                    </tr> 
                                    <tr style="display:none;">
                                        <td class="secao">Vínculo:</td>
                                        <td>
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
                                        <td><label><input name="contratacao" type="radio" class="reset" id="contratacao" value="2" checked="checked"> CLT</label></td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Curso:</td>
                                        <td>
                                            <?php
                                            $qr_curso = mysql_query("SELECT * FROM curso WHERE id_regiao = '$id_regiao' AND campo3 = '$projeto' AND tipo = '2' ORDER BY nome ASC");
                                            $verifica_curso = mysql_num_rows($qr_curso);
                                            if (!empty($verifica_curso)) {

                                                if (!empty($idcurso)) {
                                                    $var_disabled = 'display:none;';
                                                }

                                                print "<select name='idcursos' id='idcursos' onChange='location.href=this.value;'>
       				    <option style='margin-bottom:3px;$var_disabled' value='' selected disabled>--Selecione--</option>";

                                                while ($row_curso = mysql_fetch_array($qr_curso)) {

                                                    $margem++;

                                                    if ($margem != $verifica_curso) {
                                                        $var_margem = ' style="margin-bottom:3px;"';
                                                    } else {
                                                        $var_margem = NULL;
                                                    }

                                                    $salario = number_format($row_curso['salario'], 2, ',', '.');

                                                    if ($row_curso['0'] == $idcurso) {
                                                        print "<option value='$row_curso[0]' selected$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
                                                    } else {
                                                        print "<option value='cadastroclt.php?regiao=$id_regiao&projeto=$projeto&idcursos=$row_curso[0]'$var_margem>$row_curso[0] - $row_curso[campo2] (Valor: $salario)</option>";
                                                    }
                                                }

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
                                        <td><input type="checkbox" name="contrato_medico" value="1"/> Necessita de contrato para médicos?</td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Horário:</td>
                                        <td>
                                            <?php
                                            $qr_horarios = mysql_query("SELECT * FROM rh_horarios WHERE funcao = '$idcurso' AND id_regiao = '$id_regiao'");
                                            $verifica_horario = mysql_num_rows($qr_horarios);
                                            if (!empty($verifica_horario)) {
                                                $idHorario = '';
                                                print '<select name="horario" id="horario">
				   <option style="margin-bottom:3px;" value="" selected disabled>--Selecione--</option>';

                                                while ($row_horarios = mysql_fetch_array($qr_horarios)) {

                                                    $margem2++;

                                                    if ($margem2 != $verifica_horario) {
                                                        $var_margem2 = ' style="margin-bottom:3px;"';
                                                    } else {
                                                        $var_margem2 = NULL;
                                                    }

                                                    print "<option name = '$row_horarios[horas_semanais]' value='$row_horarios[0]'$var_margem2>$row_horarios[0] - $row_horarios[nome] ( $row_horarios[entrada_1] - $row_horarios[saida_1] - $row_horarios[entrada_2] - $row_horarios[saida_2] )</option>";
                                                    $idHorario = $row_horarios['id_horario'];
                                                }

                                                print '</select>';
                                            } else {

                                                if (empty($projeto)) {
                                                    print 'Selecione um Projeto';
                                                } elseif (empty($idcurso) and !empty($verifica_curso)) {
                                                    print 'Selecione um Curso';
                                                } else {
                                                    print 'Nenhum Horário Cadastrado para o Curso';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Horas Semanais:</td>
                                        <td>
                                            <input type="text" id="horas_semanais" name ="horas_semanais" value="" disabled="disabled" size="15">&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="<?php echo 'rh_horarios_alterar.php?regiao=' . $id_regiao . '&horario=' . $idHorario ?>" target="_blank"> EDITAR</a>   
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Unidade:</td>
                                        <td>
    <?php
    $qr_unidade = mysql_query("SELECT * FROM unidade WHERE id_regiao = '$id_regiao' AND campo1 = '$projeto' ORDER BY unidade ASC");
    $verifica_unidade = mysql_num_rows($qr_unidade);
    if (!empty($verifica_unidade)) {
        print '<select name="locacao" id="locacao">
       				    <option style="margin-bottom:3px;" value="" selected disabled>--Selecione--</option>';

        while ($row_unidade = mysql_fetch_array($qr_unidade)) {

            $margem3++;

            if ($margem3 != $verifica_unidade) {
                $var_margem3 = ' style="margin-bottom:3px;"';
            } else {
                $var_margem3 = NULL;
            }
            print "<option value='$row_unidade[unidade]'$var_margem3>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
        }

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
                                </table>
                                <table cellpadding="0" cellspacing="1" class="secao">
                                    <tr>
                                        <td class="secao_pai" colspan="6">DADOS PESSOAIS</td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td colspan="5">
                                            <input name="nome" type="text" id="nome" size="75" onChange="this.value = this.value.toUpperCase();" onKeyPress="return(verificanome(this, event));"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Data de Nascimento:</td>
                                        <td>
                                            <input name="data_nasc" type="text" id="data_nasc" size="15" maxlength="10"
                                                   onkeyup="mascara_data(this);"/>
                                        </td>
                                        <td class="secao">Município de Nascimento:</td>
                                        <td>
                                            <input name="municipio_nasc" type="text" id="municipio_nasc" size="15"  />
                                        </td>
                                        <td class="secao">UF de Nascimento:</td>
                                        <td>
                                            <select name="uf_nasc_select" id="uf_nasc_select" >
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select>
                                            <input name="uf_nasc_text" type="text" id="uf_nasc_text" size="16"  onchange="this.value = this.value.toUpperCase()"/>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td class="secao" width="16%">Estado Civil:</td>
                                        <td width="16%">
                                            <select name="civil" id="civil">
                                                <option value="Solteiro">Solteiro</option>
                                                <option value="Casado">Casado</option>
                                                <option value="Vi&uacute;vo">Vi&uacute;vo</option>
                                                <option value="Sep. Judicialmente">Sep. Judicialmente</option>
                                                <option value="Divorciado">Divorciado</option>
                                            </select>
                                        </td>
                                        <td class="secao" width="16%">Sexo:</td>
                                        <td width="16%">
                                            <label><input name="sexo" type="radio" class="reset" value="M" checked /> Masculino</label><br>    		
                                            <label><input name="sexo" type="radio" class="reset" value="F" /> Feminino</label>
                                        </td>
                                        <td class="secao" width="16%">Nacionalidade:</td>
                                        <td width="16%">
                                        <!--<input name="nacionalidade" type="text" id="nacionalidade" size="15" 
                                                   onchange="this.value=this.value.toUpperCase()"/>-->
                                            <select name="nacionalidade" id="nacionalidade">
    <?php
    while ($row_nacionalidade = mysql_fetch_assoc($qr_nacionalidade)) {
        echo '<option value="' . $row_nacionalidade['codigo'] . '">' . $row_nacionalidade['nome'] . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Ano de chegada no país:</td>
                                        <td colspan="5">
                                            <input name="ano_chegada_pais" type="text" id="ano_chegada_pais" size="2" maxlength="2"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">CEP:</td>
                                        <td colspan="5"><input name="cep" type="text" id="cep" maxlength="9" onKeyPress="formatar('#####-###', this)"> <span></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Endereço:</td>
                                        <td>
                                            <input name="endereco" type="text" id="endereco" size="32" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">N&uacute;mero:</td>
                                        <td>
                                            <input name="numero" type="text" id="numero" size="10" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Complemento:</td>
                                        <td>
                                            <input name="complemento" type="text" id="complemento" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Bairro:</td>
                                        <td>
                                            <input name="bairro" type="text" id="bairro" size="16" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Cidade:</td>
                                        <td><input name="cidade" type="text" id="cidade" size="16"  onchange="this.value = this.value.toUpperCase()"/></td>
                                        <td class="secao">UF:</td>
                                        <td>   <select name="uf" id="uf" >
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Naturalidade:</td>
                                        <td>
                                            <input name="naturalidade" type="text" id="naturalidade" size="15"  
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Estuda Atualmente?</td>
                                        <td>
                                            <label><input name="estuda" type="radio" class="reset" value="sim" checked="checked" /> SIM</label>
                                            <label><input name="estuda" type="radio" class="reset" value="não" /> NÃO</label>
                                        </td>
                                        <td class="secao">Término em:</td>
                                        <td>
                                            <input name="data_escola" type="text" id="data_escola" size="15" maxlength="10"
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
        echo '<option value="' . $escolaridade['id'] . '">' . $escolaridade['cod'] . ' - ' . $escolaridade['nome'] . '</option>';
    }
    ?> 
                                            </select>
                                        </td>
                                        <td class="secao">Curso:</td>
                                        <td>
                                            <input name="curso" type="text" id="zona" size="16" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">Instituição:</td>
                                        <td>
                                            <input name="instituicao" type="text" id="titulo" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Telefone Fixo:</td>
                                        <td><input name="tel_fixo" type="text" id="tel_fixo" size="14" 
                                                   onKeyPress="return(TelefoneFormat(this, event))"
                                                   onkeyup="pula(13, this.id, tel_cel.id)" />
                                        </td>
                                        <td class="secao">Celular:</td>
                                        <td><input name="tel_cel " class="celmask" type="text" id="tel_cel" size="16" />
                                        </td>
                                        <td class="secao">Recado:</td>
                                        <td><input name="tel_rec" type="text" id="tel_rec" size="15" 
                                                   onKeyPress="return(TelefoneFormat(this, event))" 
                                                   onkeyup="pula(13, this.id, pai.id)" />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">E-mail:</td>
                                        <td colspan="5">
                                            <input name="email" type="text" id="email" size="35" value='<?= $row['email'] ?>' />
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
        ?>
                                                    <option value="<?php echo $i["nome"] ?>"><?php echo $i["nome"] ?></option>
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
                                        <td colspan="4">
                                            <input name="pai" type="text" id="pai" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_pai" id="ddir_pai" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Nacionalidade Pai:</td>
                                        <td>
                                            <input name="nacionalidade_pai" type="text" id="nacionalidade_pai" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()"/>	

                                        </td>
                                        <td class="secao">Data de nascimento do Pai:</td>
                                        <td><input type="text" name="data_nasc_pai" id="data_nasc_pai"</td>
                                    </tr>



                                    <tr>
                                        <td class="secao">Filiação - Mãe:</td>
                                        <td colspan="4">
                                            <input name="mae" type="text" id="mae" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_mae" id="ddir_mae" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nacionalidade Mãe:</td>
                                        <td>
                                            <input name="nacionalidade_mae" type="text" id="nacionalidade_mae" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()"/>	
                                        </td>
                                        <td class="secao">Data de nascimento da Mãe:</td>
                                        <td><input type="text" name="data_nasc_mae" id="data_nasc_mae"/> </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Avô:</td>
                                        <td colspan="4">
                                            <input name="avo_h" type="text" id="avo_h" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_avo_h" id="ddir_avo_h" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>          
                                        <td class="secao" >Data de nascimento do Avô:</td>
                                        <td colspan="3"><input type="text" name="data_nasc_avo_h" id="data_nasc_avo_h"/> </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Avó:</td>
                                        <td colspan="4">
                                            <input name="avo_m" type="text" id="avo_m" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_avo_m" id="ddir_avo_m" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>            
                                        <td class="secao">Data de nascimento da Avó:</td>
                                        <td colspan="3"><input type="text" name="data_nasc_avo_m" id="data_nasc_avo_m"/> </td>
                                    </tr>


                                    <tr>
                                        <td class="secao">Bisavô:</td>
                                        <td colspan="4">
                                            <input name="bisavo_h" type="text" id="bisavo_h" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_bisavo_h" id="ddir_bisavo_h" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>            
                                        <td class="secao">Data de nascimento do Bisavô:</td>
                                        <td colspan="3"><input type="text" name="data_nasc_bisavo_h" id="data_nasc_bisavo_h"/> </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Bisavó:</td>
                                        <td colspan="4">
                                            <input name="bisavo_m" type="text" id="bisavo_m" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_bisavo_m" id="ddir_bisavo_m" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>              
                                        <td class="secao">Data de nascimento da Bisavó:</td>
                                        <td colspan="3"><input type="text" name="data_nasc_bisavo_m" id="data_nasc_bisavo_m" /> </td>
                                    </tr>

                                    <tr>
                                        <td class="secao">Conjuge:</td>
                                        <td colspan="3">
                                            <input name="conjuge" type="text" id="conjuge" size="45" 
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                            <input type="checkbox" name="ddir_conjuge" id="ddir_conjuge" value="1"/> Dependente de IRRF
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Data de nascimento do Conjuge:</td>
                                        <td colspan="3">
                                            <input name="data_nasc_conjuge" type="text" id="data_nasc_conjuge" size="15" 
                                                   onchange="this.value = this.value.toUpperCase()"/>	
                                        </td>
                                    </tr> 
                                    <tr>
                                        <td class="secao">Número de Filhos:</td>
                                        <td colspan="3">
                                            <input name="filhos" type="text" id="filhos" size="2" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_1" type="text" id="filho_1" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_1" type="text" size="12" maxlength="10" id="data_filho_1"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, filho_2.id)"
                                                   onchange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br>
                                            <input name="portador1" id="portador1" value="1"  type="checkbox"/> Portador de deficiência
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_2" type="text" id="filho_2" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td> 
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_2" type="text" size="12" maxlength="10" id="data_filho_2"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, filho_3.id)"
                                                   onchange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br>
                                            <input name="portador2" id="portador2" value="1"  type="checkbox"/> Portador de deficiência
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_3" type="text" id="filho_3" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_3" type="text" size="12" maxlength="10" id="data_filho_3"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, filho_4.id)"
                                                   onchange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br>
                                            <input name="portador3" id="portador3" value="1"  type="checkbox"/> Portador de deficiência

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_4" type="text" id="filho_4" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_4" type="text" size="12" maxlength="10" id="data_filho_4"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, filho_5.id)"
                                                   onchange="this.value = this.value.toUpperCase()" class="data_filho">
                                            <br>
                                            <input name="portador4" id="portador4" value="1"  type="checkbox"/> Portador de deficiência
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_5" type="text" id="filho_5" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_5" type="text" size="12" maxlength="10" id="data_filho_5"
                                                   onkeyup="mascara_data(this);"
                                                   onchange="this.value = this.value.toUpperCase()" class="data_filho"/>
                                            <br>
                                            <input name="portador5" id="portador5" value="1"  type="checkbox"/> Portador de deficiência
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nome:</td>
                                        <td><input name="filho_6" type="text" id="filho_6" size="50" 
                                                   onchange="this.value = this.value.toUpperCase()" class="nome_filho"/>
                                        </td>
                                        <td class="secao">Nascimento:</td>
                                        <td><input name="data_filho_6" type="text" size="12" maxlength="10" id="data_filho_6"
                                                   onkeyup="mascara_data(this);"
                                                   onchange="this.value = this.value.toUpperCase()"  class="data_filho"/>
                                            <br>
                                            <input name="portador6" id="portador6" value="1"  type="checkbox"/> Portador de deficiência
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
                                                    print "<option>$row_cabelos[nome]</option>";
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
        print "<option>$row_olhos[nome]</option>";
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Peso:</td>
                                        <td>
                                            <input name="peso" type="text" id="peso" size="5" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Altura:</td>
                                        <td>
                                            <input name="altura" type="text" id="altura" size="5" />
                                        </td>
                                        <td class="secao">Etnia:</td>
                                        <td>
                                            <select name="etnia">
    <?php
    $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on' ORDER BY id DESC");
    while ($etnia = mysql_fetch_assoc($qr_etnias)) {
        echo '<option value="' . $etnia['id'] . '">' . $etnia['nome'] . '</option>';
    }
    ?>
                                            </select>
                                        </td>
                                        <td class="secao">Marcas ou Cicatriz:</td>
                                        <td>
                                            <input name="defeito" type="text" id="defeito" size="18" 
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
                                            <input name="rg" type="text" id="rg" size="13" maxlength="14"
                                                   onkeypress="formatar('##.###.###-###', this)"
                                                   onkeyup="pula(14, this.id, orgao.id)">
                                        </td>
                                        <td class="secao">Orgão Expedidor:</td>
                                        <td>
                                            <input name="orgao" type="text" id="orgao" size="8"
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td class="secao">UF:</td>
                                        <td>
                                            <select name="uf_rg" id="uf_rg" >
                                                <option value=""></option>
    <?php
    $qr_uf = mysql_query("SELECT * FROM uf");
    while ($row_uf = mysql_fetch_assoc($qr_uf)) {
        echo '<option value="' . $row_uf['uf_sigla'] . '">' . $row_uf['uf_sigla'] . '</option>';
    }
    ?>    
                                            </select></td>
                                        <td class="secao">Data Expedição:</td>
                                        <td>
                                            <input name="data_rg" type="text" size="12" maxlength="10" id="data_rg" 
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, cpf.id)" />
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="secao">CPF:</td>
                                        <td>
                                            <input name="cpf" type="text" id="cpf" size="17" maxlength="14"
                                                   onkeypress="formatar('###.###.###-##', this)" 
                                                   onkeyup="pula(14, this.id, reservista.id)"/>
                                        </td>
                                        <td class="secao">&Oacute;rg&atilde;o Regulamentador:</td>
                                        <td colspan="3">
                                            <input name="conselho" type="text" id="conselho" size="17" /><br><br>
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
                                            <input name="trabalho" type="text" id="trabalho" size="15" />
                                        </td>
                                        <td class="secao">Série:</td>
                                        <td>
                                            <input name="serie_ctps" type="text" id="serie_ctps" size="10" />		  
                                        </td>
                                        <td class="secao">UF:</td>
                                        <td>
                                            <select name="uf_ctps" id="uf_ctps" >
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
                                            <input name="data_ctps" type="text" size="12" maxlength="10" id="data_ctps"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, titulo2.id)" />    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Nº Título de Eleitor:</td>
                                        <td>
                                            <input name="titulo" type="text" id="titulo2" size="10" />
                                        </td>
                                        <td class="secao">Zona:</td>
                                        <td colspan="3">
                                            <input name="zona" type="text" id="zona2" size="3"/>
                                        </td>
                                        <td class="secao">Seção:</td>
                                        <td>
                                            <input name="secao" type="text" id="secao" size="3" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">PIS:</td>
                                        <td>
                                            <input name="pis" type="text" id="pis" size="12">
                                        </td>
                                        <td class="secao">Data Pis:</td>
                                        <td colspan="3">
                                            <input name="data_pis" type="text" size="12" maxlength="10" id="data_pis"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, fgts.id)" />
                                        </td>
                                        <td class="secao">FGTS:</td>
                                        <td>
                                            <input name="fgts" type="text" id="fgts" size="10" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Certificado de Reservista:</td>
                                        <td colspan="7">
                                            <input name="reservista" type="text" id="reservista" size="18" />
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
                                    <tr>
                                        <td class="secao">Seguro, Apólice:</td>
                                        <td>
                                            <select name="apolice" id="apolice">
                                                <option value="0">Não Possui</option>
    <?php
    $result_ap = mysql_query("SELECT * FROM apolice WHERE id_regiao = $row[regiao]");
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
                                            <label><input name="tipo_desconto_inss" type="radio" class="reset" onClick="document.getElementById('valor_desconto_inss').style.display = 'none';" value="isento" checked>Suspenção de Recolhimento</label><br>
                                            <label><input name="tipo_desconto_inss" type="radio" class="reset" onClick="document.getElementById('valor_desconto_inss').style.display = '';" value="parcial">Parcial</label>
                                            <input name="valor_desconto_inss" type="text" id="valor_desconto_inss" size="12" style="display:none;" class="formata_valor">
                                        </td>
                                        <td class="secao">Trabalha em outra empresa?</td>
                                        <td>
                                            <label><input name="trabalha_outra_empresa" type="radio" class="reset" onClick="document.getElementById('outra_empresa').style.display = '';" value="sim">Sim</label>
                                            <label><input name="trabalha_outra_empresa" type="radio" class="reset" onClick="document.getElementById('outra_empresa').style.display = 'none';" value="nao" checked>Não</label>
                                        </td>
                                    </tr>
                                    <tr id="outra_empresa" style="display:none;">
                                        <td class="secao">Salário da outra empresa:</td>
                                        <td>
                                            <input name="salario_outra_empresa" type="text" size="12" class="formata_valor">
                                        </td>
                                        <td class="secao">Desconto da outra empresa:</td>
                                        <td>
                                            <input name="desconto_outra_empresa" type="text" size="12" class="formata_valor">
                                        </td>
                                    </tr>
                                </table>

                                <table cellpadding="0" cellspacing="1" class="secao" id="tablevale" style="display:none;">
                                    <tr>
                                        <td colspan="6" class="secao_pai">VALE TRANSPORTE</td>
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
                                            <label><input name="radio_sindicato" type="radio" class="reset" onClick="document.getElementById('trsindicato').style.display = '';" value="sim" >Sim</label>
                                            <label><input name='radio_sindicato' type='radio' class="reset" onClick="document.getElementById('trsindicato').style.display = 'none';" value='nao' checked='checked' >Não</label>
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
                                            <label><input name="radio_contribuicao" type="radio" class="reset" onClick="document.getElementById('trcontribuicao').style.display = '';" value="sim" >Sim</label>
                                            <label><input name='radio_contribuicao' type='radio' class="reset" onClick="document.getElementById('trcontribuicao').style.display = 'none';" value='nao' checked='checked' >Não</label>
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
                                        <td class="secao">Data de Entrada:</td>
                                        <td>
                                            <input name="data_entrada" type="text" size="12" maxlength="10" id="data_entrada"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, data_exame.id)" />
                                        </td>
                                        <td class="secao">Data do Exame Admissional:</td>
                                        <td>
                                            <input name="data_exame" type="text" size="12" maxlength="10" id="data_exame"
                                                   onkeyup="mascara_data(this);
                    pula(10, this.id, localpagamento.id)" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Local de Pagamento:</td>
                                        <td width="20%">
                                            <input name="localpagamento" type="text" id="localpagamento" size="25"  
                                                   onchange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td width="19%" class="secao">Tipo de admiss&atilde;o</td>
                                        <td width="38%">
                                            <select name="tipo_admissao" id="tipo_admissao">
                                                <option value="">Selecione um tipo de admissão</option>
    <?php
    $tipo_admissoes = array(
        10 => 'Primeiro emprego',
        20 => 'Reemprego',
        25 => 'Contrato por prazo determinado',
        35 => 'Reintegração',
        70 => 'Trnsferência da entrada'
    );
    foreach ($tipo_admissoes as $num => $tipo) {
        ?>
                                                    <option value="<?= $num ?>"><?= $tipo ?></option>
    <?php } ?>
                                            </select></td>
                                    </tr>
                                    <tr>
                                        <td class="secao">Tipo de Pagamento:</td>
                                        <td colspan="3">
                                            <select name="tipopg" id="tipopg">
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
                                        <td class="secao">Observações:</td>
                                        <td colspan="3">
                                            <textarea name="observacoes" id="observacoes" cols="55" rows="4"  onchange="this.value = this.value.toUpperCase()"></textarea></td>
                                    </tr>
                                </table>
                                <div id="finalizacao">
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
                                </div>
                                <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                                <div align="center">
                                    <input type="submit" name="Submit" value="CADASTRAR" class="botao" /></div> 
                                <input type="hidden" name="regiao" value="<?= $id_regiao ?>"/>
                                <input type="hidden" name="projeto" value="<?= $projeto ?>" />
                                <input type="hidden" name="user" value="<?= $id_user ?>" />
                                <input type="hidden" name="update" value="1" />
                            </form>
                        </td>
                    </tr>
                </table>
            </div>
            <script language="javascript" type="text/javascript">







                function validaForm() {
                    d = document.form1;



                    if (d.idcursos.value == '') {
                        alert('Selecione um curso!');
                        d.idcursos.focus();
                        return false;
                    }
                    if (d.horario.value == '') {
                        alert('Selecione um horário!');
                        d.horario.focus();
                        return false;
                    }
                    if (d.locacao.value == '') {
                        alert('Selecione uma unidade!');
                        d.locacao.focus();
                        return false;
                    }
                    if (d.nome.value == '') {
                        alert('O campo nome deve ser preenchido!');
                        d.nome.focus();
                        return false;
                    }
                    if (d.data_nasc.value == '') {
                        alert('O campo data de nascimento deve ser preenchido!');
                        d.data_nasc.focus();
                        return false;
                    }
                    if (d.cep.value == '') {
                        alert('O campo CEP deve ser preenchido!');
                        d.cep.focus();
                        return false;
                    }
                    if (d.endereco.value == '') {
                        alert('O campo endereço deve ser preenchido!');
                        d.endereco.focus();
                        return false;
                    }
                    if (d.bairro.value == '') {
                        alert('O campo bairro deve ser preenchido!');
                        d.bairro.focus();
                        return false;
                    }
                    if (d.cidade.value == '') {
                        alert('O campo cidade deve ser preenchido!');
                        d.cidade.focus();
                        return false;
                    }
                    if (d.uf.value == '') {
                        alert('O campo UF deve ser preenchido!');
                        d.uf.focus();
                        return false;
                    }
                    if (d.rg.value == '') {
                        alert('O campo RG deve ser preenchido!');
                        d.rg.focus();
                        return false;
                    }


                    if (d.cpf.value == '') {
                        alert('O campo CPF deve ser preenchido!');
                        d.cpf.focus();
                        return false;
                    }


                    var cpf = d.cpf.value.replace('.', '').replace('.', '').replace('-', '');

                    if (!VerificaCPF(cpf)) {

                        alert('CPF inválido!');
                        d.cpf.focus();
                        return false;
                    }
                    ;

                    /*
                     if(VerificaCPF(cpf.value)) {
                     alert('CPF inválido!');
                     d.cpf.focus();
                     return false;
                     }
                     */

                    if (d.trabalho.value == '') {
                        alert('O campo nº carteira de trabalho deve ser preenchido!');
                        d.trabalho.focus();
                        return false;
                    }
                    if (d.serie_ctps.value == '') {
                        alert('O campo serie deve ser preenchido!');
                        d.serie_ctps.focus();
                        return false;
                    }
                    if (d.uf_ctps.value == '') {
                        alert('O campo UF deve ser preenchido!');
                        d.uf_ctps.focus();
                        return false;
                    }
                    /*
                     if(d.pis.value == '') {
                     alert('O campo PIS deve ser preenchido!');
                     d.pis.focus();
                     return false;
                     }
                     */

                    if (d.pis.value == '') {
                        alert('O campo de PIS foi deixado em branco, mas precisa ser preenchido no futuro');
                    }

                    if (ChecaPIS(d.pis.value) == false) {
                        alert('PIS inválido');
                        d.pis.focus();
                        return false;
                    }


                    if (d.localpagamento.value == '') {
                        alert('O campo local de pagamento deve ser preenchido!');
                        d.localpagamento.focus();
                        return false;
                    }
                    if (d.tipo_admissao.value == '') {
                        alert('O campo tipo de admissão deve ser preenchido!');
                        d.tipo_admissao.focus();
                        return false;
                    }

                    if (d.tipo_admissao.value == '') {
                        alert('O campo tipo de admissão deve ser preenchido!');
                        d.tipo_admissao.focus();
                        return false;
                    }




                    return true;
                }

                $(function() {
                    $('#data_nasc, #data_nasc_conjuge, #data_nasc_pai, #data_nasc_mae, #data_escola, #data_filho_1,#data_filho_2, #data_filho_3,#data_filho_4,#data_filho_5, #data_filho_6,#data_rg,\n\
            #data_ctps, #data_pis, #data_entrada,#data_exame, #data_nasc_avo_h, #data_nasc_avo_m, #data_nasc_bisavo_h, #data_nasc_bisavo_m, #data_emissao').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "1950:<?php echo date('Y') ?>"
                    });


                    $('#portador1,#portador2,#portador3, #portador4, #portador5, #portador6').change(function() {

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

                    $('#ddir_pai').change(function() {

                        var linha = $(this).parent().parent();
                        var pai = linha.find('#pai')

                        if (pai.val() == '') {
                            alert('Preencha o nome do pai.');
                            pai.focus();
                            $(this).attr('checked', false);
                        }


                    });


                    $('#ddir_mae').change(function() {

                        var linha = $(this).parent().parent();
                        var mae = linha.find('#mae')

                        if (mae.val() == '') {
                            alert('Preencha o nome da mãe.');
                            mae.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_conjuge').change(function() {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#conjuge')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do conjuge.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_avo_h').change(function() {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#avo_h')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Avô.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_avo_m').change(function() {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#avo_m')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Avó.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });

                    $('#ddir_bisavo_h').change(function() {

                        var linha = $(this).parent().parent();
                        var conjuge = linha.find('#bisavo_h')

                        if (conjuge.val() == '') {
                            alert('Preencha o nome do Bisavô.');
                            conjuge.focus();
                            $(this).attr('checked', false);
                        }
                    });
                    $('#ddir_bisavo_m').change(function() {

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
    $regiao = $_REQUEST['regiao'];
    $id_projeto = $_REQUEST['projeto'];
// Dados de Contratação
    $vinculo = $_REQUEST['vinculo'];
    $tipo_contratacao = $_REQUEST['contratacao'];
    $id_curso = $_REQUEST['idcursos'];
    $locacao = $_REQUEST['locacao'];
    $horario = $_REQUEST['horario'];

// Dados Pessoais
    $nome = mysql_real_escape_string($_REQUEST['nome']);
    $sexo = $_REQUEST['sexo'];
    $endereco = mysql_real_escape_string($_REQUEST['endereco']);
    $numero = $_REQUEST['numero'];
    $complemento = $_REQUEST['complemento'];
    $bairro = mysql_real_escape_string($_REQUEST['bairro']);
    $cidade = mysql_real_escape_string($_REQUEST['cidade']);
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
    $rg = $_REQUEST['rg'];
    $uf_rg = $_REQUEST['uf_rg'];
    $secao = $_REQUEST['secao'];
    $data_rg = $_REQUEST['data_rg'];
    $cpf = $_REQUEST['cpf'];
    $conselho = $_REQUEST['conselho'];
    $titulo = $_REQUEST['titulo'];
    $zona = $_REQUEST['zona'];
    $orgao = $_REQUEST['orgao'];

// Mais
    $pai = $_REQUEST['pai'];
    $mae = $_REQUEST['mae'];
    $conjuge = $_REQUEST['conjuge'];
    $avo_h = $_REQUEST['avo_h'];
    $avo_m = $_REQUEST['avo_m'];
    $bisavo_h = $_REQUEST['bisavo_h'];
    $bisavo_m = $_REQUEST['bisavo_m'];
    $nacionalidade_pai = $_REQUEST['nacionalidade_pai'];
    $nacionalidade_mae = $_REQUEST['nacionalidade_mae'];

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
    $curso = $_REQUEST['curso'];

// Dados Financeiros
    $data_entrada = $_REQUEST['data_entrada'];

    $banco = $_REQUEST['banco'];
    $agencia = $_REQUEST['agencia'];
    $conta = $_REQUEST['conta'];
    $nomebanco = $_REQUEST['nomebanco'];
    $tipoDeConta = $_REQUEST['radio_tipo_conta'];
    $localpagamento = $_REQUEST['localpagamento'];
    $apolice = $_REQUEST['apolice'];
    $campo1 = $_REQUEST['trabalho'];
    $campo2 = $_REQUEST['dependente'];
    $campo3 = $_REQUEST['codigo'];
    $data_cadastro = date('Y-m-d');
    $pis = $_REQUEST['pis'];
    $fgts = $_REQUEST['fgts'];
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

    $contrato_medico = $_POST['contrato_medico'];

//Inicio Verificador CPF
    $qrCpf = mysql_query("SELECT COUNT(id_clt) AS total FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto'  AND id_regiao = '$id_regiao' ");
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
    $reservista = $_REQUEST['reservista'];
    $cabelos = $_REQUEST['cabelos'];
    $peso = $_REQUEST['peso'];
    $altura = $_REQUEST['altura'];
    $olhos = $_REQUEST['olhos'];
    $defeito = $_REQUEST['defeito'];
    $cipa = $_REQUEST['cipa'];
    $etnia = $_REQUEST['etnia'];
    $deficiencia = $_REQUEST['deficiencia'];
    $tipo_admissao = $_REQUEST['tipo_admissao'];
    $filho_1 = $_REQUEST['filho_1'];
    $filho_2 = $_REQUEST['filho_2'];
    $filho_3 = $_REQUEST['filho_3'];
    $filho_4 = $_REQUEST['filho_4'];
    $filho_5 = $_REQUEST['filho_5'];
    $filho_6 = $_REQUEST['filho_6'];
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
    $data_emissao = $_REQUEST['data_emissao'];

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

    /* Função para converter a data */

    function ConverteData($data) {
        if (strstr($data, '/')) {
            $nova_data = implode('-', array_reverse(explode('/', $data)));
            return $nova_data;
        } elseif (strstr($data, '-')) {
            $nova_data = implode('/', array_reverse(explode('-', $data)));
            return $nova_data;
        } else {
            return '';
        }
    }

    $data_filho_1 = ConverteData($data_filho_1);
    $data_filho_2 = ConverteData($data_filho_2);
    $data_filho_3 = ConverteData($data_filho_3);
    $data_filho_4 = ConverteData($data_filho_4);
    $data_filho_5 = ConverteData($data_filho_5);
    $data_filho_6 = ConverteData($data_filho_6);
    $data_nasc = ConverteData($data_nasc);
    $data_rg = ConverteData($data_rg);
    $data_escola = ConverteData($data_escola);
    $data_entrada = ConverteData($data_entrada);
    $pis_data = ConverteData($pis_data);
    $exame_data = ConverteData($exame_data);
    $trabalho_data = ConverteData($trabalho_data);

    $data_nasc_pai = ConverteData($data_nasc_pai);
    $data_nasc_mae = ConverteData($data_nasc_mae);
    $data_nasc_conjuge = ConverteData($data_nasc_conjuge);
    $data_nasc_avo_h = ConverteData($data_nasc_avo_h);
    $data_nasc_avo_m = ConverteData($data_nasc_avo_m);
    $data_nasc_bisavo_h = ConverteData($data_nasc_bisavo_h);
    $data_nasc_bisavo_m = ConverteData($data_nasc_bisavo_m);
    $data_emissao = ConverteData($data_emissao);
    $email = $_POST['email'];


// VERIFICANDO SE O FUNCIONÁRIO JA ESTÁ CADASTRADO NA TABELA CLT
    $verificando_clt = mysql_query("SELECT nome,id_clt FROM rh_clt WHERE cpf = '$cpf' AND id_projeto = '$id_projeto' AND status IN(10,200)");
    $row_verificando_clt = mysql_fetch_assoc($verificando_clt);
//$verifica_clt = mysql_num_rows($verificando_clt);
///////VERIFICA SE A QUANTIDADE LIMITE DE TRABALHADORES FOI ATINGIDA NO INSTITUO LAGOS
    $qr_curso_max = mysql_query("SELECT * FROM curso WHERE id_curso = '$id_curso' ");
    $row_curso_max = mysql_fetch_assoc($qr_curso_max);

    $total_clt_curso = mysql_result(mysql_query("SELECT COUNT(id_curso) as total_curso FROM rh_clt  WHERE id_curso = '$id_curso' AND id_regiao = '$regiao' AND id_projeto = '$id_projeto' and status IN(10,200)"), 0);


    if ($total_clt_curso == $row_curso_max['qnt_maxima'] and $row_curso_max['qnt_maxima'] != 0) {


        $menssagem = 'O número máximo de vagas para esta atividade (' . $row_curso_max['nome'] . ') foi atingido.<br><br>
			 Para mais informações, entrar em contato com o setor de TI.';

        $link_voltar = "../ver.php?regiao=$regiao&projeto=$id_projeto";

        include('../pagina_alerta.php');

        exit();
    }




    if ($verifica_clt != 0) {
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
        mysql_query("INSERT INTO rh_clt
(id_projeto,id_regiao,localpagamento,locacao,nome,sexo,endereco,numero,complemento,bairro,cidade,uf,cep,tel_fixo,tel_cel,tel_rec,
data_nasci,naturalidade,nacionalidade,civil,rg,orgao,data_rg,cpf,conselho,titulo,zona,secao,pai,nacionalidade_pai,mae,nacionalidade_mae,
estuda,data_escola,escolaridade,instituicao,curso,tipo_contratacao,banco,agencia,conta,tipo_conta,id_curso,apolice,status,data_entrada,data_saida,
campo1,campo2,campo3,data_exame,reservista,etnia,deficiencia,cabelos,altura,olhos,peso,defeito,cipa,ad_noturno,plano,assinatura,distrato,
outros,pis,dada_pis,data_ctps,serie_ctps,uf_ctps,uf_rg,fgts,insalubridade,transporte,medica,tipo_pagamento,nome_banco,num_filhos,
observacao,impressos,sis_user,data_cad,foto,rh_vinculo,rh_status,rh_horario,rh_sindicato,rh_cbo,status_admi,
desconto_inss,tipo_desconto_inss,valor_desconto_inss,trabalha_outra_empresa,salario_outra_empresa,desconto_outra_empresa,matricula,n_processo, contrato_medico, email,
data_nasc_pai, data_nasc_mae, data_nasc_conjuge, nome_conjuge, nome_avo_h,nome_avo_m, nome_bisavo_h, nome_bisavo_m, 
data_nasc_avo_h, data_nasc_avo_m, data_nasc_bisavo_h, data_nasc_bisavo_m,municipio_nasc,uf_nasc,data_emissao, verifica_orgao, tipo_sanguineo, ano_contribuicao, ano_chegada_no_pais, cod_pais_rais ) 
VALUES
('$id_projeto','$regiao','$localpagamento','$locacao','$nome','$sexo','$endereco','$numero','$complemento','$bairro','$cidade','$uf',
'$cep','$tel_fixo','$tel_cel','$tel_rec','$data_nasc','$naturalidade','$nome_nacionalidade','$civil','$rg',
'$orgao','$data_rg','$cpf','$conselho','$titulo','$zona','$secao','$pai','$nacionalidade_pai','$mae','$nacionalidade_mae','$estuda',
'$data_escola','$escolaridade','$instituicao','$curso','$tipo_contratacao','$banco','$agencia','$conta','$tipoDeConta','$id_curso','$apolice',
'10','$data_entrada','0000-00-00','$campo1','$campo2','$campo3','$exame_data','$reservista','$etnia','$deficiencia','$cabelos','$altura','$olhos',
'$peso','$defeito',
'$cipa','$ad_noturno','$plano_medico','$impressos','$assinatura2','$assinatura3','$pis','$pis_data','$trabalho_data','$serie_ctps',
'$uf_ctps','$uf_rg','$fgts','$insalubridade','$transporte','$medica','$tipopg','$nomebanco','$filhos','$observacoes','$impressos',
'$_COOKIE[logado]','$data_cadastro','$foto_banco','$vinculo','$rh_status','$horario','$SINDICATO','$rh_cbo','$tipo_admissao',
'$desconto_inss','$tipo_desconto_inss','$valor_desconto_inss','$trabalha_outra_empresa','$salario_outra_empresa','$desconto_outra_empresa','$matricula','$n_processo','$contrato_medico', '$email',
'$data_nasc_pai', '$data_nasc_mae','$data_nasc_conjuge','$conjuge', '$avo_h', '$avo_m', '$bisavo_h', '$bisavo_m', '$data_nasc_avo_h','$data_nasc_avo_m', '$data_nasc_bisavo_h', '$data_nasc_bisavo_m',
    '$municipio_nasc','$uf_nasc', '$data_emissao', '$verifica_orgao', '$tiposanguineo', '$ano_contribuicao', '$ano_chegada_pais','$cod_nacionalidade')") or die("$mensagem_erro<br><BR>" . mysql_error());

        $row_id_participante = mysql_insert_id();
        $row_id_clt = $row_id_participante;
    } // AQUI TERMINA DE INSERIR OS DADOS DO CLT

    $id_bolsista = $row_id_participante;




    if ($transporte == '1') {
        mysql_query("INSERT INTO rh_vale(id_clt,id_regiao,id_projeto,id_tarifa1,id_tarifa2,id_tarifa3,id_tarifa4,
id_tarifa5,id_tarifa6,cartao1,cartao2) VALUES 
('$row_id_participante','$regiao','$projeto','$vale1','$vale2','$vale3','$vale4','$vale5','$vale6','$num_cartao','$num_cartao2')") or die("$mensagem_erro - 2.3<br><br>" . mysql_error());
    }

    if ($filho_1 == '' and $filho_2 == '' and $filho_3 == '' and $filho_4 == '' and $filho_5 == '' and $filho_6 == '') {

        $naa = '0';
    } else {
        mysql_query("INSERT INTO dependentes(id_regiao,id_projeto,id_bolsista,contratacao,nome,data1,nome1,data2,nome2,data3,nome3,data4,nome4,data5,nome5,data6,nome6, ddir_pai, ddir_mae, ddir_conjuge, portador_def1, portador_def2, portador_def3, portador_def4, portador_def5, portador_def6, ddir_avo_h, ddir_avo_m, ddir_bisavo_m,ddir_bisavo_h) VALUES
('$regiao','$id_projeto','$row_id_participante','$tipo_contratacao','$nome','$data_filho_1','$filho_1','$data_filho_2','$filho_2','$data_filho_3','$filho_3','$data_filho_4','$filho_4','$data_filho_5','$filho_5','$data_filho_6','$filho_6','$ddir_pai', '$ddir_mae', '$ddir_conjuge', '$portador1','$portador2', '$portador3', '$portador4', '$portador5', '$portador6', '$ddir_avo_h', '$ddir_avo_m', '$ddir_bisavo_m', '$ddir_bisavo_h')") or die("$mensagem_erro 2.4<br><br>" . mysql_error());
        $naa = "2";
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
    mysql_query("insert into tvsorrindo(id_clt,id_projeto,nome,cpf,matricula,senha,inicio) values
('$row_id_participante','$id_projeto','$nome','$cpf','$matricula','$senha','$inicio')") or die("$mensagem_erro<br><Br>");

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