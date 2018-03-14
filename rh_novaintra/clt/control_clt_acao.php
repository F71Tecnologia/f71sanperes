<?php

/**
 * *****************************************************************************
 * *******************INICIO EDIÇAO E CADASTRO**********************************
 * *****************************************************************************
 */
if (isset($_REQUEST['salvar']) || isset($_REQUEST['editar'])) {
//    print_array($_POST);

    $arrayClt = $_POST;
    unset(
            $arrayClt['salvar'], $arrayClt['editar'], $arrayClt['id_clt'], $arrayClt['radio_contribuicao'], $arrayClt['incapaz_trab_pai'], $arrayClt['incapaz_trab_mae'], $arrayClt['incapaz_trab_conjuge'], $arrayClt['ddir_pai'], $arrayClt['ddir_mae'], $arrayClt['ddir_conjuge'], $arrayClt['dependente'], $arrayClt['id_dependentes'], $arrayClt['outro_dependente'], $arrayClt['outra_empresa'], $arrayClt['id_vale'], $arrayClt['vt']
    );

    $arrayClt['data_nasci'] = implode('-', array_reverse(explode('/', $arrayClt['data_nasci'])));
    $arrayClt['dtChegadaPais'] = implode('-', array_reverse(explode('/', $arrayClt['dtChegadaPais'])));
    $arrayClt['data_escola'] = implode('-', array_reverse(explode('/', $arrayClt['data_escola'])));
    $arrayClt['data_nasc_pai'] = implode('-', array_reverse(explode('/', $arrayClt['data_nasc_pai'])));
    $arrayClt['data_nasc_mae'] = implode('-', array_reverse(explode('/', $arrayClt['data_nasc_mae'])));
    $arrayClt['data_nasc_conjuge'] = implode('-', array_reverse(explode('/', $arrayClt['data_nasc_conjuge'])));
    $arrayClt['dtExpedRNE'] = implode('-', array_reverse(explode('/', $arrayClt['dtExpedRNE'])));
    $arrayClt['data_rg'] = implode('-', array_reverse(explode('/', $arrayClt['data_rg'])));
    $arrayClt['data_emissao'] = implode('-', array_reverse(explode('/', $arrayClt['data_emissao'])));
    $arrayClt['data_validade_oc'] = implode('-', array_reverse(explode('/', $arrayClt['data_validade_oc'])));
    $arrayClt['dtExpNrRic'] = implode('-', array_reverse(explode('/', $arrayClt['dtExpNrRic'])));
    $arrayClt['data_exp_cnh'] = implode('-', array_reverse(explode('/', $arrayClt['data_exp_cnh'])));
    $arrayClt['cnh_1_habilitacao'] = implode('-', array_reverse(explode('/', $arrayClt['cnh_1_habilitacao'])));
    $arrayClt['cnh_validade'] = implode('-', array_reverse(explode('/', $arrayClt['cnh_validade'])));
    $arrayClt['data_ctps'] = implode('-', array_reverse(explode('/', $arrayClt['data_ctps'])));
    $arrayClt['dada_pis'] = implode('-', array_reverse(explode('/', $arrayClt['dada_pis'])));
    $arrayClt['data_entrada'] = implode('-', array_reverse(explode('/', $arrayClt['data_entrada'])));
    $arrayClt['data_exame'] = implode('-', array_reverse(explode('/', $arrayClt['data_exame'])));
    $arrayClt['data_opcao_fgts'] = implode('-', array_reverse(explode('/', $arrayClt['data_opcao_fgts'])));
    $arrayClt['dt_ini_vinc_suc'] = implode('-', array_reverse(explode('/', $arrayClt['dt_ini_vinc_suc'])));
    $arrayClt['estat_dt_nomeacao'] = implode('-', array_reverse(explode('/', $arrayClt['estat_dt_nomeacao'])));
    $arrayClt['estat_dt_posse'] = implode('-', array_reverse(explode('/', $arrayClt['estat_dt_posse'])));
    $arrayClt['estat_dt_exercicio'] = implode('-', array_reverse(explode('/', $arrayClt['estat_dt_exercicio'])));
    $arrayClt['emissao_crt'] = implode('-', array_reverse(explode('/', $arrayClt['emissao_crt'])));
    $arrayClt['validade_crt'] = implode('-', array_reverse(explode('/', $arrayClt['validade_crt'])));
    $arrayClt['prazo_contrato'] = implode('-', array_reverse(explode('/', $arrayClt['prazo_contrato'])));

    $arrayClt['gratificacao'] = str_replace(',', '.', str_replace('.', '', $arrayClt['gratificacao']));
    $arrayClt['gratificacao_complexidade'] = str_replace(',', '.', str_replace('.', '', $arrayClt['gratificacao_complexidade']));
    $arrayClt['produtividade_percentual_fixo'] = str_replace(',', '.', str_replace('.', '', $arrayClt['produtividade_percentual_fixo']));
    $arrayClt['ad_transferencia_valor'] = str_replace(',', '.', str_replace('.', '', $arrayClt['ad_transferencia_valor']));
    $arrayClt['vt_valor_diario'] = str_replace(',', '.', str_replace('.', '', $arrayClt['vt_valor_diario']));

    $arrayClt['cnpj_empreg_ant'] = str_replace(['.', '/', '-'], '', $arrayClt['cnpj_empreg_ant']);
    $arrayClt['cpf_trab_substituido'] = str_replace(['.', '/', '-'], '', $arrayClt['cpf_trab_substituido']);

    $arrayClt['contrato_medico'] = ($arrayClt['contrato_medico']) ? $arrayClt['contrato_medico'] : 0;
    $arrayClt['desconto_inss'] = ($arrayClt['desconto_inss']) ? $arrayClt['desconto_inss'] : 0;
    $arrayClt['trabAposent'] = ($arrayClt['trabAposent']) ? $arrayClt['trabAposent'] : 0;
    $arrayClt['insalubridade'] = ($arrayClt['insalubridade']) ? $arrayClt['insalubridade'] : 0;
    $arrayClt['seguro_desemprego'] = ($arrayClt['seguro_desemprego']) ? $arrayClt['seguro_desemprego'] : 0;
    $arrayClt['transporte'] = ($arrayClt['transporte']) ? $arrayClt['transporte'] : 0;
    $arrayClt['documentos_entregues'] = ($arrayClt['documentos_entregues']) ? $arrayClt['documentos_entregues'] : 0;
    $arrayClt['verifica_orgao'] = ($arrayClt['verifica_orgao']) ? $arrayClt['verifica_orgao'] : 0;
    $arrayClt['deficiencia_intelectual'] = ($arrayClt['deficiencia_intelectual']) ? $arrayClt['deficiencia_intelectual'] : 0;
    $arrayClt['deficiencia_fisica'] = ($arrayClt['deficiencia_fisica']) ? $arrayClt['deficiencia_fisica'] : 0;
    $arrayClt['deficiencia_auditiva'] = ($arrayClt['deficiencia_auditiva']) ? $arrayClt['deficiencia_auditiva'] : 0;
    $arrayClt['deficiencia_mental'] = ($arrayClt['deficiencia_mental']) ? $arrayClt['deficiencia_mental'] : 0;
    $arrayClt['deficiencia_visual'] = ($arrayClt['deficiencia_visual']) ? $arrayClt['deficiencia_visual'] : 0;
    $arrayClt['preenche_cota'] = ($arrayClt['preenche_cota']) ? $arrayClt['preenche_cota'] : 0;
    $arrayClt['reabReadap'] = ($arrayClt['reabReadap']) ? $arrayClt['reabReadap'] : 0;
    $arrayClt['deficiencia'] = ($arrayClt['deficiencia']) ? $arrayClt['deficiencia'] : 0;
    $arrayClt['filhos_br'] = ($arrayClt['filhos_br']) ? $arrayClt['filhos_br'] : 0;
    $arrayClt['casado_brasileiro'] = ($arrayClt['casado_brasileiro']) ? $arrayClt['casado_brasileiro'] : 0;
    $arrayClt['isento_sindical_confederativa'] = ($arrayClt['isento_sindical_confederativa']) ? $arrayClt['isento_sindical_confederativa'] : 0;
    $arrayClt['isento_sindical_assistencial'] = ($arrayClt['isento_sindical_assistencial']) ? $arrayClt['isento_sindical_assistencial'] : 0;
    $arrayClt['isento_sindical_associativa'] = ($arrayClt['isento_sindical_associativa']) ? $arrayClt['isento_sindical_associativa'] : 0;
    $arrayClt['tipo_valor_hora'] = ($arrayClt['tipo_valor_hora']) ? $arrayClt['tipo_valor_hora'] : 0;
    $arrayClt['ad_unidocencia'] = ($arrayClt['ad_unidocencia']) ? $arrayClt['ad_unidocencia'] : 0;
    $arrayClt['trab_temporario'] = ($arrayClt['trab_temporario']) ? $arrayClt['trab_temporario'] : 0;
    $arrayClt['jovem_aprendiz'] = ($arrayClt['jovem_aprendiz']) ? $arrayClt['jovem_aprendiz'] : 0;
    $arrayClt['estatutario'] = ($arrayClt['estatutario']) ? $arrayClt['estatutario'] : 0;
    $arrayClt['suc_vinc_trab'] = ($arrayClt['suc_vinc_trab']) ? $arrayClt['suc_vinc_trab'] : 0;
//    $arrayClt['trabAposent'] = ($arrayClt['desconto_inss']) ? $arrayClt['desconto_inss'] : 0;

    $arrayClt['valor_hora'] = str_replace(',', '.', str_replace('.', '', $arrayClt['valor_hora']));
    $arrayClt['quantidade_horas_proporcional'] = str_replace(',', '.', str_replace('.', '', $arrayClt['quantidade_horas_proporcional']));

    if (!$arrayClt['tipo_quantidade_horas']) {
        $arrayClt['quantidade_horas'] = $arrayClt['quantidade_horas'] . ":00"; //o valor chega com HORA:MIN adicionando :SEG:

        $arrayClt['quantidade_horas_proporcional'] = $calc->getHorasParaCalculo($arrayClt['quantidade_horas']);
    } else {
        $arrayClt['quantidade_horas'] = $calc->HorasPropParaNormal($arrayClt['quantidade_horas_proporcional']);
    }

//    print_array($arrayClt);
//    exit();
    if (!$arrayClt['desconto_inss']) {
        $arrayClt['tipo_desconto_inss'] = '';
        $arrayClt['trabalha_outra_empresa'] = 0;
    }

    if (!$arrayClt['deficiencia']) {
        $arrayClt['deficiencia_intelectual'] = 0;
        $arrayClt['deficiencia_fisica'] = 0;
        $arrayClt['deficiencia_auditiva'] = 0;
        $arrayClt['deficiencia_mental'] = 0;
        $arrayClt['deficiencia_visual'] = 0;
    }


    $sqlEstadoCivil = mysql_query("SELECT id_estado_civil, nome_estado_civil FROM estado_civil WHERE id_estado_civil = '{$arrayClt['id_estado_civil']}' LIMIT 1;");
    $rowEstadoCivil = mysql_fetch_assoc($sqlEstadoCivil);
    $arrayClt['civil'] = $rowEstadoCivil['nome_estado_civil'];

    foreach ($arrayClt as $key => $value) {
        $arrayClt[$key] = removeAspas($value);
    }

    //INSERT
    if (isset($_REQUEST['salvar'])) {
        $keysClt = implode(', ', array_keys($arrayClt));
        $valuesClt = implode("' , '", $arrayClt);
        $insertClt = "INSERT INTO rh_clt ($keysClt) VALUES ('$valuesClt');";
        mysql_query($insertClt) or die("<pre>ERRO CADASTRO DE CLT: <br>" . $insert . "<br>" . mysql_error() . '</pre>');
        $id_clt = mysql_insert_id();
        $log->log('2', "Cadastro do CLT: ID{$id_clt} - $nome", 'rh_clt');
    } else if (isset($_REQUEST['editar'])) {

        $id_clt = $_REQUEST['id_clt'];
        $data_hoje = date('Y-m-d');

        //UPDATE
        $camposUpdateClt = null;
        foreach ($arrayClt as $key => $value) {
            $camposUpdateClt[] = "$key = '" . removeAspas($value) . "'";
        }
        $updateClt = "UPDATE rh_clt SET " . implode(", ", ($camposUpdateClt)) . " WHERE id_clt = {$id_clt} LIMIT 1";
//        print_array($updateClt);
//        echo $updateClt; die;
        $antigo = $log->getLinha('rh_clt', $id_clt);
        mysql_query($updateClt) or die("ERRO ALTERAÇÃO DE CLT: " . mysql_error());
        $novo = $log->getLinha('rh_clt', $id_clt);
        $log->log('2', "Edição do CLT: ID{$id_clt} - $nome", 'rh_clt', $antigo, $novo);
    }
    
//    exit();
    $novoDep = null;

    $sql_remove_dependentes = "DELETE FROM dependentes WHERE id_clt = $id_clt;";
    $query_remove_dependentes = mysql_query($sql_remove_dependentes);

    $sql_remove_dependente = "DELETE FROM dependente WHERE id_clt = $id_clt;";
    $query_remove_dependente = mysql_query($sql_remove_dependente);

    $sql_remove_favorecido = "DELETE FROM favorecido_pensao_assoc WHERE id_clt = $id_clt;";
    $query_remove_favorecido = mysql_query($sql_remove_favorecido);



    if ($_REQUEST['dependente']) {

        $d = $_REQUEST['dependente'];
//        print_array($d);
        unset($d['id_dependentes']);
        //$arrayClt['incapaz_trab_pai'], $arrayClt['incapaz_trab_mae'], $arrayClt['incapaz_trab_conjuge']
        $d['nome'] = $_REQUEST['nome'];
        $d['contratacao'] = 2;
        $d['id_regiao'] = $_REQUEST['id_regiao'];
        $d['id_projeto'] = $_REQUEST['id_projeto'];
        $d['id_clt'] = $id_clt;
        $d['id_bolsista'] = $id_clt;
//        $d['ddir_pai'] = $_REQUEST['ddir_pai'];
//        $d['ddir_mae'] = $_REQUEST['ddir_mae'];
//        $d['ddir_conjuge'] = $_REQUEST['ddir_conjuge'];
//        $d['incapaz_trab_pai'] = $_REQUEST['incapaz_trab_pai'];
//        $d['incapaz_trab_mae'] = $_REQUEST['incapaz_trab_mae'];
//        $d['incapaz_trab_conjuge'] = $_REQUEST['incapaz_trab_conjuge'];

        for ($i = 0; $i <= 5; $i++) {
            $i2 = $i + 1;
            $d['nome' . $i2] = removeAspas($d[$i]['nome']);
            $d['cpf' . $i2] = $d[$i]['cpf'];
            $d['nao_ir_filho' . $i2] = $d[$i]['nao_ir_filho'];
            $d['data' . $i2] = implode('-', array_reverse(explode('/', $d[$i]['data_nasc'])));
            $d['portador_def' . $i2] = $d[$i]['deficiencia'];
            $d["dep{$i2}_cur_fac_ou_tec"] = $d[$i]['fac_tec'];
            $d["salario_familia{$i2}"] = $d[$i]['salario_familia'];
            $d["dep_plano_saude{$i2}"] = $d[$i]['dep_plano_saude'];
            $d["incapaz_trab_filho{$i2}"] = $d[$i]['incapaz_trab'];
            $d["possui_guarda{$i2}"] = $d[$i]['possui_guarda'];

            if ($d[$i]['favorecidos_pensao']['base_pensao'] == 1) {
                //Sobre Salário Líquido

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 1;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['valorfixo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['quantSalMinimo'] = 0;
            } else if ($d[$i]['favorecidos_pensao']['base_pensao'] == 2) {
                //Sobre Salário Bruto

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 1;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['valorfixo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['quantSalMinimo'] = 0;
            } else if ($d[$i]['favorecidos_pensao']['base_pensao'] == 3) {
                //Sobre Quantidade Salário Minimo

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 1;
                $d[$i]['favorecidos_pensao']['valorfixo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 0;
            } else if ($d[$i]['favorecidos_pensao']['base_pensao'] == 4) {
                //Sobre Valor Fixo

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['quantSalMinimo'] = 0;
            } else if ($d[$i]['favorecidos_pensao']['base_pensao'] == 5) {
                //Sobre 1/3 Salário Líquido

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['valorfixo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 1;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['quantSalMinimo'] = 0;
            } else if ($d[$i]['favorecidos_pensao']['base_pensao'] == 6) {
                //Sobre % Salário Mínimo

                $d[$i]['favorecidos_pensao']['sobreSalLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalBruto'] = 0;
                $d[$i]['favorecidos_pensao']['sobreSalMinimo'] = 0;
                $d[$i]['favorecidos_pensao']['valorfixo'] = 0;
                $d[$i]['favorecidos_pensao']['umTercoSobreLiquido'] = 0;
                $d[$i]['favorecidos_pensao']['sobrePorcentagemSalMinimo'] = 1;
                $d[$i]['favorecidos_pensao']['quantSalMinimo'] = 0;
            }

            $favorecidos_pensao[$i] = $d[$i]['favorecidos_pensao'];
            $favorecidos_pensao[$i]['id_clt'] = $id_clt;
            $favorecidos_pensao[$i]['aliquota'] = str_replace(',', '.', $favorecidos_pensao[$i]['aliquota']);
            $favorecidos_pensao[$i]['valorfixo'] = str_replace(',', '.', str_replace('.', '', $favorecidos_pensao[$i]['valorfixo']));
            $favorecidos_pensao[$i]['nome_dependente'] = removeAspas($d[$i]['nome']);
            $favorecidos_pensao[$i]['cpf_dependente'] = $d[$i]['cpf'];
            $favorecidos_pensao[$i]['data_nasc_dependente'] = implode('-', array_reverse(explode('/', $d[$i]['data_nasc'])));
            $favorecidos_pensao[$i]['incide_ferias'] = ($favorecidos_pensao[$i]['incide_ferias']) ? $favorecidos_pensao[$i]['incide_ferias'] : 0;
            $favorecidos_pensao[$i]['incide_rescisao'] = ($favorecidos_pensao[$i]['incide_rescisao']) ? $favorecidos_pensao[$i]['incide_rescisao'] : 0;
            $favorecidos_pensao[$i]['incide_13'] = ($favorecidos_pensao[$i]['incide_13']) ? $favorecidos_pensao[$i]['incide_13'] : 0;
            $favorecidos_pensao[$i]['pensao'] = ($d[$i]['pensao_alimenticia']) ? $d[$i]['pensao_alimenticia'] : 0;

            $arrayNovoDep[] = [
                'id_dependente' => $d[$i]['id_dependente'],
                'id_projeto' => $_REQUEST['id_projeto'],
                'nome' => removeAspas($d[$i]['nome']),
                'cpf' => $d[$i]['cpf'],
                'data_nasc' => implode('-', array_reverse(explode('/', $d[$i]['data_nasc']))),
                'nao_ir_filho' => $d[$i]['nao_ir_filho'],
                'deficiencia' => $d[$i]['deficiencia'],
                'fac_tec' => $d[$i]['fac_tec'],
                'parentesco' => 3,
                'id_clt' => $id_clt,
                'salario_familia' => $d[$i]['salario_familia'],
                'dep_plano_saude' => $d[$i]['dep_plano_saude'],
                'incapaz_trab' => $d[$i]['incapaz_trab'],
                'possui_guarda' => $d[$i]['possui_guarda'],
            ];
            unset($d[$i]);
        }

        foreach ($_REQUEST['outro_dependente'] as $key => $value) {
            $value['id_clt'] = $id_clt;
            $value['id_projeto'] = $_REQUEST['id_projeto'];
            $value['data_nasc'] = implode('-', array_reverse(explode('/', $value['data_nasc'])));
            $value['salario_familia'] = ($value['salario_familia']) ? $value['salario_familia'] : 0;
            $value['dep_plano_saude'] = ($value['dep_plano_saude']) ? $value['dep_plano_saude'] : 0;
            $value['incapaz_trab'] = ($value['incapaz_trab']) ? $value['incapaz_trab'] : 0;
            $value['nao_ir_filho'] = ($value['nao_ir_filho']) ? $value['nao_ir_filho'] : 0;
            $arrayNovoDep[] = $value;
        }
        
//        print_array([$d, $favorecidos_pensao]);
//        exit();
        
        /**
         * GRAVANDO NA TABELA DE DEPENDENTES ANTIGA
         */
        $keysDep = implode(', ', array_keys($d));
        $valuesDep = implode("' , '", $d);
//        if (!empty($_REQUEST['id_dependentes'])) {
//            //UPDATE
//            $camposUpdateDep = null;
//            foreach ($d as $key => $value) {
//                $camposUpdateDep[] = "$key = '$value'";
//            }
//            $updateDependente = "UPDATE dependentes SET " . implode(", ", ($camposUpdateDep)) . " WHERE id_dependentes = '{$_REQUEST['id_dependentes']}' LIMIT 1;";
////            print_array($updateDependente);
//            $updateDependente = mysql_query($updateDependente);
//        } else {
            //INSERT
            $insertDependente = "INSERT INTO dependentes ($keysDep) VALUES ('$valuesDep');";
//            print_array($insertDependente);
            $insertDependente = mysql_query($insertDependente);
//        }
        
        /**
         * GRAVANDO NA TABELA DE DEPENDENTES NOVA
         */
        foreach ($arrayNovoDep as $key => $value) {
            if ($value['nome'] && $value['cpf']) {
//                if ($value['id_dependente']) {
//                    $id_dependente = $value['id_dependente'];
//                    unset($value['id']);
//                    //UPDATE
//                    $camposUpdateNovoDep = null;
//                    foreach ($value as $k => $v) {
//                        $camposUpdateNovoDep[] = "$k = '$v'";
//                    }
//                    $updateNovoDependente = "UPDATE dependente SET " . implode(", ", ($camposUpdateNovoDep)) . " WHERE id_dependente = {$id_dependente} LIMIT 1;";
////                    print_array($updateNovoDependente);
//                    $updateNovoDependente = mysql_query($updateNovoDependente) or die("ERRO ALTERAÇÃO DE DEPENDENTE: " . mysql_error());
//                } else {
                    //INSERT
                    $keysNovoDep = implode(', ', array_keys($value));
                    $valuesNovoDep = implode("' , '", $value);
                    $insertNovoDependente = "INSERT INTO dependente ($keysNovoDep) VALUES ('$valuesNovoDep');";
//                    print_array($insertNovoDependente);
                    $insertNovoDependente = mysql_query($insertNovoDependente) or die("ERRO INSERSÃO DE DEPENDENTE: " . mysql_error());
//                }
            }
        }
        
        /**
         * FAVORECIDOS DA PENSAO ALIMENTICIA
         */
//        print_array($favorecidos_pensao);
        foreach ($favorecidos_pensao as $key => $value) {
            if ($value['cpf']) {
//                if ($value['pensao']) {
                    unset($value['pensao']);

//                    if ($value['id']) {
//                        $id_favorecido = $value['id'];
//                        unset($value['id']);
//                        //UPDATE
//                        $camposUpdateFav = null;
//                        foreach ($value as $k => $v) {
//                            $camposUpdateFav[] = "$k = '$v'";
//                        }
//                        $updateFavorecido = "UPDATE favorecido_pensao_assoc SET " . implode(", ", ($camposUpdateFav)) . " WHERE id = {$id_favorecido} LIMIT 1;";
////                        print_array($updateFavorecido);
//                        $updateFavorecido = mysql_query($updateFavorecido) or die("ERRO ALTERAÇÃO DE FAVORECIDO: " . mysql_error());
//                    } else {
                        //INSERT
                        $keysFav = implode(', ', array_keys($value));
                        $valuesFav = implode("' , '", $value);
                        $insertFavorecido = "INSERT INTO favorecido_pensao_assoc ($keysFav) VALUES ('$valuesFav');";
//                        print_array($insertFavorecido);
                        $insertFavorecido = mysql_query($insertFavorecido) or die("ERRO INSERSÃO DE FAVORECIDO: " . mysql_error());
//                    }
//                } else {
//                    $updateFavorecido = "UPDATE favorecido_pensao_assoc SET status_reg = 0 WHERE id = {$value['id']} LIMIT 1;";
////                    print_array($updateFavorecido);
//                    $updateFavorecido = mysql_query($updateFavorecido) or die("ERRO ALTERAÇÃO DE FAVORECIDO: " . mysql_error());
//                }
            }
        }
    }
    
//    exit();
    
    /**
     * DESCONTO OUTRA EMPRESA
     */
    if ($arrayClt['desconto_inss'] && $arrayClt['trabalha_outra_empresa']) {
        foreach ($_REQUEST['outra_empresa'] as $key => $value) {
//            print_array($value);
            $objInssOutrasEmpresas->setDefault();
            if ($value['id_inss']) {
                $objInssOutrasEmpresas->setIdInss($value['id_inss']);
                $objInssOutrasEmpresas->getByIdInss();
                $objInssOutrasEmpresas->getRow();
            }

            $objInssOutrasEmpresas->setSalario(str_replace(',', '.', str_replace('.', '', $value['salario'])));
            $objInssOutrasEmpresas->setDesconto(str_replace(',', '.', str_replace('.', '', $value['desconto'])));
            $objInssOutrasEmpresas->setInicio(implode('-', array_reverse(explode('/', $value['inicio']))));
            $objInssOutrasEmpresas->setFim(implode('-', array_reverse(explode('/', $value['fim']))));
            $objInssOutrasEmpresas->setCnpjOutroVinculo($value['cnpj_outro_vinculo']);

            if ($value['id_inss']) {
                $objInssOutrasEmpresas->update();
            } else {
                $objInssOutrasEmpresas->setIdClt($id_clt);
                $objInssOutrasEmpresas->setStatus(1);
                $objInssOutrasEmpresas->setDataCad(date('Y-m-d'));
                $objInssOutrasEmpresas->insert();
            }
        }
    } else {
        $objInssOutrasEmpresas->setDefault();
        $objInssOutrasEmpresas->setIdClt($id_clt);
        $objInssOutrasEmpresas->inativaByIdClt();
    }
//    exit();
    /**
     * VALE TRANSPORTE
     */
    if ($arrayClt['transporte'] == 1) {
        $vt = $_REQUEST['vt'];
        $vt['id_clt'] = $id_clt;
        $vt['id_projeto'] = $_REQUEST['id_projeto'];
        $vt['id_regiao'] = $_REQUEST['id_regiao'];

        $keysVale = implode(', ', array_keys($vt));
        $valuesVale = implode("' , '", $vt);
        if (!empty($vt['id_vale'])) {
            $id_vale = $vt['id_vale'];
            unset($vt['id_vale']);
            //UPDATE
            $camposUpdateVale = null;
            foreach ($vt as $key => $value) {
                $camposUpdateVale[] = "$key = '$value'";
            }
            $updateVale = "UPDATE rh_vale SET " . implode(", ", ($camposUpdateVale)) . " WHERE id_vale = '{$id_vale}' LIMIT 1;";
//            print_array($updateVale);
            $updateVale = mysql_query($updateVale) or die(mysql_error());
        } else {
            //INSERT
            $insertVale = "INSERT INTO rh_vale ($keysVale) VALUES ('$valuesVale');";
//            print_array($insertVale);
            $insertVale = mysql_query($insertVale) or die(mysql_error());
        }
    } else {
        $updateVale = "UPDATE rh_vale SET status_reg = 0 WHERE id_clt = '{$id_clt}';";
    }
    
    header("Location: /intranet/rh_novaintra/alter_clt_teste.php?regiao=$id_regiao&projeto=$id_projeto&clt=$id_clt");
} else {
//    print_array($rowClt);
}

//if (isset($_REQUEST['editar'])) {

//}
?>