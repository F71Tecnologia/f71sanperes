<!--
        <tr> 
          <td height="25" colspan="2" bgcolor="#aaaaaa">
          <marquee scrolldelay="100" scrollamount="5" hspace="0" truespeed="truespeed"> 
          <div align="center"><font color="#ffffff" face="Verdana, Arial, Helvetica, sans-serif" size="1"><strong>A  medida real de um homem n&atilde;o se v&ecirc; na forma como se comporta no conforto, mas em como se mant&eacute;m durante o desafio.</strong></font></div>
          </marquee></td>
        </tr>
       </table>
-->


<!-------- TABELA COM O ICONE SUPORTE ------------->

<div class="conteudo_aba">
    <ul>
        <?php
        $qr_botoes = mysql_query("SELECT * FROM botoes 
                                INNER JOIN botoes_assoc 
                                ON botoes.botoes_id = botoes_assoc.botoes_id
                                WHERE botoes.botoes_menu = '1'  AND botoes_assoc.id_funcionario = '$_COOKIE[logado]'  ORDER BY botoes.botoes_menu ASC");

        while ($row_botoes = mysql_fetch_assoc($qr_botoes)):

            //configurando links	
            $onclick = 'javascript:abrir("' . $row_botoes['botoes_link'] . '","AVDesempenho","750","450","yes")';
            $pagina = '<a href="' . $row_botoes['botoes_link'] . '" target="_blank">';
            switch ($row_botoes['botoes_id']) {
                case 22: $pagina = '<li><a href="#" target="_blank" onClick="javascript:abrir("' . $row_botoes['botoes_link'] . 'id=1&regiao=' . $regiao_usuario . '","AVDesempenho","750","450","yes")"  title="' . $row_botoes['botoes_descricao'] . '"></li>';
                    break;
            }

            $grupo = explode(',', $row_botoes['grupo']);

            //VERIFICA 	SESSÃO DO MENU
            ///exibe em high slide
            if ($row_botoes['botoes_id'] == 44) {  //botão email    
                ?> 

                <li>
                    <a href="#" onClick="window.open('<?= $row_botoes['botoes_link']; ?>','<?= $palavra ?>','width=800,height=600,scrollbars=yes,resizable=yes')"  title="<?php echo $row_botoes['botoes_descricao']; ?>" >    
                        <img src="<?= $row_botoes['botoes_img'] ?>" border="0" align="absmiddle"><br />
                        <?= $row_botoes['botoes_nome'] ?>
                    </a>
                </li>

                <?php
            } elseif ($row_botoes['botoes_onclick'] == 2) {
                ?>

                <li>                                     
                    <a href="<?= $row_botoes['botoes_link'] . $regiao_usuario ?>"  onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"   title="<?php echo $row_botoes['botoes_descricao'] ?>">                                     				 <img src="<?= $row_botoes['botoes_img'] ?>" border=0 align="absmiddle"><br />
                        <?= $row_botoes['botoes_nome'] ?>
                    </a>
                </li>

                <?php
                ///exibe em uma nova janela
            } elseif ($row_botoes['botoes_onclick'] == 1) {
                ?>  
                <li>
                    <a href="#" onClick="window.open('<?= $row_botoes['botoes_link'] . $regiao; ?>&id_user=<?= $_COOKIE['logado'] ?>','AVDesempenho','width=750,height=450,scrollbars=yes')"  title="<?php echo $row_botoes['botoes_descricao'] ?>">    
                        <img src="<?= $row_botoes['botoes_img'] ?>" border=0 align="absmiddle"><br />
                        <?= $row_botoes['botoes_nome'] ?>
                    </a>
                </li>
                <?php
            }
        endwhile;
        ?>  
    </ul>

    <?php if ($_COOKIE['logado'] == "158" || $_COOKIE['logado'] == "87") { ?>
        <!--
        <iframe src="http://netsorrindo.com/intranet/webmailt/index.php" width="400" height="400" id="frame" frameborder="0"></iframe>-->
        
        <script>
            $(function(){
                $.post('webmailt/process.php?lid=English&tid=default',{f_user: "ramon", six: 0, f_pass: "ramon2012", submit: "Logado >>", logado: "158"},function(data){
                    
                },"html");
            })
        </script>
        
    <?php } ?>

</div>

<!-------- FIM TABELA COM O ICONE SUPORTE ------------->


<br />



<!-------- TABELA COM O CALENDÁRIO------------->

<table width="100%">
    <tr>
        <td  class="titulo_tabela">

            <!----- São dois para efeito de sombra----->
            <div class="sombra1"> CALEND&Aacute;RIO

                <div class="texto"> CALEND&Aacute;RIO </div>

            </div>

        </td>

        <td  class="titulo_tabela" colspan="2">

            <!----- São dois para efeito de sombra----->
            <div class="sombra1"> ANIVERSARIANTES DO M&Ecirc;S

                <div class="texto"> ANIVERSARIANTES DO M&Ecirc;S </div>

            </div>

        </td>
    </tr>
    <tr>
        <td align="center" valign="top" > <?php include "index_dfcalendar.php"; ?> </td>

        <td align="center" valign="top" >      
            <table>

                <?php
                $niver = mysql_query("SELECT *,date_format(funcionario.data_nasci, '%d/%m') as data_nasci1 
												FROM funcionario
												INNER JOIN funcionario_master
												ON  funcionario_master.id_funcionario = funcionario.id_funcionario
												WHERE month(data_nasci) = '$mes' 
												AND funcionario.status_reg = '1' 	
												AND funcionario.data_nasci != '0000-00-00'
												AND  funcionario_master.id_master = '$row_master_1[id_master]'	
												GROUP BY funcionario.id_funcionario
												ORDER BY month(data_nasci),day(data_nasci)");


                while ($aniversarios = mysql_fetch_array($niver)) {
                    $nomeNiver = explode(" ", $aniversarios['nome']);
                    ?>
                    <tr>

                        <td align="center" valign="top">
                            <div style=' font-size:12px; text-align:center;'><?= $nomeNiver[0] . ' ' . $nomeNiver[1] ?></div>
                        </td>
                        <td  align="center" valign="top">
                            <div style=' font-size:12px;text-align:center;'><?= $aniversarios['data_nasci1'] ?></div>
                        </td>
                    </tr> 

                <?php } ?>

            </table> 
        </td>
    </tr>
</table>

<!-------- FIM TABELA COM O CALENDÁRIO ------------->




<!-- ############################### feriados JR - 05/03/2010 as 17hs ################################ -->
<table>
    <tr>
        <td  class="titulo_tabela">
            <!----- São dois para efeito de sombra----->
            <div class="sombra1"> FERIADOS DO M&Ecirc;S

                <div class="texto"> FERIADOS DO M&Ecirc;S </div>

            </div>
        </td>
    </tr>
    <tr>
        <td align="left" valign="top" class="style3">
            <div id="niver"><table  width="100%">
                    <?php
                    $feriado = mysql_query("SELECT * FROM rhferiados where 
		  month(data) = '$mes' AND status = '1' AND id_regiao IN(0,$regiao) ORDER BY month(data),day(data)");

                    while ($feriados = mysql_fetch_array($feriado)) {
                        $nomeferiado = $feriados['nome'];

                        list($feriado_ano, $feriado_mes, $feriado_dia) = explode('-', $feriados['data']);
                        $dt_feriado = mktime(0, 0, 0, $feriado_mes, $feriado_dia, date('Y'));
                        $dt_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));


                        if ($dt_feriado < $dt_hoje) {
                            $class = 'style="color:#BBB;"';
                        } else {
                            $class = 'style="color:#666;"';
                        }
                        ?>
                        <tr <?php echo $class; ?>>
                            <td width='80%'>
                                <div style=' font-size:10px'>
                                    <?php echo $nomeferiado; ?>
                                </div>
                            </td>

                            <td width='20%'>
                                <div style=' font-size:10px'>
                                    <?php echo $feriado_dia . '/' . $feriado_mes; ?>
                                </div>
                            </td>
                        </tr> 
                        <?php
                    }
                    ?>
                </table>
            </div>



        </td>
    </tr>
</table>





<script language="javascript">
    
    function validaForm(){
        d = document.form1;
	
        input_box = confirm("Deseja realmente Deletar as tarefas selecionadas?!");
	
        if (input_box == false){
            return false;
        }
	
        return true;
    }
</script>
<tr><td colspan="2">&nbsp;</td></tr>

</table>




