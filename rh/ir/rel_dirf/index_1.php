<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include("../../../conn.php");
include("../../../classes/funcionario.php");
include("../../../classes_permissoes/regioes.class.php");
include("../../../wfunction.php");

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$id_master'");
$optRegiao = array();
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
    $ids_Regioes[] = $valor['id_regiao'];
}

$regiaoSel = (isset($_REQUEST['id_regiao'])) ? $_REQUEST['id_regiao'] : '';

if (isset($_POST['historico'])) {
    $ano_calendario = $_POST['ano'];
    $master = $_POST['id_master'];
    $qr_historico = mysql_query("SELECT * FROM dirf WHERE id_master = '$master' AND status = 1");
    $verifica_historico = mysql_num_rows($qr_historico);
}

if (isset($_POST['gerar'])) {
    $ano_referencia = $_REQUEST['anoref'];
    $ano_calendario = $_POST['ano'];
    $CnpjCoop = $_REQUEST['coop'];
    // $id_regiao = $_POST['regiao'];
    //$tipo_arquivo = $_POST['tipo_arquivo'];
    $tipo_arquivo = 1; //COOPERADO SOMENTE
    
    $checked_clt = ($tipo_arquivo == 1) ? 'checked' : '';
    $checked_autonomo = ($tipo_arquivo == 2) ? 'checked' : '';
    $checked_prestador = ($tipo_arquivo == 3) ? 'checked' : '';

    //$master = mysql_result(mysql_query("SELECT id_master FROM regioes WHERE id_regiao = '$id_regiao'"), 0) or die(mysql_error());
    $master = $_POST['id_master'];
    $n_arquivo = $master . '_' . $ano_calendario . '.txt';
    $nome_arquivo = 'arquivos_coop/' . $n_arquivo;
    
    $rsCoops = montaQuery("cooperativas", "id_coop,gera_dirf", "cnpj='{$CnpjCoop}' AND id_regiao IN (".implode(",",$ids_Regioes).")");
    $idsCoops = array();
    $idCoopMaster = null;
    
    foreach($rsCoops as $coop){
        $idsCoops[] = $coop['id_coop'];
        if($coop['gera_dirf']==1){
            $idCoopMaster = $coop['id_coop'];
        }
    }
    
    
    
    $qr_empresa = mysql_query("SELECT   REPLACE(REPLACE(REPLACE(cnpj,'/',''),'.',''),'-','')as cnpj, 
                                        SUBSTR(REPLACE(REPLACE(nome,'?',''),',',''),1,150)as nome,
                                        cpfd,contador_cpf, contador_nome, contador_tel_ddd, contador_tel_num, contador_fax,contador_email
                                  FROM  cooperativas WHERE id_coop = {$idCoopMaster};") OR die(mysql_error());
    $empresa = mysql_fetch_assoc($qr_empresa);
    
    $ok=true;
    //if (!empty($tipo_arquivo)) {
    if ($ok) {

        $verifica_dirf = mysql_num_rows(mysql_query("SELECT * FROM dirf WHERE id_master = '$master' AND ano_calendario = '$ano_calendario' AND status = 1 AND tipo_contratacao_id = 3"));
        if ($verifica_dirf == 0) {

            //////////////////////////////////////                   
            /////CABEÇALHO (IDENTIFICADOR DIRF /////
            ////////////////////////////////////////
            
            $IDENTIFICADOR_DIRF['ID_REGISTRO'] = 'DIRF';
            $IDENTIFICADOR_DIRF['ANO_REFERENCIA'] = $ano_referencia;
            $IDENTIFICADOR_DIRF['ANO_CALENDARIO'] = $ano_calendario;
            $IDENTIFICADOR_DIRF['IDENTIFICADOR_RETIFICADORA'] = 'N';
            $IDENTIFICADOR_DIRF['NUMERO_RECIBO'] = NULL;
            $IDENTIFICADOR_DIRF['IDENTIFICADOR_ESTRUTURA_LEIAUTE'] = 'F8UCL6S'; //2012/2013('7C2DE7J')

            $LINHA_ID_DIRF = implode('|', $IDENTIFICADOR_DIRF) . '|';

            //////////////////////////////////////                   
            /////RESPONSÁVEL (IDENTIFICADOR RESPO /////
            ////////////////////////////////////////
            $IDENTIFICADOR_RESPO['ID_REGISTRO'] = 'RESPO';
            $IDENTIFICADOR_RESPO['CPF'] = $empresa['contador_cpf'];
            $IDENTIFICADOR_RESPO['NOME'] = $empresa['contador_nome'];
            $IDENTIFICADOR_RESPO['DDD'] = $empresa['contador_tel_ddd'];
            $IDENTIFICADOR_RESPO['TELEFONE'] = $empresa['contador_tel_num'];
            $IDENTIFICADOR_RESPO['RAMAL'] = '';
            $IDENTIFICADOR_RESPO['FAX'] = $empresa['contador_fax'];
            $IDENTIFICADOR_RESPO['EMAIL'] = $empresa['contador_email'];

            $LINHA_ID_RESPO = implode('|', $IDENTIFICADOR_RESPO) . '|';


            //////////////////////////////////////                   
            /////DECLARAÇÃO DE PESSOA JURÍDICA (IDENTIFICADOR DECPJ) /////
            ////////////////////////////////////////
            $IDENTIFICADOR_DECPJ['ID_REGISTRO'] = 'DECPJ';
            $IDENTIFICADOR_DECPJ['CNPJ'] = $empresa['cnpj'];
            $IDENTIFICADOR_DECPJ['NOME_EMPRESA'] = str_replace('-', '', $empresa['nome']);
            $IDENTIFICADOR_DECPJ['NATUREZA_DECLARANTE'] = 0;
            $IDENTIFICADOR_DECPJ['CPF_RESPONSAVEL'] = $empresa['cpfd'];
            $IDENTIFICADOR_DECPJ['INDICADOR_SOCIO'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_DEPOSITARIO'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_INSTITUICAO'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_DECLARANTE_RENDIMENTOS'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_PLANO_PRIVADO'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_PAGAMENTOS'] = 'N';
            $IDENTIFICADOR_DECPJ['INDICADOR_SITUACAO_ESPECIAL'] = 'N';
            $IDENTIFICADOR_DECPJ['DATA_EVENTO'] = '';

            $LINHA_DECPJ = implode('|', $IDENTIFICADOR_DECPJ) . '|';

            ///////////////////////////// //////////////////////////////////////                   
            /////IDENTIFICAÇÃO DE CÓDIGO DA RECEITA (IDENTIFICADOR IDREC) /////
            ///////////////////////////// //////////////////////////////////////  
            $IDENTIFICADOR_IDREC['ID_REGISTRO'] = 'IDREC';
            $IDENTIFICADOR_IDREC['CODIGO_RECEITA'] = '0561';

            $LINHA_IDREC = implode('|', $IDENTIFICADOR_IDREC) . '|';

            $arquivo = fopen($nome_arquivo, 'w');

            fwrite($arquivo, $LINHA_ID_DIRF);
            fwrite($arquivo, "\n");
            fwrite($arquivo, $LINHA_ID_RESPO);
            fwrite($arquivo, "\n");
            fwrite($arquivo, $LINHA_DECPJ);
            fwrite($arquivo, "\n");
            fwrite($arquivo, $LINHA_IDREC);
            fwrite($arquivo, "\n");

            if ($tipo_arquivo == 1) {
                ///////////////////////////// //////////////////////////////////////                   
                ///////////REGISTROS DE VALORES MENSAIS) ////////////////////////////
                ///////////////////////////// //////////////////////////////////////  
                $qrAut = "SELECT C.id_autonomo, C.nome, REPLACE(REPLACE(C.cpf,'-',''), '.','') as cpf
                                        FROM folhas as A
                                       INNER JOIN folha_cooperado as B
                                       ON A.id_folha = B.id_folha
                                       INNER JOIN  autonomo as C
                                       ON B.id_autonomo = C.id_autonomo
                                       WHERE A.ano = '$ano_calendario' AND B.ano = '$ano_calendario' AND A.coop IN(".implode(",",$idsCoops).") AND A.regiao IN (".implode(",",$ids_Regioes).")
                                       AND A.mes > 5
                                       AND A.status = 3 AND A.contratacao = 3 AND B.status = 3
                                       GROUP BY C.id_autonomo
                                       ORDER BY cpf ASC";
                
                $qr_aut = mysql_query($qrAut);
                while ($aut = mysql_fetch_assoc($qr_aut)) {

                    // echo $clt['nome'];
                    // echo '<br>';
                    //DADOS DO CLT 
                    $IDENTIFICADOR_BPFDEC['ID_REGISTRO'] = 'BPFDEC';
                    $IDENTIFICADOR_BPFDEC['CPF'] = trim($aut['cpf']);
                    $IDENTIFICADOR_BPFDEC['NOME_TRAB'] = trim(RemoveCaracteres($aut['nome']));
                    $IDENTIFICADOR_BPFDEC['DATA_ATRIBUIDA'] = '';
                    $LINHA_BPFDEC = implode('|', $IDENTIFICADOR_BPFDEC) . '|';
                    fwrite($arquivo, $LINHA_BPFDEC);
                    fwrite($arquivo, "\n");


                    $LINHA_RTRT['IDENTIFICADOR'] = 'RTRT'; //RENDIMENTOS
                    $LINHA_RTPO['IDENTIFICADOR'] = 'RTPO'; //PREVIDÊNCIA OFICIAL
                    $LINHA_RTPP['IDENTIFICADOR'] = 'RTPP'; //PREVIDÊNCIA PRIVADA
                    $LINHA_RTDP['IDENTIFICADOR'] = 'RTDP'; //DEPENDENTES
                    $LINHA_RTIRF['IDENTIFICADOR'] = 'RTIRF'; //IRRF              
                    $LINHA_RIIRP['IDENTIFICADOR'] = 'RIIRP'; //RESCISAO              
                    $LINHA_RIAP['IDENTIFICADOR'] = 'RIAP'; //ABONO PECUNIÁRIO    
                    $LINHA_RIDAC['IDENTIFICADOR'] = 'RIDAC'; //AJUDA DE CUSTO
                    //12 meses SEM décimo terceiro 
                    for ($i = 1; $i < 13; $i++) {


                        //if ($i > 5) {
                            $qr_valores = mysql_query("select A.salario , A.adicional, 
                                                    IF( adicional != '',
                                                    REPLACE(REPLACE(format(salario+adicional,2),',',''),'.','') ,
                                                    REPLACE(REPLACE(format(salario,2),',',''),'.',''))  as total_rend,
                                                    REPLACE(REPLACE(inss,',',''),'.','') as inss,
                                                    REPLACE(REPLACE(irrf,',',''),'.','') as irrf,
                                                    REPLACE(REPLACE(ajuda_custo,',',''),'.','') as ajuda_custo,
                                                    A.mes
                                                    from folha_cooperado as A
                                                    INNER JOIN folhas as B                                                    
                                                    ON A.id_folha = B.id_folha 
                                                    WHERE A.id_autonomo = {$aut['id_autonomo']} AND A.mes = '" . sprintf('%02d', $i) . "' 
                                                    AND A.ano = '$ano_calendario' AND B.terceiro = 0 AND A.status=3 AND B.coop IN (".implode(",",$idsCoops).");") or die(mysql_error());
                        //}

                        ////////

                        $row_valor = mysql_fetch_assoc($qr_valores);
                        
                        $LINHA_RTRT[] = $row_valor['total_rend'];
                        $LINHA_RTPO[] = $row_valor['inss'];
                        $LINHA_RTPP[] = NULL;
                        $LINHA_RTDP[] = '';
                        $LINHA_RTIRF[] = $row_valor['irrf'];
                        $LINHA_RIIRP[] = NULL;
                        $LINHA_RIAP[] = NULL;
                        $LINHA_RIDAC[] = $row_valor['ajuda_custo'];
                    }

                    ////DÉCIMO TERCEIRO
                    $qr_valores_dt = mysql_query("select IF( adicional != '',
                                                    REPLACE(REPLACE(format(salario+adicional,2),',',''),'.','') ,
                                                    REPLACE(REPLACE(format(salario,2),',',''),'.',''))  as total_rend,
                                                    REPLACE(REPLACE(inss,',',''),'.','') as inss,
                                                    REPLACE(REPLACE(irrf,',',''),'.','') as irrf,
                                                    REPLACE(REPLACE(ajuda_custo,',',''),'.','') as ajuda_custo
                                                    FROM folha_cooperado as A
                                                    INNER JOIN folhas as B
                                                    ON A.id_folha = B.id_folha 
                                                    WHERE A.id_autonomo = $aut[id_autonomo]  AND A.ano = '$ano_calendario' AND B.terceiro = 1;") or die(mysql_error());
                    while ($row_valor_dt = mysql_fetch_assoc($qr_valores_dt)) {

                        $DECIMO_RTRT += $row_valor_dt['total_rend'];
                        $DECIMO_RTPO += $row_valor_dt['inss'];
                        $DECIMO_RTPP += NULL;
                        $DECIMO_RTDP += '';
                        $DECIMO_RTIRF += $row_valor_dt['irrf'];
                        $DECIMO_RIDAC += '';
                    }


                    //////GRAVANDO VALORES MENSAIS NO TXT
                    for ($i = 0; $i < 12; $i++) {

                        $exibir_rtrt += (!empty($LINHA_RTRT[$i])) ? 1 : 0;
                        $exibir_rtpo += (!empty($LINHA_RTPO[$i])) ? 1 : 0;
                        $exibir_rtpp += (!empty($LINHA_RTPP[$i])) ? 1 : 0;
                        $exibir_rtdp += (!empty($LINHA_RTDP[$i])) ? 1 : 0;
                        $exibir_rtirf += (!empty($LINHA_RTIRF[$i])) ? 1 : 0;
                        $exibir_riirp += (!empty($LINHA_RIIRP[$i])) ? 1 : 0;
                        $exibir_riap += (!empty($LINHA_RIAP[$i])) ? 1 : 0;
                        $exibir_ridac += (!empty($LINHA_RIDAC[$i])) ? 1 : 0;
                    }

                    if (!empty($exibir_rtrt) or !empty($DECIMO_RTRT)) {
                        $LINHA_RTRT = implode('|', $LINHA_RTRT) . '|' . $DECIMO_RTRT . '|';
                        fwrite($arquivo, $LINHA_RTRT);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_rtpo) or !empty($DECIMO_RTPO)) {
                        $LINHA_RTPO = implode('|', $LINHA_RTPO) . '|' . $DECIMO_RTPO . '|';
                        fwrite($arquivo, $LINHA_RTPO);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_rtpp) or !empty($DECIMO_RTPP)) {
                        $LINHA_RTPP = implode('|', $LINHA_RTPP) . '|' . $DECIMO_RTPP . '|';
                        fwrite($arquivo, $LINHA_RTPP);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_rtdp) or !empty($DECIMO_RTDP)) {
                        $LINHA_RTDP = implode('|', $LINHA_RTDP) . '|' . $DECIMO_RTDP . '|';
                        fwrite($arquivo, $LINHA_RTDP);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_rtirf) or !empty($DECIMO_RTIRF)) {
                        $LINHA_RTIRF = implode('|', $LINHA_RTIRF) . '|' . $DECIMO_RTIRF . '|';
                        fwrite($arquivo, $LINHA_RTIRF);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_riirp) or !empty($DECIMO_RIIRP)) {
                        $LINHA_RIIRP = implode('|', $LINHA_RIIRP) . '|' . $DECIMO_RIIRP . '|';
                        fwrite($arquivo, $LINHA_RIIRP);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_riap) or !empty($DECIMO_RIAP)) {
                        $LINHA_RIAP = implode('|', $LINHA_RIAP) . '|' . $DECIMO_RIAP . '|';
                        fwrite($arquivo, $LINHA_RIAP);
                        fwrite($arquivo, "\n");
                    }

                    if (!empty($exibir_ridac)) {
                        $LINHA_RIDAC = implode('|', $LINHA_RIDAC) . '||';
                        fwrite($arquivo, $LINHA_RIDAC);
                        fwrite($arquivo, "\n");
                    }

                    unset($DECIMO_RIDAC, $LINHA_RIDAC, $exibir_ridac, $LINHA_RTRT, $DECIMO_RTRT, $LINHA_RTPO, $DECIMO_RTPO, $LINHA_RTPP, $DECIMO_RTPP, $LINHA_RTDP, $DECIMO_RTDP, $LINHA_RTIRF, $DECIMO_RTIRF, $exibir_rtrt, $exibir_rtpo, $exibir_rtpp, $exibir_rtdp, $exibir_rtirf, $LINHA_RIIRP, $DECIMO_RIIRP, $exibir_riirp, $chave, $LINHA_RIAP, $DECIMO_RIAP, $exibir_riap);
                }
            }//FIM LINHAS CLT  
            fwrite($arquivo, "FIMDirf|");
            fclose($arquivo);

            /*    mysql_query("INSERT INTO dirf (id_master, ano_calendario, data_geracao, gerado_por, arquivo_clt,arquivo_autonomo, arquivo_prestador)
              VALUES
              ('$master', '$ano_calendario', NOW(), '$_COOKIE[logado]', '$checked_clt', '$checked_autonomo', '$checked_prestador')") or die(mysql_error());
             */

            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Content-type: application/x-msdownload");
            header("Content-Length: " . filesize($nome_arquivo));
            header("Content-Disposition: attachment; filename={$n_arquivo}");
            flush();

            readfile($nome_arquivo);
            exit;
        }
    }
}


