<?php
require("../../conn.php");
require("../../classes/uploadfile.php");
require("../../wfunction.php");
require("../../funcoes.php");

//if(!empty($_FILES['file'])){
//
//	$diretorio = '../../fotos';
//
//	if(isset($_POST['clt'])){
//		$diretorio = '../../fotosclt';
//	}
//
//	$nome_arquivo = $_POST['regiao']."_".$_POST['projeto'];
//	if(isset($_POST['clt'])){
//		$nome_arquivo .= "_".$_POST['clt'];
//	}else{
//		$nome_arquivo .= "_".$_POST['id_participantes'];
//	}
//
//	$obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif'));
//	$obj->arquivo($_FILES['file']);
//	$obj->verificaFile();
//	$obj->NomeiaFile($nome_arquivo);
//	$obj->Envia();
//
//
//	echo $obj->erro;
//
//	if(empty($obj->erro)) {
//		if(isset($_POST['clt'])){
//
//			mysql_query("UPDATE rh_clt SET foto = '1' WHERE id_clt = '$_POST[clt]' LIMIT 1");
//
//		}else{
//
//			mysql_query("UPDATE autonomo SET foto = '1' WHERE id_autonomo = '$_POST[id_participantes]' LIMIT 1");
//		}
//
//	}
//}

/**
 * UPLOADS DA NOVA TELA DO VER_CLT
 */
if(!empty($_FILES['file'])){

    /**
     * UPLOADS DE FOTOS
     */
    if(!empty($_POST['action']) && $_POST['action'] == 'upload_foto') {

        $diretorio = '../../fotos';

        if(isset($_POST['clt'])){
            $diretorio = '../../fotosclt';
        }

        $nome_arquivo = $_POST['regiao']."_".$_POST['projeto'];
        if(isset($_POST['clt'])){
            $nome_arquivo .= "_".$_POST['clt'];
        }else{
            $nome_arquivo .= "_".$_POST['id_participantes'];
        }

        $obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif'));
        $obj->arquivo($_FILES['file']);
        $obj->verificaFile();
        $obj->NomeiaFile($nome_arquivo);
        $obj->Envia();
        echo $obj->erro;

        if(empty($obj->erro)) {
            if(isset($_POST['clt'])){
                mysql_query("UPDATE rh_clt SET foto = '1', ext_foto = '$obj->extensao' WHERE id_clt = '$_POST[clt]' LIMIT 1");
            }else{
                mysql_query("UPDATE autonomo SET foto = '1' WHERE id_autonomo = '$_POST[id_participantes]' LIMIT 1");
            }
        }
    }

    /**
     * upload anexo itens viagem
     */
    if(!empty($_POST['action']) && $_POST['action'] == 'upload_anexo_itens_viagem') {

        $diretorio = "../../finan/viagem/";
        if (!file_exists($diretorio)) {
            if (!mkdir($diretorio, 755)) {
                exit("erro ao criar a pasta: {$diretorio}");
            }
        }
        $diretorio .= "anexo/";
        if (!file_exists($diretorio)) {
            if (!mkdir($diretorio, 755)) {
                exit("erro ao criar a pasta: {$diretorio}");
            }
        }
        $diretorio .= "{$_REQUEST['id_viagem']}/";
        if (!file_exists($diretorio)) {
            if (!mkdir($diretorio, 755)) {
                exit("erro ao criar a pasta: {$diretorio}");
            }
        }

        $obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif','pdf'));
        $obj->arquivo($_FILES['file']);
        $obj->verificaFile();

        if(empty($obj->erro)) {
            mysql_query("INSERT INTO viagem_anexos (id_viagem, id_item, tipo_anexo, extensao) VALUES ('{$_REQUEST['id_viagem']}', '{$_REQUEST['id_item']}', '{$_REQUEST['tipo_anexo']}', '{$obj->extensao}');") or die(mysql_error());
            $id = mysql_insert_id();

            $obj->NomeiaFile($id);
            $obj->Envia();
        }
        echo $obj->erro;
    }

    /**
     * UPLOADS DE DOCUMENTOS
     */
    if(!empty($_POST['action']) && $_POST['action'] == 'upload_documentos') {

//        print_array($_FILES);
//        exit();

        $diretorio = '../../rh/documentos';

        $arquivo      = $_FILES['file']['name'];
        $nome	      = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
        $id_documento = $_POST['id_documento'];
        $id_clt	      = $_POST['id_clt'];

        $obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif', 'pdf'));
        $obj->arquivo($_FILES['file']);
        $obj->verificaFile();
        $obj->NomeiaFile($nome);
        $obj->Envia();
        echo $obj->erro;
//
//        echo "INSERT INTO documento_clt_anexo (anexo_nome, id_upload, id_clt, anexo_extensao, data_cad,  anexo_status) VALUES ('$nome', '$id_documento', '$id_clt', '{$obj->extensao}', NOW(),  '1')";
        if(empty($obj->erro)) {
            $qr_inser = mysql_query("INSERT INTO documento_clt_anexo (anexo_nome, id_upload, id_clt, anexo_extensao, data_cad,  anexo_status) VALUES ('$nome', '$id_documento', '$id_clt', '{$obj->extensao}', NOW(),  '1')") or die(mysql_error());
        }
    }

    /**
     * UPLOADS DE DOCUMENTOS do CONTROLE DE DOCUMENTOS
     */
    if(!empty($_POST['action']) && $_POST['action'] == 'upload_documentos_rh') {

//        print_array($_FILES);
//        exit();

        $diretorio = '../../rh/documentos';

        $arquivo      = $_FILES['file']['name'];
        $nome	      = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
        $id_documento = $_POST['id_documento'];
        $id_clt	      = $_POST['id_clt'];

        $obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif', 'pdf'));
        $obj->arquivo($_FILES['file']);
        $obj->verificaFile();
        $obj->NomeiaFile($nome);
        $obj->Envia();
        echo $obj->erro;
//
//        echo "INSERT INTO documento_clt_anexo (anexo_nome, id_upload, id_clt, anexo_extensao, data_cad,  anexo_status) VALUES ('$nome', '$id_documento', '$id_clt', '{$obj->extensao}', NOW(),  '1')";
        if(empty($obj->erro)) {
            $qr_inser = mysql_query("INSERT INTO documento_rh_anexo (anexo_nome, id_doc, id_clt, anexo_extensao, data_cad,  anexo_status) VALUES ('$nome', '$id_documento', '$id_clt', '{$obj->extensao}', NOW(),  '1')") or die(mysql_error());
        }
    }


}

