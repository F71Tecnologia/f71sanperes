<?php
include ("../include/restricoes.php");
/*
  SISTEMA DE BUSCA EM AJAX USANDO TECNOLOGIA JSON.
  USADO PARA OBTER MAIS RAPIDEZ E PRECISÃƒO NA CONSULTA.
  VERSÃƒO 1.0 rsrs...
 */

/*-- NEM FALO NADA SOBRE O COMENTÁRIO ACIMA... OLHA O CÓDIGO NOJO!! 12/2013-- */
$grupo = montaQuery("entradaesaida_grupo", "*", "id_grupo >= 10");
?>
<script type="text/javascript">
    $(function() {
        var loading = $('.carregando');
        var resultado = $('#resultado');

        $('#form_busca').submit(function(e) {
            e.preventDefault();
            // quantidade de campos vazios
            var quant_campos = $(this).find('input,select').not('input[type=hidden],input[type=submit]').length;
            var quant_vazios = $(this).find('input[value=],select[value=]').length;
            if (quant_campos == quant_vazios) {
                resultado.html('<center>Selecione um argumento para pesquisar!</center>');
                return false;
            }


            var dados = $(this).serialize();
            loading.fadeIn('fast');
            resultado.fadeTo('fast', 0.5);
            $.post('../actions/form.submit.busca.php',
                    dados,
                    function(retorno) {

                        resultado.html('');

                        if (retorno.erro != null) {
                            resultado.html('<center>' + retorno.erro + '</center>');
                            loading.fadeOut('fast');
                            resultado.fadeTo('fast', 1.0);
                            return false;
                        }
                        if (retorno.sql != null) {
                            //resultado.append('<center>'+retorno.sql+'</center>');
                            resultado.append('<center><a id="impressao" href="#" onclick="return false">Visualizar impress&atilde;o</a></center>');

                            $("#sql").val(retorno.sql);
                            $('#impressao').click(function() {
                                $('#form_rel').submit();
                            });

                        }

                        resultado.append('<table width="100%">');

                        var tabela = '<tr class="cabecalho"><th>ID</th><th></th><th>Nome</th><th>Descri&ccedil;&atilde;o</th><th>Banco</th><th>Regiao</th><th>Projeto</th><th>Data de vencimento</th><th>Valor</th><th>Anexo 1</th><th>Anexo 2</th></tr>';
                        $.each(retorno.dados, function(i, valor) {
                            tabela += '<tr>';
                            if (valor.id_saida == undefined) {
                                tabela += '<td>' + valor.id_entrada + '</td>';
                            } else {
                                tabela += '<td>' + valor.id_saida + '---' + valor.tipo + '</td>';

                                if (valor.tipo == 168 || valor.tipo == 167 || valor.tipo == 169 || valor.tipo == 170 || valor.tipo == 175 || valor.tipo == 156 || valor.tipo == 154 || valor.tipo == 32 || valor.tipo == 31 || valor.tipo == 30 || valor.tipo == 29 || valor.tipo == 171) {

                                    tabela += '<td><a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/novoFinanceiro/editar_data.php?id=' ?>' + valor.id_saida + '"\n\
                                                onclick=\"return hs.htmlExpand(this, { objectType: \'iframe\', width: 650 } )\" >editar </a> </td>';
                                } else {

                                    tabela += '<td><a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/intranet/novoFinanceiro/cad_edit_saida_1.php?id=' ?>' + valor.id_saida + '"\n\
                                                onclick=\"return hs.htmlExpand(this, { objectType: \'iframe\', width: 650 } )\" >editar </a> </td>';
                                }


                            }

                            tabela += '<td>' + valor.nome + '</td>';
                            tabela += '<td>' + valor.especifica + '</td>';
                            tabela += '<td>' + valor.id_banco + '</td>';
                            tabela += '<td>' + valor.regiao + '</td>';
                            tabela += '<td>' + valor.nome_projeto + '</td>';
                            tabela += '<td>' + valor.data_vencimento + '</td>';
                            tabela += '<td>' + valor.valor + '</td>';
                            tabela += '<td align="center">' + valor.comprovante1 + '</td>';
                            tabela += '<td align="center">' + valor.comprovante2 + '</td>';
                            tabela += '</tr>';

                        });
                        resultado.find('table').append(tabela);

                        loading.fadeOut('fast');
                        resultado.fadeTo('fast', 1.0);
                        resultado.find('table tr:odd').addClass('linha_um_busca');
                    }, 'json');
        });

        $("select[name*=grupo]").change(function() {

            $("#tipo").html('<option value="">Carregando...</option>');
            $("#tipo").attr('disabled', 'disabled');
            $("#nome").html('<option value="" selected="selected">Selecione</option>');
            
            $.post('../actions/combo.tipo.php',{grupo: $(this).val()}, function(retorno) {
                
                $("#tipo").removeAttr('disabled');
                $("#tipo").html('<option value="" selected="selected">Selecione</option>');
                var opcao = "<option value='-1'>« Selecione »</option>";
                for (var i in retorno){
                    opcao += "<option value='" + i + "'>" + retorno[i] + "</option>";
                }
                
                $("#tipo").append(opcao);
            },'json');
        });
    });
