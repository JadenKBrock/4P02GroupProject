<?php
$page_title = "Dashboard";
$page_styles = ["dashboard.css"];
include "./views/header.php";
?>

<h1>Welcome to Article Helper</h1>
<h3> Making Writing Easier.</h3>

<div class="searchbar" id="searchBar">
    <input type="text" class="searchbar__input" name="q" placeholder="Search Contents">
    <select id="filterDropdown" class="searchbar__filter">
        <option value="title">Sort by Title</option>
        <option value="date">Sort by Date Added</option>
        <option value="tags">Sort by Tags</option>
    </select>
    <button type="submit" class="searchbar_button">
        <i class="material-icons">search</i>
    </button>
</div>


<button id="generateBtn">Generate More</button>
<div class="card-container" id="cardContainer">
    <article class="card">
        <div class="card-content">
            <h3>Card 1: Business</h3>
            <p>words words words words And more words :) </p>
            <a href="#" class="btn">Read more</a>
            <p>Published on: 2025-02-06</p>
            <div class="tags-container"></div>
        </div>
    </article>

    <article class="card">
        <div class="card-content">
            <h3>Card 2: Travel</h3>
            <p>words words words words And more words :) </p>
            <a href="#" class="btn">Read more</a>
            <p>Published on: 2025-02-06</p>
            <div class="tags-container"></div>
        </div>
    </article>

    <article class="card">
        <div class="card-content">
            <h3>Card 3: Lifestyle</h3>
            <p>words words words words And more words :) </p>
            <a href="#" class="btn">Read more</a>
            <p>Published on: 2025-02-06</p>
            <div class="tags-container"></div>
        </div>
    </article>
</div>

<?php
$page_scripts = ["dashboard_script.js"];
include "./views/footer.php";
?>


