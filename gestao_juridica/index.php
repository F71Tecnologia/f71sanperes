<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
} 

                                                
include('include/restricoes.php');
include('../conn.php');
include('../funcoes.php');
//include "../funcoes.php";
include "include/criptografia.php";
include("../classes_permissoes/regioes.class.php");
include("../wfunction.php");

$obj_regiao = new Regioes();

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


$id_user = $_COOKIE['logado'];
$regiao = (isset($_GET['regiao'])) ? $_GET['regiao'] : $usuario['id_regiao'];
$qr_regiao = mysql_query("SELECT id_regiao,regiao FROM regioes WHERE id_regiao = '$regiao'");
$rw_regiao = mysql_fetch_array($qr_regiao);

$query_funcionario = mysql_query("SELECT id_funcionario, nome, tipo_usuario,id_master FROM funcionario WHERE id_funcionario = '$id_user'");
$row_funcionario = mysql_fetch_array($query_funcionario);
$tipo_user = $row_funcionario['tipo_usuario'];

$query_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_funcionario[id_master];'");
$row_master = mysql_fetch_assoc($query_master);
$id_master = $row_master['id_master'];


/*
  //-- ENCRIPTOGRAFANDO A VARIAVEL
  $linkfo = encrypt("$regiao&1");
  $linkfo = str_replace("+","--",$linkfo);
  // -----------------------------

  //-- ENCRIPTOGRAFANDO A VARIAVEL
  $linkevento = encrypt("$regiao");
  $linkevento = str_replace("+","--",$linkevento);

  //-- ENCRIPTOGRAFANDO A VARIAVEL
  $linkferias = encrypt("$regiao&1");
  $linkferias = str_replace("+","--",$linkferias);
  // ----------------------------- */


/* Resumo */



/* Resumo */
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"24", "area"=>"Gestão Jurírica", "id_form"=>"form1", "ativo"=>"Principal");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Gest&atilde;o  Jur&iacute;dica</title> 
      
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
        <link href="../resources/css/main.css" rel="stylesheet" media="screen"/>
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>

        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <!--<link href="../js/highslide.css" rel="stylesheet" type="text/css"  />--> 
       
       
          <!--<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>-->
        <script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
         <script type="text/javascript" src="../js/highslide-with-html.js"></script>
        <script src="../jquery/jquery_ui/development-bundle/ui/jquery.ui.core.js"></script>
        <script src="../jquery/jquery_ui/development-bundle/ui/jquery.ui.widget.js"></script>
        <script src="../jquery/jquery_ui/development-bundle/ui/jquery.ui.mouse.js"></script>
        <script src="../jquery/jquery_ui/development-bundle/ui/jquery.ui.draggable.js"></script>
                
        <style>

            img{ border:0;}

            .link_nome{

                text-decoration:none;
                color:  #4A4A4A;

            }
            .link_nome:hover{

                text-decoration:underline;
            }

        </style>
        <link rel="stylesheet" type="text/css" href="../css_principal.css"/>

    </head>
    <body  >
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-juridico-header" style="text-align:left"><h2><span class="glyphicon glyphicon-briefcase"></span> - Gestão Jurídica <small> - Informações da Gestão Jurídica</small></h2></div>
            <div id="conteudo">
                <div id="topo">

<!--                    <table width="100%">
                        <tr>
                            <td width="11%" height="81" align="center"> <img src="../imagens/logomaster<?= $id_master ?>.gif" width="110" height="79"></td>
                            <td width="36%" align="left" valign="top">
                                <br />
                                GEST&Atilde;O JUR&Iacute;DICA<br />
                                <span class="nome"><?php echo $row_funcionario[1] ?></span><br />
                                <strong>Data:</strong> <?php echo date('d/m/Y'); ?><br />
                                <strong>Regiao:</strong> <?php echo $rw_regiao[1]; ?></td>
                            <td width="53%" align="right"><table width="0" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td align="left" width="60" style="margin-right:10px;"><?php //include('../reportar_erro.php'); ?></td>
                                        <td>
                                            ---- Visualizando Regiões -------
                                            

                                        </td>

                                    </tr>
                                </table></td>
                        </tr>
                    </table>-->

                </div>
                <div class="col-sm-3">
                <div id="menu_principal" class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Gestões</div>
                    <div class="pannel-body">
                    <ul class="tabs">
                        <li>
                            <a href="#" class="btn btn-default ">
                                PRINCIPAL
                            </a>
                        </li>

