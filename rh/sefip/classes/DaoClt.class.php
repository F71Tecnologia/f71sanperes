<?php
/*
 * PHP-DOC - DaoClt.class.php 
 * 
 * Classe para conexão a banco de dados MySql moldando a conexão a orientação a objetos 
 *
 * 00-00-0000
 * 
 * @version
 *  
 * Versão: 3.00.7648 - 09/03/2016 - Jacques - No método buscaEmpresa foi acrescentado a substituição '/' por '' pois campo com esse caracter estava gerando erro no GRRF
 * 
 * @author Não definida
 * 
 * @copyright www.f71.com.br
 */

//include_once 'Grrf.class.php';

class DaoClt extends DaoMain {
    
    public function buscaRescisao(Clt $clt) {     
        $dataDemi = explode('/', $clt->getRescisao()->getDataDemi());
        
        $sql = "SELECT *, IF(A.aviso='indenizado',2 ,IF(A.aviso='trabalhado','1',3)) AS aviso_codigo        
                FROM rh_recisao AS A INNER JOIN  rhstatus AS B ON B.codigo= A.motivo   WHERE MONTH(data_demi) = '$dataDemi[1]' AND YEAR(data_demi) = '$dataDemi[0]' AND id_regiao = '{$clt->getRegiao()->getId()}' AND id_projeto = '{$clt->getProjeto()->getId()}' AND id_clt = '{$clt->getId()}' AND status = '1'";                
//        echo '<br>'.$sql.'<br>';
        return $this->mysqlQueryToArray(mysql_query($sql));
    }
    function buscaClt(Clt $clt, $id_rescisao){
        //, pensao_percentual
        $sql = "SELECT C.id_regiao, B.id_projeto, A.id_curso, A.status,
                A.pis, A.data_entrada, A.nome, A.campo1 AS numero_ctps, A.serie_ctps, 
                A.escolaridade, A.data_nasci, A.cpf , A.sexo ,D.codigo_saque ,D.cod_movimentacao 
                FROM rh_clt AS A 
                INNER JOIN projeto AS B ON A.id_projeto = B.id_projeto 
                INNER JOIN regioes AS C ON B.id_regiao = C.id_regiao  
                INNER JOIN rh_recisao AS E ON A.id_clt = E.id_clt
                INNER JOIN rhstatus AS D ON E.motivo = D.codigo  
                WHERE E.id_recisao = $id_rescisao"; 
                
        return mysql_fetch_array(mysql_query($sql));        
    }
    function buscaEmpresa(Clt $clt){
        $id_regiao = $clt->getRegiao()->getIdRegiao();
        $projeto = $clt->getProjeto()->getIdProjeto();
        
        $sql = "SELECT *, REPLACE(REPLACE(REPLACE(cnae,'-',''),'.',''),'/','') as cnae2 FROM rhempresa WHERE id_regiao = '$id_regiao' AND id_projeto = '$projeto'";
        
        return mysql_fetch_array(mysql_query($sql));
    }
    function getCbo(Clt $clt, $sobrescreveCboCodigo = array()){
        $idCurso = $clt->getCurso()->getIdCurso();
        $sql_curso = "SELECT * FROM curso WHERE id_curso = '$idCurso'";
//        echo $sql_curso.'<br>';
        $qr_cruso = mysql_query($sql_curso);
        $row_curso = mysql_fetch_assoc($qr_cruso);
        
        
        //gambi para o cbo
        if(!empty($sobrescreveCboCodigo) && isset($sobrescreveCboCodigo[$row_curso[cbo_codigo]])){
            $cbo_codigo = $sobrescreveCboCodigo[$row_curso[cbo_codigo]];
        }else{
            $cbo_codigo = $row_curso[cbo_codigo];
        }
        
        $sql_cbo = "SELECT cod FROM rh_cbo WHERE id_cbo = '$cbo_codigo'";
//        echo $sql_cbo.'<br>';
	$qr_cbo  = mysql_query($sql_cbo);
	$row_cbo = mysql_fetch_assoc($qr_cbo);
	$num_cbo = mysql_num_rows($qr_cbo);	
	if(empty($num_cbo)) {
		$cbo = $row_curso['cbo_codigo'];
	} else {
		$cbo = $row_cbo['cod'];
	}
        return $row_cbo;
    }

}