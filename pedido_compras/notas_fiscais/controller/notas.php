<?php

include_once("../../../conn.php");
include_once("../../../classes/NotaFiscalClass.php");
$notas = new NotaFiscal();

/**********************************CADASTRO************************************/
if(isset($_REQUEST['acao']) && $_REQUEST['acao'] != ""){
    if($_REQUEST['acao'] == "cadastrar"){
        $sql = $notas->cadNotas($_REQUEST);
        if($sql){
            header("Location: ../notas_page.php");
        }        
    }else if($_REQUEST['acao'] == "editar"){
        $sql = $notas->editNotas($_REQUEST);
        if($sql){
            header("Location: ../notas_page.php");
        }
    }else if($_REQUEST['acao'] == "excluir"){
        $retorno = array("status" => 0);
        $sql = $notas->removeNotas($_REQUEST['nota']);
        if($sql){
           $retorno = array("status" => 1); 
        }         
        echo json_encode($retorno);
        exit();
    }
}

if(isset($_REQUEST['method'])){
    
    //CARREGA PROJETO
    if($_REQUEST['method'] == 'carregaProjeto'){
        $qr_projeto = "SELECT * FROM projeto WHERE id_regiao = {$_REQUEST['regiao']} AND status_reg = 1 ORDER BY nome";
        $retorno = array("status" => 0);
        $dados = array();
        try{
            $sql_projeto = mysql_query($qr_projeto); 
            while ($row_projeto = mysql_fetch_assoc($sql_projeto)) {
                $dados[$row_projeto['id_projeto']][] = $row_projeto['id_projeto'] ." - ". utf8_encode($row_projeto['nome']);
            }        
            $retorno = array("status" => 1, "dados" => $dados);

        }catch(Exception $e){
            echo $e->getMessage("Erro ao selecionar projetos");
        }

        echo json_encode($retorno);
        exit();
    }
}