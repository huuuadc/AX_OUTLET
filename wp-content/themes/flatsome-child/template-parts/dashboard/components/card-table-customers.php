<?php

    $args = array(
        'role'       => 'customer',
        'orderby'    => 'ID',
        'order'      => 'DESC'
    );

    $users = get_users($args);
    $count = 0;

?>

<div class="card">
    <div class="card-header">
        Danh mục
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="table_categories" class="table table-bordered table-striped dataTable dtr-inline table_simple">
            <thead>
            <tr>
                <th>Số TT</th>
                <th>ID</th>
                <th>Username</th>
                <th>Tên hiển thị</th>
                <th>Quyền</th>
                <th>Email</th>
                <th>Ngày đăng ký</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user) :$count++;?>
                <tr>
                    <td><?php echo $count ?></td>
                    <td><?php echo $user->ID ?></td>
                    <td><?php echo $user->user_login ?></td>
                    <td><?php echo $user->display_name ?></td>
                    <td><?php echo implode(',', $user->roles) ?></td>
                    <td><?php echo $user->user_email?></td>
                    <td><?php echo $user->user_registered?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <div class="card-footer"></div>
</div>