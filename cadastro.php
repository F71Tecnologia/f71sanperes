<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
} else {

    include "conn.php";
    include "wfunction.php";
    
    $usuario = carregaUsuario();

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

// SELECIONANDO AS REGIÕES CADASTRADAS NO BANCO
    $sql = "SELECT * from regioes where id_master = '$row_user[id_master]'";
    $result = mysql_query($sql, $conn);

//PEGANDO O ID DO CADASTRO
    $id = $_REQUEST['id'];

    function acoes_checked($acoes_id, $id_funcionario) {

        $qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc  WHERE id_funcionario = '$id_funcionario'  AND acoes_id = '$acoes_id' ");
        if (mysql_num_rows($qr_acoes_assoc) != 0)
            return 'checked="checked"';
    }

    if (isset($_POST['ajax'])) {

        $id_master = $_POST['id_master'];

        $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master = '$id_master'");
        while ($row_regiao = mysql_fetch_assoc($qr_regiao)) {


            echo '<option value="' . $row_regiao['id_regiao'] . '">' . htmlentities(utf8_encode($row_regiao['regiao'])) . '</option>';
        }
        exit;
    }
    ?>
    <html>
        <head><title>:: Intranet ::</title>
            <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
            <script language="javascript" src="jquery-1.3.2.js"></script>
            <script src='ajax.js' type='text/javascript'></script>
            <script language="javascript" src='js/ramon.js' type='text/javascript'></script>
            <link href='autocomp/css.css' type='text/css' rel='stylesheet'>
            <link href="net1.css" rel="stylesheet" type="text/css">

            <script src="jquery/jquery-1.4.2.min.js"></script>
            <script src="jquery/base64.js"></script>
            <script>

                $(function() {

                    $('input[name=todos]').click(function() {

                        var verifica = $(this).attr('checked');
                        var numero = $(this).val();


                        if (verifica == true) {

                            $('.' + numero).attr('checked', 'checked');

                        } else {

                            $('.' + numero).attr('checked', false)
                        }
                    });





                    $('.todos_master').click(function() {

                        var valor = $(this).val();
                        var verifica = $(this).attr('checked');


                        if (verifica == true) {

                            $('.master_' + valor).attr('checked', 'checked');

                        } else {

                            $('.master_' + valor).attr('checked', false);
                        }



                    });





                    $('.master_email').change(function() {

                        var master_id = $(this).val();
                        var senha = $(this).parent().parent().parent().find('.senha');
                        var email = $(this).parent().parent().parent().find('.email');



                        if ($(this).attr('checked')) {

                            $('.master_' + master_id).css('display', 'block');

                        } else {

                            $('.master_' + master_id).hide();

                        }

                    });


                    $('.senha_email').live('blur', function() {

                        var master = $(this).attr('rel');
                        var senha = Base64.encode($(this).val());
                        var email = $(this).parent().parent().find('.email').val();
                        var menssagem = $(this).parent().parent().find('.menssagem');

                        $.ajax({
                            url: 'action.verifica_email.php?master=' + master + '&senha=' + senha + '&email=' + email,
                            success: function(resposta) {


                                if (resposta == 1) {
                                    3
                                    menssagem.html('<span class="ok"> OK</span>')

                                } else {
                                    menssagem.html('<span class="email_incorreto"> E-mail ou senha incorreto!</span>');

                                }

                            }

                        })


                    });


                });
            </script>




            <style type="text/css">
                .style2 {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    font-weight: bold;
                }
                .style5 {color: #FF0000}
                .style6 {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                }
                .style11 {font-weight: bold}
                .style13 {font-weight: bold}
                .style15 {font-weight: bold}
                .style17 {font-weight: bold}
                .style19 {font-weight: bold}
                .style23 {font-weight: bold}
                .style24 {
                    font-size: 10px;
                    font-weight: bold;
                    color: #003300;
                }
                .style25 {color: #003300}
                .style26 {
                    color: #FFFFFF;
                    font-size: 10px;
                }
                .style27 {color: #FFFFFF; }

                .ok{
                    background-color:#090;
                    font-weight:bold;
                    color:#FFF;	
                    padding:2px;
                }
                .email_incorreto{
                    background-color: #FF5B5B;
                    font-weight:bold;
                    color:#FFF;	
                    padding:2px;
                }


            </style>
        </head>
        <body>
    <?php
    switch ($id) {     //SELEÇÃO DE CASOS
        case 1:      //CASO O ID SEJA 1 ELE VAI RODAR O - CADASTRO DE PROJETO -

            $sql_projeto = "SELECT COUNT(id_projeto) FROM projeto";
            $resulto_projeto = mysql_query($sql_projeto);
            $row_projeto = mysql_fetch_array($resulto_projeto);
            $id_projeto = $row_projeto[0] + 1;
            $id_regiao = $_REQUEST['regiao'];
            $id_user = $_REQUEST['user'];
            ?>
                    <form action="cadastro2.php" method="post" name="form1" onSubmit="return validaForm()">
                        <table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                            <tr>
                                <td height="38" colspan='6' align="right"><?php include('reportar_erro.php'); ?></td>
                            </tr>
                            <tr>
                                <td height="38" colspan='2' class="fundo_azul"><div class="titulo">Cadastro de Projetos</div></td>
                            </tr>

                            <tr><td width="136" height="30" class="secao">Nome do Projeto:</td>
                                <td width="381" height="30" class="conteudo"><input name="nome" type="text" class="campotexto" id="nome" size="20"></td>
                            </tr>
                            <tr><td height="30" class="secao">Tema:</td>
                                <td height="30" class="conteudo"><input name="tema" type="text" class="campotexto" id="tema" size="35"></td></tr>
                            <tr>
                                <td height="30" class="secao">&Aacute;rea:</td>
                                <td height="30" class="conteudo"><input name="area" type="text" class="campotexto" id="area" size="25"></td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Local:</td>
                                <td height="30" class="conteudo"><input name="local" type="text" class="campotexto" id="local" size="25"></td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Região:</td>
                                <td height="30" class="conteudo">

                                    <select name="id_regiao" class="campotexto" id="regiao">
            <?
            while ($row = mysql_fetch_array($result)) {
                $row_regiao = "$row[id_regiao]";
                if ($id_regiao == "$row_regiao") {
                    print "<option value=$row[id_regiao] selected>$row[0] - $row[regiao] - $row[sigla]</option>";
                } else {
                    print "<option value=$row[id_regiao]>$row[0] - $row[regiao] - $row[sigla]</option>";
                }
            }
            ?>
                                    </select>

                                </td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Inicio:</td>
                                <td height="30" class="conteudo">

                                    <table width="241" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="60">
                                                <select name="ini_dia" class="campotexto" id="ini_dia">
                                                    <option>01</option>
                                                    <option>02</option>
                                                    <option>03</option>
                                                    <option>04</option>
                                                    <option>05</option>
                                                    <option>06</option>
                                                    <option>07</option>
                                                    <option>08</option>
                                                    <option>09</option>
                                                    <option>10</option>
                                                    <option>11</option>
                                                    <option>12</option>
                                                    <option>13</option>
                                                    <option>14</option>
                                                    <option>15</option>
                                                    <option>16</option>
                                                    <option>17</option>
                                                    <option>18</option>
                                                    <option>19</option>
                                                    <option>20</option>
                                                    <option>21</option>
                                                    <option>22</option>
                                                    <option>23</option>
                                                    <option>24</option>
                                                    <option>25</option>
                                                    <option>26</option>
                                                    <option>27</option>
                                                    <option>28</option>
                                                    <option>29</option>
                                                    <option>30</option>
                                                    <option>31</option>
                                                </select></td>
                                            <td width="110"><select name="ini_mes" class="campotexto" id="ini_mes">
                                                    <option value="01">Janeiro</option>
                                                    <option value="02">Fevereiro</option>
                                                    <option value="03">Mar&ccedil;o</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Maio</option>
                                                    <option value="06">Junho</option>
                                                    <option value="07">Julho</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setembro</option>
                                                    <option value="10">Outubro</option>
                                                    <option value="11">Novembro</option>
                                                    <option value="12">Dezembro</option>
                                                </select></td>
                                            <td width="71"><select name="ini_ano" class="campotexto" id="ini_ano">
                                                    <option value="2007">2007</option>
                                                    <option value="2008">2008</option>
                                                    <option value="2009">2009</option>
                                                    <option value="2010">2010</option>
                                                    <option value="2011">2011</option>
                                                    <option value="2012">2012</option>
                                                    <option value="2013">2013</option>
                                                    <option value="2014">2014</option>
                                                    <option value="2015">2015</option>
                                                </select></td>
                                        </tr>
                                    </table></td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Previs&atilde;o de T&eacute;rmino:</td>
                                <td height="30" class="conteudo">

                                    <table width="241" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="61">
                                                <select name="ter_dia" class="campotexto" id="ter_dia">
                                                    <option>01</option>
                                                    <option>02</option>
                                                    <option>03</option>
                                                    <option>04</option>
                                                    <option>05</option>
                                                    <option>06</option>
                                                    <option>07</option>
                                                    <option>08</option>
                                                    <option>09</option>
                                                    <option>10</option>
                                                    <option>11</option>
                                                    <option>12</option>
                                                    <option>13</option>
                                                    <option>14</option>
                                                    <option>15</option>
                                                    <option>16</option>
                                                    <option>17</option>
                                                    <option>18</option>
                                                    <option>19</option>
                                                    <option>20</option>
                                                    <option>21</option>
                                                    <option>22</option>
                                                    <option>23</option>
                                                    <option>24</option>
                                                    <option>25</option>
                                                    <option>26</option>
                                                    <option>27</option>
                                                    <option>28</option>
                                                    <option>29</option>
                                                    <option>30</option>
                                                    <option>31</option>
                                                </select></td>
                                            <td width="109"><select name="ter_mes" class="campotexto" id="ter_mes">
                                                    <option value="01">Janeiro</option>
                                                    <option value="02">Fevereiro</option>
                                                    <option value="03">Mar&ccedil;o</option>
                                                    <option value="04">Abril</option>
                                                    <option value="05">Maio</option>
                                                    <option value="06">Junho</option>
                                                    <option value="07">Julho</option>
                                                    <option value="08">Agosto</option>
                                                    <option value="09">Setembro</option>
                                                    <option value="10">Outubro</option>
                                                    <option value="11">Novembro</option>
                                                    <option value="12">Dezembro</option>
                                                </select></td>
                                            <td width="71"><select name="ter_ano" class="campotexto" id="ter_ano">
                                                    <option value="2007">2007</option>
                                                    <option value="2008">2008</option>
                                                    <option value="2009">2009</option>
                                                    <option value="2010">2010</option>
                                                    <option value="2011">2011</option>
                                                    <option value="2012">2012</option>
                                                    <option value="2013">2013</option>
                                                    <option value="2014">2014</option>
                                                    <option value="2015">2015</option>
                                                </select></td>
                                        </tr>
                                    </table></td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Descri&ccedil;&atilde;o:</td>
                                <td height="30" class="conteudo"> <textarea id="textarea" name="caracteres" cols="30" rows="7" class="campotexto" onKeyPress='soma(this.value)' onKeyUp='soma(this.value);
                            Contar(this)'></textarea><input type="hidden" name="exibe" size="10" maxlength="10">
                                    <input type="hidden" name="exibe2" size="10" maxlength="10">
                                    <br>&nbsp;<font size="1" color="#CCCCCC">(<span id="Qtd">250</span> caracteres restantes)</font>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Valor de Reserva Inicial:</td>
                                <td height="30" class="conteudo"> R$: <input name="valor_ini" type="text"  class="campotexto" id="reserva" size="5"></td>
                            </tr>
                            <tr>
                                <td height="30" class="secao">Total de Participantes:</td>
                                <td height="30" class="conteudo"><input name="bolsista" type="text"  class="campotexto" id="bolsista" size="3" onKeypress="if (event.keyCode < 45 || event.keyCode > 57)
                                event.returnValue = false;"></td>
                            </tr>
                            <tr>
                                <td height="67" colspan="2" align="center" bgcolor="#FFFFFF"><table width="246" border="0" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td width="76" align="center"> <input type="reset" name="Submit2" value="Limpar">
                                            </td>
                                            <td width="170" align="center" valign="middle"> <input type="submit" name="Submit" value="CADASTRAR"></td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="id_cadastro" value="1">
                                    <input type="hidden" name="id_projeto" value="<?= $id_projeto ?>">
                                    <input type="hidden" name="user" value="<?= $id_user ?>">
                                </td>
                            </tr>
                        </table>
                    </form>
                    <br><a href="javascript:window.close()" class="link"><img src="imagens/voltar.gif" border=0></a>


                    <script>
                        function validaForm() {
                            d = document.form1;
                            if (d.nome.value == "") {
                                alert("O campo Nome do Projeto deve ser preenchido!");
                                d.nome.focus();
                                return false;
                            }
                            if (d.tema.value == "") {
                                alert("O campo Tema deve ser preenchido!");
                                d.tema.focus();
                                return false;
                            }
                            if (d.area.value == "") {
                                alert("O campo Área deve ser preenchido!");
                                d.area.focus();
                                return false;
                            }
                            if (d.local.value == "") {
                                alert("O campo Local deve ser preenchido!");
                                d.local.focus();
                                return false;
                            }
                            if (d.reserva.value == "") {
                                alert("O campo Valor de Reserva Inicial deve ser preenchido!");
                                d.reserva.focus();
                                return false;
                            }
                            if (d.bolsista.value == "") {
                                alert("O campo Total de Participantes deve ser preenchido!");
                                d.bolsista.focus();
                                return false;
                            }
                            return true;
                        }
                    </script>

            <?php
            break;

        case 2:         // CASO O ID SEJA 2 ELE VAI RODAR O - REGIÕES -
            ?>

                    <style type="text/css">
                        .novo_estilo {
                            font-weight:bold;
                            font-size:14px;
                            text-align:center;
                            width:100%;
                        }
                    </style>
                    <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
                        <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                            <tr>
                                <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                            </tr>
                            <tr>
                                <td height="38" colspan='2' class="fundo_azul"><div class="titulo">Cadastro de Regi&otilde;es</div></td>
                            </tr>

                            <tr>
                                <td width="164" class="secao">Master</td>
                                <td width="311" class="conteudo">
                                    <select name="master">
            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1");
            while ($row_master = mysql_fetch_assoc($qr_master)):

                echo '<option value="' . $row_master['id_master'] . '" >' . $row_master['nome'] . '</option>';

            endwhile;
            ?>
                                    </select>

                                </td>
                            </tr>

                            <tr>
                                <td width="164" class="secao">Nome da Regi&atilde;o:</td>
                                <td width="311" class="conteudo">
                                    <input name='regiao' type='text' class='campotexto' id='regiao2' size='20' style='background:#FFFFFF;'
                                           onfocus="this.style.background = '#CCFFCC'" onBlur="this.style.background = '#FFFFFF'" /></td>
                            </tr>
                            <tr>
                                <td class="secao">Sigla:</td>
                                <td class="conteudo">
                                    <input name='sigla' type='text' class='campotexto' id='sigla' size='2' maxlength='2' style='background:#FFFFFF;' onFocus="this.style.background = '#CCFFCC'" onBlur="this.style.background = '#FFFFFF'" onChange="this.value = this.value.toUpperCase()" /></td>
                            </tr>
                            <tr>
                                <td height="55" colspan='2' align='center' valign="middle">
                                    <input type='hidden' name='id_cadastro' value='2' />
                                    <input type='submit' name='Submit3' value='CADASTRAR'/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>


            <script>function validaForm() {
                            d = document.form1;
                            if (d.regiao.value == "") {
                                alert("O campo Nome da Região deve ser preenchido!");
                                d.regiao.focus();
                                return false;
                            }
                            if (d.sigla.value == "") {
                                alert("O campo Sigla deve ser preenchido!");
                                d.sigla.focus();
                                return false;
                            }
                            return true;
                        }
            </script>
            <?php
            break;

        case 3:   //CASO O ID SEJA 3 ELE VAI RODAR O - CADASTRO DE FUNCIONÁRIO/USUÁRIO -

            $id_user = $_COOKIE['logado'];
            $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'", $conn);
            $row_user = mysql_fetch_array($result_user);
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()" enctype='multipart/form-data'>
                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='8'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='8' class="fundo_azul">
                            <div class="titulo">Cadastro de Usu&aacute;rio para acesso a Intranet</div></td>
                    </tr>
                    <tr>
                        <td class="secao">Empresa:</td>
                        <td colspan="5" class="conteudo">
                            <select name="empresa" id="empresa">
            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1");
            while ($master = mysql_fetch_assoc($qr_master)):
                ?>
                                    <option value="<?php echo $master['id_master']; ?>"> <?php echo $master['nome']; ?></option>
                                    <?php
                                endwhile;
                                ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td width="16%" class="secao" id="regiao">Região:</td>
                        <td colspan="5" class="conteudo"><select name="id_regiao" class="campotexto" id="id_regiao">
            <?php
            while ($row = mysql_fetch_array($result)) {
                $regiao_atual = $row_user[id_regiao];
                $regiao_atual2 = $row[id_regiao];
                if ($regiao_atual == $regiao_atual2) {
                    print "<option value='$row[id_regiao]' selected>$row[0] - $row[regiao] - $row[sigla]</option>";
                } else {
                    print "<option value='$row[id_regiao]'>$row[regiao] - $row[sigla]</option>";
                }
            }
            ?>
                            </select></td>
                        <td width="11%">&nbsp;</td>
                        <td width="26%">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="secao">Função:</td>
                        <td colspan="4" class="conteudo"> 
                            <input name="funcao" type="text" class="campotexto" id="funcao" size="30" 
                                   onFocus="document.all.funcao.style.background = '#CCFFCC'" onBlur="document.all.funcao.style.background = '#FFFFFF'" 
                                   style="background:#FFFFFF" onChange="this.value = this.value.toUpperCase()">
                        <td class="secao">Locação:</td>
                        <td class="conteudo" colspan="2"><input name="locacao" type="text" class="campotexto'" id="locacao" size="20"
                                                                onFocus="document.all.locacao.style.background = '#CCFFCC'" onBlur="document.all.locacao.style.background = '#FFFFFF'" style="background:#FFFFFF" onChange="this.value = this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                        <td class="secao">Grupo: </td>
                        <td colspan="5" class="conteudo"> 
                            <select name='grupo_usuario' class='campotexto'>
                                <?php
                                $result_grupo = mysql_query("SELECT * FROM grupo", $conn);
                                while ($row_grupo = mysql_fetch_array($result_grupo)) {
                                    print "<option value=$row_grupo[id_grupo]>$row_grupo[nome]</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <!--
                        <td class="secao">Salário:</td>
                        <td class="conteudo" colspan="2">R$&nbsp;&nbsp;<input name='salario' type='text' class='campotexto' id='salario' size='10'
                        onFocus="document.all.salario.style.background='#CCFFCC'"
                        onBlur="document.all.salario.style.background='#FFFFFF'" 
                        style="background:#FFFFFF"> <font color="#999999" size="1">Somente números</font></td>
                        -->
                    </tr>
                    <tr>
                        <td class="secao">Nome:</td>
                        <td class="conteudo" colspan="4"><input name='nome' type='text' class='campotexto' id='nome' size='35' onFocus="document.all.nome.style.background = '#CCFFCC'" onBlur="document.all.nome.style.background = '#FFFFFF'" 
                                                                style="background:#FFFFFF" onChange="this.value = this.value.toUpperCase()"></td>
                        <td class="secao">Nome para exibi&ccedil;&atilde;o:</td>
                        <td class="conteudo" colspan="2"><input name='nome1' type='text' class='campotexto' id='nome1' size='15' onFocus="document.all.nome1.style.background = '#CCFFCC'" onBlur="document.all.nome1.style.background = '#FFFFFF'" 
                                                                style="background:#FFFFFF"></td>
                    </tr>
                    <!--
                    <tr>
                    <td class="secao">Endereço:</td>
                    <td class="conteudo" colspan="7"><input name='endereco' type='text' class='campotexto' id='endereco' size='75' onFocus="document.all.endereco.style.background='#CCFFCC'"
                    onBlur="document.all.endereco.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                    <td width="16%" class="secao">Bairro:</td>
                    <td width="18%" class="conteudo"><input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
                    onFocus="document.all.bairro.style.background='#CCFFCC'"
                    onBlur="document.all.bairro.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
                    </td>
                    <td width="9%" class="secao">Cidade:</td>
                    <td width="14%" class="conteudo"><input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
                    onFocus="document.all.cidade.style.background='#CCFFCC'"
                    onBlur="document.all.cidade.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    <td width="4%" class="secao">UF:</td>
                    <td width="2%" class="conteudo"><input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
                    onFocus="document.all.uf.style.background='#CCFFCC'"
                    onBlur="document.all.uf.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()" onKeyUp="pula(2,this.id,cep.id)" ></td>
                    <td class="secao">CEP:</td>
                    <td class="conteudo"><input name='cep' type='text' class='campotexto' id='cep' size='12' 
                    onFocus="document.all.cep.style.background='#CCFFCC'"
                    onBlur="document.all.cep.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onKeyUp="pula(9,this.id,tel_fixo.id)" 
                    OnKeyPress="formatar('#####-###', this)" ></td>
                    </tr>
                    <tr>
                    <td class="secao">Telefone Fixo:</td>
                    <td class="conteudo" colspan="2"><input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' size='13'
                    onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,tel_cel.id)" 
                    onFocus="document.all.tel_fixo.style.background='#CCFFCC'"
                    onBlur="document.all.tel_fixo.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Telefone M&oacute;vel:</td>
                    <td class="conteudo" colspan="2"><input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='13' 
                    onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,tel_rec.id)" 
                    onFocus="document.all.tel_cel.style.background='#CCFFCC'"
                    onBlur="document.all.tel_cel.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Recado:</td>
                    <td class="conteudo"><input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='13' 
                    onKeyPress="return(TelefoneFormat(this,event))"  onKeyUp="pula(13,this.id,data_nasci.id)" 
                    onFocus="document.all.tel_rec.style.background='#CCFFCC'"
                    onBlur="document.all.tel_rec.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    </tr>
                    -->

                    <tr>
                        <td class="secao">Data de Nascimento:</td>
                        <td class="conteudo" colspan="3">
                            <input name='data_nasci' type='text' id='data_nasci' size='10' class='campotexto'
                                   onKeyUp="mascara_data(this);
                            pula(10, this.id, naturalidade.id)" maxlength='10' 
                                   onFocus="document.all.data_nasci.style.background = '#CCFFCC'" 
                                   onBlur="document.all.data_nasci.style.background = '#FFFFFF'" 
                                   style="background:#FFFFFF"></td>
                    </tr>
                    <!---
                    <td class="secao">Naturalidade:</td>
                    <td class="conteudo" colspan="3"><input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='14' onFocus="document.all.naturalidade.style.background='#CCFFCC'"
                    onBlur="document.all.naturalidade.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                    <td class="secao">Nacionalidade:</td>
                    <td class="conteudo" colspan="4">
                    <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='12' 
                    onFocus="document.all.nacionalidade.style.background='#CCFFCC'"
                    onBlur="document.all.nacionalidade.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()">
                    </td>
                    <td class="secao">Estado Civil:</td>
                    <td class="conteudo" colspan="2"><select name='civil' class='campotexto' id='civil' >
                    <option>Solteiro</option>
                    <option>Casado</option>
                    <option>Viúvo</option>
                    <option>Sep. Judicialmente</option>
                    <option>Divorciado</option>
                    </select></td>
                    </tr>
                    <tr>
                    <td class="secao">CTPS:</td>
                    <td class="conteudo">
                    <input name='ctps' type='text' class='campotexto' id='ctps' size='10' 
                    onFocus="document.all.ctps.style.background='#CCFFCC'"
                    onBlur="document.all.ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Série:</td>
                    <td class="conteudo"><input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='8' 
                    onFocus="document.all.serie_ctps.style.background='#CCFFCC'"
                    onBlur="document.all.serie_ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">UF:</td>
                    <td class="conteudo"><input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' 
                    onFocus="document.all.uf_ctps.style.background='#CCFFCC'"
                    onBlur="document.all.uf_ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" 
                    onChange="this.value=this.value.toUpperCase()" onKeyUp="pula(2,this.id,pis.id)" ></td>
                    <td class="secao">PIS:</td>
                    <td class="conteudo"><input name='pis' type='text' class='campotexto' id='pis' size='15' 
                    onFocus="document.all.pis.style.background='#CCFFCC'"
                    onBlur="document.all.pis.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    </tr>
                    <tr>
                    <td class="secao">Nº do RG:</td>
                    <td class="conteudo" colspan="3">
                    <input name='rg' type='text' class='campotexto' id='rg' 
                    onFocus="document.all.rg.style.background='#CCFFCC'"
                    onBlur="document.all.rg.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" size='12' maxlength=13 OnKeyPress="formatar('##.###.###-#', this)">
                    </td>
                    <td class="secao">Orgão Expedidor:</td>
                    <td class="conteudo"><input name='orgao' type='text' class='campotexto' id='orgao' size='8' 
                    onFocus="document.all.orgao.style.background='#CCFFCC'"
                    onBlur="document.all.orgao.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    <td class="secao">Data:</td>
                    <td class="conteudo"><input name='data_rg' type='text' id='data_ctps' size='10' class='campotexto'
                    onKeyUp="mascara_data(this); pula(10,this.id,cpf.id)" maxlength='10' 
                    onFocus="document.all.data_rg.style.background='#CCFFCC'" 
                    onBlur="document.all.data_rg.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    </tr>
                    <tr>
                    <td class="secao">CPF:</td>
                    <td class="conteudo"><input name='cpf' type='text' class='campotexto' id='cpf' size='14' maxlength='14'
                    OnKeyPress="formatar('###.###.###-##', this)" onKeyUp="pula(14,this.id,n_titulo.id)" 
                    onFocus="document.all.cpf.style.background='#CCFFCC'" 
                    onBlur="document.all.cpf.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Nº Título de Eleitor:</td>
                    <td class="conteudo"><input name='titulo' type='text' class='campotexto' id='n_titulo' size='10'
                    onFocus="document.all.n_titulo.style.background='#CCFFCC'" 
                    onBlur="document.all.n_titulo.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Zona:</td>
                    <td class="conteudo"><input name='zona' type='text' class='campotexto' id='n_zona' size='3' 
                    onFocus="document.all.n_zona.style.background='#CCFFCC'" 
                    onBlur="document.all.n_zona.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    <td class="secao">Seção:</td>
                    <td class="conteudo"><input name='secao' type='text' class='campotexto' id='secao' size='3' 
                    onFocus="document.all.secao.style.background='#CCFFCC'" 
                    onBlur="document.all.secao.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    </tr>
                    <tr>
                    <td class="secao">Filiação - Pai:</td>
                    <td class="conteudo" colspan="7">
                    <input name='pai' type='text' class='campotexto' id='pai' size='75' 
                    onFocus="document.all.pai.style.background='#CCFFCC'" 
                    onBlur="document.all.pai.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                    <td class="secao">Filiação - Mãe:</td>
                    <td class="conteudo" colspan="7">
                    <input name='mae' type='text' class='campotexto' id='mae' size='75' 
                    onFocus="document.all.mae.style.background='#CCFFCC'" 
                    onBlur="document.all.mae.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                    <td class="secao">Estuda Atualmente:</td>
                    <td class="conteudo" colspan="7"><input type='radio' checked name='estuda' value='sim' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? 'none' : 'none' ;"> Sim&nbsp;&nbsp;<input type='radio' name='estuda' value='nao' onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? '' : '' ;"> Não</td>
                    </tr>
                    <tr id='linha_termino' style='display:none'>
                    <td class="secao">Término em:</td>
                    <td class="conteudo" colspan="7"><input name='escola_dia' type='text' class='campotexto' value='30' size='2' maxlength=2 > / <input name='escola_mes' type='text' class='campotexto' size='2' maxlength=2 value='11'> / <input name='escola_ano' type='text' class='campotexto' size='4' maxlength=4></td>
                    </tr>
                    <tr>
                    <td class="secao">Escolaridade:</td>
                    <td class="conteudo" colspan="3">
                    <select name='escolaridade'>";
            <? $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
            while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
                ?>
                        <option value="<?= $escolaridade['id'] ?>">
                        <?= $escolaridade['cod'] ?> - <?= $escolaridade['nome'] ?>
                        </option>
                    <? } ?>
                    </select>
                    </td>
                    <td class="secao">Instituição:</td>
                    <td class="conteudo"><input name='instituicao' type='text' class='campotexto' id='titulo' size='20' 
                    onFocus="document.all.instituicao.style.background='#CCFFCC'" 
                    onBlur="document.all.instituicao.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    <td class="secao">Atividade:</td>
                    <td class="conteudo"><input name='curso' type='text' class='campotexto' id='zona' size='10' 
                    onFocus="document.all.curso.style.background='#CCFFCC'" 
                    onBlur="document.all.curso.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                    </tr>
                    <tr>
                      <td class="secao">Foto:</td>
                      <td class="conteudo" colspan="7">
                      
                      <table width='100%' border='0' cellspacing='0' cellpadding='0' class='linha'>
                      <tr>
                      <td width='8%'><label>
                      <input name='foto' type='checkbox' id='foto' onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/>Sim</label></td>
                      <td width="77%">
                      <span style='display:none' id='logomarca'> &nbsp;&nbsp;&nbsp;&nbsp;
                        selecione:
                      <input type='file' name='arquivo' id='arquivo' class='campotexto'>
                      <font size='1' color='#999999'>(.jpg, .png, .gif, .jpeg)</font>  </span></td>
                      </tr>
                      </table>  </td>
                    </tr>
                    <tr>
                      <td height="30" colspan="8" class="fundo_claro"><div class="titulo">Informações Bancárias</div></td>
                    </tr>
                    <tr>
                      <td class="secao">Banco:</td>
                      <td class="conteudo" colspan="3">
                        <input name='banco' type='text' class='campotexto' id='banco' size='15' onFocus="document.all.banco.style.background='#CCFFCC'" 
                    onBlur="document.all.banco.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" onChange="this.value=this.value.toUpperCase()"></td>
                      <td class="secao">Agência:</td>
                      <td class="conteudo"><input name='agencia' type='text' class='campotexto' id='agencia' size='7' 
                    onFocus="document.all.agencia.style.background='#CCFFCC'" 
                    onBlur="document.all.agencia.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                      <td class="secao">Nº da Conta:</td>
                      <td class="conteudo"><input name='conta' type='text' class='campotexto' id='conta' size='15'
                    onFocus="document.all.conta.style.background='#CCFFCC'" 
                    onBlur="document.all.conta.style.background='#FFFFFF'" 
                    style="background:#FFFFFF"></td>
                    </tr>-->
                    <tr>
                        <td height="30" colspan="8" class="fundo_claro"><div class="titulo">Informações de Login</div></td>
                    </tr>
                    <tr>
                        <td class="secao">Login:</td>
                        <td class="conteudo"><input name='login' type='text' class='campotexto' id='login' size='10' 
                                                    onFocus="document.all.login.style.background = '#CCFFCC'" 
                                                    onBlur="document.all.login.style.background = '#FFFFFF'" 
                                                    style="background:#FFFFFF">  
                        <td class="secao">Senha padrão:</td>
                        <td class="conteudo"><input name='senha' type='text' class='campotexto' id='senha' size='7' value='123456'
                                                    onFocus="document.all.senha.style.background = '#CCFFCC'" 
                                                    onBlur="document.all.senha.style.background = '#FFFFFF'" 
                                                    style="background:#FFFFFF"></td>
                        <td class="secao">Tipo de Conta:</td>
                        <td class="conteudo"><select name='tipo_usuario' class='campotexto'>
                                <?php
                                $result_tipo = mysql_query("SELECT * FROM funcionario_tipo order by id_funcionario_tipo ", $conn);
                                while ($row_tipo = mysql_fetch_array($result_tipo)) {
                                    print "<option value=$row_tipo[id_funcionario_tipo]>$row_tipo[funcionario_tipo]</option>";
                                }
                                ?>
                            </select></td>
                    </tr>


                    <tr>
                        <td height="30" colspan="8" class="fundo_claro"><div class="titulo">E-mail:</div></td>
                    </tr>
                    <tr>
                        <td>MASTER:</td>
                        <td colspan="7"> 

            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
            while ($row_master = mysql_fetch_assoc($qr_master)):

                echo '<input type="checkbox" name="master_email[]" value="' . $row_master['id_master'] . '" class="master_email"/> ' . $row_master['nome'] . ' &nbsp;';

            endwhile;
            ?>
                        </td>
                        <td>

                        </td>

                    </tr>
                    <tr>
                        <td colspan="9">
                            <table  border ='0' width="980">
            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
            while ($row_master = mysql_fetch_assoc($qr_master)):
                ?>	

                                    <tr class="master_<?php echo $row_master['id_master']; ?>" style="display:none;">
                                        <td colspan="4" align="left" width="980" heigth="20" bgcolor="#F0F0F0"> 
                                            <strong> <?php echo $row_master['nome']; ?></strong>
                                        </td>
                                    </tr>
                                    <tr class="master_<?php echo $row_master['id_master']; ?>" style="display:none;">
                                        <td width="100" align="right"><strong>E-mail:</strong></td>
                                        <td width="200"><input type="text" name="email[]" class="email" /></td>
                                        <td width="150" align="right"><strong>Senha do e-mail:</strong></td>
                                        <td width="400"><input type="password" name="senha_email[]" rel="<?php echo $row_master['id_master']; ?>" class="senha_email"/> <span class="menssagem">daffsd</span>

                                        </td>
                                    </tr>


                <?php
            endwhile;
            ?>
                            </table>
                        </td>
                    </tr>




                    <tr>
                        <td height="30" colspan="8" class="fundo_claro"><div class="titulo">Gerenciamento de Acesso a Intranet</div></td>
                    </tr>

                    <tr><td>&nbsp; </td></tr>
            <?php
////CONTROLE DE ACESSO ADAS REGIõES
            $array_status = array(1 => 'REGIÕES ATIVAS', 0 => 'REGIÕES INATIVAS');

            foreach ($array_status as $status => $nome_status) {
                ?>
                        <tr>
                            <td bgcolor="#EFEFEF" align="center" valign="top"><?php echo $nome_status; ?></td>
                            <td  colspan="6">
                                <table width="100%" cellspacing="0">
                        <?php
                        if ($status == 0) {
                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                        } else {
                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                        }

                        while ($row_regioes = mysql_fetch_assoc($qr_regioes)):
                            if ($row_regioes['id_regiao'] == 38 and $row_regioes['id_regiao'] == 16)
                                continue;

                            $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                            $row_master = mysql_fetch_assoc($qr_master);


                            if ($row_master['id_master'] != $master_anterior) {
                                echo '<tr  bgcolor="#C7E2E2"><td align="left">' . $row_master['nome'] . ' 
					 <span style="float:right;"> <input name="todos_master"  type="checkbox" value="' . $row_regioes['id_master'] . '_' . $status . '" class="todos_master"  />Marcar/Desmarcar todos </span>
					  </td>
					  </tr>';
                            }

                            echo '<tr bgcolor="#D9ECFF">
						<td>
							<input name="empresas[]"  type="hidden" value="' . $row_regioes['id_master'] . '"/>
							<input name="regioes_permitidas[' . $row_regioes['id_master'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"   class="master_' . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
						</td>
     				</tr>';

                            $master_anterior = $row_master['id_master'];

                        endwhile;

                        echo '<tr><td>&nbsp;</td></tr>';

                        unset($master_anterior);
                        ?>  
                                </table>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                                <?php
                                } //fim foreach
///////////////////////////////////////
                                ?>
                    <tr><td>&nbsp; </td></tr>
            <?php
            $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE 1");
            while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):



                echo '<tr bgcolor="#EFEFEF">
			<td colspan="8" align="center"><strong>' . $row_pagina['botoes_pg_nome'] . '</strong><br></td>
			</tr>';


                ///PERMISSÔES PARA OS RELATÓRIOS DO FINANCEIRO
                if ($row_pagina['botoes_pg_id'] == 3) {

                    echo '<tr>
										<td style="background-color: #EFEFEF;" align="center">PÁGINA INICIAL</td>
										<td>
									';

                    $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_pagina_id = '$row_pagina[botoes_pg_id]'");
                    while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                        echo "<input type='checkbox' name='acoes[]' value='" . $row_acoes['acoes_id'] . "' /> " . $row_acoes['acoes_nome'] . "<br>";

                    endwhile;
                }

                echo '</td></tr>';
                ////////////////////////////////////////////


                $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = '$row_pagina[botoes_pg_id]'");
                while ($row_botoes_menu = mysql_fetch_assoc($qr_botoes_menu)):

                    $todos++;
                    ?> 

                            <tr>
                                <td height="30" bgcolor="FFF" align="center" valign="top" style="background-color: #F5F5F5" >

                            <?php echo $row_botoes_menu['botoes_menu_nome'] ?><br><br><br>

                                    <input type="checkbox" class="tipo_menu"  name="todos" value="<?php echo $todos; ?>">Marcar/Desmarcar todos

                                </td>

                                <td colspan="7">         
                                    <?php
                                    $qr_botoes = mysql_query("SELECT * FROM botoes WHERE   botoes_menu = '$row_botoes_menu[botoes_menu_id]'  ORDER BY  botoes_menu ASC");
                                    $contador_icone = 0;

                                    while ($row_botoes = mysql_fetch_assoc($qr_botoes)):


                                        ////permisões pra deletar, exluir e etc;.
                                        $qr_acoes = mysql_query("SELECT * FROM acoes WHERE   botoes_id = '$row_botoes[botoes_id]' ORDER BY tp_contratacao_id ASC") or die(mysql_error());
                                        if (mysql_num_rows($qr_acoes) != 0) {
                                            ?>

                                            <table border="0"  cellspacing="0">
                                                <tr bgcolor="#C7E2E2">

                                                    <td colspan="2">
                                                        <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>" class="<?php echo $todos; ?>"> <?php echo $row_botoes['botoes_nome']; ?> -  Ações
                                                    </td>  
                                                </tr>


                            <?php
                            ///BOTÕES VISUALIZAR OBRIGAÇÃO E EXCLUIR OBRIGAÇÃO			
                            if ($row_botoes['botoes_id'] == 82) {

                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):


                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" /> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                endwhile;
                            } else
                            //CONDIÇÃO PARA EXIBIR AS REGIÕES PERMITIDAS PARA VISUALIZAÇÃO DA FOLHA 
                            if ($row_botoes['botoes_id'] == 33 or $row_botoes['botoes_id'] == 60) {


                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):


                                    //ações
                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" /> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                endwhile;


                                echo'<tr  bgcolor="#D9ECFF"><td colspan="2">';

                                foreach ($array_status as $status => $nome_status) {



                                    if ($status == 0) {
                                        $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES INATIVAS</td></tr>';
                                    } else {
                                        $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES ATIVAS</td></tr>';
                                    }

                                    while ($row_regioes = mysql_fetch_assoc($qr_regioes)):

                                        $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                        $row_master = mysql_fetch_assoc($qr_master);

                                        if ($row_master['status'] == 0)
                                            continue;




                                        if ($row_master['id_master'] != $master_anterior) {
                                            echo '<tr  bgcolor="#C7E2E2"><td align="left" colspan="2">' . $row_master['nome'] . ' 
																	 <span style="float:right;"> <input name=""  type="checkbox" value="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td></tr>';
                                        }

                                        echo '<tr bgcolor="#D9ECFF">
																		<td colspan="2">
																			<input name="acoes_folhas[' . $row_botoes['botoes_id'] . ']" type="hidden" value="' . $row_acoes['acoes_id'] . '"/>
																			<input name="regiao_folhas[' . $row_botoes['botoes_id'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"   class="master_' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
																		</td>
																	</tr>';

                                        $master_anterior = $row_master['id_master'];

                                    endwhile;
                                }
                            } else if ($row_botoes['botoes_id'] == 6) {





                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):


                                    if ($row_acoes['tp_contratacao_id'] != $tipo_contratacao_anterior) {

                                        $nome_tipo = mysql_result(mysql_query("SELECT tipo_contratacao_nome FROM tipo_contratacao WHERE tipo_contratacao_id = '$row_acoes[tp_contratacao_id]'"), 0);

                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" >' . $nome_tipo . ' <span style="float:right;"> <input name=""  type="checkbox" value="acoes_' . $row_acoes['tp_contratacao_id'] . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td>
												
												</tr>';
                                    }
                                    else


                                    //ações
                                        echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" class="master_acoes_' . $row_acoes['tp_contratacao_id'] . '"/> ' . '(' . $row_acoes['acoes_id'] . ') ' . $row_acoes['acoes_nome'] . '</td></tr>';

                                    $tipo_contratacao_anterior = $row_acoes['tp_contratacao_id'];
                                endwhile;
                            }


                            echo '</td></tr>';




                            echo '</table>';
                        } else {
                            ?> 
                                                <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>" class="<?php echo $todos; ?>"> <?php echo '(' . $row_botoes['botoes_id'] . ') ' . $row_botoes['botoes_nome']; ?><br>
                                            <?php
                                            }
                                        endwhile;

                                        echo '</td>
		</tr>
		
		<tr><td>&nbsp; </td</tr>';
                                        unset($checked);
                                    endwhile;

                                endwhile;
                                ?>


                                <tr>
                                    <td height="66" colspan="8" align=center bgcolor="#FFFFFF"><font color='red'> Atenção: <BR> - O usuário modificará a senha no seu 1º logon <BR> - Verifique todos os dados acima atentamente, após verificação clique em CADASTRAR</font>  </td>
                                </tr>
                                <tr>
                                    <td height="58" colspan='8' align='center' bgcolor="#FFFFFF">
                                        <input type='submit' name='Submit4' value='CADASTRAR' />    
                                        <input type='hidden' name='id_cadastro' value='3'>
                                    </td>
                                </tr>
                            </table>
                            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

                            <script>
                        $(function() {

                            $('#empresa').change(function() {

                                var id_master = $(this).val();

                                $.post('cadastro.php', {ajax: 1, id_master: id_master}, function(resposta) {


                                    $('#id_regiao').html(resposta);
                                })


                            })

                        });


                        function validaForm() {

                            d = document.form1;
                            if (d.funcao.value == "") {
                                alert("O campo Função deve ser preenchido!");
                                d.funcao.focus();
                                return false;
                            }
                            if (d.locacao.value == "") {
                                alert("O campo Lotação deve ser preenchido!");
                                d.locacao.focus();
                                return false;
                            }
                            if (d.salario.value == "") {
                                alert("O campo Salário deve ser preenchido!");
                                d.salario.focus();
                                return false;
                            }
                            if (d.nome.value == "") {
                                alert("O campo Nome deve ser preenchido!");
                                d.nome.focus();
                                return false;
                            }
                            if (d.nome1.value == "") {
                                alert("O campo Nome para Exibição deve ser preenchido!");
                                d.nome1.focus();
                                return false;
                            }
                            if (d.login.value == "") {
                                alert("O campo Login deve ser preenchido!");
                                d.login.focus();
                                return false;
                            }
                            return true;
                        }
                            </script>

            <?php
            break;


        case 4:        //CASO O ID SEJA 4 ELE VAI RODAR O - CADASTRO DE PARTICIPANTES -

            $projeto = $_REQUEST['pro'];
            $id_regiao = $_REQUEST['regiao'];

            $sql_pro = "SELECT * FROM projeto where id_projeto = $projeto AND status_reg = '1'";
            $result_pro = mysql_query($sql_pro, $conn);
            $row = mysql_fetch_array($result_pro);

            $result_grupo = mysql_query("SELECT * FROM curso where id_regiao = '$id_regiao' and tipo = '1' ORDER BY nome");

