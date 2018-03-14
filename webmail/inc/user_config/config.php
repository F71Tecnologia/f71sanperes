<?php
$jsonConfig = ((isset($_REQUEST['json_config']))? $_REQUEST['json_config']: null);
if (!is_null($jsonConfig))	$jsonConfig = str_replace("\\", '', $jsonConfig); //sanitize json!!
$objConfig = json_decode($jsonConfig);

header('Content-type: text/html');
?>
<div class="config_content">
	<div class="config_title">
		<img class="left" src="assets/img/config-button.png" />
		<h2 class="left">Configurações</h2>
		<div class="clear"></div>
	</div>
	<div class="config_box">
		<div class="box_info">
			<input class="left" id="preview_include" type="checkbox" value='1' <?= (!is_null($objConfig))? (($objConfig->preview_include == true)? 'checked': null): 'checked';  ?> />
			<label class="left pointer" for="preview_include">Preview do Email</label>
			<div class="help right" title="Quando ativo, clique uma vez para abrir o preview do email e duas para visualizar todo o email."></div>
			<div class="clear"></div>
		</div>

		<div class="box_info">
			<input class="left" id="preview_redimensionavel" type="checkbox" value='1' <?= (!is_null($objConfig))? (($objConfig->preview_redimensionavel == true)? 'checked': null): 'checked';  ?> />
			<label class="left pointer" for="preview_redimensionavel">Preview Redimensionável</label>
			<div class="help right" title="Torna o preview redimensionável verticalmente e horizontalmente."></div>
			<div class="clear"></div>
		</div>

		<div class="box_info">
			<input class="left" id="production_mode" type="checkbox" value='1' <?= (!is_null($objConfig))? (($objConfig->production_mode == true)? 'checked': null): 'checked';  ?> />
			<label class="left pointer" for="production_mode">Modo de Produção</label>
			<div class="help right" title="Toda ação sobre o email aberto, mover, responder, encaminhar e apagar, leva para o próximo email. Quando esta opção está desmarcada toda ação leva para a caixa de entrada."></div>
			<div class="clear"></div>
		</div>

		<div class="box_info">
			<input class="left" id="include_assinatura" type="checkbox" value='1' <?= (!is_null($objConfig))? (($objConfig->include_assinatura == true)? 'checked': null): 'checked';  ?> />
			<label class="left pointer" for="include_assinatura">Incluir assinatura </label>
			<div class="help right" title="Inclui a assinatura no email."></div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="config_box">
		<div>
			<label for="assinatura">Assinatura do email</label>
			<div>
				<textarea id="assinatura" cols="30" rows="10"><?= (!is_null($objConfig))? (($objConfig->assinatura != '')? $objConfig->assinatura: null): null;  ?></textarea>
			</div>
		</div>
	</div>

	<div>
		<input class="left" id="save" type="button" value="Salvar">
		<input class="right" id="cancel" type="button" value="Cancelar">
		<div class="clear"></div>
	</div>
</div>