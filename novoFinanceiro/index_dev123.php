<?php
include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include("../classes_permissoes/regioes.class.php");

$usuario = carregaUsuario();


if(isset($_POST['action']) && !empty($_POST['action'])){
    $id_banco =  isset($_POST['id_banco']) ? $_POST['id_banco'] : NULL;
    $action =  isset($_POST['action']) ? $_POST['action'] : NULL;
    $enc =  isset($_REQUEST['enc']) ? $_REQUEST['enc'] : NULL;
    
    

//    $select_base = ' A.id_saida AS cod, A.nome AS nome, date_format(A.data_vencimento, "%d/%m/%Y") AS data_vencimento_formatada, A.valor AS valor, MONTH(A.data_vencimento) as mes_vencimento ';
//    $where_base = '  A.id_regiao='.$usuario[id_regiao].' AND A.id_banco ='.$id_banco.'  AND A.status = 1  AND A.data_vencimento != "0000-00-00"  AND (YEAR(A.data_vencimento) = "'. date('Y').'" OR YEAR(A.data_vencimento) = "'.(date('Y') - 1).'")  ';
//    $order_by = ' ORDER BY A.data_vencimento ASC ';
//    $group_by = ' GROUP BY A.id_saida';
//    $complemento = '';
//    $tabelas = 'saida AS A  LEFT JOIN saida_files AS B ON A.id_saida = B.id_saida ';
    $label = '';
    switch ($action) {
        case 'vencidas':
            $label = 'VENCIDAS';
            $complemento = ' AND B.data_vencimento < CURDATE() ';
            break;
        case 'hoje':
            $label = 'VENCENDO HOJE';
            $complemento = ' AND B.data_vencimento =  CURDATE() ';
            break;
        case 'vencendo':
            $label = 'A VENCER';
            $complemento = ' AND B.data_vencimento >  CURDATE() ';
            break;
        case 'todas':

            break;
        case 'entradas':
            $tabelas = 'entrada';
            $select_base = '*';
            break;


        default:
            break;
    }
    $sql = "SELECT A.id_banco,A.id_regiao,A.id_projeto,A.nome,B.id_saida AS cod,B.id_projeto AS id_projeto_saida,B.valor,B.`status`, date_format(B.data_vencimento,'%d/%m/%Y') AS data_vencimento_formatada, B.data_impresso, C.id_saida_file, B.user_impresso, D.id_funcionario, D.nome AS nome_funcionario
            FROM bancos AS A
            LEFT JOIN saida AS B ON (A.id_banco = B.id_banco)  
            LEFT JOIN saida_files AS C ON (B.id_saida= C.id_saida) 
            LEFT JOIN funcionario AS D ON (B.user_impresso=D.id_funcionario)
            WHERE A.id_regiao = '$usuario[id_regiao]' AND B.`status` = 1 AND B.data_vencimento != '0000-00-00'  AND A.id_banco =$id_banco $complemento 
            AND (YEAR(B.data_vencimento) = '" . date('Y') . "' OR YEAR (B.data_vencimento) = '" . (date('Y') - 1) . "') 
            GROUP BY B.id_saida
            ORDER BY B.data_vencimento ASC ";
    
//    $sql = "SELECT $select_base FROM $tabelas WHERE $where_base $complemento $group_by $order_by";
    
    $array['sql'] = $sql;
    
    $qr = mysql_query($sql);
    
    $registros = array();
    
    if(mysql_num_rows($qr)>0){
        while($registro =  mysql_fetch_array($qr)){
            $registros[] = $registro;
        }
    }else{
       $registros = array(); 
    }
    
    $array['html'] = '<br><br><small>&nbsp;&nbsp;&nbsp;'.count($registros).' registros encontrados.<small><br><form method="post" onsubmit="return false" action="" id="form" name="forma"><table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">';
    $array['html'] .= '<thead><tr><th colspan="10"><span id="recebeUni"></span> - RELAÇÃO DE ENTRADAS E SAÍDAS '.$label.'</th></tr></thead>';
    $array['html'] .= '<tbody>';
    $array['html'] .= '<tr class="titulo"><td><input type="checkbox"  onclick="if($(this).is(\':checked\')){$(\'.saidas_check\').attr(\'checked\',true);}else{$(\'.saidas_check\').attr(\'checked\',false);}" ></td><td>Cod.</td><td>Nome</td><td>Data vencimento</td><td> Valor</td><td style="width: 190px">Ações</td></tr>';
    
    if(!empty($registros)){
        $tipos_saidas_nao_editaveis = array(167, 175, 168, 169, 260);
        foreach($registros as $linha){        
            if ($linha['tipo_saida'] > 4) {
                if(!in_array($linha['tipo_saida'], $tipos_saidas_nao_editaveis)){
                    $editar = '<a href="cad_edit_saida.php?id='.$linha['cod'].'&tipo=saida&enc='.$enc.'&rel&keepThis=true&TB_iframe=true&width=800&height=600" class="thickbox" style="float: left; margin-right:15px;" > <img src="../imagens/icone_lapis.png" width="16" height="16" border="0" title="EDITAR SAÍDA"></a>';
                }else{
                    $editar = '<a href="editar_data.php?id='.$linha['cod'].'&tipo=saida&enc='.$enc.'"  onclick="return hs.htmlExpand(this, { objectType: \'iframe\', width: 650 } )" style="float: left; margin-right: 15px;" ><img src="../imagens/icone_lapis.png" width="16" height="16" border="0" title="EDITAR SAÍDA" /></a>';
                }
            }else{
                $editar = '<a href="view/editar.saida.naopaga.php?id='.$linha['cod'].'&tipo=saida"  onclick="return hs.htmlExpand(this, { objectType: \'iframe\' } )" style="float: left; margin-right: 15px;" ><img src="image/editar.gif" width="16" height="16" border="0" ></a>';
            }
            $title_impressao = (!empty($linha['id_funcionario'])) ? 'Nota impressa por '.$linha['nome_funcionario'].' em '.$linha['data_impresso'] : '';
            $class_impressao = ($title_impressao!='') ? ' impresso ' : '';
            $link_file = ($linha['id_saida_file']) ? '<a target="_blank" title="Comprovante" href="view/comprovantes.php?'.encrypt('ID=' . $linha['cod'] . '&tipo=0').'" style="float: left; margin-right: 15px;" ><img src="../financeiro/imagensfinanceiro/attach-32.png" width="16" height="16"  border="0"/></a>' : '';
            $form_impressao_class = ($link_file=='') ? ' margin-left: 30px; ' : '';
            $form_impressao = '<div style="float:left; margin-right: 15px; '.$form_impressao_class.'" ><form name="nota" action="nota_debito.php" method="post"><input type="hidden" name="saida" value="'.$linha['cod'].'" /><input type="hidden" name="link_enc" value="'.$enc.'" /><input type="submit" value="" name="enviar" class="imprimir '.$class_impressao.'" title="'.$title_impressao.'"/></form></div>';
            
//            $Comando_pagar_entrada = "'../ver_tudo.php?id=17&pro=$linha[cod]&tipo=pagar&tabela=entrada&regiao=$regiao&idtarefa=2','Deseja CONFIRMAR esta ENTRADA?'";
//            $Comando_delet_entrada = "'../ver_tudo.php?id=17&pro=$row_saida[id_entrada]&tipo=deletar&tabela=entrada&regiao=$regiao','Deseja DELETAR esta ENTRADA?'";
            $cmd_pagar_saida = "'../ver_tudo.php?id=17&pro=$linha[cod]&tipo=pagar&tabela=saida&regiao=$usuario[id_regiao]&idtarefa=1','Deseja PAGAR esta SAIDA?'";
            $cmd_delet_saida = "'../ver_tudo.php?id=17&pro=$linha[cod]&tipo=deletar&tabela=saida&regiao=$usuario[id_regiao]','Deseja DELETAR esta SAIDA?'";
            
            $pagar_saida = '<a href="#" onclick="confirmacao('.$cmd_pagar_saida.')"  style="float: left; margin-right: 15px;" ><img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" title="Pagar saída"/></a>';
            $delet_saida = '<a href="#" onclick="confirmacao('.$cmd_delet_saida.')"  style="float: left; margin-right: 15px;" ><img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" title="Deletar saída"/></a>';
            $array['html'] .=  '<tr><td class="center"><input type="checkbox" class="saidas_check" name="saidas[]" value="'.$linha['cod'].'"></td><td class="center" >'.$linha['cod'].'</td><td>'.$linha['nome'].'</td><td class="center" >'.$linha['data_vencimento_formatada'].'</td><td class="center" >'.number_format($linha['valor'],2,',','.').'</td><td>'.$link_file.' '.$form_impressao.' '.$editar.' '.$pagar_saida.' '.$delet_saida.'</td></tr>';
        } 
        
    }else{
        $array['html'] .=  '<tr><td colspan="7">Nenhum registro encontrado.</td></tr>';
        
    }
    
    $array['html'] .= '</tr></tbody></table></form>';
    
    
    $array['html'] = utf8_encode($array['html']);
    
    echo json_encode($array);
    
    exit(); 
}


