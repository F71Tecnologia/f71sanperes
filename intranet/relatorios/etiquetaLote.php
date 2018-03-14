<?php
/*
 * PHO-DOC - etiquetaLote.php
 * 
 * ??-??-????
 * 
 * Rotina para gera��o de etiqueta em lote
 * 
 * Vers�o: 3.0.8413 - 21/03/2016 - Jacques - Crit�rio para escolha do �ltimo sal�rio passou a ser pela diferen�a,
 *                                           e o campo nome do projeto o endere�o para empresa
 * 
 * Vers�o: 3.0.9675 - 02/06/2016 - Leonardo - Altera��o do curso para puxar o cbo correto. Antes estava puxando o id_cbo
 * 
 * @Autor n�o definido
 *  
 */
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

include "../conn.php";

$clt = $_REQUEST['clt'];
$id_reg = $_REQUEST['id_reg'];
$id_pro = $_REQUEST['pro'];

$id_user = $_COOKIE['logado'];

$data = date('d/m/Y');

$result_clt = "SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada FROM rh_clt where id_clt = '$clt'";
$result_clt = mysql_query($result_clt);
$row_clt = mysql_fetch_array($result_clt);

$result_curso = mysql_query("Select * from  curso where id_curso = '$row_clt[id_curso]'");
$row_curso = mysql_fetch_array($result_curso);

$result_reg = mysql_query("Select * from  regioes where id_regiao = '$id_reg'", $conn);
$row_reg = mysql_fetch_array($result_reg);

$result_proj = mysql_query("SELECT * FROM projeto WHERE id_projeto='$id_pro' ");
$row_proj = mysql_fetch_assoc($result_proj);
//print_r($row_proj);
$qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_reg[id_master]' ") or die(mysql_error());
$row_master = mysql_fetch_assoc($qr_master);
$row3 = mysql_fetch_array($row_master);

$result_empresa = mysql_query("Select * from  rhempresa where id_projeto = '$id_pro'");
$row_empresa = mysql_fetch_array($result_empresa);

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Mar�o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$dia = date('d');
$mes = date('n');
$ano = date('Y');

switch ($mes) {
    case 1:
        $mes = "Janeiro";
        break;
    case 2:
        $mes = "Fevereiro";
        break;
    case 3:
        $mes = "Mar�o";
        break;
    case 4:
        $mes = "Abril";
        break;
    case 5:
        $mes = "Maio";
        break;
    case 6:
        $mes = "Junho";
        break;
    case 7:
        $mes = "Julho";
        break;
    case 8:
        $mes = "Agosto";
        break;
    case 9:
        $mes = "Setembro";
        break;
    case 10:
        $mes = "Outubro";
        break;
    case 11:
        $mes = "Novembro";
        break;
    case 12:
        $mes = "Dezembro";
        break;
}

$data_entrada = explode("/", $row_clt['data_entrada']);
$dia_entrada = $data_entrada[0];
$mes_entrada = $data_entrada[1];
$ano_entrada = $data_entrada[2];
$data_final = date("d/m/Y", mktime(0, 0, 0, $mes_entrada, $dia_entrada + 44, $ano_entrada));
$data_final1 = explode("/", $data_final);
$dia_final = $data_final1[0];
$mes_final = $data_final1[1];
$ano_final = $data_final1[2];
$data_final2 = date("d/m/Y", mktime(0, 0, 0, $mes_final, $dia_final + 44, $ano_final));


if ($_COOKIE['logado'] != 87 and $row_clt['status'] == 10) {
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '3' and id_clt = '$clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);
    if ($num_row_verifica == "0") {
        mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('3','$clt','$data_cad', '$user_cad')");
    } else {
        mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$clt' and tipo = '3'");
    }
//-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
}
?>
<?php

