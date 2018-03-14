<?php
if ($regiao != '15') { //EXIBE A REGIÃO ESCOLHIDA NA PÁGINA GESTÃO RH
    $tabela = 0;

    $total_clt = NULL;
    $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
    while ($projetos = mysql_fetch_assoc($qr_projetos)) :
        $REClts = mysql_query("SELECT A.*, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(A.data_saida, '%d/%m/%Y') AS data_saida2, B.nome_status, B.`data`, B.data_retorno  FROM rh_clt AS A LEFT JOIN (SELECT * FROM rh_eventos WHERE CURRENT_DATE BETWEEN data AND data_retorno) AS B ON(A.id_clt = B.id_clt)  WHERE A.id_projeto = '$projetos[id_projeto]' AND A.id_regiao = '$regiao' AND (A.status < '60' OR A.status = '200') ORDER BY A.nome ASC");
        $numero_clts = mysql_num_rows($REClts);
        
        if (!empty($numero_clts)) {
            $total_clt++;
        }
        ?>

        <table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:5px;">

            <?php
            while ($row_clt10 = mysql_fetch_array($REClts)) :

                $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1'");
                $ferias = mysql_fetch_assoc($qr_ferias);

                if (empty($ferias['data_ini'])) {
                    $DataEntrada = $row_clt10['data_entrada2'];
                } else {
                    $preview1 = explode('-', $ferias['data_fim']);
                    $preview2 = $preview1[0];
                    $preview3 = explode('/', $row_clt10['data_entrada2']);
                    $DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
                }

                $DataEntrada = explode('/', $DataEntrada);

                $F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
                $F_ini_E = explode('/', $F_ini);



                $data_vencimento = mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1); //DATA DE VENCIMENTO DAS FÉRIAS
                //CALCULO PARA VERIFICAR O VENCIMENTO 3 MESES ANTES
                $prazo_vencimento = mktime(0, 0, 0, $F_ini_E[1] - 3, $F_ini_E[0] - 1, $F_ini_E[2] + 1);

                $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                if ($data_hoje < $data_vencimento and $data_hoje > $prazo_vencimento) {


                    $verifica++;
                    $vencendo = ($data_vencimento - $data_hoje) / 86400;


                    //FIM CALCULO PARA VERIFICAR O VENCIMENTO 3 MESES ANTES	


                    $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1'");
                    $row_pro = mysql_fetch_array($result_pro);

                    // Encriptografando a Variável
                    $link = encrypt("$regiao&2&$row_clt10[0]");
                    $link2 = str_replace("+", "--", $link);
                    // -----------------------------



                    if ($tabela == 0) {
                        ?>
                        <tr>
                            <td colspan="4" class="show">
                                &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome']; ?> 
                            </td>
                        </tr>
                        <tr class="novo_tr">
                            <td width="5%">COD</td>
                            <td width="35%">NOME</td>

                            <td width="	20%" align="center">DATA DE VENCIMENTO</td>
                            <td width="20%" align="center">STATUS</td>
                        </tr>
                        <?php
                        $tabela = 1;
                    }
                    ?>
                    <tr style="background-color:<?php
                    if ($alternateColor++ % 2 != 0) {
                        echo "#F0F0F0";
                    } else {
                        echo "#FDFDFD";
                    }
                    ?>">
                        <td><?= $row_clt10[0] ?></td>

                        <td>
                            
                            <?php if(!empty($row_clt10['nome_status'])){ ?> 
                                <?php echo  $row_clt10['nome'] . "<br> (" . $row_clt10['nome_status'] . ")";  ?>
                            <?php }else{ ?>
                                <a href='index.php?enc=<?= $link2 ?>' target="_blank">
                                    <?php echo  $row_clt10['nome'];  ?>
                                </a>
                            <?php } ?>
                            
                            <?php
                                if ($row_clt10['status'] == '40') {
                                    echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                } elseif ($row_clt10['status'] == '200') {
                                    echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                }
                            ?>
                        </td>


                        <td align="center" class="style3"><?= date('d/m/Y', $data_vencimento) ?></td>
                        <td align="center" class="style3"><span style="color: #0080C0;text-decoration:blink;">VENCE EM <?= (int) $vencendo ?> dias</span></td>
                    </tr>
                    <?php
                }

            endwhile; //	fim $REClts
            $tabela = 0;
            ?>
        </table>

        <?php
    endwhile; //projetos

    if ($verifica == 0) {
        ?>

        <table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:5px;">
            <tr>
                <td colspan="4">
                    <span style="color: #F30; text-weight:bold"><strong>Sem CLT's com férias a vencer</strong></span>
                </td>
            </tr>
        </table>
        <?php
    }
} else {  //EXIBIE TODAS AS REGIÕES QUANDO  A REGIÃO ESCOLHIDA NA PÁGINA GESTÃO RH FOR "ADMINISTRAÇÃO"
    $status_reg = array(1 => 'Ativas', 2 => 'Inativas');

    foreach ($status_reg as $chave => $valor) {


        if ($chave == '1') {

            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 1 AND status_reg = 1 ORDER BY regiao");
        } else {

            $qr_regioes = mysql_query("SELECT * FROM regioes WHERE status = 0 OR status_reg = 0 ORDER BY regiao");
        }



        while ($row_regiao = mysql_fetch_assoc($qr_regioes)):

            $status++;
            if ($row_regiao['id_regiao'] == '15')
                continue;

            $tabela = 0;

            $total_clt = NULL;
            $qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$row_regiao[id_regiao]' AND status_reg = '1' ORDER BY nome ASC");
            while ($projetos = mysql_fetch_assoc($qr_projetos)) :
                $REClts = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[id_projeto]' AND id_regiao = '$row_regiao[id_regiao]' AND (status < '60' OR status = '200')  ORDER BY nome ASC");
                $numero_clts = mysql_num_rows($REClts);
                if (!empty($numero_clts)) {
                    $total_clt++;
                }
                ?>
                <table width="95%" border='0' cellpadding='8' cellspacing='0' bgcolor='#f5f5f5' align='center' style="margin-top:15px;">

                    <?php
                    while ($row_clt10 = mysql_fetch_array($REClts)) :



                        $qr_ferias = mysql_query("SELECT * FROM rh_ferias WHERE id_clt = '$row_clt10[id_clt]' AND status = '1'");
                        $ferias = mysql_fetch_assoc($qr_ferias);

                        if (empty($ferias['data_ini'])) {
                            $DataEntrada = $row_clt10['data_entrada2'];
                        } else {
                            $preview1 = explode('-', $ferias['data_fim']);
                            $preview2 = $preview1[0];
                            $preview3 = explode('/', $row_clt10['data_entrada2']);
                            $DataEntrada = "$preview3[0]/$preview3[1]/$preview2";
                        }

                        $DataEntrada = explode('/', $DataEntrada);

                        $F_ini = date('d/m/Y', mktime(0, 0, 0, $DataEntrada[1] + 12, $DataEntrada[0], $DataEntrada[2]));
                        $F_ini_E = explode('/', $F_ini);



                        $data_vencimento = mktime(0, 0, 0, $F_ini_E[1], $F_ini_E[0] - 1, $F_ini_E[2] + 1); //DATA DE VENCIMENTO DAS FÉRIAS
                        //CALCULO PARA VERIFICAR O VENCIMENTO 3 MESES ANTES
                        $prazo_vencimento = mktime(0, 0, 0, $F_ini_E[1] - 3, $F_ini_E[0] - 1, $F_ini_E[2] + 1);

                        $data_hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));

                        if ($data_hoje < $data_vencimento and $data_hoje > $prazo_vencimento) {

                            $vencendo = ($data_vencimento - $data_hoje) / 86400;


                            //FIM CALCULO PARA VERIFICAR O VENCIMENTO 3 MESES ANTES	

                            $result_pro = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$row_clt10[id_projeto]' AND status_reg = '1'");
                            $row_pro = mysql_fetch_array($result_pro);

                            // Encriptografando a Variável
                            $link = encrypt("$regiao&2&$row_clt10[0]");
                            $link2 = str_replace("+", "--", $link);
                            // -----------------------------

                            if ($tabela == 0) {
                                ?>






                                <tr>
                                    <td colspan="4" class="show">
                                        &nbsp;<span class="seta">&#8250;</span> <?php echo $projetos['nome'] . ' - ' . $row_regiao['regiao']; ?>
                                    </td>
                                </tr>
                                <tr class="novo_tr">
                                    <td width="5%">COD</td>
                                    <td width="35%">NOME</td>

                                    <td width="	20%" align="center">DATA DE VENCIMENTO</td>
                                    <td width="20%" align="center">STATUS</td>
                                </tr>


                                <?php
                                $tabela = 1;
                            }
                            ?>
                            <tr style="background-color:<?php
                        if ($alternateColor++ % 2 != 0) {
                            echo "#F0F0F0";
                        } else {
                            echo "#FDFDFD";
                        }
                            ?>">
                                <td><?= $row_clt10[0] ?></td>


                                <td><a href='index.php?enc=<?= $link2 ?>' target="_blank"><?= $row_clt10['nome'] ?></a>
                                    <?php
                                    if ($row_clt10['status'] == '40') {
                                        echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
                                    } elseif ($row_clt10['status'] == '200') {
                                        echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
                                    }
                                    ?></td>


                                <td align="center" class="style3"><?= date('d/m/Y', $data_vencimento) ?></td>
                                <td align="center" class="style3"><span style="color: #0080C0;text-decoration:blink;">VENCE EM <?= (int) $vencendo ?> dias</span></td>
                            </tr>
                            <?php
                        }

                    endwhile; //	fim $REClts
                    ?>
                </table>

                <?php
            endwhile; //projetos


        endwhile;
    }//FIM FOREACH
}// fim regiao 15
?>
        