if ($_REQUEST['method'] == "destruirSession") {
    $return = array("staus" => true);
    unset($_SESSION['msgError']);
    json_encode($return);
    exit;
}
function link_editar_saida($tipo_saida, $id_saida, $link_enc) {
    ////ESTE ARRAY CONTÉM OS TIPOS DE SAÌDA QUE SÒ PODEM SER EDITADO A DATA DE VENCIMENTO Ex: RESCISÔES QUE VEM DOS "PAGAMENTOS" NA 
    //GESTÃO DE RH
    $array_nao_editaveis = array(167, 175, 168, 169, 260);
    if ($tipo_saida > 4) {
        if (!in_array($tipo_saida, $array_nao_editaveis)) {
            $editar = "<a href=\"cad_edit_saida.php?id=$id_saida&tipo=saida&enc=$link_enc&rel&keepThis=true&TB_iframe=true&width=800&height=600\" class=\"thickbox\"> <img src='../imagens/icone_lapis.png' width='16' height='16' border='0' title='EDITAR SAÍDA'/></a>";
        } else {
            $editar = "<a href=\"editar_data.php?id=$id_saida&tipo=saida&enc=$link_enc\"  onclick=\"return hs.htmlExpand(this, { objectType: 'iframe', width: 650 } )\"><img src='../imagens/icone_lapis.png' width='16' height='16' border='0' title='EDITAR SAÍDA'/></a>";
        }
    } else {
        $editar = "<a href='view/editar.saida.naopaga.php?id=$id_saida&tipo=saida'  onclick=\"return hs.htmlExpand(this, { objectType: 'iframe' } )\" ><img src='image/editar.gif' width='16' height='16' border='0'></a>";
    }
    return $editar;
}
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

