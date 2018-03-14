<?php

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/PrestadorServicoClass.php");
include('../../classes/global.php');

$path = '../../processo/prestador_documentos/';

//METODO PARA RETORNAR UM HTML COM A LISTA DOS DOCUMENTOS DO PRESTADOR DE SERVIÇO SOLICITADO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "getDocs") {   
    
    $qr = "SELECT A.prestador_tipo_doc_id, A.prestador_tipo_doc_nome, B.prestador_documento_id, COUNT(B.prestador_documento_id) as qnt FROM prestador_tipo_doc AS A
        LEFT JOIN prestador_documentos AS B ON (A.prestador_tipo_doc_id = B.prestador_tipo_doc_id AND B.id_prestador = {$_REQUEST['prestador']})
        GROUP BY A.prestador_tipo_doc_id
        ORDER BY A.ordem";
    $rs = mysql_query($qr);
    
    $html = "<script src=\"modal_mx.js\" type=\"text/javascript\"></script>";
    $html .= "<div id='modal_moldura'><div id='modal_geral'><div id='modal_lado1'>";
    
    $html .= "<table cellpadding='0' cellspacing='0' border='0' class='grid' width='100%'>";
    $html .= "<thead><tr><th>Documentos</th><th>Qntd</th><th>Ações</th></tr></thead><tbody>";
    $cnt = 0;
    while ($row = mysql_fetch_assoc($rs)) {
        $class = ($cnt++ % 2 == 0) ? "odd" : "even";

        $anexo = "-";
        $bt_ver = "<img src=\"../../img_menu_principal/anexar.png\" title=\"Anexar Documento\" class=\"bt-image modal-bt\" data-doc=\"{$row['prestador_tipo_doc_id']}\" data-prest=\"{$_REQUEST['prestador']}\" data-act='anexar'>";

        //VERIFICANDO SE EXISTE ARQUIVO PARA O TIPO
        if ($row['prestador_documento_id']) {
            //VERIFICANDO SE ESTÃO VENCIDOS
            $qr = montaQueryFirst("prestador_documentos", "COUNT(prestador_documento_id) AS total", "prestador_tipo_doc_id = {$row['prestador_tipo_doc_id']} AND id_prestador = {$_REQUEST['prestador']} AND data_vencimento > CURDATE()", "data_vencimento");
            $bt_ver = "<img src=\"../../imagens/icones/icon-docview.gif\" title=\"Documentos\" class=\"bt-image modal-bt\" data-doc=\"{$row['prestador_tipo_doc_id']}\" data-prest=\"{$_REQUEST['prestador']}\" data-act='ver' data-qtd='{$qr['total']}'>";

            //SE FOR ZERO, ESTÁ VENCIDO
            if ($qr['total'] == 0) {
                $class .= " back-red";
                $bt_ver = "<img src=\"../../imagens/icones/icon-error.gif\" title=\"Doc Vencido\" class=\"bt-image modal-bt\" data-doc=\"{$row['prestador_tipo_doc_id']}\" data-prest=\"{$_REQUEST['prestador']}\" data-act='ver'>";
            } else {
                $class .= " back-green";
            }
        }

        $html .= "<tr class='{$class}'><td>{$row['prestador_tipo_doc_nome']}</td><td class='center'>{$row['qnt']}</td><td class='center'>{$bt_ver}</td></tr>";
    }
    $html .= "</tbody></table></div>";

    $html .= "<div id='modal_lado2'><div></div>
        <p class='controls'>
            <input type='button' name='voltar' id='modal_voltar' value='Voltar' />
            <input type='button' name='novodoc' id='modal_novodoc' value='Novo Doc' />
        </p></div>";
    $html .= "</div></div>";
    echo utf8_encode($html);
    exit;
}

