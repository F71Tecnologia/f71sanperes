<?php if(isset($alert) && !empty($alert)){ 
    $msgcolor = (isset($alert['color']) && !empty($alert['color'])) ? $alert['color'] : 'yellow';
    ?>
<div class="message-box message-<?php echo $msgcolor; ?>">
    <p><?php echo isset($alert['message']) ? $alert['message'] : ''; ?></p>
</div>
<?php } ?>