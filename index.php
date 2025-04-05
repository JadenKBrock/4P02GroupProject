<!-- Currently this index.php page is the DASHBOARD PAGE.-->
<!-- This index.php page should be our landing page (whatever page that may be).-->
<!-- That being said, the stylesheet(s) and script(s) which are in the variables, page_styles and page_scripts belong only to the dashboard page. -->
<!-- If we change this landing page to be something other than the dashboard page, we need to change the values of those variables to reflect the change.-->

<?php
session_start();
$_SESSION['user_id'] = $user_id;
$_SESSION['profile_pic'] = isset($profile_pic_url) ? $profile_pic_url : 'default-profile-pic.png';


$page_title = "News Portal";
$page_styles = ["dashboard.css"];

$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

// 连接数据库
$conn = sqlsrv_connect($serverName, $connectionOptions);
// if ($conn === false) {
//     die(print_r(sqlsrv_errors(), true));
// } else {
//     echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px;'>数据库连接成功！</div>";
// }

// 测试表查询
$test_sql = "SELECT TOP 1 * FROM Posts";
$test_stmt = sqlsrv_query($conn, $test_sql);
// if ($test_stmt === false) {
//     echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px;'>表查询失败：" . print_r(sqlsrv_errors(), true) . "</div>";
// } else {
//     echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px;'>Posts表查询成功！</div>";
    
//     // 检查表结构
//     $columns = sqlsrv_field_metadata($test_stmt);
//     echo "<div style='color: blue; padding: 10px; border: 1px solid blue; margin: 10px;'>";
//     echo "表结构：<br>";
//     foreach ($columns as $column) {
//         echo "列名: " . $column['Name'] . "<br>";
//     }
//     echo "</div>";
    
//     sqlsrv_free_stmt($test_stmt);
// }

// Get news data
$sql = "SELECT post_id, user_id, post_content, creation_date, post_type FROM Posts";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$newsItems = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    // Process content
    $content = $row['post_content'];
    $type = $row['post_type'];
    
    // Clean invalid UTF-8 characters
    $content = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $content);
    $content = preg_replace('/\xEF\xBF\xBD/', '', $content);
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
    
    $type = mb_convert_encoding($type, 'UTF-8', 'UTF-8');
    
    $newsItems[] = [
        'id' => $row['post_id'],
        'userId' => $row['user_id'],
        'content' => $content,
        'date' => $row['creation_date']->format('Y-m-d H:i:s'),
        'type' => $type
    ];
}

// Convert to JSON
$json_data = json_encode($newsItems, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
if ($json_data === false) {
    $newsItems = [];
    $json_data = '[]';
}

// 显示数据统计信息
// echo "<div style='color: blue; padding: 10px; border: 1px solid blue; margin: 10px;'>";
// echo "查询到的记录数: " . count($newsItems);
// echo "</div>";

// 如果数据为空，显示SQL语句以便调试
// if (count($newsItems) == 0) {
//     // 检查表中是否有数据
//     $check_sql = "SELECT COUNT(*) as count FROM Posts";
//     $check_stmt = sqlsrv_query($conn, $check_sql);
//     $row = sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC);
    
//     echo "<div style='color: orange; padding: 10px; border: 1px solid orange; margin: 10px;'>";
//     echo "警告：没有找到任何数据<br>";
//     echo "Posts表中的总记录数: " . $row['count'] . "<br>";
//     echo "执行的SQL语句: " . $sql;
//     echo "</div>";
// }

// // 调试：打印PHP中的数据
// echo "<script>console.log('PHP中的数据:', " . $json_data . ");</script>";

// 关闭连接
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

include "./views/header.php";
?>

<div class="main-container">

  <div id=news-container class="news-grid">
  </div>
  <div class="middle-container">
    <div id="app">
      <div id="news-list">
        <div id="card-container" class="card-container"></div>
      </div>
    </div>
    <script>
      const items = <?php echo $json_data; ?>;
      const cardContainer = document.getElementById("card-container");
      
      if (!cardContainer) {
        console.error('Card container not found');
      } else if (!items || items.length === 0) {
        cardContainer.innerHTML = '<div class="no-data-message">No data available</div>';
      } else {
        items.forEach(item => {
          const card = document.createElement("div");
          card.className = "card";
          card.style.backgroundImage = `url(https://picsum.photos/400/200?random=${item.id})`;
          
          const contentDiv = document.createElement("div");
          contentDiv.className = "card-content";
          
          const metaDiv = document.createElement("div");
          metaDiv.className = "post-meta";
          
          const typeSpan = document.createElement("span");
          typeSpan.className = "post-type";
          typeSpan.textContent = item.type;
          
          const dateSpan = document.createElement("span");
          dateSpan.className = "post-date";
          dateSpan.textContent = item.date;
          
          metaDiv.appendChild(typeSpan);
          metaDiv.appendChild(dateSpan);
          
          const contentP = document.createElement("p");
          const cleanContent = item.content.replace(/[\uFFFD]/g, '');
          contentP.textContent = cleanContent;
          
          // Create share buttons
          const shareDiv = document.createElement("div");
          shareDiv.className = "share-buttons";
          shareDiv.innerHTML = `
            <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                <a class="a2a_button_facebook"></a>
                <a class="a2a_button_x"></a>
                <a class="a2a_button_email"></a>
            </div>
          `;
          
          contentDiv.appendChild(metaDiv);
          contentDiv.appendChild(contentP);
          contentDiv.appendChild(shareDiv);
          
          card.appendChild(contentDiv);
          cardContainer.appendChild(card);
        });

        // Refresh AddToAny buttons
        if (window.a2a) {
          a2a.init_all();
        }
      }
    </script>
    <!-- AddToAny script -->
    <script async src="https://static.addtoany.com/menu/page.js"></script>
    <script>
      var a2a_config = a2a_config || {};
      a2a_config.templates = a2a_config.templates || {};
      a2a_config.templates.email = {
        subject: "Check out this news article",
        body: "I found this interesting article:\n${link}"
      };
      a2a_config.templates.x = {
        text: "Check out this news article: ${title}\n${link}"
      };
    </script>
  </div>
  <div class="sidebar">
    <h2>Filter News</h2>
      <div class="search-container">
        <input type="text" id="search-input" placeholder="Search articles...">
        <button id="search-btn">Search</button>
      </div>

      <!-- Role Selector Dropdown -->
      <div class="role-selector">
        <button id="role-selector-btn">
          Choose your audience/role <span class="arrow-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
              <path fill="currentColor" d="M7 10l5 5 5-5H7z"/>
            </svg>
          </span>
        </button>
        <div id="role-options" class="role-options hidden">
          <button id="role-business-btn" class="role-option">Business</button>
          <button id="role-influencer-btn" class="role-option">Influencer</button>
        </div>
      </div>

      <!-- New Sort Filter -->
      <div class="sort-filter">
        <label for="sort-select">Sort by:</label>
        <select id="sort-select">
          <option value="desc" selected>Date:Most Recent</option>
          <option value="asc">Date:Oldest</option>
          <option value="asc">Relevance</option>
        </select>
      </div>

      <button id="clear-btn">Clear Filters</button>
  </div>
</div>


<?php
$page_scripts = ["dashboard_script.js"];
include "./views/footer.php";
?>



