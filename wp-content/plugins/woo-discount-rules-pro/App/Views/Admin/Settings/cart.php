<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<tr>
    <td scope="row">
        <label for="free_shipping_title" class="awdr-left-align"><?php _e('Free shipping title', 'woo-discount-rules-pro') ?></label>
        <span class="wdr_desc_text awdr-clear-both"><?php esc_attr_e('Title for free shipping', 'woo-discount-rules-pro'); ?></span>
    </td>
    <td>
        <input type="text" name="free_shipping_title"
               value="<?php echo $configuration->getConfig('free_shipping_title', 'Free shipping'); ?>">
    </td>
</tr>