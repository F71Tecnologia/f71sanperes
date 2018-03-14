<?php
if (empty($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/BotoesClass.php");
include("../../classes_permissoes/acoes.class.php");
include("../../classes/EduEventosClass.php");
include("../../classes/FeriadosClass.php");

$eventosClass = new EduEventosClass();
$feriadosClass = new FeriadosClass();

//$objAcoes = new Acoes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
//CARREGANDO MENU DE ACORDO COM AS PERMISSOES DA PESSOA
$botoes = new BotoesClass($dadosHeader['defaultPath'], $dadosHeader['fullRootPath']);

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Calendário Escolar", "id_form" => "form1");
//$breadcrumb_pages = array("Gestão de RH"=>"../../", "Seguro Desemprego"=>"index2.php");

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Calendário Escolar</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" type="text/css">
        <link href='../../classes/responsive-calendar/0.9/css/responsive-calendar.css' rel='stylesheet'>
    </head>
    <body>
        <?php include_once("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> EDUCACIONAL - <small>Calendário Escolar</small></h2></div>
                    <div class='col-lg-8 col-md-8 col-sm-6 col-xs-12 text-center'>
                        <!-- Responsive calendar - START -->
                        <div class='responsive-calendar'>
                            <div class='controls '>
                                <a class='pull-left' data-go='prev'><div  id='voltar' class='btn btn-purple'>Voltar</div></a>
                                <h4><span data-head-year></span>&nbsp;<span data-head-month></span></h4>
                                <a class='pull-right' data-go='next'><div id='ir' class='btn btn-purple'>Próximo</div></a>
                                <hr/>
                                <div class='day-headers'>
                                    <div class='day header'>Seg</div>
                                    <div class='day header'>Ter</div>
                                    <div class='day header'>Qua</div>
                                    <div class='day header'>Qui</div>
                                    <div class='day header'>Sex</div>
                                    <div class='day header'>Sáb</div>
                                    <div class='day header'>Dom</div>
                                </div>
                                <div id='data' class='days data' data-group='days'>
                                </div>
                            </div> 
                        </div>
                        <!-- Responsive calendar - END -->
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <h4>Eventos
                            <a href="../evento/visualizar_eventos.php" class="btn btn-primary btn-xs pull-right" title="Visualizar Todos os Eventos"><span class="fa fa-eye"></span></a>
                        </h4>                    
                        <div class="list-group">
                            <?php
                            $row_eventos = $eventosClass->listEvento();
                            foreach ($row_eventos as $row) { ?>
                                <div class="list-group-item">
                                    <span class="pull-right fa fa-calendar-o"></span>
                                    <p class="list-group-item-text"><strong><?php echo $row['dataBr'] ?></strong> - <?php echo $row['nome'] ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <h4>Feriados</h4>
                        <div class="list-group">
                            <?php
                            $row_feriados = $feriadosClass->getFeriados();
                            foreach ($row_feriados as $row) { ?>
                                <div class="list-group-item">
                                    <span class="pull-right fa fa-calendar-o"></span>
                                    <p class="list-group-item-text"><strong><?php echo $row['dataBr'] ?></strong> - <?php echo $row['nome'] ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once ('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.maskedinput.min.js" type="text/javascript"></script>
        <script src='../../classes/responsive-calendar/0.9/js/responsive-calendar.js'></script>

        <script>
            $(function () {
                verifica();
                var timer;
                timer = setInterval(verifica(), 1000);
                var arr = new Array();


                $("#bug").click(function () {
                    pauseVid();
                });
                $('.responsive-calendar').responsiveCalendar({
                    time: "",
                    events: {
                    <?php
                    foreach ($row_eventos as $key => $value) {
                        echo "'{$value['data']}': {'number': " . count($key) . " },";
                    }
                    ?>
//                    "2017-04-26": {"number": 1, "badgeClass": "badge-warning"},
                    },
                    translateMonths: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                    onMonthChange: function () { //Quando o mês(botão de PREV for clidado) mudado.
                        var mescorrente = $(this)[0].currentMonth + 1; //Variavel reagata mes corrente do calendario soma mais um ao mes para dezembro ser 12 e não 11.
                        var anocorrente = $(this)[0].currentYear; //Variavel reagata ano corrente do calendario 
                        var dataCalendario = new Date(mescorrente + "/" + "01" + "/" + anocorrente); //Variavel onde cria uma nova data juntando mes do calendario, mais o primeiro dia e o ano do calendario.
                        var data = new Date(); //Variavel criando uma nova data
                        var newData = new Date(data.getTime() - 7948800000); //Variavel onde pega o tempo real( data e mes e ano) e diminui 3 meses em milisegundos

                        if (dataCalendario < newData) { //se a data do calendario for menor que a data real
                            $("#voltar").hide(); //esconde o botao de previous
                        } else { //senao
                            $("#voltar").show(); //volta com o botão
                        }
                    },
                    onActiveDayClick: function () { //Ao clicar nos dias abre o Popup com informações dos procesos.
                        var dia = $(this).data('day');
                        var mes = $(this).data('month');
                        var ano = $(this).data('year');
//                        alert(dia+"/"+mes+"/"+ano);

                        $.ajax({url: "/intranet/classes/responsive-calendar/0.9/example/popup_escola.php?dia=" + dia + "&mes=" + mes + "&ano=" + ano, success: function (result) {

                                new BootstrapDialog({
                                    nl2br: false,
                                    size: BootstrapDialog.SIZE_WIDE,
                                    type: 'type-success',
                                    title: 'EVENTOS',
                                    message: result,
                                    closable: true,
                                    buttons:
                                    [{
                                        label: 'Fechar',
                                        action: function (dialog) {
                                            dialog.close();
                                            //window.location.reload();
                                        }
                                    }]
                                }).open();
                            }});
                    }
                });
                $("body").on("click", "#realizado", function () { //Quando clicado em Realizar inicia está confirmação
//                    var id = $(this).data("realizado");
//                    //            return confirm("Definir como realizado?");
//                    bootConfirm("DESEJA DEFINIR COMO REALIZADO?", "VERIFICANDO", function (data) {
//                        if (data == true) {
//                            location.href = "/intranet/gestao_juridica/action.realizado.php?id=" + id;
//                        }
//
//                    }, "success");

                });

                $("body").on("change", "#mes, #ano", function () { //Setando o Mês e o Ano manualmente do calendario de processos
                    var mes = $("#mes").val();
                    var ano = $("#ano").val();

                    if (mes != "" && ano != "") {
                        $('.responsive-calendar').responsiveCalendar(ano + "-" + mes);
                    }
                });

            });

            function pauseVid() {
                var vid = document.getElementById("myVideo");
                vid.pause();
            }

            var verifica = function () {
                $.post('http://www.netsorrindo.com/intranet_2014_11_19/webmail/inc/boxcount.php', {email: $("#h-email").val()}, function (data) {
                    data = data[0];
                    $("#email-unread").html(data.unread);
                }, 'json');
            }
        </script>
    </body>
</html>