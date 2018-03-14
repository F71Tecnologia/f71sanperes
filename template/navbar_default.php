<div class="navbar navbar-default hidden-print"> 
    <div class="<?=($container_full) ? 'container-full' : 'container'?>">

        <div class="navbar-header top-header-default">
            <a href="<?php echo $dadosHeader['defaultPath']; ?>index.php" class="navbar-brand">
                <img src="<?php echo $dadosHeader['defaultPath']; ?>imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="logo-border">
            </a>

            <div class="header-info">
                <p class="text-primary">Olá <strong><?php echo $usuario['nome1'] ?></strong></p>
                <p class="text-primary">Data: <?php echo date("d/m/Y") ?></p>
            </div>

            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        
        <div class="navbar-collapse collapse navbar-right" id="navbar-main">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-regioes">Regiões <span class="caret"></span></a>
                    <div class="dropdown-menu drop-especial" aria-labelledby="drp-regioes">
                        <ul>
                            <li><a href="javascript:;" id="regiao-ativa" data-key="<?php echo $usuario['id_regiao'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $dadosHeader['regiaoSelected']; ?></a></li>
                            <?php echo (count($dadosHeader['regioes']) > 0) ? '<li class="divider"></li>' : ""; ?>
                            <?php foreach ($dadosHeader['regioes'] as $k => $regiaoHeader) { ?>
                                <li class="col-lg-3 col-md-4 col-sm-6"><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-regiao" data-base-url="<?php echo $dadosHeader['defaultPath']; ?>"><?php echo $regiaoHeader ?></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="drp-master">Empresa <span class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="drp-master">
                        <li><a href="javascript:;" id="master-ativo" data-key="<?php echo $usuario['id_master'] ?>"><span class="glyphicon glyphicon-ok"></span> <?php echo $dadosHeader['masterSelected']; ?></a></li>
                        <?php echo (count($dadosHeader['masters']) > 0) ? '<li class="divider"></li>' : ""; ?>
                        <?php foreach ($dadosHeader['masters'] as $k => $regiaoHeader) { ?>
                            <li><a href="javascript:;" data-key="<?php echo $k ?>" class="bt-troca-master" data-base-url="<?php echo $dadosHeader['defaultPath']; ?>"><?php echo $regiaoHeader ?></a></li>
                            <?php } ?>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo $dadosHeader['defaultPath']; ?>logof.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php if(isset($breadcrumb_config)){ ?>
<div class="<?=($container_full) ? 'container-full' : 'container'?> hidden-print">
    <ul class="breadcrumb">
        <li><a href="<?php echo $breadcrumb_config['nivel']; ?>">Home</a></li>
        <li><a href="javascript:;" data-key="<?php echo $breadcrumb_config['key_btn']; ?>" data-nivel="<?php echo $breadcrumb_config['nivel']; ?>" data-form="<?php echo $breadcrumb_config['id_form']; ?>" class="return_principal"><?php echo $breadcrumb_config['area']; ?></a></li>
        <?php foreach ($breadcrumb_pages as $breadcrumb_k => $breadcrumb_pagina){ ?>
        <li><a <?php echo $breadcrumb_attr[$breadcrumb_k]; ?> href="<?php echo $breadcrumb_pagina; ?>"><?php echo $breadcrumb_k; ?></a></li>
        <?php } ?>
        <li class="active"><?php echo $breadcrumb_config['ativo']; ?></li>
    </ul>
</div>
<?php } ?>