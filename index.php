<?php
$page_title = "News Portal";
$page_styles = ["dashboard.css"];
include "./views/header.php";
?>

<header>
        <h1>News Portal</h1>
        <div class="header-right">
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search articles...">
                <button id="search-btn">Search</button>
            </div>
            <button id="login-btn">Login</button>
            <button id="signup-btn">Sign Up</button>
        </div>
    </header>

    <main>
        <section id="news-container" class="news-grid"></section>

        <aside class="sidebar">
            <h2>Filter News</h2>
            <button id="business-btn" class="category-btn">Business</button>
            <button id="influencer-btn" class="category-btn">Influencer</button>

            <div id="business-filters" class="filter-section">
                <h3>Business Topics</h3>
                <button class="tag-btn" data-type="businessTopics">Tech Industry</button>
                <button class="tag-btn" data-type="businessTopics">Stock Market</button>
                <button class="tag-btn" data-type="businessTopics">Marketing</button>
            </div>

            <div id="influencer-filters" class="filter-section hidden">
                <h3>Influencer Topics</h3>
                <button class="tag-btn" data-type="influencerTopics">Social Media</button>
                <button class="tag-btn" data-type="influencerTopics">Lifestyle</button>
                <button class="tag-btn" data-type="influencerTopics">Fitness</button>
            </div>

            <button id="clear-btn">Clear Filters</button>
        </aside>
    </main>

<?php
$page_scripts = ["dashboard_script.js"];
include "./views/footer.php";
?>


