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
            <pre><code class="bash">
# Here is a curl example
curl \
-X POST http://api.westeros.com/character/get \
-F 'secret_key=your_api_key' \
-F 'house=Stark,Bolton' \
-F 'offset=0' \
-F 'limit=50'
                </code></pre>
            <p>
                To get characters you need to make a POST call to the following url :<br>
                <code class="higlighted break-word">http://api.westeros.com/character/get</code>
            </p>
            <br>
            <pre><code class="json">
Result example :

{
  query:{
    offset: 0,
    limit: 50,
    house: [
      "Stark",
      "Bolton"
    ],
  }
  result: [
    {
      id: 1,
      first_name: "Jon",
      last_name: "Snow",
      alive: true,
      house: "Stark",
      gender: "m",
      age: 14,
      location: "Winterfell"
    },
    {
      id: 2,
      first_name: "Eddard",
      last_name: "Stark",
      alive: false,
      house: "Stark",
      gender: "m",
      age: 35,
      location: 'Winterfell'
    },
    {
      id: 3,
      first_name: "Catelyn",
      last_name: "Stark",
      alive: false,
      house: "Stark",
      gender: "f",
      age: 33,
      location: "Winterfell"
    },
    {
      id: 4,
      first_name: "Roose",
      last_name: "Bolton",
      alive: false,
      house: "Bolton",
      gender: "m",
      age: 40,
      location: "Dreadfort"
    },
    {
      id: 5,
      first_name: "Ramsay",
      last_name: "Snow",
      alive: false,
      house: "Bolton",
      gender: "m",
      age: 15,
      location: "Dreadfort"
    },
  ]
}
                </code></pre>
            <h4>QUERY PARAMETERS</h4>
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
                    <td>secret_key</td>
                    <td>String</td>
                    <td>Your API key.</td>
                </tr>
                <tr>
                    <td>search</td>
                    <td>String</td>
                    <td>(optional) A search word to find character by name.</td>
                </tr>
                <tr>
                    <td>house</td>
                    <td>String</td>
                    <td>
                        (optional) a string array of houses:
                    </td>
                </tr>
                <tr>
                    <td>alive</td>
                    <td>Boolean</td>
                    <td>
                        (optional) a boolean to filter alived characters
                    </td>
                </tr>
                <tr>
                    <td>gender</td>
                    <td>String</td>
                    <td>
                        (optional) a string to filter character by gender:<br>
                        m: male<br>
                        f: female
                    </td>
                </tr>
                <tr>
                    <td>offset</td>
                    <td>Integer</td>
                    <td>(optional - default: 0) A cursor for use in pagination. Pagination starts offset the specified offset.</td>
                </tr>
                <tr>
                    <td>limit</td>
                    <td>Integer</td>
                    <td>(optional - default: 10) A limit on the number of objects to be returned, between 1 and 100.</td>
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