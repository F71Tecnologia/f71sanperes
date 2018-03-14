<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes/EventoClass.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass("../img_menu_principal/");
$icon = $botoes->iconsModulos;
$master = $usuario['id_master'];

$parceiro = $_REQUEST['id'];

$qr_parceiro = mysql_query("SELECT * FROM parceiros WHERE parceiro_id = '$parceiro'");
$row_parceiro = mysql_fetch_assoc($qr_parceiro);

$regioes = GlobalClass::carregaRegioes($master);

if ($_POST['pronto'] == 'edicao') {

    $update_logo = (!empty($_POST['nome_logo'])) ? ", parceiro_logo = '$_POST[nome_logo]'" : "";

    $nulos = array('Nome' => $_POST['nome']);
    foreach ($nulos as $campo => $valor) {
        if (empty($valor)) {
            header("Location: cadastro.php?nulo=$campo&m=$_POST[link_master]");
            exit;
        }
    }


    $qr_insert = mysql_query("UPDATE parceiros SET 
						id_regiao 			= '$_POST[regiao]', 
						parceiro_nome 		= '$_POST[nome]'
						$update_logo,
						parceiro_endereco 	= '$_POST[endereco]', 
						parceiro_cnpj 		= '$_POST[cnpj]', 
						parceiro_ccm 		= '$_POST[ccm]', 
						parceiro_ie 		= '$_POST[ie]', 
						parceiro_im 		= '$_POST[im]', 
						parceiro_bairro 	= '$_POST[bairro]', 
						parceiro_cidade 	= '$_POST[cidade]', 
						parceiro_estado 	= '$_POST[estado]', 
						parceiro_telefone 	= '$_POST[telefone]', 
						parceiro_celular 	= '$_POST[celular]', 
						parceiro_email 		= '$_POST[email]', 
						parceiro_contato 	= '$_POST[contato]', 
						parceiro_cpf 		= '$_POST[cpf]', 
						parceiro_banco 		= '$_POST[banco]', 
						parceiro_agencia 	= '$_POST[agencia]', 
						parceiro_conta 		= '$_POST[conta]',
						parceiro_atualizacao = NOW(),
						parceiro_id_atualizacao = '$_COOKIE[logado]'
							
						WHERE parceiro_id = '$_POST[parceiro]' 
						LIMIT 1") or die(mysql_error());

    if ($qr_insert) {

        $nome_funcionario = mysql_result(mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'"), 0);
        registrar_log('ADMINISTRAÇÃO - EDIÇÃO DE PARCEIROS', $nome_funcionario . ' editou o parceiro: ' . '(' . $_POST['parceiro'] . ') - ' . $_POST['nome']);

        header("Location: index.php?sucesso=curso&m=$_POST[link_master]&curso=$id");
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Administrativo</title>

        <link rel="shortcut icon" href="../../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">
    </head>
    <body>
<?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-admin-header"><h2><?php echo $icon[2] ?> - ADMINISTRATIVO</h2></div>
                    <form name="cadastro" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" onSubmit="return validaForm()">
                        <input type="hidden" name="id_parceiro" value="" />

                        <h3>Edição de Parceiros Comercial</h3>
<?php
if ($_GET['nulo']) {
    echo 'O campo <b>' . $_GET['nulo'] . '</b> não pode ficar em branco!';
}
?>

                        <table cellspacing="0" cellpadding="4" class="relacao">
                            <tr>
                                <td class="secao">Nome:</td>
                                <td colspan="5" align="left"><input type="text" id="nome" name="nome" size="50" value="<?php echo $row_parceiro['parceiro_nome']; ?>"></td>
                            </tr>
                            <tr>
                                <td class="secao">Regiao:</td>
                                <td colspan="5" align="left">
                                    <?php echo montaSelect($regioes, $row_parceiro['id_regiao'], "name='regiao' id='regiao'"); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="secao">Endere&ccedil;o:</td>
                                <td colspan="5" align="left"><input type="text" id="endereco" name="endereco" size="90" value="<?php echo $row_parceiro['parceiro_endereco']; ?>"></td>
                            </tr>
                            <tr>
                                <td class="secao">CNPJ:</td>
                                <td align="left"><span class="descricao">
                                        <input type="text" id="cnpj" name="cnpj" size="25" value="<?php echo $row_parceiro['parceiro_cnpj']; ?>">
                                    </span></td>
                                <td class="secao">CCM</td>
                                <td class="secao" align="left"><span class="descricao">
                                        <input type="text" id="ccm" name="ccm" size="25" value="<?php echo $row_parceiro['parceiro_ccm']; ?>">
                                    </span></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="secao">I.E.:</td>
                                <td align="left"><span class="descricao">
                                        <input type="text" id="ie" name="ie" size="25" value="<?php echo $row_parceiro['parceiro_ie']; ?>">
                                    </span></td>
                                <td class="secao">&nbsp;</td>
                                <td class="secao">&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="secao">Bairro:</td>
                                <td align="left"><input type="text" id="bairro" name="bairro" size="40" value="<?php echo $row_parceiro['parceiro_bairro']; ?>"></td>
                                <td class="secao">Cidade:</td>
                                <td align="left"><input type="text" id="cidade" name="cidade" size="30" value="<?php echo $row_parceiro['parceiro_cidade']; ?>"></td>
                                <td class="secao">Estado:</td>
                                <td align="left">
                                    <select name="estado" id="estado"   class="validate[required]" > 
                                        <option value="<?php echo $row_parceiro['parceiro_estado']; ?>"><?php echo $row_parceiro['parceiro_estado']; ?></option>   
                                        <option value=""></option>

                                        <option value="AC">AC</option>  
                                        <option value="AL">AL</option>  
                                        <option value="AM">AM</option>  
                                        <option value="AP">AP</option>  
                                        <option value="BA">BA</option>  
                                        <option value="CE">CE</option>  
                                        <option value="DF">DF</option>  
                                        <option value="ES">ES</option>  
                                        <option value="GO">GO</option>  
                                        <option value="MA">MA</option>  
                                        <option value="MG">MG</option>  
                                        <option value="MS">MS</option>  
                                        <option value="MT">MT</option>  
                                        <option value="PA">PA</option>  
                                        <option value="PB">PB</option>  
                                        <option value="PE">PE</option>  
                                        <option value="PI">PI</option>  
                                        <option value="PR">PR</option>  
                                        <option value="RJ">RJ</option>  
                                        <option value="RN">RN</option>  
                                        <option value="RO">RO</option>  
                                        <option value="RR">RR</option>  
                                        <option value="RS">RS</option>  
                                        <option value="SC">SC</option>  
                                        <option value="SE">SE</option>  
                                        <option value="SP">SP</option>  
                                        <option value="TO">TO</option>  
                                    </select></td>
                            </tr>
                            <tr>
                                <td class="secao">Telefone:</td>
                                <td class="descricao" align="left"><input type="text" id="telefone" name="telefone" size="25" value="<?php echo $row_parceiro['parceiro_telefone']; ?>"></td>
                                <td class="secao">Celular:</td>
                                <td colspan="3" class="descricao" align="left"><input type="text" id="celular" name="celular" size="25" value="<?php echo $row_parceiro['parceiro_celular']; ?>"></td>
                            </tr>
                            <tr>
                                <td class="secao">Email:</td>
                                <td colspan="5" class="descricao" align="left"><input type="text" id="email" name="email" size="40" value="<?php echo $row_parceiro['parceiro_email']; ?>"></td>
                            </tr>
                            <tr>
                                <td class="secao">Contato:</td>
                                <td class="descricao" align="left"><input type="text" id="contato" name="contato" size="40" value="<?php echo $row_parceiro['parceiro_contato']; ?>"></td>
                                <td class="secao">CPF:</td>
                                <td class="descricao" align="left"><input type="text" id="cpf" name="cpf" size="25" value="<?php echo $row_parceiro['parceiro_cpf']; ?>"></td>
                                <td class="secao">&nbsp;</td>
                                <td class="descricao">&nbsp;</td>
                            </tr>
                            <tr>
                                <td class="secao">Banco:</td>
                                <td class="descricao" align="left"><input type="text" id="banco" name="banco" size="30" value="<?php echo $row_parceiro['parceiro_banco']; ?>"></td>
                                <td class="secao">Ag&ecirc;ncia:</td>
                                <td class="descricao" align="left"><input type="text" id="agencia" name="agencia" size="10" value="<?php echo $row_parceiro['parceiro_agencia']; ?>"></td>
                                <td class="secao">Conta:</td>
                                <td class="descricao" align="left"><input type="text" id="conta" name="conta" size="17" value="<?php echo $row_parceiro['parceiro_conta']; ?>"></td>
                            </tr>
                            <tr>
                                <td colspan="6" align="center">
                                    <div id="content_logo"><input type="file" name="logo" id="logo" /></div>
                                    <div id="barra_processo"></div>
                                    <a href="#" onClick="return false" id="remove_logo" rel="<?php echo $row_parceiro['parceiro_id']; ?>">remover logo</a>
                                    <div id="visualiza_img">

                                        <img src="<?php echo 'logo/' . $row_parceiro['parceiro_logo']; ?>" width="300" height="300" id="logomarca" />
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" align="center">
                                    <input type="hidden" name="nome_logo" id="nome_logo" value="" />
                                    <input name="master" value="<?php echo $Master; ?>" type="hidden" />
                                    <input name="parceiro" value="<?php echo $parceiro; ?>" type="hidden" />
                                    <input name="link_master" value="<?php echo $_GET['m']; ?>" type="hidden" />
                                    <input name="pronto" value="edicao" type="hidden" />
                                    <input value="Atualizar" type="submit" class="botao" style="float:right;" />
                                </td>
                            </tr>
                        </table>
                    </form>
                    
                </div>
            </div>
            
            <footer>
                <div class="row">
                    <div class="page-header"></div>
                    <div class="pull-right"><a href="#top">Voltar ao topo</a></div>
                    <div class="col-lg-12">
                        <p>Pay All Fast 3.0</p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script language="javascript" type="text/javascript">
            function validaForm() {
                d = document.cadastro;
                if (d.nome.value == '') {
                    alert('O campo Nome deve ser preenchido!');
                    d.nome.focus();
                    return false;
                }
                return true;
            }


            $(function() {
                <?php
                if ($row_parceiro['parceiro_logo'] == "") {
                    echo "$('#logomarca').hide(); $('#remove_logo').hide();";
                }
                ?>



                $('#remove_logo').click(function() {
                    $.ajax({
                        url: 'removeLogo.php',
                        data: {'caminho': $('#logomarca').attr('src'), 'id': $('#remove_logo').attr('rel')},
                        success: function() {
                            $('#visualiza_img').html('');
                            $('#remove_logo').hide();
                        }
                    });
                });


                $("#cnpj").mask('99.999.999/9999-99');
                $("#telefone").mask('(99) 9999-9999');
                $("#celular").mask('(99) 9999-9999');
                $("#cpf").mask('999.999.999-99');

                $("#cadastro").validationEngine();
            });
        </script>
    </body>
</html>


