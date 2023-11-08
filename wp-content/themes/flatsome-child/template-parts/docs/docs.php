<div class="left-menu">
    <div class="content-logo">
        <div class="logo">
            <img alt="oms-logo" title="platform by Emily van den Heever from the Noun Project" src="/wp-content/themes/flatsome-child/assets/docs/images/logo.png" height="32" />
            <span>OMS API Docs</span>
        </div>
        <button class="burger-menu-icon" id="button-menu-mobile">
            <svg width="34" height="34" viewBox="0 0 100 100"><path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058"></path><path class="line line2" d="M 20,50 H 80"></path><path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942"></path></svg>
        </button>
    </div>
    <div class="mobile-menu-closer"></div>
    <div class="content-menu">
        <div class="content-infos">
            <div class="info"><b>Phiên bản:</b> 1.0.0</div>
            <div class="info"><b>Cập nhật gần nhất:</b> 11-06-2023</div>
        </div>
        <ul>
            <li class="scroll-to-link active" data-target="content-get-started">
                <a>Lời nói đầu</a>
            </li>
            <li class="scroll-to-link" data-target="content-authentication">
                <a>Xác thực</a>
            </li>
            <li class="scroll-to-link" data-target="content-create-order">
                <a>Tạo đơn hàng mới</a>
            </li>
            <li class="scroll-to-link" data-target="content-errors">
                <a>Trạng thái lỗi</a>
            </li>
        </ul>
    </div>
</div>
<div class="content-page">
    <div class="content-code"></div>
    <div class="content">
        <div class="overflow-hidden content-section" id="content-get-started">
            <h1>Lời nói đầu</h1>
            <pre>
Base Url:

    https://glam-test.rexarcade.com.vn
                </pre>
            <p>
                Đây là tài liệu kết nối với hệ thống OMS
            </p>
            <p>
                Để sử dụng được api bạn cần liên hệ với bộ phận IT Dafc để cung cấp REST API Consumer Key và REST API Consumer Secret.
           </p>
            <p>
                Hoặc liên hệ qua mail: <a href="mailto:huu.tran@dafc.com.vn">huu.tran@dafc.com.vn</a>
            </p>
        </div>
        <div class="overflow-hidden content-section" id="content-authentication">
            <h1>Xác thực</h1>
            <pre><code class="bash">
curl https://www.example.com/wp-json/wc/v3/orders \
-u consumer_key:consumer_secret
                </code>
            </pre>
            <p>
                Xử dụng HTTP Basic Auth bằng cách cung cấp:
            </p>
            <p>
                - REST API Consumer Key như là tài khoản.<br>
                - REST API Consumer Secret như là mật khẩu.
            </p>
        </div>
        <div class="overflow-hidden content-section" id="content-create-order">
            <h2>Tạo đơn hàng mới</h2>
            <pre>
                <code class="bash">
# Here is a curl example
curl https://www.example.com/wp-json/v1/orders/create \
-u consumer_key:consumer_secret
                </code>
            </pre>
            <p>
                Method : <code class="higlighted break-word">POST</code><br>
                Endpoint :<code class="higlighted break-word">/wp-json/v1/orders/create</code>
            </p>
            <br>
            <pre><code class="json">
Request body example :

{
    "order_key" : "24d7546fsdfaddsfcsa",
    "order_type": "shopee",
    "billing": {
        "first_name": "Huu",
        "last_name": "Tran",
        "company":"",
        "city":"Hồ Chí Minh",
        "district":"Quận 3",
        "ward":"Phường Võ Thị Sáu",
        "ward_code" :"11111",
        "address" :"72 74 nguyễn thị minh khai",
        "full_address" : "72-74 nguyễn thị minh khai, Phường võ thị sáu, Quận 3, Hồ Chí Minh",
        "email": "huuuadc@gmail.com"
    },
    "buyer_message": "Giao hàng giờ hành chính",
    "shipping_method": "shopee",
    "tracking_id": "",
    "payment_method": "shopee",
    "payment": {
        "subtotal": 55555555,
        "shipping_fee" : 40000,
        "total" : 55595555
    },
    "items": [
        {
            "sku": "1119735",
            "barcode": "1119735",
            "name": "Túi đựng",
            "price": 22222222
        },
        {
            "sku": "1112673000",
            "barcode": "1112673000",
            "name": "Quần dài nữ phom suông kẻ sọc - white, 0036",
            "price": 33333333
        }
    ]
}


