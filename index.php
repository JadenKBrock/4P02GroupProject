<!-- Currently this index.php page is the DASHBOARD PAGE.-->
<!-- This index.php page should be our landing page (whatever page that may be).-->
<!-- That being said, the stylesheet(s) and script(s) which are in the variables, page_styles and page_scripts belong only to the dashboard page. -->
<!-- If we change this landing page to be something other than the dashboard page, we need to change the values of those variables to reflect the change.-->

<?php
$page_title = "News Portal";
$page_styles = ["dashboard.css"];
include "./views/header.php";
?>

<div class="main-container">
  <div id=news-container class="news-grid">
  <div class="sharethis-inline-share-buttons"
     data-url=""
     data-title=""
     data-description="">
</div>
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