//CARREGA TELA 2
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "showDocs") {        

    $tpdoc = PrestadorServico::getTipoDoc($_REQUEST['id_doc']);
    $docs = PrestadorServico::listarDocs($_REQUEST['id_prestador'], $_REQUEST['id_doc']);

    $html = "<table cellpadding='0' cellspacing='0' border='0' class='grid' width='100%' id='tableDoc'>";
    $html .= "<h3>{$tpdoc['prestador_tipo_doc_nome']}</h3><thead><tr><th>Documento</th><th>Data</th><th colspan='2'>Ações</th></tr></thead><tbody>";
    $cnt = 0;

    foreach ($docs as $doc) {
        $editar = "<img src=\"../../imagens/icones/icon-edit.gif\" title=\"Editar\" class=\"bt-image modal-bt-det\" data-type=\"editar\" data-key=\"{$doc['prestador_documento_id']}\" />";
        $excluir = "<img src=\"../../imagens/icones/icon-trash.gif\" title=\"Excluir\" class=\"bt-image modal-bt-det\" data-type=\"excluir\" data-tipo=\"{$tpdoc['prestador_tipo_doc_id']}\" data-key=\"{$doc['prestador_documento_id']}\"  data-prest=\"{$_REQUEST['id_prestador']}\" />";

        $html .= "<tr><td><a href='{$path}{$doc['nome_arquivo']}{$doc['extensao_arquivo']}' target='_blanc'>{$doc['nome_arquivo']}</a></td><td><span>{$doc['data_vencimentobr']}</span></td><td class='center'>{$editar}</td><td class='center'>{$excluir}</td></tr>";
    }

    $html .= "</tbody></table></div>";

    $html .= "
        <div id=\"edicao_data\" class=\"hidden\">
            <input type=\"hidden\" name=\"id_edit\" id=\"id_edit\" value=\"\" />
            <p>Data de Vencimento: <input type=\"text\" name=\"edit_data\" id=\"edit_data\" class=\"data\" value=\"\" size=\"12\" /> <input type=\"button\" name=\"salvar\" id=\"bt-salvar\" value=\"Salvar\"/> <input type=\"button\" name=\"cancel\" id=\"bt-cancel\" value=\"Cancelar\"/></p>
        </div>
        
        <div id=\"upload_doc\" class=\"hidden\">
            <iframe src=\"actions_mx.php?method=uploadDoc&act=1&id_doc={$_REQUEST['id_doc']}&id_prestador={$_REQUEST['id_prestador']}\" width='98%' height='180' frameborder='0' scrolling='no' ></iframe>
        </div>";

    echo utf8_encode($html);
    exit;
}

//EDITANDO A DATA DO DOCUMENTO DO PRESTADOR
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "editaDataDoc") {
    $dt = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['valor'])));

    $campos = array("data_vencimento" => $dt);
    $where = array("prestador_documento_id" => $_REQUEST['id']);
    sqlUpdate("prestador_documentos", $campos, $where);

    $return['status'] = 1;
    echo json_encode($return);
    exit;
}

//EXCLUIR DOCUMENTO DO PRESTADOR
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "excluirDoc") {

    $resSel = mysql_query("SELECT * FROM prestador_documentos WHERE prestador_documento_id = {$_REQUEST['id']}");
    $row = mysql_fetch_assoc($resSel);

    mysql_query("DELETE FROM prestador_documentos WHERE prestador_documento_id = {$_REQUEST['id']}");
    $file = $path . "{$row['nome_arquivo']}{$row['extensao_arquivo']}";

    if (is_file($file))
        unlink($file);

    $return['status'] = 1;
    echo json_encode($return);
    exit;
}

