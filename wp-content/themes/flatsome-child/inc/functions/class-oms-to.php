<?php

namespace OMS;

class OMS_TO extends \WC_Order
{

    public array $ORDER_STATUS_LABEL = array(
        'reject' => [
            'title' => 'Đã hủy',
            'class_name' => 'badge-danger'
        ],
        'trash' => [
            'title' => 'Xóa',
            'class_name' => 'badge-danger'
        ],
        'on-hold' => [
            'title' => 'Đang giữ',
            'class_name' => 'badge-danger'
        ],
        'pending' => [
            'title' => 'Tạo mới',
            'class_name' => 'badge-primary'
        ],
        'processing' => [
            'title' => 'Đang xử lý',
            'class_name' => 'badge-warning'
        ],
        'confirm' => [
            'title' => 'Xác nhận',
            'class_name' => 'badge-primary'
        ],
        'completed' => [
            'title' => 'Đã nhập tồn',
            'class_name' => 'badge-success'
        ],
        'request' => [
            'title' => 'Gọi lấy hàng',
            'class_name' => 'badge-info'
        ],
        'shipping' => [
            'title' => 'Đang giao hàng',
            'class_name' => 'badge-info'
        ],
        'delivered' => [
            'title' => 'Đã giao hàng',
            'class_name' => 'badge-success'
        ],
        'delivery-failed' => [
            'title' => 'Giao hàng thất bại',
            'class_name' => 'badge-danger'
        ],
        'cancelled' => [
            'title' => 'Đã hủy',
            'class_name' => 'badge-danger'
        ],
        'auto-draft' => [
            'title' => 'Tự động lưu',
            'class_name' => 'badge-secondary'
        ],
        'confirm-goods' => [
            'title' => 'Đã trả lại tồn kho',
            'class_name' => 'badge-warning'
        ],
        'draft' => [
            'title' => 'Xóa',
            'class_name' => 'badge-danger'
        ],
    );
    public function __construct($order = 0)
    {
        parent::__construct($order);
    }

    public function get_type(): string
    {
        return 'transfer_order';
    }

    public function get_status_title()
    {
        return $this->ORDER_STATUS_LABEL[$this->get_status()]['title'];
    }

    public function get_status_class_name()
    {
        return $this->ORDER_STATUS_LABEL[$this->get_status()]['class_name'];
    }

}