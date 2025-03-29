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
include "./views/header.php";
?>

<div class="main-container">

  <div id=news-container class="news-grid">
<!-- AddToAny BEGIN -->
<div class="a2a_kit a2a_kit_size_32 a2a_default_style">
<a class="a2a_dd" href="https://www.addtoany.com/share"></a>
<a class="a2a_button_facebook"></a>
<a class="a2a_button_x"></a>
<a class="a2a_button_email"></a>

<script>
var a2a_config = a2a_config || {};
a2a_config.templates = a2a_config.templates || {};
a2a_config.templates.email = {
    subject: "Subject Preset Text Here: ${title}",
    body: "Body Preset Text Here: \n${link}",
};
a2a_config.templates.x = {
    text: "X Preset Text Here: ${title} ${link}",
};
</script>
</div>
<script defer src="https://static.addtoany.com/menu/page.js"></script>
<!-- AddToAny END -->
  </div>
  <div class="middle-container">
    <div id="app">
      <div id="news-list">
        <div id="card-container" class="card-container"></div>
      </div>
    </div>
    <script>
      // TODO: Replace this with actual news data from the database
      const items = [
        { 
          title: "新闻标题1", 
          content: "这是第一条新闻的内容描述...",
          image: "https://picsum.photos/400/200?random=1"
        },
        { 
          title: "新闻标题2", 
          content: "这是第二条新闻的内容描述...",
          image: "https://picsum.photos/400/200?random=2"
        },
        { 
          title: "新闻标题3", 
          content: "这是第三条新闻的内容描述...",
          image: "https://picsum.photos/400/200?random=3"
        }
      ];
      
      const cardContainer = document.getElementById("card-container");
      items.forEach((item, index) => {
        const card = document.createElement("div");
        card.className = "card";
        card.innerHTML = `
          <div class="card-image">
            <img src="${item.image}" alt="${item.title}">
          </div>
          <div class="card-content">
            <h3>${item.title}</h3>
            <p>${item.content}</p>
          </div>
        `;
        cardContainer.appendChild(card);
      });
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



