<?php
include('include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');
include('../../classes/formato_data.php');

header("Content-Type: text/html; charset=ISO-8859-1", true);
?>
<html>
    <head>
        <title>Administra&ccedil;&atilde;o de Projetos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    </head>
    <body>

        <?php
        $regiao = mysql_real_escape_string($_GET['regiao']);
        $status = mysql_real_escape_string($_GET['status']);

        $acesso_exclusao = array(9, 5, 87);

        function verifica_expiracao($data) {
            ///verificar projeto expirado ou ok

            list($ano, $mes, $dia) = explode('-', $data);

            $data_termino = mktime(0, 0, 0, $mes, $dia, $ano);
            $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $prazo_renovacao = mktime(0, 0, 0, $mes, $dia - 45, $ano); //45 dias

            if (($prazo_renovacao <= $data_hoje) and ( $data_termino >= $data_hoje)) {
                $dif = ($data_termino - $data_hoje) / 86400;

                if ($dif == 0) {

                    return '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';
                } else {

                    return '<span style="color:#09C;font-weight:bold;">Expira em ' . $dif . ' dias!</span>';
                }
            } elseif ($data_hoje > $data_termino) {
                return '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
            } else {
                return '<span style="color:#0C0;font-weight:bold;"> OK </span>';
            }
        }
        ?>


        <style>
            .linha_lista{
                font-size:12px;
                color:#000;
            }
            .linha_1{
                font-size:12px;
                background-color:#D2E9FF;}
            .linha_2{
                font-size:12px;
                background-color:#F3F3F3;
            }

            .editar {
                margin-left:16px;	
            }
        </style>


<?php
    $qr_projetos = mysql_query("SELECT *, 
                                        IF(inicio != '0000-00-00',DATE_FORMAT(inicio,'%d/%m/%Y'),'Data n�o informada.') as dt_inicio,
                                        IF(termino != '0000-00-00',DATE_FORMAT(termino,'%d/%m/%Y'),'Data n�o informada.') as dt_termino, 
                                        IF(data_assinatura != '0000-00-00',DATE_FORMAT(data_assinatura,'%d/%m/%Y'),'Data n�o informada.') as dt_assinatura 
                                        FROM projeto WHERE id_master = '$Master'  AND id_regiao = '$regiao' AND status_reg = '$status' ORDER BY data_assinatura") or die(mysql_error());
    
    while ($row_projeto = mysql_fetch_assoc($qr_projetos)) :

    $projeto = $row_projeto['id_projeto'];
    $subprojeto = $row_projeto['id_subprojeto'];
    $regiao = $row_projeto['id_regiao'];
    $status_atual = $row_projeto['status_reg'];
    ?>
            <div class="titulo_projeto"> <?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?></div>
            <span style="clear:left"></span>          


            <table  width="100%" style="width:100%; margin-bottom:50px;" cellspacing="1" cellpadding="4" class="relacao">    


                <tr class="secao_nova">
                    <td width="30%">Projeto</td>
                    <td width="34" align="center">Editar</td>
                    <td width="46" align="center">Renovar</td>
                    <td width="38" align="center">Anexos</td>
                    <td width="30%">Local</td>
                    <td width="143">Data de Assinatura</td>
                    <td width="88">In&iacute;cio</td>
                    <td width="101">T&eacute;rmino</td>
                    <td width="48">Valor Estimado</td>
                    <td width="58">Valor Alcan&ccedil;ado</td>
                    <td width="101" align="center">Status</td>
                    <td width="30%" align="center">&uacute;ltima edi&ccedil;&atilde;o</td>

    <?php
    if ($status == 1) {
        if (in_array($_COOKIE['logado'], $acesso_exclusao)) { ?> 
            <td>		
                Desativar projeto
            </td>
            <?php
        }
    }
    ?>


    </tr>
    <tr class="linha_<?php if ($cor++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?>">
        <td>
            <?php
            echo $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome'];
            ?>
                </td>
                <td>
                    <a style="margin-left:3px;margin-top:9px;text-decoration:none;" href="../../projeto/edicao_projeto.php?m=<?= $link_master ?>&id=<?php echo $row_projeto['id_projeto']; ?>&regiao=<?php echo $row_projeto['id_regiao']; ?>">
                        <img src="../../imagens/editar_projeto.png"  title="Editar"/>
                    </a>

                </td>
                <td>
                    <?php
                    // VERIFICANDO se esta expirado
                    $qr_data = mysql_query("SELECT id_projeto, inicio, termino, IF(DATEDIFF(termino,NOW()) <=45,1,'') as renovar FROM `projeto` WHERE id_projeto  = '$row_projeto[id_projeto]'");
                    $resultado = mysql_fetch_assoc($qr_data);

                    if ($resultado['renovar'] == 1) {
                        ?>

                            <a style="margin-left:3px;margin-top:3px;text-decoration:none;" href="../../projeto/subprojeto/index.php?m=<?= $_GET['m'] ?>&id=<?= $row_projeto['id_projeto']; ?>&regiao=<?= $row_projeto['id_regiao']; ?>" ><img src="../../imagens/renovar_projeto.png"  title="Renovar"/></a>

                        <?php } ?>

                    </td>
                    <td>
                        <a href="anexos_projeto.php?m=<?= $link_master ?>&id=<?php echo $row_projeto['id_projeto']; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})">ver</a>
                    </td>
                    <td><?php echo $row_projeto['local']; ?></td>              
                    <td><?php echo $row_projeto['dt_assinatura']; ?></td>
                    <td><?php echo $row_projeto['dt_inicio']; ?></td>
                    <td><?php echo $row_projeto['dt_termino']; ?></td>
                    <td>                
                    <?php
                    //valor alcançado dos projetos   			
                    //ESTA QUERY ANTES PEGA PELA  DATA DE ENTRADA QUE ESTIVESSE ENTRE O INICIO E FIM DO PROJETO		
                    $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                                            (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                            INNER JOIN entrada 
                                            ON notas_assoc.id_entrada = entrada.id_entrada
                                            WHERE notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_projeto] AND entrada.status IN(2) AND notas.tipo_contrato2 = 'projeto'");

                    /* ANTES
                     *   $qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                      (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas)
                      INNER JOIN entrada
                      ON notas_assoc.id_entrada = entrada.id_entrada
                      WHERE notas.id_projeto = '$row_projeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $row_projeto[id_projeto] AND entrada.status IN(1,2) AND notas.tipo_contrato2 = 'projeto'");
                     */

                    $total_entrada = (float) @mysql_result($qr_valor, 0);
                    $valor_alcancado = $total_entrada;
                    $totalizador += $valor_alcancado;
                    if ((( $row_projeto['verba_destinada']) == 0.00) or ( ( $row_projeto['verba_destinada']) == '')) {
                        echo 'Valor n&atilde;o adicionado';
                    } else {
                        echo 'R$ ' . number_format($row_projeto['verba_destinada'], 2, ',', '.');
                    }

                    $totalizador_verbadestinada+= $row_projeto['verba_destinada'];
                    ?>

                    </td>
                    <td>
                        R$ <a href="ver_notas.php?id_projeto=<?php echo $row_projeto['id_projeto'] ?>&tp=1" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})" >


                        <?php echo number_format($valor_alcancado, 2, ',', '.') ?> <img src="../../rh/folha/sintetica/seta_um.gif" width="8" height="8">
                        </a>


                    </td>
                    <td align="center">
                    <?php
                    if ((formato_brasileiro($row_projeto['inicio']) == '00/00/0000' ) or ( formato_brasileiro($row_projeto['termino']) == '00/00/0000' )) {

                        echo "Faltam informa&ccedil;&otilde;es";
                    } else {
                        $function = substr(verifica_expiracao($row_projeto['termino']), 45, -7);

                        if ($function == 'Expirado') {

                            /* 								 
                              $qr_verifica_subprojeto_1 = mysql_query("SELECT id_subprojeto,termino FROM subprojeto WHERE id_projeto = $row_projeto[id_projeto] AND status_reg = '$status' AND termino ='".date('Y-m-d')."' " );
                              $verifica1 = mysql_num_rows($qr_verifica_subprojeto_1);
                             */

                            //verifica se existe subprojeto não expirado
                            $qr_verifica_subprojeto = mysql_query("SELECT id_subprojeto,termino FROM subprojeto WHERE id_projeto = $row_projeto[id_projeto] AND status_reg = '$status' AND termino >='" . date('Y-m-d') . "' ");
                            $verifica = mysql_num_rows($qr_verifica_subprojeto);



                            /*  if($verifica1 != 0) {

                              echo '<span style="color:#F60;font-weight:bold;">Expira hoje!</span>';


                              }else */if ($verifica != 0) {
                                echo '<span style="color:#0C0;font-weight:bold;"> OK </span>';
                            } else {

                                echo '<span  style="color:#F00;font-weight:bold;"> Expirado</span>';
                            }
                        } else {
                            echo verifica_expiracao($row_projeto['termino']);
                        }
                    }
                    ?>

                    </td>
                    <td>

                        <?php
                        //Mostrar o funcionario que fez a última edição e a data de alteração
                        if ($row_projeto['data_atualizacao'] != '0000-00-00') {

                            $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_projeto[id_usuario_atualizacao]'");
                            $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                            $nome_func = explode(' ', $row_funcionario['nome']);

                            echo 'Editado por:<br> ' . $nome_func[0] . ' <br>em ' . formato_brasileiro($row_projeto['data_atualizacao']);
                        } else {

                            $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_projeto[id_usuario]'");
                            $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                            $nome_func = explode(' ', $row_funcionario['nome']);

                            if ($row_projeto['data'] != '') {
                                echo 'Cadastrado por:<br> ' . $nome_func[0] . ' <br>em ' . date('d/m/Y', strtotime($row_projeto['data']));
                            }
                        }
                        ?>

                    </td>


                        <?php
                        if ($status == 1) {
                            if (in_array($_COOKIE['logado'], $acesso_exclusao)) {
                                ?>

                            <td>	 
                                <a href="../../projeto/desativar_projeto1.php?m=<?= $link_master ?>&id=<?= $row_projeto['id_projeto'] ?>&regiao=<?php echo $regiao; ?>"  onClick="return window.confirm('Deseja desativar o projeto <?= $row_projeto['nome'] ?> ?')"><img src="../../imagens/desativar.png" width="30" height="30"/></a>
                            </td>

                    <?php
                    }
                }
                ?>


                </tr>

                <?php
                //Seleciona dados da tabela subprojeto exibie as linhas do projeto
                $qr_subprojeto = mysql_query("SELECT * FROM subprojeto WHERE id_projeto='$row_projeto[id_projeto]' AND status_reg='1' ORDER BY data_assinatura ASC ") or die('erro');
                $n_linha_subprojeto = mysql_num_rows($qr_subprojeto);

                while ($rows_subprojeto = mysql_fetch_assoc($qr_subprojeto)):

                    if ($n_linha_subprojeto != 0) {
                        ?>

                        <tr class="novo">
                            <td> 

                        <?php
                        switch ($rows_subprojeto['tipo_termo_aditivo']) {

                            case 0: echo $rows_subprojeto['numero_contrato'] . ' - ' . $rows_subprojeto['tipo_subprojeto'];
                                break;
                            case 1: echo $rows_subprojeto['numero_contrato'] . ' - ' . $rows_subprojeto['tipo_subprojeto'] . '<br>(Prorroga&ccedil;&atilde;o)';
                                break;
                            case 2: echo $rows_subprojeto['numero_contrato'] . ' - ' . $rows_subprojeto['tipo_subprojeto'] . '<br>(Atualiza&ccedil;&atilde;o Contratual)';
                                break;
                            case 3: echo $rows_subprojeto['tipo_subprojeto'];
                                break;
                        }
                        ?>
                            </td>
                            <td>

                                <?php if ($rows_subprojeto['tipo_termo_aditivo'] == 3) { ?>

                                    <a style="text-decoration:none;" href="../../projeto/editar_rescisao.php?m=<?= $link_master ?>&id=<?php echo $rows_subprojeto['id_subprojeto']; ?>&regiao=<?php echo $rows_subprojeto['id_regiao']; ?>"><img src="../../imagens/editar_projeto.png" title="Editar"/></a>

                                <?php } else { ?>

                                    <a style="text-decoration:none;" href="../../projeto/subprojeto/edicao_subprojeto1.php?m=<?= $link_master ?>&id=<?php echo $rows_subprojeto['id_subprojeto']; ?>&regiao=<?php echo $rows_subprojeto['id_regiao']; ?>"><img src="../../imagens/editar_projeto.png" title="Editar"/></a>
                                <?php } ?>
                            </td>              
                            <td>&nbsp;

                            </td>              
                            <td>
                            <?php
                            if ($rows_subprojeto['tipo_termo_aditivo'] != 3) {
                                ?>
                                    <a href="../../projeto/subprojeto/anexos_subprojeto.php?m=<?= $link_master ?>&id=<?php echo $rows_subprojeto['id_subprojeto']; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})">ver</a>

                            <?php } else { ?>


                                    <a href="../../projeto/anexos_projeto_rescisao.php?m=<?= $link_master ?>&id_projeto=<?php echo $row_projeto['id_projeto']; ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})">ver</a>

                                    <?php } ?>
                            </td>              
                            <td>
                                <?= $row_projeto['local']; ?>
                            </td> 
                            <td><?php
                                if ($rows_subprojeto['data_assinatura'] != '0000-00-00') {

                                    echo formato_brasileiro($rows_subprojeto['data_assinatura']);
                                } else {
                                    echo "Data n&atilde;o informada";
                                }
                                ?>

                            </td>
                            <td>

                            <?php
                            if ($rows_subprojeto['tipo_termo_aditivo'] == 1) {
                                if ($rows_subprojeto['inicio'] != '0000-00-00') {

                                    echo formato_brasileiro($rows_subprojeto['inicio']);
                                } elseif ($rows_subprojeto['tipo_termo_aditivo'] != 3) {
                                    echo "Data n&atilde;o informada";
                                }
                            } elseif ($rows_subprojeto['tipo_subprojeto'] != 'APOSTILAMENTO' and $rows_subprojeto['tipo_subprojeto'] != 'TERMO ADITIVO') {

                                if ($rows_subprojeto['inicio'] != '0000-00-00') {
                                    echo formato_brasileiro($rows_subprojeto['inicio']);
                                } elseif ($rows_subprojeto['tipo_termo_aditivo'] != 3) {
                                    echo "Data n&atilde;o informada";
                                }
                            }
                            ?>

                            </td>
                            <td>

                                <?php
                                if ($rows_subprojeto['tipo_termo_aditivo'] == 1) {
                                    if ($rows_subprojeto['termino'] != '0000-00-00') {

                                        echo formato_brasileiro($rows_subprojeto['termino']);
                                    } else {
                                        echo "Data n&atilde;o informada";
                                    }
                                } elseif ($rows_subprojeto['tipo_subprojeto'] != 'APOSTILAMENTO' and $rows_subprojeto['tipo_subprojeto'] != 'TERMO ADITIVO') {

                                    if ($rows_subprojeto['termino'] != '0000-00-00') {
                                        echo formato_brasileiro($rows_subprojeto['termino']);
                                    } else {
                                        echo "Data n&atilde;o informada";
                                    }
                                }
                                ?>                   
                            </td>
                            <td>               
                                <?php
                                //verba destina subprojeto 


                                /* $qr_valor = mysql_query("SELECT SUM( REPLACE( VALOR,  ',',  '.' ) ) FROM  `entrada`  WHERE  `id_projeto` = '$row_projeto[id_projeto]' AND status ='2' AND tipo ='12' AND data_pg >=' $rows_subprojeto[inicio]' AND  data_pg <='$rows_subprojeto[termino]' ;"); */


                                //ESTA QUERY ANTES PEGA PELA  DATA DE ENTRADA QUE ESTIVESSE ENTRE O INICIO E FIM DO PROJETO				  
                                $qr_valor = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                                                            (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                                            INNER JOIN entrada 
                                                            ON notas_assoc.id_entrada = entrada.id_entrada
                                                            WHERE notas.id_projeto = '$rows_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $rows_subprojeto[id_subprojeto]  AND entrada.status IN(2) AND notas.tipo_contrato2 = 'subprojeto'")or die(mysql_error());
                                /*
                                  $qr_valor=mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM
                                  (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas)
                                  INNER JOIN entrada
                                  ON notas_assoc.id_entrada = entrada.id_entrada
                                  WHERE notas.id_projeto = '$rows_subprojeto[id_projeto]' AND notas.status = 1 AND notas.tipo_contrato = $rows_subprojeto[id_subprojeto]  AND entrada.status IN(,2) AND notas.tipo_contrato2 = 'subprojeto'")or die(mysql_error());

                                 * 
                                 */
                                $total_entrada_sub = (float) @mysql_result($qr_valor, 0);
                                $valor_alcancado_sub = $total_entrada_sub;
                                $totalizador += $valor_alcancado_sub;

                                //totalizador	
                                $verba_destinada = str_replace(',', '.', (str_replace('.', '', $rows_subprojeto['verba_destinada'])));
                                $totalizador_verbadestinada += $verba_destinada;


                                if ((( $rows_subprojeto['verba_destinada']) == 0.00) or ( ( $rows_subprojeto['verba_destinada']) == '')) {
                                    if ($rows_subprojeto['tipo_termo_aditivo'] != 3) {
                                        echo 'Valor n&atilde;o adicionado';
                                    }
                                } else {
                                    echo 'R$ ' . $rows_subprojeto['verba_destinada'];
                                }
                                ?>

                            </td>           
                            <td>

                                <?php if ($rows_subprojeto['tipo_termo_aditivo'] != 3) { ?>
                                    R$ <a href="ver_notas.php?id_projeto=<?php echo $rows_subprojeto['id_projeto'] ?>&tp=2&id_subprojeto=<?php echo $rows_subprojeto['id_subprojeto'] ?>" onClick="return hs.htmlExpand(this, {objectType: 'iframe'})" > 

                                    <?php echo number_format($valor_alcancado_sub, 2, ',', '.') ?> <img src="../../rh/folha/sintetica/seta_um.gif" width="8" height="8">
                                <?php } ?>
                                </a>



                            </td>
                            <td align="center">
                                <?php
                                if ((formato_brasileiro($rows_subprojeto['inicio']) == '00/00/0000' ) or ( formato_brasileiro($rows_subprojeto['termino']) == '00/00/0000' )) {

                                    if ($rows_subprojeto['tipo_subprojeto'] == 'TERMO ADITIVO' or $rows_subprojeto['tipo_subprojeto'] == 'APOSTILAMENTO') {
                                        if ($rows_subprojeto['tipo_termo_aditivo'] == 1) {
                                            echo "Faltam informa&ccedil;&otilde;es";
                                        }
                                    } else {
                                        echo "Faltam informa&ccedil;&otilde;es";
                                    }
                                } else {
                                    switch ($rows_subprojeto['tipo_subprojeto']) {

                                        case 'TERMO ADITIVO': if ($rows_subprojeto['tipo_termo_aditivo'] == 1) {
                                                echo verifica_expiracao($rows_subprojeto['termino']);
                                            }

                                            break;

                                        case 'TERMO DE PARCERIA': echo verifica_expiracao($rows_subprojeto['termino']);
                                            break;

                                        case 'NOVO CONVÊNIO': echo verifica_expiracao($rows_subprojeto['termino']);
                                            break;

                                        case 'APOSTILAMENTO': if ($rows_subprojeto['tipo_termo_aditivo'] == 1) {
                                                echo verifica_expiracao($rows_subprojeto['termino']);
                                            }

                                            break;
                                    }
                                }
                                ?>
                            </td>
                            <td>

                                <?php
                                //Mostrar o funcionario que fez a última edição do subprojeto(renovação)



                                if ($rows_subprojeto['subprojeto_data_atualizacao'] != '0000-00-00') {


                                    $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$rows_subprojeto[subprojeto_id_usuario_atualizacao]'");
                                    $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                    $nome = explode(' ', $row_funcionario['nome']);

                                    echo 'Editado por: <br>' . $nome[0] . ' <br>em ' . formato_brasileiro($rows_subprojeto['subprojeto_data_atualizacao']);
                                } else {

                                    $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$rows_subprojeto[id_usuario]'");
                                    $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                                    $nome_func = explode(' ', $row_funcionario['nome']);

                                    if ($rows_subprojeto['data'] != '') {
                                        echo 'Cadastrado por:<br> ' . $nome_func[0] . ' <br>em ' . date('d/m/Y', strtotime($rows_subprojeto['data']));
                                    }
                                }
                                ?>

                            </td>


                                <?php
                                if ($status == 1) {
                                    if (in_array($_COOKIE['logado'], $acesso_exclusao)) {
                                        echo '<td></td>';
                                    }
                                }
                                ?>
                        </tr>


                                <?php
                                //fim loop da tabela subprojeto(renovação) 
                            }
                        endwhile; //fim if($n_linha_subprojeto!=0)
                        ?>   

                <tr>
                    <td></td>
                    <td></td>
                    <td width="1%"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total:</td>
                    <td width="18%"><?php echo 'R$ ' . number_format($totalizador_verbadestinada, 2, ',', '.'); ?></td>
                    <td width="18%"><?php echo 'R$ ' . number_format($totalizador, 2, ',', '.'); ?><td>
                    <td width="15%" colspan="3" > <?php
                        if ($totalizador > $totalizador_verbadestinada) {
                            echo '<br> <span  style="color:#F00;font-weight:bold; font-size:9px;"> Verba do projeto ultrapassou o valor estimado!</span>';
                        }


                        unset($totalizador_verbadestinada);
                        unset($totalizador);
                        ?>

                    </td>
                    <td ></td>
                    <td></td>
                </tr> 


            </table>

        </td>
    </tr>
    </table>

    <?php
    $regiao_anterior = $row_projeto['id_regiao'];

endwhile; // FIM DO LOOP DOS PROJETOS 
?>

</body>
</html>