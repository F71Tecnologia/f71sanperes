<?php
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

$meses_pt = array('Erro', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

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
        $mes = "Março";
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

function valor_extenso($valor = 0, $maiusculas = false) {
    // verifica se tem virgula decimal
    if (strpos($valor, ",") > 0) {
        // retira o ponto de milhar, se tiver
        $valor = str_replace(".", "", $valor);

        // troca a virgula decimal por ponto decimal
        $valor = str_replace(",", ".", $valor);
    }
    $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
    $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões",
        "quatrilhões");

    $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
        "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
    $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
        "sessenta", "setenta", "oitenta", "noventa");
    $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
        "dezesseis", "dezesete", "dezoito", "dezenove");
    $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
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

<!DOCTYPE html>
    <html lang="pt">
        <head>
            <title>:: Intranet :: Ficha Cadastral de Estabelecimento de Saúde</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link rel="shortcut icon" href="../favicon.ico">
            <style>
                * { margin: 0; padding: 0; }
            </style>
            <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
            <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
            <link href="../resources/css/style-print.css" rel="stylesheet">
            <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
            <script src="../resources/js/print.js" type="text/javascript"></script>
            <style>
                .text-cont{ 
                    font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif!important;
                    color: #000000!important; 
                    font-size: 10pt!important; 
                    text-align: justify!important;
                    text-justify: inter-word!important;
                }
                .titulo_documento { text-align: center!important; font-weight: bold!important;}

                .pagina{
                    font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif!important;
                    color: #000000!important; 
                    font-size: 10pt!important; 
                    text-align: justify!important;
                    text-justify: inter-word!important;
                
                }
                     @media print {
                        [class*="col-md-"] {
                          float: left;
                        }
                      }
            </style>
        </head>
        <!--body onload="print();"-->
        <body>
            
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <!--div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div-->
                    <!--div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3"-->
                        <div class="text-center"> 
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                            <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                        </div>
                    <!--/div-->
                </div>
            </nav>
            <!--WHILE -->
            <?php
            if (!empty($_POST['check_list'])) {
                $cont = 0;
                foreach ($_POST['check_list'] as $check) {
                    $cont++;

                    $qrlote = "select * from rh_clt where id_clt = $check";
                    $rslote = mysql_query($qrlote);
                    
                    while ($row = mysql_fetch_array($rslote)) {
                        $result = $row['id_regiao'];
                        $curso = $row['id_curso'];
                        //PEGA A CURSO DO PERIODO
                        if(isset($_REQUEST['gerar_lote_contratual'])){ 
                            $sql_transf = "
                            SELECT A.id_curso_para, A.id_curso_de, B.nome
                            FROM rh_transferencias A, curso B
                            WHERE A.id_clt = $row[id_clt]
                            ORDER BY A.data_proc ASC LIMIT 1";
                            $sql_transf = mysql_fetch_assoc(mysql_query($sql_transf));
                            if(!empty($sql_transf['id_curso_de'])){
                                $curso = $sql_transf['id_curso_de'];
                            }
                        }
                        
                        $dataNova = date('d/m/Y', strtotime($row['data_entrada']));
                        $qrlote2 = "select * from curso where id_curso = '$curso'";
                        echo "<!-- $qrlote2 -->";
                        $rslote2 = mysql_query($qrlote2);
                        $row2 = mysql_fetch_array($rslote2);
                        
                        $row_master1 = mysql_query("Select * from  rhempresa where id_regiao = '$result'");
                        $row3 = mysql_fetch_array($row_master1);
                        
                        $id_curso = $row2['id_curso'];
                        
                        $qrsalario = "SELECT * FROM rh_salario WHERE id_curso = '$id_curso' ORDER BY data DESC, id_salario DESC limit 1";
                        $rssalario = mysql_query($qrsalario);
                        $salarioAntigo = mysql_fetch_array($rssalario);
                        $totalHistorico = mysql_num_rows($rssalario);

                        /*
                         * POR CONTA DESSA CONDIÇÃO O SALÁRIO
                         * ESTAVA VINDO O ANTIGO
                         */
//                        if ($salarioAntigo['salario_antigo'] == '0' or $salarioAntigo['salario_antigo'] == '1') {
//                            $salario1 = $salarioAntigo['salario_novo'];
//                        }
//                        else {
//                            $salario1 = $salarioAntigo['salario_antigo'];
//                        }
                        if ($salarioAntigo['salario_novo'] == '0' or $salarioAntigo['salario_novo'] == '1') {
                            $salario1 = $salarioAntigo['salario_antigo'];
                        }
                        else {
                            $salario1 = $salarioAntigo['salario_novo'];
                        }
                        
                        if($totalHistorico == 0){
                            $salario1 = $row2['salario'];
                        }
                        ?>
                        <script type="text/javascript">
                             var c=document.getElementById("UgCanvas");
                             var ctx=c.getContext("2d");
                             ctx.fillStyle=""rgba(0, 0, 200, 0.5)";";
                             ctx.fillRect(0,0,150,100);
                         </script>
                         <style>
                             fieldset.field {
                                border: 1px groove #ddd !important;
                                padding: 0 1.4em 1.4em 1.4em !important;
                                margin: 0 0 5px 0 !important;
                                -webkit-box-shadow:  0px 0px 0px 0px #000;
                                        box-shadow:  0px 0px 0px 0px #000;
                                        width:100%;
                            }

                            
                         </style>
                         
                        <div class="pagina">
                            <fieldset class="field">
                                <div class ="row">
                                    <h5><strong>1.DADOS OPERACIONAIS</strong></h5>                                
                                    <div class="col-md-2">
                                        <strong>IDENTIFICAÇÃO:</strong><br>
                                    </div>
                                </div>
                                <div class ="row">
                                    <!--div class="col-md-2">  
                                        <strong></strong><br>
                                        INCLUSÃO<br><br>
                                        ALTERAÇÂO<br><br>
                                        DESLIGAMENTO 
                                    </div-->
                                    <div class="col-md-5">
                                        <strong>Data (dia/mês/ano)</strong><br>
                                        <table>
                                            <tr>
                                                <td>INCLUSÃO</td>
                                                <td><canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>____/___/__________</td>
                                            </tr>
                                            <tr>
                                                <td>ALTERAÇÂO</td>
                                                <td><canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>____/___/__________</td>
                                            </tr>
                                            <tr>
                                                <td>DESLIGAMENTO</td>
                                                <td><canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>____/___/__________</td>
                                            </tr>
                                        </table>
                                        </div>
                                    <div class="col-md-7">
                                        <strong>Motivos de Desligamento:</strong><br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>&nbsp;&nbsp;Aposentadoria<br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>&nbsp;&nbsp;Demissão<br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>&nbsp;&nbsp;Licença/Afastamento por + 60 dias<br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>&nbsp;&nbsp;Término de Contrato<br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>Transferência para outro Estabelecimento do Município <br>
                                         <canvas id="UgCanvas" width="15" height="15" style="border:1px solid; margin-top:5px;"></canvas>&nbsp;Outros:________________________________________
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="field">
                                <div class ="row">
                                    <h5><strong>2.DADOS DO PROFISSIONAL</strong></h5> 
                                    <strong>A) Dados de Identificação</strong><br>
                                    <div class="col-md-4">
                                        <p>                                        
                                            <small><i>Nome do Profissional</i></small><br>
                                            <strong><?=$row['nome'];?></strong>

                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <p>
                                            <small><i>PIS/PASEP</i></small><br>
                                            <strong><?=$row['pis'];?></strong>
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p>
                                            <small><i>CPF</i></small><br>
                                            <strong><?=$row['cpf'];?></strong>
                                        </p>
                                    </div>
                                </div>
                                <div class ="row">
                                    <div class="col-md-6">
                                        <small></i>Nome da Mãe</i></small><br>
                                        <strong><?=$row['mae'];?></strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small></i>Nome da Pai</i></small><br>
                                        <strong><?=$row['pai'];?></strong>
                                    </div>
                                </div>
                                <div class ="row">
                                    <div class="col-md-2">
                                        <small></i>Data Nascimento</i></small><br>
                                        <strong><?=date("d/m/Y",strtotime($row['data_nasci']));?></strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small></i>Município</i></small><br>
                                        <strong><?=$row['municipio_nasc'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>UF</i></small><br>
                                        <strong><?=$row['uf'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Sexo</i></small><br>
                                        <strong><?=$row['sexo'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Raça</i></small><br>
                                        <strong><?php $etnia = $row['etnia']; 
                                        $etni = mysql_query("SELECT nome FROM etnias WHERE id=$etnia"); 
                                        $rowetni = mysql_fetch_array($etni);
                                        echo $rowetni['nome'];
                                        ?></strong>
                                    </div>
                                </div>
                                <div class ="row">
                                    <div class="col-md-2">
                                        <small></i>Nº Identidade</i></small><br>
                                        <strong><?=$row['rg'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>UF</i></small><br>
                                        <strong><?=$row['uf_rg'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Data Emissão</i></small><br>
                                        <strong><?=date("d/m/Y",strtotime($row['data_rg']));?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Nº Titulo Eleitor</i></small><br>
                                        <strong><?=$row['titulo'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Zona</i></small><br>
                                        <strong><?=$row['zona'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Seção</i></small><br>
                                        <strong><?=$row['secao'];?></strong>
                                    </div>
                                </div>
                                <div class ="row">
                                    <div class="col-md-2">
                                        <small></i>Nacionalidade</i></small><br>
                                        <strong><?=$row['nacionalidade'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>País de Origem</i></small><br>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Data Entrada</i></small><br>
                                        
                                    </div>
                                    <div class="col-md-3    ">
                                        <small></i>Data Naturalização</i></small><br>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Nº Portaria</i></small><br>
                                        
                                    </div>
                                </div>
                                <div class ="row">
                                    <div class="col-md-2">
                                        <small></i>Nº CTPS</i></small><br>
                                        <strong><?=$row['campo1'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Série</i></small><br>
                                        <strong><?=$row['serie_ctps'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>UF</i></small><br>
                                        <strong><?=$row['uf_ctps'];?></strong>
                                    </div>
                                    <div class="col-md-2">
                                        <small></i>Data Emissão</i></small><br>
                                        <strong><?=date("d/m/Y",strtotime($row['data_ctps']));?></strong>
                                    </div>
                                    <div class="col-md-4">
                                        <small></i>Escolaridade</i></small><br>
                                        <strong><?php
                                            $escolar =$row['escolaridade']; $query_escolar = mysql_query("SELECT * FROM escolaridade WHERE id =$escolar");
                                            $row_escolar = mysql_fetch_assoc($query_escolar); echo $row_escolar['nome'];
                                        ?></strong>
                                    </div>
                                </div>
                                <div class ="row"> 
                                    <strong>B) Dados Residenciais</strong><br>
                                    <div class="col-md-4">                                       
                                        <small><i>Nome do Profissional</i></small><br>
                                        <strong><?=$row['endereco'];?></strong>
                                    </div>
                                    <div class="col-md-2">                                       
                                        <small><i>Nº</i></small><br>
                                        <strong><?=$row['numero'];?></strong>
                                    </div>
                                    <div class="col-md-2">                                       
                                        <small><i>Complemento</i></small><br>
                                        <strong><?=$row['complemento'];?></strong>
                                    </div>
                                    <div class="col-md-4">                                       
                                        <small><i>Bairro</i></small><br>
                                        <strong><?=$row['bairro'];?></strong>
                                    </div>
                                </div>
                                <div class ="row"> 
                                    <div class="col-md-4">                                       
                                        <small><i>Município</i></small><br>
                                        <strong><?=$row['cidade'];?></strong>
                                    </div>
                                    <div class="col-md-2">                                       
                                        <small><i>UF</i></small><br>
                                        <strong><?=$row['uf'];?></strong>
                                    </div>
                                    <div class="col-md-2">                                       
                                        <small><i>CEP</i></small><br>
                                        <strong><?=$row['cep'];?></strong>
                                    </div>
                                    <div class="col-md-4">                                       
                                        <small><i>Telefone</i></small><br>
                                        <strong><?=$row['tel_cel'];?></strong>
                                    </div>
                                </div>
                                <div class ="row"> 
                                    <strong>C) Dados de Vínculo e Ocupação de Trabalho</strong><br>
                                    <div class="col-md-4"></div> 
                                    <div class="col-md-4">                                       
                                        <strong><i>Carga Horária Semanal                                        
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">                                              
                                            <tr>
                                                <th></th> 
                                                <th><small><i>CBO/Especialidade</i><small></th> 
                                                <th><small><i>Ambulatorial</i><small></th> 
                                                <th><small><i>Outros </i><small></th> 
                                                <th><small><i>Registro no Conselho de Classe</i><small></th> 
                                                <th><small><i>Órgão Emissor/UF</i><small></th> 
                                                <th><small><i>Empregatício</i><small></th>                                                
                                            </tr> 
                                             
                                            <tbody> 
                                                <tr> 
                                                    <th>[&nbsp;&nbsp;&nbsp;]</th> 
                                                    <td>						</td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td>
                                                    <td></td>
                                                </tr> 
                                                <tr> 
                                                    <th>[&nbsp;&nbsp;&nbsp;]</th> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td>   
                                                    <td></td>
                                                    <td></td>
                                                </tr>                                             
                                            </tbody> 
                                            <small><i>*Preencha os dados da tabela abaixo</i></<strong>
                                        </table>  
                                    </div>
                                </div>
                                <div class ="row">
                                        Tipo de vinculo empregatício:<br>
                                        [1] Estatutário: Profissional da Prefeitura de São Paulo<br>
                                        [2] Emprego Público: Profissional do Estado de São Paulo ou Municipalizado<br>
                                        [3] Contrato Prazo Determinado: Profissional Contratado de Emergência<br>
                                        [4] Autônomo: Profissional das OS's e Instituições Parceiras<br>
                                        [5] Bolsa: Profissionais "residentes"

                                </div><br>
                                <div class ="row">                                 
                                    <div class="col-md-8">                                       
                                        <strong><i>Assinatura e Carimbo do Diretor da Unidade</i></strong><br><br>
                                        ________________________________________________________________________
                                    </div>
                                    <div class="col-md-4">                                       
                                        <strong><i>Data</i></strong><br><br>
                                        _____/_____/____________
                                    </div>                               
                                </div>                            
                            </fieldset>                               
                        </div>
                   
                   
            <?php
                    }                
                    if($cont==8) echo '<div class="page-break"></div>';                    
                }
            }
            ?>
            <!--FIM WHILE -->
                
                
       </body>
    </html>

