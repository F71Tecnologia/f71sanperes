<style>
    .aviso_box
    {
        position:relative;

        width:50%;
        height:auto;
        text-transform:uppercase;

    }

    /*table {
    text-transform:uppercase;
    font-size:12px;
    
    }
    */
    .letra{
        text-transform:uppercase;

    }
</style>


<?php
for ($mail = 0; $mail < 1; $mail++):



    if ($mail == 1) {

        /* <!--------------------------------------ENVIAR OS AVISOS POR EMAIL A CADA 10 DIAS----------------------------------------------------------------------------------------------------------> */
        $pega_master = $Master;

     //   ob_start(); //come�a a armazenar oc�digo no buffer


        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml">
							<head>
							<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
							<title>Quadro de avisos</title> 
							<style>
							table.relacao {
								width:95%;
								
								margin:0px auto;
								font-size:12px;
								text-align:center;
							}
							
							table.relacao h1 {
								background-color:#C99;
								font-size:13px;
								color:#FFF;
								padding:4px 8px;
								width:150px;
							}
							
							tr.secao_nova {
								text-align:center; 
								background-color:#777; 
								color:#FFF; 
								font-weight:bold; 
								line-height:24px; 
								font-size:11px;
								text-transform:uppercase;
							}
							
							td.secao {
								text-align:right;
								padding-right:8px;
								font-style:italic;
								font-weight:bold;
								width:200px;
							}
							
							
							tr.novo {
								font-size:12px;
								padding:4px; 
								font-weight:normal;
								background-color:#Eff1e9;
							}
							tr.novo2 {
								font-size:10px;
								padding:10px; 
								font-weight:normal;
								background-color:#DBEAE8;
								text-align:center;
								margin-left:30px;
							}
							
							.linha_um {
								background-color:#FAFAFA;
							}
							
							.titulo{
								background-color:#C99;
								font-size:13px;
								color:#FFF;
								padding:4px 8px;
								width:180px;
								margin:20px auto;
								
								}
								#conteudo {
								width:100%;
								text-align:center;
							}
							
							</style>
							</head>
							<body>
							<div id="conteudo">
							<div>';

        $date = date('d/m/Y');
        ?>

        <div style="margin:0 auto; font-size:16px; color:#09F; font-weight:bold;">

            <?php
            if ($Master == '1') {

                echo 'Instituto Sorrindo para a Vida<br>';
            } else if ($Master == '2') {
                echo 'SOE<br>';
            } else if ($Master == '4') {
                echo 'FAHJEL<br>';
            }

            echo '</div>';
        } //fim if
        ?>

        <!-----------------------------------QUADRO DE AVISOS---------------------------------------------------------->


        <!--<div style="background-image:url(../imagens/fundo_quadro.jpg); width:auto; height:auto;">-->

        <table width="100%" >
            <tr>
                <td class="titulo_tabela">

                    <div class="sombra1">QUADRO DE AVISOS                                               
                        <div class="texto">QUADRO DE AVISOS</div>              
                    </div>

                </td>
            </tr>
        </table>


        <div class="letra">


            <table class="relacao">
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>

                <tr class="titulo">
                    <td colspan="3" align="center">PROJETO </td>
                </tr>

            </table> 

            <table class="relacao">
                <tr class="secao_nova">
                    <td>Nome</td>
                    <td>Local</td>
                    <td colspan="2">Status</td>
                </tr>

    <?php
    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master='$Master' ORDER BY regiao");
    while ($row_regiao = mysql_fetch_assoc($qr_regiao)):




        if ($row_regiao['id_regiao'] == '15' or $row_regiao['id_regiao'] == '36' or $row_regiao['id_regiao'] == '37')
            continue;

        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE status_reg = '1' AND id_regiao = '$row_regiao[id_regiao]';");
        while ($row_projeto = mysql_fetch_assoc($qr_projeto)):


            //OBTEM AS DATAS DE T�RMINO DO PROJETO, DATA ATUAL E A DATA 45 DIAS ANTES DA DATA DE T�RMINO
            list($ano, $mes, $dia) = explode('-', $row_projeto['termino']);
            $data_termino = mktime(0, 0, 0, $mes, $dia, $ano);
            $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $prazo_renovacao = mktime(0, 0, 0, $mes, $dia - 45, $ano); //45 dias
            /////////////////////
            ////VERIFICA SE A VERBA DO PROJETO ULTRAPASSOU O VALOR ESTIMADO									
            $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
			    (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
			    INNER JOIN entrada 
			    ON notas_assoc.id_entrada = entrada.id_entrada
			    WHERE notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_projeto] AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'projeto'");

            $total_entrada = (float) @mysql_result($qr_valor, 0);
            $valor_alcancado = $total_entrada;
            $totalizador += $valor_alcancado;
            $totalizador_verbadestinada += $row_projeto['verba_destinada'];



            $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
            $verifica = mysql_num_rows($qr_subprojeto);

            if (!empty($verifica)) {

                while ($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):

                    $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
													   (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
													   INNER JOIN entrada 
													   ON notas_assoc.id_entrada = entrada.id_entrada
													   WHERE notas.id_projeto = '$row_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_subprojeto[id_subprojeto]  AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'subprojeto'") or die(mysql_error());

                    $total_entrada_sub = (float) @mysql_result($qr_valor, 0);
                    $valor_alcancado_sub = $total_entrada_sub;
                    $totalizador += $valor_alcancado_sub;

                    //totalizador	
                    $verba_destinada = str_replace(',', '.', (str_replace('.', '', $row_subprojeto['verba_destinada'])));
                    $totalizador_verbadestinada += $verba_destinada;

                endwhile;
            }

            if ($totalizador > $totalizador_verbadestinada) {
                ?>      <tr class="linha_um">
                                <td align="left"><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                <td align="left"><?php echo $row_regiao['regiao']; ?> </td>
                                <td>
                            <?php
                            echo '<br> <span  style="color:#F00;"> Verba do projeto ultrapassou o valor estimado!</span>';
                            ?>

                                </td>

                            </tr>
                                    <?php
                                }


                                unset($totalizador_verbadestinada);
                                unset($totalizador);

                                /////////////FIM VERIFICA��O DE VERBAS DO PROJETO ///////////////////////////    
                                //// PROJETOS EXPIRANDO ///////                                                     
                                if (($prazo_renovacao <= $data_hoje) and ($data_termino > $data_hoje)) {
                                    $dif = ($data_termino - $data_hoje) / 86400;
                                    ?>
                            <tr class="linha_um">
                                <td align="left"><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                <td align="left"><?php echo $row_regiao['regiao']; ?> </td>
                                <td><?php echo '<span style="color:#09C;">Expira em ' . $dif . ' dias!</span>'; ?> </td>
                                <td>

                            </tr>

                            <?php
                        }

                        ////PROJETOS EXPIRADOS
                        elseif ($data_hoje > $data_termino) {
                            ///// VERIFICA SE EXITE SUBPROJETOS   E EXBIBE CASO EXISTA E ESTEJA EXPIRANDO OU EXPIRADO ////
                            $verifica_ok = 0;
                            $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY termino DESC");
                            $verifica = mysql_num_rows($qr_subprojeto);

                            if (!empty($verifica)) {

                                while ($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)):



                                    list($ano, $mes, $dia) = explode('-', $row_subprojeto['termino']);
                                    $data_termino = mktime(0, 0, 0, $mes, $dia, $ano);
                                    $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                                    $prazo_renovacao = mktime(0, 0, 0, $mes, $dia - 45, $ano); //45 dias

                                    if ($data_hoje < $data_termino) {
                                        $verifica_ok++;
                                    }

                                    ////SUBPROJETOS EXPIRANDO //////
                                    if (($prazo_renovacao <= $data_hoje) and ($data_termino >= $data_hoje)) {
                                        $dif = ($data_termino - $data_hoje) / 86400;
                                        ?>

                                        <tr class="linha_um">
                                            <td align="left"><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                            <td align="left"><?php echo $row_regiao['regiao']; ?> </td>
                                        <?php
                                        if ($dif == 0) {

                                            echo '<td><span style="color:#F60;">Expira hoje!</span></td></tr>';
                                        } else {
                                            ?>


                                                <td><?php echo '<span style="color:#09C;">Renova��o expira em ' . round($dif) . ' dias!</span>'; ?> </td>
                                            </tr>

                                            <?php
                                        }
                                    }

                                    ////SUBPROJETOS EXPIRADO //////
                                    elseif ($data_hoje > $data_termino and $verifica_ok == 0) {
                                        ?>
                                        <tr class="linha_um">
                                            <td align="left"><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                            <td align="left"><?php echo $row_regiao['regiao']; ?> </td>
                                            <td>

                            <?php
                            if ($row_subprojeto['termino'] == '0000-00-00' or $row_subprojeto['inicio'] == '0000-00-00') {
                                echo '<span  style="color:#F90;">Faltam informa��es sobre as datas</span>';
                            } else {
                                echo '<span  style="color:#F00;"> Renova��o expirada</span>';
                            }
                            ?> 

                                            </td>
                                        </tr> 
                                        <?php
                                        $verifica_ok = 1;
                                    }

                                endwhile; //FIM SUBPROJETO	
                            } else {
                                ?>


                                <tr class="linha_um">
                                    <td align="left"><?php echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?> </td>
                                    <td align="left"><?php echo $row_regiao['regiao']; ?> </td>
                                    <td>
                                        <?php
                                        if ($row_projeto['termino'] == '0000-00-00' or $row_projeto['inicio'] == '0000-00-00') {
                                            echo '<span  style="color:#F90;">Faltam informa��es sobre as datas</span>';
                                        } else {
                                            echo '<span  style="color:#F00;"> Expirado</span>';
                                        }
                                        ?> 
                                    </td>
                                </tr>

                                <?php
                            }
                        }

                    endwhile; //projeto


                endwhile; //regiao
                ?>
            </table> 

        </div>



        <div>   

            <!----------------------------------NOTAS FISCAIS-------------------------->

            <table class="relacao">
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>

                <tr class="titulo">
                    <td colspan="3" align="center">NOTAS FISCAIS </td>
                </tr>

            </table> 

            <table class="relacao">
                <tr class="secao_nova">			
                    <td width="50">Regi�o</td>
                    <td width="50">Projeto</td>
                    <td width="130">Valor NF/ Carta Medi��o</td>
                    <td width="130">Repasse</td>
                    <td width="130">Diferen�a</td>
                    <td width="40">Ano</td>
                </tr>


    <?php
    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_master='$Master' ORDER BY regiao");
    while ($row_regiao = mysql_fetch_assoc($qr_regiao)):

        if ($row_regiao['id_regiao'] == '15' or $row_regiao['id_regiao'] == '36' or $row_regiao['id_regiao'] == '37')
            continue; // 	

        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '1'");
        while ($row_projeto = mysql_fetch_assoc($qr_projeto)) :

            for ($ano = 2010; $ano <= date('Y'); $ano++) :

                $qr_notas = mysql_query("SELECT * FROM notas WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1'   AND YEAR(data_emissao) = '$ano' ORDER BY data_emissao DESC");
                $num_notas = mysql_num_rows($qr_notas);

                if (empty($num_notas))
                    continue;

                while ($row_notas = mysql_fetch_assoc($qr_notas)):

                    // totalizadores por ano						
                    $total_ano += $row_notas['valor'];

                    $qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
								(notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
								INNER JOIN entrada 
								ON notas_assoc.id_entrada = entrada.id_entrada
								WHERE notas.id_notas = '$row_notas[id_notas]' AND YEAR(data_emissao) = '$ano' AND entrada.status = 2 ;
								");

                    $totalizador_repasse_anos += @str_replace(',', '.', mysql_result($qr_total_anos, 0));
                    $totalizador_valor += $row_notas['valor'];

                endwhile;

                $class = ($cont++ % 2 == 0) ? 'class="linha_um"' : 'class="linha_2"';
                $totalizador_diferenca_anos = ($totalizador_repasse_anos - $totalizador_valor);


                if (!empty($totalizador_diferenca_anos)) {

                    if ($totalizador_diferenca_anos < 0) {
                        echo '<tr style="background-color:#FEC0C1" height="40">';
                    } else {
                        echo '<tr ' . $classe . ' height="40">';
                    }

                    echo '<td align="center">' . $row_regiao['id_regiao'] . ' - ' . $row_regiao['regiao'] . '</td>
							  <td align="center">(' . $row_projeto['id_projeto'] . ') <br>' . $row_projeto['nome'] . '</td>
							  <td align="center">' . 'R$ ' . number_format($totalizador_valor, 2, ',', '.') . '</td>
							  <td align="center">' . 'R$ ' . number_format($totalizador_repasse_anos, 2, ',', '.') . '</td>
							  <td align="center">' . 'R$ ' . number_format($totalizador_diferenca_anos, 2, ',', '.') . '</td>
							  <td  align="center">' . $ano . '</td>
						</tr> ';
                }

                unset($totalizador_repasse_anos, $totalizador_diferenca_anos, $totalizador_valor);

            endfor; //ANOS
        endwhile; //projeto
    endwhile; //regiao
    ?>
            </table>  
        </div>      


        <div class="titulo_tabela">  


            <!---------OBRIGA��ES OSCIP--------------->

            <table class="relacao">
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>

                <tr class="titulo">
                    <td colspan="3" align="center">OBRIGA��ES DA INSTITUI��O</td>
                </tr>

            </table> 
            <table class="relacao">


                <tr class="secao_nova">			
                    <td>Documento</td>
                    <td>Data de Publica��o</td>
                    <td>Validade</td>
                    <td>Descri��o</td>
                    <td>Status</td>
                </tr>

    <?php
    
   
    
    $qr_tipo_oscip = mysql_query("SELECT * FROM tipo_doc_oscip WHERE tipo_status = 1 ORDER BY tipo_nome") or die(mysql_error());
    while ($row_tipo_oscip = mysql_fetch_assoc($qr_tipo_oscip)):

        ////PEgando o registro mais recente
        //$qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$Master' AND status = 1 AND tipo_oscip = '$row_tipo_oscip[tipo_nome]' AND periodo != 'indeterminado' ORDER BY data_publicacao DESC ") or die(mysql_error());
        $qr_oscip = mysql_query("
        SELECT * FROM
            (SELECT *,
                CASE periodo
                    WHEN 'Dias'    THEN DATEDIFF(DATE_ADD(data_publicacao, INTERVAL numero_periodo DAY),NOW())
                    WHEN 'Meses'   THEN DATEDIFF(DATE_ADD(data_publicacao, INTERVAL numero_periodo MONTH),NOW())
                    WHEN 'Anos'    THEN DATEDIFF(DATE_ADD(data_publicacao, INTERVAL numero_periodo YEAR),NOW())
                    WHEN 'Per�odo' THEN DATEDIFF(oscip_data_termino, NOW())

                END as prazo_expiracao
            FROM obrigacoes_oscip
            WHERE id_master = '$Master' AND status = 1 AND tipo_oscip = '$row_tipo_oscip[tipo_nome]' 
            ORDER BY data_publicacao DESC)
            as obrigacoes  
        WHERE prazo_expiracao >-10 AND prazo_expiracao <= 60 ORDER BY prazo_expiracao ASC, descricao ASC");
    while($row_oscip = mysql_fetch_assoc($qr_oscip)){

        if (mysql_num_rows($qr_oscip) == 0)
            continue;
        //while($row_oscip = mysql_fetch_assoc($qr_oscip)){

            $class = ($cont++ % 2 == 0) ? 'class="linha_um"' : 'class="linha_dois"';

            $periodo = $row_oscip['periodo'];
            $data = $row_oscip['data_publicacao'];
            $n_periodo = $row_oscip['numero_periodo'];
            $data_termino_oscip = $row_oscip['oscip_data_termino'];
            $tipo_oscip = $row_oscip['tipo_oscip'];

            $prazo_dias = 60;

            list($ano, $mes, $dia) = explode('-', $data);
            //descobre a data de vencimento	
            switch ($periodo) {

                case 'Dias':
                    $data_vencimento = mktime(0, 0, 0, $mes, $dia + $n_periodo, $ano);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    break;

                case 'Meses':
                    $data_vencimento = mktime(0, 0, 0, $mes + $n_periodo, $dia, $ano);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                    break;

                case 'Anos':
                    $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $n_periodo);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                    break;

                case 'Per�odo':
                    list($ano_2, $mes_2, $dia_2) = explode('-', $data_termino_oscip);
                    $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                    break;
            }//fim switch

            ///VERIFICANDO SE O DOCUMENTO EST�  DENTRO DO PER�ODO DE 45 DIAS ANTES DA DATA DE VENCIMENTO								
            $data_venc = date('Y-m-d', $data_vencimento);
            $qnt_dias_vencimento = @mysql_result(mysql_query("SELECT  DATEDIFF('$data_venc',NOW()) as dias  FROM `obrigacoes_oscip` WHERE tipo_oscip = '$row_tipo_oscip[tipo_nome]' AND id_master = '$Master'   AND status = 1 ORDER BY data_publicacao DESC "), 0);


            if ($qnt_dias_vencimento == 0) {
                $mesg = '<span style="color:#F60;">Expira hoje!</span>';
            } else if ($qnt_dias_vencimento < 0) {
                $mesg = '<span  style="color:#F00;">Expirado</span>';
            } else if ($qnt_dias_vencimento > 0 and $qnt_dias_vencimento < $prazo_dias) {
                $mesg = '<span style="color:#09C;">Expira em ' . $qnt_dias_vencimento . ' dias!</span>';
            }

            if (!empty($mesg)) {

                echo '<tr ' . $class . '>
                <td width="20%">' . $row_oscip['tipo_oscip'] . '</td>
                <td width="20%">' . implode('/', array_reverse(explode('-', $row_oscip['data_publicacao']))) . '						
                <td>' . $row_oscip['numero_periodo'] . ' ' . $row_oscip['periodo'] . '</td>
                <td width="50%">';

                if (empty($row_oscip['descricao'])) {
                    echo "<span style='font-style: italic; color: red;'>Descri��o n�o informado.<span>";//$qnt_dias_vencimento . '---------';
                } else {
                    echo $row_oscip['descricao'];
                }

                echo '</td>
                <td>' . $mesg . '</td>
                </tr>';
            }
            unset($mesg);
        //}
    }
    endwhile; // fim tipo doc oscip
    
    
    
    
///publica��es do Anexo 1

    $hoje = date('Y-m-d');


    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master' AND  status_reg =1 ORDER BY regiao");
    while ($row_projeto = mysql_fetch_assoc($qr_projeto)):




        $qr_oscip = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_projeto = '$row_projeto[id_projeto]' AND status = '1' AND id_master = '$Master' ORDER BY data_publicacao ASC") or die(mysql_error());
        while ($row_oscip = mysql_fetch_assoc($qr_oscip)):
            $class = ($cont % 2 == 0) ? 'class="linha_um"' : 'class="linha_dois"';

            $periodo = $row_oscip['periodo'];
            $data = $row_oscip['data_publicacao'];
            $n_periodo = $row_oscip['numero_periodo'];
            $data_termino_oscip = $row_oscip['oscip_data_termino'];

            list($ano, $mes, $dia) = explode('-', $data);


            switch ($periodo) {

                case 'Dias':


                    //descobre a data de vencimento	
                    $data_vencimento = mktime(0, 0, 0, $mes, $dia + $n_periodo, $ano);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    break;

                case 'Meses':

                    $data_vencimento = mktime(0, 0, 0, $mes + $n_periodo, $dia, $ano);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    break;



                case 'Anos':

                    $data_vencimento = mktime(0, 0, 0, $mes, $dia, $ano + $n_periodo);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                    break;

                case 'Per�odo':
                    list($ano_2, $mes_2, $dia_2) = explode('-', $data_termino_oscip);
                    $data_vencimento = mktime(0, 0, 0, $mes_2, $dia_2, $ano_2);
                    @$prazo_renovacao = $data_vencimento - 5184000;
                    $data_atual = mktime(0, 0, 0, date('m'), date('d'), date('Y'));


                    break;
            }//fim switch




            if ($prazo_renovacao < $data_atual and $data_vencimento > $data_atual) {

                //quantidade de dias para a expira��o
                $dif = ($data_vencimento - $data_atual) / 86400;

                echo '<tr class="novo">
							<td width="20%">' . $resultado['tipo_oscip'] . ' - <br>' . $row_projeto['nome'] . '<br>(' . $row_projeto['regiao'] . ')' . '</td>
							
							<td width="30%">' . $row_oscip['descricao'] . '</td>';
                echo '<td width="20%">' . implode('/', array_reverse(explode('-', $row_oscip['data_publicacao'])));
                echo ' <td>' . $row_oscip2['numero_periodo'] . ' ' . $row_oscip2['periodo'] . '</td>';

                if ($dif == 0) {

                    echo '<td width="100%"><span style="color:#F60;">Expira hoje!</span></td></tr>';
                } else {

                    echo '<td width="200%"><span style="color:#09C;">Expira em ' . (int) $dif . ' dias!</span></td></tr>';
                }
            } elseif ($data_atual > $data_vencimento) {


                $ok = 0;
                $id_expirado = $row_oscip['id_oscip'];
                $status_reg = $row_projeto['status_reg'];
            } else {

                $ok = 1;
            }




        endwhile;


        if ($tipo_anterior != $row_projeto['id_projeto'] and $ok == 0 and $indeterminado != 1) {


            if ($status_reg != 0) {

                if ($id_expirado) {

                    $qr_expirado = mysql_query("SELECT * FROM obrigacoes_oscip WHERE id_master = '$Master' AND status = 1 AND id_oscip = '$id_expirado'") or die(mysql_error());
                    $resultado = mysql_fetch_assoc($qr_expirado);
                    echo '<tr ' . $class . '>
													<td width="30%">' . $resultado['tipo_oscip'] . ' - <br>' . $row_projeto['nome'] . '<br>(' . $row_projeto['regiao'] . ')' . '</td>
													<td width="20%">' . implode('/', array_reverse(explode('-', $resultado['data_publicacao']))) . '
													<td>' . $row_oscip2['numero_periodo'] . ' ' . $row_oscip2['periodo'] . '</td>
													<td width="50%">' . $resultado['descricao'] . '</td>';
                    echo '<td><span  style="color:#F00;">Expirado</span></td></tr>';

                    unset($data_expiracao_recente, $id_expirado, $ok, $indeterminado);
                }
            }
        }



        $tipo_anterior = $row_projeto['id_projeto'];


    endwhile;
    ?>
            </table>

        </div>   
        <div class="titulo_tabela">  


            <!------------------------------PROJETOS SEM PUBLICA��O---------------------->  
                <?php
                ///verifica publica��o para cada projeto


                unset($qr_projeto, $row_projeto);
              
if($Master == 1){
    
  
                
                $a = 0;
                $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '$Master'  AND status_reg = '1' ORDER BY regiao");
                while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
                    $class = ($cont % 2 == 0) ? 'class="linha_um"' : 'class="linha_dois"';

                    if ($row_projeto['id_regiao'] == '15' or $row_projeto['id_regiao'] == '36' or $row_projeto['id_regiao'] == '37')
                        continue; // 	



                    $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto = '$row_projeto[id_projeto]'  AND status_reg = 1 ORDER BY termino ASC");
                    while ($row_subprojeto = mysql_fetch_assoc($qr_subprojeto)) :

                        $ano_fim = substr($row_subprojeto['termino'], 0, 4);

                    endwhile;

                    if (mysql_num_rows($qr_subprojeto) == 0) {

                        $ano_fim = substr($row_projeto['termino'], 0, 4);
                    }
                    //a publica��o do ano 2010, � publicada em 2011 e assim por diante
                    for ($ano_oscip = 2012; $ano_oscip <= date('Y'); $ano_oscip++) {



                        $ano_anterior = $ano_oscip - 1;


                        if ($ano_fim >= $ano_oscip or $ano_fim = '2010') {
                            $qr_verifica = mysql_query("SELECT * FROM  obrigacoes_oscip WHERE id_projeto = '$row_projeto[id_projeto]' AND tipo_oscip = 'Publica��o Anexo 1 em Jornal' AND  status = 1 AND YEAR(data_publicacao) = '$ano_oscip' ");
                            $verifica_oscip = mysql_num_rows($qr_verifica);


                            if ($verifica_oscip == 0) {
                                if ($a == 0) {
                                    echo '<table class="relacao">
													<tr>
														<td colspan="4">&nbsp;</td>
													</tr>
													<tr>
														<td colspan="4" class="titulo" align="center"> PROJETOS SEM PUBLICA��O</td> 
													</tr>
													<tr  class="secao_nova">
														<td>Projeto</td>';

                                    if ($mail != 1) {
                                        echo '<td>Cadastrar </td>';
                                    }
                                    echo '<td>Regi�o</td>
															  <td>Ano</td>
													</tr>';
                                    $a = 1;
                                }


                                echo '</tr>
								  
										<tr class="linha_um">
											<td align="left">' . $row_projeto['nome'] . '</td>';

                                if ($mail != 1) {
                                    echo '<td><a href="adm_contratos/cadastro_oscip.php?m=' . $link_master . '&id=' . $row_projeto['id_projeto'] . '&tp=Publica��o Anexo 1 em Jornal" target="_blanck"><img src="../imagens/cadastro_oscip.jpg" width="20" heigth="20" border="0"/></a></td>';
                                }

                                echo '<td align="left">' . $row_projeto['regiao'] . '</td>					
											<td align="center">' . ($ano_oscip - 1) . '</td>
										</tr>';
                            }
                        }
                    }
                endwhile; //projeto
                ?>     

            <tr>
                <td>&nbsp;</td>
            </tr>

            </table> 
  <?php } ?>
        </div>
        
       
        <div class="titulo_tabela">    


            <!-------- ALERTA ADMINISTRATIVO E JURIDICO-->
            <?php
            include('alerta_notificacao.php');
            ?>

        </div>	

            <?php
            //FIM DA CRIA��O DO EMAIL

            if ($mail == 1) {
                ?>           
        </table>  

        </div>
        </div>
        </body>
        </html>

                <?php
                // Recebe o valor do buffer na vari�vel $resultado
              //  $resultado = ob_get_contents();



                //  encerra o buffer e limpa tudo que h� nele
            //    ob_end_clean();
            //    ob_clean();

                $headers = "Content-type: text/html; charset=iso-8859-1";

                $qr_aviso = mysql_query("SELECT * FROM avisos WHERE id_master = '$Master' ORDER BY data_ultimo_aviso DESC");
                $row_aviso = mysql_fetch_assoc($qr_aviso);
                $verifica_aviso = mysql_num_rows($qr_aviso);

                if ($verifica_aviso == 0) {
                    if (mysql_query("INSERT INTO avisos (data_ultimo_aviso,id_master) VALUES (NOW(),'$Master')")) {
                        mail('fabricio@sorrindo.org,cinthia@sorrindo.org,fabio.souza@sorrindo.org,vitorio@sorrindo.org,fausto@sorrindo.org,cristiano.santos@sorrindo.org', 'Quadro de Avisos (' . $date . ')', $resultado, $headers);
                    }
                }

                $ultimo_aviso = explode('-', $row_aviso['data_ultimo_aviso']);


                $fim_semana = date('w', mktime(0, 0, 0, $ultimo_aviso[1], $ultimo_aviso[2] + 5, $ultimo_aviso[0]));

                if ($fim_semana == 0) {

                    $add_dia = 1;
                } elseif ($fim_semana == 6) {

                    $add_dia = 2;
                } else {

                    $add_dia = 0;
                }


                list($ano, $mes, $dia) = explode('-', $row_aviso['data_ultimo_aviso']);
                $data_aviso = mktime(0, 0, 0, $mes, $dia + 5 + $add_dia, $ano);

                $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));


                if ($data_hoje == $data_aviso and $row_aviso['data_ultimo_aviso'] != date('Y-m-d') and $row_aviso['id_master'] == $Master) {
                    if (mysql_query("INSERT INTO avisos (data_ultimo_aviso,id_master) VALUES (NOW(),'$Master')")) {


                        mail('fabricio@sorrindo.org,cinthia@sorrindo.org,fabio.souza@sorrindo.org,vitorio@sorrindo.org,fausto@sorrindo.org,cristiano.santos@sorrindo.org', 'Quadro de Avisos (' . $date . ')', $resultado, $headers);
                    }
                }
            }//fim if

        endfor;
        ?>

<div style="clear:left;"></div>

