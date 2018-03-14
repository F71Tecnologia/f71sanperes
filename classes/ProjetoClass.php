<?php
/**
 * Description of BotoesClass
 *
 * @author Ramon Lima
 */
class ProjetoClass {
   
    public function getProjetos($regiao=null) {
        if ($regiao === null) {
            $usuario = carregaUsuario();
        } else {
            $usuario['id_regiao'] = $regiao;
        }
        
        $qrProjeto = "SELECT * 
                        FROM projeto
                        WHERE id_regiao = {$usuario['id_regiao']}
                        GROUP BY nome";
        $rsProjeto = mysql_query($qrProjeto);
        $projetos = array();
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos[] = $row;
        }
        return $projetos;
    }
   
    public function getProjetosWebService($regiao=null) {
        if ($regiao === null) {
            $usuario = carregaUsuario();
        } else {
            $usuario['id_regiao'] = $regiao;
        }
        
        $qrProjeto = "SELECT * 
                        FROM projeto
                        WHERE id_regiao = '{$usuario['id_regiao']}' AND id_projeto IN(3303,3304,3320,3338)
                        GROUP BY nome";
        $rsProjeto = mysql_query($qrProjeto);
        $projetos = array();
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos[] = $row;
        }
        return $projetos;
    }
    
    public function getProjeto($projeto){
        $qrProjeto = "SELECT * 
                        FROM projeto
                        WHERE id_projeto = {$projeto}";
        $rsProjeto = mysql_query($qrProjeto);
        $projetos = "";
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos = $row;
        }
        return $projetos;
    }
    
    public function getNome($projeto){
        $qrProjeto = "SELECT nome FROM projeto WHERE id_projeto = {$projeto} LIMIT 1";
        $rsProjeto = mysql_fetch_assoc(mysql_query($qrProjeto));
        $nome = $rsProjeto['nome'];
        return $nome;
    }
    
    public function getSubprojetos($projeto){
        $qrProjeto = "SELECT * FROM subprojeto WHERE id_projeto='$projeto' AND status_reg = '1' ORDER BY termino DESC";
        $rsProjeto = mysql_query($qrProjeto);
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos[] = $row;
        }
        return $projetos;
    }
    
    public function getProjetosMaster(){
        $usuario = carregaUsuario();
        $qrProjeto = "SELECT * FROM projeto WHERE status_reg = '1' AND id_master = '{$usuario['id_master']}' ORDER BY nome";
        $rsProjeto = mysql_query($qrProjeto);
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos[] = $row;
        }
        return $projetos;
    }
    
    public function getProjetosUser($regioes){
        $usuario = carregaUsuario();
        $qrProjeto = "SELECT * FROM projeto WHERE status_reg = '1' AND id_regiao IN ({$regioes}) ORDER BY nome";
        $rsProjeto = mysql_query($qrProjeto);
        while ($row = mysql_fetch_assoc($rsProjeto)) {
            $projetos[] = $row;
        }
        return $projetos;
    }
    
}
