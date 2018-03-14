<?php
include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";
include "../classes_permissoes/acoes.class.php";
include("../classes_permissoes/regioes.class.php");

$obj_regiao = new Regioes();
$acoes = new Acoes();

function format_date($data) {
    return implode('/', array_reverse(explode('-', $data)));
}

if (isset($_GET['encriptar'])) {
    $link_enc = encrypt($_GET['encriptar']);
    $link_enc = str_replace('+', '--', $link_enc);
    echo $link_enc;
    exit();
}

//$regiao = $_GET['regiao'];
// RECEBENDO A VARIAVEL CRIPTOGRAFADA
list($regiao) = explode('&', decrypt(str_replace('--', '+', $_REQUEST['enc'])));

$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master,id_regiao FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$tipo_user = $row_funcionario['tipo_usuario'];
$id_master = $row_funcionario['id_master'];
$link_enc = $_REQUEST['enc'];

/* Controle de combustivel */
if (!empty($_REQUEST['apro'])) {
    $apro = $_REQUEST['apro'];
    $vale = $_REQUEST['vale'];
    $valor = $_REQUEST['valor'];
    $regiao = $_REQUEST['regiao'];
    $idComb = $_REQUEST['idcomb'];
    $dataCad = date('Y-m-d');
    if ($apro == 1) {
        mysql_query("UPDATE fr_combustivel SET status_reg = '2', data_libe = '$dataCad', numero='$vale', user_libe = '$id_user' WHERE 
		id_combustivel = '$idComb'");
        $link = "../frota/printcombustivel.php?com=$idComb&regiao=$regiao";
    } else {
        mysql_query("UPDATE fr_combustivel SET status_reg = '0', data_libe = '$dataCad', user_libe = '$id_user' WHERE id_combustivel = '$idComb'");
        $link = "index.php?regiao=$regiao";
    }
    print "<script>
	location.href=\"$link\";
	</script>";
    exit;
}

/* FIM do CONT|ROLE de COMBUSTIVEL */

// Bloqueio Administração
echo bloqueio_administracao($regiao);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Financeiro</title> 
        <script type="text/javascript" src="../js/highslide-with-html.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" />
        <script type="text/javascript">
            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>


        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />


        <script type="text/javascript">
            function confirmacao(url,mensagem){
                if(window.confirm(mensagem)){
                    location.href = url;
                }
            }
            function abrir(URL,w,h,NOMEZINHO) {
                var width = w;
                var height = h;
                var left = 99;
                var top = 99;
                window.open(URL,NOMEZINHO, 'width='+width+', height='+height+', top='+top+', left='+left+', scrollbars=yes, status=no, toolbar=no, location=no, directories=no, menubar=no, resizable=yes, fullscreen=no');
            }
            $(function(){
	
                $("ul.tabs").tabs("div.panes > div");
	
                var iten_banco = $('.bancos');
                var iten_loading = $('.loading');
	
                iten_banco.click(function(){
                    var iten_lista = $(this).next();
		
                    iten_lista.slideToggle('fast');
		
                });
	
                var checkbox = $('.saidas_check');
                var linha_checkbox = $('.saidas_check').parent().parent();
	
                linha_checkbox.click(function(){
                    $(this).find('.saidas_check').attr('checked',!$(this).find('.saidas_check').attr('checked'));
                    if($(this).find('.saidas_check').attr('checked')){
                        $(this).addClass('linha_selectd');
                    }else{
                        $(this).removeClass('linha_selectd');
                    }
                });
	
                checkbox.change(function(){
                    $(this).attr('checked',!$(this).attr('checked'));
                    if($(this).attr('checked')){
                        $(this).parent().parent().addClass('linha_selectd');
                    }else{
                        $(this).parent().parent().removeClass('linha_selectd');
                    }
                });
	
                $('#Pagar_all').click(function(){
                    /*var ids = new Array;
                        $('.saidas_check:checked').each(function(){
                                ids.push($(this).val());
                        });*/
                    var msg = 'Você tem certeza que deseja PAGAR as saidas:\n';
                    $('.saidas_check:checked').each(function(){
                        var id = $(this).parent().next().next().text();
			
                        var nome = $(this).parent().next().next().next().find('span').text();
                        var valor = $(this).parent().next().next().next().next().next().text();
                        msg += '\n'+id+' - '+nome+' '+ valor;
                    });
		
                    if(window.confirm(msg)){
                        var ids = $('#form').serialize();
                        alert(ids);
                        $.post('actions/pagar.selecao_old.php',ids,function(retorno){ window.location.reload();});
                    }
                });
	
	
                $('#Deletar_all').click(function(){
                    /*var ids = new Array;
                        $('.saidas_check:checked').each(function(){
                                ids.push($(this).val());
                        });*/
                    var msg = 'Você tem certeza que deseja DELETAR as saidas:\n';
                    $('.saidas_check:checked').each(function(){
                        var id = $(this).parent().next().next().text();
                        var nome = $(this).parent().next().next().next().find('span').text();
                        var valor = $(this).parent().next().next().next().next().next().text();
                        msg += '\n'+id+' - '+nome+' '+ valor;
			
                    });
                    if(window.confirm(msg)){
                        var ids = $('#form').serialize();
                        $.post('actions/apaga.selecao_old.php',ids,function(retorno){ window.location.reload(); });
                    }
                });
	
	
                $('.date').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true
                });
	
                ////SELECIONAR REGIÃO
                $('#select_regiao'). change(function(){
		
                    var  valor = $(this).val();
                    $.ajax({
                        url: 'relatorios/encriptar.php?encriptar='+valor,
                        success: function(link_encriptado){
				
                            location.href="relatorios.php?enc="+link_encriptado;	
				
                        }
                    });
		
                });
                /*linha_selectd*/

                /*$('.bancos a').click(function(){
		
                        $('.loading').clone(true).prependTo($(this).parent().next('.lista'));
		
                        $('.bancos a').not(this).parent().next('.lista').slideUp('fast');
                        $(this).parent().next('.lista').slideToggle('fast');
                        $(this).parent().next('.lista').load($(this).attr('href'));
                });*/
            });
        </script>
        <link rel="stylesheet" type="text/css" href="style/form.css" />

        <style type="text/css">
            span.nome {	color:#F00000;
            }
            a#Pagar_all,a#Deletar_all{
                background-attachment:initial;
                background-clip:initial;
                background-color:initial;
                background-image:url(http://www.netsorrindo.com/intranet/imagens/fundo_botao.jpg);
                background-origin:initial;
                background-repeat:no-repeat no-repeat;
                color:#555555;
                display:block;
                float:left;
                font-weight:bold;
                height:28px;
                list-style-type:none;
                margin-bottom:12px;
                margin-left:0;
                margin-right:12px;
                margin-top:0;
                padding-top:7px;
                text-align:center;
                text-decoration:none;
                width:150px;
            }

        </style>
        <script type="text/javascript">
            function MM_jumpMenu(targ,selObj,restore){ //v3.0
                eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
                if (restore) selObj.selectedIndex=0;
            }
        </script>



        <link rel="stylesheet" type="text/css" href="../css_principal.css"/>

    </head>
    <body>
        <div id="corpo">
            <div id="conteudo">
                <div id="topo">

                    <table width="100%" border="0">

                        <tr>
                            <td width="11%" height="81" rowspan="3" align="center"><img src="../imagens/logomaster<?= $id_master ?>.gif" width="110" height="79" /></td>
                            <td width="36%" rowspan="3" align="left" valign="top"><br />
                                <span>Financeiro</span><br />
                                <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                                <?php echo date('d/m/Y'); ?><br />
                                Regiao: <?php echo $rw_regiao[1]; ?>
                            </td>

                        </tr>
                    </table>
                    <table width="100%" border="0">     
                        <tr class="barra">
                            <td colspan="2" align="right">
                                TROCAR REGIÃO:  
                                <!------ Visualizando Regiões --------->
                                <select name='select_regiao' class='campotexto' id='select_regiao' > 
                                    <option>Selecione...</option>
                                    <?php $obj_regiao->Preenhe_select_por_master($id_master, $regiao); ?>
                                    
                                        <?php //$obj_regiao->Select_permissao_relatorio($regioes); ?>
                                    
                                </select> 
                            </td>
                        </tr>
                    </table>
                 
                    
                </div>
                <div id="menu_principal">
                    <ul class="tabs">
                            <?php
                            $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_menu_id = 21 ORDER BY botoes_menu_id ");
                            $row_btn_menu = mysql_fetch_assoc($qr_botoes_menu);

                            $qr_botoes = mysql_query("SELECT * FROM botoes 
                                                                INNER JOIN botoes_assoc 
                                                                ON botoes.botoes_id = botoes_assoc.botoes_id
                                                                WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
                            ?>
                        <li <?php if (mysql_num_rows($qr_botoes) == 0) echo 'style="display:none;"'; ?>>
                            <a href="#">
                                <div class="sombra1"><?php echo $row_btn_menu['botoes_menu_nome']; ?>   <div class="texto">  <?php echo $row_btn_menu['botoes_menu_nome']; ?> </div>      </div>
                            </a>
                        </li>           
                    </ul>
                </div>
                
                <div id="submenu"  class="panes">
                    <?php
                    $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_menu_id = 21 ORDER BY botoes_menu_id ");
                    while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                        if ($row_btn_menu['botoes_menu_id'] == 21) {
                            $janela = "window.open('Relatórios', 'width=800, heigth=600')";
                        }
                        ?>
                        <div class="conteudo_aba" style="display:none;"> 
                            <table width="100%" >
                                <tr>
                                    <td class="titulo_tabela">
                                        <div class="sombra1"> <?php echo $row_btn_menu['botoes_menu_nome']; ?>                                               
                                            <div class="texto" > <?php echo $row_btn_menu['botoes_menu_nome']; ?></div>              
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <ul>
                            <?php
                            $Qr = "SELECT * FROM botoes 
                                                INNER JOIN botoes_assoc 
                                                ON botoes.botoes_id = botoes_assoc.botoes_id
                                                WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC";
                            echo "<!-- {$Qr} -->";
                            $qr_botoes = mysql_query($Qr);
                            while ($row_botoes = mysql_fetch_assoc($qr_botoes)) {
                                ?>
                                    <li> 
                                        <a href="<?= $row_botoes['botoes_link'] . $link_enc ?>" title="<?= $row_botoes['botoes_descricao'] ?>">
                                            <img src="../<?= $row_botoes['botoes_img'] ?>"/> <br />
                                            <?php echo $row_botoes['botoes_nome']; ?>
                                        </a>
                                    </li>		
                                    <?php }///fim loop   ?>
                            </ul>
                        </div> 
                    <?php
                    endwhile;
                    ?>
                </div>
                <div class="clear"></div>
                <div class="rodape2">
                    <?php
                    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
                    $master = mysql_fetch_assoc($qr_master);
                    ?>
                    <?= $master['razao'] ?>
                    &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
                </div>
            </div>
        </div>
    </body>
</html>