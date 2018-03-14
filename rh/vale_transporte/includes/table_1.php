<?php if (!empty($arr_cls)) { ?>
    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%">
        <thead>
            <tr>
                <th colspan="15">Projeto <?= $projetos[$post_projeto] . ' (CNPJ DO ARQUIVO ' . $post_cnpj . ') '; ?></th>
            </tr>
            <tr>
                <th>Id</th>
                <th>Matricula</th>
                <th>Nome</th>
                <th>Status</th>
                <th>CPF</th>
                <th>Curso</th>
                <th>CBO</th>
                <th>Horário</th>
                <th>Tipo Cartão</th>
                <th>Número Cartão</th>
                <th>Dias Afastamento</th>
                <th>Dias Úteis</th>
                <th>Dias Trabalhados</th>
                <th>Valor Diário</th>
                <th>Recarga</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $arr_t = array(); 
            
            $cont = 1;
            $cont_zerados = 0;
            $valor_total_pedido = 0;
            foreach ($arr_cls as $clt) {
                
                $arr_t[$clt['id_clt']] = $clt['id_clt'];


                $criar_tarifas = FALSE;
                /* ROTINA PARA ATUALIZAR O VT DO IDR */
                if ($criar_tarifas) {

                    $arr_itinerario = array('IDA', 'VOLTA');

                    foreach ($arr_itinerario as $itinerario) {

                        $valor[$itinerario] = ($clt[valor_teste] / 2);

                        $sql_tarifa = "SELECT * FROM rh_tarifas WHERE valor = $valor[$itinerario] AND status_reg=1 AND "
                                . "itinerario='$itinerario' AND id_regiao='$clt[id_regiao]' LIMIT 1";
                        $result = mysql_query($sql_tarifa);

                        $teste = mysql_num_rows($result);

                        if ($teste <= 0) {

                            $sql_cria = "INSERT INTO rh_tarifas(tipo,valor,itinerario,descricao,id_concessionaria,id_user,data,id_regiao,codigo,status_reg)
                    VALUES('CARTÃO',$valor[$itinerario],'$itinerario','','0',1,'2014-09-22','$clt[id_regiao]','','1');";
                            mysql_query($sql_cria);

                            $sql_tarifa = "SELECT * FROM rh_tarifas WHERE valor = $valor[$itinerario] AND status_reg=1 AND "
                                    . "itinerario='$itinerario' AND id_regiao='$clt[id_regiao]' LIMIT 1";
                            $result = mysql_query($sql_tarifa);

                            $row_tarifa[$itinerario] = mysql_fetch_array($result);
                        } else {
                            $row_tarifa[$itinerario] = mysql_fetch_array($result);
                        }
                    }
                    $cartao_n = str_replace('-', '', str_replace('.', '', str_replace(',', '', $clt['cartao1'])));
//
                    $ida = $row_tarifa['IDA']['id_tarifas'];
                    $volta = $row_tarifa['VOLTA']['id_tarifas'];


                    $sql_clt_vale = 'SELECT * FROM rh_vale WHERE id_clt=' . $clt[id_clt] . ' AND status_reg=1;';
                    $result = mysql_query($sql_clt_vale);

                    $teste2 = mysql_num_rows($result);

                    if ($teste2 <= 0) {
                        $sql_final = "INSERT INTO rh_vale(id_clt, `id_regiao`,`id_projeto`,`id_tarifa1`,`id_tarifa2`, `id_tarifa3`, `id_tarifa4`, `id_tarifa5`,`id_tarifa6`,`qnt1`,`qnt2`,`qnt3`,`qnt4`,`qnt5`,`qnt6`,`cartao1`,`status_reg`)"
                                . " VALUES($clt[id_clt], '$clt[id_regiao]','$clt[id_projeto]','$ida','$volta','0','0','0','0','','','','','','','$cartao_n','1');";
                    } else {
//                        $sql_final = "UPDATE rh_vale SET id_tarifa1='$ida',id_tarifa2='$volta',id_tarifa3='0',id_tarifa4='0',id_tarifa5='0',id_tarifa6='0', cartao1='$cartao_n' WHERE id_clt=$clt[id_clt] LIMIT 1;";
                    }
                    
                    

                    mysql_query($sql_final);
                }
                /* FIM DA ROTINA */
                
                $class = (($cont % 2) == 0) ? 'even' : 'odd';
                if($clt['valor_total']>0){
                ?>
                <tr class="<?= $class; ?>">
                    <td class="center"><?= $clt['id_clt']; ?></td>
                    <td class="center"><?= $clt['matricula']; ?></td>
                    <td><?= $clt['nome']; ?></td>
                    <td><?= $clt['status'].' - '.$clt['nome_status']; ?></td>
                    <td><?= $clt['cpf']; ?></td>
                    <td><?= $clt['id_curso'].' - '.$clt['nome_curso']; ?></td>
                    <td><?= $clt['numero_cbo'].' - '.$clt['nome_cbo']; ?></td>
                    <td><?= $clt['nome_horario']; ?></td>
                    <td class="center"><?= $tipos_cartao[$clt['tipo_cartao']]; ?></td>
                    <td class="center"><?= $clt['cartao1'] ?></td>                                 
                    <td class="center"><?= $clt['dias_afastamento']; ?></td>
                    <td class="center"><?= $clt['dias_uteis'] ?></td>                                 
                    <td class="center"><?= $clt['dias_trabalhados'] ?></td>                             
                    <td class="center"><?= number_format($clt['valor_diario'], 2, ',', '.'); ?></td>                                 
                    <td class="center"><?= number_format($clt['valor_total'], 2, ',', '.'); ?></td>                                 
                </tr>
                <?php
                }else{
                    $arr_clt_sem_recarga[$clt['id_clt']] = $clt;
                }
                if ($clt['valor_total']<=0) {
                    $cont_zerados++;
                }
                $valor_total_pedido += $clt['valor_total'];
                $cont++;
            }
//            echo '<tr><td colspan="15">'.implode(',',$arr_t).'</td></tr>';
            ?>
        </tbody>
        <tfoot>            
            <tr>
                <td style="text-align: right" colspan="15">
                    <br>
                    <h5>Valor total do pedido: <?= number_format($valor_total_pedido, 2, ',', '.'); ?></h5>
                    <h5><?= count($arr_cls); ?> registros encontrados</h5>
                    <h5><a href="javascript:;" onclick="$('#tab_sem_recarga').toggle();"><?= $cont_zerados; ?> registros sem valor para recarga</a></h5>
                    
                    <div id="content_download_txt" style="text-align: right">
                    </div>
                    <input type="hidden" id="post_regiao_1" value="<?= $post_regiao ?>" />
                    <input type="hidden" id="post_projeto_1" value="<?= $post_projeto ?>" />
                    <input type="hidden" id="post_cnpj_1" value="<?= $post_cnpj; ?>" />
                    <input type="hidden" id="post_ano_base_1" value="<?= $post_ano_base; ?>" />
                    <input type="hidden" id="post_mes_base_1" value="<?= $post_mes_base; ?>" />
                    <input type="hidden" id="post_data_inicial_1" value="<?= $post_data_inicial; ?>" />
                    <input type="hidden" id="post_data_final" value="<?= $post_data_final; ?>" />
                    <input type="hidden" id="post_dias_uteis" value="<?= $post_dias_uteis; ?>" />
                    <p id="baixar_txt_1"></p>
                    <input type="button" onclick="finalizar_pedido()" value="Finalizar Pedido" id="bt_finalizar_pedido"/>
                    <br>
                    <br>
                    <table class="grid" cellpadding="0" cellspacing="0" border="0" align="center" width="100%" style="display: none;" id="tab_sem_recarga">
                    <thead>
                            <tr>
                                <th colspan="15">FUNCIONÁRIOS SEM RECARGA</th>
                            </tr>
                            <tr>
                                <th>Id</th>
                                <th>Matricula</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>CPF</th>
                                <th>Curso</th>
                                <th>CBO</th>
                                <th>Horário</th>
                                <th>Tipo Cartão</th>
                                <th>Número Cartão</th>
                                <th>Dias Afastamento</th>
                                <th>Dias Úteis</th>
                                <th>Dias Trabalhados</th>
                                <th>Valor Diário</th>
                                <th>Recarga</th>
                            </tr>
                        </thead>
                        <?php 
                            $cont = 0;
                            foreach($arr_clt_sem_recarga as $clt){
                            $cont++;
                            $class = (($cont % 2) == 0) ? 'even' : 'odd'; 
                        ?>
                                <tr class="<?= $class; ?>">
                                    <td class="center"><?= $clt['id_clt']; ?></td>
                                    <td class="center"><?= $clt['matricula']; ?></td>
                                    <td><?= $clt['nome']; ?></td>
                                    <td><?= $clt['status'].' - '.$clt['nome_status']; ?></td>
                                    <td><?= $clt['cpf']; ?></td>
                                    <td><?= $clt['id_curso'].' - '.$clt['nome_curso']; ?></td>
                                    <td><?= $clt['numero_cbo'].' - '.$clt['nome_cbo']; ?></td>
                                    <td><?= $clt['nome_horario']; ?></td>
                                    <td class="center"><?= $tipos_cartao[$clt['tipo_cartao']]; ?></td>
                                    <td class="center"><?= $clt['cartao1'] ?></td>                                 
                                    <td class="center"><?= $clt['dias_afastamento']; ?></td>
                                    <td class="center"><?= $clt['dias_uteis'] ?></td>                                 
                                    <td class="center"><?= $clt['dias_trabalhados'] ?></td>                             
                                    <td class="center"><?= number_format($clt['valor_diario'], 2, ',', '.'); ?></td>                                 
                                    <td class="center"><?= number_format($clt['valor_total'], 2, ',', '.'); ?></td>                                 
                                </tr>
                            <?php } ?>
                </table>
                    
                    <br>
                    
                </td>
            </tr>
        </tfoot>
    </table>

    <?php
} else {
    if(!isset($alert)){
    $alert['message'] = 'Nenhum resultado para esta consulta';
    }
    include 'box_message.php';
}
?>