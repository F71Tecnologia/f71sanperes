<?php
header("Content-Type: text/html; charset=ISO-8859-1",true);

include_once("../../../conn.php");
include_once("../../../wfunction.php");
include_once("../../../classes/uploadfile.php");
include_once("../../../classes/PatrimoniosClass.php");
include_once("../../../classes/PatrimoniosFotoClass.php");

$usuario = carregaUsuario();

$objPatrimonios = new PatrimoniosClass();
$objPatrimoniosFoto = new PatrimoniosFotoClass();

//$array = Array(Array('name'=>"A", 'price'=>10.8),Array('name'=>"B", 'price'=>12.8));
//print_array($_REQUEST);
if($_REQUEST['method'] == 'getPatrimonios'){
    $sql = "SELECT * FROM patrimonios WHERE status = 1";
    $qry = mysql_query($sql);
    while($row = mysql_fetch_assoc($qry)){
        $row['fotos'] = array();
        $sql2 = "SELECT * FROM patrimonios_foto WHERE id_patrimonio = {$row['id_patrimonio']} AND status = 1";
        $qry2 = mysql_query($sql2);
        while($row2 = mysql_fetch_assoc($qry2)){
            $row['fotos'][] = $row2;
        }

        $array[$row['id_patrimonio']] = $row;
    }
    //print_array($array);
    echo json_encode($array); exit;
}

//if($_REQUEST['method'] == 'addPatrimonios'){
//
//    $patrimonio = (array) json_decode($_REQUEST['patrimonio']);
//    $patrimonio['status'] = 1;
//    unset($patrimonio['fotos']);
//    if(empty($patrimonio['id_patrimonio'])){
//        mysql_query("INSERT INTO patrimonios (" . implode(",",array_keys($patrimonio)) . ") VALUES ('" . implode("','",array_values($patrimonio)) . "')");
//        $id_patrimonio = mysql_insert_id();
//    } else {
//        foreach ($patrimonio as $key => $value) {
//            $camposUpdate[] = "$key = '$value'";
//        }
//        mysql_query("UPDATE patrimonios SET " . implode(", ", ($camposUpdate)) . " WHERE id_patrimonio = {$patrimonio['id_patrimonio']} LIMIT 1;");
//        $id_patrimonio = $patrimonio['id_patrimonio'];
//    }
//    echo $id_patrimonio.mysql_error();
//    exit;
//}

//if($_REQUEST['method'] == 'inativaPatrimonio'){
//    //print_array($_REQUEST);exit;
//    mysql_query("UPDATE patrimonios SET status = 0 WHERE id_patrimonio = {$_REQUEST['id_patrimonio']} LIMIT 1;");
//    echo mysql_error();
//    exit;
//}

if($_REQUEST['method'] == 'uploadFoto'){
//    print_array($_REQUEST);
//    print_array($_FILES[file]);
    $id_patrimonio = $_REQUEST['id_patrimonio'];
    
    $diretorio = "../fotos";
    $uniq = uniqid();

    $upload = new UploadFile($diretorio,array('jpg','gif','png','JPG','GIF','PNG'));
    $upload->arquivo($_FILES[file]);
    $upload->verificaFile();
    
    
    $objPatrimoniosFoto->setIdPatrimonio($_REQUEST['id_patrimonio']);
    $objPatrimoniosFoto->setNome($uniq);
    $objPatrimoniosFoto->setTipo($upload->extensao);
    $objPatrimoniosFoto->setStatus(1);
    $objPatrimoniosFoto->insert();
//    echo $insert = "INSERT INTO patrimonios_foto VALUES ('',{$id_patrimonio}, '{$uniq}', '{$upload->extensao}', 1);";
    
    $upload->NomeiaFile($uniq);
    $upload->Envia();
    exit;
}

if($_REQUEST['method'] == 'gallery' AND !empty($_REQUEST['id'])){
    $objPatrimoniosFoto->setIdPatrimonio($_REQUEST['id']);
    $objPatrimoniosFoto->getByPatrimonio(); ?>
    <ul class="img-thumbnails clearfix">
        <?php while ($objPatrimoniosFoto->getRow()) { ?>
        <li class="small-image pull-left thumbnail" id="f<?= $objPatrimoniosFoto->getIdFoto() ?>">
            <a href="fotos/<?= $objPatrimoniosFoto->getNome() ?>.<?= $objPatrimoniosFoto->getTipo() ?>" target="_blank">
                <img style="max-width: 100px; height:100px;" src="fotos/<?= $objPatrimoniosFoto->getNome() ?>.<?= $objPatrimoniosFoto->getTipo() ?>" alt="Description" />
            </a>
            <button type="button" class="col-xs-12 btn btn-danger btn-xs deletaFoto" data-id="<?= $objPatrimoniosFoto->getIdFoto() ?>"><i class="fa fa-trash-o"></i> Deletar</button>
        </li>
        <?php } ?>
    </ul>
    <?php
    exit;
}