//CARREGANDO DADOS PARA OS SELECTS

//ANO
$optAnos = anosArray(2009, date('Y'));
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');

//COOPERATIVAS VINCULADAS AS REGIÕES DO MASTER E QUE TEM A FLAG GERA_DARF
$rsCoops = montaQuery("cooperativas", "cnpj,nome", "id_regiao IN (".implode(",",$ids_Regioes).") AND gera_dirf = 1", null, null, "array",false, "cnpj");
$optCoops = array("-1"=> "« Selecione »");
#print_r($ids_Regioes);
foreach($rsCoops as $valor){
    $optCoops[$valor['cnpj']] = $valor['cnpj']." - ".$valor['nome'];
}
$coopSel = (isset($_REQUEST['coop'])) ? $_REQUEST['coop'] : null;

?>
<html>
    <head>
        <title>Gerar DIRF Cooperado</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../../js/global.js" type="text/javascript" ></script>
        <script>
            $(function() {
                alert();

                /*$('#form').submit(function(){
                 
                 // var checkbox = $('input[name=tipo_arquivo]:checked');
                 alert(checkbox);
                 return false;
                 });*/


            });

        </script>


    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <!--<img src="../../../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                --><div class="fleft">
                    <h2>DIRF Cooperado</h2>
                    <p>Gerar arquivo de DIRF</p>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>DIRF</legend>
                    <div class="fleft">
                        <p><label class="first">Ano Calendário:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                        <p><label class="first">Ano Referência:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "anoref", 'id' => 'anoref')); ?></p>
                        <p><label class="first">Cooperativa:</label> <?php echo montaSelect($optCoops, $coopSel, array('name' => "coop", 'id' => 'coop')); ?></p>
                        <!--<p><label class="first">Tipo de arquivo:</label> 
                            <input type="checkbox" name="tipo_arquivo" value="1" <?php echo $checked_clt; ?>/>CLT 
                            <input type="checkbox" name="tipo_arquivo" value="2" <?php echo $checked_autonomo; ?>/>AUTÔNOMO
                            <input type="checkbox" name="tipo_arquivo" value="3" <?php echo $checked_prestador; ?>/>PRESTADOR DE SERVIÇO
                            <span class="erro"><?php if (empty($tipo_arquivo)) {
                                                echo '*Selecione os tipo de arquivo.';
                                            } ?></span>
                        </p>-->
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                        <input type="submit" name="historico" value="Exibir histórico" id="historico"/>
                        <input type="submit" name="gerar" value="Gerar" id="gerar"/>
                    </p>
                </fieldset>
            </form>
                <?php
                if (!empty($verifica_historico) and isset($_POST['historico'])) {

                    while ($row_historico = mysql_fetch_assoc($qr_historico)) {
                        ?> 
                    <span class="box_download fleft ">
                        <a href="arquivos_coop/<?php echo $row_historico['id_master'] . '_' . $row_historico['ano_calendario'] . '.txt'; ?>" style="text-decoration:none;">
                            <img src="../../../imagens/download.png"/>
                            <br>
                            DIRF <?php echo $row_historico['ano_calendario']; ?>
                        </a>
                    </span>
                    <?php
                        }
                    } else {
                        echo '<div class="txcenter">Não existem arquivos de DIRF.</div>';
                    }
                ?>  
            <div class="clear"></div>
        </div>
    </body>
</html>