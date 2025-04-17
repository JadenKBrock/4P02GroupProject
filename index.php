<?php
//ob_start();
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => $_SERVER['HTTP_HOST'],
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();

// 测试用的user_id，方便测试不同用户
$test_user_id = 3;  // 可以随时修改这个值来测试不同用户

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
$sql = "SELECT post_id, user_id, post_content, creation_date, post_type FROM Posts WHERE user_id = ?";
$params = array($test_user_id);
$stmt = sqlsrv_query($conn, $sql, $params);
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
        // 按时间排序
        items.sort((a, b) => {
          const dateA = new Date(a.date);
          const dateB = new Date(b.date);
          return dateB - dateA; // 降序排列，最新的在前
        });

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
          
          // 添加显示动画
          requestAnimationFrame(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            requestAnimationFrame(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            });
          });
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
        subject: "",
        body: "{currentContent}"
      };
      a2a_config.templates.x = {
        text: "{currentContent}"
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
          Choose Platform <span class="arrow-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16">
              <path fill="currentColor" d="M7 10l5 5 5-5H7z"/>
            </svg>
          </span>
        </button>
        <div id="role-options" class="role-options hidden">
          <button id="role-business-btn" class="role-option">Facebook</button>
          <button id="role-influencer-btn" class="role-option">X (Twitter)</button>
          <button id="role-email-btn" class="role-option">Email</button>
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

<style>
/* 卡片动画效果 */
.card {
    transition: all 0.5s ease-in-out;
    transform-origin: center;
}

.card.hiding {
    transform: scale(0.98);
    opacity: 0;
}

.card.showing {
    transform: scale(1);
    opacity: 1;
}

/* 过滤动画 */
@keyframes filterIn {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes filterOut {
    from {
        transform: translateY(0);
        opacity: 1;
    }
    to {
        transform: translateY(-20px);
        opacity: 0;
    }
}

.card.filtering-in {
    animation: filterIn 0.5s ease-out forwards;
}

.card.filtering-out {
    animation: filterOut 0.5s ease-out forwards;
}

/* 排序动画 */
@keyframes scaleIn {
    from {
        transform: scale(0.98);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.card.sorting {
    animation: scaleIn 0.5s ease-out;
}

/* 平台选择器动画 */
.role-options {
    transition: all 0.5s ease-in-out;
    transform-origin: top;
}

.role-options.hidden {
    transform: scaleY(0);
    opacity: 0;
    display: none;
}

.role-options:not(.hidden) {
    transform: scaleY(1);
    opacity: 1;
    display: block;
}

/* 搜索框焦点效果 */
#search-input:focus {
    transform: scale(1.01);
    transition: transform 0.3s ease-in-out;
}

/* 按钮悬停效果 */
button {
    transition: all 0.3s ease-in-out;
}

button:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>

<?php
include "./views/footer.php";
//ob_end_flush();
?>

<script>
// 搜索、平台过滤和排序功能实现
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const roleSelectorBtn = document.getElementById('role-selector-btn');
    const roleOptions = document.getElementById('role-options');
    const roleButtons = document.querySelectorAll('.role-option');
    const clearBtn = document.getElementById('clear-btn');
    const sortSelect = document.getElementById('sort-select');
    const cardContainer = document.querySelector('.card-container');
    
    let selectedPlatform = null;
    let initialCardOrder = Array.from(cardContainer.children);
    
    // 初始加载动画
    function initializeCards() {
        const cards = Array.from(cardContainer.children);
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 300);
        });
    }
    
    // 显示/隐藏平台选项
    roleSelectorBtn.addEventListener('click', function() {
        roleOptions.classList.toggle('hidden');
    });
    
    // 选择平台
    roleButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedPlatform = this.textContent;
            roleSelectorBtn.textContent = `Platform: ${selectedPlatform}`;
            roleOptions.classList.add('hidden');
            performFilter();
        });
    });
    
    // 清除过滤器
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        selectedPlatform = null;
        roleSelectorBtn.textContent = 'Choose Platform';
        sortSelect.value = 'desc';
        
        // 先让所有卡片滑出
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.remove('filtering-in');
            card.classList.add('filtering-out');
        });
        
        // 等待动画完成后重新显示所有卡片
        setTimeout(() => {
            // 清空当前容器
            cardContainer.innerHTML = '';
            
            // 恢复初始卡片顺序并添加动画
            initialCardOrder.forEach((card, index) => {
                // 重置所有动画相关的类
                card.classList.remove('filtering-out');
                card.classList.add('filtering-in');
                card.style.display = 'block';
                
                setTimeout(() => {
                    cardContainer.appendChild(card);
                }, index * 100);
            });
        }, 500); // 从800ms缩短到500ms
    });
    
    // 排序函数
    function sortCards(cards) {
        const sortBy = sortSelect.value;
        const cardArray = Array.from(cards);
        
        cardArray.sort((a, b) => {
            const dateA = new Date(a.querySelector('.post-date').textContent);
            const dateB = new Date(b.querySelector('.post-date').textContent);
            
            switch(sortBy) {
                case 'desc':
                    return dateB - dateA;
                case 'asc':
                    return dateA - dateB;
                case 'relevance':
                    if (searchInput.value) {
                        const searchTerm = searchInput.value.toLowerCase();
                        const contentA = a.querySelector('p').textContent.toLowerCase();
                        const contentB = b.querySelector('p').textContent.toLowerCase();
                        const countA = (contentA.match(new RegExp(searchTerm, 'g')) || []).length;
                        const countB = (contentB.match(new RegExp(searchTerm, 'g')) || []).length;
                        return countB - countA;
                    }
                    return dateB - dateA;
                default:
                    return 0;
            }
        });
        
        return cardArray;
    }
    
    // 过滤和排序函数
    function performFilter() {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = document.querySelectorAll('.card');
        
        // 先让所有卡片滑出
        cards.forEach(card => {
            card.classList.remove('filtering-in');
            card.classList.add('filtering-out');
        });
        
        // 等待动画完成后进行过滤和排序
        setTimeout(() => {
            // 先过滤
            cards.forEach(card => {
                const content = card.querySelector('p').textContent.toLowerCase();
                const type = card.querySelector('.post-type').textContent;
                
                let showCard = true;
                
                if (searchTerm) {
                    showCard = content.includes(searchTerm);
                }
                
                if (selectedPlatform) {
                    showCard = showCard && type === selectedPlatform;
                }
                
                if (!showCard) {
                    card.style.display = 'none';
                }
            });
            
            // 再排序
            const filteredCards = Array.from(cards).filter(card => card.style.display !== 'none');
            const sortedCards = sortCards(filteredCards);
            
            // 清空容器
            cardContainer.innerHTML = '';
            
            // 重新排列并添加滑入动画
            sortedCards.forEach((card, index) => {
                card.classList.remove('filtering-out');
                card.classList.add('filtering-in');
                card.style.display = 'block';
                
                setTimeout(() => {
                    cardContainer.appendChild(card);
                }, index * 100);
            });
        }, 500); // 从800ms缩短到500ms
    }
    
    // 监听搜索按钮点击
    searchBtn.addEventListener('click', performFilter);
    
    // 监听输入框回车
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performFilter();
        }
    });
    
    // 监听排序变化
    sortSelect.addEventListener('change', performFilter);
    
    // 初始化卡片动画
    initializeCards();
});
</script>
