<?php
error_reporting(E_ALL);

include "../../../conn.php";
include("../../../funcoes.php");
include("../../../wfunction.php");
include("../../../classes/LogClass.php");
include("../../../classes/uploadfile.php");
include("../../../empresa.php");
include("../prestadores/PrestadorServicoClass.php");
include("../prestadores/PrestadorDocumentosClass.php");
include("../prestadores/PrestadorTipoDocClass.php");
//include("../prestadores/PrestadorDependenteClass.php");
//include("../prestadores/PrestadorSocioClass.php");
include("../prestadores/ImpostoAssocClass.php");
include("../../../classes/ContabilEmpresaClass.php");
include("../../../classes/ContabilImpostosClass.php");

$charset = mysql_set_charset('utf8');

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : NULL;
$usuario = carregaUsuario();

$log = new Log();
$objPrestador = new PrestadorServicoClass();
$objPrestadorDocumentos = new PrestadorDocumentosClass();
$objPrestadorTipoDoc = new PrestadorTipoDocClass();
//$objSocio = new SocioClass();
//$objDependente = new PrestadorDependenteClass();
$objImpostoAssoc = new ImpostoAssoc();

$objEmpresa = new ContabilEmpresa();

if (!empty($_REQUEST['id_prestador'])) {
    $objPrestador->setId_prestador($_REQUEST['id_prestador']);
    if ($objPrestador->getPrestador()) {
        $objPrestador->getRowPrestador();
    }
}

