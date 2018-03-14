<?php
include("../conn.php");
include("../wfunction.php");
include('../classes/global.php');

$result = mysql_query("SELECT id_funcionario, nome, nome1, funcao FROM funcionario WHERE status_reg = 0 ORDER BY nome ASC");

$html = "<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
    <link href=\"../net1.css\" rel=\"stylesheet\" type=\"text/css\" />
    <link href=\"../css/cupertino/jquery-ui-1.9.2.custom.css\" rel=\"stylesheet\" type=\"text/css\" />
    <link href=\"../favicon.ico\" rel=\"shortcut icon\" />
    <script src=\"../js/jquery-1.8.3.min.js\" type=\"text/javascript\"></script>
    <script src=\"../js/jquery-ui-1.9.2.custom.min.js\" type=\"text/javascript\"></script>
    <script src=\"../js/global.js\" type=\"text/javascript\"></script>
    <script>
        $(function(){
            $('.bt-image').on('click', function(){
                 var action = $(this).data('type');
                 var key = $(this).data('key');
                 var emp = $(this).parents('tr').find('td:first').next().html();
                 
                if (action === 'logs') {
                    $('#funcionario').val(key);
                    $('#form1').attr('action','ver_logs.php');
                    $('#form1').submit();        
                 }else if (action === 'ativar') {
                    var confirma=confirm('Deseja ativar este usuário?');
                    if (confirma===true) {
                        thickBoxIframe(emp,'ativar_usuario.php', {funcionario: key},'300-not', '180');
                        $('#'+key).remove();
                    } else {
                      return false;
                    }
                }
            });
        });
    </script>
</head>
<body class='novaintra'>
  <div id='content' style='margin: auto;'>
  <form action='' method='post' name='form1' id='form1' enctype='multipart/form-data' >
  <div class='fleft'>
              <h2>Sistema - Gestor de Funcionários</h2>
              <p>Funcionários Inativos</p>
          </div>
          <p><input type='hidden' name='funcionario' id='funcionario' value='' /></p>
      <table cellpadding='0' cellspacing='0' border='0' class='grid' width='100%'>
          <thead>
              <tr>
                  <th>COD.</th>
                  <th>NOME</th>
                  <th>NOME NO SISTEMA</th>
                  <th>FUNÇÃO</th>
                  <th>LOGS</th>
                  <th>ATIVAR USUÁRIO</th>
              </tr>
          </thead>
          <tbody>";
     
              $cnt = 0;
              while ($row = mysql_fetch_assoc($result)) {
                  $class = ($cnt++ % 2 == 0) ? "odd" : "even";
                 $html .= "<tr class='{$class}' id='{$row['id_funcionario']}'>
                              <td>".str_pad($row['id_funcionario'],3,"0",STR_PAD_LEFT)."</td>
                              <td>".acentoMaiusculo($row['nome'])."</td>
                              <td>".acentoMaiusculo($row['nome1'])."</td>
                              <td>".acentoMaiusculo($row['funcao'])."</td>
                              <td class='center'><img src='../imagens/icones/icon-docview.gif' title='Ver Logs' class='bt-image' data-type='logs' data-key='{$row['id_funcionario']}' /></td>
                              <td class='center'><img src='../imagens/icones/icon-accept.gif' title='Ativar Usuário' class='bt-image' data-type='ativar' data-key='{$row['id_funcionario']}' /></td>
                            </tr>";
              }
     
    $html .= "</tbody>
    </table>
    <p class='controls'> 
        <input type='button' name='voltar' id='voltar' value='Voltar' onclick='window.history.go(-1)'/> 
    </p>
    </form>      
</div>
</body>
</html>";
echo $html;
    
    



?>