$obj_regiao = new Regioes();
$acoes = new Acoes();

$botoes_pagina = montaQuery('botoes_menu', '*', ' botoes_pagina = "3" ORDER BY botoes_menu_id ');
$link_enc = encrypt($usuario['id_regiao']);
$link_enc = str_replace('+', '--', $link_enc);


$sql = 'SELECT * FROM entrada WHERE id_regiao='.$usuario['id_regiao'].' AND `status`=1';
$qr = mysql_query($sql);
$array_entradas = array();
while($entrada = mysql_fetch_array($qr)){
    $array_entradas[$entrada['id_projeto']][] = $entrada;
}



$sql = "SELECT A.id_banco,A.id_regiao,A.id_projeto,A.nome,B.id_saida,B.id_projeto as id_projeto_saida,B.valor,B.`status`,B.data_vencimento,
            IF(data_vencimento=CURDATE(), 'hoje',IF(data_vencimento<CURDATE(),'vencidas','avencer')) as diaStatus ,COUNT(*) as total,SUM(CAST( REPLACE(B.valor, ',', '.') as decimal(13,2))) as totalValor
            FROM bancos AS A
            LEFT JOIN saida AS B ON (A.id_banco = B.id_banco)
            WHERE A.id_regiao = '$usuario[id_regiao]' AND B.`status` = 1 AND B.data_vencimento != '0000-00-00'
            AND (YEAR(B.data_vencimento) = '" . date('Y') . "' OR YEAR (B.data_vencimento) = '" . (date('Y') - 1) . "')
            GROUP BY id_projeto,diaStatus";
$qr = mysql_query($sql);

$tabela = array();
while($res = mysql_fetch_array($qr)){
    
    $tabela[$res['id_projeto']]['nome'] = $res['id_projeto']." - ".$res['nome'];
    $tabela[$res['id_projeto']]['id_banco'] = $res['id_banco'];
    $tabela[$res['id_projeto']][$res['diaStatus']] = $res['total'];
    $tabela[$res['id_projeto']]['todas'] += $res['total'];
    $tabela[$res['id_projeto']]['totalValor'] += $res['totalValor'];
    $tabela[$res['id_projeto']]['entradas'] = (array_key_exists($res['id_projeto'], $array_entradas)) ? count($array_entradas[$res['id_projeto']]) : 0;
    
}

