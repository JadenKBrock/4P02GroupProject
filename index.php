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

$user_id = $_SESSION['user_id'] ?? null;

$serverName = "ts19cpsqldb.database.windows.net";
$connectionOptions = array(
    "Database" => "ts19cpdb3p96",
    "Uid" => "ts19cp",
    "PWD" => "@Group93p96",
    "TrustServerCertificate" => true
);

$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn == false) {
  die(print_r(sqlsrv_errors(), true));
}

// 测试表查询
$test_sql = "SELECT TOP 1 * FROM Posts";
$test_stmt = sqlsrv_query($conn, $test_sql);


// Get news data
$sql = "SELECT post_id, user_id, post_content, creation_date, post_type FROM Posts WHERE user_id = ?";
$params = array($user_id);
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

//$base_url = "http://localhost:8080/";
$base_url = "https://" . $_SERVER['HTTP_HOST'] . "/";

$page_title = "News Portal";
$page_styles = ["dashboard.css"];
include "./views/header.php";
?>

<div class="main-container">
  <div class="sidebar">
    <div class="filter-sub-container">
      <div class="filter-dropdowns">
        <!-- New Sort Filter -->
        <div class="sort-filter">
          <label for="sort-select">Sort by:</label>
          <select id="sort-select">
            <option value="desc" selected>Date:Most Recent</option>
            <option value="asc">Date:Oldest</option>
            <option value="asc">Relevance</option>
          </select>
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
        <button id="clear-btn">Clear Filters</button>
      </div>
    </div>
    <div class="filter-sub-container">
      <div class="search-container">
        <input type="text" id="search-input" placeholder="Search articles...">
        <button id="search-btn">Search</button>
      </div>
    </div>  
  </div>


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
      const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;
      
      if (!cardContainer) {
        console.error('Card container not found');
      } else if (!items || items.length === 0) {
        
        document.querySelector(".middle-container").classList.add("middle-container-no-posts");
        cardContainer.classList.add("middle-container-no-posts");
        if (!userId) {
          cardContainer.innerHTML = '<div class="no-posts-message"><a href="<?php echo $base_url;?>src/Login/login_pageNew.php">Login</a> to see your saved posts or <a href="<?php echo $base_url;?>src/Register/register_pageNew.php">Register</a> now to create your first post!</div>';
        } else {
          cardContainer.innerHTML = '<div class="no-posts-message"><a href="<?php echo $base_url;?>src/Generate/generate_page.php">Generate</a> your first post!</div>';
        }
      } else {
        // Sort by date
        items.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            return dateB - dateA; // Sort in descending order, newest first
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
          
          // Add edit button
          const editButton = document.createElement("button");
          editButton.className = "edit-btn";
          editButton.textContent = "edit";
          editButton.onclick = function() {
            const card = this.closest('.card');
            const currentContent = card.querySelector('p').textContent;
            editPost(item.id, currentContent);
          };
          
          // Create share buttons
          const shareDiv = document.createElement("div");
          shareDiv.className = "share-buttons";
          shareDiv.innerHTML = `
            <div 
                class="a2a_kit a2a_kit_size_32 a2a_default_style" 
                data-a2a-title="${cleanContent}">
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

          // add display animation
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
      var a2a_config = a2a_config || {};
      a2a_config.templates = a2a_config.templates || {};
      a2a_config.templates.email = {
        subject: "",
        body: ""
      };

</script>
    <script>
      // Edit post function
      function editPost(postId, currentContent) {
        const card = event.target.closest('.card');
        if (!card) {
          console.error('Card not found');
          return;
        }
        
        const contentP = card.querySelector('p');
        if (!contentP) {
          console.error('Content paragraph not found');
          return;
        }
        
        // Create edit area
        const editArea = document.createElement('textarea');
        editArea.value = currentContent;
        editArea.className = 'edit-textarea';
        
        // Create save and cancel buttons
        const buttonContainer = document.createElement('div');
        buttonContainer.className = 'edit-buttons';
        
        const saveButton = document.createElement('button');
        saveButton.textContent = 'save';
        saveButton.onclick = function() {
           if (editArea.value.trim() === '') {
            alert('Edit cannot be empty.');
            return;
          }    
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
        
        // Replace content
        contentP.style.display = 'none';
        contentP.parentNode.insertBefore(editArea, contentP);
        contentP.parentNode.insertBefore(buttonContainer, editArea.nextSibling);
      }
      
      // Save post function
      function savePost(postId, newContent) {
        // Send to server
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
            // Update display
            const card = document.querySelector(`.card[data-id="${postId}"]`);
            if (!card) {
              console.error('Card not found');
              return;
            }
            const contentP = card.querySelector('p');
            if (!contentP) {
              console.error('Content paragraph not found');
              return;
            }
            
            contentP.textContent = newContent;
            contentP.style.display = 'block';
            
            // Remove edit area
            const editArea = card.querySelector('.edit-textarea');
            const buttonContainer = card.querySelector('.edit-buttons');
            if (editArea) editArea.remove();
            if (buttonContainer) buttonContainer.remove();
          } else {
            alert('Save failed: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Save failed, please try again');
        });
      }
    </script>
  </div>
</div>

<?php
include "./views/footer.php";
//ob_end_flush();
?>

<script>
// search, platform filter and sort function
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
    
    // initial loading animation
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
    
    // show/hide platform options
    roleSelectorBtn.addEventListener('click', function() {
        roleOptions.classList.toggle('hidden');
    });
    
    // select platform
    roleButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedPlatform = this.textContent;
            roleSelectorBtn.textContent = `Platform: ${selectedPlatform}`;
            roleOptions.classList.add('hidden');
            performFilter();
        });
    });
    
    // clear filters
    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        selectedPlatform = null;
        roleSelectorBtn.textContent = 'Choose Platform';
        sortSelect.value = 'desc';
        
        // first let all cards slide out
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.remove('filtering-in');
            card.classList.add('filtering-out');
        });
        
        // wait for animation to complete, then show all cards
        setTimeout(() => {
            // clear current container
            cardContainer.innerHTML = '';
            
            // restore initial card order and add animation
            initialCardOrder.forEach((card, index) => {
                // reset all animation related classes
                card.classList.remove('filtering-out');
                card.classList.add('filtering-in');
                card.style.display = 'block';
                
                setTimeout(() => {
                    cardContainer.appendChild(card);
                }, index * 100);
            });
        }, 500); 
    });
    
    // sort function
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
    
    // filter and sort function
    function performFilter() {
        const searchTerm = searchInput.value.toLowerCase();
        const cards = document.querySelectorAll('.card');
        
        // first let all cards slide out
        cards.forEach(card => {
            card.classList.remove('filtering-in');
            card.classList.add('filtering-out');
        });
        
        // wait for animation to complete, then filter and sort
        setTimeout(() => {
            // first filter
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
            
            // then sort
            const filteredCards = Array.from(cards).filter(card => card.style.display !== 'none');
            const sortedCards = sortCards(filteredCards);
            
            // clear container
            cardContainer.innerHTML = '';
            
            // rearrange and add slide in animation
            sortedCards.forEach((card, index) => {
                card.classList.remove('filtering-out');
                card.classList.add('filtering-in');
                card.style.display = 'block';
                
                setTimeout(() => {
                    cardContainer.appendChild(card);
                }, index * 100);
            });
        }, 500); 
    }
    
    // listen to search button click
    searchBtn.addEventListener('click', performFilter);
    
    // listen to input box enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performFilter();
        }
    });
    
    // listen to sort change
    sortSelect.addEventListener('change', performFilter);
    
    // initialize card animation
    initializeCards();
});
</script>