// PEGANDO O MAIOR NUMERO
            $resut_maior = mysql_query("SELECT CAST(campo3 AS UNSIGNED) campo30 , 
MAX(campo3) 
FROM autonomo 
WHERE id_regiao= '$id_regiao' 
AND id_projeto ='$projeto' 
AND campo3 != 'INSERIR' 
GROUP BY campo30 DESC ");
            $row_maior = mysql_num_rows($resut_maior);

            $codigo = $row_maior + 1;

            print "
<style type='text/css'>
<!--

.style1 {
font-family: Arial, Helvetica, sans-serif;
font-weight: bold;
font-size: 12px;
}
.style3 {font-size: 12px}
.style6 {color: #003300}
.style7 {font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
.style37 {font-family: Arial, Helvetica, sans-serif}
.style39 {font-family: Arial, Helvetica, sans-serif; color: #003300;}
.style40 {font-weight: bold; font-family: Arial, Helvetica, sans-serif;}
.style41 {
color: #FFFFFF;
font-size: 16px;
}
.style42 {font-weight: bold; color: #003300; font-family: Arial, Helvetica, sans-serif;}
.style43 {font-family: Arial, Helvetica, sans-serif; color: #FFFFFF; font-size: 14px; }
.style44 {font-family: Arial, Helvetica, sans-serif; color: #003300; font-size: 14px; }
.style45 {font-size: 14px}
.style46 {font-family: Arial, Helvetica, sans-serif; font-size: 14px; }
.style47 {
font-size: 16px;
color: #FF0000;
}
.style48 {
font-size: 8px;
color: #FF0000;
}
.style49 {font-size: 9px}
-->
</style>";
            ?>

                            <form action='cadastro2.php' method='post' name='form1' enctype='multipart/form-data' onSubmit="return validaForm()">

                                <table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                                    <tr>
                                        <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                                    </tr>
                                    <tr>
                                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Formulário de Cadastro de Participante</div></td>
                                    </tr>
                                </table>

                                <table width='80%' border='0' cellpadding='0' cellspacing='0' align='center'>
                                    <tr>
                                        <td height="28" colspan=4><div align=center class='title' style="font-weight:bold; font-size:16px;"> <BR><BR> </div>
                                        </td>
                                    </tr>
                                </table>
                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
                                    <tr>
                                        <td height="32" colspan='2' class="fundo_claro"><div class="titulo">Dados do Projeto</div></td>
                                    </tr>
                                    <tr>
                                        <td bgcolor='#E8E8E8' align="right">Código:&nbsp;</td>
                                        <td>
                                            <input name='codigo2' type='text' class='campotexto' id='codigo2' size='10' value='<?= $codigo ?>' disabled 
                                                   onFocus="document.all.codigo.style.background = '#CCFFCC'"
                                                   onBlur="document.all.codigo.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;' />
                                            <input name='codigo' type='text' class='campotexto' id='codigo' size='10' value='<?= $codigo ?>' style='display:none' />
                                        </td>
                                    </tr>

                                    <tr>
                                        <td height='28' bgcolor='#E8E8E8'><div align='right' class='style39'>
                                                Tipo Contratação:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF'>
                                            &nbsp;
                                            <label class='style39'>
                                                <input name='contratacao' type='radio' id='contratacao' value='1' checked onClick="document.all.linhacoop.style.display = 'none'"/> Autônomo</label>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td height='28' bgcolor='#E8E8E8' ><div align='right' class='style39'><span class='style37'>Projeto:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' > <span class='style6 style37'>&nbsp; &nbsp;<?= "$row[id_projeto] - $row[nome]" ?> &nbsp;&nbsp;/ <span class='style37'>&nbsp;&nbsp;Região: <?= "$row[id_regiao] - $row[regiao]" ?>
                                                </span></span></td>
                                    </tr>
                                    <tr>
                                        <td height='28' bgcolor='#E8E8E8' ><div align='right' class='style39'><span class='style37'>Atividade:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <select name='idcurso' id='idcurso' class='campotexto'>

            <?php
            $result_grupo = mysql_query("SELECT * FROM curso where id_regiao = '$id_regiao' and campo3 = '$projeto' and tipo = '1' ORDER BY campo3");
            while ($row_grupo = mysql_fetch_array($result_grupo)) {
                $salario = number_format($row_grupo['salario'], 2, ",", ".");
                print "
<option value='$row_grupo[id_curso]'>$row_grupo[id_curso] - $row_grupo[campo2] / Valor: $salario - $row_grupo[campo1]</option>";
            }
            ?>
                                                </select>

                                            </span></td>
                                    </tr>
                                    <tr>
                                        <td height='28' bgcolor='#E8E8E8' ><div align='right' class='style39'><span class='style37'>Unidade:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <select name='locacao' id='locacao' class='campotexto'>
            <?php
            $result_unidade = mysql_query("SELECT * FROM unidade where id_regiao = $row[id_regiao] and 
campo1 = '$projeto' ORDER BY unidade", $conn);
            while ($row_unidade = mysql_fetch_array($result_unidade)) {
                print "<option value='$row_unidade[unidade]'>$row_unidade[id_unidade] - $row_unidade[unidade]</option>";
            }
            ?>
                                                </select>
                                            </span></td>
                                    </tr>

                                    <tr id='linhacoop' style='display:none'>
                                        <td height='28' bgcolor='#E8E8E8' ><div align='right' class='style39'><span class='style37'>Cooperativa:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <select name='cooperativa' id='cooperativa' class='campotexto'>
            <?php
            $RECoop = mysql_query("SELECT id_coop,nome FROM cooperativas where id_regiao = '$row[id_regiao]' ORDER BY nome");
            while ($RowCoop = mysql_fetch_array($RECoop)) {
                print "<option value='$RowCoop[0]'>$RowCoop[0] - $RowCoop[nome]</option>";
            }
            ?>
                                                </select>
                                            </span></td>
                                    </tr>


                                </table>


                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
                                    <tr>
                                        <td height="32" colspan='8' class="fundo_claro"><div class="titulo">Dados Cadastrais</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td width='13%' height="28" bgcolor='#E8E8E8' >
                                            <div align='right'>Nome<span class='style37'>:&nbsp;</span></div>
                                        </td>
                                        <td width='87%' height="28" colspan='7' bgcolor='#FFFFFF' ><div align='left' class='style6 style3 style40 style42'>
                                                <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                        <input name='nome' type='text' class='campotexto' id='nome' size='75'
                                                               onFocus="document.all.nome.style.background = '#CCFFCC'"
                                                               onBlur="document.all.nome.style.background = '#FFFFFF'" 
                                                               style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                    </span></div>
                                            </div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'>Endereco:&nbsp;
                                            </div></td>
                                        <td height="28" colspan='7' bgcolor='#FFFFFF' >
                                            <div align='left'>&nbsp;&nbsp;
                                                <input name='endereco' type='text' class='campotexto' id='endereco' size='75' 
                                                       onFocus="document.all.endereco.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.endereco.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>

                                            </div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'>Bairro:&nbsp;
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='bairro' type='text' class='campotexto' id='bairro' size='15' 
                                                           onFocus="document.all.bairro.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.bairro.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                    &nbsp;&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'><span class='style37'> Cidade:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='cidade' type='text' class='campotexto' id='cidade' size='12' 
                                                           onFocus="document.all.cidade.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.cidade.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                </span>
                                            </div></td>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'><span class='style37'>UF:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' 
                                                           onFocus="document.all.uf.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.uf.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"
                                                           onkeyup="pula(2, this.id, cep.id)" />
                                                </span>
                                            </div></td>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'><span class='style37'>CEP:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><div align='left' class='style6 style3 style40 style42'>
                                                <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                        <input name='cep' type='text' class='campotexto' id='cep' size='10' maxlength='9' 
                                                               style='background:#FFFFFF; text-transform:uppercase;'
                                                               onFocus="document.all.cep.style.background = '#CCFFCC'" 
                                                               onBlur="document.all.cep.style.background = '#FFFFFF'"
                                                               OnKeyPress="formatar('#####-###', this)" 
                                                               onKeyUp="pula(9, this.id, tel_fixo.id)" />
                                                    </span></div>
                                            </div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'><span class='style37'>Telefones:&nbsp;</span>
                                            </div></td>
                                        <td height="28" colspan='2' bgcolor='#E8E8E8' >
                                            <div align='center'><span class='style37'>Fixo:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='tel_fixo' type='text' id='tel_fixo' size='14' 
                                                           onKeyPress="return(TelefoneFormat(this, event))" 
                                                           onKeyUp="pula(13, this.id, tel_cel.id)" 
                                                           onFocus="document.all.tel_fixo.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.tel_fixo.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' class='campotexto'>
                                                </span>
                                            </div></td>
                                        <td height="28" bgcolor='#E8E8E8' > 
                                            <div align='right'><span class='style37'>Cel:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='14' onKeyPress="return(TelefoneFormat(this, event))" 
                                                           onKeyUp="pula(13, this.id, tel_rec.id)" 
                                                           onFocus="document.all.tel_cel.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.tel_cel.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' />
                                                    &nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right'><span class='style37'>Recado:&nbsp;</span>
                                            </div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <div align='left'><span class='style37'>&nbsp;&nbsp;
                                                    <input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='14' onKeyPress="return(TelefoneFormat(this, event))" 
                                                           onKeyUp="pula(13, this.id, data_nasci.id)" 
                                                           onFocus="document.all.tel_rec.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.tel_rec.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' />
                                                </span>
                                            </div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'><span class='style37'>Data de Nascimento:&nbsp;</span></div></td>
                                        <td height="28" colspan='2' bgcolor='#FFFFFF' ><span class='style6 style37'> &nbsp;&nbsp;
                                                <input name='data_nasci' type='text' id='data_nasci' size='10' class='campotexto'
                                                       onKeyUp="mascara_data(this);
                            pula(10, this.id, naturalidade.id)"
                                                       onFocus="document.all.data_nasci.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_nasci.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'>
                                            </span> <span class='style6 style37'>&nbsp;</span></td>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right' class='style39'>Naturalidade:&nbsp;</div></td>
                                        <td height="28" colspan='2' bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10'  
                                                   onFocus="document.all.naturalidade.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.naturalidade.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nacionalidade:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' 
                                                   onFocus="document.all.nacionalidade.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.nacionalidade.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Estado Civil:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <select name='civil' class='campotexto' id='civil'>
                                                <option>Solteiro</option>
                                                <option>Casado</option>
                                                <option>Viúvo</option>
                                                <option>Sep. Judicialmente</option>
                                                <option>Divorciado</option>
                                            </select>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'  class='style39'>Sexo:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            <table align='left'>
                                                <tr height='30'>
                                                    <td class='style39'><span class='style37'>
                                                            &nbsp;&nbsp;
                                                            <label>
                                                                <input type='radio' name='sexo' value='M' checked='checked' /> Masculino </label></span></td>
                                                    <td class='style39'><span class='style37'>
                                                            &nbsp;&nbsp;
                                                            <label>		
                                                                <input type='radio' name='sexo' value='F' />Feminino</label></span></td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                    <tr>
                                        <td height="28" colspan='8' class="fundo_claro"><div class="titulo">Dados da Fam&iacute;lia e Educacionais</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Filiação - Pai:&nbsp;</div></td>
                                        <td height="28" colspan='7' bgcolor='#FFFFFF' ><span class='style6 style37'>&nbsp;&nbsp;
                                                <input name='pai' type='text' class='campotexto' id='pai' size='75' 
                                                       onFocus="document.all.pai.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.pai.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Pai:</span>&nbsp;&nbsp;

                                                <input name='nacionalidade_pai' type='text' class='campotexto' id='nacionalidade_pai' size='15' 
                                                       onFocus="document.all.nacionalidade_pai.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.nacionalidade_pai.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>	

                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Filiação - Mãe:&nbsp;</div></td>
                                        <td height="28" colspan='7' bgcolor='#FFFFFF' ><span class='style6 style37'>&nbsp;&nbsp;
                                                <input name='mae' type='text' class='campotexto' id='mae' size='75' 
                                                       onFocus="document.all.mae.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.mae.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='style39'>Nacionalidade Mãe:</span>&nbsp;&nbsp;

                                                <input name='nacionalidade_mae' type='text' class='campotexto' id='nacionalidade_mae' size='15' 
                                                       onFocus="document.all.nacionalidade_mae.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.nacionalidade_mae.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>	



                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Estuda Atualmente?&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><table align='left'>
                                                <tr height='30'>
                                                    <td class='style39'><span class='style37'>&nbsp;&nbsp;
                                                            <input type='radio' name='estuda' value='sim' checked='checked' />
                                                            SIM</span></td>
                                                    <td class='style39'><span class='style37'>&nbsp;&nbsp;
                                                            <input type='radio' name='estuda' value='não' />
                                                            NÃO</span></td>
                                                </tr>
                                            </table></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Término em:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <input name='data_escola' type='text' id='data_escola' size='10' class='campotexto'
                                                   onKeyUp="mascara_data(this);
                            pula(10, this.id, escolaridade.id)" maxlength='10' 
                                                   onFocus="document.all.data_escola.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.data_escola.style.background = '#FFFFFF'" 
                                                   style="background:#FFFFFF">
                                        </td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Escolaridade:&nbsp;</div></td>
                                        <td height="28" colspan='2' bgcolor='#FFFFFF' ><span class='style6 style37'>&nbsp;&nbsp;&nbsp;
                                                <select name='escolaridade'>";
            <? $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
            while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
                ?>
                                                        <option value="<?= $escolaridade['id'] ?>">
                                                        <?= $escolaridade['cod'] ?> - <?= $escolaridade['nome'] ?>
                                                        </option>
                                                    <? } ?>
                                                </select>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Instituíção:&nbsp;</div></td>
                                        <td height="28" colspan='2' bgcolor='#FFFFFF' ><span class='style6 style37'>&nbsp;
                                                <input name='instituicao' type='text' class='campotexto' id='titulo' size='20' 
                                                       onFocus="document.all.instituicao.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.instituicao.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Atividade:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style6 style37'>&nbsp;&nbsp;
                                                <input name='curso' type='text' class='campotexto' id='zona' size='10' 
                                                       onFocus="document.all.curso.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.curso.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Número de Filhos:&nbsp;</div></td>
                                        <td height="28" colspan='7' bgcolor='#FFFFFF' >&nbsp;&nbsp;&nbsp;
                                            <input name='filhos' type='text' class='campotexto  style37' id='filhos' size='2' 
                                                   onFocus="document.all.filhos.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.filhos.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/>
                                            <div align='right'></div>    <div align='right'></div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nome:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;&nbsp;
                                            <input name='filho_1' type='text' class='campotexto' id='filho_1' size='50' 
                                                   onFocus="document.all.filho_1.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.filho_1.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>nascimento:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;&nbsp;
                                                <input name='data_filho_1' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_1'
                                                       onFocus="document.all.data_filho_1.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_filho_1.style.background = '#FFFFFF'" 
                                                       onKeyUp="mascara_data(this);
                            pula(10, this.id, filho_2.id)"
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nome:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
                                                <input name='filho_2' type='text' class='campotexto' id='filho_2' size='50' 
                                                       onFocus="document.all.filho_2.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.filho_2.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span>      <div align='right' class='style39'></div></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>nascimento:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;&nbsp;
                                                <input name='data_filho_2' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_2'
                                                       onFocus="document.all.data_filho_2.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_filho_2.style.background = '#FFFFFF'" 
                                                       onKeyUp="mascara_data(this);
                            pula(10, this.id, filho_3.id)"
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nome:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
                                                <input name='filho_3' type='text' class='campotexto' id='filho_3' size='50' 
                                                       onFocus="document.all.filho_3.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.filho_3.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                                &nbsp;</span>      <div align='right' class='style39'></div></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>nascimento:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;&nbsp;
                                                <input name='data_filho_3' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_3'
                                                       onFocus="document.all.data_filho_3.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_filho_3.style.background = '#FFFFFF'" 
                                                       onKeyUp="mascara_data(this);
                            pula(10, this.id, filho_4.id)"
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nome:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><span class='style6 style37'> &nbsp;&nbsp;&nbsp;
                                                <input name='filho_4' type='text' class='campotexto' id='filho_4' size='50' 
                                                       onFocus="document.all.filho_4.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.filho_4.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span>      <div align='right' class='style39'></div></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>nascimento:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;&nbsp;
                                                <input name='data_filho_4' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_4'
                                                       onFocus="document.all.data_filho_4.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_filho_4.style.background = '#FFFFFF'" 
                                                       onKeyUp="mascara_data(this);
                            pula(10, this.id, filho_5.id)"
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Nome:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;&nbsp;
                                                <input name='filho_5' type='text' class='campotexto' id='filho_5' size='50' 
                                                       onFocus="document.all.filho_5.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.filho_5.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span>      <div align='right' class='style39'></div></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>nascimento:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;&nbsp;
                                                <input name='data_filho_5' type='text' class='campotexto' size='12' maxlength='10' id='data_filho_5'
                                                       onFocus="document.all.data_filho_5.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_filho_5.style.background = '#FFFFFF'" 
                                                       onkeyup="mascara_data(this)"
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr>
                                        <td height="28" colspan='8' class="fundo_claro"><div class="titulo">Apar&ecirc;ncia</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' >
                                            <div align='right' class='style39'>Cabelos:&nbsp;</div></td>
                                        <td height="28" colspan='3' bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;<select name='cabelos' id='cabelos'>
                                                <option>Loiro</option>
                                                <option>Castanho Claro</option>
                                                <option>Castanho Escuro</option>
                                                <option>Ruivo</option>
                                                <option>Pretos</option>
                                            </select>
                                        </td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Olhos:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style6'><span class='style37'>&nbsp;&nbsp;
                                                    <select name='olhos' id='olhos'>
                                                        <option>Castanho Claro</option>
                                                        <option>Castanho Escuro</option>
                                                        <option>Verde</option>
                                                        <option>Azul</option>
                                                        <option>Mel</option>
                                                        <option>Preto</option>
                                                    </select>
                                                </span></span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Peso:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style6'><span class='style37'>
                                                    &nbsp;&nbsp;
                                                    <input name='peso' type='text' class='campotexto' id='peso' size='5' 
                                                           onFocus="document.all.peso.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.peso.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' />
                                                </span></span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8'><div align='right' class='style39'>Altura:&nbsp;</div></td>
                                        <td height="28" colspan='3' bgcolor='#FFFFFF' ><span class='style6'><span class='style37'>
                                                    &nbsp;&nbsp;
                                                    <input name='altura' type='text' class='campotexto' id='altura' size='5' 
                                                           onFocus="document.all.altura.style.background = '#CCFFCC'" 
                                                           onBlur="document.all.altura.style.background = '#FFFFFF'" 
                                                           style='background:#FFFFFF;' />
                                                    &nbsp;&nbsp; </span></span></td>
                                        <td bgcolor='#E8E8E8'><div align='right' class='style39'>Etnia:&nbsp;</div></td>
                                        <td bgcolor="#FFFFFF">
                                            <select name='etnia'><?
                                                $qr_etnias = mysql_query("SELECT * FROM etnias WHERE status = 'on'");
                                                while ($etnia = mysql_fetch_assoc($qr_etnias)) {
                                                    ?>
                                                    <option value="<?= $etnia['id'] ?>"><?= $etnia['nome'] ?></option>
                                                <? } ?>
                                            </select>
                                        </td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Marcas ou Cicatriz aparente:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <input name='defeito' type='text' class='campotexto' id='defeito' size='18' 
                                                   onFocus="document.all.defeito.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.defeito.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="28" bgcolor='#E8E8E8'><div align='right' class='style39'>Deficiências:&nbsp;</div></td>
                                        <td bgcolor="#FFFFFF">&nbsp;&nbsp;
                                            <select name='deficiencia'>
                                                <option value="">Não é portador de deficiência</option>
                                                <? $qr_deficiencias = mysql_query("SELECT * FROM deficiencias WHERE status = 'on'");
                                                while ($deficiencia = mysql_fetch_assoc($qr_deficiencias)) {
                                                    ?>
                                                    <option value="<?= $deficiencia['id'] ?>"><?= $deficiencia['nome'] ?></option>
            <? } ?>
                                            </select></td>
                                        <td colspan='6'>&nbsp;</td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" colspan='8' bgcolor='#FFFFFF' >
                                            <div align='center' class='style39'>
                                                Enviar Foto:
                                                <input name='foto' type='checkbox' id='foto' onClick="document.all.arquivo.style.display = (document.all.arquivo.style.display == 'none') ? '' : 'none';" value='1'/>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input name='arquivo' type='file' id='arquivo' size='60' style='display:none'/>
                                            </div></td>
                                    </tr>
                                </table>


                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
                                    <tr>
                                        <td height="30" colspan='8' class="fundo_claro"><div class="titulo">Documenta&ccedil;&atilde;o</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td width='16%' height="28" bgcolor='#E8E8E8' >
                                            <div align='right' class='style39'>Nº do RG:&nbsp;</div></td>
                                        <td width='12%' height="28" bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <input name='rg' type='text' id='rg' size='13' maxlength='14' class='campotexto'
                                                   OnKeyPress="formatar('##.###.###-###', this)" 
                                                   onFocus="document.all.rg.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.rg.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'
                                                   onkeyup="pula(14, this.id, orgao.id)">
                                        </td>
                                        <td width='15%' height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Orgão Expedidor:&nbsp;</div></td>
                                        <td width='9%' height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='orgao' type='text' class='campotexto' id='orgao' size='8'
                                                       onFocus="document.all.orgao.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.orgao.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span> </td>
                                        <td width='5%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>UF:&nbsp;</div></td>
                                        <td width='7%' height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <input name='uf_rg' type='text' class='campotexto' id='uf_rg' size='2' maxlength='2' 
                                                   onfocus="document.all.uf_rg.style.background = '#CCFFCC'" 
                                                   onblur="document.all.uf_rg.style.background = '#FFFFFF'"
                                                   onKeyUp="pula(2, this.id, data_rg.id)"
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/></td>
                                        <td width='18%' height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Data Expedição:&nbsp;</div></td>
                                        <td width='18%' height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='data_rg' type='text' class='campotexto' size='12' maxlength='10'
                                                       id='data_rg'
                                                       onFocus="document.all.data_rg.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.data_rg.style.background = '#FFFFFF'" 
                                                       onkeyup="mascara_data(this);
                            pula(10, this.id, cpf.id)"
                                                       style='background:#FFFFFF;'/>

                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>CPF:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='cpf' type='text' class='campotexto' id='cpf' size='17' maxlength='14'
                                                       OnKeyPress="formatar('###.###.###-##', this)" 
                                                       onFocus="document.all.cpf.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.cpf.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'
                                                       onkeyup="pula(14, this.id, reservista.id)"/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Certificado de Reservista:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;<span class='style39'>
                                                <input name='reservista' type='text' class='campotexto' id='reservista' 
                                                       size='18'
                                                       onFocus="document.all.reservista.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.reservista.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style39'>Nº Carteira de Trabalho:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='trabalho' type='text' class='campotexto' id='trabalho' size='15'
                                                       onFocus="document.all.trabalho.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.trabalho.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Série:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='10'
                                                       onfocus="document.all.serie_ctps.style.background = '#CCFFCC'"
                                                       onblur="document.all.serie_ctps.style.background = '#FFFFFF'" style='background:#FFFFFF;'/>
                                            </span>

                                        </td>
                                        <td height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>UF:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' 
                                                   onfocus="document.all.uf_ctps.style.background = '#CCFFCC'" 
                                                   onblur="document.all.uf_ctps.style.background = '#FFFFFF'" 
                                                   onKeyUp="pula(2, this.id, data_ctps.id)"
                                                   style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Data carteira de Trabalho:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;

                                            <input name='data_ctps' type='text' class='campotexto' size='12' maxlength='10' id='data_ctps'
                                                   onFocus="document.all.data_ctps.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.data_ctps.style.background = '#FFFFFF'" 
                                                   onkeyup="mascara_data(this);
                            pula(10, this.id, titulo2.id)"
                                                   style='background:#FFFFFF;'/>

                                        </td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style39'>Nº Título de Eleitor:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='titulo' type='text' class='campotexto' id='titulo2' size='10'
                                                       onFocus="document.all.titulo2.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.titulo2.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' />
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style39'> Zona:&nbsp;</span></div></td>
                                        <td height="28" colspan='3' bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='zona' type='text' class='campotexto' id='zona2' size='3'
                                                       onFocus="document.all.zona2.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.zona2.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style39'>Seção:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='secao' type='text' class='campotexto' id='secao' size='3'
                                                       onFocus="document.all.secao.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.secao.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style28'><span class='style39'>PIS:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='pis' type='text' class='campotexto' id='pis' size='12'
                                                       onFocus="document.all.pis.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.pis.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Data Pis:&nbsp;</div></td>
                                        <td height="28" colspan='3' bgcolor='#FFFFFF' >&nbsp;&nbsp;

                                            <input name='data_pis' type='text' class='campotexto' size='12' maxlength='10' id='data_pis'
                                                   onFocus="document.all.data_pis.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.data_pis.style.background = '#FFFFFF'" 
                                                   onkeyup="mascara_data(this);
                            pula(10, this.id, fgts.id)"
                                                   style='background:#FFFFFF;'/>

                                        </td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right'><span class='style39'>FGTS:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='fgts' type='text' class='campotexto' id='fgts' size='10'
                                                       onFocus="document.all.fgts.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.fgts.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                    </tr>
                                </table>


                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
                                    <tr>
                                        <td height="27" colspan='6' class="fundo_claro"><div class="titulo">Benef&iacute;cios</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td width='19%' height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>
                                                Assistência Médica:&nbsp;</div>	</td>
                                        <td height="28"  colspan='3' bgcolor='#FFFFFF'>

                                            <table width='100%' class=linha>
                                                <tr> 
                                                    <td width='74'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='medica' value='1'>Sim</label></td><td width='255'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='medica' value='0' checked>Não</label></td>
                                                </tr>
                                            </table>	</td>
                                        <td width='19%' height="28" bgcolor='#E8E8E8' >
                                            <div align='right' class='style39'>Tipo de Plano:&nbsp;</div></td>
                                        <td width='19%' height="28" bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <select name='plano_medico' class='campotexto' id='plano_medico'>

                                                <option value=1 >Familiar</option>
                                                <option value=2 selected>Individual</option>
                                            </select>   </td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Seguro, Apólice:&nbsp;</div></td>
                                        <td height="28"  colspan='3' bgcolor='#FFFFFF'><span class='style39'>&nbsp;&nbsp;
                                                <select name='apolice' class='campotexto' id='apolice'>
                                                    <option value='0'>Não Possui</option>
                                                    <?php
                                                    $result_ap = mysql_query("SELECT * FROM apolice where id_regiao = $id_regiao", $conn);
                                                    while ($row_ap = mysql_fetch_array($result_ap)) {

                                                        print "<option value='$row_ap[id_apolice]'>$row_ap[razao]</option>";
                                                    }
                                                    ?>
                                                </select>
                                                </select>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Dependente:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='dependente' type='text' class='campotexto' id='dependente' size='20'
                                                       onFocus="document.all.dependente.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.dependente.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Insalubridade:&nbsp;</div></td>
                                        <td height="28"  colspan='3' bgcolor='#FFFFFF'>&nbsp;&nbsp;
                                            <input name='insalubridade' type='checkbox' id='insalubridade2' value='1'/></td>

                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Vale Transporte:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >
                                            &nbsp;<input name='transporte' type='checkbox' id='transporte2' value='1'/>    </td>
                                    </tr>



                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo de Vale:&nbsp;</div></td>
                                        <td height="28"  colspan='5' bgcolor='#FFFFFF'><span class='style39'>
                                                &nbsp;&nbsp;
                                                <select name='tipo_vale' class='campotexto'>
                                                    <option value='1'>Cartão</option>
                                                    <option value='2'>Papel</option>
                                                    <option value='3'>Ambos</option>
                                                </select>
                                            </span></td>
                                    </tr>






                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Cartão 1:&nbsp;</div></td>
                                        <td width='15%' height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;
                                                <input name='num_cartao' type='text' class='campotexto' id='num_cartao' size='12'
                                                       onfocus="document.all.num_cartao.style.background = '#CCFFCC'" 
                                                       onblur="document.all.num_cartao.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td width='15%' height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Valor Total 1:&nbsp;</div></td>
                                        <td width='13%' height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='valor_cartao' type='text' class='campotexto' id='valor_cartao' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.valor_cartao.style.background = '#CCFFCC'" 
                                                   onblur="document.all.valor_cartao.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Cartão 1:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo_cartao_1' type='text' class='campotexto' id='tipo_cartao_1' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()"
                                                   onfocus="document.all.tipo_cartao_1.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo_cartao_1.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Cartão 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' ><span class='style39'>
                                                &nbsp;
                                                <input name='num_cartao2' type='text' class='campotexto' id='num_cartao2' size='12' 
                                                       onfocus="document.all.num_cartao2.style.background = '#CCFFCC'" 
                                                       onblur="document.all.num_cartao2.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Valor Total 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='valor_cartao2' type='text' class='campotexto' id='valor_cartao2' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.valor_cartao2.style.background = '#CCFFCC'" 
                                                   onblur="document.all.valor_cartao2.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Cartão 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo_cartao_2' type='text' class='campotexto' id='tipo_cartao_2' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()" 
                                                   onfocus="document.all.tipo_cartao_2.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo_cartao_2.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>



                                    <tr height='30'>
                                        <td height="28"  bgcolor='#E8E8E8' ><div align='right' class='style39'>
                                                Papel: &nbsp;&nbsp;Quantidade 1:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_qnt_1' type='text' class='campotexto' id='vale_qnt_1' size='3'
                                                   onFocus="document.all.vale_qnt_1.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.vale_qnt_1.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>&nbsp;Valor 1:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_valor_1' type='text' class='campotexto' id='vale_valor_1' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.vale_valor_1.style.background = '#CCFFCC'" 
                                                   onblur="document.all.vale_valor_1.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Vale 1:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo1' type='text' class='campotexto' id='tipo1' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()"
                                                   onfocus="document.all.tipo1.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo1.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Quantidade 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_qnt_2' type='text' class='campotexto' id='vale_qnt_2' size='3' 
                                                   onFocus="document.all.vale_qnt_2.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.vale_qnt_2.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Valor 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_valor_2' type='text' class='campotexto' id='vale_valor_2' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.vale_valor_2.style.background = '#CCFFCC'" 
                                                   onblur="document.all.vale_valor_2.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Vale 2:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo2' type='text' class='campotexto' id='tipo2' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()"
                                                   onfocus="document.all.tipo2.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo2.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Quantidade 3:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_qnt_3' type='text' class='campotexto' id='vale_qnt_3' size='3' 
                                                   onFocus="document.all.vale_qnt_3.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.vale_qnt_3.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>&nbsp;Valor 3:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_valor_3' type='text' class='campotexto' id='vale_valor_3' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.vale_valor_3.style.background = '#CCFFCC'" 
                                                   onblur="document.all.vale_valor_3.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Vale 3:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo3' type='text' class='campotexto' id='tipo3' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()"
                                                   onfocus="document.all.tipo3.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo3.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Quantidade 4:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_qnt_4' type='text' class='campotexto' id='vale_qnt_4' size='3' 
                                                   onFocus="document.all.vale_qnt_4.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.vale_qnt_4.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>&nbsp;Valor 4:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='vale_valor_4' type='text' class='campotexto' id='vale_valor_4' size='12' 
                                                   onkeydown="FormataValor(this, event, 20, 2)" 
                                                   onfocus="document.all.vale_valor_4.style.background = '#CCFFCC'" 
                                                   onblur="document.all.vale_valor_4.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Tipo Vale 4:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='tipo4' type='text' class='campotexto' id='tipo4' size='12' 
                                                   onChange="this.value = this.value.toUpperCase()"
                                                   onfocus="document.all.tipo4.style.background = '#CCFFCC'" 
                                                   onblur="document.all.tipo4.style.background = '#FFFFFF'" 
                                                   style='background:#FFFFFF;'/></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Adicional Noturno:&nbsp;</div></td>
                                        <td height="28"  colspan='3' bgcolor='#FFFFFF'>

                                            <table class='linha'>
                                                <tr> 
                                                    <td width='98'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='ad_noturno' value='1'>Sim</label></td>
                                                    <td width='86'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='ad_noturno' value='0' checked>Não</label></td>
                                                </tr>
                                            </table>	</td>
                                        <td height="28" bgcolor='#E8E8E8' ><div align='right' class='style39'>Integrante do CIPA:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >

                                            <table class='linha'>
                                                <tr> 
                                                    <td width='61'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='cipa' value='1' >Sim</label></td>
                                                    <td width='61'>&nbsp;&nbsp; 
                                                        <label><input type='radio' name='cipa' value='0' checked>Não</label></td>
                                                </tr>
                                            </table>	</td>
                                    </tr>
                                </table>




                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2' class='bordaescura1px'>
                                    <tr>
                                        <td height="32" colspan='6' class="fundo_claro"><div class="titulo">Dados Banc&aacute;rios</div></td>
                                    </tr>



                                    <tr height='30'>
                                        <td height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>
                                                Tipo de Pagamento:&nbsp;</div></td>
                                        <td height="28" colspan='5' bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <select name='tipopg' class='campotexto' id='tipopg'>
                                                <?php
                                                $RE_pg_dep = mysql_query("SELECT id_tipopg FROM tipopg where id_projeto = '$projeto' and campo1 = '1'");
                                                $Row_pg_dep = mysql_fetch_array($RE_pg_dep);

                                                $RE_pg_che = mysql_query("SELECT id_tipopg FROM tipopg where id_projeto = '$projeto' and campo1 = '2'");
                                                $Row_pg_che = mysql_fetch_array($RE_pg_che);


                                                $result_pg = mysql_query("SELECT * FROM tipopg where id_projeto = '$projeto'");
                                                while ($row_pg = mysql_fetch_array($result_pg)) {
                                                    print "<option value='$row_pg[id_tipopg]'>$row_pg[tipopg]</option>";
                                                }
                                                ?>
                                            </select>
                                            &nbsp;</td>
                                    </tr>

                                    <tr height='30'>
                                        <td width='15%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Banco:&nbsp;</div></td>
                                        <td width='25%' height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;
                                                <select name='banco' class='campotexto' id='banco'>
                                                    <option value='0'>Sem Banco</option>
            <?php
            $result_banco = mysql_query("SELECT * FROM bancos WHERE id_projeto = '$projeto' AND status_reg = '1'");
            while ($row_banco = mysql_fetch_array($result_banco)) {
                print "<option value='$row_banco[0]'>$row_banco[id_banco] - $row_banco[nome]</option>";
            }
            ?>
                                                    <option value="9999">Outro Banco</option>
                                                </select>
                                            </span></td>
                                        <td width='10%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Agência:&nbsp;</div></td>
                                        <td width='20%' height="28" bgcolor='#FFFFFF' ><span class='style39'>&nbsp;&nbsp;
                                                <input name='agencia' type='text' class='campotexto' id='agencia' size='12' 
                                                       onFocus="document.all.agencia.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.agencia.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr height='30'>
                                        <td width='10%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Conta:&nbsp;</div></td>
                                        <td width='20%' height="28" bgcolor='#FFFFFF' >&nbsp;<span class='style39'>
                                                <input name='conta' type='text' class='campotexto' id='conta' size='12' 
                                                       onFocus="document.all.conta.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.conta.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;'/>
                                            </span></td>
                                        <td width='10%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Tipo de Conta:&nbsp;</div></td>
                                        <td width='20%' height="28" bgcolor='#FFFFFF' >
                                            <span class='style39'>
                                                <label><input type='radio' name='radio_tipo_conta' value='salario'>Conta Salário </label>&nbsp;
                                                <label><input type='radio' name='radio_tipo_conta' value='corrente'>Conta Corrente </label>&nbsp;
                                            </span>
                                        </td>
                                    </tr>
                                    <tr id="linhabanc3">
                                        <td width='10%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Outro Banco:&nbsp;</div></td>
                                        <td colspan="3" bgcolor='#FFFFFF'>&nbsp;<span class='style39'>
                                                <input name="nome_banco" type="text" id="nome_banco" size="30" class="campotexto" /></span>
                                        </td>
                                    </tr>
                                </table>



                                <span ><br />
                                </span>



                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'  class='bordaescura1px'>
                                    <tr>
                                        <td height="32" colspan='4' class="fundo_claro"><div class="titulo">Dados Financeiros e de Contrato</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td height="28" bgcolor='#CCCCCC' ><div align='right'><span class='style39'>Data de Entrada:&nbsp;</span></div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;&nbsp;
                                            <input name='data_entrada' type='text' class='campotexto' size='12' maxlength='10' id='data_entrada'
                                                   onFocus="document.all.data_entrada.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.data_entrada.style.background = '#FFFFFF'" 
                                                   onkeyup="mascara_data(this);
                            pula(10, this.id, data_exame.id)"
                                                   style='background:#FFFFFF;'/>
                                        </td>
                                        <td height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>
                                                Data do Exame Admissional:&nbsp;</div></td>
                                        <td height="28" bgcolor='#FFFFFF' >&nbsp;
                                            <input name='data_exame' type='text' class='campotexto' size='12' maxlength='10' id='data_exame'
                                                   onFocus="document.all.data_exame.style.background = '#CCFFCC'" 
                                                   onBlur="document.all.data_exame.style.background = '#FFFFFF'" 
                                                   onkeyup="mascara_data(this);
                            pula(10, this.id, localpagamento.id)"
                                                   style='background:#FFFFFF;'/>
                                        </td>
                                    </tr>
                                    <tr height='30'>
                                        <td width='23%' height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Local de Pagamento:&nbsp;</div></td>
                                        <td width='77%' height="28" colspan='3' bgcolor='#FFFFFF' >&nbsp;&nbsp;<span class='style39'>
                                                <input name='localpagamento' type='text' class='campotexto' id='localpagamento' size='25'  
                                                       onFocus="document.all.localpagamento.style.background = '#CCFFCC'" 
                                                       onBlur="document.all.localpagamento.style.background = '#FFFFFF'" 
                                                       style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"/>
                                            </span></td>
                                    </tr>

                                    <tr height='30'>
                                        <td height="28" bgcolor='#CCCCCC' ><div align='right' class='style39'>Observações:&nbsp;</div></td>
                                        <td height="28" colspan='3' bgcolor='#FFFFFF' >
                                            &nbsp;&nbsp;
                                            <textarea name='observacoes' id='observacoes' class='campotexto' cols='55' rows='4'  
                                                      onFocus="document.all.observacoes.style.background = '#CCFFCC'" 
                                                      onBlur="document.all.observacoes.style.background = '#FFFFFF'" 
                                                      style='background:#FFFFFF;' onChange="this.value = this.value.toUpperCase()"></textarea></td>
                                    </tr>
                                </table>


                                <br />


                                <table width='95%' border='0' align='center' cellpadding='0' cellspacing='2'  class='bordaescura1px'>
                                    <tr>
                                        <td height="35" colspan='4' bgcolor='#999999' ><div align='center' class='title'>Finaliza&ccedil;&atilde;o do Cadastramento</div></td>
                                    </tr>
                                    <tr height='30'>
                                        <td colspan='4' bgcolor='#FFFFCC' >
                                            <div align='center' class='style39'>
                                                <p><br> O contrato foi ASSINADO?
                                                    &nbsp;&nbsp;
                                                    <input name='impressos2' type='checkbox' id='impressos2' value='1' />
                                                </p>
                                                <br>
                                                O Distrato foi ASSINADO?
                                                &nbsp;&nbsp;
                                                <table class=linha><tr>
                                                        <td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='1' $selected_ass_sim2> Sim </label></td>
                                                        <td>&nbsp;&nbsp;<label><input type='radio' id='assinatura3' name='assinatura3' value='0' $selected_ass_nao2> Não</label></td>
                                                    </tr></table>
                                                <br>
                                                Outros documentos foram ASSINADO?
                                                &nbsp;&nbsp;
                                                <table class=linha><tr>
                                                        <td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='1' $selected_ass_sim3> Sim </label></td>
                                                        <td>&nbsp;&nbsp;<label><input type='radio' id='assinatura' name='assinatura' value='0' $selected_ass_nao3> Não</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$mensagem_ass</td>
                                                    </tr></table>

                                                <br>
                                                <p>

                                                    <span class='style47'>NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</span>

                                                    <br />
                                                </p>
                                                <table width='200' border='0' align='center' cellpadding='0' cellspacing='0'>
                                                    <tr height='30'>
                                                        <td align='center' class='style7'>-</td>
                                                        <td align='center' valign='middle' class='style7'><input type='submit' name='Submit' value='CADASTRAR' />
                                                            <br /></td>
                                                    </tr>
                                                </table>
                                                <br />
                                                <div align='center'><span class='style7'>


                                                    </span><br />
                                                </div>
                                            </div></td>
                                    </tr>
                                </table>
                                <span class='style7'>

                                    <input type='hidden' name='regiao' value='<?= $id_regiao ?>'/>
                                    <input type='hidden' name='id_cadastro' value='4'>
                                    <input type='hidden' name='id_projeto' value='<?= $projeto ?>'>
                                    <input type='hidden' name='user' value='<?= $id_user ?>'>

                                </span></td>
                    </tr>
                </table>
            </form><br><a href='javascript:history.go(-1)' class='link'><img src='imagens/voltar.gif' border=0></a>

            <script language="javascript">

                        function validaForm() {

                            d = document.form1;
                            deposito = "$Row_pg_dep[0]";
                            cheque = "$Row_pg_che[0]";

                            if (d.nome.value == "") {
                                alert("O campo Nome deve ser preenchido!" + deposito + " " + cheque);
                                d.nome.focus();
                                return false;
                            }

                            if (d.endereco.value == "") {
                                alert("O campo Endereço deve ser preenchido!");
                                d.endereco.focus();
                                return false;
                            }

                            if (d.data_nasci.value == "") {
                                alert("O campo Data de Nascimento deve ser preenchido!");
                                d.data_nasci.focus();
                                return false;
                            }

                            if (d.rg.value == "") {
                                alert("O campo RG deve ser preenchido!");
                                d.rg.focus();
                                return false;
                            }

                            if (d.cpf.value == "") {
                                alert("O campo CPF deve ser preenchido!");
                                d.cpf.focus();
                                return false;
                            }

                            if (document.getElementById("tipopg").value == deposito) {

                                if (document.getElementById("banco").value == 0) {
                                    alert("Selecione um banco!");
                                    return false;
                                }

                                if (d.agencia.value == "") {
                                    alert("O campo Agencia deve ser preenchido!");
                                    d.agencia.focus();
                                    return false;
                                }

                                if (d.conta.value == "") {
                                    alert("O campo Conta deve ser preenchido!");
                                    d.conta.focus();
                                    return false;
                                }


                            }

                            if (document.getElementById("tipopg").value == cheque) {

                                if (document.getElementById("banco").value != 0) {
                                    alert("Para pagamentos em cheque deve selecionar SEM BANCO!");
                                    return false;
                                }
                                d.agencia.value = "";
                                d.conta.value = "";

                            }


                            if (d.localpagamento.value == "") {
                                alert("O campo Local de Pagamento deve ser preenchido!");
                                d.localpagamento.focus();
                                return false;
                            }
                            return true;
                        }
            </script>
            <?php
            break;

        case 5:         //CASO O ID SEJA 2 ELE VAI RODAR O - CADASTRO DE APÓLICES -

            $id_regiao = $_REQUEST['regiao'];
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
                <table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>

                    <tr>
                        <td  colspan='4'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>

                    <tr>
                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Cadastro de Apólices</div></td>
                    </tr>
                    <tr>
                        <td width="27%" class="secao">Razão Social do Banco:</td>
                        <td colspan="3" class="conteudo"><input name='razao' type='text' class='campotexto' id='razao' size='30'></td>
                    </tr>
                    <tr>
                        <td class="secao">Apólice:</td>
                        <td colspan="3" class="conteudo"><input name='apolice' type='text' class='campotexto' id='apolice' size='10'></td>
                    </tr>
                    <tr>
                        <td class="secao">Contrato:</td>
                        <td colspan="3" class="conteudo"><input name='contrato' type='text' class='campotexto' id='contrato' size='20'></td>
                    </tr>
                    <tr>
                        <td class="secao">Telefone:</td>
                        <td width="32%" class="conteudo">
                            <input name='tel' type='text' id='tel' size='10'
                                   onKeyPress="return(TelefoneFormat(this, event))" 
                                   onKeyUp="pula(13, this.id, gerente.id)" 
                                   onFocus="this.style.background = '#CCFFCC'" 
                                   onBlur="this.style.background = '#FFFFFF'" 
                                   style='background:#FFFFFF;' class='campotexto'>
                        </td>
                        <td width="11%" class="secao">Gerente:</td>
                        <td width="30%" class="conteudo"><input name='gerente' type='text' class='campotexto' id='gerente' size='10'></td>
                    </tr>
                    <tr>
                        <td height="54" colspan='4' align='center' bgcolor="#FFFFFF"><input type='submit' name='Submit5' value='CADASTRAR'>
                            <input type='hidden' name='id_cadastro' value='8'>
                            <input type='hidden' name='regiao' value='<?= $id_regiao ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

            <script>function validaForm() {
                            d = document.form1;
                            if (d.banco.value == "") {
                                alert("O campo Nome do Banco deve ser preenchido!");
                                d.banco.focus();
                                return false;
                            }
                            if (d.razao.value == "") {
                                alert("O campo Razão Social do Banco deve ser preenchido!");
                                d.razao.focus();
                                return false;
                            }
                            if (d.conta.value == "") {
                                alert("O campo Conta deve ser preenchido!");
                                d.conta.focus();
                                return false;
                            }
                            if (d.agencia.value == "") {
                                alert("O campo Agencia deve ser preenchido!");
                                d.agencia.focus();
                                return false;
                            }
                            if (d.contrato.value == "") {
                                alert("O campo Contrato deve ser preenchido!");
                                d.contrato.focus();
                                return false;
                            }
                            if (d.gerente.value == "") {
                                alert("O campo Gerente deve ser preenchido!");
                                d.gerente.focus();
                                return false;
                            }
                            return true;
                        }
            </script>

            <?php
            break;

        case 6:         //CASO O ID SEJA 6 ELE VAI RODAR O - CADASTRO DE BANCOS -

            $id_regiao = $_REQUEST['regiao'];
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
                <table width='80%' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='4'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Cadastro de Bancos</div></td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Local:</td>
                        <td height="25" colspan="3" class="conteudo">
                            <select name='interno' id='interno' class='campotexto'>
                                <option value='1'>INTERNO</option>
                                <option value='2'>EXTERNO</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td width="30%" height="25" class="secao">Projeto:</td>
                        <td width='70%' height="25" colspan="3" class="conteudo"><select name='projeto' class='campotexto'>
                                <?php
                                $result_pro1 = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' AND status_reg = '1'");
                                while ($row_pro1 = mysql_fetch_array($result_pro1)) {
                                    print "<option value=$row_pro1[0]>$row_pro1[0] - $row_pro1[nome]</option>";
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Selecione o Banco:</td>
                        <td height="25" colspan="3" class="conteudo"><select name='banco' type='text' class='campotexto' id='banco'>
                                <?php
                                $result_banco = mysql_query("SELECT * FROM listabancos ORDER BY banco");
                                while ($row_banco = mysql_fetch_array($result_banco)) {
                                    print "<option value=$row_banco[0]>$row_banco[0] - $row_banco[2]</option>";
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Nome para Exibição:</td>
                        <td height="25" colspan="3" class="conteudo"><input name='nom_banco' type='text' class='campotexto' id='nom_banco' size='31'>
                            &nbsp;&nbsp;&nbsp;&nbsp;<font size=1 color=#999999>( Ex: Real - Educação )</font>
                        </td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Localidade:</td>
                        <td height="25" colspan="3" class="conteudo"><input name='localidade' type='text' class='campotexto' id='localidade' size='31'>&nbsp;&nbsp;&nbsp;&nbsp;<font size=1 color=#999999>( Ex: Mauá, Itaboraí )</font>
                        </td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Conta Corrente:</td>
                        <td height="25" class="conteudo"><input name='conta' type='text' class='campotexto' id='conta' size='10'></td>
                        <td height="25" class="secao">Agência:</td>
                        <td height="25" class="conteudo"><input name='agencia' type='text' class='campotexto' id='agencia' size='5'></td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Endereço:</td>
                        <td height="25" colspan="3" class="conteudo"><input name='endereco' type='text' class='campotexto' id='endereco' size='31'></td>
                    </tr>
                    <tr>
                        <td height="25" class="secao">Telefone:</td>
                        <td height="25" class="conteudo"><input name='tel' type='text' id='tel' size='10'
                                                                onKeyPress="return(TelefoneFormat(this, event))" 
                                                                onKeyUp="pula(13, this.id, gerente.id)" 
                                                                onFocus="this.style.background = '#CCFFCC'" 
                                                                onBlur="this.style.background = '#FFFFFF'" 
                                                                style='background:#FFFFFF;' class='campotexto'></td>
                        <td height="25" class="secao">Gerente:</td>
                        <td height="25" class="conteudo"><input name='gerente' type='text' class='campotexto' id='gerente' size='10'></td>
                    </tr>
                    <tr>
                        <td height="52" colspan='4' align='center' bgcolor="#FFFFFF"><input type='submit' name='Submit6' value='CADASTRAR'>
                            <input type='hidden' name='id_cadastro' value='9'>
                            <input type='hidden' name='regiao' value='<?= $id_regiao ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

            <script>
                        function validaForm() {
                            d = document.form1;
                            if (d.banco.value == "") {
                                alert("O campo Nome do Banco deve ser preenchido!");
                                d.banco.focus();
                                return false;
                            }
                            if (d.razao.value == "") {
                                alert("O campo Razão Social do Banco deve ser preenchido!");
                                d.razao.focus();
                                return false;
                            }
                            if (d.conta.value == "") {
                                alert("O campo Conta deve ser preenchido!");
                                d.conta.focus();
                                return false;
                            }
                            if (d.agencia.value == "") {
                                alert("O campo Agência deve ser preenchido!");
                                d.agencia.focus();
                                return false;
                            }
                            if (d.endereco.value == "") {
                                alert("O campo Endereço deve ser preenchido!");
                                d.endereco.focus();
                                return false;
                            }
                            if (d.gerente.value == "") {
                                alert("O campo Gerente deve ser preenchido!");
                                d.gerente.focus();
                                return false;
                            }
                            return true;
                        }
            </script>
            <?php
            break;


        case 9:

            $user = $_REQUEST['id_user'];
            $regi_atu = $_REQUEST['regi_atu'];
            ?>
            <form action='cadastro2.php' method='post' name='form1'>

                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='2' class="fundo_azul"><div class="titulo">Alterando Região</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="secao">Alterar para a Região:</td>
                        <td width="70%" class="conteudo"><select name='regiao' class='campotexto' id='regiao'>
                                <?php
                                while ($row = mysql_fetch_array($result)) {
                                    $row_regiao = "$row[id_regiao]";
                                    if ($id_regiao == "$row_regiao") {
                                        print "<option value=$row[id_regiao] selected>$row[regiao] - $row[sigla]</option>";
                                    } else {
                                        print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
                                    }
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td height="46" colspan='2' align='center'><input type='submit' name='Submit7' value='ALTERAR'>
                            <input type='hidden' name='regiao_de' value='<?= $regi_atu ?>'>
                            <input type='hidden' name='id_cadastro' value='13'>
                            <input type='hidden' name='user' value='<?= $user ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

            <?php
            break;

        case 10:                                      //MARCAÇÃO DO PONTO

            $id_user = $_COOKIE['logado'];
            $id_reg = $_REQUEST['regiao'];
            $data = date('d/m/Y');
            $data2 = date('Y/m/d');
            $hora = date('H:i');

            $consulta = mysql_query("SELECT entrada1,saida1,entrada2,saida2 FROM ponto where id_funcionario = '$id_user' and id_regiao = '$id_reg' and data = '$data2'", $conn) or die("Erro no sql");
            $row = mysql_fetch_array($consulta);

            if ($row['0'] == "") {
                $entrada1 = "<input name='radiobutton' type='radio' value='1'>";
                $saida1 = "<font color=red>Marque a Entrada</font>";
                $entrada2 = "<font color=red>Marque a Saída</font>";
                $saida2 = "<font color=red>Marque a Volta</font>";
                $bt = "<input type='submit' name='Enviar' value='Enviar'>";
                $justificativa = "1";
            } elseif ($row['1'] == "00:00:00") {
                $entrada1 = "<font size=2><b>$row[0]</b></font>";
                $saida1 = "<input name='radiobutton' type='radio' value='2'>";
                $entrada2 = "<font color=red>Marque a Saída</font>";
                $saida2 = "<font color=red>Marque a Volta</font>";
                $bt = "<input type='submit' name='Enviar' value='Enviar'>";
                $justificativa = "2";
            } elseif ($row['2'] == "00:00:00") {
                $entrada1 = "<font size=2><b>$row[0]</b></font>";
                $saida1 = "<font size=2><b>$row[1]</b></font>";
                $entrada2 = "<input name='radiobutton' type='radio' value='3'>";
                $saida2 = "<font color=red>Marque a Volta</font>";
                $bt = "<input type='submit' name='Enviar' value='Enviar'>";
                $justificativa = "3";
            } elseif ($row['3'] == "00:00:00") {
                $entrada1 = "<font size=2><b>$row[0]</b></font>";
                $saida1 = "<font size=2><b>$row[1]</b></font>";
                $entrada2 = "<font size=2><b>$row[2]</b></font>";
                $saida2 = "<input name='radiobutton' type='radio' value='4'>";
                $bt = "<input type='submit' name='Enviar' value='Enviar'>";
                $justificativa = "4";
            } else {
                $entrada1 = "<font size=2><b>$row[0]</b></font>";
                $saida1 = "<font size=2><b>$row[1]</b></font>";
                $entrada2 = "<font size=2><b>$row[2]</b></font>";
                $saida2 = "<font size=2><b>$row[3]</b></font>";
                $bt = "";
                $justificativa = "0";
            }
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit='return valida()'>
                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>

                    <tr>
                        <td  colspan='4'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Marca&ccedil;&atilde;o do Ponto</div>
                        </td>
                    </tr>
                    <tr>
                        <td height="56" colspan='4'><div align='center' class="titulo_opcoes"><b>Data: <?= $data ?> <br> Hora: <?= $hora ?></b></div></td>
                    </tr>
                    <tr>
                        <td colspan='4'>&nbsp;</td>
                    </tr>
                    <tr>
                        <td width="91" bgcolor="#999999"><div align='center' class="teste2">Entrada</div></td>
                        <td width="123" bgcolor="#999999"><div align='center' class="teste2">Sa&iacute;da Almo&ccedil;o </div></td>
                        <td width="115" bgcolor="#999999"><div align='center' class="teste2">Volta Almo&ccedil;o </div></td>
                        <td width="132" bgcolor="#999999"><div align='center' class="teste2">Sa&iacute;da</div></td>
                    </tr>
                    <tr>
                        <td bgcolor="#CCCCCC"><div align='center'>
                                <?= $entrada1 ?>
                            </div></td>
                        <td bgcolor="#CCCCCC"><div align='center'>
                                <?= $saida1 ?>
                            </div></td>
                        <td bgcolor="#CCCCCC"><div align='center'>
                                <?= $entrada2 ?>
                            </div></td>
                        <td bgcolor="#CCCCCC"><div align='center'>
                                <?= $saida2 ?>
                            </div></td>
                    </tr>
                    <tr>
                        <td colspan='4' align='center'><br><br><br>
                            <div><strong>Justificativa</strong></div>
                            <textarea name='justifica<?= $justificativa ?>' id='justifica<?= $justificativa ?>' cols=35 rows=5></textarea>  <br></td>
                    </tr>
                    <tr>
                        <td height='35' colspan='4' align='center' valign='middle'><br><br><?= $bt ?></td>
                    </tr>
                </table>
                <input type='hidden' name='id_cadastro' value='14'>
                <input type='hidden' name='regiao' value='<?= $id_reg ?>'>
                <input type='hidden' name='user' value='<?= $id_user ?>'></td>
            </form><center><br><a href='javascript:window.location.reload()' class='rodape'>ATUALIZAR</a></center>

            <script>
            <!--
                        function valida() {
                            if (!document.form1.radiobutton[0].checked && !document.form1.radiobutton[1].checked && !document.form1.radiobutton[2].checked && !document.form1.radiobutton[3].checked) {
                                alert("Escolha uma marcação de Ponto");
                                return false;
                            }
                        }
            //-->
            </script>

            <?php
            break;
        case 11:
            $id_regiao = $_REQUEST['regiao'];
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Cadastro de Atividades</div>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="secao">Nome:</td>
                        <td width="70%" class="conteudo"><input name='nome' type='text' class='campotexto' id='nome' size='20'></td>
                    </tr>
                    <tr>
                        <td class="secao">Área:</td>
                        <td class="conteudo"><input name='area' type='text' class='campotexto' id='area' size='31'></td>
                    </tr>
                    <tr>
                        <td class="secao">Região:</td>
                        <td class="conteudo"><select name='id_regiao' class='campotexto' id='regiao'>
                                <?php
                                while ($row = mysql_fetch_array($result)) {
                                    $row_regiao = "$row[id_regiao]";
                                    if ($id_regiao == "$row_regiao") {
                                        print "<option value=$row[id_regiao] selected>$row[regiao] - $row[sigla]</option>";
                                    } else {
                                        print "<option value=$row[id_regiao]>$row[regiao] - $row[sigla]</option>";
                                    }
                                }
                                ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td class="secao">Descrição:</td>
                        <td class="conteudo"><textarea name='descricao' cols='35' rows='5' class='campotexto'></textarea></td>
                    </tr>
                    <tr>
                        <td height="40" colspan='2' align='center'><input type='submit' name='Submit8' value='CADASTRAR'>
                            <input type='hidden' name='id_cadastro' value='15'>
                            <input type='hidden' name='regiao' value='<?= $id_regiao ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>

            <script>function validaForm() {
                            d = document.form1;
                            if (d.nome.value == "") {
                                alert("O campo Nome deve ser preenchido!");
                                d.nome.focus();
                                return false;
                            }
                            if (d.area.value == "") {
                                alert("O campo Área deve ser preenchido!");
                                d.area.focus();
                                return false;
                            }
                            return true;
                        }



            </script>
            <?php
            break;

        case 12:                            //EDITANDO FUNCIONARIOS

            $id_user = $_REQUEST['user'];
            $pag = $_REQUEST['pag'];

            $result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'", $conn);
            $row_user = mysql_fetch_array($result_user);

            if (empty($_REQUEST['master'])) {
                $mostrar = "style='display:none'";
            } else {
                $mostrar = "";
            }

            $link_foto = $row_user['id_regiao'] . "funcionario" . $row_user['0'] . $row_user['foto'];

            if ($row_user['foto'] != "0") {
                $link = "<img src='fotos/$link_foto' border=1 width='100' height='130'>";
                $foto = "Deseja remover a foto? <label><input name='foto' type='checkbox' id='foto' value='3'/> Sim</label>";
            } else {
                $link = "<img src='fotos/semimagem.gif' border=1 width='100' height='130'>";
                $foto = "Foto: <input name='foto' type='checkbox' id='foto' value='1' onClick=\"document.all.tablearquivo.style.display = (document.all.tablearquivo.style.display == 'none') ? '' : 'none' ;\">";
            }
            ?>

            <script src="jquery/jquery-1.4.2.min.js"></script>
            <script>

                $(function() {

                    $('input[name=todos]').click(function() {

                        var verifica = $(this).attr('checked');
                        var numero = $(this).val();


                        if (verifica == true) {

                            $('.' + numero).attr('checked', 'checked');

                        } else {

                            $('.' + numero).attr('checked', false)
                        }
                    });


                    $('.todos_master').click(function() {

                        var valor = $(this).val();
                        var verifica = $(this).attr('checked');


                        if (verifica == true) {

                            $('.master_' + valor).attr('checked', 'checked');

                        } else {

                            $('.master_' + valor).attr('checked', false);
                        }



                    });



                });
            </script>


            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()"  enctype='multipart/form-data'>

                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='4'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='4' class="fundo_azul"><div class="titulo">Editar Funcion&aacute;rio</div>
                        </td>
                    </tr>
                    <tr>
                        <td height="30" colspan=4 align=center bgcolor="#FFFFFF"><?= $link ?></td>
                    </tr>
                    <tr>
                        <td width='17%' height="30" align=right bgcolor="#FFFFFF">Master:</td>
                        <td width='38%' class="conteudo">&nbsp;&nbsp; 
                            <?php
                            include "classes/regiao.php";

                            $SelMas = new regiao();
                            $SelMas->SelectMaster('master', 'class=\'campotexto\'', $id_user);
                            ?>
                        </td>
                        <td height="30" align=right bgcolor="#FFFFFF">Regi&atilde;o:</td>
                        <td height="30" bgcolor="#FFFFFF">

                            <select name='id_regiao' class='campotexto' id='regiao'>

            <?php
            $REReg = mysql_query("SELECT * FROM regioes");
            while ($row = mysql_fetch_array($REReg)) {

                $regiao_atual = $row_user['id_regiao'];
                $regiao_atual2 = $row['id_regiao'];

                if ($regiao_atual == $regiao_atual2) {
                    print "<option value='$row[id_regiao]' selected>$row[regiao] - $row[sigla]</option>";
                } else {
                    print "<option value='$row[id_regiao]'>$row[regiao] - $row[sigla]</option>";
                }
            }
            ?>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <td width='17%' height="30" align=right bgcolor="#FFFFFF">Função:</td>
                        <td width='38%' height="30" bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='funcao' type='text' class='campotexto' id='funcao' size='30' value='<?= $row_user['funcao'] ?>'>
                        <td width='13%' height="30" align=right bgcolor="#FFFFFF">Locação:</td>
                        <td width='32%' height="30" bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='locacao' type='text' class='campotexto' id='locacao' size='20' value='<?= $row_user['locacao'] ?>'></td>
                    </tr>
                    <!--
                    <tr $mostrar>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Grupo: </td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <select name='grupo_usuario' class='campotexto'>
            <?php
            $result_grupo = mysql_query("SELECT * FROM grupo", $conn);

            while ($row_grupo = mysql_fetch_array($result_grupo)) {

                $r_grupo = "$row_grupo[id_grupo]";

                if ($row_user['grupo_usuario'] == "$r_grupo") {
                    print "<option value=$row_grupo[id_grupo] selected>$row_grupo[nome]</option>";
                } else {
                    print "<option value=$row_grupo[id_grupo]>$row_grupo[nome]</option>";
                }
            }
            ?></select>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    &nbsp;&nbsp; Salário: R$&nbsp;&nbsp;<input name='salario' type='text' class='campotexto' id='salario' size='10' value='<?= $row_user['salario'] ?>'> <font color=#999999 size=1>Somente números</font></td>
                    </tr>
                    -->
                    <tr>
                        <td width='17%' height="30" align=right bgcolor="#FFFFFF">Nome Completo:</td>
                        <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='nome' type='text' class='campotexto' id='nome' size='35' value='<?= $row_user['nome'] ?>'>
                            &nbsp;&nbsp;Nome para exibição: <input name='nome1' type='text' class='campotexto' id='nome1' size='15' value='<?= $row_user['nome1'] ?>'></td>
                    </tr>
                    <!--
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Endereco:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='endereco' type='text' class='campotexto' id='endereco' size='75' value='<?= $row_user['endereco'] ?>'></td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Bairro:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='bairro' type='text' class='campotexto' id='bairro' size='15' value='<?= $row_user['bairro'] ?>'>
                    &nbsp;&nbsp; Cidade:&nbsp;&nbsp; <input name='cidade' type='text' class='campotexto' id='cidade' size='12' value='<?= $row_user['cidade'] ?>'>
                    &nbsp;&nbsp; UF:&nbsp;&nbsp; <input name='uf' type='text' class='campotexto' id='uf' size='2' maxlength='2' value='<?= $row_user['uf'] ?>'>
                    &nbsp;&nbsp; CEP:&nbsp;&nbsp; <input name='cep' type='text' class='campotexto' id='cep' size='12' value='<?= $row_user['cep'] ?>'></td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Telefones:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp; Fixo:&nbsp;&nbsp; <input name='tel_fixo' type='text' class='campotexto' id='tel_fixo' size='12' maxlength='14' value='<?= $row_user['tel_fixo'] ?>'>
                    &nbsp; Cel:&nbsp;&nbsp; <input name='tel_cel' type='text' class='campotexto' id='tel_cel' size='12' maxlength='14' value='<?= $row_user['tel_cel'] ?>'>
                    &nbsp; Recado:&nbsp;&nbsp; <input name='tel_rec' type='text' class='campotexto' id='tel_rec' size='12' maxlength='14' value='<?= $row_user['tel_rec'] ?>'></td>
                    </tr>-->

                    <tr>
                        <td height="30" align=right bgcolor="#FFFFFF">Data de </td>
                        <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp; Nascimento:
                            &nbsp; <input name='nasc_dia' type='text' class='campotexto' size='10' maxlength=10 value='<?= implode('/', array_reverse(explode('-', $row_user['data_nasci']))) ?>'> Ano / mes / dia</td>
                    </tr>


                    <!----
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Naturalidade:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='naturalidade' type='text' class='campotexto' id='naturalidade' size='10' value='<?= $row_user['naturalidade'] ?>'>
                    &nbsp;&nbsp; Nacionalidade:&nbsp;&nbsp; <input name='nacionalidade' type='text' class='campotexto' id='nacionalidade' size='8' value='<?= $row_user['nacionalidade'] ?>'>
                    &nbsp;&nbsp; Estado Civil:&nbsp;&nbsp; <input type='text' name='civil' class='campotexto' id='civil' value='<?= $row_user['civil'] ?>'></td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">CTPS:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; 
                    <input name='ctps' type='text' class='campotexto' id='ctps' size='10' value='<?= $row_user['ctps'] ?>'
                    onFocus="document.all.ctps.style.background='#CCFFCC'"
                    onBlur="document.all.ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF">
                    &nbsp;&nbsp; &nbsp;&nbsp; 
                    Série:
                    &nbsp;&nbsp; 
                    <input name='serie_ctps' type='text' class='campotexto' id='serie_ctps' size='8' value='<?= $row_user['serie_ctps'] ?>'
                    onFocus="document.all.serie_ctps.style.background='#CCFFCC'"
                    onBlur="document.all.serie_ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF">
                    &nbsp;&nbsp; &nbsp;&nbsp; 
                    UF:
                    &nbsp;&nbsp; 
                    <input name='uf_ctps' type='text' class='campotexto' id='uf_ctps' size='2' maxlength='2' value='<?= $row_user['uf_ctps'] ?>'
                    onFocus="document.all.uf_ctps.style.background='#CCFFCC'"
                    onBlur="document.all.uf_ctps.style.background='#FFFFFF'" 
                    style="background:#FFFFFF" 
                    onChange="this.value=this.value.toUpperCase()" >
                    &nbsp;&nbsp; &nbsp;&nbsp; 
                    PIS:
                    &nbsp;&nbsp; 
                    <input name='pis' type='text' class='campotexto' id='pis' size='15' value='<?= $row_user['pis'] ?>'
                    onFocus="document.all.pis.style.background='#CCFFCC'"
                    onBlur="document.all.pis.style.background='#FFFFFF'" 
                    style="background:#FFFFFF">
                    &nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Nº do RG:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='rg' type='text' class='campotexto' id='rg' size='12' value='<?= $row_user['rg'] ?>'>
                    &nbsp;&nbsp; Orgão Expedidor:&nbsp;&nbsp; <input name='orgao' type='text' class='campotexto' id='orgao' size='8' value='<?= $row_user['orgao'] ?>'>
                    &nbsp;&nbsp; Data:&nbsp;&nbsp; <input name='data_rg' type='text' class='campotexto' size='10' maxlength=10 value='<?= $row_user['data_rg'] ?>'> Ano / mes / dia</td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">CPF:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='cpf' type='text' class='campotexto' id='cpf' size='12' value='<?= $row_user['cpf'] ?>'>
                    &nbsp;&nbsp; Nº Título de Eleitor:&nbsp;&nbsp; <input name='titulo' type='text' class='campotexto' id='titulo' size='10' value='<?= $row_user['titulo'] ?>'>
                    &nbsp;&nbsp; Zona:&nbsp;&nbsp; <input name='zona' type='text' class='campotexto' id='zona' size='3' value='<?= $row_user['zona'] ?>'>
                    &nbsp;&nbsp; Seção:&nbsp;&nbsp; <input name='secao' type='text' class='campotexto' id='secao' size='3' value='<?= $row_user['secao'] ?>'></td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Filiação - Pai:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='pai' type='text' class='campotexto' id='pai' size='75' value='<?= $row_user['pai'] ?>'></td>
                    </tr>
                    <tr>
                      <td width='17%' height="30" align=right bgcolor="#FFFFFF">Filiação - Mãe:</td>
                      <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='mae' type='text' class='campotexto' id='mae' size='75' value='<?= $row_user['mae'] ?>'></td>
                    </tr>
                    <tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Estuda Atualmente:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF" >&nbsp;&nbsp; 
                    <input type='radio' checked name='estuda' value='sim' 
                    onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? 'none' : 'none' ;"> Sim&nbsp;&nbsp;
                    
                    <input type='radio' name='estuda' value='nao' 
                    onClick="document.all.linha_termino.style.display = (document.all.linha_termino.style.display == 'none') ? '' : '' ;"> Não</td>
                    </tr>
                    <tr id='linha_termino' style='display:none'>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Término em:</td>
                    <td height="30" colspan="3" bgcolor="#FFFFFF">&nbsp;&nbsp; 
                    <input name='escola_dia' type='text' class='campotexto' value='30' size='2' maxlength=2 > / <input name='escola_mes' type='text' class='campotexto' size='2' maxlength=2 value='11'> / <input name='escola_ano' type='text' class='campotexto' size='4' maxlength=4></td>
                    </tr>
                    <td width='17%' height="30" align=right bgcolor="#FFFFFF">Escolaridade:</td>
                    <td height="30" colspan=3 bgcolor="#FFFFFF">&nbsp;&nbsp; 
                    <select name='escolaridade'>";
            <? $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE status = 'on'");
            while ($escolaridade = mysql_fetch_assoc($qr_escolaridade)) {
                ?>
                        <option value="<?= $escolaridade['id'] ?>" <? if ($row_user['escolaridade'] == $escolaridade['id']) { ?>selected<? } ?>>
                <?= $escolaridade['cod'] ?> - <?= $escolaridade['nome'] ?>
                        </option>
                    <? } ?>
                    </select>
                    &nbsp;&nbsp; Instituíção:&nbsp;&nbsp; 
                    <input name='instituicao' type='text' class='campotexto' id='instituicao' size='20' value='<?= $row_user['instituicao'] ?>'>
                    &nbsp;&nbsp; Atividade:&nbsp;&nbsp; <input name='curso' type='text' class='campotexto' id='curso' size='10' value='<?= $row_user['curso'] ?>'></td>
                    </tr>
                    <tr>
                      <td width='17%' height="30" align="right" bgcolor="#FFFFFF"></td>
                      <td height="30" colspan="3" bgcolor="#FFFFFF">
                      <table width='100%' border='0' cellspacing='0' cellpadding='0' class='linha'>
                      <tr>
                      <td>
            <?= $foto ?>  </td>
                      <td style='display:none' id='tablearquivo'>
                      <input type='file' name='arquivo' id='arquivo' class='campotexto' >
                      <font size='1' color='#999999'>(.jpg, .png, .gif, .jpeg)</font></td>
                      </tr>
                      </table>  </td>
                    </tr>
                    <tr>
                      <td height="30" colspan="4" class="fundo_claro"><div class="nota">Informações Bancárias</div></td>
                    </tr>
                    <tr>
                      <td width='17%' height="30" align="right" bgcolor="#FFFFFF">Banco:</td>
                      <td height="30" colspan="3" bgcolor="#FFFFFF">&nbsp;&nbsp; <input name='banco' type='text' class='campotexto' id='banco' size='15' value='<?= $row_user['banco'] ?>'>
                        &nbsp;&nbsp; Agência:&nbsp;&nbsp; <input name='agencia' type='text' class='campotexto' id='agencia' size='7' value='<?= $row_user['agencia'] ?>'>
                        &nbsp;&nbsp; nº da Conta:&nbsp;&nbsp; <input name='conta' type='text' class='campotexto' id='conta' size='15' value='<?= $row_user['conta'] ?>'>    </td>
                    </tr>-->
                    <tr>
                        <td height="30" colspan="4" class="fundo_claro"><div class="nota">Informações de Login</div></td>
                    </tr>
                    <tr>
                        <td width='17%' height="30" align="right" bgcolor="#FFFFFF">Login:</td>
                        <td height="30" colspan="3" bgcolor="#FFFFFF">&nbsp;&nbsp; <?= $row_user['login'] ?>
                            &nbsp;&nbsp; Senha padrão:&nbsp;&nbsp; ******
                            &nbsp;&nbsp;</tr>
                    <!--
                    <tr $mostrar>
                    
                    
                    <td width='17%' height="30" align="right" bgcolor="#FFFFFF">Tipo de Conta:</td>
                    
                    <td height="30" colspan="3" bgcolor="#FFFFFF">
                    &nbsp;&nbsp; 
                    <select name='tipo_usuario' class='campotexto'>
            <?php
            $result_tipo_user = mysql_query("SELECT * FROM grupo where tipo = '2'");
            while ($row_tipo_user = mysql_fetch_array($result_tipo_user)) {

                if ($row_tipo_user['id_tipo'] == $row_user['tipo_usuario']) {
                    print "<option value=$row_tipo_user[id_tipo] selected>$row_tipo_user[nome]</option>";
                } else {
                    print "<option value=$row_tipo_user[id_tipo]>$row_tipo_user[nome]</option>";
                }
            }
            ?></select>
                    </td>
                    </tr>
                    -->

                    <tr>
                        <td height="30" colspan="8" class="fundo_claro"><div class="titulo">E-mail:</div></td>
                    </tr>


                    <tr>
                        <td>MASTER:</td>
                        <td colspan="7"> 

            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
            while ($row_master = mysql_fetch_assoc($qr_master)):

                $verifica_master_email = mysql_num_rows(mysql_query("SELECT * FROM funcionario_email_assoc WHERE id_master = '$row_master[id_master]' AND id_funcionario = '$id_user'"));
                $checked = ($verifica_master_email != 0 ) ? 'checked="checked"' : '';


                echo '<input type="checkbox" name="master_email[]" value="' . $row_master['id_master'] . '" class="master_email" ' . $checked . '/> ' . $row_master['nome'] . ' &nbsp;';

            endwhile;
            ?>
                        </td>
                        <td>

                        </td>

                    </tr>
                    <tr>
                        <td colspan="9">
                            <table  border ='0' width="980">
            <?php
            $qr_master = mysql_query("SELECT * FROM master WHERE status = 1 AND email_servidor != ''");
            while ($row_master = mysql_fetch_assoc($qr_master)):

                $qr_email = mysql_query("SELECT * FROM funcionario_email_assoc  WHERE id_funcionario = '$id_user' AND id_master = '$row_master[id_master]'");
                $row_email = mysql_fetch_assoc($qr_email);

                $display = (mysql_num_rows($qr_email) == 0) ? 'display:none;' : 'display:block;';
                ?>	

                                    <tr class="master_<?php echo $row_master['id_master']; ?>" style="<?php echo $display; ?>">
                                        <td colspan="5" align="left" width="980" heigth="20" bgcolor="#F0F0F0"> 
                                            <strong> <?php echo $nome_master; ?></strong>
                                        </td>
                                    </tr>
                                    <tr class="master_<?php echo $row_master['id_master']; ?>" style="<?php echo $display; ?>">
                                        <td width="100"><?php echo $row_master['nome'] ?></td>
                                        <td width="50" align="right"><strong>E-mail:</strong></td>
                                        <td width="200"><input type="text" name="email[<?php echo $row_master['id_master']; ?>]" class="email"  value="<?php echo $row_email['email']; ?>"/></td>
                                        <td width="150" align="right"><strong>Senha do e-mail:</strong></td>
                                        <td width="400"><input type="password" name="senha_email[<?php echo $row_master['id_master']; ?>]" rel="<?php echo $row_master['id_master']; ?>" class="senha_email" value="<?php echo $row_email['senha']; ?>"/> <span class="menssagem"></span>

                                        </td>
                                    </tr>

                <?php
            endwhile;
            unset($checked);
            ?>
                            </table>
                        </td>
                    </tr>





                    <tr>
                        <td height="30" colspan="8" class="fundo_claro"><div class="titulo">Gerenciamento de Acesso a Intranet</div></td>
                    </tr>

                    <tr>

                    <tr><td>&nbsp; </td></tr>
                    <tr  bgcolor="#EFEFEF">
                        <td  colspan="8" align="center"><strong>Acesso as regiões</strong></td>
                    </tr>

            <?php
////CONTROLE DE ACESSO DAS REGIõES
            $array_status = array(1 => 'REGIÕES ATIVAS', 0 => 'REGIÕES INATIVAS');

            foreach ($array_status as $status => $nome_status) {
                ?>
                        <tr>
                            <td bgcolor="#EFEFEF" align="center" valign="top"><?php echo $nome_status; ?></td>
                            <td  colspan="6">
                                <table width="100%" cellspacing="0">
                        <?php
                        if ($status == 0) {
                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                        } else {
                            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                        }

                        while ($row_regioes = mysql_fetch_assoc($qr_regioes)):
                            if ($row_regioes['id_regiao'] == 38 and $row_regioes['id_regiao'] == 16)
                                continue;

                            $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                            $row_master = mysql_fetch_assoc($qr_master);

                            $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_regiao = '$row_regioes[id_regiao]' AND id_funcionario = '$id_user'"));
                            $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';


                            if ($row_master['id_master'] != $master_anterior) {
                                echo '<tr  bgcolor="#C7E2E2"><td align="left">' . $row_master['nome'] . ' 
					 <span style="float:right;"> <input name="todos_master"  type="checkbox" value="' . $row_regioes['id_master'] . '_' . $status . '" class="todos_master"  />Marcar/Desmarcar todos </span>
					  </td>
					  </tr>';
                            }

                            echo '<tr bgcolor="#D9ECFF">
						<td>
							<input name="empresas[]"  type="hidden" value="' . $row_regioes['id_master'] . '"/>
							<input name="regioes_permitidas[' . $row_regioes['id_master'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
						</td>
     				</tr>';

                            $master_anterior = $row_master['id_master'];

                        endwhile;

                        echo '<tr><td>&nbsp;</td></tr>';

                        unset($master_anterior);
                        ?>  
                                </table>
                            </td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                                <?php
                                } //fim foreach
///////////////////////////////////////
                                ?>



                    <tr><td>&nbsp; </td></tr>
                    <?php
                    $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE 1");
                    while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                        echo '<tr bgcolor="#EFEFEF">
			<td colspan="8" align="center"><strong>' . $row_pagina['botoes_pg_nome'] . '</strong><br></td>
			</tr>';
                        ///PERMISSÔES PARA OS RELATÓRIOS DO FINANCEIRO
                        if ($row_pagina['botoes_pg_id'] == 3) {

                            echo '<tr>
                                <td style="background-color: #EFEFEF;" align="center">PÁGINA INICIAL</td>
                                <td>
                        ';

                            $qr_acoes = mysql_query("SELECT * FROM acoes WHERE botoes_pagina_id = '$row_pagina[botoes_pg_id]'");
                            while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                $qr_acoes_assoc = mysql_query("SELECT * FROM funcionario_acoes_assoc  WHERE id_funcionario = '$id_user'  AND acoes_id = '$row_acoes[acoes_id]' ");
                                $checked = (mysql_num_rows($qr_acoes_assoc) != 0) ? 'checked="checked"' : '';

                                echo "<input type='checkbox' name='acoes[]' value='" . $row_acoes['acoes_id'] . "' " . $checked . "/> " . $row_acoes['acoes_nome'] . "<br>";


                            endwhile;
                        }
                        echo '</td></tr>';
                        ////////////////////////////////////////////


                        $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina = '$row_pagina[botoes_pg_id]'");
                        while ($row_botoes_menu = mysql_fetch_assoc($qr_botoes_menu)) {
                            $todos++;
                            ?>         
                            <tr>
                                <td height="30" bgcolor="FFF" align="center" valign="top" style="background-color: #F5F5F5" >

                            <?php echo $row_botoes_menu['botoes_menu_nome'] ?><br><br><br>

                                    <input type="checkbox" class="tipo_menu"  name="todos" value="<?php echo $todos; ?>">Marcar/Desmarcar todos

                                </td>

                                <td colspan="7">         
                                    <?php
                                    $qr_botoes = mysql_query("SELECT * FROM botoes WHERE   botoes_menu = '$row_botoes_menu[botoes_menu_id]'  ORDER BY  botoes_menu ASC");
                                    $contador_icone = 0;

                                    while ($row_botoes = mysql_fetch_assoc($qr_botoes)):



                                        $qr_botoes_assoc = mysql_query("SELECT * FROM botoes_assoc WHERE botoes_id = '$row_botoes[botoes_id]'  AND id_funcionario = '$id_user' ");
                                        $row_assoc = mysql_fetch_assoc($qr_botoes_assoc);


                                        ////permisões pra deletar, exluir e etc;.
                                        $qr_acoes = mysql_query("SELECT * FROM acoes WHERE   botoes_id = '$row_botoes[botoes_id]' ORDER BY tp_contratacao_id ASC") or die(mysql_error());

                                        /////GESTÃO DE COMPRAS
                                        if ($row_botoes['botoes_id'] == 8) {

                                            echo '<table border="0"  cellspacing="0">';
                                            ?>
                                    <tr bgcolor="#C7E2E2">

                                        <td colspan="2">
                                            <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo $row_botoes['botoes_nome']; ?> -  ETAPAS DE COMPRA
                                        </td>  
                                    </tr>


                            <?php
                            $qr_acompanhamento = mysql_query("SELECT * FROM acompanhamento_compra WHERE status = 1") or die(mysql_error());
                            while ($row_acomp = mysql_fetch_assoc($qr_acompanhamento)):

                                $verifica_acomp = mysql_num_rows(mysql_query("SELECT * FROM func_acompanhamento_assoc WHERE id_funcionario = '$id_user' AND id_acompanhamento = '$row_acomp[acompanhamento_id]'"));

                                $checked = ($verifica_acomp != 0) ? 'checked="checked"' : '';
                                echo '<tr bgcolor="#D9ECFF">                                       
											 <td colspan="2">
											 	<input type="checkbox" name="acomp_compra[]" value="' . $row_acomp['acompanhamento_id'] . '" ' . $checked . '/> ' . $row_acomp['acompanhamento_nome'] . '
											 </td>
										</tr>';


                            endwhile;


                            echo '</table>';
                            unset($checked);
                        }   ////////////FIM BOTÃO 8



                        if (mysql_num_rows($qr_acoes) != 0) {
                            ?>

                                    <table border="0"  cellspacing="0">
                                        <tr bgcolor="#C7E2E2">

                                            <td colspan="2">
                                                <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo $row_botoes['botoes_nome']; ?> -  Ações
                                            </td>  
                                        </tr>


                            <?php
                            ////acoes	
                            while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="master_acoes_' . $row_acoes['tp_contratacao_id'] . '"/> ' . '(' . $row_acoes['acoes_id'] . ') ' . $row_acoes['acoes_nome'] . '</td></tr>';

                            endwhile;




                            ///BOTÕES VISUALIZAR OBRIGAÇÃO E EXCLUIR OBRIGAÇÃO			
                            if ($row_botoes['botoes_id'] == 82) {

                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                    $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . '/> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                endwhile;
                            } else
                            //CONDIÇÃO PARA EXIBIR AS REGIÕES PERMITIDAS PARA VISUALIZAÇÃO DA FOLHA 
                            if ($row_botoes['botoes_id'] == 33 or $row_botoes['botoes_id'] == 60) {


                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):


                                    $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                    //ações
                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . '/> ' . '(' . $row_acoes['acoes_id'] . ')' . $row_acoes['acoes_nome'] . '</td></tr>';

                                endwhile;


                                echo'<tr  bgcolor="#D9ECFF"><td colspan="2">';

                                foreach ($array_status as $status => $nome_status) {



                                    if ($status == 0) {
                                        $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES INATIVAS</td></tr>';
                                    } else {
                                        $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES ATIVAS</td></tr>';
                                    }

                                    while ($row_regioes = mysql_fetch_assoc($qr_regioes)):

                                        $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                        $row_master = mysql_fetch_assoc($qr_master);

                                        if ($row_master['status'] == 0)
                                            continue;

                                        $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM funcionario_acoes_assoc WHERE id_regiao = '$row_regioes[id_regiao]' AND id_funcionario = '$id_user' AND botoes_id = '$row_botoes[botoes_id]'"));
                                        $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';



                                        if ($row_master['id_master'] != $master_anterior) {
                                            echo '<tr  bgcolor="#C7E2E2"><td align="left" colspan="2">' . $row_master['nome'] . ' 
																	 <span style="float:right;"> <input name=""  type="checkbox" value="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td></tr>';
                                        }

                                        echo '<tr bgcolor="#D9ECFF">
																		<td colspan="2">
																			<input name="acoes_folhas[' . $row_botoes['botoes_id'] . ']" type="hidden" value="' . $row_acoes['acoes_id'] . '"/>
																			<input name="regiao_folhas[' . $row_botoes['botoes_id'] . '][]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
																		</td>
																	</tr>';

                                        $master_anterior = $row_master['id_master'];

                                    endwhile;
                                }
                            } else if ($row_botoes['botoes_id'] == 6) {





                                while ($row_acoes = mysql_fetch_assoc($qr_acoes)):

                                    $checked = acoes_checked($row_acoes['acoes_id'], $id_user);

                                    if ($row_acoes['tp_contratacao_id'] != $tipo_contratacao_anterior) {

                                        $nome_tipo = mysql_result(mysql_query("SELECT tipo_contratacao_nome FROM tipo_contratacao WHERE tipo_contratacao_id = '$row_acoes[tp_contratacao_id]'"), 0);


                                        echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" >' . $nome_tipo . ' <span style="float:right;"> <input name=""  type="checkbox" value="acoes_' . $row_acoes['tp_contratacao_id'] . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td>
												
												</tr>';
                                    }


                                    //ações
                                    echo '<tr bgcolor="#D9ECFF"><td width="30">&nbsp;</td> <td><input type="checkbox" name="acoes[]" value="' . $row_acoes['acoes_id'] . '" ' . $checked . ' class="master_acoes_' . $row_acoes['tp_contratacao_id'] . '"/> ' . '(' . $row_acoes['acoes_id'] . ') ' . $row_acoes['acoes_nome'] . '</td></tr>';

                                    $tipo_contratacao_anterior = $row_acoes['tp_contratacao_id'];
                                endwhile;
                            }


                            echo '</td></tr>';




                            echo '</table>';
                        } else if ($row_botoes['botoes_id'] == 100) {
                            ?> 

                                        <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo '(' . $row_botoes['botoes_id'] . ') ' . $row_botoes['botoes_nome']; ?><br>

                                        <table>
                                            <tr>
                                                <td>
                                        <?php
                                        foreach ($array_status as $status => $nome_status) {



                                            if ($status == 0) {
                                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' OR status_reg = '$status' ORDER BY id_master");
                                                echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES INATIVAS</td></tr>';
                                            } else {
                                                $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = '$status' AND status_reg = '$status' ORDER BY id_master");
                                                echo '<tr  bgcolor="#D9ECFF"><td colspan="2" >&nbsp;</td></tr><tr  bgcolor="#74BABA" height="25"><td colspan="2" align="center">REGIÕES ATIVAS</td></tr>';
                                            }

                                            while ($row_regioes = mysql_fetch_assoc($qr_regioes)):

                                                $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regioes[id_master]' ");
                                                $row_master = mysql_fetch_assoc($qr_master);

                                                if ($row_master['status'] == 0)
                                                    continue;
                                                $qr_reg_relatorio = mysql_query("");



                                                $verifica_reg_assoc = mysql_num_rows(mysql_query("SELECT * FROM regioes_relatorios_assoc WHERE id_funcionario = '$_COOKIE[logado]' AND id_regiao = '$row_regioes[id_regiao]'"));
                                                $checked = ($verifica_reg_assoc != 0) ? 'checked="checked"' : '';



                                                if ($row_master['id_master'] != $master_anterior) {
                                                    echo '<tr  bgcolor="#C7E2E2"><td align="left" colspan="2">' . $row_master['nome'] . ' 
																	 <span style="float:right;"> <input name=""  type="checkbox" value="' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"   class="todos_master"  />Marcar/Desmarcar todos </span> </td></tr>';
                                                }

                                                echo '<tr bgcolor="#D9ECFF">
																		<td colspan="2">
																			<input name="regiao_relatorios[]" type="checkbox" value="' . $row_regioes['id_regiao'] . '"  ' . $checked . '  class="master_' . $row_botoes['botoes_id'] . $row_regioes['id_master'] . '_' . $status . '"/>' . $row_regioes['id_regiao'] . ' - ' . ($row_regioes['regiao']) . '
																		</td>
																	</tr>';

                                                $master_anterior = $row_master['id_master'];

                                            endwhile;
                                        }
                                        ?>
                                                </td>
                                            </tr>
                                        </table>


                                                <?php } else { ?>
                                        <input type="checkbox" name="botoes[]" value="<?php echo $row_botoes['botoes_id']; ?>"  <?php if ($row_assoc['botoes_id'] == $row_botoes['botoes_id']) echo 'checked'; ?> class="<?php echo $todos; ?>"> <?php echo '(' . $row_botoes['botoes_id'] . ') ' . $row_botoes['botoes_nome']; ?><br>

                                                    <?php
                                                }
                                            endwhile;

                                            echo '</td>
		</tr>
		
		<tr><td>&nbsp; </td</tr>';

                                            unset($checked);
                                        };

                                    endwhile;
                                    ?>

                        <tr>
                            <td colspan="4" align=center bgcolor="#FFFFFF"><br>
                                <font color='red'> Atenção: <BR> - Verifique o TIPO DE CONTA antes de ATUALIZAR</font></td>
                        </tr>
                        <tr>
                            <td height="56" colspan='4' align='center' bgcolor="#FFFFFF"><input type='submit' name='Submit9' value='ATUALIZAR'>
                                <input type='hidden' name='pag' value='<?= $pag ?>'>
                                <input type='hidden' name='id_cadastro' value='16'>
                                <input type='hidden' name='id_funcionario' value='<?= $row_user['id_funcionario'] ?>'>  </td>
                        </tr>
                    </table>
            </form><br><a href='javascript:history.go(-1);' class='link'><img src='imagens/voltar.gif' border=0></a>


            <script>function validaForm() {
                    d = document.form1;
                    if (d.funcao.value == "") {
                        alert("O campo Função deve ser preenchido!");
                        d.funcao.focus();
                        return false;
                    }
                    if (d.locacao.value == "") {
                        alert("O campo Lotação deve ser preenchido!");
                        d.locacao.focus();
                        return false;
                    }

                    if (d.nome.value == "") {
                        alert("O campo Nome deve ser preenchido!");
                        d.nome.focus();
                        return false;
                    }
                    if (d.nome1.value == "") {
                        alert("O campo Nome para Exibição deve ser preenchido!");
                        d.nome1.focus();
                        return false;
                    }
                    if (d.login.value == "") {
                        alert("O campo Login deve ser preenchido!");
                        d.login.focus();
                        return false;
                    }
                    if (d.nome.value == "") {
                        alert("O campo Nome deve ser preenchido!");
                        d.nome.focus();
                        return false;
                    }
                    return true;
                }
            </script>
            <?php
            break;

        case 13:        //CADASTRO DE UNIDADES
            $id_regiao = $_REQUEST['regiao'];
            ?>
            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">
                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='2' class="fundo_azul"><div class="titulo">Cadastro de Unidades</div></td>
                    </tr>
                    <tr>
                        <td width="30%" class="secao">Projeto:</td>
                        <td width="70%" class="conteudo"><select name='projeto' class='campotexto'>
            <?php
            $result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' AND status_reg = '1'");

            while ($row_pro = mysql_fetch_array($result_pro)) {
                print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
            }
            ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td class="secao">Nome:</td>
                        <td class="conteudo"><input name='nome' type='text' class='campotexto' id='nome' size='30'></td>
                    </tr>
                    <tr>
                        <td class="secao">Local:</td>
                        <td class="conteudo"><input name='local' type='text' class='campotexto' id='local' size='20'></td>
                    </tr>
                    <tr>
                        <td class="secao">Telefone:</td>
                        <td class="conteudo"><input name='tel' type='text' id='tel' size='12' 
                                                    onKeyPress="return(TelefoneFormat(this, event))" 
                                                    onKeyUp="pula(13, this.id, tel2.id)" 
                                                    onFocus="this.style.background = '#CCFFCC'" 
                                                    onBlur="this.style.background = '#FFFFFF'" 
                                                    style='background:#FFFFFF;' class='campotexto'></td>
                    </tr>
                    <tr>
                        <td class="secao">Telefone Recado:</td>
                        <td class="conteudo"><input name='tel2' type='text'id='tel2' size='12'
                                                    onKeyPress="return(TelefoneFormat(this, event))" 
                                                    onKeyUp="pula(13, this.id, responsavel.id)" 
                                                    onFocus="this.style.background = '#CCFFCC'" 
                                                    onBlur="this.style.background = '#FFFFFF'" 
                                                    style='background:#FFFFFF;' class='campotexto'></td>
                    </tr>
                    <tr>
                        <td class="secao">Responsável:</td>
                        <td class="conteudo"><input name='responsavel' type='text' class='campotexto' id='responsavel' size='20'></td>
                    </tr>
                    <tr>
                        <td class="secao">Celular do Responsável:</td>
                        <td class="conteudo">
                            <input name='cel' type='text'  id='cel' size='12' 
                                   onKeyPress="return(TelefoneFormat(this, event))" 
                                   onKeyUp="pula(13, this.id, email.id)" 
                                   onFocus="this.style.background = '#CCFFCC'" 
                                   onBlur="this.style.background = '#FFFFFF'" 
                                   style='background:#FFFFFF;' class='campotexto'></td>
                    </tr>
                    <tr>
                        <td class="secao">E-mail do Responsável:</td>
                        <td class="conteudo"><input name='email' type='text' class='campotexto' id='email' size='20'></td>
                    </tr>

                    <tr>
                        <td height="52" colspan='2' align='center'><input type='submit' name='Submit10' value='CADASTRAR'>
                            <input type='hidden' name='id_cadastro' value='17'>
                            <input type='hidden' name='regiao' value='<?= $id_regiao ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>


            <script>function validaForm() {
                    d = document.form1;
                    if (d.nome.value == "") {
                        alert("O campo Nome deve ser preenchido!");
                        d.nome.focus();
                        return false;
                    }
                    if (d.local.value == "") {
                        alert("O campo Local deve ser preenchido!");
                        d.local.focus();
                        return false;
                    }
                    return true;
                }
            </script>

            <?php
            break;

        case 14: //CADASTRO DE TIPOS DE PAGAMENTOS

            $id_regiao = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
            ?>

            <form action='cadastro2.php' method='post' name='form1' onSubmit="return validaForm()">

                <table width='454' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' class='bordaescura1px' align='center'>
                    <tr>
                        <td  colspan='2'  align="right"><?php include('reportar_erro.php'); ?></td>
                    </tr>
                    <tr>
                        <td height="38" colspan='2' class="fundo_azul"><div class="titulo">Cadastro de Tipos de Pagamentos</div></td>
                    </tr>
                    <tr>
                        <td width="30%" class="secao">Projeto:</td>
                        <td width="70%" class="conteudo">
                            <select name='projeto' class='campotexto'>
            <?php
            $result_pro = mysql_query("SELECT * FROM projeto where id_regiao = '$id_regiao' ");
            while ($row_pro = mysql_fetch_array($result_pro)) {
                print "<option value=$row_pro[0]>$row_pro[0] - $row_pro[nome]</option>";
            }
            ?>
                            </select></td>
                    </tr>
                    <tr>
                        <td class="secao">Tipo Pagamento:</td>
                        <td class="conteudo">
                            <select name='tipopg' type='text' class='campotexto' id='tipopg'>
                                <option value="Depósito em Conta Corrente">Depósito em Conta Corrente</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Dinheiro">Dinheiro</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td height="54" colspan="2" align='center'><input type='submit' name='Submit11' value='CADASTRAR'>
                            <input type='hidden' name='id_cadastro' value='19'>
                            <input type='hidden' name='regiao' value='<?= $id_regiao ?>'></td>
                    </tr>
                </table>
            </form><br><a href='javascript:window.close()' class='link'><img src='imagens/voltar.gif' border=0></a>


            <script>function validaForm() {
                    d = document.form1;
                    if (d.tipopg.value == "") {
                        alert("O campo Tipo de Pagamento deve ser preenchido!");
                        d.tipopg.focus();
                        return false;
                    }
                    return true;
                }
            </script>
            <?php
            break;
    }
    /*
      <script type="text/javascript">
      <!--
      var Accordion1 = new Spry.Widget.Accordion("Accordion1");
      var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgRight:"../SpryAssets/SpryMenuBarRightHover.gif"});
      var TabbedPanels1 = new Spry.Widget.TabbedPanels("TabbedPanels1");
      var CollapsiblePanel1 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel1");
      var sprytooltip1 = new Spry.Widget.Tooltip("sprytooltip1", "#sprytrigger1", {useEffect:"blind"});
      //-->
      </script>
      <script language="javascript" src="designer_input.js"></script>
     */
    ?>
    </body>
    </html>
    <?php
}
?>
