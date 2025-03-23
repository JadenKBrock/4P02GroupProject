<?php
session_start();
$page_title = "News Portal";
$page_styles = ["dashboard.css"];
include "./views/header.php";
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News Portal</title>
  <link rel="stylesheet" href="dashboard.styles.css">
</head>
<body>
  <main>
    <section id="news-container" class="news-grid"></section>

    <aside class="sidebar">
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

      <!-- New Content Generator Section -->
      <div class="content-generator">
        <h2>Generate Content</h2>
        <input type="text" id="generate-input" placeholder="Enter news ideas...">
        <button id="generate-btn">Generate</button>
        <div class="generator-filters">
          <p>Hot Topics:</p>
          <button class="gen-filter" data-keyword="politics">Politics</button>
          <button class="gen-filter" data-keyword="technology">Technology</button>
          <button class="gen-filter" data-keyword="sports">Sports</button>
          <button class="gen-filter" data-keyword="entertainment">Entertainment</button>
        </div>
      </div>

    </aside>

    <automated_scheduler class="sidebar">
    <h2>Newsletter Scheduler</h2>
      

    </automated_scheduler>
  </main>

<?php
$page_scripts = ["dashboard_script.js"];
include "./views/footer.php";
?>