</script>
<style type="text/css">
    .carregando {
        overflow: hidden;
        position: absolute;
        height: 85px;
        width: 100px;
        left: 50%;
        z-index: 100;
        display:none;
        text-align:center;
        background-color:#FFF;
    }

    #resultado {
        overflow:scroll;
        height:400px;
        width:100%;
    }

    #resultado table{
        padding:3px;
        font-size:10px;
    }
    #resultado table tr.cabecalho {
        background-color:#CCC !important;
        color:#000 !important;
        text-align:left !important;
    }

    #resultado table tr:hover {
        background-color:#666;
        color:#FFF;
    }

    .linha_um_busca {
        background-color:#f5f5f5;
    }

    .linha_um_busca td {
        border-bottom:1px solid #ccc;
    }

    span.titulos {
        font-weight:bold;
    }
</style>

<form name="form_busca" id="form_busca" method="post" action="">
    <table width="0" border="0" align="center" cellpadding="2" cellspacing="0">
        <tr>
            <td colspan="5" align="center"><span class="titulos">Pesquisa por</span></td>
        </tr>
        <tr>
            <td align="right">Cod.<input name="id" type="text" size="8" /></td>
            <td >ou</td>
            <td>Nome:</td>
            <td><input name="nome" type="text" size="65" /></td>
            <td>Tipo <select name="tabela">
                    <option selected="selected" value="saida">Saida</option>
                    <option value="entrada">Entrada</option>
                </select>
            </td>
        </tr>
    </table>
    <table width="489" align="center">
        <tr>
            <td colspan="10" align="center"><span class="titulos">Pesquisar por tipo</span></td>
        </tr>
        <tr>
            <td>Grupo</td>
            <td width="93">
                <select name="grupo" style="width: 200px;">
                    <option value="" selected="selected">Selecione</option>
                    <?php
                    foreach ($grupo as $valor):
                        print '<option value="' . $valor['id_grupo'] . '">' . $valor['id_grupo'] . ' - ' . $valor['nome_grupo'] . '</option>';
                    endforeach;
                    ?>
                </select></td>
            <td width="48">&nbsp;</td>
            <td width="28">Tipo</td>
            <td width="181">
                <select name="tipo" nome="tipo" id="tipo" style="width: 200px;">
                    <option value="">Selecione um tipo</option>
                </select>
                </select>
            </td>
            <td width="1">M&ecirc;s&nbsp;</td>
            <td width="1">
                <select name="mes" id="mes">
                    <option value="">- Selecione -</option>
                    <?php
                    $query_mes = mysql_query("SELECT * FROM ano_meses ORDER BY num_mes");
                    while ($row_mes = mysql_fetch_array($query_mes)) {
                        echo "<option $selected value=\"$row_mes[0]\" >$row_mes[1]</option>";
                    }
                    ?>
                </select></td>
            <td width="244">Ano</td>
            <td width="244">
                <select name="ano" id="ano">
                    <option value="">- Selecione -</option>
                    <?php
                    $ano_hj = date('Y');
                    $ano_antes = $ano_hj - 4;
                    $ano_depois = $ano_hj + 5;
                    for ($i = $ano_antes; $i < $ano_depois; $i++) {
                        if (!empty($i)) {
                            echo "<option $selected value=\"$i\">$i</option>";
                        }
                    }
                    ?>
                </select>
            </td>
            <td width="244"><input type="hidden" name="regiao" value="<?= $regiao; ?>"/>
            </td>
        </tr>
        <tr>
            <td colspan="10" align="center">
                <input type="submit" value="Buscar" class="submit-go">
            </td>
        </tr>
    </table>
</form>

<form action="../../financeiro/views/impressao.php" method="post" name="form_rel" id="form_rel" target="_blank">
    <input type="hidden" name="sql" id="sql" value=""/>
</form>

<div class="carregando">
    <img src="../image/ajax-loader.gif"> <br />
    Carregando...
</div>
<div id="resultado">

</div>