switch ($action) {

    case 'ver_documentos' :
        ?>

        <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
            <thead>
                <tr>
                    <th class="text-center">Documento</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-center">A&ccedil;&atilde;o</th>
                </tr>
                <?php
                $objPrestadorTipoDoc->getTipoDocumento();
                while ($objPrestadorTipoDoc->getRowPrestadorTipoDoc()) {
                    $objPrestadorDocumentos->setId_prestador($objPrestador->getId_prestador());
                    $objPrestadorDocumentos->setPrestador_tipo_doc_id($objPrestadorTipoDoc->getPrestador_tipo_doc_id());
                    $objPrestadorDocumentos->getDocumentoPrestador();
                    ?>
                    <tr>
                        <td class="no-padding-vr"><?= $objPrestadorTipoDoc->getPrestador_tipo_doc_nome() ?></td>
                        <td class="text-center no-padding-vr"><?= $objPrestadorDocumentos->getNumRowPrestadorDocumentos() ?></td>
                        <td class="text-center no-padding-vr">
                            <button class="btn-info ger_anexos" data-doc="<?= $objPrestadorTipoDoc->getPrestador_tipo_doc_id() ?>" data-key="<?= $objPrestador->getId_prestador() ?>">
                                <i class="fa fa-paperclip"></i>
                            </button>
                        </td>
                    </tr>
                <?php } ?>
            </thead>
        </table>

        <?php
        break;

    case 'ger_anexos' :
        ?>

        <table class="table table-condensed table-bordered table-hover text-sm valign-middle">
            <thead>
                <tr>
                    <th class="text-center">Documento</th>
                    <th class="text-center">Data</th>
                    <th class="text-center">A&ccedil;&atilde;o</th>
                </tr>
                <?php
                $objPrestadorDocumentos->setPrestador_tipo_doc_id($_REQUEST['id_documento']);
                $objPrestadorDocumentos->setId_prestador($objPrestador->getId_prestador());
                $objPrestadorDocumentos->getDocumentoPrestador();
                while ($objPrestadorDocumentos->getRowPrestadorDocumentos()) {
                    $extensao = str_replace('.', '', $objPrestadorDocumentos->getExtensao_arquivo());
                    if ($extensao == 'pdf') {
                        $text_color = 'text-danger';
                        $text = '-pdf';
                    } else if ($extensao == 'doc' || $extensao == 'docx') {
                        $text_color = 'text-info';
                        $text = '-word';
                    } else if ($extensao == 'xls' || $extensao == 'xlsx') {
                        $text_color = 'text-success';
                        $text = '-excel';
                    } else {
                        $text_color = '';
                        $text = '-image';
                    }

                    if ($objPrestadorDocumentos->getData_vencimento("Y-m-d") < date("Y-m-d")) {
                        $status_color = 'text-danger';
                    } else if ($objPrestadorDocumentos->getData_vencimento("Y-m-d") == date("Y-m-d")) {
                        $status_color = 'text-warning';
                    } else {
                        $status_color = '';
                    }
                    ?>

                    <tr class="">
                        <td class="text-center">
                            <a class="btn btn-xs btn-default <?= $status_color ?>" href="/intranet/processo/prestador_documentos/<?= $objPrestadorDocumentos->getNome_arquivo() . $objPrestadorDocumentos->getExtensao_arquivo() ?>" target="_blanc"><i class="<?= $text_color ?> fa fa-file<?= $text ?>-o"></i> <?= $objPrestadorDocumentos->getNome_arquivo() ?></a>
                        </td>
                        <td class="text-center" style="width: 100px;">
                            <div class="col-sm-12 no-padding-hr"><input type="text" id="data_<?= $objPrestadorDocumentos->getPrestador_documento_id() ?>" class="data form-control input-sm text-center no-padding-hr" disabled data-data="<?= $objPrestadorDocumentos->getData_vencimento('d/m/Y') ?>" value="<?= $objPrestadorDocumentos->getData_vencimento('d/m/Y') ?>"></div>
                            <div class="col-sm-12 no-padding-hr hide">
                                <button class="btn btn-success badge margin_t5 salvar_edicao" data-prestador="<?= $objPrestadorDocumentos->getId_prestador() ?>" data-documento="<?= $objPrestadorDocumentos->getPrestador_documento_id() ?>" data-toggle="tooltip" data-original-title="Salvar Edição"><i class="fa fa-check"></i></button>
                                <button class="btn btn-danger badge margin_t5 cancelar_edicao" data-toggle="tooltip" data-original-title="Cancelar Edição"><i class="fa fa-close"></i></button>
                            </div>
                        </td>
                        <td class="text-center" style="width: 70px;">
                            <button class="btn btn-warning btn-xs editar_documento" data-toggle="tooltip" data-original-title="Editar Documento" data-doc="<?= $objPrestadorDocumentos->getPrestador_documento_id() ?>"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-danger btn-xs excluir_documento" data-toggle="tooltip" data-original-title="Excluir Documento" data-documento="<?= $objPrestadorDocumentos->getPrestador_documento_id() ?>"><i class="fa fa-trash-o"></i></button>
                        </td>
                    </tr>
                <?php } ?>
            </thead>
        </table>
        <div class="panel panel-default">
            <div class="panel-body text-right">
                <button class="btn btn-xs btn-success novo-documento"><i class="fa fa-paperclip"></i> Novo Documento</button>
                <button class="btn btn-xs btn-danger novo-documento" style="display: none;"><i class="fa fa-ban"></i> Cancelar Documento</button>
            </div>
            <div class="panel-footer div-novo-documento" style="display: none;">
                <label class="control-label">Data Vencimento: </label>
                <input type="text" class="data form-control" id="data_vencimento" name="data_vencimento">

                <div id="dropzone" class="dropzone margin_t20" style="min-height: 150px;"></div>

                <div class="text-right margin_t20">
                    <button class="btn btn-sm btn-primary botaoSubmit"><i class="fa fa-save"></i> Salvar</button>
                    <div>
                    </div>
                </div>

                <script>
                    $(function () {
                        Dropzone.autoDiscover = false;
                        var myDropzone = new Dropzone("#dropzone", {
                            url: "/intranet/admin/actions/action_prestadores.php?action=upload_anexo&id_prestador=<?= $objPrestadorDocumentos->getId_prestador() ?>&id_tipo_documento=<?= $objPrestadorDocumentos->getPrestador_tipo_doc_id() ?>",
                            addRemoveLinks: true,
                            maxFilesize: 10,
                            autoQueue: false,
                            dictResponseError: "Erro no servidor!",
                            dictCancelUpload: "Cancelar",
                            dictFileTooBig: "Tamanho máximo: 10MB",
                            dictRemoveFile: "Remover Arquivo",
                            canceled: "Arquivo Cancelado",
                            acceptedFiles: '.jpg,.gif,.png,.pdf,.JPG,.GIF,.PNG,.PDF'
                                    //                    , sending: function(file, xhr, formData) {
                                    //                        formData.append("frids", "value"); // Append all the additional input data of your form here!
                                    //                    }
                                    //                    , success: function(file, responseText){
                                    //                        console.log(responseText);
                                    //                        //$('.close').trigger('click');
                                    //                    }
                        });

                        $(".botaoSubmit").on('click', function () {

                            myDropzone.on('sending', function (file, xhr, formData) {
                                formData.append("data_vencimento", $("#data_vencimento").val()); // Append all the additional input data of your form here!
                            });
                            myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));

                        });

                        $('.data').datepicker({
                            dateFormat: 'dd/mm/yy',
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '2005:c+1',
                            beforeShow: function () {
                                setTimeout(function () {
                                    $('.ui-datepicker').css('z-index', 5010);
                                }, 0);
                            }
                        });

                        $("[data-toggle='tooltip']").tooltip();
                    })
                </script>
                <?php
                break;

    case 'salvar_edicao' :

        $objPrestadorDocumentos->setData_vencimento(implode('-', array_reverse(explode('/', $_REQUEST['data']))));
        $objPrestadorDocumentos->setPrestador_documento_id($_REQUEST['id_documento']);
        $objPrestadorDocumentos->updatePrestadorDocumentos();

        break;

    case 'editar_prestador' :

        $objPrestador->setId_regiao($_REQUEST['id_regiao']);
        $objPrestador->setId_projeto($_REQUEST['id_projeto']);
        $objPrestador->setId_medida($_REQUEST['id_medida']);
        $objPrestador->setContratado_por($_REQUEST['contratado_por']);
        $objPrestador->setContratado_em(implode('-', array_reverse(explode('/', $_REQUEST['contratado_em']))));
        $objPrestador->setEncerrado_por($_REQUEST['encerrado_por']);
        $objPrestador->setEncerrado_em(implode('-', array_reverse(explode('/', $_REQUEST['encerrado_em']))));
        $objPrestador->setContratante($_REQUEST['contratante']);
        $objPrestador->setNumero($_REQUEST['numero']);
        $objPrestador->setEndereco($_REQUEST['endereco']);
        $objPrestador->setCnpj($_REQUEST['cnpj']);
        $objPrestador->setResponsavel($_REQUEST['responsavel']);
        $objPrestador->setCivil($_REQUEST['civil']);
        $objPrestador->setNacionalidade($_REQUEST['nacionalidade']);
        $objPrestador->setFormacao($_REQUEST['formacao']);
        $objPrestador->setRg($_REQUEST['rg']);
        $objPrestador->setCpf($_REQUEST['cpf']);
        $objPrestador->setC_fantasia($_REQUEST['c_fantasia']);
        $objPrestador->setC_razao($_REQUEST['c_razao']);
        $objPrestador->setC_endereco($_REQUEST['c_endereco']);
        $objPrestador->setC_cnpj($_REQUEST['c_cnpj']);
        $objPrestador->setC_ie($_REQUEST['c_ie']);
        $objPrestador->setC_im($_REQUEST['c_im']);
        $objPrestador->setC_tel($_REQUEST['c_tel']);
        $objPrestador->setC_fax($_REQUEST['c_fax']);
        $objPrestador->setC_email($_REQUEST['c_email']);
        $objPrestador->setC_responsavel($_REQUEST['c_responsavel']);
        $objPrestador->setC_civil($_REQUEST['c_civil']);
        $objPrestador->setC_nacionalidade($_REQUEST['c_nacionalidade']);
        $objPrestador->setC_formacao($_REQUEST['c_formacao']);
        $objPrestador->setC_rg($_REQUEST['c_rg']);
        $objPrestador->setC_cpf($_REQUEST['c_cpf']);
        $objPrestador->setC_email2($_REQUEST['c_email2']);
        $objPrestador->setC_site($_REQUEST['c_site']);
        $objPrestador->setCo_responsavel($_REQUEST['co_responsavel']);
        $objPrestador->setCo_tel($_REQUEST['co_tel']);
        $objPrestador->setCo_fax($_REQUEST['co_fax']);
        $objPrestador->setCo_civil($_REQUEST['co_civil']);
        $objPrestador->setCo_nacionalidade($_REQUEST['co_nacionalidade']);
        $objPrestador->setCo_email($_REQUEST['co_email']);
        $objPrestador->setCo_municipio($_REQUEST['co_municipio']);
        $objPrestador->setAssunto($_REQUEST['assunto']);
        $objPrestador->setObjeto($_REQUEST['objeto']);
        $objPrestador->setEspecificacao($_REQUEST['especificacao']);
        $objPrestador->setValor($_REQUEST['valor']);
        $objPrestador->setData(implode('-', array_reverse(explode('/', $_REQUEST['data']))));
        $objPrestador->setData_proc(implode('-', array_reverse(explode('/', $_REQUEST['data_proc']))));
        $objPrestador->setAcompanhamento($_REQUEST['acompanhamento']);
        $objPrestador->setImprimir($_REQUEST['imprimir']);
        $objPrestador->setPrestador_tipo($_REQUEST['prestador_tipo']);
        $objPrestador->setC_data_nascimento(implode('-', array_reverse(explode('/', $_REQUEST['c_data_nascimento']))));
        $objPrestador->setCo_responsavel_socio1($_REQUEST['co_responsavel_socio1']);
        $objPrestador->setCo_tel_socio1($_REQUEST['co_tel_socio1']);
        $objPrestador->setCo_fax_socio1($_REQUEST['co_fax_socio1']);
        $objPrestador->setCo_civil_socio1($_REQUEST['co_civil_socio1']);
        $objPrestador->setCo_nacionalidade_socio1($_REQUEST['co_nacionalidade_socio1']);
        $objPrestador->setCo_email_socio1($_REQUEST['co_email_socio1']);
        $objPrestador->setCo_municipio_socio1($_REQUEST['co_municipio_socio1']);
        $objPrestador->setData_nasc_socio1(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_socio1']))));
        $objPrestador->setCo_responsavel_socio2($_REQUEST['co_responsavel_socio2']);
        $objPrestador->setCo_tel_socio2($_REQUEST['co_tel_socio2']);
        $objPrestador->setCo_fax_socio2($_REQUEST['co_fax_socio2']);
        $objPrestador->setCo_civil_socio2($_REQUEST['co_civil_socio2']);
        $objPrestador->setCo_nacionalidade_socio2($_REQUEST['co_nacionalidade_socio2']);
        $objPrestador->setCo_email_socio2($_REQUEST['co_email_socio2']);
        $objPrestador->setCo_municipio_socio2($_REQUEST['co_municipio_socio2']);
        $objPrestador->setData_nasc_socio2(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_socio2']))));
        $objPrestador->setNome_banco($_REQUEST['nome_banco']);
        $objPrestador->setAgencia($_REQUEST['agencia']);
        $objPrestador->setConta($_REQUEST['conta']);
        $objPrestador->setValor_limite($_REQUEST['valor_limite']);
        $objPrestador->setId_compra($_REQUEST['id_compra']);
        $objPrestador->setPrestacao_contas($_REQUEST['prestacao_contas']);
        $objPrestador->setEspecialidade($_REQUEST['especialidade']);
        $objPrestador->setC_cep($_REQUEST['c_cep']);
        $objPrestador->setC_id_tp_logradouro($_REQUEST['c_id_tp_logradouro']);
        $objPrestador->setC_numero($_REQUEST['c_numero']);
        $objPrestador->setC_complemento($_REQUEST['c_complemento']);
        $objPrestador->setC_bairro($_REQUEST['c_bairro']);
        $objPrestador->setC_uf($_REQUEST['c_uf']);
        $objPrestador->setC_cod_cidade($_REQUEST['c_cod_cidade']);
        $objPrestador->setIdContabilEmpresa($_REQUEST['id_empresa']);
        $objPrestador->setId_cnae($_REQUEST['cnae']);

        $objPrestador->updatePrestador();

//                foreach ($_REQUEST['socio']['nome'] as $key => $value) {
//                    if (!empty($value)) {
//                        $objSocio->setNome($_REQUEST['socio']['nome'][$key]);
//                        $objSocio->setTel($_REQUEST['socio']['tel'][$key]);
//                        $objSocio->setCpf($_REQUEST['socio']['cpf'][$key]);
//                        $objSocio->setIdPrestador($objPrestador->getId_prestador());
//                        if (!empty($_REQUEST['socio']['id_socio'][$key])) {
//                            $objSocio->setIdSocio($_REQUEST['socio']['id_socio'][$key]);
//                            $objSocio->updateSocio();
//                            //echo "UPATE socio SET nome = '{$_REQUEST['socio']['nome'][$key]}', tel = '{$_REQUEST['socio']['tel'][$key]}', cpf = '{$_REQUEST['socio']['cpf'][$key]}' WHERE id_socio = '{$_REQUEST['socio']['id_socio'][$key]}' LIMIT 1;<br>";
//                        } else {
//                            $objSocio->insertSocio();
//                            //echo "INSERT INTO socio (nome, tel, cpf) VALUES ('{$_REQUEST['socio']['nome'][$key]}','{$_REQUEST['socio']['tel'][$key]}','{$_REQUEST['socio']['cpf'][$key]}');<br>";
//                        }
//                    }
//                }
//
//                foreach ($_REQUEST['dependente']['nome'] as $key => $value) {
//                    if (!empty($value)) {
//                        $objDependente->setNome($_REQUEST['dependente']['nome'][$key]);
//                        $objDependente->setTel($_REQUEST['dependente']['tel'][$key]);
//                        $objDependente->setParentesco($_REQUEST['dependente']['parentesco'][$key]);
//                        $objDependente->setIdPrestador($objPrestador->getId_prestador());
//                        $objDependente->setStatus(1);
//                        
//                        if (!empty($_REQUEST['dependente']['id_dependente'][$key])) {
//                            $objDependente->setIdDependente($_REQUEST['dependente']['id_dependente'][$key]);
//                            $objDependente->updatePrestadorDependente();
//                            //echo "UPATE dependente SET nome = '{$_REQUEST['dependente']['nome'][$key]}', tel = '{$_REQUEST['dependente']['tel'][$key]}', parentesco = '{$_REQUEST['dependente']['parentesco'][$key]}' WHERE id_socio = '{$_REQUEST['dependente']['id_dependente'][$key]}' LIMIT 1;<br>";
//                        } else {
//                            $objDependente->insertPrestadorDependente();
//                            //echo "INSERT INTO dependente (nome, tel, parentesco) VALUES ('{$_REQUEST['dependente']['nome'][$key]}','{$_REQUEST['dependente']['tel'][$key]}','{$_REQUEST['dependente']['parentesco'][$key]}')<br>";
//                        }
//                    }
//                }
        
        foreach ($_REQUEST['imposto']['id_imposto'] as $key => $value) {
            if (!empty($value)) {
                $objImpostoAssoc->setidImposto($_REQUEST['imposto']['id_imposto'][$key]);
                $objImpostoAssoc->setAliquota($_REQUEST['imposto']['aliquota'][$key]);
                $objImpostoAssoc->setIdContrato($objPrestador->getId_prestador());
                if (!empty($_REQUEST['imposto']['id_assoc'][$key])) {
                    $objImpostoAssoc->setIdAssoc($_REQUEST['imposto']['id_assoc'][$key]);
                    $objImpostoAssoc->updateImpostoAssoc();
                    //echo "UPATE dependente SET nome = '{$_REQUEST['dependente']['nome'][$key]}', tel = '{$_REQUEST['dependente']['tel'][$key]}', parentesco = '{$_REQUEST['dependente']['parentesco'][$key]}' WHERE id_socio = '{$_REQUEST['dependente']['id_dependente'][$key]}' LIMIT 1;<br>";
                } else {
                    $objImpostoAssoc->insertImpostoAssoc();
                    //echo "INSERT INTO dependente (nome, tel, parentesco) VALUES ('{$_REQUEST['dependente']['nome'][$key]}','{$_REQUEST['dependente']['tel'][$key]}','{$_REQUEST['dependente']['parentesco'][$key]}')<br>";
                }
            }
        }

        //print_array($_REQUEST);
        //$objPrestador->updatePrestador();
        header("Location: ../prestadores");

        break;

    case 'cadastrar_prestador' :

//                print_array($_REQUEST);
//                exit();

        $objPrestador->setId_regiao($_REQUEST['id_regiao']);
        $objPrestador->setId_projeto($_REQUEST['id_projeto']);
        $objPrestador->setId_medida($_REQUEST['id_medida']);
        $objPrestador->setAberto_por($usuario['id_funcionario']);
        $objPrestador->setAberto_em(date("Y-m-d"));
        $objPrestador->setContratado_por($_REQUEST['contratado_por']);
        $objPrestador->setContratado_em(implode('-', array_reverse(explode('/', $_REQUEST['contratado_em']))));
        $objPrestador->setEncerrado_por($_REQUEST['encerrado_por']);
        $objPrestador->setEncerrado_em(implode('-', array_reverse(explode('/', $_REQUEST['encerrado_em']))));
        $objPrestador->setContratante($_REQUEST['contratante']);
        $objPrestador->setNumero($_REQUEST['numero']);
        $objPrestador->setEndereco($_REQUEST['endereco']);
        $objPrestador->setCnpj($_REQUEST['cnpj']);
        $objPrestador->setResponsavel($_REQUEST['responsavel']);
        $objPrestador->setCivil($_REQUEST['civil']);
        $objPrestador->setNacionalidade($_REQUEST['nacionalidade']);
        $objPrestador->setFormacao($_REQUEST['formacao']);
        $objPrestador->setRg($_REQUEST['rg']);
        $objPrestador->setCpf($_REQUEST['cpf']);
        $objPrestador->setC_fantasia($_REQUEST['c_fantasia']);
        $objPrestador->setC_razao($_REQUEST['c_razao']);
        $objPrestador->setC_endereco($_REQUEST['c_endereco']);
        $objPrestador->setC_cnpj($_REQUEST['c_cnpj']);
        $objPrestador->setC_ie($_REQUEST['c_ie']);
        $objPrestador->setC_im($_REQUEST['c_im']);
        $objPrestador->setC_tel($_REQUEST['c_tel']);
        $objPrestador->setC_fax($_REQUEST['c_fax']);
        $objPrestador->setC_email($_REQUEST['c_email']);
        $objPrestador->setC_responsavel($_REQUEST['c_responsavel']);
        $objPrestador->setC_civil($_REQUEST['c_civil']);
        $objPrestador->setC_nacionalidade($_REQUEST['c_nacionalidade']);
        $objPrestador->setC_formacao($_REQUEST['c_formacao']);
        $objPrestador->setC_rg($_REQUEST['c_rg']);
        $objPrestador->setC_cpf($_REQUEST['c_cpf']);
        $objPrestador->setC_email2($_REQUEST['c_email2']);
        $objPrestador->setC_site($_REQUEST['c_site']);
        $objPrestador->setCo_responsavel($_REQUEST['co_responsavel']);
        $objPrestador->setCo_tel($_REQUEST['co_tel']);
        $objPrestador->setCo_fax($_REQUEST['co_fax']);
        $objPrestador->setCo_civil($_REQUEST['co_civil']);
        $objPrestador->setCo_nacionalidade($_REQUEST['co_nacionalidade']);
        $objPrestador->setCo_email($_REQUEST['co_email']);
        $objPrestador->setCo_municipio($_REQUEST['co_municipio']);
        $objPrestador->setAssunto($_REQUEST['assunto']);
        $objPrestador->setObjeto($_REQUEST['objeto']);
        $objPrestador->setEspecificacao($_REQUEST['especificacao']);
        $objPrestador->setValor($_REQUEST['valor']);
        $objPrestador->setData(implode('-', array_reverse(explode('/', $_REQUEST['data']))));
        $objPrestador->setData_proc(implode('-', array_reverse(explode('/', $_REQUEST['data_proc']))));
        $objPrestador->setAcompanhamento($_REQUEST['acompanhamento']);
        $objPrestador->setImprimir($_REQUEST['imprimir']);
        $objPrestador->setStatus(1);
        $objPrestador->setPrestador_tipo($_REQUEST['prestador_tipo']);
        $objPrestador->setC_data_nascimento(implode('-', array_reverse(explode('/', $_REQUEST['c_data_nascimento']))));
        $objPrestador->setCo_responsavel_socio1($_REQUEST['co_responsavel_socio1']);
        $objPrestador->setCo_tel_socio1($_REQUEST['co_tel_socio1']);
        $objPrestador->setCo_fax_socio1($_REQUEST['co_fax_socio1']);
        $objPrestador->setCo_civil_socio1($_REQUEST['co_civil_socio1']);
        $objPrestador->setCo_nacionalidade_socio1($_REQUEST['co_nacionalidade_socio1']);
        $objPrestador->setCo_email_socio1($_REQUEST['co_email_socio1']);
        $objPrestador->setCo_municipio_socio1($_REQUEST['co_municipio_socio1']);
        $objPrestador->setData_nasc_socio1(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_socio1']))));
        $objPrestador->setCo_responsavel_socio2($_REQUEST['co_responsavel_socio2']);
        $objPrestador->setCo_tel_socio2($_REQUEST['co_tel_socio2']);
        $objPrestador->setCo_fax_socio2($_REQUEST['co_fax_socio2']);
        $objPrestador->setCo_civil_socio2($_REQUEST['co_civil_socio2']);
        $objPrestador->setCo_nacionalidade_socio2($_REQUEST['co_nacionalidade_socio2']);
        $objPrestador->setCo_email_socio2($_REQUEST['co_email_socio2']);
        $objPrestador->setCo_municipio_socio2($_REQUEST['co_municipio_socio2']);
        $objPrestador->setData_nasc_socio2(implode('-', array_reverse(explode('/', $_REQUEST['data_nasc_socio2']))));
        $objPrestador->setNome_banco($_REQUEST['nome_banco']);
        $objPrestador->setAgencia($_REQUEST['agencia']);
        $objPrestador->setConta($_REQUEST['conta']);
        $objPrestador->setValor_limite($_REQUEST['valor_limite']);
        $objPrestador->setId_compra($_REQUEST['id_compra']);
        $objPrestador->setPrestacao_contas($_REQUEST['prestacao_contas']);
        $objPrestador->setEspecialidade($_REQUEST['especialidade']);
        $objPrestador->setC_cep($_REQUEST['c_cep']);
        $objPrestador->setC_id_tp_logradouro($_REQUEST['c_id_tp_logradouro']);
        $objPrestador->setC_numero($_REQUEST['c_numero']);
        $objPrestador->setC_complemento($_REQUEST['c_complemento']);
        $objPrestador->setC_bairro($_REQUEST['c_bairro']);
        $objPrestador->setC_uf($_REQUEST['c_uf']);
        $objPrestador->setC_cod_cidade($_REQUEST['c_cod_cidade']);
        $objPrestador->setIdContabilEmpresa($_REQUEST['id_empresa']);
        $objPrestador->setId_cnae($_REQUEST['cnae']);

        $objPrestador->insertPrestador();

//                foreach ($_REQUEST['socio']['nome'] as $key => $value) {
//                    if (!empty($value)) {
//                        $objSocio->setNome($_REQUEST['socio']['nome'][$key]);
//                        $objSocio->setTel($_REQUEST['socio']['tel'][$key]);
//                        $objSocio->setCpf($_REQUEST['socio']['cpf'][$key]);
//                        $objSocio->setIdPrestador($objPrestador->getId_prestador());
//                        if (!empty($_REQUEST['socio']['id_socio'][$key])) {
//                            $objSocio->setIdSocio($_REQUEST['socio']['id_socio'][$key]);
//                            $objSocio->updateSocio();
//                            //echo "UPATE socio SET nome = '{$_REQUEST['socio']['nome'][$key]}', tel = '{$_REQUEST['socio']['tel'][$key]}', cpf = '{$_REQUEST['socio']['cpf'][$key]}' WHERE id_socio = '{$_REQUEST['socio']['id_socio'][$key]}' LIMIT 1;<br>";
//                        } else {
//                            $objSocio->insertSocio();
//                            //echo "INSERT INTO socio (nome, tel, cpf) VALUES ('{$_REQUEST['socio']['nome'][$key]}','{$_REQUEST['socio']['tel'][$key]}','{$_REQUEST['socio']['cpf'][$key]}');<br>";
//                        }
//                    }
//                }
//
//                foreach ($_REQUEST['dependente']['nome'] as $key => $value) {
//                    if (!empty($value)) {
//                        $objDependente->setNome($_REQUEST['dependente']['nome'][$key]);
//                        $objDependente->setTel($_REQUEST['dependente']['tel'][$key]);
//                        $objDependente->setParentesco($_REQUEST['dependente']['parentesco'][$key]);
//                        $objDependente->setIdPrestador($objPrestador->getId_prestador());
//                        if (!empty($_REQUEST['dependente']['id_dependente'][$key])) {
//                            $objDependente->setIdDependente($_REQUEST['dependente']['id_dependente'][$key]);
//                            $objDependente->updatePrestadorDependente();
//                            //echo "UPATE dependente SET nome = '{$_REQUEST['dependente']['nome'][$key]}', tel = '{$_REQUEST['dependente']['tel'][$key]}', parentesco = '{$_REQUEST['dependente']['parentesco'][$key]}' WHERE id_socio = '{$_REQUEST['dependente']['id_dependente'][$key]}' LIMIT 1;<br>";
//                        } else {
//                            $objDependente->insertPrestadorDependente();
//                            //echo "INSERT INTO dependente (nome, tel, parentesco) VALUES ('{$_REQUEST['dependente']['nome'][$key]}','{$_REQUEST['dependente']['tel'][$key]}','{$_REQUEST['dependente']['parentesco'][$key]}')<br>";
//                        }
//                    }
//                }

        foreach ($_REQUEST['imposto']['id_imposto'] as $key => $value) {
            if (!empty($value)) {
                $objImpostoAssoc->setidImposto($_REQUEST['imposto']['id_imposto'][$key]);
                $objImpostoAssoc->setAliquota($_REQUEST['imposto']['aliquota'][$key]);
                $objImpostoAssoc->setIdContrato($objPrestador->getId_prestador());
                if (!empty($_REQUEST['imposto']['id_assoc'][$key])) {
                    $objImpostoAssoc->setIdAssoc($_REQUEST['imposto']['id_assoc'][$key]);
                    $objImpostoAssoc->updateImpostoAssoc();
                    //echo "UPATE dependente SET nome = '{$_REQUEST['dependente']['nome'][$key]}', tel = '{$_REQUEST['dependente']['tel'][$key]}', parentesco = '{$_REQUEST['dependente']['parentesco'][$key]}' WHERE id_socio = '{$_REQUEST['dependente']['id_dependente'][$key]}' LIMIT 1;<br>";
                } else {
                    $objImpostoAssoc->insertImpostoAssoc();
                    //echo "INSERT INTO dependente (nome, tel, parentesco) VALUES ('{$_REQUEST['dependente']['nome'][$key]}','{$_REQUEST['dependente']['tel'][$key]}','{$_REQUEST['dependente']['parentesco'][$key]}')<br>";
                }
            }
        }


        //print_array($objPrestador);
        //$objPrestador->insertPrestador();
        header("Location: ../prestadores");

        break;

    case 'excluir_documento' :

        $objPrestadorDocumentos->setDocumentoStatus(0);
        $objPrestadorDocumentos->setPrestador_documento_id($_REQUEST['id_documento']);
        //print_array($objPrestador);
        $objPrestadorDocumentos->updatePrestadorDocumentos();

        break;

    case 'form_duplicar_prestador' :
        ?>

        <form action="../actions/action_prestadores.php" method="post">
            <table class="table table-condensed table-hover table-bordered text-sm valign-middle">
                <thead>
                    <tr>
                        <th class="text-center"></th>
                        <th class="text-center">Projeto</th>
                        <!--<th class="text-center">Razão</th>-->
                        <!--<th class="tex-center">CNPJ</th>-->
                        <th class="text-center">Nº Processo</th>
                        <th class="text-center">Vigência</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $queryProjetos = getProjetosRegiao($usuario['id_regiao']);
                    while ($rowProjetos = mysql_fetch_assoc($queryProjetos)) {
                        if ($rowProjetos['id_projeto'] == $objPrestador->getId_projeto())
                            continue;
                        ?>
                        <tr>
                            <td class="text-center"><input type="checkbox" name="id_projeto[<?= $rowProjetos['id_projeto'] ?>]" value="<?= $rowProjetos['id_projeto'] ?>"></td>
                            <td class="text-left"><?= $rowProjetos['nome'] ?></td>
                            <!--<td class="text-center"><?= $rowProjetos['razao'] ?></td>-->
                            <!--<td class="text-center"><?= $rowProjetos['cnpj'] ?></td>-->
                            <td class="text-center"><input type="text" name="n_processo[<?= $rowProjetos['id_projeto'] ?>]" class="form-control input-sm"></td>
                            <td class="text-center" style="width: 250px;">
                                <div class="input-group">
                                    <input type="text" name="v_inicio[<?= $rowProjetos['id_projeto'] ?>]" value="<?= $objPrestador->getContratado_em("d/m/Y") ?>" class="data form-control input-sm no-padding-hr text-center">
                                    <div class="input-group-addon">até</div>
                                    <input type="text" name="v_termino[<?= $rowProjetos['id_projeto'] ?>]" value="<?= $objPrestador->getEncerrado_em("d/m/Y") ?>" class="data form-control input-sm no-padding-hr text-center">
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td colspan="4" class="text-right">
                            <input type="hidden" name="action" value="duplicar_prestador">
                            <input type="hidden" name="id_prestador" value="<?= $objPrestador->getId_prestador() ?>">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-copy"></i> Duplicar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        <script>
            $(function () {
                $('.data').datepicker({
                    dateFormat: 'dd/mm/yy',
                    changeMonth: true,
                    changeYear: true,
                    yearRange: '2005:c+1',
                    beforeShow: function () {
                        setTimeout(function () {
                            $('.ui-datepicker').css('z-index', 5010);
                        }, 0);
                    }
                });

                $("[data-toggle='tooltip']").tooltip();
            })
        </script>

        <?php
        break;

    case 'duplicar_prestador' :

        foreach ($_REQUEST['id_projeto'] as $value) {
//            $array[$value]['n_processo'] = $_REQUEST['n_processo'][$value];
//            $array[$value]['v_inicio'] = implode('-', array_reverse(explode('/', $_REQUEST['v_inicio'][$value])));
//            $array[$value]['v_termino'] = implode('-', array_reverse(explode('/', $_REQUEST['v_termino'][$value])));
            $objPrestador->setId_projeto($value);
            $objPrestador->setNumero($_REQUEST['n_processo'][$value]);
            $objPrestador->setContratado_em(implode('-', array_reverse(explode('/', $_REQUEST['v_inicio'][$value]))));
            $objPrestador->setEncerrado_em(implode('-', array_reverse(explode('/', $_REQUEST['v_termino'][$value]))));

            $objPrestador->insertPrestador();
        }

        header("Location: ../prestadores");

        //print_array($array);

        break;

    case 'upload_anexo' :

        $objPrestadorTipoDoc->setPrestador_tipo_doc_id($_REQUEST['id_tipo_documento']);
        $objPrestadorTipoDoc->getTipoDocumento();
        $objPrestadorTipoDoc->getRowPrestadorTipoDoc();

        //$diretorio = "/intranet/processo/prestador_documentos";
        $diretorio = "../../../processo/prestador_documentos";

        $upload = new UploadFile($diretorio, array('jpg', 'gif', 'png', 'pdf', 'JPG', 'GIF', 'PNG', 'PDF'));
        $upload->arquivo($_FILES[file]);
        $upload->verificaFile();

        $nomeDoc = RemoveAcentos($objPrestadorTipoDoc->getPrestador_tipo_doc_nome());
        $data_vencimento = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['data_vencimento'])));
        $nome_arquivo = $objPrestador->getId_prestador() . "_" . str_replace(" ", "_", $nomeDoc) . "_" . date("dmYHi");

        $objPrestadorDocumentos->setId_prestador($objPrestador->getId_prestador());
        $objPrestadorDocumentos->setPrestador_tipo_doc_id($objPrestadorTipoDoc->getPrestador_tipo_doc_id());
        $objPrestadorDocumentos->setData_vencimento($data_vencimento);
        $objPrestadorDocumentos->setNome_arquivo($nome_arquivo);
        $objPrestadorDocumentos->setExtensao_arquivo('.' . $upload->extensao);
        $objPrestadorDocumentos->setStatus(1);
        $objPrestadorDocumentos->insertPrestadorDocumentos();

        $upload->NomeiaFile($nome_arquivo);

        $upload->Envia();
//        print_array($upload);exit;
        //$log->gravaLog('Anexo de Logo', 'Anexo de Logo Parceiro: ' . $objParceiro->getIdParceiro());
        echo $diretorio . '/' . $nome_arquivo . '.' . $upload->extensao;
        exit;
        break;

    case 'gerenciar_prestador' :
        $array_projeto = projetosId($objPrestador->getId_projeto()); ?>
        <div class="row">
            <div class="col-sm-3">
                <div class="col-sm-12 btn btn-info margin_b5 menu" data-target="#m1">Gerencia de Processo</div>
                <div class="col-sm-12 btn btn-info margin_b5 menu" data-target="#m2">Contrato e Anexos</div>
                <div class="col-sm-12 btn btn-info margin_b5 menu" data-target="#m3">Pagamentos</div>
                <div class="col-sm-12 btn btn-info margin_b5 menu hide" data-target="#m4">Ficha Financeira</div>
                <div class="col-sm-12 btn btn-info margin_b5 menu" data-target="#m5">Imposto Retido</div>
                <div class="col-sm-12 btn btn-info margin_b5 menu hide" data-target="#m6">Ficha de Cadastro</div>
            </div>
            <div class="col-sm-9 bordered padding-sm-vr">
                <div class="col-sm-12 collapse in" id="m1">
                    <fieldset>
                        <legend><small>Contrato e Anexos</small></legend>
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2">Projeto:</label>
                                <label class="col-sm-10 text-normal"><?= $array_projeto['nome'] ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Prestador:</label>
                                <label class="col-sm-10 text-normal"><?= $objPrestador->getC_fantasia() ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Status:</label>
                                <?php if ($objPrestador->getImprimir() > 0) { ?>
                                    <label class="col-sm-10 text-success">ABERTO</label>
                                <?php } else { ?>
                                    <label class="col-sm-10 text-danger">FECHADO</label>
                                <?php } ?>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Ações:</label>
                                <?php if ($objPrestador->getImprimir() > 0) { ?>
                                    <a href="../actions/imprimir_prestadores.php?action=encerramento&id_prestador=<?= $objPrestador->getId_prestador() ?>" target="_blank" class="btn btn-xs btn-default margin_l10"><i class="fa fa-folder"></i> Fechar Processo</a>
                                <?php } else { ?>
                                    <a href="../actions/imprimir_prestadores.php?action=abertura&id_prestador=<?= $objPrestador->getId_prestador() ?>" target="_blank" class="btn btn-xs btn-default margin_l10 abrir_processo"><i class="fa fa-folder-open"></i> Abrir Processo</a>
                                <?php } ?>
                            </div>
                        </form>
                    </fieldset>
                </div>
                <div class="col-sm-12 collapse" id="m2">
                    <fieldset>
                        <?php if($objPrestador->getImprimir() > 0) { ?>
                        <legend><small>Contrato e Anexos</small></legend>
                        <form action="../actions/imprimir_prestadores.php" target="_blank" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2">Projeto:</label>
                                <label class="col-sm-10 text-normal"><?= $array_projeto['nome'] ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Prestador:</label>
                                <label class="col-sm-10 text-normal"><?= $objPrestador->getC_fantasia() ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label text-left">Valor:</label>
                                <div class="col-sm-4"><input type="text" name="valor" id="valor" class="form-control input-sm"></div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label text-sm no-padding-r">Gerar Contrato: </label>
                                <!--<input type="button" value="Gerar Contrato Mensal" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>'" />
                                <input type="button" value="Gerar Contrato Horista" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>&horista=1'" />-->
                                <div class="col-sm-8">
                                    <input type="hidden" name="action" value="contrato">
                                    <input type="hidden" name="id_prestador" value="<?=$objPrestador->getId_prestador()?>">
                                    <button type="submit" class="btn btn-sm btn-default" name="horista" value="0"><i class="fa fa-calendar"></i> Gerar Contrato Mensal</button>
                                    <button type="submit" class="btn btn-sm btn-default" name="horista" value="1"><i class="fa fa-clock-o"></i> Gerar Contrato Horista</button>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-warning">Você precisa abrir o processo antes de gerar contrato!</div>
                        <?php } ?>
                        </form>
                    </fieldset>
                </div>
                <div class="col-sm-12 collapse" id="m3">
                    <legend><small>Pagamentos</small></legend>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2">Projeto:</label>
                            <label class="col-sm-10 text-normal"><?= $array_projeto['nome'] ?></label>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2">Prestador:</label>
                            <label class="col-sm-10 text-normal"><?= $objPrestador->getC_fantasia() ?></label>
                        </div>
                    </form>
                    <?php $sql = "SELECT A.id_saida, A.ano_competencia, A.mes_competencia, A.nome, REPLACE(A.valor, ',', '.') AS valor, A.data_vencimento, DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') AS data_vencimento_f from saida AS A WHERE id_prestador = {$objPrestador->getId_prestador()} ORDER BY ano_competencia DESC, mes_competencia DESC;";
                    $pagamentos = array();
                    $query = mysql_query($sql);
                    while ($row = mysql_fetch_array($query)) {
                        $pagamentos[] = $row;
                    }
                    if (count($pagamentos) > 0) { ?>
                    <table class="table table-condensed table-bordered text-sm valign-middle">
                        <thead>
                            <tr>
                                <th colspan="7">Histórico de Lançamentos</th>
                            </tr>
                            <tr>
                                <th>Número</th>
                                <th>Competência</th>
                                <th>Nome</th>
                                <th>Valor</th>
                                <th>Vencimento</th>
                                <th>Boleto</th>
                                <th>CP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($pagamentos as $pagamento) { ?>
                            <tr>
                                <td><?= $pagamento['id_saida'] ?></td>
                                <td><?= $pagamento['mes_competencia'].'/'.$pagamento['ano_competencia'] ?></td>
                                <td><?= $pagamento['nome'] ?></td>
                                <td class="center">R$ <?= number_format($pagamento['valor'],2,',','.'); ?></td>
                                <td class="center"><?= $pagamento['data_vencimento_f'] ?></td>
                                <td class="center">
                                <?php $res_file_pg = mysql_query('SELECT * FROM saida_files WHERE id_saida = '.$pagamento['id_saida']);
                                while($res = mysql_fetch_array($res_file_pg)){   
                                    $link_encryptado_pg = ''; ?>
                                    <a target="_blank" class="btn btn-xs btn-primary" title="Comprovante de pagamento" href="/intranet/comprovantes/<?= $res['id_saida_file'].'.'. $pagamento['id_saida'].$res['tipo_saida_file'] ?>"><i class="fa fa-paperclip"></i></a>                                        
                                <?php } ?>
                                </td>
                                <td class="center">
                                <?php $res_file_pg = mysql_query('SELECT * FROM saida_files_pg WHERE id_saida = '.$pagamento['id_saida']);
                                while($res = mysql_fetch_array($res_file_pg)){   
                                    $link_encryptado_pg = ''; ?>
                                    <a target="_blank" class="btn btn-xs btn-info" title="Comprovante de pagamento" href="/intranet/comprovantes/<?= $res['id_pg'].'.'. $pagamento['id_saida'].'_pg'.$res['tipo_pg'] ?>"><i class="fa fa-paperclip"></i></a>
                                <?php } ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="alert alert-warning">Não há registros!</div>
                <?php } ?>
                </div>
                <div class="col-sm-12 collapse" id="m4">D</div>
                <div class="col-sm-12 collapse" id="m5">
                    <fieldset>
                        <legend>Imposto Retido</legend>
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2">Projeto:</label>
                                <label class="col-sm-10 text-normal"><?= $array_projeto['nome'] ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Prestador:</label>
                                <label class="col-sm-10 text-normal"><?= $objPrestador->getC_fantasia() ?></label>
                            </div>
                        </form>
                    </fieldset>
                    <br><br>
                    <div>
                        <?php 
                        $sql = "SELECT id_saida,ano_competencia,mes_competencia,especifica, CAST(REPLACE(valor, ',', '.') as decimal(13,2))AS valor FROM saida WHERE tipo_nf = 1 AND id_prestador = {$objPrestador->getId_prestador()} AND status = 2 ORDER BY ano_competencia DESC, mes_competencia DESC;";
                        $impostoRetido = array();
                        $query = mysql_query($sql);
                        while ($row = mysql_fetch_array($query)) {
                            $impostoRetido[] = $row;
                        }
                        if(isset($impostoRetido) && !empty($impostoRetido) && count($impostoRetido) > 0){ ?>
                            <table class="table table-condensed table-bordered text-sm valign-middle">
                                <thead>
                                    <tr>
                                        <th colspan="7">Histórico de Lançamentos</th>
                                    </tr>
                                    <tr>
                                        <th>Número</th>
                                        <th>Competência</th>
                                        <th>Nome</th>
                                        <th>Valor</th>
                                        <th>Comprovante <br>de Pagamento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($impostoRetido as $ir) { ?>
                                    <tr>
                                        <td><?= $ir['id_saida'] ?></td>
                                        <td><?php echo sprintf("%02d",$ir['mes_competencia']).'/'.$ir['ano_competencia'] ?></td>
                                        <td><?= $ir['especifica'] ?></td>
                                        <td>R$ <?= $ir['valor'] ?></td>
                                        <td></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-warning">Não há registros!</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-sm-12 collapse" id="m6">
                    <fieldset>
                        <legend>Ficha de Cadastro</legend>
                        <form action="../actions/imprimir_prestadores.php" target="_blank" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2">Projeto:</label>
                                <label class="col-sm-10 text-normal"><?= $array_projeto['nome'] ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2">Prestador:</label>
                                <label class="col-sm-10 text-normal"><?= $objPrestador->getC_fantasia() ?></label>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label text-sm no-padding-r">Gerar Ficha: </label>
                                <!--<input type="button" value="Gerar Contrato Mensal" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>'" />
                                <input type="button" value="Gerar Contrato Horista" onclick="window.location.href='contrato/?id=<?= $id_prestador; ?>&horista=1'" />-->
                                <div class="col-sm-8">
                                    <input type="hidden" name="action" value="contrato">
                                    <input type="hidden" name="id_prestador" value="<?=$objPrestador->getId_prestador()?>">
                                    <!--<input type="button" value="Gerar" onclick=" window.open('fichacadastro/?id=<?= $id_prestador; ?>&pro=<?= $prestador['id_projeto'] ?>&reg=<?= $prestador['id_regiao'] ?>', '_blank');" />-->
                                    <button type="submit" class="btn btn-sm btn-default" name="" value="Gerar"><i class="fa fa-clock-o"></i> Gerar Contrato Horista</button>
                                </div>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>

        <?php
        break;

    case 'remover_socio':
        $objSocio->setIdSocio($_REQUEST['id_socio']);
        $array = ($objSocio->removerSocio()) ? array('msg' => utf8_encode('Sócio Excluido com sucesso.'), 'status' => 'success') : array('msg' => utf8_encode('Erro ao excluir sócio!'), 'status' => 'danger');
        echo json_encode($array);
        break;

    case 'remover_dependente':
        $objDependente->setIdDependente($_REQUEST['id_dependente']);
        $array = ($objDependente->removerDependente()) ? array('msg' => utf8_encode('Dependente Excluido com sucesso.'), 'status' => 'success') : array('msg' => utf8_encode('Erro ao excluir dependente!'), 'status' => 'danger');
        echo json_encode($array);
        break;

    default:
        echo 'action: ' . $action;
        break;
}