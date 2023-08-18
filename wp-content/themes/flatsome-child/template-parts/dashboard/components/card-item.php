<?php

defined( 'ABSPATH' ) || exit;

if ( ! $args || !$args['class'] ) {
    return;
}

?>

<div class="small-box bg-<?php echo $args['class'] ?>">
    <div class="inner">
        <h3><?php echo $args['quantity'] ?></h3>

        <p><?php echo $args['title'] ?></p>
    </div>
    <div class="icon">
        <i class="<?php echo $args['icon_big']?>"></i>
    </div>
    <a href="<?php echo $args['url']?>" class="small-box-footer">Xem thông tin<i class="fas fa-arrow-circle-right"></i></a>
</div>