// UPLOAD DE DOCUMENTO DO PRESTADOR
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "uploadDoc") {
    $tipo_doc = $_REQUEST['id_doc'];
    $id_prestador = $_REQUEST['id_prestador'];

    echo "<html>
                <head>
                    <link href=\"../../net1.css\" rel=\"stylesheet\" type=\"text/css\" />
                    <link href=\"../../css/cupertino/jquery-ui-1.9.2.custom.css\" rel=\"stylesheet\" type=\"text/css\" />
                    <link href=\"../../css/validationEngine.jquery.css\" rel=\"stylesheet\" type=\"text/css\" />
                    <link href=\"prestador.css\" rel=\"stylesheet\" type=\"text/css\" />
                    <link href=\"../../favicon.ico\" rel=\"shortcut icon\" />
                    <script src=\"../../js/jquery-1.8.3.min.js\" type=\"text/javascript\"></script>
                    <script src=\"../../js/jquery-ui-1.9.2.custom.min.js\" type=\"text/javascript\"></script>
                    <script src=\"../../js/jquery.validationEngine.js\" type=\"text/javascript\"></script>
                    <script src=\"../../js/jquery.validationEngine-pt.js\" type=\"text/javascript\"></script>
                    <script src=\"../../jquery/mascara/jquery.maskedinput-1.2.2.js\" type=\"text/javascript\" ></script>
                    <script src=\"../../js/global.js\" type=\"text/javascript\"></script>
                    <script>
                        $(function(){
                            $(\".data\").mask(\"99/99/9999\");
                            $(\"#form1\").validationEngine();
                            
                            $('#bt-cancelup').click(function(){
                                $('#docUpload').val('');
                                $('#nova_data').val('');
                                parent.$('#upload_doc').addClass('hidden');
                            });
                            
                            $('#bt-atualizar').click(function(){
                                parent.$('.modal-bt[data-doc={$tipo_doc}][data-prest={$id_prestador}]').trigger('click');
                                var trQtd = parent.$('.modal-bt[data-doc={$tipo_doc}][data-prest={$id_prestador}]').parent().prev(); // seleciona a tag com as quantidades de documentos
                                var qtd = parseInt(trQtd.html());               // pega o valor html da tag e transforma em inteiro
                                qtd = qtd+1;                                    // soma o valor mais 1
                                trQtd.html(qtd);                                // coloca o novo valor na tag
                                parent.$('.modal-bt[data-doc={$tipo_doc}][data-prest={$id_prestador}]').data('act','ver');
                                parent.$('.modal-bt[data-doc={$tipo_doc}][data-prest={$id_prestador}]').attr('src','../../imagens/icones/icon-docview.gif');
                                parent.$('#upload_doc').addClass('hidden');
                            });
                            $('#bt-enviar').click(function(){
                                showLoading($(\"#bt-cancelup\"), \"../\");
                            });
                        });
                    </script>
                    <style>
                        p {line-height: 10px;}
                    </style>
                </head>
                <body style='background-color:#FFF!important; font-size: 12px'>";

    if (isset($_REQUEST['act']) && $_REQUEST['act'] == "1") {                
        
        echo "<form action=\"\" method=\"post\" name=\"form1\" id=\"form1\" enctype=\"multipart/form-data\" class=\"formUpDoc\" >
                <fieldset>
                    <legend>Upload MX</legend>
                        <input type=\"hidden\" name=\"id_doc\" id=\"id_doc\" value=\"{$_REQUEST['id_doc']}\" />
                        <input type=\"hidden\" name=\"id_prestador\" id=\"id_prestador\" value=\"{$_REQUEST['id_prestador']}\" />
                        <input type=\"hidden\" name=\"act\" id=\"act\" value=\"2\" />
                        
                        <p>
                        <label for=\"docUpload\">Arquivo:</label>
                        <input name=\"arquivo\" id=\"docUpload\" type=\"file\" class=\"validate[required]\"> 
                        </p>
                        <p>
                        <label for=\"nova_data\">Vencimento:</label> 
                        <input type=\"text\" name=\"nova_data\" id=\"nova_data\" class=\"data validate[required,custom[dateBr]]\" value=\"\" size=\"12\" /> 
                        </p>
                        <p class=\"upDoc-control\">
                        <input type=\"submit\" name=\"salvar\" id=\"bt-enviar\" value=\"Enviar\"/> 
                        <input type=\"button\" name=\"cancel\" id=\"bt-cancelup\" value=\"Cancelar\"/>
                        </p>
                </fieldset>
            </form>";
                        
    } elseif (isset($_REQUEST['act']) && $_REQUEST['act'] == "2") {
        $arquivo = $_FILES['arquivo'];
        $infos = explode(".", $arquivo["name"]);
        $tipoArquivo = "." . end($infos);
        $tipos = array('jpg', 'png', 'gif', 'pdf', 'doc');
        
        $tpdoc = PrestadorServico::getTipoDoc($tipo_doc);
        $nomeDoc = RemoveAcentos($tpdoc['prestador_tipo_doc_nome']);
        
        $dt = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['nova_data'])));
        
        
        $nome = $id_prestador . "_" . str_replace(" ", "_", $nomeDoc) . "_" . date("dmYHi");
        $enviar = GlobalClass::uploadFile($arquivo, $path, $tipos, $nome);
        
        
        if ($enviar['erro']) {
            echo "<div id='message-box' class='message-red'>Erro ao enviar o arquivo ({$enviar['erro']}), tente novamente mais tarde</div>";
        } else {
            
            $campos = "id_prestador, prestador_tipo_doc_id, nome_arquivo, data_vencimento, extensao_arquivo, status";
            $valores = array($id_prestador, $tipo_doc, $nome, $dt, $tipoArquivo, '1');
            
            $in = sqlInsert("prestador_documentos", $campos, $valores);
            
            $prestador_his = mysql_query("SELECT * FROM prestadorservico WHERE id_prestador = {$id_prestador}");
            $row_prestador = mysql_fetch_assoc($prestador_his);                        
            
            //Cria um historico pra alterar a data final do contrato
            if($tipo_doc == 15){
                $campos_his = "id_prestador, inicio_contrato, fim_contrato, renova_contrato";
                $valores_his = array($id_prestador, converteData($row_prestador['contratado_em']), converteData($row_prestador['encerrado_em']), $dt);
                
                $campos_pres = array("encerrado_em" => $dt);
                $where_his = array("id_prestador" => $id_prestador);
                
                sqlUpdate("prestadorservico", $campos_pres, $where_his);
                sqlInsert("prestador_historico", $campos_his, $valores_his);
            }
            
            if ($in > 0) {
                echo "<div id='message-box' class='message-green'>Arquivo enviado com sucesso</div>";
                echo "<p><input type=\"button\" name=\"continue\" id=\"bt-atualizar\" value=\"Atualizar\"/></p>";
            }
        }
    }
    
    echo "</body> </html>";
    exit;
}

//ANEXAR NOVO DOCUMENTO
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "novoDoc") {
    echo "<div id=\"upload_doc\">"
    . "<iframe src=\"actions_mx.php?method=uploadDoc&act=1&id_doc={$_REQUEST['id_doc']}&id_prestador={$_REQUEST['id_prestador']}\" width='98%' height='180' frameborder='0' scrolling='no' ></iframe>"
    . "</div>";
    exit();
}
?>