Response example :

{
    "success": true,
    "data": {
        "id": 82789,
        "parent_id": 0,
        "status": "processing",
        "currency": "VND",
        "version": "8.0.1",
        "prices_include_tax": false,
        "date_created": {
            "date": "2023-11-08 09:14:06.000000",
            "timezone_type": 1,
            "timezone": "+00:00"
        },
        "date_modified": {
            "date": "2023-11-08 09:14:06.000000",
            "timezone_type": 1,
            "timezone": "+00:00"
        },
        "discount_total": "0",
        "discount_tax": "0",
        "shipping_total": "40000",
        "shipping_tax": "0",
        "cart_tax": "0",
        "total": "55595555",
        "total_tax": "0",
        "customer_id": 0,
        "order_key": "24d7546fsdfaddssa",
        "billing": {
            "first_name": "Huu",
            "last_name": "Tran",
            "company": "",
            "address_1": "72-74 nguyễn thị minh khai, Phường võ thị sáu, Quận 3, Hồ Chí Minh",
            "address_2": "",
            "city": "Hồ Chí Minh",
            "state": "",
            "postcode": "",
            "country": "VN",
            "email": "huuuadc@gmail.com",
            "phone": ""
        },
        "shipping": {
            "first_name": "",
            "last_name": "",
            "company": "",
            "address_1": "",
            "address_2": "",
            "city": "",
            "state": "",
            "postcode": "",
            "country": "",
            "phone": ""
        },
        "payment_method": "shopee",
        "payment_method_title": "Thanh toán qua sàn",
        "transaction_id": "",
        "customer_ip_address": "",
        "customer_user_agent": "",
        "created_via": "",
        "customer_note": "Giao hàng giờ hành chính",
        "date_completed": null,
        "date_paid": {
            "date": "2023-11-08 09:14:06.000000",
            "timezone_type": 1,
            "timezone": "+00:00"
        },
        "cart_hash": "",
        "order_stock_reduced": true,
        "download_permissions_granted": true,
        "new_order_email_sent": true,
        "recorded_sales": true,
        "recorded_coupon_usage_counts": true,
        "number": "82789",
        "meta_data": [],
        "line_items": {
            "546": {},
            "547": {}
        },
        "tax_lines": [],
        "shipping_lines": {
            "548": {}
        },
        "fee_lines": [],
        "coupon_lines": []
    },
    "message": "success"
}
                </code></pre>
            <h4>REQUEST BODY PARAMETERS</h4>
            <table class="central-overflow-x">
                <thead>
                <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>order_key</td>
                    <td>tring</td>
                    <td>(Bắt buộc) Mã đơn hàng liên kết</td>
                </tr>
                <tr>
                    <td>order_type</td>
                    <td>string</td>
                    <td>(Bắt buộc) Loại đơn hàng từ platform nào. Trong các loại sau:
                        <code class="higlighted break-word">shopee</code>
                        <code class="higlighted break-word">lazada</code>
                        <code class="higlighted break-word">tiktok</code>
                        <code class="higlighted break-word">website</code>
                    </td>
                </tr>
                <tr>
                    <td>billing</td>
                    <td>object</td>
                    <td>
                        (Bắt buộc) Thông tin hóa đơn và giao hàng.
                    </td>
                </tr>
                <tr>
                    <td>first_name</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Họ của người mua hàng
                    </td>
                </tr>
                <tr>
                    <td>last_name</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Tên của người mua hàng
                    </td>
                </tr>
                <tr>
                    <td>company</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Tên công ty
                    </td>
                </tr>
                <tr>
                    <td>city</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Thành phố
                    </td>
                </tr>
                <tr>
                    <td>district</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Quận huyện
                    </td>
                </tr>
                <tr>
                    <td>ward</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Phường xã
                    </td>
                </tr>
                <tr>
                    <td>ward_code</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Mã phường xã (Đỗi với đơn được giao hàng với nhà vận chuyển)
                    </td>
                </tr>
                <tr>
                    <td>address</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Địa chỉ chi tiết (Số tần, số nhà, tên đường,...)
                    </td>
                </tr>
                <tr>
                    <td>full_address</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Địa chỉ đầy đủ
                    </td>
                </tr>
                <tr>
                    <td>email</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) email của người mua hàng
                    </td>
                </tr>
                <tr>
                    <td>buyer_message</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Ghi chú của người mua hàng.
                    </td>
                </tr>
                <tr>
                    <td>shipping_method</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Phuương thức vận chuyển:
                        <code class="higlighted break-word">shopee</code>
                        <code class="higlighted break-word">lazada</code>
                        <code class="higlighted break-word">tiktok</code>
                        <code class="higlighted break-word">tnsl</code>
                        <code class="higlighted break-word">sbp</code>
                    </td>
                </tr>
                <tr>
                    <td>tracking_id</td>
                    <td>string</td>
                    <td>
                        (Không bắt buộc) Mã đơn vận chuyển.
                    </td>
                </tr>
                <tr>
                    <td>payment_method</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Phuương thức vận chuyển:
                        <code class="higlighted break-word">shopee</code>
                        <code class="higlighted break-word">lazada</code>
                        <code class="higlighted break-word">tiktok</code>
                        <code class="higlighted break-word">cod</code>
                        <code class="higlighted break-word">bacs</code>
                    </td>
                </tr>
                <tr>
                    <td>payment</td>
                    <td>object</td>
                    <td>
                        (Bắt buộc) Thông tin tiền thanh toán.
                    </td>
                </tr>
                <tr>
                    <td>subtotal</td>
                    <td>double>
                    <td>
                        (Bắt buộc) Tổng số tiền hàng.
                    </td>
                </tr>
                <tr>
                    <td>shipping_fee</td>
                    <td>double</td>
                    <td>
                        (Bắt buộc) Số tiền vận chuyển.
                    </td>
                </tr>
                <tr>
                    <td>total</td>
                    <td>double</td>
                    <td>
                        (Bắt buộc) Tổng số của đơn hàng.
                    </td>
                </tr>
                <tr>
                    <td>items</td>
                    <td>array</td>
                    <td>
                        (Bắt buộc) Danh sách hàng hóa của đơn hàng.
                    </td>
                </tr>
                <tr>
                    <td>sku</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) sku của website.
                    </td>
                </tr>
                <tr>
                    <td>barcode</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Mã rfid.
                    </td>
                </tr>
                <tr>
                    <td>name</td>
                    <td>string</td>
                    <td>
                        (Bắt buộc) Danh sách hàng hóa của đơn hàng.
                    </td>
                </tr>
                <tr>
                    <td>price</td>
                    <td>array</td>
                    <td>
                        (Bắt buộc) Giá bán trên platform.
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <div class="overflow-hidden content-section" id="content-errors">
            <h2>Trạng thái lỗi</h2>
            <p>
                Đôi khi bạn có thể gặp lỗi khi truy cập REST API. Có bốn loại có thể:
            </p>
            <table>
                <thead>
                <tr>
                    <th>Mã lỗi</th>
                    <th>Ý nghĩa</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>400</td>
                    <td>
                        Yêu cầu không hợp lệ, ví dụ: sử dụng phương thức HTTP không được hỗ trợ.
                    </td>
                </tr>
                <tr>
                    <td>401</td>
                    <td>
                        Lỗi xác thực hoặc quyền, ví dụ: khóa API không chính xác.
                    </td>
                </tr>
                <tr>
                    <td>404</td>
                    <td>
                        Yêu cầu tài nguyên không tồn tại hoặc bị thiếu.
                    </td>
                </tr>
                <tr>
                    <td>500</td>
                    <td>
                        Lỗi máy chủ.
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="content-code"></div>
</div>