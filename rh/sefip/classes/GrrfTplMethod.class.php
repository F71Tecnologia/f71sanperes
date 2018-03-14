<?php

class GrrfTplMethod extends IGrrfTplMethod {

    function download() {

        if ($download) {
            $file = $_GET['download'];
            $dirFile = 'arquivos/grrf/' . $file;
            header("Content-Type: application/save");
            header("Content-Length:" . filesize($dirFile));
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header("Content-Transfer-Encoding: binary");
            header('Expires: 0');
            header('Pragma: no-cache');
            $fp = fopen("$dirFile", "r");
            fpassthru($fp);
            fclose($fp);
            exit();
        }
    }

    function loadUser() {
        $this->usuario = carregaUsuario();
    }

    function loadClt($idClt) {

        $daoClt = new DaoClt();
        
        $this->clt = new Clt();
        $this->clt->setIdClt($idClt);
        
        $row_clt = $daoClt->buscaClt($this->clt);

        $regiao = new Regiao();
        $regiao->setIdRegiao($row_clt['id_regiao']);

        $projeto = new Projeto();
        $projeto->setIdProjeto($row_clt['id_projeto']);

        $curso = new Curso();
        $curso->setIdCurso($row_clt['id_curso']);

        $row_cbo = $daoClt->getCbo($this->clt);

        $this->clt->setRegiao($regiao);
        $this->clt->setProjeto($projeto);
        $this->clt->setCurso($curso);
        $this->clt->setCbo($row_cbo['cod']);
    }

    function loadEmpresa() {
        $row_empresa = $daoClt->buscaEmpresa($this->clt);
    }

    function loadGrrf($dataRecolhimento, $mes, $ano, $valorBaseInformado) {
        $this->grrf = new Grrf();
        $this->grrf->setDataRecolhimento($dataRecolhimento)
                ->setMes($mes)
                ->setAno($ano)
                ->setClt($this->clt)
                ->setValorBaseInformado($valorBaseInformado);

        $dao = new DaoClt();
        $row_rescisao = $dao->buscaRescisao($this->clt);
        $row_rescisao = $row_rescisao[0]; // gambi ??
        
        $rescisao = new Rescisao();
        $rescisao->setCodMovimentacao($row_rescisao['cod_movimentacao']);
        $rescisao->setDataDemi($row_rescisao['data_demi']);
        $rescisao->setCodSaque($row_rescisao['codigo_saque']);
        $rescisao->setAvisoCodigo($row_rescisao['aviso_codigo']);
        $rescisao->setDataAviso($row_rescisao['data_aviso']);
        $rescisao->setAvisoValor($row_rescisao['terceiro_ss']);
        
        $this->clt->setRescisao($rescisao);
//        $this->grrf->setClt($this->clt);
    }

}