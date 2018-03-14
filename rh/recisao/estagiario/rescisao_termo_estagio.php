<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../../../conn.php');
include('../../../funcoes.php');

$id_estagiario = $_REQUEST['id_estagiario'];
$id_estagiario = 11;
$opt_motivo = $_REQUEST['opt_motivo'];

$qr_estagiario = mysql_query("SELECT * FROM estagiario WHERE id_estagiario = '$id_estagiario'");
$estagiario = mysql_fetch_array($qr_clt);

$qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$estagiario[id_projeto]'");
$row_pro = mysql_fetch_array($qr_projeto);

$qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$estagiario[id_regiao]'");
$row_reg = mysql_fetch_array($qr_regiao);

$qr_empresa = mysql_query("SELECT * FROM rhempresa WHERE id_regiao = '$regiao' AND id_projeto = '$row[id_projeto]'");
$row_empresa = mysql_fetch_assoc($qr_empresa);

$dia = date('d');
$mes = (int) date('m');
$ano = date('Y');

$meses = array('-', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');

$nomeMes = $meses[$mes];
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <title>:: Intranet :: Relatório de Termo de Vale Transporte em Lote </title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="../favicon.ico">
        <link href="../../../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../../../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../../../resources/css/style-print.css" rel="stylesheet">
        <script src="../../../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../../../resources/js/print.js" type="text/javascript"></script>
        <style>
            .table-not-borded {
                border-collapse: collapse;
            }
            .table-not-borded, .table-not-borded th, .table-not-borded td {
                border: 0;
            }
            /*            body{
                            margin: 0;
                            font-size: 14px;
                            font-family: Arial, Helvetica, sans-serif;
                        }
                        ol{
                            padding: 0;
                        }
                        table{
                            width: 100%;
                        }
                        .pagina{
                            width: 210mm;
                            height: 297mm;
            
                            page-break-after: always;
                        }
                        .table-borded {
                            border-collapse: collapse;
                        }
                        .table-borded, .table-borded th, .table-borded td {
                            border: 1px solid black;
                        }
                        .text-justify{
                            text-align: justify;
                        }
                        .text-left{
                            text-align: left;
                        }
                        .text-rigth{
                            text-align: right;
                        }
                        .text-center{
                            text-align: center;
                        }
                        .logo{
                            display: block;
                            margin: auto;
                            height: 1.5cm;
                        }
                        .padding{
                            display: inline-block;
                            padding: 0 5px;
                        }
                        h1,h2,h3,h4,h5,h6{text-align: center;}
                        h1{font-size: 1.5em !important;}
                        h2{font-size: 1.4em !important;}
                        h3{font-size: 1.3em !important;}
                        h4{font-size: 1.2em !important;}
                        h5{font-size: 1.1em !important;}
                        h6{font-size: 1em !important;}
                        
                        @media screen {
                            body{
                                background-color: #555;
                                margin-top: 80px;
                            }
                            .pagina{
                                background-color: #fff;
                                margin: 50px auto;
                                padding: 2cm;
                                box-shadow: 0 0 10px #000;
                                -moz-shadow: 0 0 10px #000;
                                -webkit-box-shadow: 0 0 10px #000;
                            }
                        }
            
                        @media print{
                            .pagina{padding: 0;}
                            .no-print{
                                display: none;
                            }
                        }*/


        </style>
        <script>
//            $(document).ready(function () {
//                $("#imprimir").click(function () {
//                    window.print();
//                });
//                $("#voltar").click(function () {
//                    window.history.back();
//                });
//            });



        </script>
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn">Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <?php
                include('../../../empresa.php');
                $img = new empresa();
                $img->imagem();
                ?>
            </div>
            <h2 class="text-center">RESCISÃO DO TERMO DE COMPROMISSO DE ESTÁGIO</h2>

            <p class="text-justify">Pelo presente instrumento legal a empresa <?= $row_empresa['nome'] ?>, inscrita no CNPJ no.
                <?= $row_empresa['cnpj'] ?>, com sede à <?= $row_empresa['endereco'] ?>, em <?= $row_empresa['municipio'] ?>, doravante
                denominada CONCEDENTE, nos termos da Lei nº 11.788/2008, de 25/09/2008, neste ato
                representada por <?= $estagiario['supervisor'] ?> e o estagiário <?= $estagiario['nome'] ?>,
                portador(a) da cédula de identidade nº <?= $estagiario['rg'] ?>, aluno(a) do curso <?= $estagiario['curso'] ?>
                da <?= $estagiario['instituicao_ensino'] ?>, resolvem de comum
                acordo RESCINDIR, de fato e de direito, o Termo de Compromisso de Estágio firmado
                pelas partes em <?= $data_fim_br ?>, tornando-o, a partir desta data, sem nenhum efeito jurídico.</p>
            <br>
            <p><strong>Motivo da rescisão:</strong></p>
            <p><strong>ESTUDANTE:</strong></p>
            <p>( ) Trancou a matrícula</p>
            <p>( ) Mudou de curso</p>
            <p>( ) Transferiu-se para outra Instituição de Ensino</p>
            <p>( ) Recebeu outra proposta de estágio/emprego</p>
            <p>( ) Foi efetivado</p>
            <p>( ) Não iniciou o estágio</p>
            <p>( ) Formou-se</p>
            <p>( ) Não se adaptou às atividades propostas, por que? _____________________________</p>
            <p>( ) Outro motivo, especifique: _______________________________________________</p>
            <br>
            <p><strong>EMPRESA:</strong></p>
            <p>( ) Excesso de faltas no estágio</p>
            <p>( ) Redução de custos e/ou pessoal</p>
            <p>( ) Não atendeu às expectativas da empresa, por que? _____________________________</p>
            <p>( ) Outro motivo, especifique: _______________________________________________</p>
            <br>
            <br>
            <p><?= '&nbsp;&nbsp;&nbsp;&nbsp;' . $row_reg['regiao'] . ', ' . $dia . ' de ' . $nomeMes . ' de ' . $ano . '.' ?></p>

            <br>
            <br>
            <table class="table-not-borded">
                <tbody>
                    <tr>
                        <td class="text-center">
                            _______________________<br>
                            Empresa - Concedente</td>
                        <td class="text-center">
                            _______________________<br>
                            Estagiário</td>
                        <td class="text-center">
                            _______________________<br>
                            Instituição de Ensino
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

    </body>
</html>

