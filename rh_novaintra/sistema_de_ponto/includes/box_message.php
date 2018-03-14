<?php if(isset($alert) && !empty($alert)){ 
    $msgcolor = (isset($alert['color']) && !empty($alert['color'])) ? $alert['color'] : 'warning';
?>
<div class="alert alert-<?=$msgcolor?>">
    <p><?=isset($alert['message']) ? $alert['message'] : ''?></p>
</div>
<?php } ?>