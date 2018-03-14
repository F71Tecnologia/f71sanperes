<?php

class PrestacaoContas {
    public $tipoSemADM;
    
    public function __construct() {
        $this->tipoSemADM = array("equipe");
    }
    
    /**** @param string $master Id do master do usuário * @return string ****/
    
    public static function carregaProjetos($master,$tipo=null){
        $tipoSemADM = array();
        $id_user = $_COOKIE['logado'];
        $ids_regs = array();
        $whereADM="";
        if($tipo !== null){
            if(in_array($tipo, $tipoSemADM)){
                $whereADM = "AND administracao = 0";
            }
        }
        
        $qrpermreg = mysql_query("SELECT * FROM funcionario_regiao_assoc WHERE id_funcionario = {$id_user} AND id_master = {$master}");
        while($row_regs = mysql_fetch_assoc($qrpermreg)){
            $ids_regs[] = $row_regs['id_regiao'];
        }
        
        $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_master = '{$master}' AND id_regiao IN (".implode(",",$ids_regs).") AND status_reg = 1 AND prestacontas = 1 {$whereADM} ORDER BY nome");
        $projetos = array("-1" => "« Selecione »");
        while ($row_projeto = mysql_fetch_assoc($qr_projeto)) {
            $projetos[$row_projeto['id_projeto']] = $row_projeto['id_projeto'] . " - " . $row_projeto['nome'];
        }
        return $projetos;
    }
    
    public static function getTiposPrestacoes($tipo=null){
        $return = null;
        $tipos = array(
            'rh'            => "Recursos Humanos",
            'despesa'       => "Despesas",
            'terceiro'      => "Contrato de Terceiros",
            'bens'          => "Bens Adquiridos",
            'conciliacao'   => "Conciliação Bancária",
            'fluxocaixa'    => "Fluxo de Caixa",
            'rateio'        => "Rateio de Despesas",
            'equipe'        => "Equipe",
            'rhrpa'         => "RPA"
        );
        
        if($tipo!=null){
            $return = $tipos[$tipo];
        }else{
            $return = $tipos;
        }
        
        return $return;
    }
    
    /**** @param type $tipo * @param type $dtReferencia * @param type $dtInicio ****/
    public static function getQueryVerifica($tipo,$dtReferencia,$dtInicio){
        $tipoSemADM = array("equipe");
        if(in_array($tipo, $tipoSemADM)){
            $whereAdm = "AND A.administracao = 0";
        }else{
            $whereAdm = "AND IF(A.administracao=1, D.administracao=1, D.administracao=0)";
        }
        
        $qr = "SELECT B.id_prestacao,A.id_projeto,A.nome as projeto,DATE_FORMAT(B.gerado_em, '%d/%m/%Y') as gerado_embr, C.nome as funcionario, D.id_banco, D.agencia, D.conta, A.administracao
                        FROM projeto AS A
                        LEFT JOIN bancos AS D ON (A.id_projeto=D.id_projeto)
                        LEFT JOIN prestacoes_contas AS B ON (A.id_projeto=B.id_projeto AND tipo = '{$tipo}' AND status = 1 AND data_referencia = '{$dtReferencia}' AND erros = 0 AND B.id_banco = D.id_banco)
                        LEFT JOIN funcionario AS C ON (B.gerado_por=C.id_funcionario)
                        WHERE A.inicio < '{$dtInicio}' AND A.prestacontas = 1 {$whereAdm}";
        return $qr;
    }
}