//echo '<pre>';
//print_r($tabela);
//echo '</pre>';
//exit();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <title>Financeiro</title> 
        <script type="text/javascript" src="../js/highslide-with-html.js"></script>
        <link rel="stylesheet" type="text/css" href="../js/highslide.css" />
        <link rel="stylesheet" type="text/css" href="../net1.css" />
        <script type="text/javascript">
            hs.graphicsDir = '../images-box/graphics/';
            hs.outlineType = 'rounded-white';
        </script>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js" ></script>
        <script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
        <script src="../jquery/jquery.tools.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />
        <link rel="stylesheet" href="../jquery/thickbox/thickbox.css" type="text/css" media="screen" />       
        <script type="text/javascript" src="../jquery/thickbox/thickbox.js"></script>        
        <link rel="stylesheet" type="text/css" href="style/form.css" />
        <script>
        $(function(){
            $('a[id^=getRelacao]').click(function(){
                var $this = $(this);
                var acao = $this.attr('data-action');
                var dados = $this.attr('id').split('_');
                var unidade = $this.parents('tr').children('td:first').html();
                $('#relacao_resp').html('<br><br><br><p>&nbsp;&nbsp;&nbsp;Carregando...</p>');
                
                $.post('index_dev123.php?enc=<?= ($_REQUEST['enc']) ? $_REQUEST['enc'] : ''; ;?>',{id_banco: dados[1], action: acao}, function(data){
                    $('#relacao_resp').html(data.html);
//                    $('#relacao_resp').prepend(data.sql);
                    $('#recebeUni').html(unidade);
                    $('#bts_all').show();
                },'json');
            });           
            
        })
        </script>
        <style type="text/css">
            #message-box{
                display: none;
            }
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


            .imprimir{
                background-image: url('../imagens/impressora.png');
                width: 35px;
                height: 35px;
                background-color: transparent;
                border: 0;
                cursor: pointer;

            }

            .impresso{
                background-image: url('../imagens/impressora2.png');
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
    <body  class="novaintra">
        <div id="corpo">
            <div id="conteudo">
                <div id="topo">
                    <div style="float:right;">
                        <?php include('../reportar_erro.php'); ?>
                    </div>
                    <table width="100%" border="0">
                        <tr>
                            <td width="11%" height="81" rowspan="3" align="center"><img src="../imagens/logomaster<?= $usuario['id_master']; ?>.gif" width="110" height="79" /></td>
                            <td width="36%" rowspan="3" align="left" valign="top"><br />
                                <span>Financeiro</span><br />
                                <span class="nome">
                                    <?php echo $usuario['nome'] ?></span><br />
                                    <?php echo date('d/m/Y'); ?><br />
                                    Regiao: <?php echo $usuario['regiao']; ?>
                            </td>
                        </tr>
                    </table>
                    <table width="100%" border="0">     
                        <tr class="barra">
                            <td colspan="2" align="right">
                                TROCAR REGIÃO:  
                                <select name='select_regiao' class='campotexto' id='select_regiao' >                                                                                
                                      <?php $obj_regiao->Preenhe_select_por_master($usuario['id_master'], $usuario['id_regiao']); ?>                                                        
                                </select> 
                            </td>
                        </tr>
                    </table>
                </div>
                <div id="menu_principal">
                    <ul class="tabs">
                        <li>
                            <a href="#">
                                <div class="sombra1">PRINCIPAL<div class="texto">PRINCIPAL</div></div>
                            </a>
                        </li>
                        <?php
                        foreach ($botoes_pagina as $botao) {
                            if ($row_btn_menu['botoes_menu_id'] == 21) {
                                $janela = "window.open('../financeiro/login_adm2.php?regiao= $usuario[id_regiao]','Relatórios', 'width=800, heigth=600, scrollbars=1,resizable=1' );";
                                $class = "class='none'";
                            }
                            ?>
                                <li>                            
                                    <a href="#" onclick="<?= $janela; ?>">
                                        <div class="sombra1" >
                                            <?php echo $botao['botoes_menu_nome']; ?>   
                                            <div class="texto"><?php echo $botao['botoes_menu_nome']; ?> </div>
                                        </div>
                                    </a>
                                </li>
                        <?php } ?>
                    </ul>
                </div>                
                <div id="submenu"  class="panes">
                    <div class="conteudo_aba" style="display:none;">                        
                        <?php
                        $id_master = $usuario['id_master'];
                        include('include_principal.php');
                        unset($id_master);
                        ?>
                    </div>
                </div>
                <div class="clear"></div>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th colspan="7">RELAÇÃO DE ENTRADAS E SAÍDAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="titulo">
                            <td>Projetos</td>
                            <td>Vencidas</td>
                            <td>Vencendo</td>
                            <td>A Vencer</td>
                            <td>Todas</td>
                            <td>Entradas</td>
                            <td>Total</td>
                        </tr>
                        <?php foreach($tabela as $linha){ ?>
                        <tr>
                            <td><?= $linha['nome']; ?></td>
                            <td class="center"><a href="javascript:;" id="getRelacao_<?= $linha['id_banco'] ?>" data-action='vencidas' ><?php echo empty($linha['vencidas']) ? 0 : $linha['vencidas']; ?></a></td>
                            <td class="center"><a href="javascript:;" id="getRelacao_<?= $linha['id_banco'] ?>" data-action="hoje" ><?php echo empty($linha['hoje']) ? 0 : $linha['hoje']; ?></a></td>
                            <td class="center"><a href="javascript:;" id="getRelacao_<?= $linha['id_banco'] ?>" data-action="vencendo"  ><?php echo empty($linha['avencer']) ? 0 : $linha['avencer']; ?></a></td>
                            <td class="center"><a href="javascript:;" id="getRelacao_<?= $linha['id_banco'] ?>"  data-action="todas"  ><?php echo empty($linha['todas']) ? 0 : $linha['todas']; ?></a></td>
                            <td class="center"><a href="javascript:;" id="getRelacao_<?= $linha['id_banco'] ?>"  data-action="entradas" ><?php echo $linha['entradas']; ?></a></td>
                            <td class="center"><?= number_format($linha['totalValor'],2,',','.'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <span style="float:right; margin-top:20px; display: none;" id="bts_all">
                    <a id="Pagar_all" href="#" onclick="return false">Confirmar&nbsp;<img src="../financeiro/imagensfinanceiro/Money-32.png" alt="Editar" border="0" align="absmiddle" /></a>
                    <a id="Deletar_all" href="#" onclick="return false">Deletar&nbsp;<img src="../financeiro/imagensfinanceiro/Delete-32.png" alt="Deletar" border="0" align="absmiddle" /></a>	
                </span>
                <div id="relacao_resp">
                    
                </div>
                
                    <fieldset style="margin-top:150px;">    
                    <?php
                    /////PERMISSAO  RELACAO DE ENTRADAS E SAIDAS 
                    if ($acoes->verifica_permissoes(13)) {
                    ?>
                    <span style="clear:right;"></span>
                    </fieldset>
                    <div class="rodape2">
                        <?php
                        $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$Master'");
                        $master = mysql_fetch_assoc($qr_master);
                        ?>
                        <?= $master['razao'] ?>
                        &nbsp;&nbsp;ACESSO RESTRITO &Agrave; FUNCION&Aacute;RIOS    
                    </div>
                    <?php } ?>
            </div>
        </div>
        <script type="text/javascript">
            
            var closeMessageBox = function(){
                $("#message-box").slideUp("slow");
            }
            
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
                
                if( typeof $("#msgError").val() != "undefined"){
                    $("#message-box").show();
                }else{
                    $("#message-box").hide();
                }
                
                
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
                    var msg = 'Você tem certeza que deseja PAGAR as saidas selecionadas?\n';
                    if(window.confirm(msg)){
                        var ids = $('#form').serialize();
                        $.post('actions/pagar.selecao_old.php',ids,function(retorno){ 
                            alert(retorno);
                            window.location.reload();
                        });
                    }
                });
	
                $('#Deletar_all').click(function(){
                    var msg = 'Você tem certeza que deseja DELETAR as saidas selecionadas?\n';
                    
                    if(window.confirm('Teste =>'+msg)){
                        var ids = $('#form').serialize();
                        $.post('actions/apaga.selecao_old.php',ids,function(){ window.location.reload(); });
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
                        url: 'index.php?encriptar='+valor,
                        success: function(link_encriptado){
                            location.href="index.php?enc="+link_encriptado;	
                        }
                    });
                });
                
                $(".bt-message-red").click(function(){
                    $.ajax({
                        type:"POST",
                        dataType:"json",
                        data:{
                            method:"destruirSession"  
                        }
                    });
                });
            });
        </script>
    </body>
</html>