if($_REQUEST['method'] == 'del_foto'){
    $objPatrimoniosFoto->setIdFoto($_REQUEST['id']);
    $objPatrimoniosFoto->inativa(); 
    exit;
}

if($_REQUEST['method'] == 'del_patrimonio'){
    $objPatrimonios->setIdPatrimonio($_REQUEST['id']);
    $objPatrimonios->inativa(); 
    exit;
}

if($_REQUEST['method'] == 'add_edit'){
    $arButton = array('name' => 'cadastrar', 'type' => 'primary', 'text' => 'Cadastrar');
    if(!empty($_REQUEST['id'])){
        $objPatrimonios->setIdPatrimonio($_REQUEST['id']);
        $objPatrimonios->getById();
        $objPatrimonios->getRow();
        $objPatrimoniosFoto->setIdPatrimonio($objPatrimonios->getIdPatrimonio());
        $objPatrimoniosFoto->getByPatrimonio();
        $arButton = array('name' => 'editar', 'type' => 'warning', 'text' => 'Editar');
    }
    
    ?>
    <div class="modal-header">
        <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
        <h4 class="modal-title" id="myModalLabel">
            <?= $arButton['text'] ?> Patrimônio
        </h4>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label text-sm">Projeto:</label>
                <?= montaSelect(getProjetos($usuario['id_regiao']), $objPatrimonios->getIdProjeto(), 'name="id_projeto" id="id_projeto" class="form-control input-sm validate[required,custom[select]]"') ?>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label text-sm">Nome:</label>
                <input type="text" placeholder="Nome Patrimônio" name="nome" id="nome" class="form-control input-sm validate[required]" value="<?= $objPatrimonios->getNome() ?>" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6">
                <label class="control-label text-sm">Numero (NB):</label>
                <input type="text" placeholder="Numero (NB)" name="numero" id="numero" class="form-control input-sm validate[required]" value="<?= $objPatrimonios->getNumero() ?>" required>
            </div>
            <div class="col-md-6">
                <label class="control-label text-sm">Numero Serie (NS):</label>
                <input type="text" placeholder="Numero Serie (NS)" name="numero_serie" id="numero_serie" class="form-control input-sm validate[required]" value="<?= $objPatrimonios->getNumeroSerie() ?>" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12">
                <label class="control-label text-sm">Descrição:</label>
                <textarea type="text" placeholder="Descrição" name="descricao" id="descricao" class="form-control input-sm validate[required]" required><?= $objPatrimonios->getDescricao() ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label text-sm">Data Aquisição:</label>
                <input type="text" placeholder="" name="data_aquisicao" id="data_aquisicao" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataAquisicao('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Data Acerto:</label>
                <input type="text" placeholder="" name="data_acerto" id="data_acerto" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataAcerto('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Data Cadastro:</label>
                <input type="text" placeholder="" name="data_cadastro" id="data_cadastro" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataCadastro('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Data Contabilização:</label>
                <input type="text" placeholder="" name="data_contabilizacao" id="data_contabilizacao" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataContabilizacao('d/m/Y') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label text-sm">Data Vistoria:</label>
                <input type="text" placeholder="" name="data_vistoria" id="data_vistoria" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataVistoria('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Data Marcação:</label>
                <input type="text" placeholder="" name="data_marcacao" id="data_marcacao" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataMarcacao('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Data Baixa:</label>
                <input type="text" placeholder="" name="data_baixa" id="data_baixa" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getDataBaixa('d/m/Y') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Vencimento Garantia:</label>
                <input type="text" placeholder="" name="vencimento_garantia" id="vencimento_garantia" class="form-control input-sm data validate[required]" value="<?= $objPatrimonios->getVencimentoGarantia('d/m/Y') ?>" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-6">
                <label class="control-label text-sm">Numero Nota Fiscal:</label>
                <input type="text" placeholder="Numero Nota Fiscal" name="n_nf" id="n_nf" class="form-control input-sm validate[required]" value="<?= $objPatrimonios->getNNf() ?>" required>
            </div>
            <div class="col-md-6">
                <label class="control-label text-sm">Chave NF-S:</label>
                <input type="text" placeholder="Chave NF-S" name="chave_nfs" id="chave_nfs" class="form-control input-sm validate[required]" value="<?= $objPatrimonios->getChaveNfs() ?>" required>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-3">
                <label class="control-label text-sm">Valor Original:</label>
                <input type="text" placeholder="9,999.99" name="valor_original" id="valor_original" class="form-control input-sm valor validate[required]" value="<?= $objPatrimonios->getValorOriginal() ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Valor Compra:</label>
                <input type="text" placeholder="9,999.99" name="valor_compra" id="valor_compra" class="form-control input-sm valor validate[required]" value="<?= $objPatrimonios->getValorCompra() ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Valor Atualizado:</label>
                <input type="text" placeholder="9,999.99" name="valor_atualizado" id="valor_atualizado" class="form-control input-sm valor validate[required]" value="<?= $objPatrimonios->getValorAtualizado() ?>" required>
            </div>
            <div class="col-md-3">
                <label class="control-label text-sm">Valor Baixa:</label>
                <input type="text" placeholder="9,999.99" name="valor_baixa" id="valor_baixa" class="form-control input-sm valor validate[required]" value="<?= $objPatrimonios->getValorBaixa() ?>" required>
            </div>
        </div>
        <!-- DROPZONE -->
        <div id="actions" class="row">
            <div class="gallery">
                
            </div>
            <div class="col-lg-12">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button dz-clickable">
                    <i class="fa fa-camera"></i>
                    <span>Selecionar Foto</span>
                </span>
            </div>
        </div>
        <div id="drop"></div>
        <!-- HTML heavily inspired by http://blueimp.github.io/jQuery-File-Upload/ -->
        <div class="table table-striped" class="files" id="previews">

            <div id="template" class="teste file-row margin_t10">
                <!-- This is used as the file preview template -->
                <div class="col-lg-3 text-center">
                    <span class="preview"><img data-dz-thumbnail /></span>
                </div>
                <div class="col-lg-9">
                    <strong class="error text-danger" data-dz-errormessage></strong>
                </div>
                <div>
                    <p class="name" data-dz-name></p>
                </div>
                <div>
                    <p class="size" data-dz-size></p>
                    <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
                    </div>
                </div>
                <div>
                    <button type="button" id="startUpload" class="btn btn-primary start hide">
                        <i class="glyphicon glyphicon-upload"></i>
                        <span>Start</span>
                    </button>
                    <button data-dz-remove class="btn btn-warning cancel">
                        <i class="glyphicon glyphicon-ban-circle"></i>
                        <span>Cancel</span>
                    </button>
                    <button data-dz-remove class="btn btn-danger delete">
                        <i class="glyphicon glyphicon-trash"></i>
                        <span>Delete</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- DROPZONE -->
    </div>
    <div class="modal-footer">
        <input type="hidden" name="id_patrimonio" id="id_patrimonio" value="<?= $objPatrimonios->getIdPatrimonio() ?>">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button name="salvar" id="salvar" type="submit" class="btn btn-<?= $arButton['type'] ?>"><i class="fa fa-save"></i> <?= $arButton['text'] ?></button>
        <button style="display: none;" type="submit" class="btn btn-warning">Atualizar</button>
    </div>
    <script>
        $(function(){
            
            function loadGallery(id_patrimonio){
                $.post("ctrl/patrimonio.php", {method: "gallery", id: id_patrimonio}, function (data) {
                    $(".gallery").html(data);
                });
            }
            
            $('.valor').maskMoney({thousands: ''});
            
            // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
            var previewNode = document.querySelector("#template");
            previewNode.id = "";
            var previewTemplate = previewNode.parentNode.innerHTML;
            previewNode.parentNode.removeChild(previewNode);

            var myDropzone = new Dropzone('#drop', { // Make the whole body a dropzone
                url: "ctrl/patrimonio.php?method=uploadFoto", // Set the url
                thumbnailWidth: 80,
                thumbnailHeight: 80,
                maxFilesize: 10,
                dictResponseError: "Erro no servidor!",
                dictCancelUpload: "Cancelar",
                dictFileTooBig: "Tamanho máximo: 10MB",
                dictRemoveFile: "Remover Arquivo",
                canceled: "Arquivo Cancelado",
                acceptedFiles: '.jpg,.gif,.png,.JPG,.GIF,.PNG',
                previewTemplate: previewTemplate,
                autoQueue: false, // Make sure the files aren't queued until manually added
                previewsContainer: "#previews", // Define the container to display the previews
                clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
            });

            myDropzone.on("addedfile", function(file) {
                // Hookup the start button
//                if (this.files[1]!=null){
//                    this.removeFile(this.files[1]);
//                    alert("Limite de 1 arquivos excedido!");
//                } else {
                    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
//                }
            });

            myDropzone.on("sending", function(file, xhr, formData) {
                formData.append("id_patrimonio", $("#id_patrimonio").val());
                file.previewElement.querySelector(".cancel").setAttribute("disabled", "disabled");
            });
            
            myDropzone.on("queuecomplete", function(progress) {
                loadGallery($("#id_patrimonio").val());
//                $('.teste').remove();
myDropzone.removeAllFiles(true);
            });
            
//            document.querySelector("#actions .delete").onclick = function() {
//                myDropzone.removeAllFiles(true);
//            };
            
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
            
            loadGallery($("#id_patrimonio").val());
        });
        </script>
<?php exit;
}

