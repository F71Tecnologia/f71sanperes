<?php
error_reporting(E_ALL);

include ('../conn.php');

class Relatorio {

    public $funcionario;
    public $projeto;
    public $regiao;
    public $documento;
    public $pde;

    public function __construct($funcionario, $regiao, $projeto) {
        $this->funcionario = $funcionario;
        $this->regiao = $regiao;
        $this->projeto = $projeto;

        $query = "SELECT pde FROM rh_clt WHERE id_clt = $funcionario";
        $x = mysql_fetch_assoc(mysql_query($query));
        $this->pde = $x['pde'];
    }

    public function setDocumento($documento) {
        $this->documento = $documento;

        if (method_exists($this, $documento)) {
            $this->conteudo = $this->$documento();
        } else {
            $this->conteudo = "<div><h3>Documento não encontrado</h3></div>";
        }

        /* switch($this->documento) {
          case 'kit':
          $this->conteudo = $this->kit();
          break;
          case 'ctps_impressao':
          $this->conteudo = $this->ctps_impressao();
          break;
          case 'contrato_experiencia':
          $this->conteudo = $this->contrato_experiencia();
          break;
          case 'termo_sigilo_confidencialidade':
          $this->conteudo = $this->termo_sigilo_confidencialidade();
          break;
          case 'termo_responsabilidade':
          $this->conteudo = $this->termo_responsabilidade();
          break;
          case 'declaracao_dependentes':
          $this->conteudo = $this->declaracao_dependentes();
          break;
          case 'recibo_entrega_ctps':
          $this->conteudo = $this->recibo_entrega_ctps();
          break;
          case 'acordo_compensacao':
          $this->conteudo = $this->acorco_compensacao();
          case 'contrato_prazo_det':
          $this->conteudo = $this->contrato_prazo_det();
          break;
          } */
    }

    public function kit() {
        $this->titulo = 'Kit Admisional';
        if ($this->pde == 1) {
            return $this->contrato_prazo_det() . $this->acordo_compensacao() . $this->recibo_entrega_ctps() . $this->ctps_impressao() . $this->termo_sigilo_confidencialidade() . $this->declaracao_dependentes() . $this->termo_responsabilidade();
        } else {
            return $this->contrato_experiencia() . $this->acordo_compensacao() . $this->recibo_entrega_ctps() . $this->ctps_impressao() . $this->termo_sigilo_confidencialidade() . $this->declaracao_dependentes() . $this->termo_responsabilidade();
        }
    }

    public function ctps_impressao() {
        $this->titulo = 'ImpressÃ£o de CTPS';
        return $this->pegar_conteudo('ctps_impressao.php');
    }

    public function contrato_prazo_det() {
        $this->titulo = 'Contrato de ExperiÃªncia';
        return $this->pegar_conteudo('contrato_prazo_det.php');
    }

    public function contrato_experiencia() {
        $this->titulo = 'Contrato de ExperiÃªncia';
        return $this->pegar_conteudo('contrato_experiencia.php');
    }

    public function termo_sigilo_confidencialidade() {
        $this->titulo = 'Termo de Sigilo de Confidencialidade';
        return $this->pegar_conteudo('termo_sigilo_confidencialidade.php');
    }

    public function termo_responsabilidade() {
        $this->titulo = 'Termo de Responsabilidade';
        return $this->pegar_conteudo('termo_responsabilidade.php');
    }

    public function declaracao_dependentes() {
        $this->titulo = 'DeclaraÃ§Ã£o de Dependentes';
        return $this->pegar_conteudo('declaracao_dependentes.php');
    }

    public function recibo_entrega_ctps() {
        $this->titulo = 'Recibo de Entrega de CTPS';
        return $this->pegar_conteudo('recibo_entrega_ctps.php');
    }

    public function acordo_compensacao() {
        $this->titulo = 'Acordo de Compensação de Horas de Trabalho';
        return $this->pegar_conteudo('acordo_compensacao.php');
    }

    public function rescisao_termo() {
        $this->titulo = 'Acordo de Compensação de Horas de Trabalho';
        return $this->pegar_conteudo('acordo_compensacao.php');
    }
    
