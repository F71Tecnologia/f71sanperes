<?php
require_once("../../classes/LogClass.php");

//CLASSE cooperado 04.08.2009
class cooperativa {

    public function __construct() {
        $user = $_COOKIE['logado'];
    }

    function MostraCoop($coop) {

        $RE = mysql_query("SELECT * FROM cooperativas WHERE id_coop = '$coop'");
        $Row = mysql_fetch_array($RE);

        $this->id_coop = $Row['id_coop'];
        $this->id_regiao = $Row['id_regiao'];
        $this->tipo = $Row['tipo'];
        $this->nome = $Row['nome'];
        $this->fantasia = $Row['fantasia'];
        $this->endereco = $Row['endereco'];
        $this->bairro = $Row['bairro'];
        $this->cidade = $Row['cidade'];
        $this->cnpj = $Row['cnpj'];
        $this->tel = $Row['tel'];
        $this->fax = $Row['fax'];
        $this->contato = $Row['contato'];
        $this->cel = $Row['cel'];
        $this->email = $Row['email'];
        $this->site = $Row['site'];
        $this->diretor = $Row['diretor'];
        $this->matriculad = $Row['matriculad'];
        $this->rgd = $Row['rgd'];
        $this->cpfd = $Row['cpfd'];
        $this->enderecod = $Row['enderecod'];
        $this->presidente = $Row['presidente'];
        $this->matriculap = $Row['matriculap'];
        $this->rgp = $Row['rgp'];
        $this->cpfp = $Row['cpfp'];
        $this->enderecop = $Row['enderecop'];
        $this->entidade = $Row['entidade'];
        $this->fundo = $Row['fundo'];
        $this->parcelas = $Row['parcelas'];
        $this->cursos = $Row['cursos'];
        $this->taxa = $Row['taxa'];
        $this->foto = $Row['foto'];
        $this->iss = $Row['iss'];
        $this->status_reg = $Row['status_reg'];
        $this->id_banco = $Row['id_banco'];
        $this->bonificacao = $Row['bonificacao'];
        $this->cooperativa_cep = $Row['cooperativa_cep'];
        $this->cooperativa_uf = $Row['cooperativa_uf'];
        $this->cooperativa_cnae = $Row['cooperativa_cnae'];
        $this->cooperativa_fpas = $Row['cooperativa_fpas'];

        /* JOGUE ESTE CÓDIGO NA PÁGINA PARA PEGAR A VARIAVEL
          include "../classes/cooperativa.php";
          $cooperativa = new cooperativa();
          $cooperativa -> MostraCoop(row_folha['coop']);

          $id_coop	 	= $cooperativa -> id_coop;
          $id_regiao 		= $cooperativa -> id_regiao;
          $nome	 		= $cooperativa -> nome;
          $fantasia		= $cooperativa -> fantasia;
          $endereco		= $cooperativa -> endereco;
          $bairro			= $cooperativa -> bairro;
          $cidade			= $cooperativa -> cidade;
          $cnpj			= $cooperativa -> cnpj;
          $tel			= $cooperativa -> tel;
          $fax			= $cooperativa -> fax;
          $contato		= $cooperativa -> contato;
          $cel			= $cooperativa -> cel;
          $email			= $cooperativa -> email;
          $site			= $cooperativa -> site;
          $diretor		= $cooperativa -> diretor;
          $matriculad		= $cooperativa -> matriculad;
          $rgd			= $cooperativa -> rgd;
          $cpfd			= $cooperativa -> cpfd;
          $enderecod		= $cooperativa -> enderecod;
          $presidente		= $cooperativa -> presidente;
          $matriculap		= $cooperativa -> matriculap;
          $rgp			= $cooperativa -> rgp;
          $cpfp			= $cooperativa -> cpfp;
          $enderecop		= $cooperativa -> enderecop;
          $entidade		= $cooperativa -> entidade;
          $fundo			= $cooperativa -> fundo;
          $parcelas		= $cooperativa -> parcelas;
          $cursos			= $cooperativa -> cursos;
          $taxa			= $cooperativa -> taxa;
          $foto			= $cooperativa -> foto;
          $iss			= $cooperativa -> iss;
          $status_reg		= $cooperativa -> status_reg; */
    }

    function SelectCooperativa($regiao, $nome) {

        $RE = mysql_query("SELECT * FROM cooperativas where id_regiao = '$regiao'");
        $select = "<select name='$nome' id='$nome'>\n";
        $select .= "<option value='0'> Selecione </option>\n";
        while ($Row = mysql_fetch_array($RE)) {
            $select .= "<option value='$Row[0]'>$Row[0] - $Row[fantasia] </option>\n";
        }

        $select .= "</select>";

        echo $select;
    }

    public static function getCoop($idCoop) {
        $rs = montaQueryFirst("cooperativas", "*", "id_coop = {$idCoop}", NULL, null, null, false);
        return $rs;
    }

    public static function insert(array $dados) {
        $log = new Log();
        /* metodo para inserir no banco 
         * Autor: Leonardo
         * Arquivos que utilizam:
         * - form_cooperativa.php
         * - ver_cooperativa.php
         * - cooperativa_nova.php     */
//print_r($dados);
        foreach ($dados as $key => $value) {
            $colunas[] = $key;
            $valores[] = $value;
        }

        $resp = sqlInsert('cooperativas', $colunas, $valores);

        if ($resp) {
            
            $insertId = mysql_insert_id();
            $log->gravaLog('Gestão de Cooperativas', "Cadastro de Cooperativa: ID{$insertId}");
            return $insertId;
        } else {
            return FALSE;
        }
    }

    public static function update(array $dados) {
         $log = new Log();
        /* metodo para atualizar o banco
         * Autor: Leonardo
         * Arquivos que utilizam:
         * - form_cooperativa.php
         * - ver_cooperativa.php
         * - cooperativa_nova.php     */

        $resp = sqlUpdate('cooperativas', $dados, "id_coop = {$dados['id_coop']}");
        if ($resp) {
            $log->gravaLog('Gestão de Cooperativas', "Edição de Cooperativa: ID{$dados['id_coop']}");
            return $dados['id_coop'];
        } else {
            return FALSE;
        }
    }

    public static function save(array $dados) {
        /* metodo que atualiza ou insere no banco
         * Autor: Leonardo
         * Arquivos que utilizam:
         * - form_cooperativa.php
         * - ver_cooperativa.php
         * - cooperativa_nova.php     */

        if (isset($dados['id_coop']) && !empty($dados['id_coop'])) {
            return cooperativa::update($dados);
        } else {
            return cooperativa::insert($dados);
        }
    }

}

/* ARQUIVOS EXECUTANDO ESTA ROTINA
  - ESCALA.PHP
  - PONTO.PHP
 */
?>