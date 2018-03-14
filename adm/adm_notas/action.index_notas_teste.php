<?php
include('../include/restricoes.php');
include('../../conn.php');
include('../../funcoes.php');
include('../include/criptografia.php');

header("Content-Type: text/html; charset=ISO-8859-1", true);

$regiao = mysql_real_escape_string($_GET['regiao']);
$status = mysql_real_escape_string($_GET['status']);
$acesso_exclusao = array(9, 5, 255);
?>
<script type="text/javascript">
    $(function(){
        $('.mostrar_notas').click(function(){
            if($(this).next().css('display') == 'none') {
                $('.relacao').fadeOut();			
                $(this).next().toggle('slow');
            } else {
                $(this).next().fadeOut();
            }
            return false;	
        });
    });
</script>

<?php
// Loop dos Projetos e Subprojetos
$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '$status' ORDER BY id_projeto ASC");
while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
   
//    if($_COOKIE[logado] != 257){
//    $qr_notas = mysql_query("SELECT A.*, C.id_entrada
//                            FROM notas AS A
//                            LEFT JOIN notas_assoc AS B ON (A.id_notas = B.id_notas)
//                            LEFT JOIN entrada AS C ON (B.id_entrada = C.id_entrada)
//                            WHERE A.id_projeto = '$row_projeto[id_projeto]' 	
//                            AND A.status = '1'		
//                            AND A.nota_ano_competencia BETWEEN 2012 AND YEAR(NOW())
//                            ORDER BY A.nota_ano_competencia DESC;
//                            ") or die(mysql_error());
//    }else{
    $qr_notas = mysql_query("SELECT A.*
                            FROM notas A
                            WHERE A.id_projeto = '$row_projeto[id_projeto]' 	
                            AND A.status = '1'		
                            AND A.nota_ano_competencia BETWEEN 2012 AND YEAR(NOW())
                            ORDER BY A.nota_ano_competencia DESC") or die(mysql_error());
//    }
    ?>

    <div class="titulo_projeto"> <?= $row_projeto['id_projeto'] . ' - ' . $row_projeto['nome']; ?></div>
    <span style="clear:left"></span>

    <?php
    // LOOP DE NOTAS					

    while ($row_notas = mysql_fetch_assoc($qr_notas)):
       
        $ano = $row_notas['nota_ano_competencia'];
        $mes = substr($row_notas['data_emissao'], 5, 2);

        // parceiro
        $qr_parceiro = mysql_query("SELECT parceiro_id, parceiro_nome FROM parceiros WHERE parceiro_id = '$row_notas[id_parceiro]'");
        $row_parceiro = mysql_fetch_assoc($qr_parceiro);
        $nome_parceiro = $row_parceiro['parceiro_id'] . ' - ' . $row_parceiro['parceiro_nome'];
        //totalizadores por ano
        
        
        /*$total_notas_anos = mysql_num_rows(mysql_query("SELECT * FROM notas 
                                                                    WHERE notas.id_projeto = '$row_projeto[id_projeto]' 	
                                                                    AND notas.status = '1'	
                                                                    AND YEAR(data_emissao) = '$ano'																	
                                                                    ORDER BY notas.data_emissao DESC"));*/
        
        $total_notas_anos = mysql_num_rows(mysql_query("SELECT A.*
                                                        FROM notas AS A
                                                        /*LEFT JOIN notas_assoc AS B ON (A.id_notas = B.id_notas)
                                                        LEFT JOIN entrada AS C ON (B.id_entrada = C.id_entrada)*/
                                                        where 
                                                        A.id_projeto = '$row_projeto[id_projeto]' AND A.status = '1' AND A.nota_ano_competencia = '$ano'
                                                        ORDER BY A.nota_ano_competencia DESC"));
        $total_ano += str_replace(',', '.', $row_notas['valor']);
        $a++;
        
        if ($ano != $ano_anterior) {
            echo '<a href="#"  class="mostrar_notas" >' . $ano . '</a>';
            echo '<table style="width:100%; margin-bottom:50px; display:none;" cellspacing="1" cellpadding="4" class="relacao">';
            echo '<tr class="secao_nova">
                    <td>-</td>
                    <td>Cod.</td>
                    <td width="18%">Parceiro</td>
                    <td>N&deg; </td>
                    <td>Data de Emiss&atilde;o</td>
                    <td>Valor NF / Carta Medi&ccedil;&atilde;o</td>
                    <td>Repasse<br>Parceiro</td>
                    <td>Diferenca</td>
                    <td>Anexo</td>
                    <td>Editar</td>
                    ';
            if (in_array($_COOKIE['logado'], $acesso_exclusao)) {

                echo' <td>Excluir</td>';
            }

            echo'<td>&Uacute;ltima edi&ccedil;&atilde;o</td>
                <td>Status</td>
            </tr>';
            $ano_anterior = $ano;
        }

        $qr_total_anos = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                                            (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                            INNER JOIN entrada 
                                            ON notas_assoc.id_entrada = entrada.id_entrada
                                            WHERE notas.id_notas = '$row_notas[id_notas]' AND nota_ano_competencia = '$ano'  AND entrada.status = '2';
                                            ");
        $total_entrada_anos = (float) @mysql_result($qr_total_anos, 0);
        $totalizador_repasse_anos += $total_entrada_anos;
        $diferenca_anos = (float) $total_entrada_anos - str_replace(',', '.', $row_notas['valor']);
        $total_diferenca_anos += $diferenca_anos;
        
        
        ?>
        <tr class="linha_<?php if ($alternateColor++ % 2 == 0) { echo 'um'; } else { echo 'dois'; } ?>">
        <?php
//        if($_COOKIE[logado] != 257){
//    //        echo "SELECT * FROM entrada  WHERE id_entrada = '$row_notas[id_entrada]';";
//            $qr_entrada = mysql_query("SELECT * FROM entrada  WHERE id_entrada = '$row_notas[id_entrada]';");
//            $row_entrada = mysql_fetch_assoc($qr_entrada);
//
//
//            switch ($row_entrada['status']) {
//                case 0 :
//                    $cor = "#FF4500";
//                    $status_entrada = "Entrada Cancelada";
//                    break;
//                case 1 :
//                    $cor = "#FFEE00";
//                    $status_entrada = "Entrada A PAGAR";
//                    break;
//                case 2 :
//                    $cor = "#000080";
//                    $status_entrada = "Entrada Paga";
//                    break;
//            }
//        }else{
            $teste[0] = $teste[1] = $teste[2] = 0;
            $qr_entrada = mysql_query("SELECT entrada.* FROM 
                                entrada INNER JOIN notas_assoc ON (entrada.id_entrada = notas_assoc.id_entrada)
                                WHERE notas_assoc.id_notas = '$row_notas[id_notas]'") or die(mysql_error());
            $nEntradas = mysql_num_rows($qr_entrada);
            while($row_entrada = mysql_fetch_assoc($qr_entrada)){
                
                switch ($row_entrada['status']) {
                    case 0 : $teste[0]++;
                    case 1 : $teste[1]++;
                    case 2 : $teste[2]++;
                }
            }

            if($teste[0] >= 1){
                    $cor = "#FF4500";
                    $status_entrada = "Entrada Cancelada";
            }elseif ($teste[1] >= 1) {
                    $cor = "#FFEE00";
                    $status_entrada = "Entrada A PAGAR";
            }elseif ($teste[2] == $nEntradas) {
                    $cor = "#000080";
                    $status_entrada = "Entrada Paga";
            }
//        }
        ?>
            <td><input type="checkbox" name="check[]" id="check<?= $row_notas['id_notas'] ?>" value="<?= $row_notas['id_notas'] ?>" /></td>
            <td><?= $row_notas['id_notas'] ?></td>
            <td><?= $nome_parceiro ?></td>
            <td><?= $row_notas['numero']; ?></td>
            <td><?= implode('/', array_reverse(explode('-', $row_notas['data_emissao']))); ?></td>
            <td>R$ <?php
        $totalizador_valor += str_replace(',', '.', $row_notas['valor']);
        echo number_format($row_notas['valor'], 2, ',', '.');
        ?>
            </td>
            <td>
                <a href="view_entrada.php?nota=<?=$row_notas['id_notas'] ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" style="text-decoration:none;">
                    R$ 
                <?php
                $qr_total = mysql_query("SELECT SUM(REPLACE(entrada.valor,',','.')) FROM 
                                                (notas INNER JOIN notas_assoc ON notas.id_notas = notas_assoc.id_notas) 
                                                INNER JOIN entrada 
                                                ON notas_assoc.id_entrada = entrada.id_entrada
                                                WHERE notas.id_notas = '$row_notas[id_notas]' AND entrada.status = '2';
                                                ");
                $total_entrada = (float) @mysql_result($qr_total, 0);
                $totalizador_repasse += $total_entrada;
                echo number_format($total_entrada, 2, ',', '.');
                ?>
                    <img src="../../novoFinanceiro/image/seta.gif" border="0"/>
                </a>
            </td>
            <td <?php
            $vn = (float) str_replace(',', '.', $row_notas['valor']);
            if ($vn > $total_entrada) {
                echo 'class="vermelho"';
            } else if ($vn < $total_entrada) {
                echo 'class="azul"';
            }
        ?>>R$
        <?php
        $diferenca = (float) $total_entrada - str_replace(',', '.', $row_notas['valor']);
        $totalizador_diferenca += $diferenca;
        echo number_format($diferenca, 2, ',', '.');
        ?>
            </td>

            <td>
            <?php
            /* $qr_imagem	=	mysql_query("SELECT notas.id_notas, notas_files.id_file, notas_files.tipo, notas_files.status
              FROM notas
              INNER JOIN notas_files ON notas_files.id_notas = '$row_notas[id_notas]'");
              $row_imagem	=	mysql_fetch_assoc($qr_imagem); */
            ?>

                <a href="anexo_notas.php?id_nota=<?php echo $row_notas['id_notas']; ?>" target="_blank"  onclick="return hs.htmlExpand(this,{ objectType:'iframe' })">                     
                    <img src="../../imagens/print.png" alt="imprimir" width="50" height="38" />
                </a>

            </td>
            <td>
                <a href="edicao.php?m=<?php echo $_GET['m']; ?>&id=<?php echo $row_notas['id_notas']; ?>">
                    <img src="../../imagens/editar_projeto.png" alt="Editar nota" >
                </a> 

            </td>

        <?php
        if (in_array($_COOKIE['logado'], $acesso_exclusao)) {
            ?>
                <td>
                    <a href="exclusao.php?m=<?php echo $_GET['m']; ?>&id=<?php echo $row_notas['id_notas']; ?>">
                        <img src="../../imagens/lixo.gif" alt="Excluir nota"/></a>
                </td>

        <?php } ?>

            <td>
            <?php
            if ($row_notas['ultima_edicao'] != '0000-00-00 00:00:00') {
                $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[editado_por]'");
                $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                $nome_func = explode(' ', $row_funcionario['nome']);

                $data = date('d/m/Y', strtotime($row_notas['ultima_edicao']));
                echo 'Editado por:<br> ' . $nome_func[0] . ' <br>em ' . $data;
            } else {
                $qr_funcionario = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario='$row_notas[id_funcionario]'");
                $row_funcionario = mysql_fetch_assoc($qr_funcionario);
                $nome_func = explode(' ', $row_funcionario['nome']);
                $data = date('d/m/Y', strtotime($row_notas['nota_data']));

                echo 'Cadastrado por ' . $nome_func[0] . ' em ' . $data;
            }
            ?>


            </td>
            <td>
                <?php echo '<label style="color:'.$cor.';">'.$status_entrada.'</label>'; ?>
            </td>
        </tr>
                <?php                
                if ($a == $total_notas_anos) {
                    echo '<tr>						
                    <td colspan="5" align="right"><strong>TOTAL:</strong></td>
                    <td>' . 'R$ ' . number_format($totalizador_valor, 2, ',', '.') . '</td>
                    <td>' . 'R$ ' . number_format($totalizador_repasse_anos, 2, ',', '.') . '</td>
                    <td>' . 'R$ ' . number_format($total_diferenca_anos, 2, ',', '.') . '</td>
                    <td></td>
                    <td></td>
                    <td></td>';

                    if (in_array($_COOKIE['logado'], $acesso_exclusao)) {

                        echo '<td></td>';
                    }
                    echo '</tr>
                        <tr><td colspan="11"><input type="button" name="lote" value="Notas Selecionadas" id="lote" /></td></tr>
                        </table>';

                    unset($ano_anterior, $a,$total_ano,$totalizador_repasse_anos, $diferenca_anos, $total_diferenca_anos,$totalizador_valor);                    
                }
                unset($cor,$status_entrada);
            endwhile; // Notas
        endwhile;
    ?>
<p style="margin-bottom:40px;">&nbsp;</p> 