<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {

    include "conn.php";
    include './wfunction.php';
    include_once("classes/CalculoFeriasClass.php");

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    $pro = $_REQUEST['pro'];
    $id_reg = $_REQUEST['id_reg'];
    $clt = $_REQUEST['clt'];

    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
    $row_regiao = mysql_fetch_assoc($qr_regiao);

    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
    $row_master = mysql_fetch_assoc($qr_master);

    $result_bol = mysql_query( "SELECT A.id_clt, A.nome, A.endereco, A.numero, A.complemento, A.bairro, A.cidade, A.uf, A.nacionalidade, A.tel_fixo, A.tel_cel, A.tel_rec, A.foto, A.sexo, date_format(A.data_nasci, '%d/%m/%Y') AS data_nasci, A.naturalidade, A.civil,
                                A.cpf, A.rg, A.uf_rg, date_format(A.data_rg, '%d/%m/%Y') AS data_rg, A.orgao, A.pai, A.nacionalidade_pai, A.mae, A.nacionalidade_mae, A.campo1, A.serie_ctps, A.uf_ctps,date_format(A.data_ctps, '%d/%m/%Y') AS data_ctps,
                                A.titulo, A.zona, A.secao, A.reservista, A.escolaridade, date_format(A.data_entrada, '%d/%m/%Y') AS data_entrada, A.rh_sindicato, A.fgts, A.agencia, A.conta, A.pis,date_format(A.dada_pis, '%d/%m/%Y') AS data_pis, 
                                IF(C.id_transferencia IS NULL, A.id_curso, C.id_curso_de) AS id_curso, IF(C.id_transferencia IS NULL, A.rh_horario, C.id_horario_de) AS rh_horario
                                FROM rh_clt AS A
                                LEFT JOIN (
                                        SELECT *
                                        FROM rh_transferencias
                                        WHERE id_clt = $clt
                                        ORDER BY id_transferencia ASC
                                        LIMIT 1) AS C ON (A.id_clt = C.id_clt)
                                WHERE A.id_clt = $clt");
    $row = mysql_fetch_array($result_bol);
    $data_entrada = implode('-', array_reverse(explode('/',$row['data_entrada'])));
    
    $result_reci = mysql_query("SELECT *,date_format(data_demi, '%d/%m/%Y') AS data_demibr FROM rh_recisao WHERE id_clt = $clt  AND status = 1");
    $row_reci = mysql_fetch_array($result_reci);
    $row_demi = array();
    if ($row_reci['data_demi'] == "") {
        $row_demi['data_saida'] = "";
        $row_demi['homologacao'] = "";
        $row_demi['desligamento'] = "";
        $row_demi['local_homolog'] = "";
    } else {
        $row_demi['data_saida'] = $row_reci['data_demibr'];
        $row_demi['homologacao'] = "";
        $qr_status = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row['status']}'");
        $row_status = mysql_fetch_assoc($qr_status);
        $row_demi['desligamento'] = $row_status['especifica'];
        $row_demi['local_homolog'] = "";
    }

//    $result_horario = mysql_query("Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where funcao = '{$row['id_curso']}' AND id_horario = '{$row['rh_horario']}'");
    echo "<!-- Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where id_horario = '{$row['rh_horario']}' -->";
    $result_horario = mysql_query("Select *, date_format(entrada_1, '%H:%i') as entrada_1br, date_format(saida_1, '%H:%i') as saida_1br, date_format(entrada_2, '%H:%i') as entrada_2br, date_format(saida_2, '%H:%i') as saida_2br from rh_horarios where id_horario = '{$row['rh_horario']}'");
    $row_horario = mysql_fetch_array($result_horario);

    /* CONTRIBU���O SINDICAL */
    $qr_cont_sind = mysql_query("SELECT A.id_clt,A.mes,A.ano,A.data_proc,A.a5019,B.rh_sindicato,C.nome FROM rh_folha_proc AS A
                                    LEFT JOIN rh_clt AS B ON (A.id_clt=B.id_clt)
                                    LEFT JOIN rhsindicato AS C ON (B.rh_sindicato=C.id_sindicato)
                                    WHERE A.id_clt = '{$clt}' AND A.a5019 != 0
                                    ORDER BY A.data_proc DESC");


    /* F�RIAS */
    $qr_ferias = mysql_query("SELECT *, date_format(data_aquisitivo_ini, '%d/%m/%Y') as data_aquisitivo_ini, date_format(data_aquisitivo_fim, '%d/%m/%Y') as data_aquisitivo_fim, date_format(data_ini, '%d/%m/%Y') as data_ini, date_format(data_fim, '%d/%m/%Y') as data_fim, date_format(data_proc, '%d/%m/%Y') as data_proc FROM rh_ferias WHERE id_clt = '$clt' AND status = 1");
    $objFerias = new Calculo_Ferias();
    $objFerias->setIdClt($clt);
    $periodos_gozados = $objFerias->getPeriodosGozados2();
    $periodos_disponiveis = $objFerias->getPeriodoAquisitivo($data_entrada, NULL, 1,$periodos_gozados);
    /**
     * ESSES S�O OS PERIODOS AQUISITIVOS QUE FORAM TIRADOS DO FUNCION�RIO POR QUANTIDADE DE DIAS DE 
     * LICEN�A MAIOR QUE 30 DENTRO DO PERIODO.
     */
    $p = $objFerias->getPeriodosNegados();
    
    /**
     * MOTIVOS PELO O QUAL FOI DESCONSIDERADO F�RIAS EM DOBRO PARA O FUNCIONARIO
     */
    $qry_motivos_ferias_dobro = "SELECT A.*, B.nome, DATE_FORMAT(A.criado_em,'%d/%m/%Y %H:%i:%s') AS data_br
                                FROM his_ferias_dobro_canceladas AS A
                                LEFT JOIN funcionario AS B ON(A.criado_por = B.id_funcionario)
                                WHERE A.id_clt = '{$clt}'";
    $sql_motivos_ferias_dobro = mysql_query($qry_motivos_ferias_dobro) or die("Erro ao selecionar motivo de ferias em dobro ter sido desconsiderada");
    $motivos = array();
    while($rows_motivos = mysql_fetch_assoc($sql_motivos_ferias_dobro)){
        $motivos["nome"]            = $rows_motivos['nome'];
        $motivos["criado_em"]       = $rows_motivos['data_br'];
        $motivos["motivo"][]        = $rows_motivos['motivo'];
    }
    
    
    /* AFASTAMENTOS */
    $qr_afasta = mysql_query("SELECT *,DATE_FORMAT(data, '%d/%m/%Y') AS databr, DATE_FORMAT(data_retorno, '%d/%m/%Y') AS data_retornobr FROM rh_eventos
                                WHERE cod_status NOT IN (10,40,60,61,62,81,200,63,101,64,65,66)
                                AND id_clt = {$clt}");

    /* DEPENTES */
    $qr_depen = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1br, date_format(data1, '%d/%m/%Y') AS data1br, date_format(data2, '%d/%m/%Y') AS data2br, date_format(data3, '%d/%m/%Y') AS data3br, date_format(data4, '%d/%m/%Y') AS data4br, date_format(data5, '%d/%m/%Y') AS data5br, date_format(data6, '%d/%m/%Y') AS data6br FROM dependentes 
                                WHERE id_bolsista = {$clt} AND (nome1!='' or nome2!='' or nome3!= '' or nome4 != '' or nome5 != '' or nome6 != '')");
    $row_depen = mysql_fetch_assoc($qr_depen);

    /* EXAMES */
    $qr_exame = mysql_query("SELECT documento,date_format(data, '%d/%m/%Y') AS data FROM rh_doc_status AS A
                                LEFT JOIN rh_documentos AS B ON (B.id_doc=A.tipo)
                                WHERE A.id_clt = {$clt} AND A.tipo IN (1,13) ORDER BY data DESC");

    /* TRANSFERENCIAS DE UNIDADES */
    $qr_transf = mysql_query("SELECT A.id_transferencia, B.nome AS de, C.nome AS para, A.motivo, date_format(A.data_proc, '%d/%m/%Y') AS data FROM rh_transferencias AS A 
                                LEFT JOIN projeto AS B ON (A.id_projeto_de = B.id_projeto)
                                LEFT JOIN projeto AS C ON (A.id_projeto_para = C.id_projeto) 
                                WHERE id_clt = {$clt} AND id_projeto_de <> id_projeto_para");

    /* ALTERA��O DE FUN��ES */
    $qr_alt_funcao = mysql_query("SELECT A.id_transferencia, B.nome AS de, C.nome AS para,D.cod AS cbo_de,E.cod AS cbo_para, A.motivo, date_format(A.data_proc, '%d/%m/%Y') AS data FROM rh_transferencias AS A 
                                    LEFT JOIN curso AS B ON (A.id_curso_de = B.id_curso)
                                    LEFT JOIN curso AS C ON (A.id_curso_para = C.id_curso)
                                    LEFT JOIN rh_cbo AS D ON (B.cbo_codigo = D.id_cbo)
                                    LEFT JOIN rh_cbo AS E ON (A.id_curso_para = E.id_cbo)
                                    WHERE id_clt = {$clt} AND B.nome <> C.nome");
                                    
    /* ALTERA��O DE SAL�RIO */
    $qr_transf_sal = "SELECT id_curso_de, id_curso_para, data_proc FROM rh_transferencias WHERE id_curso_de <> id_curso_para AND id_clt = '$clt' AND status = 1";
    $qr_transf_sal = mysql_query($qr_transf_sal);
    while($row_transf_sal = mysql_fetch_assoc($qr_transf_sal)){$array[] = $row_transf_sal;}
    if (!empty($row_reci['data_demi'])) { $auxDemi = " AND data < '{$row_reci['data_demi']}' "; }
    if(is_array($array)){
        for($i=0; $i<=count($array); $i++){
            if($i==0){
                $qr_alt_salario_or[] = "id_curso = {$array[$i]['id_curso_de']} AND data < '{$array[$i]['data_proc']}' AND data > '$data_entrada' AND status = 1";
            }elseif($i == count($array)-1){
                $qr_alt_salario_or[] = "id_curso = {$array[$i]['id_curso_de']} AND data < '{$array[$i]['data_proc']}' AND data > '{$array[$i-1]['data_proc']}' AND status = 1";
            }elseif($i == count($array)){
                $qr_alt_salario_or[] = "id_curso = {$array[$i-1]['id_curso_para']} AND data > '{$array[$i-1]['data_proc']}' AND status = 1 $auxDemi";
            }else{
                $qr_alt_salario_or[] = "id_curso = {$array[$i]['id_curso_de']} AND data < '{$array[$i]['data_proc']}' AND data > '{$array[$i-1]['data_proc']}' AND status = 1";
            }
        }
        $qr_alt_salario[] = "(SELECT 'A' ordem, salario_antigo, salario_novo, data, motivo FROM rh_salario WHERE ".implode(" OR ",$qr_alt_salario_or)." GROUP BY DATE_FORMAT(data,'%Y-%m'))";
    }else{
        $qr_alt_salario[] = "(SELECT 'A' ordem, salario_antigo, salario_novo, data, motivo FROM rh_salario WHERE  id_curso = {$row['id_curso']} AND data > '$data_entrada' AND status = 1 $auxDemi GROUP BY DATE_FORMAT(data,'%Y-%m'))";
    }
    $qr_alt_salario[] = "(SELECT 'B' ordem, null, sallimpo salario_novo, (SELECT MAX(data_proc) FROM rh_transferencias WHERE id_clt = rh_folha_proc.id_clt AND MONTH(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.mes AND YEAR(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.ano AND id_curso_de <> id_curso_para) data, '' AS motivo FROM rh_folha_proc WHERE id_clt = {$row['id_clt']} AND status = 3 GROUP BY sallimpo ORDER BY data_proc LIMIT 1,100)";
//    $qr_alt_salario[] = "(SELECT 'B' ordem, null, sallimpo salario_novo,  z.data , z.motivo FROM rh_folha_proc LEFT JOIN (SELECT MAX(data_proc) AS data, motivo FROM rh_transferencias WHERE id_curso_de <> id_curso_para ) AS z ON (id_clt = rh_folha_proc.id_clt AND MONTH(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.mes AND YEAR(ADDDATE(data_proc, INTERVAL 1 MONTH)) = rh_folha_proc.ano ) WHERE id_clt = {$row['id_clt']} AND status = 3 GROUP BY sallimpo ORDER BY data_proc LIMIT 1,100)";
    $qr_alt_salario = "SELECT *, date_format(data, '%d/%m/%Y') AS dataBR FROM (SELECT * FROM (".implode(' UNION ', $qr_alt_salario).") A ORDER BY ordem, data DESC) B WHERE data IS NOT NULL GROUP BY salario_novo ORDER BY data";
//    print_r($qr_alt_salario); exit;
    $qr_alt_salario = mysql_query($qr_alt_salario);
    
    $result_bol3 = mysql_query("SELECT *,date_format(inicio, '%d/%m/%Y')as inicio FROM curso where id_curso = '{$row['id_curso']}'");
    $row_bol3 = mysql_fetch_array($result_bol3);

    $result_bol2 = mysql_query("SELECT *,date_format(termino, '%d/%m/%Y')as termino FROM curso where id_curso = '{$row['id_curso']}'");
    $row_bol2 = mysql_fetch_array($result_bol2);

    $result_reg = mysql_query("Select * from  regioes where id_regiao = '{$row['id_regiao']}'");
    $row_reg = mysql_fetch_array($result_reg);

    $result_curso = mysql_query("SELECT A.salario_antigo AS salario, B.nome
                                FROM rh_salario AS A
                                LEFT JOIN curso AS B ON (A.id_curso = B.id_curso)
                                WHERE A.id_curso = '{$row['id_curso']}' AND A.data >=  '$data_entrada'
                                LIMIT 1;");
    $nrRow = mysql_num_rows($result_curso);
    if ($nrRow != 0){
        $row_curso = mysql_fetch_array($result_curso);
    }else{
        $result_curso = mysql_query("SELECT A.salario AS salario, A.nome
                                FROM curso AS A
                                WHERE A.id_curso = '{$row['id_curso']}'
                                LIMIT 1;");
        $row_curso = mysql_fetch_array($result_curso);
    }

    $result_pro = mysql_query("Select * from  projeto where id_projeto = $pro");
    $row_pro = mysql_fetch_array($result_pro);
    
    $result_empresa = mysql_query("Select * from  rhempresa where id_projeto = '$pro'");
    $row_empresa = mysql_fetch_array($result_empresa);

    $total = "$row_horario[horas_mes]" / "$row_horario[dias_semana]";

    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '2' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if ($num_row_verifica == "0") {
        // mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('2','$clt','$data_cad', '$user_cad')");
    } else {
        //  mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '2'");
    }
    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS

    $result_vale = mysql_query("Select * from vale where id_bolsista = '$row[id_antigo]' AND id_projeto = '$pro'");
    $row_vale = mysql_fetch_array($result_vale);

    $result_banco = mysql_query("Select * from bancos where id_banco = '$row[banco]'");
    $row_banco = mysql_fetch_array($result_banco);

    if ($row['id_banco'] == "") {
        $banco = $row['nome_banco'];
    } else {
        $banco = $row_banco['nome'];
    }

    $result_depende = mysql_query("SELECT *, date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$row[id_clt]' AND nome1 != '' AND id_regiao = '$id_reg' AND id_projeto = '$pro' ORDER BY nome");
    $row_depende = mysql_fetch_array($result_depende);

    $meses = array("1" => "Janeiro", "2" => "Fevereiro", "3" => "Mar�o", "4" => "Abril", "5" => "Maio", "6" => "Junho", "7" => "Julho", "8" => "Agosto", "9" => "Setembro", "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro");
    $mes = $meses[date('n')];
    ?>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
            <title>Registro de Empregado</title>
            <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
            <link href="favicon.png" rel="shortcut icon">
            <link href="resources/css/bootstrap.css" rel="stylesheet" media="all">
            <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
            <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="all">
            <link href="resources/css/font-awesome.min.css" rel="stylesheet" media="all">
            <link href="resources/css/main.css" rel="stylesheet" media="all">
            <link href="resources/css/style-print.css" rel="stylesheet" media="all">
            <style>
                #content{width: 910px; margin: 0 auto;}
                #empresa table{width: 80%; margin-left: 5px; float: right;}
                #empresaAut{float: left; width: 120px; height: 135px; border: 1px solid #000;}
                #pessoal #foto{float: left; height: 140px; padding: 5px 4px 0 0; width: 120px;}

                #estrangeiro {float: left; width: 450px;}
                #recisao {float: right; width: 300px;}
                #polegar {float: left; width: 80px; height: 54px; margin-right: 5px;}
                #assinatura {float: left; width: 341px; height: 54px;}

                hr{border: none; border-top: 1px solid #333;}

                .box{border: 2px solid #000; padding: 10px; margin-top: 10px;}
                .txright{text-align: right;}
                .txcenter{text-align: center;}
                .legenda{font-size: 10px; padding: 0; margin: 0; float: left;}
                .clear{clear: both; padding: 0; margin: 0; line-height: 16px;}
                .txleft{text-align: left;}
                td{padding: 1px 5px;}
                td.b{border: 1px solid #333;}
                td.bl{border-left: none !important;}
                tr.bf td{border-bottom: none !important;}
                tr.bt td{border-top: none !important;}
                p{font-size: 13px; padding: 5px;}

                table thead tr th{font-size: 14px; font-weight: bold;}
                table tbody tr td{padding: 1px 5px; font-size: 13px!important;}

                table.grid{border-top: 1px solid #333; border-left: 1px solid #333;}
                table.grid tr td{border-bottom: 1px solid #333; border-right: 1px solid #333;}
                table.grid tr th{border-bottom: 1px solid #333; background: #F0F0F0;}
                table.grid tr th:last-child{border-right: 1px solid #333;}

                .tbl-foto{
                    width:629px;
                    height:25%;
                }
                body{
                    background-color: #555!important;
                }

                .pagina{
                    height: auto !important;
                }
            </style>
        </head>
        <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="text-center">
                    <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                    <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                </div>
            </div>
        </nav>
            <div id="content" class="pagina">
                <div id="empresa" class="box">
                    <div id="empresaAut"></div>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td colspan="2" class="txcenter"><p><strong>REGISTRO DE EMPREGADO</strong></p></td>
                            <td>
                                <p class="legenda">N� Legal:</p>
                                <p class="clear"> </p>
                            </td>
                            <td>
                                <p class="legenda">Data Emiss�o:</p>
                                <p class="clear"><?php echo date("d/m/Y H:i:s") ?></p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="b">
                                <p class="legenda">Empregador:</p>
                                <p class="clear"><?php echo $row_master['razao']; ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">CGC:</p>
                                <p class="clear"><?php echo $row_empresa['cnpj']; ?></p>
                            </td>
                        </tr>
                        <tr class="bt">
                            <td colspan="4" class="b">
                                <p class="legenda">Endere�o:</p>
                                <p class="clear"><?php echo $row_pro['endereco']; ?></p>
                            </td>
                        </tr>
                        <tr class="bt">
                            <td colspan="2" class="b">
                                <p class="legenda">Bairro:</p>
                                <p class="clear"><?php echo $row_pro['bairro']; ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Cidade:</p>
                                <p class="clear"><?php echo $row_pro['cidade']; ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo $row_pro['estado']; ?></p>
                            </td>
                        </tr>
                    </table>
                    <br class="clear"/>
                </div>

                <div id="pessoal" class="box">

                    <table border="0" cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td colspan="5" class="b">
                                <p class="legenda">Empregado:</p>
                                <p class="clear"> <?php echo $row['nome'] ?></p>
                            </td>
                        </tr>
                        <tr class="bt">
                            <td colspan="3" class="b">
                                <p class="legenda">Resid�ncia:</p>
                                <p class="clear"> <?php echo $row['endereco'] . ", " . $row['numero'] ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Complemento:</p>
                                <p class="clear"> <?php echo $row['complemento'] ?></p>
                            </td>
                        </tr>
                        <tr class="bt">
                            <td class="b">
                                <p class="legenda">Bairro:</p>
                                <p class="clear"> <?php echo $row['bairro'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Cidade:</p>
                                <p class="clear"> <?php echo $row['cidade'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">UF:</p>
                                <p class="clear"> <?php echo $row['uf'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Nacionalidade:</p>
                                <p class="clear"> <?php echo $row['nacionalidade'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Pa�s:</p>
                                <p class="clear"> </p>
                            </td>
                        </tr>
                        <tr class="bt">
                            <td class="b">
                                <p class="legenda">Telefone Fixo:</p>
                                <p class="clear"> <?php echo $row['tel_fixo'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Telefone Celular:</p>
                                <p class="clear"> <?php echo $row['tel_cel'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Telefone para Recados:</p>
                                <p class="clear"> <?php echo $row['tel_rec'] ?></p>
                            </td>
                            <td colspan="2" class="b bl"></td>
                        </tr>
                    </table>
                    <div class="row">
                        <div id="foto">
                            <?php
                            if ($row['foto'] == "1") {
                                $nome_imagem = $id_reg . "_" . $pro . "_" . $row['0'] . ".gif";
                            } else {
                                $nome_imagem = 'semimagem.gif';
                            }
                            print "<img src='fotosclt/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>";
                            ?>
                        </div>

                        <table border="0" cellspacing="0" cellpadding="0" class="tbl-foto" width="652px">
                            <tr class="bt">
                                <td class="b">
                                    <p class="legenda">Sexo:</p>
                                    <p class="clear"> <?php echo $row['sexo'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Data Nasc.:</p>
                                    <p class="clear"> <?php echo $row['data_nasci'] ?></p>
                                </td>
                                <td colspan="4" class="b bl">
                                    <p class="legenda">Naturalidade:</p>
                                    <p class="clear"> <?php echo $row['naturalidade'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Estado Civil:</p>
                                    <p class="clear"> <?php echo $row['civil'] ?></p>
                                </td>
                            </tr>
                            <tr class="bt">
                                <td colspan="2" class="b">
                                    <p class="legenda">CIC/CPF:</p>
                                    <p class="clear"> <?php echo $row['cpf'] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">C�dula Identidade RG:</p>
                                    <p class="clear"> <?php echo $row['rg'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">UF:</p>
                                    <p class="clear"> <?php echo $row['uf_rg'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Data de Emiss�o:</p>
                                    <p class="clear"> <?php echo $row['data_rg'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Org�o Emissor:</p>
                                    <p class="clear"> <?php echo $row['orgao'] ?></p>
                                </td>
                            </tr>
                            <tr class="bt">
                                <td colspan="5" class="b">
                                    <p class="legenda">Pai:</p>
                                    <p class="clear"> <?php echo $row['pai'] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Nacionalidade:</p>
                                    <p class="clear"> <?php echo $row['nacionalidade_pai'] ?></p>
                                </td>
                            </tr>
                            <tr class="bt">
                                <td colspan="5" class="b">
                                    <p class="legenda">M�e:</p>
                                    <p class="clear"> <?php echo $row['mae'] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Nacionalidade:</p>
                                    <p class="clear"> <?php echo $row['nacionalidade_mae'] ?></p>
                                </td>
                            </tr>
                            <tr class="bt bf">
                                <td colspan="2" class="b">
                                    <p class="legenda">CTPS n�:</p>
                                    <p class="clear"> <?php echo $row['campo1'] ?></p>
                                </td>
                                <td class="b bl bf">
                                    <p class="legenda">S�rie:</p>
                                    <p class="clear"> <?php echo $row['serie_ctps'] ?></p>
                                </td>
                                <td class="b bl bf">
                                    <p class="legenda">Letra:</p>
                                    <p class="clear"> - </p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">UF Emissor:</p>
                                    <p class="clear"> <?php echo $row['uf_ctps'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Data Expedi��o:</p>
                                    <p class="clear"> <?php echo $row['data_ctps'] ?></p>
                                </td>
                                <td class="b bl">
                                    <p class="legenda">Data Validade:</p>
                                    <p class="clear"> - </p>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                        <tr>
                            <td colspan="3" class="b">
                                <p class="legenda">Titulo de Eleitor N�:</p>
                                <p class="clear"><?php echo empty($row['titulo']) ? " - " : $row['titulo'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Zona:</p>
                                <p class="clear"><?php echo empty($row['zona']) ? " - " : $row['zona'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Se��o:</p>
                                <p class="clear"><?php echo empty($row['secao']) ? " - " : $row['secao'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Carat Habilita��o:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Categoria:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="2" class="b">
                                <p class="legenda">Doc. Militar:</p>
                                <p class="clear"> <?php echo empty($row['reservista']) ? " - " : $row['reservista'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Esp�cie:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="3" class="b bl">
                                <p class="legenda">Escolaridade:</p>
                                <p class="clear"><?php
                                    $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '{$row['escolaridade']}' AND status = 'on'");
                                    $escolaridade = mysql_fetch_assoc($qr_escolaridade);
                                    echo $escolaridade['nome'];
                                    ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Data Admiss�o:</p>
                                <p class="clear"> <?php echo $row['data_entrada'] ?></p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="3" class="b">
                                <p class="legenda">Fun��o:</p>
                                <p class="clear"> <?php echo $row_curso['nome'] ?></p>
                            </td>
                            <td  colspan="3" class="b bl">
                                <p class="legenda">Se��o:</p>
                                <p class="clear"> <?php
                                    $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$_GET[pro]'");
                                    $projeto = mysql_fetch_assoc($qr_projeto);
                                    echo $projeto['nome'];
                                    ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Sal�rio:</p>
                                <p class="clear"> <?php echo number_format($row_curso['salario'], 2, ',', '.'); ?></p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="2" class="b">
                                <p class="legenda">Regime:</p>
                                <p class="clear"> Mensalista </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Hor�rio Trabalho:</p>
                                <p class="clear"> <?php echo $row_horario['entrada_1br'] . " at� " . $row_horario['saida_2br']; ?> </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Intervalo de:</p>
                                <p class="clear"> <?php echo $row_horario['saida_1br'] . " at� " . $row_horario['entrada_2br']; ?> </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Horas Semanais:</p>
                                <p class="clear"> <?php echo $row_horario['horas_mes'] / 4; ?> </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Horas Padr�o:</p>
                                <p class="clear"> <?php echo $row_horario['horas_mes']; ?> </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="2" class="b">
                                <p class="legenda">Sindicato Categoria:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="3" class="b bl">
                                <p class="legenda">Sindicato Predominante:</p>
                                <p class="clear"> <?php
                                    $qr_sindi = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '{$row['rh_sindicato']}'");
                                    $row_sindi = mysql_fetch_assoc($qr_sindi);
                                    echo empty($row_sindi['entidade']) ? "-" : $row_sindi['entidade'];
                                    ?> </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Sindicalizado:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td class="b" width="100">
                                <p class="clear">FGTS</p>
                                <p class="clear"><?php echo empty($row['fgts']) ? "-" : $row['fgts']; ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Op��o em:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Conta Vinculada no banco:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Data da Retrata��o:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td class="b" width="80">
                                <p class="clear">CR�DITO</p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Banco:</p>
                                <p class="clear"> <?php echo $row_banco['nome'] ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Ag�ncia:</p>
                                <p class="clear"> <?php echo $row['agencia'] ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Conta:</p>
                                <p class="clear"> <?php echo $row['conta'] ?></p>
                            </td>
                        </tr>

                    </table>
                </div>

                <div id="pispasep" class="box">
                    <p class="center"><strong>PIS/PASEP</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                        <tr>
                            <td class="b">
                                <p class="legenda">Cadastrado em:</p>
                                <p class="clear"><?php echo $row['data_pis'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Sob o:</p>
                                <p class="clear"><?php echo $row['pis'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Data Expedi��o:</p>
                                <p class="clear"><?php echo $row['data_pis'] ?></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Domic�lio Banc�rio:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td class="b">
                                <p class="legenda">N� Banco:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Ag�ncia c�digo:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Endere�o da Agencia:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                    </table>
                </div>

                <div id="estrangeiro" class="box">
                    <p class="center"><strong>Quadro Estrangeiro</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                        <tr>
                            <td colspan="2" class="b">
                                <p class="legenda">Registro Nacional de Estra.RNE:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Casado (brasileira)</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="4" class="b">
                                <p class="legenda">Nome do c�njuge:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td class="b">
                                <p class="legenda">Filhos Brasil:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Data Chegada:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Naturalizado:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Decreto n.:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>

                        <tr class="bt">
                            <td colspan="2" class="b">
                                <p class="legenda">Visto Permanente:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Tempor�rio:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Com vencto. em:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="recisao" class="box txleft">
                    <p class="center"><strong>Rescis�o do contrato de trabalho</strong></p>
                    <hr/>
                    <p>Data sa�da: <strong><?php echo $row_demi['data_saida'] ?></strong> homologa��o n�: <strong><?php echo $row_demi['homologacao'] ?></strong></p>
                    <p>
                        Tipo de Desligamento: <br/>
                        <strong><?php echo $row_demi['desligamento'] ?></strong>
                    </p>
                    <p>Org�o onde foi feito a homologa��o: <?php echo $row_demi['local_homolog'] ?> </p>
                    <br>
                    <br>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                        <tr>
                            <td class="b" width="80">
                                <p class="legenda">Data:</p>
                                <p class="clear"> <br/> <br/> <br/> <br/></p>
                            </td>
                            <td class="b bl">
                                <p class="legenda">Carimbo e assinatura do empregador</p>
                                <p class="clear"> <br/> <br/> <br/> <br/></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="polegar" class="box">
                    <p class="legenda">Polegar Direito</p>
                </div>

                <div id="assinatura" class="box">
                    <p class="center"><strong>Assinatura</strong></p>
                    <p class="center">______________________________________________</p>
                </div>


                <br class="clear"/>
                <br class="quebra-aqui"/>

                <div class="box">
                    <p class="center"><strong>CONTRIBU���O SINDICAL</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Guia n�</th>
                                <th>Refer�ncia</th>
                                <th>Desconto</th>
                                <th>Valor</th>
                                <th>Nome do Sindicato</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php while ($rowCSind = mysql_fetch_assoc($qr_cont_sind)) { ?>
                                <tr>
                                    <td>-</td>
                                    <td><?php echo $rowCSind['mes'] . "/" . $rowCSind['ano'] ?></td>
                                    <td><?php echo $rowCSind['mes'] . "/" . $rowCSind['ano'] ?></td>
                                    <td><?php echo $rowCSind['a5019'] ?></td>
                                    <td><?php echo $rowCSind['nome'] ?></td>
                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>ALTERA��ES DE FUN��O</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Em</th>
                                <th>CBO</th>
                                <th>Fun��o</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysql_num_rows($qr_alt_funcao) > 0) {
                                while ($row_alt_func = mysql_fetch_assoc($qr_alt_funcao)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row_alt_func['data'] ?></td>
                                        <td><?php echo "DE: " . $row_alt_func['cbo_de'] . ", PARA: " . $row_alt_func['cbo_para'] ?></td>
                                        <td><?php echo "DE: " . $row_alt_func['de'] . ", PARA: " . $row_alt_func['para'] ?></td>
                                        <td><?php echo (empty($row_alt_func['motivo'])) ? "-" : $row_alt_func['motivo']; ?></td>
                                    </tr>
        <?php }
    } else { ?>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>ALTERA��ES DE SAL�RIOS</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Em</th>
                                <th>Valor</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(mysql_num_rows($qr_alt_salario) > 0){
                                while($row_alt_salario = mysql_fetch_assoc($qr_alt_salario)) { 
                                    ?>
                                    <tr>
                                        <td><?=$row_alt_salario['dataBR']?></td>
                                        <td><?=number_format($row_alt_salario['salario_novo'],2,',','.')?></td>
                                        <td><?=(empty($row_alt_salario['motivo']))?'-':$row_alt_salario['motivo']?></td>
                                    </tr>
                                <?php 
                                }
                            } else { ?>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>F�RIAS</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>F�RIAS - PER�ODO AQUISITIVO</th>
                                <th>F�RIAS - PER�ODO DE GOZO</th>
                                <th>1/3 DAS F�RIAS EM ABONO PECUNI�RIO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row_ferias = mysql_fetch_array($qr_ferias)) { ?>
                                <tr>
                                    <td><?php echo $row_ferias['data_aquisitivo_ini'] . " - " . $row_ferias['data_aquisitivo_fim'] ?></td>
                                    <td><?php echo $row_ferias['data_ini'] . " - " . $row_ferias['data_fim'] ?></td>
                                    <td><?php echo "Em " . $row_ferias['data_proc'] . " Dias " . $row_ferias['dias_abono_pecuniario']; ?></td>
                                </tr>
                            <?php } ?>
                            <?php if(!empty($p) && isset($p)){ ?>    
                                    <tr>
                                        <th colspan="3">PER�ODOS PERDIDOS </th>
                                    </tr>
                                <?php foreach ($p as $k => $periodosAquisitivo){ ?>
                                    <tr>
                                        <td colspan="2"><?php echo $periodosAquisitivo['periodos']; ?></td>
                                        <td><?php echo $periodosAquisitivo['dias']; ?></td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                                    
                            <?php if(!empty($motivos) && isset($motivos)){ ?>         
                                <tr>
                                    <th colspan="3">MOTIVO</th>
                                </tr>
                                <tr>
                                    <td colspan="3"><?php echo "<b>Criado Por: </b>" . $motivos['nome'] ." - ". $motivos['criado_em']; ?></td>
                                </tr>       
                                <?php foreach ($motivos['motivo'] as $dados){ ?>
                                     <tr>
                                        <td colspan="3"><?php echo $dados; ?></td>
                                    </tr>                             
                                <?php } ?>
                                
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>OCORR�NCIAS DE AFASTAMENTOS</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Data afastamento</th>
                                <th>Data retorno</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php while ($row_afasta = mysql_fetch_array($qr_afasta)) { ?>
                                <tr>
                                    <td><?php echo $row_afasta['databr'] ?></td>
                                    <td><?php echo $row_afasta['data_retornobr'] ?></td>
                                    <td><?php echo empty($row_afasta['nome_status']) ? "-" : $row_afasta['nome_status']; ?></td>
                                </tr>
    <?php } ?> 
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>DEPENDENTES</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Sexo</th>
                                <th>Parentesco</th>
                                <th>Data Nascimento</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php if (expersonalizada($row_depen['nome1'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome1'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data1br'] ?></td>
                                </tr>
    <?php } ?>
    <?php if (expersonalizada($row_depen['nome2'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome2'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data2br'] ?></td>
                                </tr>
    <?php } ?>
    <?php if (expersonalizada($row_depen['nome3'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome3'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data3br'] ?></td>
                                </tr>
    <?php } ?>
    <?php if (expersonalizada($row_depen['nome4'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome4'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data4br'] ?></td>
                                </tr>
    <?php } ?>
    <?php if (expersonalizada($row_depen['nome5'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome5'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data5br'] ?></td>
                                </tr>
    <?php } ?>
    <?php if (expersonalizada($row_depen['nome6'], null, array('[[:digit:]]')) != "") { ?>
                                <tr>
                                    <td><?php echo $row_depen['nome6'] ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><?php echo $row_depen['data6br'] ?></td>
                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>CARTEIRAS PROFISSIONAIS DO FUNCION�RIO</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>N�mero</th>
                                <th>S�rie</th>
                                <th>Letra</th>
                                <th>Data Expedi��o</th>
                                <th>Data validade</th>
                                <th>Sigla</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><!--,serie_ctps,uj_ctps,campo3-->
                                <td><?php echo $row['campo1'] ?></td>
                                <td><?php echo $row['serie_ctps'] ?></td>
                                <td>-</td>
                                <td><?php echo $row['data_ctps'] ?></td>
                                <td>-</td>
                                <td><?php echo $row['uf_ctps'] ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>HIST�RICO CIPA</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>C�digo</th>
                                <th>Descri��o</th>
                                <th>Data in�cio</th>
                                <th>Data fim</th>
                                <th>Observa��o</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>GRADUA��O DO FUNCION�RIO</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Institu���o</th>
                                <th>Profiss�o</th>
                                <th>Org. Classe</th>
                                <th>Data final</th>
                                <th>Num. Reg.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $row['instituicao'] ?></td>
                                <td><?php echo $row_curso['nome'] ?></td>
                                <td><?php echo $row['conselho'] ?></td>
                                <td><?php echo $row['data_escola'] ?></td>
                                <td><?php echo ($row['conselho'] != "") ? $row['rg'] : "-" ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>HIST�RICO DE EXAMES</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Exame</th>
                                <th>Data pedido</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php while ($row_exame = mysql_fetch_assoc($qr_exame)) { ?>
                                <tr>
                                    <td><?php echo $row_exame['documento'] ?></td>
                                    <td><?php echo $row_exame['data'] ?></td>
                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>HIST�RICO DE CURSOS</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Curso</th>
                                <th>Data in�cio</th>
                                <th>Data t�rmino</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>TRANSFER�NCIA DE UNIDADES</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>C�digo</th>
                                <th>Descri��o</th>
                                <th>Motivo</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php if (mysql_num_rows($qr_transf) > 0) {
        while ($row_transf = mysql_fetch_assoc($qr_transf)) {
            ?>
                                    <tr>
                                        <td><?php echo $row_transf['id_transferencia'] ?></td>
                                        <td><?php echo "DE: " . $row_transf['de'] . ", PARA: " . $row_transf['para'] ?></td>
                                        <td><?php echo (empty($row_transf['motivo'])) ? "-" : $row_transf['motivo']; ?></td>
                                        <td><?php echo $row_transf['data'] ?></td>
                                    </tr>
        <?php }
    } else { ?>
                                <tr>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>AVERBA��ES</strong></p>
                    <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Campo</th>
                                <th>Conte�do</th>
                                <th>Justificativa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="box">
                    <p class="center"><strong>OBSERVA��ES</strong></p>
                    <p class="txleft"><?php echo $row['observacao'] ?></p>
                </div>

            </div>
        </body>
    </html>
<?php } ?>