    public function aviso_previo_trabalhado(){
        $this->titulo = 'Aviso Prévio Trabalhado';
        return $this->pegar_conteudo('../rh/docs/rescisao/1_aviso_previo_trabalhado.php');
    }
    
     public function contrato_aprendizagem(){
        $this->titulo = 'Contrato de Aprendizagem';
        return $this->pegar_conteudo('contrato_aprendizagem.php');
    }

    public function aviso_previo_indenizado(){
        $this->titulo = 'Aviso Prévio Indenizado';
        return $this->pegar_conteudo('../rh/docs/rescisao/2_aviso_previo_indenizado.php');
    }
    
    public function interrupcao_contrato_experiencia(){
        $this->titulo = 'Interrupcao do Contrato de Experiência';
        return $this->pegar_conteudo('../rh/docs/rescisao/3_interrupcao_contrato_experiencia.php');
    }
    
    public function termino_contrato_experiencia(){
        $this->titulo = 'Termino do Contrato de Experiência';
        return $this->pegar_conteudo('../rh/docs/rescisao/4_termino_contrato_experiencia.php');
    }
    
    public function pedido_demissao_descontando_aviso(){
        $this->titulo = 'Pedido de Demissão Descontando o Aviso';
        return $this->pegar_conteudo('../rh/docs/rescisao/5_pedido_demissao_descontando_aviso.php');
    }
    
    public function pedido_demissao_aviso_trabalhado(){
        $this->titulo = 'Pedido de Demissão Aviso Trabalhado';
        return $this->pegar_conteudo('../rh/docs/rescisao/6_pedido_demissao_aviso_trabalhado.php');
    }
    
    public function pedido_demissao_interrupcao_contrato(){
        $this->titulo = 'Pedido de Demissão com Interrupção do Contrato';
        return $this->pegar_conteudo('../rh/docs/rescisao/7_pedido_demissao_interrupcao_contrato.php');
    }
    
    public function pegar_conteudo($arquivo) {
        ob_start();
        echo "<div class='text-cont' style='text-justify: inter-word!important;'>";
        include $arquivo;
        echo "</div>";
        return ob_get_clean();
    }

    public function cabecalho() {
        return '
		<!DOCTYPE html>
                    <html lang="pt">
                        <head>
                            <title>:: Intranet :: ' . $this->titulo . '</title>
                            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                            <link rel="shortcut icon" href="../favicon.ico">
                            <style>
                                * { margin: 0; padding: 0; }
                            </style>
                            <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
                            <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
                            <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
                            <link href="../resources/css/style-print.css" rel="stylesheet">
                            <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
                            <script src="../resources/js/print.js" type="text/javascript"></script>
                            <style>
                                .text-cont{ 
                                    font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif!important;
                                    color: #000000!important; 
                                    font-size: 11pt!important; 
                                    text-align: justify!important;
                                    text-justify: inter-word!important;
                                }
                                .titulo_documento { text-align: center!important; font-weight: bold!important;}
                                
                                .pagina{
                                    font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif!important;
                                    color: #000000!important; 
                                    font-size: 11pt!important; 
                                    text-align: justify!important;
                                    text-justify: inter-word!important;
                                }
                            </style>
                        </head>
                        <body>
		';
    }

    public function botao() {
        return '
		<div class="no-print">
                    <nav class="navbar navbar-default navbar-fixed-top">
                        <div class="container-fluid">
                            <!--div class="navbar-header">
                                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                            </div-->
                            <!--div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3"-->
                                <div class="text-center"> 
                                    <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                                    <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
                                </div>
                            <!--/div-->
                        </div>
                    </nav>
                </div>
		    ';
    }

    public function rodape() {
        return '
			</body>
		</html>
		';
    }

    public function formato_brasileiro($data) {
        return implode('/', array_reverse(explode('-', $data)));
    }

    public function gerar() {
        return
            $this->cabecalho() .
            $this->botao() .
            $this->conteudo .
            $this->rodape();
    }

}

$relatorio = new Relatorio($_GET['clt'], $_GET['reg'], $_GET['pro']);
$relatorio->setDocumento($_GET['documento']);
echo $relatorio->gerar();
?>
