<?php

    include("../../conn.php");
    include("../../wfunction.php");
    include('../../classes/global.php');
    include("../../classes/RhClass.php");

    $rh = new RhClass();
    $rh->AddClassExt('Clt'); 
    
    /**
    * ATUALIZANDO A TABELA DE RH_CLT
    * COM A DATA ATUAL DA AÇÃO DE 
    * FINALIZAR A FOLHA
    */
    $rh->Clt->setDefault()->setIdClt($_REQUEST['clt'])->onUpdate();


    $id_clt     = $_REQUEST['clt'];
    $data_demi 	= $_REQUEST['data_demi'];
    $data_aviso = $_REQUEST['data_aviso'];
    $data_comp  = $_REQUEST['data_comp'];
    $pro 	= $_REQUEST['pro'];
    $id_reg 	= $_REQUEST['id_reg'];

    // PEQUENA FUNÇÃO PARA QUEBRAR A DATA 
    if(strstr($data_demi, "/")){
            $Dat = implode('-', array_reverse(explode('/', $data_demi)));
    }

    if(strstr($data_aviso, "/")){
            $Dat2 = implode('-', array_reverse(explode('/', $data_aviso)));
    }
    //

    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    $data_cad = date('Y-m-d');
    $user_cad = $_COOKIE['logado'];

    $result_verifica = mysql_query("SELECT * FROM rh_doc_status WHERE tipo = '12' AND id_clt = '$id_clt'");
    $num_row_verifica = mysql_num_rows($result_verifica);



    if($_COOKIE['logado'] != 9){
    if(empty($num_row_verifica)) {

                mysql_query("INSERT INTO rh_doc_status(tipo,id_clt,data,id_user) VALUES ('12','$id_clt','$data_cad', '$user_cad')");

        } else {

                mysql_query("UPDATE rh_doc_status SET data = '$data_cad', id_user = '$user_cad' WHERE id_clt = '$id_clt' and tipo = '12'");

        }
    }


    //-------------GRAVANDO NA TABELA DOCUMENTOS GERADOS
    if($_COOKIE['logado'] != 9){
    // GRAVANDO NA RH_CLT A DATA DO PEDIDO DE EMISSÃO
    mysql_query("UPDATE rh_clt SET data_aviso = '$Dat2', data_demi = '$Dat', data_saida = '$Dat', status = '200' WHERE id_clt = '$id_clt' LIMIT 1");

    // GRAVANDO NA TABELA RH EVENTOS
//	mysql_query("INSERT INTO rh_eventos (id_clt, id_regiao, id_projeto, cod_status, data, status, status_reg) VALUES ('$id_clt', '$id_reg', '$pro', '".$_REQUEST['tipo_rescisao']."', '$Dat', '1', '1')") or die(mysql_error());
    }
    #RESOLVENDO QUAL ARQUIVO VAI SER CHAMADO
    //-- ENCRIPTOGRAFANDO A VARIAVEL
   
    $link = str_replace("+","--",$link);
    $link2 = "clt=$id_clt&pro=$pro&id_reg=$id_reg&data_demi=$data_demi&data_aviso=$data_aviso&data_comp=$data_comp";
    // -----------------------------

    if($_REQUEST['tipo_rescisao'] == 991 and $_REQUEST['aviso'] == "Trabalhado"){
            echo "<script> location.href = 'avisotrabalhado.php?$link2'; </script>";
    }elseif($_REQUEST['tipo_rescisao'] == 991 and $_REQUEST['aviso'] == "Indenizado"){
            echo "<script> location.href = 'avisotraindenizado.php?$link2'; </script>";
    }elseif($_REQUEST['tipo_rescisao'] == 992 or $_REQUEST['tipo_rescisao'] == 993){
            echo "<script> location.href = 'avisoterminodecontrato.php?$link2'; </script>";
    }elseif($_REQUEST['tipo_rescisao'] == 994){
            echo "<script> location.href = 'dispensa_justa_causa.php?$link2'; </script>";
        }
?>