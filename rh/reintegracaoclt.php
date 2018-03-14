<?php
    if (empty($_COOKIE['logado'])) {
        print 'Efetue o Login<br><a href="../login.php">Logar</a>';
        exit;
    }

    function removeAspas($str) {
        $str = str_replace("'", "", $str);
        return str_replace('"', '', $str);
    }

    include('../conn.php');
    include('../classes/regiao.php');
    include('../wfunction.php');
    include('../classes/SetorClass.php');
    include('../classes/PlanoSaudeClass.php');
    include_once("../classes/LogClass.php");
    $log = new Log();    
    $id_clt = $_GET['id'];
    $query = mysql_query("SELECT * FROM rh_clt WHERE id_clt = $id_clt");
    $row = mysql_fetch_assoc($query);
    $id_projeto = $row['id_projeto'];
    $proj = mysql_query("SELECT * FROM projeto WHERE id_projeto = $id_projeto");
    $rowproj = mysql_fetch_assoc($proj);
    $sql_user2 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[useralter]'");
    $row_user2 = mysql_fetch_array($sql_user2);
    $sql_user3 = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$row[sis_user]'");
    $row_user3 = mysql_fetch_array($sql_user3);
    $id_disp = $row['status'];
    $sql_disp = mysql_query("SELECT * FROM rhstatus WHERE codigo = $id_disp");
    $row_disp = mysql_fetch_array($sql_disp);
    
    if($_REQUEST['Submit']){
        $data_reintegracao = $_POST['data_reint'];
        $data_reintegracao = explode("/", $data_reintegracao);
        $data_reint = $data_reintegracao[2].'-'.$data_reintegracao[1].'-'.$data_reintegracao[0];
        $recisao = mysql_query("SELECT * FROM rh_recisao WHERE id_clt = $id_clt");
        if($row['status'] >= 60 && $row['status'] <= 69){
            while($rowrecisao = mysql_fetch_assoc($recisao)){
                if($rowrecisao['status'] == 0){
                    mysql_query("DELETE FROM rh_recisao WHERE id_clt = $id_clt AND status = 0");
                }            
            }
            mysql_query("UPDATE rh_recisao SET status = 0 WHERE id_clt = $id_clt AND status = 1");
            mysql_query("UPDATE rh_clt SET data_demi='0000-00-00', data_saida='0000-00-00', data_reintegracao='$data_reint', status = 10, reintegracao = 1 WHERE id_clt = $id_clt");
            echo  "<script>alert('CLT: ".$row['nome']." foi REINTEGRADA');</script>";
        }else{
            echo  "<script>alert('CLT: ".$row['nome']." EXISTENTE');</script>";
        }
    }

