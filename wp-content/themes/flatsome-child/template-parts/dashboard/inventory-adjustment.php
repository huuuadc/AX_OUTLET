<?php

$transfer_ids = wc_get_orders(['post_type'=>'transfer_order','return'=>'ids','numberposts' => -1]);

if (count($transfer_ids) <= 0) return;
?>

<div id="inventory_adjustment">
    <div class="card mt-3">
        <?php wc_get_template('template-parts/dashboard/components/inventory/card-table-header.php',['ids'=>$transfer_ids]); ?>
    </div>

    <div id="inventory_card_line" class="card mt-3">
        <?php wc_get_template('template-parts/dashboard/components/inventory/card-table-line.php',['id'=>$transfer_ids[0]]); ?>
    </div>
</div>