/**
 * UPLOADS DE NOTAS
 */
if(!empty($_POST['action']) && $_POST['action'] == 'upload_notas') {
    /**
     * Tabelas envolvidas: Notas, Entrada, notas_assoc,notas_flies
     * Obs: Tabelas Entrada e Notas_assoc se serão envolvidas se houver entrada no financeiro
     */
    $diretorio = '../../adm/adm_notas/notas';

    /**
     * Separando os dados
     */
    $n_nota         = trim(mysql_real_escape_string($_POST['n_nota']));
    $nota_empenho         = trim(mysql_real_escape_string($_POST['nota_empenho']));
    $id_parceiro    = $_POST['parceiro'];
    $data_emissao   =  implode('-',array_reverse(explode('/',$_POST['data_emissao'])));
    $descricao      = utf8_decode($_POST['descricao']);
    $valor          = str_replace(',','.',str_replace('.','',$_POST['valor']));
    $valor_iss      = str_replace(',','.',str_replace('.','',$_POST['valor_iss']));
    $tipo           = (isset($_POST['tipo']) && !empty($_POST['tipo']))?$_POST['tipo']:0;
    $usuario_id     = $_COOKIE['logado'];
    $id_projeto     = $_POST['projeto'];


    $ano_competencia = $_POST['ano_competencia'];
    /**
     * Separando os dados para salvar o arquivo
     */

    $arquivo      = $_FILES['file']['name'];
    $nome	      = md5(uniqid(pathinfo($arquivo, PATHINFO_BASENAME)));
    $extensao     = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
    $id_documento = $_POST['n_nota'];



    list ($tipo_contrato2,$tipo_contrato)=explode('_',$_POST['contrato']);


    $vencimento_entrada = implode('-',array_reverse(explode('/',$_POST['data_entrada'])));

//echo "INSERT INTO `notas` (numero,  id_parceiro, data_emissao, descricao, valor, tipo, nota_data, id_funcionario,status,id_projeto,tipo_contrato,tipo_contrato2,nota_ano_competencia, nota_empenho, num_operacao_banco)
//        VALUES ('$n_nota','$id_parceiro','$data_emissao', '$descricao', '$valor', '$tipo', NOW() ,'$usuario_id','1','$id_projeto','$tipo_contrato','$tipo_contrato2','$ano_competencia', '$nota_empenho', '$operacao_bancaria') ";
//exit;
    $sql         = mysql_query("INSERT INTO `notas` (numero,  id_parceiro, data_emissao, descricao, valor, tipo, nota_data, id_funcionario,status,id_projeto,tipo_contrato,tipo_contrato2,nota_ano_competencia, nota_empenho)
        VALUES ('$n_nota','$id_parceiro','$data_emissao', '$descricao', '$valor', '$tipo', NOW() ,'$usuario_id','1','$id_projeto','$tipo_contrato','$tipo_contrato2','$ano_competencia', '$nota_empenho') ") or die("Erro ao cadastrar a nota: ".  mysql_error());

    $id_nota = (int) @mysql_insert_id();


    /**
     * Verifica se foi selecionado a criada no financeiro
     */
    if($_REQUEST['entrada'] == true){
        // CRIANDO ENTRADA NO FINANCEIRO

        $id_banco = $_POST['banco'];
        $qr_banco = mysql_query("SELECT * FROM bancos WHERE id_banco = '$id_banco' LIMIT 1");
        $row_banco = mysql_fetch_assoc($qr_banco);
        $id_regiao_entrada = $row_banco['id_regiao'];
        $id_projeto_entrada = $row_banco['id_projeto'];
        // recebendo o nome do projeto
        $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$id_projeto'");
        $nome_projeto = @mysql_result($qr_projeto,0);
        $n_nota         = trim(mysql_real_escape_string($_POST['n_nota']));

        // Montando o nome da entrada
        $nome_entrada = $id_projeto . ' - ' . $nome_projeto . ' | Nota Nº.: ' . $n_nota;

        $sql_entrada = "INSERT INTO entrada 
            ( id_regiao, id_projeto, id_banco, 	id_user, 	nome, 	especifica, 	tipo, 	adicional, 	valor, valor_iss, 	data_proc, 	data_vencimento, comprovante, status, numero_doc) 
            VALUES 
            ('$id_regiao_entrada', '$id_projeto_entrada', '$id_banco', '$usuario_id', '$nome_entrada', '$nome_entrada', '12' , '0,00' , '$valor', '$valor_iss' , NOW(),  '$vencimento_entrada', '2', '1', '$n_nota');";
        mysql_query($sql_entrada);

        $id_entrada = (int) @mysql_insert_id();


        // MUDOU a estrutura como sempre rsrs
        mysql_query("INSERT INTO notas_assoc ( id_notas, id_entrada ) VALUES ( '$id_nota', '$id_entrada');");
    }


    $selNotaFile = "SELECT * FROM notas_files WHERE id_notas = $n_nota LIMIT 1";
    $querySelNotaFile = mysql_query($selNotaFile);

    if (mysql_num_rows($querySelNotaFile)) {
        $updateNotaFiles = "UPDATE notas_files SET status = '0' WHERE id_notas = $id_nota";
        $queryNotaFiles = mysql_query($updateNotaFiles);
    }


    $qr_inser = mysql_query( "INSERT INTO notas_files (id_notas, tipo, status, ordem) VALUES ('$id_nota' , '$extensao', '1', '0')") or die(mysql_error());
    $id_file = (int) @mysql_insert_id();

    $query_update_notas = mysql_query("UPDATE notas_files SET id_notas = '$id_nota' WHERE id_file IN ($id_file)");

    $obj = new UploadFile($diretorio,array('jpg','jpeg','png','gif', 'pdf'));
    $obj->arquivo($_FILES['file']);
    $obj->verificaFile();
    $obj->NomeiaFile($id_file);
    $obj->Envia();
    //echo $obj->erro;

    $result = mysql_query("SELECT nome FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
    while($row = mysql_fetch_assoc($result))
    {
        $nome_funcionario = $row['nome'];
    }
    $msg = $nome_funcionario.' cadastrou a nota: '.'id_nota:('.$id_nota.') - Número: '.$n_nota;
    registrar_log('ADMINISTRAÇÃO - CADASTRO DE NOTAS FISCAIS',$msg);
}
?>

