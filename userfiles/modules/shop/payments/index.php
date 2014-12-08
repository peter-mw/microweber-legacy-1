<?php $payment_options =  payment_options();  ?>
<script  type="text/javascript">
mw.moduleCSS("<?php print modules_url(); ?>shop/shop-defaults.css");
$(document).ready(function(){

    mw.$('.mw-payment-gateway-<?php print $params['id']; ?> input').commuter(function() {
        mw.$('.mw-payment-gateway-selected-<?php print $params['id']; ?> .module:first').attr('data-selected-gw',this.value);
        mw.load_module(''+this.value,'#mw-payment-gateway-selected-<?php print $params['id']; ?>');
    });

});
</script>

 

<?php if(is_array($payment_options)) :?>

<div class="well">
	<?php if(count($payment_options) > 0): ?>
	<h2 style="margin-top:0 " class="edit nodrop" field="checkout_payment_information_title" rel="global" rel_id="<?php print $params['id']?>">
		<?php _e("Payment method"); ?>
	</h2>
    <hr>
	<ul name="payment_gw" class="gateway-selector field-full mw-payment-gateway mw-payment-gateway-<?php print $params['id']; ?>">
		<?php $count = 0; foreach ($payment_options as $payment_option) : $count ++; ?>
		<li>
			<label class="mw-ui-check tip" data-tipposition="top-left" data-tip="<?php print  $payment_option['name']; ?>">

                <input type="radio" <?php if($count == 1):  ?> checked="checked" <?php endif;?> value="<?php print  $payment_option['gw_file']; ?>" name="payment_gw" /><span></span>
                <?php if(isset($payment_option['icon']) and trim($payment_option['icon']) !='' and !stristr($payment_option['icon'],'default.png')) :?>
				<span><img src="<?php print  $payment_option['icon']; ?>" alt="" /></span>
				<?php else : ?>
				<span><?php print  _e($payment_option['name']); ?></span>
				<?php endif;?>


			</label>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif;?>



	<div id="mw-payment-gateway-selected-<?php print $params['id']; ?>">
        <?php //var_dump($payment_options); ?>
        <?php if(isset( $payment_options[0])): ?>
		<module type="<?php print $payment_options[0]['gw_file'] ?>"  />
        <?php endif;?>
	</div>
</div>
<?php else : ?>
<?php print lnotif("Click here to edit Payment Options"); ?>
<?php endif;?>
