<?php

include('../conn.php');

$idFunc = $_REQUEST['delete_id'];
$idRemov = $_COOKIE['logado'];
$idRegiao = $_REQUEST['idRegiao'];
$idProjeto = $_REQUEST['idProjeto'];
$tipoContratacao = $_REQUEST['tipoContratacao'];

if($tipoContratacao == 2) {
    //Query para verificar se o funcionário está em uma folha de pagamento
    $verifFolha = mysql_query("SELECT COUNT(id_clt) AS total FROM rh_folha_proc WHERE id_clt = '$idFunc' AND status = 3");
    $row = mysql_fetch_array($verifFolha);
    $total = $row['total'];

    //Verifica se o funcionário não está em uma folha de pagamento
    if ($total == 0) {
    //Query para inserir os dados do CLT na tabela de removidos
    $insertSelect = mysql_query("
        INSERT INTO rh_clt_removidos (
                id_clt,
                id_antigo,
                id_projeto,
                id_regiao,
                atividade,
                salario,
                localpagamento,
                locacao,
                unidade,
                nome,
                sexo,
                endereco,
                numero,
                complemento,
                bairro,
                cidade,
                uf,
                cep,
                tel_fixo,
                tel_cel,
                tel_rec,
                data_nasci,
                naturalidade,
                nacionalidade,
                civil,
                rg,
                orgao,
                data_rg,
                cpf,
                conselho,
                titulo,
                zona,
                secao,
                pai,
                nacionalidade_pai,
                mae,
                nacionalidade_mae,
                estuda,
                data_escola,
                escolaridade,
                instituicao,
                curso,
                tipo_contratacao,
                tvsorrindo,
                banco,
                agencia,
                conta,
                tipo_conta,
                id_curso,
                id_psicologia,
                psicologia,
                obs,
                apolice,
                status,
                data_entrada,
                data_saida,
                campo1,
                campo2,
                campo3,
                data_exame,
                data_exame2,
                reservista,
                etnia,
                deficiencia,
                cabelos,
                altura,
                olhos,
                peso,
                defeito,
                cipa,
                ad_noturno,
                plano,
                assinatura,
                distrato,
                outros,
                pis,
                dada_pis,
                data_ctps,
                serie_ctps,
                uf_ctps,
                uf_rg,
                fgts,
                insalubridade,
                transporte,
                adicional,
                terceiro,
                num_par,
                data_ini,
                medica,
                tipo_pagamento,
                nome_banco,
                num_filhos,
                nome_filhos,
                observacao,
                impressos,
                campo4,
                sis_user,
                data_cad,
                foto,
                dataalter,
                useralter,
                vale,
                documento,
                rh_vale,
                rh_vinculo,
                rh_status,
                rh_horario,
                rh_sindicato,
                rh_cbo,
                recolhimento_ir,
                desconto_inss,
                tipo_desconto_inss,
                valor_desconto_inss,
                trabalha_outra_empresa,
                salario_outra_empresa,
                desconto_outra_empresa,
                vr,
                valor_vr,
                data_aviso,
                data_demi,
                status_admi,
                status_demi,
                status_reg,
                matricula,
                n_processo,
                contrato_medico,
                email,
                data_nasc_pai,
                data_nasc_mae,
                data_nasc_conjuge,
                nome_conjuge,
                nome_avo_h,
                data_nasc_avo_h,
                nome_avo_m,
                data_nasc_avo_m,
                nome_bisavo_h,
                data_nasc_bisavo_h,
                nome_bisavo_m,
                data_nasc_bisavo_m,
                municipio_nasc,
                uf_nasc,
                data_emissao,
                verifica_orgao,
                tipo_sanguineo,
                id_funcionario_remocao,
                data_remocao,
                status_remocao
        )
        SELECT 
                id_clt,
                id_antigo,
                id_projeto,
                id_regiao,
                atividade,
                salario,
                localpagamento,
                locacao,
                unidade,
                nome,
                sexo,
                endereco,
                numero,
                complemento,
                bairro,
                cidade,
                uf,
                cep,
                tel_fixo,
                tel_cel,
                tel_rec,
                data_nasci,
                naturalidade,
                nacionalidade,
                civil,
                rg,
                orgao,
                data_rg,
                cpf,
                conselho,
                titulo,
                zona,
                secao,
                pai,
                nacionalidade_pai,
                mae,
                nacionalidade_mae,
                estuda,
                data_escola,
                escolaridade,
                instituicao,
                curso,
                tipo_contratacao,
                tvsorrindo,
                banco,
                agencia,
                conta,
                tipo_conta,
                id_curso,
                id_psicologia,
                psicologia,
                obs,
                apolice,
                status,
                data_entrada,
                data_saida,
                campo1,
                campo2,
                campo3,
                data_exame,
                data_exame2,
                reservista,
                etnia,
                deficiencia,
                cabelos,
                altura,
                olhos,
                peso,
                defeito,
                cipa,
                ad_noturno,
                plano,
                assinatura,
                distrato,
                outros,
                pis,
                dada_pis,
                data_ctps,
                serie_ctps,
                uf_ctps,
                uf_rg,
                fgts,
                insalubridade,
                transporte,
                adicional,
                terceiro,
                num_par,
                data_ini,
                medica,
                tipo_pagamento,
                nome_banco,
                num_filhos,
                nome_filhos,
                observacao,
                impressos,
                campo4,
                sis_user,
                data_cad,
                foto,
                dataalter,
                useralter,
                vale,
                documento,
                rh_vale,
                rh_vinculo,
                rh_status,
                rh_horario,
                rh_sindicato,
                rh_cbo,
                recolhimento_ir,
                desconto_inss,
                tipo_desconto_inss,
                valor_desconto_inss,
                trabalha_outra_empresa,
                salario_outra_empresa,
                desconto_outra_empresa,
                vr,
                valor_vr,
                data_aviso,
                data_demi,
                status_admi,
                status_demi,
                status_reg,
                matricula,
                n_processo,
                contrato_medico,
                email,
                data_nasc_pai,
                data_nasc_mae,
                data_nasc_conjuge,
                nome_conjuge,
                nome_avo_h,
                data_nasc_avo_h,
                nome_avo_m,
                data_nasc_avo_m,
                nome_bisavo_h,
                data_nasc_bisavo_h,
                nome_bisavo_m,
                data_nasc_bisavo_m,
                municipio_nasc,
                uf_nasc,
                data_emissao,
                verifica_orgao,
                tipo_sanguineo,
                '$idRemov',
                now(),
                '0' 
                FROM rh_clt WHERE id_clt = {$idFunc} AND id_regiao = {$idRegiao} AND id_projeto = {$idProjeto} LIMIT 1; ");

                //PERGIGO!!!!!!! 
                //Query para remover o funcionario CLT 
                //PERIGO!!!!!!!!
                $sql_articles = mysql_query( "DELETE FROM rh_clt WHERE id_clt = {$idFunc} AND id_regiao = {$idRegiao} AND id_projeto = {$idProjeto} ");
    }
} else if ($tipoContratacao == 3) {
    //Query para verificar se o funcionário está em uma folha de pagamento
    $verifFolha = mysql_query("SELECT COUNT(id_autonomo) AS total FROM folha_cooperado WHERE id_autonomo = '$idFunc' AND status = 3");
    $row = mysql_fetch_array($verifFolha);
    $total = $row['total'];

    //Verifica se o funcionário não está em uma folha de pagamento
    if ($total == 0) {
        //Query para inserir os dados do COOPERADO na tabela de removidos
        $insertSelect = mysql_query("
            INSERT INTO autonomo_removido 
            SELECT *, '$idRemov', now(), '0' 
                FROM autonomo 
                WHERE id_autonomo = {$idFunc} 
                AND id_regiao = '$idRegiao' AND id_projeto = '$idProjeto'
            ;");
        //PERGIGO!!!!!!! 
        //Query para remover o funcionario COOPERADO 
        //PERIGO!!!!!!!!
        $sql_articles = mysql_query( "
            DELETE FROM autonomo 
                WHERE id_autonomo = '{$idFunc}' AND id_regiao = '{$idRegiao}' 
                AND id_projeto = '{$idProjeto}'  AND tipo_contratacao = '{$tipoContratacao}'
            ");
    }
} else {
    //Query para inserir os dados do AUTONOMO na tabela de removidos
    $insertSelect = mysql_query("
        INSERT INTO autonomo_removido 
        SELECT *, '$idRemov', now(), '0' 
            FROM autonomo 
            WHERE id_autonomo = {$idFunc} 
            AND id_regiao = '$idRegiao' AND id_projeto = '$idProjeto'
        ;");
    //PERGIGO!!!!!!! 
    //Query para remover o funcionario AUTONOMO
    //PERIGO!!!!!!!!
    $sql_articles = mysql_query( "
        DELETE FROM autonomo 
            WHERE id_autonomo = '{$idFunc}' AND id_regiao = '{$idRegiao}' 
            AND id_projeto = '{$idProjeto}'  AND tipo_contratacao = '{$tipoContratacao}'
        ");
}

if($insertSelect) {
    echo "YES";
}
else {
    echo "NO";
}
?>