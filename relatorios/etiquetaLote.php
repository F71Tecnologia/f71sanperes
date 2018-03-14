<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
if (empty($_POST['check_list'])) {
    header("Location: /intranet/relatorios/etiquetaList.php");
    exit;
}

include ('../conn.php');
include('../wfunction.php');
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
$id_reg = $_REQUEST['id_reg'];
?>
<HTML>
    <TITLE>:: Intranet :: Etiqueta em Lote</TITLE>
    <HEAD>
        <STYLE TYPE="text/css">
            .Principal {
                width: 593px;
                padding: 10px;
            }
            .etiqueta1 {
                width: 7.5cm;
                height: 5.5cm;
                border: 1px solid #000;
                font-family:monospace;
                font-size: 7pt;
                padding: 5px;
                text-align: justify;
                float: left;
            }
            .divAssinatura {
                float: right;
            }
            .etiqueta2Altura {
                height: 6.5cm;
            }
            .etiqueta2 {
                width: 7cm;
                height: 5.5cm;
                border:1px solid #000;
                font-size: 10px;
                padding: 5px;
                text-align: justify;
                float: left;
                margin-left: 20px;
            }
            .divDados {
                padding: 8px;
            }
        </STYLE>
    </HEAD>
    <BODY>
        <div class="Principal">
            <?php
            $clts = implode(',',$_POST['check_list']);
            $row_clt = montaQuery(
                        "rh_clt AS CLT
                        LEFT JOIN rh_transferencias AS T ON T.id_transferencia = 
                            (SELECT id_transferencia 
                            FROM rh_transferencias AS T2
                            WHERE T2.id_clt = CLT.id_clt
                            ORDER BY T2.data_proc,T2.criado_em DESC
                            LIMIT 1)
                        LEFT JOIN curso AS C ON C.id_curso = IF(T.id_curso_para,T.id_curso_para,CLT.id_curso)
                        LEFT JOIN regioes AS R ON R.id_regiao = 
                        (SELECT id_regiao
                            FROM regioes AS R2
                            WHERE R2.id_regiao = IF('{$id_regiao}' != '','{$id_regiao}',CLT.id_regiao) AND status = '1'
                            ORDER BY R2.id_regiao DESC
                            LIMIT 1)
                        LEFT JOIN master AS M ON M.id_master = R.id_master
                        LEFT JOIN rhempresa AS E ON E.id_projeto = IF(T.id_projeto_para,T.id_projeto_para,CLT.id_projeto)
                        LEFT JOIN rh_horarios AS H ON (CLT.rh_horario = H.id_horario)
                        LEFT JOIN unidade AS U ON U.id_unidade = IF(T.id_unidade_para,T.id_unidade_para,CLT.id_unidade)
                        LEFT JOIN rh_cbo AS CBO ON C.cbo_codigo = CBO.id_cbo
                        LEFT JOIN cnae AS CNAE ON REPLACE(REPLACE(CNAE.codigo,'/',''),'-','') = IF(M.cnae > 0,M.cnae,E.cnae)
                        LEFT JOIN rh_salario AS S ON S.id_salario = 
                            (SELECT SAL.id_salario
                            FROM rh_salario AS SAL
                            WHERE SAL.id_curso = C.id_curso
                            ORDER BY SAL.data, SAL.id_salario DESC
                            LIMIT 1)", "CLT.*, date_format(CLT.data_entrada, '%d/%m/%Y') as data_entrada, C.nome AS curso_nome, E.nome as e_nome, E.razao as e_razao, E.cnpj as e_cnpj, E.endereco as e_endereco, E.numero as e_numero, E.complemento as e_complemento, E.bairro as e_bairro, E.cidade as e_cidade, E.uf as e_uf, IF(S.salario_novo IS NULL or S.salario_novo = '', C.salario, S.salario_novo) AS salario, S.salario_antigo, M.razao AS master_razao, M.cnpj AS master_cnpj, M.endereco AS master_endereco, M.bairro AS master_bairro, M.cep AS master_cep, M.municipio AS master_municipio, M.uf AS master_uf, CASE WHEN LENGTH(T.id_regiao_para) = 0 OR T.id_regiao_para IS NULL THEN R.id_regiao ELSE T.id_regiao_para END as id_regiao, CNAE.descricao as cnae_desc, CBO.cod as cbo_cod, C.cbo_nome as cbo_nome, C.horista_plantonista as horista", "CLT.id_clt in ($clts)", 'CLT.nome ASC',null,'',false);
            $cont = 0;
            $numResults = mysql_num_rows($row_clt);
            while($clt= mysql_fetch_assoc($row_clt)){
                
                ?>
                <div class="etiqueta1">
                    <div class="divDados">
                        <strong>
                        <?= $clt['master_razao']; ?><br />
                        CNPJ: <?= $clt['master_cnpj']; ?><br/>
                        END. <?= $clt['master_endereco'].", ".$clt['master_municipio']."/".$clt['master_uf']//.", CEP: ".formataCampo($clt['master_cep'],'#####-###'); ?><br/>
                        CARGO: <?= $clt['curso_nome']; ?><br/>
                        Especificação do Estabelecimento: <?= $clt['cnae_desc']; ?><br/>
                        CBO: <?= $clt['cbo_cod']; ?><br />
                        ADMISSÃO: <?= $clt['data_entrada']; ?><br />
                        FICHA REGISTRO N°: <?= $clt['matricula']; ?><br/>
                        Remuneração esp.: R$ <?= formataMoeda($clt['salario'],1); ?> <?= (empty($clt['horista'])) ? 'por mês' : 'por hora' ?> <br/>
                        (<?= ucwords(valor_extenso($clt['salario'], 2, ',', '')); ?>) * *<br /><br />
                        <?= $clt['master_razao']; ?><br />
                        </strong>
                    </div>
                </div>
                <div class="etiqueta2Altura">
                    <div class="etiqueta2">
                        <div>
                            <p align="center"><B>CONTRATO DE EXPERIÊNCIA</B></p>
                        </div>
                        <div class="divDados">
                            <p>Nome <?= $clt['nome']; ?>, matrícula n° <?= $clt['matricula']; ?>, admitido em <?= $dataNova; ?> por instrumento escrito pelo prazo de 45 (quarenta e cinco) dias, a título de experiência, podendo este ser prorrogado na forma da lei, assim querendo as partes.</p><br/><br/>

                            RJ, ______/______ de _________<br/><br/><br/>
                        </div>
                        <div class="divAssinatura">
                            ______________________________________________<br/>

                        </div>
                    </div>
                </div>
                <?php
                if (++$cont % 4 == 0 AND $cont != $numResults) {
                    echo '<p style="page-break-before: always;">&nbsp;</p>';
                }
            }
            ?>
        </div>
    </BODY>
</HTML>
