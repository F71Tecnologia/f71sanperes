<?php
    if (empty($_COOKIE['logado'])) {
        print 'Efetue o Login<br><a href="login.php">Logar</a>';
        exit;
    }

    include('../conn.php');
    include_once 'functions_suporte.php';

    $id_user = $_COOKIE['logado'];
    $regiao = $_REQUEST['regiao'];

    if (empty($_REQUEST['tela'])) {
        $tela = '1';
    } else {
        $tela = $_REQUEST['tela'];
    }

    $id_user = isset($_COOKIE['logado']) ? $_COOKIE['logado'] : NULL;

    $sql = '';
?>
<html>
    <head>
        <title>:: Intranet :: SUPORTE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>

        <script src="../js/global.js" type="text/javascript"></script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                /*#headerPrint{display: none;}*/
            }
        </style>

        <script>
            $(function() {
//                $("#form1").validationEngine();
//
//                $("#projeto").change(function() {
//                    var $this = $(this);
//                    if ($this.val() != "-1") {
//                        showLoading($this, "../");
//                        $.post('finan_rh.php', {projeto: $this.val(), method: "loadbancos"}, function(data) {
//                            removeLoading();
//                            if (data.status == 1) {
//                                var opcao = "";
//                                var selected = "";
//                                for (var i in data.options) {
//                                    selected = "";
//                                    if (i == $("#bancSel").val()) {
//                                        selected = "selected=\"selected\" ";
//                                    }
//                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
//                                }
//                                $("#banco").html(opcao);
//                            }
//                        }, "json");
//                    }
//                }).trigger("change");
            });
        </script>
    </head>
    <body id="page-fin-rh" class="novaintra">
        <div id="content">
            <form action="" method="post" name="form1" id="form1">
                <div id="headerPrint">
                    <div id="head">
                        <img src="../imagens/logomaster6.gif" class="fleft" style="margin-right: 25px;" />
                        <div class="fleft">
                            <h2></h2>
                            <h2>SUPORTE</h2>
                        </div>
                    </div>
                    <br class="clear"/>
                </div>

                <input type="hidden" name="folhaR" id="folhaR" value="" />
                <input type="hidden" name="proadmR" id="proadmR" value="" />
                <input type="hidden" name="bancSel" id="bancSel" value="" />

                <fieldset>
                    <legend>Filtrar</legend>
                    <p>
                        <label class="first">Situa&ccedil;&atilde;o:</label> 
                        <select id='banco' name='banco'>
                            <option value="-1">AGUARDANDO ATENDIMENTO</option>
                            <option value="-1">FINALIZADO</option>
                        </select>
                    </p>
                    <p>
                        <label class="first">Status:</label>                        
                        <select >
                            <option value="" selected="selected" >TODOS</option>
                            <?php 
                                $chamados_status = get_chamados_status(); 
                                while($status = mysql_fetch_assoc($chamados_status)){
                            ?>
                            <option value="<?= $status['id_suporte_status']; ?>"><?= $status['nome_suporte_status']; ?></option>
                            <?php } ?>
                        </select>
                    </p> 
                    <p><label class="first">Abertura:</label> <select id='mes' name='mes' class='validate[custom[select]]'><option value="-1">� Selecione o m�s �</option><option value="1">Janeiro</option><option value="2">Fevereiro</option><option value="3">Mar�o</option><option value="4">Abril</option><option value="5">Maio</option><option value="6">Junho</option><option value="7">Julho</option><option value="8">Agosto</option><option value="9">Setembro</option><option value="10" selected="selected">Outubro</option><option value="11">Novembro</option><option value="12">Dezembro</option></select>  <select id='ano' name='ano' class='validate[custom[select]]'><option value="-1">� Selecione o ano �</option><option value="2009">2009</option><option value="2010">2010</option><option value="2011">2011</option><option value="2012">2012</option><option value="2013" selected="selected">2013</option><option value="2014">2014</option></select></p>
                    <p id="pterceiro" class="hidden"><label class="first">D�cimo Terceiro?</label> <label><input type="radio" name="terceiro" id="terceiroS" value="1" /> Sim </label> <label><input type="radio" name="terceiro" id="terceiroN" value="0" checked='checked'/> N�o </label> </p>
                    <p class="hidden"><label class="first">Retificadora:</label> <input type="hidden" name="reti" id="reti" value="0" /> Sim </p>

                    <p class="controls"> 
                        <input type="submit" class="button" value="Filtrar" name="filtrar" /> 
                    </p>
                </fieldset>
            </form>
            <?php                
                $funcionario = get_funcionario();
                var_dump($funcionario);
            ?>
            <table id="rhTable" border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
                <thead>
                    <tr>
                        <th>Abertura</th>
                        <th>Chamado</th>
                        <th>Assunto</th>
                        <th>&Uacute;ltimo movimento</th>
                        <th>Situa&ccedil;&atilde;o</th>
                        <th>Finalizar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $cont=0; while($chamado = mysql_fetch_assoc($chamados)){ 
//                        if ($chamado['exibicao'] == 3 or ($chamado['user_cad'] == $id_user or $chamado['user_res'] == $id_user)) {
                        ?>
                        <tr class="<?= (($cont%2)==0) ? ' odd ' : ' even'; ?>" >
                            <td><?= $chamado['data_cad'] ?></td>
                            <td><?= $chamado['id_suporte'] ?></td>
                            <td><a href="chamado.php?chamado=<?= $chamado['id_suporte'] ?>&regiao=<?= $regiao ?>"><?= $chamado['assunto'] ?></a></td>
                            <td><?= $chamado['ultima_alteracao'] ?></td>
                            <td  class="txcenter">'<img src="imgsuporte/<?= $chamado['icon_suporte_status'] ?>" alt="<?= $chamado['nome_suporte_status'] ?>" title="<?= $chamado['nome_suporte_status'] ?>" width="18" height="18">'</td>
                            <td  class="txcenter">
                                <a href="suporte.php?tela=3&chamado=<?= $chamado['id_suporte'] ?>&regiao=<?= $regiao ?>">
                                        <img src="imgsuporte/finalizar.png" alt="finalizar" border="0"/>
                                </a>
                            </td>
                        </tr>
                    <?php $cont++; } //} ?> 
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="txright">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </body>
</html>