<?php
$qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 6");
while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

    $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'  ORDER BY botoes_menu_id ");
    while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):

        $qr_verifica = mysql_query("SELECT * FROM botoes 
												INNER JOIN botoes_assoc 
												ON botoes.botoes_id = botoes_assoc.botoes_id
												WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");
        ?>

                                <li <?php if (mysql_num_rows($qr_verifica) == 0) echo 'style="display:none;"'; ?>>
                                    <!--<button type="button" onclick="tableToExcel('tbRelatorio', 'INSS Outras Empresas')" value="Exportar para Excel" class="btn btn-primary" ><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>-->
                                    <a href="#" class="btn btn-default">
                                         <?php echo $row_btn_menu['botoes_menu_nome']; ?>
                                    </a>
                                </li>           
        <?php
    endwhile;
endwhile;
?>
                    </ul>



                    </div>
                </div>
                </div><!-- fim do menu -->

                <div class="col-sm-8">
               
                <div id="submenu"  class="panes">
                    

                    <div class="conteudo_aba" style="display:none;"> 

<?php
//include('calendario.php');

include('listagens/listagem_principal2.php');
?>

                    </div>

                        <?php
                        $qr_botoes_pg = mysql_query("SELECT * FROM botoes_pagina WHERE botoes_pg_id = 6");
                        while ($row_pagina = mysql_fetch_assoc($qr_botoes_pg)):

                            $qr_botoes_menu = mysql_query("SELECT * FROM botoes_menu WHERE botoes_pagina ='$row_pagina[botoes_pg_id]'    ORDER BY botoes_menu_id ");
                            while ($row_btn_menu = mysql_fetch_assoc($qr_botoes_menu)):
                                ?>

                            <div class="conteudo_aba" style="display:none;"> 

<!--                                <table table table-striped table-hover text-sm valign-middle >
                                    <tr>
                                        <td class="titulo_tabela">
                                            <div class="sombra1"> <?php //echo $row_btn_menu['botoes_menu_nome']; ?>                                               
                                                <?php echo $row_btn_menu['botoes_menu_nome']; ?>           
                                            </div>
                                        </td>
                                    </tr>
                                </table>-->

                                <ul>

        <?php
        $qr_botoes = mysql_query("SELECT * FROM botoes 
																	INNER JOIN botoes_assoc 
																	ON botoes.botoes_id = botoes_assoc.botoes_id
																	WHERE botoes.botoes_menu = '$row_btn_menu[botoes_menu_id]'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");

        while ($row_botoes = mysql_fetch_assoc($qr_botoes)) :
            
            if ($row_botoes['botoes_id'] == 132 or $row_botoes['botoes_id'] == 133)
                continue;
            ?>

                                        <li> 
                                            <a href="<?= $row_botoes['botoes_link'] . $regiao ?>" title="<?= $row_botoes['botoes_descricao'] ?>">
                                                <img src="<?= $row_botoes['botoes_img'] ?>"/><br />

                                        <?= $row_botoes['botoes_nome'] ?>
                                            </a>
                                        </li>	


            <?php endwhile; ///fim loop   
        ?>
                                </ul>

                                            <?php
                                            
                                            switch ($row_btn_menu['botoes_menu_id']) {

                                                case 27: include('listagens/listagem_adv_prep.php');
                                                    break;
                                                case 30 : include('alerta_notificacao.php');
                                                    break;

                                                case 31 : include('listagens/listagem_encerrados.php');
                                                    break;

                                                case 32 : include('listagens/ultimo_proc_cad.php');
                                                    break;
                                            }
                                            ?>

                            </div> 

                                <?php
                            endwhile;




                        endwhile;
                        ?>

                </div>
            
            </div><!-- fim do que aparece na tela -->


                <div class="clear"></div> 

</div>
        
            <div style="text-align: left"> 
                <?php include('../template/footer.php'); ?>
            </div>
            
             
        </div>
        
           
       

        <script type="text/javascript">
            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
            $(function(){
                $('#botoes ul li img').fadeTo('fast', 0.7).hover(function(){$(this).fadeTo('fast', 1.0)},function(){$(this).fadeTo('fast', 0.7)});
                $("#resumo table tr:odd").addClass('linha_dois');
                $("#resumo table tr:even").addClass('linha_um');
                $('#resumo').find('table').find('tr:first').addClass('titulo_table');
	
	
                $("ul.tabs").tabs("div.panes > div");
	
                ////SELECIONAR REGIÃO
                $('#select_regiao'). change(function(){
		
                    var  valor = $(this).val();
		
                    location.href="index.php?regiao="+valor;	
				
				
		
		
		
                });
	
            });	


            function MM_jumpMenu(targ,selObj,restore){ //v3.0
                eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
                if (restore) selObj.selectedIndex=0;
            }
            function MM_openBrWindow(theURL,winName,features) { //v2.0
                window.open(theURL,winName,features);
            }
        </script>
        
        
        
    </body>
</html>