?>

 <html>
        <head>
            <title>:: Intranet ::</title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
            <link rel="shortcut icon" href="../favicon.ico">

            <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
            <script type="text/javascript" src="consulta.js"></script>
            <script src="../js/ramon.js" type="text/javascript" language="javascript"></script>
            <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="../js/jquery-1.8.3.min.js"></script>
            <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
            <!--<script type="text/javascript" src="../js/jquery.ui.datepicker-pt-BR.js"></script>-->
            <script type="text/javascript" src="../jquery/priceFormat.js"></script>
            <script type="text/javascript" src="../js/valida_documento.js"></script>
            <script type="text/javascript" src="../js/jquery.maskedinput.min.js"></script>
            <script type="text/javascript" src="../js/jquery.maskMoney_3.0.2.js"></script>
            <script src="../js/jquery.validationEngine-2.6.js"></script>
            <script src="../js/jquery.validationEngine-pt.js"></script>
            <script src="../js/global.js" type="text/javascript"></script>

            <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
            <!--<link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">-->
            <!--<link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">-->
            <!--<link href="../resources/css/add-ons.min.css" rel="stylesheet" media="screen">-->
            <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
            <link href="../resources/css/main.css" rel="stylesheet" media="screen">
            <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
            <!--<link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">-->
            <script src="../resources/js/bootstrap.min.js"></script>
            <script src="../resources/js/bootstrap-dialog.min.js"></script>
            <script src="../resources/js/main.js"></script>
            <script src="../resources/js/moment.js"></script>

            <link href="css/estrutura_cadastro.css" rel="stylesheet" type="text/css">
            <script type="text/javascript">
                
    
                jQuery.fn.brTelMask = function () {

                    return this.each(function () {
                        var el = this;
                        $(el).focus(function () {
                            $(el).mask("(99) 9999-9999?9", {placeholder: " "});
                        });

                        $(el).focusout(function () {
                            var phone, element;
                            element = $(el);
                            element.unmask();
                            phone = element.val().replace(/\D/g, '');
                            if (phone.length > 10) {
                                element.mask("(99) 99999-999?9");
                            } else {
                                element.mask("(99) 9999-9999?9");
                            }
                        });
                    });
                };
                
                $(document).ready(function () {
                    $('#data_reint').datepicker({
                        changeMonth: true,
                        changeYear: true,
                        yearRange: "1950:<?php echo date('Y') + 6 ?>",
                        maxDate: '+6y',
                        dateFormat: 'dd/mm/yy'
                    });
                });


               

               
                        
               
               
                
                
            </script>
            <style>
                #participanteDesativado{
                    display: none;
                    margin: 20px 0px 20px 0px;
                    background-color: #FFCCD6;
                    border: 1px solid #F00;
                    padding: 4px;
                    font-size: 14px;
                    text-align: center;
                }
                
                .show {
                        display:block;
                    }

                    .hide {
                        display: none;
                    }
                <?php if($id_regiao == 1) { ?> 
                select[readonly] {
                    background: #eee; /*Simular campo inativo - Sugestão @GabrielRodrigues*/
                    pointer-events: none;
                    touch-action: none;
                  }
                  
                  select[disabled] {
                    background: #eee; /*Simular campo inativo - Sugestão @GabrielRodrigues*/
                    pointer-events: none;
                    touch-action: none;
                  }
                
                input.porc1[readonly] {
                    background: #eee; 
                    pointer-events: none;
                    touch-action: none;
                  }
                  
                 input.porc2[readonly] {
                    background: #eee; 
                    pointer-events: none;
                    touch-action: none;
                  }
                  
                  input#quantidade_horas {
                        width: 128px;
                    }
                <?php }?> 
            </style>
        </head>
        <body>
            <div id="corpo">
                <table align="center" width="100%" cellspacing="0" cellpadding="12" style="font-size:13px; line-height:22px;">
                    <tr>
                        <td>	<span style="float:right"><?php include('../reportar_erro.php'); ?></span>
                            <span style="clear:right"></span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div style="border-bottom:2px solid #F3F3F3; margin-top:10px;">
                                <h2 style="float:left; font-size:18px;">
                                    REINTEGRAR <span class="clt">CLT</span>
                                </h2>
                                <p style="float:right;">
                                    <?php if ($_GET['pagina'] == 'clt') { ?>
                                        <a href="clt.php?regiao=<?= $id_regiao ?>">&laquo; Voltar</a>
                                    <?php } else { ?>
                                        <a href="ver.php">&laquo; Voltar</a>
                                    <?php } ?>
                                </p>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <table cellpadding="0" cellspacing="1" class="secao">
                    <tbody>
                        <tr>
                            <td width="50%" bgcolor="#F3F3F3" valign="top">
                                <b>Nº Matricula:</b> <?=$row['matricula']?><br>
                                <b> <?=$row['nome']?></b><br>
                                <b>CPF:</b> <?=$row['cpf']?><br>
                                <b>Data de Entrada:</b> <?=date("d/m/Y",strtotime($row['data_entrada']))?><br>
                                <font color="red"><b>Data de saída:</b> <?=date("d/m/Y",strtotime($row['data_saida']))?></font><br>
                                <b>Projeto:</b> <?=$row['id_projeto']?> - <?=$rowproj['nome']?><br>
                                <div style="color:#F00; font-size:14px;"><?= $row_disp['especifica']?></div>                            <br>
                                <?php
                                $data_cad = $row['data_cad'];
                                $data_import = $row['data_importacao'];
                                $ultim_atualizacao = explode(" ", $row['data_ultima_atualizacao']);
                                if ($data_cad == "0000-00-00" or $data_import != null) {
                                    $cadastrado_import = "Importado <b>";
                                    $data = implode("/", array_reverse(explode("-", $data_import)));
                                } else {
                                    $cadastrado_import = "Cadastrado por <b>";
                                    $data = implode("/", array_reverse(explode("-", $row['data_cad'])));
                                }

                                if ($row['hora_cad'] != null) {
                                    $hora_cadastrada = "e horário '. {$row['hora_cad']}";
                                }
                                ?>
                                <i><?php echo $cadastrado_import . " " . $row_user3['nome'] . '</b> na data ' . $data . '</b> ' . $hora_cadastrada; ?></i>
                                <br><i><?php echo 'Ultima Alteração feita por <b>' . $row_user2['nome'] . '<br></b> na data ' . $row['dataalter2'] . '</b> e horário ' . $ultim_atualizacao[1] ?></i>
                           </td>
                           <td>
                               <form method="post" action="">
                                   *Favor insira a data de REINTEGRAÇÃO<br><br>
                                   Data: <input name="data_reint" type="text" id="data_reint" size="15" maxlength="10" value=""  onkeyup="mascara_data(this);">
                               
                           </td>
                        </tr>
                        
                        
                    </tbody>
                </table>                            
                <div id="observacao">NÃO DEIXE DE CONFERIR OS DADOS APÓS A DIGITAÇÃO</div>
                <div align="center">
                    <input type="submit" name="Submit" id="Submit" value="CADASTRAR" class="botao">
                    </form>
                </div>
            </div>
        </body>
 </html>