<!-- Currently this index.php page is the DASHBOARD PAGE.-->
<!-- This index.php page should be our landing page (whatever page that may be).-->
<!-- That being said, the stylesheet(s) and script(s) which are in the variables, page_styles and page_scripts belong only to the dashboard page. -->
<!-- If we change this landing page to be something other than the dashboard page, we need to change the values of those variables to reflect the change.-->

<?php
//ob_start();
session_start();

echo $_SESSION['user_id'];

$page_title = "News Portal";
$page_styles = ["dashboard.css"];

$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

$conn = sqlsrv_connect($serverName, $connectionOptions);


// 测试表查询
$test_sql = "SELECT TOP 1 * FROM Posts";
$test_stmt = sqlsrv_query($conn, $test_sql);


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
          card.setAttribute('data-id', item.id);
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
          
          // 添加编辑按钮
          const editButton = document.createElement("button");
          editButton.className = "edit-btn";
          editButton.textContent = "edit";
          editButton.onclick = function() {
            editPost(item.id, cleanContent);
          };
          
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
          contentDiv.appendChild(editButton);
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
    <script>
      // 编辑功能
      function editPost(postId, currentContent) {
        const card = event.target.closest('.card');
        if (!card) {
          console.error('cant find card');
          return;
        }
        
        const contentP = card.querySelector('p');
        if (!contentP) {
          console.error('cant find contentP');
          return;
        }
        
        // 创建编辑区域
        const editArea = document.createElement('textarea');
        editArea.value = currentContent;
        editArea.className = 'edit-textarea';
        
        // 创建保存和取消按钮
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'edit-buttons';
        
        const saveButton = document.createElement('button');
        saveButton.textContent = 'save';
        saveButton.onclick = function() {
          savePost(postId, editArea.value);
        };
        
        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'cancel';
        cancelButton.onclick = function() {
          contentP.style.display = 'block';
          editArea.remove();
          buttonContainer.remove();
        };
        
        buttonContainer.appendChild(saveButton);
        buttonContainer.appendChild(cancelButton);
        
        // 替换内容
        contentP.style.display = 'none';
        contentP.parentNode.insertBefore(editArea, contentP);
        contentP.parentNode.insertBefore(buttonContainer, editArea.nextSibling);
      }
      
      // 保存功能
      function savePost(postId, newContent) {
        // 发送到服务器
        fetch('update_post.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            post_id: postId,
            content: newContent
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // 更新显示
            const card = document.querySelector(`.card[data-id="${postId}"]`);
            if (!card) {
              console.error('cant find card');
              return;
            }
            const contentP = card.querySelector('p');
            if (!contentP) {
              console.error('cant find contentP');
              return;
            }
            
            contentP.textContent = newContent;
            contentP.style.display = 'block';
            
            // 移除编辑区域
            const editArea = card.querySelector('.edit-textarea');
            const buttonContainer = card.querySelector('.edit-buttons');
            if (editArea) editArea.remove();
            if (buttonContainer) buttonContainer.remove();
          } else {
            alert('save failed: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('save failed, please try again');
        });
      }
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
//ob_end_flush();
?>