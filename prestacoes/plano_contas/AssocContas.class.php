<?php

//class PlanoConta extends ConsultaPConta {
class PlanoContasAssoc {
    
    public $prosoft_receitaP;
    public $prosoft_receitaL;
    public $prosoft_despesaP;
    public $prosoft_despesaL;
    public $prosoft_projeto1;
    public $prosoft_terceiroD;

    public function __construct($prosoft_receitaP, $prosoft_receitaL, $prosoft_despesaP, $prosoft_despesaL, $prosoft_projeto1, $prosoft_terceiroD) {

        $this->prosoft_receitaP = $prosoft_receitaP;
        $this->prosoft_receitaL = $prosoft_receitaL;
        $this->prosoft_despesaP = $prosoft_despesaP;
        $this->prosoft_despesaL = $prosoft_despesaL;
        $this->prosoft_projeto1 = $prosoft_projeto1;
        $this->prosoft_terceiroD = $prosoft_terceiroD;
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

        $result = mysql_query($sql_lc1) or die('Erro ConsultaAssoc');

        return $result;
    }

    public function consultaentrada() {
        $qryEntra = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 AND grupo = 5");
        $Opcao1Cta[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntra)) {
            $Opcao1Cta[$dados['id_entradasaida']] = $dados['nome'];
        }
        return $Opcao1Cta;
    }

    public function consultafolha1() {
        //$qryfolha = mysql_query("SELECT (SUM(ir) + SUM(inss) + SUM(total_liquido)) AS provis, ir AS f_ir, inss AS f_inss, total_liquido AS f_liq FROM rh_ferias WHERE projeto = '{$this->prosoft_projeto1}'");
        $qryfolha = mysql_query("SELECT DISTINCT cod, descicao FROM rh_movimentos ORDER BY cod");
        $qryrescisao = mysql_query("SELECT DISTINCT id_mov, nome_movimento FROM rh_movimentos_rescisao WHERE `status` != 0 AND id_mov != 0 GROUP BY id_mov;");
        $OpcaoFolha[" "] = " ";
        $OpcaoFolha["1"] = " **** RESCISÃO TRABALHISTA **** ";
        $OpcaoFolha["r000"] = "RESCISÃO A PAGAR";
        $OpcaoFolha["r001"] = "SALDO DE SALÁRIO";
        $OpcaoFolha["r002"] = "FÉRIAS PROPORCIONAIS";
        $OpcaoFolha["r003"] = "FÉRIAS VENCIDAS";
        $OpcaoFolha["r004"] = "1/3 CONSTITUCIONAL DE FÉRIAS";
        $OpcaoFolha["rc07"] = "AVISO PRÉVIO";
        $OpcaoFolha["r480"] = "MULTA ART 480";
        $OpcaoFolha["r479"] = "MULTA ART 479";
        $OpcaoFolha["r477"] = "MULTA ART 477";
        $OpcaoFolha["12506"] = "LEI 12.506";
        $OpcaoFolha["r008"] = "INSALUBRIDADE";
        $OpcaoFolha["r009"] = "AJUSTE DE SALDO DEVEDOR";
        $OpcaoFolha["rd07"] = "AVISO PRÉVIO PAGO PELO FUNCIONÁRIO";
        $OpcaoFolha["r011"] = "INSS SALDO DE SALÁRIO";
        $OpcaoFolha["r012"] = "IR SALDO DE SALÁRIO";
        $OpcaoFolha["r005"] = "13º SALÁRIO";
        $OpcaoFolha["r006"] = "13º SALÁRIO (AVISO-PRÉVIO INDENIZADO)";  
        $OpcaoFolha["r013"] = "INSS 13º SALÁRIO";
        $OpcaoFolha["r014"] = "IR 13º SALÁRIO";
        while ($dados = mysql_fetch_array($qryrescisao)) {
            $OpcaoFolha[$dados['id_mov']] = $dados['nome_movimento']. " ( ".($dados['id_mov'])." )";
        }
        $OpcaoFolha["2"] = " ";
        $OpcaoFolha["3"] = " *** FOLHA SALÁRIO / FÉRIAS *** ";
        $OpcaoFolha["0000"] = "LIQUIDO DA FOLHA";

        while ($dados = mysql_fetch_array($qryfolha)) {
            $OpcaoFolha[$dados['cod']] = $dados['cod']." - ".$dados['descicao'];
        }
        return $OpcaoFolha;
    }
 
    public function consultaentradaesaida() {
        $qryEntSai = mysql_query("SELECT * FROM entradaesaida WHERE tipo = 1 AND grupo >= 10 ORDER BY cod");
        $OpcaoCta[" "] = " ";

        while ($dados = mysql_fetch_array($qryEntSai)) {
            $OpcaoCta[$dados['id_entradasaida']] = $dados['cod'] . " - " . $dados['nome'];
        }
        return $OpcaoCta;
    }

    public function consultaplanodecontas() {
        $qryPlcontas = mysql_query("SELECT * FROM plano_de_contas ORDER BY acesso");
        $OpcaoCtas[" "] = " ";

        while ($conta = mysql_fetch_array($qryPlcontas)) {
            $OpcaoCtas[$conta['id_plano_contas']] = $conta['acesso'] . " - " . $conta['nome'];
        }
        return $OpcaoCtas;
    }

    public function consultahistorico() {
        $qryHistorico = mysql_query("SELECT * FROM contabil_historico_prosoft");
        $OpcaoHist[" "] = " ";

        while ($historico = mysql_fetch_array($qryHistorico)) {
            $OpcaoHist[$historico['id_historico']] = $historico['codigo'] . " - " . $historico['descricao'];
        }
        return $OpcaoHist;
    }

    public function consultaterceiros() {
        $qryTerc = mysql_query("SELECT * FROM prestador_prosoft ORDER BY id_prestador_prosoft");
        $terceiros = mysql_fetch_array($qryTerc);
        $OpcaoTerceiros[" "] = " ";

        while ($terceiros = mysql_fetch_array($qryTerc)) {
            $OpcaoTerceiros[$terceiros['id_prestador_prosoft']] = " - " . $terceiros['nome'];
        }
        return $OpcaoTerceiros;
    }

    public function consultaempresaprosoft() {
        $qryEmpresaProsoft = mysql_query("SELECT * FROM empresas_Prosoft_assoc ORDER BY id_empresa");

        while ($empresa = mysql_fetch_array($qryEmpresaProsoft)) {
            $OpcaoEmpresa[$empresa['id_empresa']] = $empresa['id_empresa'];
        }
        return $OpcaoEmpresa;
    }
    
    public function checkAssoc($prosoft_despesaP, $prosoft_despesaL, $prosoft_projeto1){
        $query = "SELECT * FROM entradaesaida_plano_contas_assoc
                  WHERE id_plano_contas = $prosoft_despesaP AND id_entradasaida = $prosoft_despesaL AND id_projeto = $prosoft_projeto1";
        $result = mysql_query($query) or die("ERRO AO CHECAR ASSOCIAÇÃO: ". mysql_error());
        $num_rows = mysql_num_rows($result);
        return ($num_rows >0)?TRUE:FALSE;
    }

    public function checkAssocR($prosoft_receitaP, $prosoft_receitaL, $prosoft_projeto2){
        $query = "SELECT * FROM entradaesaida_plano_contas_assoc
                  WHERE id_plano_contas = $prosoft_receitaP AND id_entradasaida = $prosoft_receitaL AND id_projeto = $prosoft_projeto2";
        return mysql_query($query) or die("ERRO AO CHECAR ASSOCIAÇÃO: ". mysql_error());
    }

    public function checkAssocFolha($folha_projeto, $folha_codigo, $folha_prosoft, $folha_tipo){
        $query = "SELECT * FROM contabil_folha_prosoft
                  WHERE id_codigo = '{$folha_codigo}' AND id_plano_de_conta = '{$folha_prosoft}' AND id_projeto = '{$folha_projeto}' AND tipo = '{$folha_tipo}'";
        $result = mysql_query($query) or die("ERRO AO CHECAR ASSOCIAÇÃO: ". mysql_error());
        
        $num_rows = mysql_num_rows($result);
        return ($num_rows > 0)?TRUE:FALSE;
    }

}
