<?php

//class PlanoConta extends ConsultaPConta {
class PlanoContasAssoc {
    
    public $prosoft_contaP;
    public $prosoft_contaL;
    public $prosoft_projeto1;
    public $prosoft_terceiro;

    public function __construct($prosoft_contaP, $prosoft_contaL, $prosoft_projeto1, $prosoft_terceiro) {

        $this->prosoft_contaP = $prosoft_contaP;
        $this->prosoft_contaL = $prosoft_contaL;
        $this->prosoft_projeto1 = $prosoft_projeto1;
        $this->prosoft_terceiro = $prosoft_terceiro;
    }
    
    public function ConsultaAssoc() {
        $sql_assoc1 = "SELECT A.id_entradasaida AS conta, E.nome AS descricao, C.acesso AS acesso, C.nome AS descricao, D.nome AS empresa, B.nome AS terceiro
            FROM entradaesaida_plano_contas_assoc AS A
            LEFT JOIN prestador_prosoft AS B ON (B.id_prestador_prosoft = A.id_terceiro)
            LEFT JOIN plano_de_contas AS C ON(C.id_plano_contas = A.id_plano_contas)
            LEFT JOIN projeto AS D ON(D.id_projeto = A.id_projeto)
            LEFT JOIN entradaesaida E ON(E.id_entradasaida = A.id_entradasaida)
            WHERE A.id_projeto = '{$this->prosoft_projeto1}'
            ORDER BY C.acesso ASC";
        echo $sql_assoc1;
//        exit();
        $result = mysql_query($sql_lc1) or die ('Erro ConsultaAssoc');
        
        return $result;
    }
//    public function consultaCtaLagos(){
//        $qry = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 ORDER BY cod");
//        $dados = mysql_fetch_array($qry);
//        $OpcaoCta[(" ")] = " ";
//        while ($dados = mysql_fetch_array($qry)) {
//        $OpcaoCta[$dados['cod']] = " - ".$dados['nome'];
//        
//        }
//        
//        $qry1 = mysql_query("SELECT * FROM plano_de_contas ORDER BY acesso" );
//        $dados1 = mysql_fetch_array($qry1);
//        $OpcaoCtas[(" ")] = " ";
//        while ($dados1 = mysql_fetch_array($qry1)) {
//            $OpcaoCtas[$dados1['acesso']] = " - ".$dados1['nome'];
//        }
//
//        $qryTerc = mysql_query("SELECT * FROM prestador_prosoft ORDER BY id_prestador_prosoft");
//        $terceiros = mysql_fetch_array($qryTerc);
//        $OpcaoTerceiros[(" ")] = " ";
//        while ($terceiros = mysql_fetch_array($qryTerc)) {
//        $OpcaoTerceiros[$terceiros['id_prestador_prosoft']] = " - ".$terceiros['nome'];
//        }   
//
//    }
 }