function valor_extenso($valor = 0, $maiusculas = false) {
    // verifica se tem virgula decimal
    if (strpos($valor, ",") > 0) {
        // retira o ponto de milhar, se tiver
        $valor = str_replace(".", "", $valor);

        // troca a virgula decimal por ponto decimal
        $valor = str_replace(",", ".", $valor);
    }
    $singular = array("centavo", "real", "mil", "milh�o", "bilh�o", "trilh�o", "quatrilh�o");
    $plural = array("centavos", "reais", "mil", "milh�es", "bilh�es", "trilh�es",
        "quatrilh�es");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "tr�s", "quatro", "cinco", "seis",
        "sete", "oito", "nove");

    $z = 0;

    $valor = number_format($valor, 2, ".", ".");
    $inteiro = explode(".", $valor);
    $cont = count($inteiro);
    for ($i = 0; $i < $cont; $i++)
        for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
            $inteiro[$i] = "0" . $inteiro[$i];

    $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
    for ($i = 0; $i < $cont; $i++) {
        $valor = $inteiro[$i];
        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

        $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                $ru) ? " e " : "") . $ru;
        $t = $cont - 1 - $i;
        $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
        if ($valor == "000")
            $z++; elseif ($z > 0)
            $z--;
        if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
            $r .= (($z > 1) ? " de " : "") . $plural[$t];
        if ($r)
            $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                    ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
    }

    if (!$maiusculas) {
        return($rt ? $rt : "zero");
    } elseif ($maiusculas == "2") {
        return (strtoupper($rt) ? strtoupper($rt) : "Zero");
    } else {
        return (ucwords($rt) ? ucwords($rt) : "Zero");
    }
}
?>
<HTML>
    <TITLE>Etiqueta em Lote</TITLE>
    <HEAD>
        <STYLE TYPE="text/css">
            .Principal{
                width: 574px;
                padding: 10px;
            }
            .etiquetaPrincipal{
                width: 7cm;
                height: 7cm;
                border:1px solid #000;
                font-size: 11px;
                padding: 5px;
                text-align: justify;
                float: left;
                
                
            }
            .divAssinatura{
                float: right;
            }
            .etiquetaPrincipal2{
                width: 7cm;
                height: 5.5cm;
                border:1px solid #000;
                font-size: 10px;
                padding: 5px;
                text-align: justify;
                float: left;
                margin-left: 20px;
            }
        </STYLE>
    </HEAD>
    <!--<BODY onload="print();">-->
    <BODY>
    

        <div class="Principal">
            <?php
            if (!empty($_POST['check_list'])) {
                $cont = 0;
                foreach ($_POST['check_list'] as $check) {
                    $cont++;

                    $qrlote = "select * from rh_clt where id_clt = $check";
                    $rslote = mysql_query($qrlote);
                    
                    while ($row = mysql_fetch_array($rslote)) {
                        $result = $row['id_regiao'];
                        $id_projeto = $row['id_projeto'];
                        $curso = $row['id_curso'];
                        //PEGA A CURSO DO PERIODO
                        $sql_transf = "
                        SELECT A.id_curso_para, A.id_curso_de, B.nome
                        FROM rh_transferencias A, curso B
                        WHERE A.id_clt = $row[id_clt]
                        ORDER BY A.data_proc ASC LIMIT 1";
                        $sql_transf = mysql_fetch_assoc(mysql_query($sql_transf));
                        if(!empty($sql_transf['id_curso_de'])){
                            $curso = $sql_transf['id_curso_de'];
                        }
                        
                        $dataNova = date('d/m/Y', strtotime($row['data_entrada']));
                        $qrlote2 = "select a.*,b.cod from curso AS a
                            INNER JOIN rh_cbo AS b ON a.cbo_codigo = b.id_cbo
                            where id_curso = '$curso'";
                        $rslote2 = mysql_query($qrlote2);
                        $row2 = mysql_fetch_array($rslote2);
                        
                        $sql = "Select * from  rhempresa where id_regiao = {$result} and id_projeto = {$id_projeto}";
                        $row_master1 = mysql_query($sql);
                        $row3 = mysql_fetch_array($row_master1);
                        
                        $id_curso = $row2['id_curso'];
                        
                        $qrsalario = "SELECT * FROM rh_salario WHERE id_curso = '$id_curso' ORDER BY data DESC, id_salario DESC limit 1";
                        
                        $rssalario = mysql_query($qrsalario);
                        $salarioAntigo = mysql_fetch_array($rssalario);
                        $totalHistorico = mysql_num_rows($rssalario);

                        if ((float)$salarioAntigo['salario_novo'] > (float)$salarioAntigo['salario_antigo']) {
                            $salario1 = $salarioAntigo['salario_novo'];
                        }
                        else {
                            $salario1 = $salarioAntigo['salario_antigo'];
                        }
                        if($totalHistorico == 0){
                            $salario1 = $row2['salario'];
                        }
                        
                        $cursoNome = mysql_fetch_assoc(mysql_query("SELECT B.* FROM rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso WHERE id_clt = {$row['id_clt']}"),0);
                        ?>

                        <div class="etiquetaPrincipal">
                            <div>
                                <p align="center"><B>CONTRATO DE TRABALHO</B></p>
                            </div>
                            <div class="divDados">
                                <b>Empregador:</b> <?php echo $row3['razao']; ?> <b>CNPJ/MF:</b> <?= $row3['cnpj']; ?><br/>
                                <b>Rua:</b> <?=$row3['endereco']?><br/><br/>
                                <b>Nome:</b> <?= $row['nome']; ?> <b>Cargo:</b> <?= $cursoNome['nome']; ?><br/>
                                <b>CBO n�:</b> <?= $cursoNome['cbo_codigo']; ?> <b>Data admiss�o:</b> <?= $dataNova; ?> <b>Registro n�:</b> <?= $row['matricula']; ?><br/>
                                <b>Remunera��o especifica: </b>R$ <?= $cursoNome['salario']; ?> (<?php echo valor_extenso(number_format($row2['salario'], 2, ',', '')); ?>), por m�s<br/><br/>
                                <!--<b>Remunera��o especifica: </b>R$ <?= $salario1; ?> (<?php echo valor_extenso(number_format($salario1, 2, ',', '')); ?>), por m�s<br/><br/>-->
                            </div>
                            <div class="divAssinatura">
                                _______________________________________<br/>
                                <span>Ass. do empregador ou rogo c/ test.</span>
                            </div>
                        </div>
                        <div class="etiquetaPrincipal2">
                            <div>
                                <p align="center"><B>CONTRATO DE EXPERI�NCIA</B></p>
                            </div>
                            <div class="divDados">
                                <p>Nome <?= $row['nome']; ?>, matr�cula n� <?= $row['matricula']; ?>, admitido em <?= $dataNova; ?> por instrumento escrito pelo prazo de 45 (quarenta e cinco) dias, a t�tulo de experi�ncia, podendo este ser prorrogado na forma da lei, assim querendo as partes.</p><br/><br/>

                                RJ, ______/______ de _________<br/><br/><br/>
                            </div>
                            <div class="divAssinatura">
                                ______________________________________________<br/>

                            </div>
                        </div>
                    <?php
                    }
                
                if($cont == 4){
                    echo '<p style="page-break-before: always;">&nbsp;-</p>';
                    $cont=0;
                }
                    
                }
            }
            ?>
        </div>
        
    </BODY>
</HTML>
