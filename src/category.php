<?php
session_start();
include('aggregator.php');

// Initialize saved categories and saved articles in session if not already set.
if (!isset($_SESSION['saved_categories'])) {
    $_SESSION['saved_categories'] = [];
}
if (!isset($_SESSION['saved_articles'])) {
    $_SESSION['saved_articles'] = [];
}

// Get the selected tag from the query string.
$selectedTag = isset($_GET['tag']) ? $_GET['tag'] : '';
$articlesByTag = [];

// If a tag is provided, retrieve the related articles.
if (!empty($selectedTag)) {
    $articlesByTag = getArticlesByTag($selectedTag);
}

// Process saving an article if the 'save_article' parameter is set.
if (isset($_GET['save_article']) && !empty($_GET['save_article'])) {
    $saveArticleId = $_GET['save_article'];
    $articleToSave = null;
    // Look for the article in the current list.
    foreach ($articlesByTag as $article) {
        if ($article['id'] == $saveArticleId) {
            $articleToSave = $article;
            break;
        }
    }
    if ($articleToSave) {
        // Check if the article is already saved.
        $alreadySaved = false;
        foreach ($_SESSION['saved_articles'] as $savedArticle) {
            if ($savedArticle['id'] == $articleToSave['id']) {
                $alreadySaved = true;
                break;
            }
        }
        if (!$alreadySaved) {
            $_SESSION['saved_articles'][] = $articleToSave;
        }
    }
    // Redirect back to the same tag view to prevent duplicate submissions.
    header("Location: category.php?tag=" . urlencode($selectedTag));
    exit();
}

// Retrieve all tags from the aggregator to populate the dropdown.
$allTags = getAccurateTags(getNewsArticles());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Sources by Category</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Common dashboard styles -->
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="category.css">
</head>
<body>
    <!-- Header Section with Navigation -->
    <header class="dashboard-header">
        <h1>My Dashboard</h1>
        <nav class="header-nav">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="sources.php">Sources</a></li>
                <li><a href="category.php" class="active">Categories</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Area -->
    <div class="main-container">
        <div class="news-grid">
            <div class="category-container">
                <h1>Browse Sources by Category</h1>
                <!-- Form for selecting a category -->
                <form method="get" action="category.php" class="select-container">
                    <label for="categorySelect">Select a Category:</label>
                    <select id="categorySelect" name="tag">
                        <option value="">--Choose a Category--</option>
                        <?php foreach ($allTags as $tag): 
                            $selected = ($tag == $selectedTag) ? 'selected' : '';
                        ?>
                            <option value="<?php echo htmlspecialchars($tag); ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars(ucfirst($tag)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Two buttons: one to just search and one to save & search -->
                    <button type="submit" name="action" value="search">Search Category</button>
                    <button type="submit" name="action" value="save">Save &amp; Search Category</button>
                </form>

                <!-- Display articles for the selected tag -->
                <?php if (!empty($selectedTag)): ?>
                    <div class="articles-section">
                        <h2>Articles for "<?php echo htmlspecialchars(ucfirst($selectedTag)); ?>"</h2>
                        <?php if (!empty($articlesByTag)): ?>
                            <ul class="articles-list">
                                <?php foreach ($articlesByTag as $article): ?>
                                    <li>
                                        <a href="<?php echo htmlspecialchars($article['url']); ?>" target="_blank">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </a>
                                        <!-- Link to save the article -->
                                        <a href="category.php?tag=<?php echo urlencode($selectedTag); ?>&save_article=<?php echo urlencode($article['id']); ?>" class="save-article-btn">Save Article</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No articles found for this category.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Display saved categories -->
                <div class="saved-categories">
                    <h2>Saved Categories</h2>
                    <ul id="savedCategoriesList">
                        <?php 
                        foreach ($_SESSION['saved_categories'] as $savedCategory) {
                            echo "<li><a href=\"category.php?tag=" . urlencode($savedCategory) . "\">" . htmlspecialchars(ucfirst($savedCategory)) . "</a></li>";
                        }
                        ?>
                    </ul>
                </div>

                <!-- Display saved articles -->
                <div class="saved-articles">
                    <h2>Saved Articles</h2>
                    <ul id="savedArticlesList">
                        <?php 
                        if (!empty($_SESSION['saved_articles'])) {
                            foreach ($_SESSION['saved_articles'] as $savedArticle) {
                                echo "<li><a href=\"" . htmlspecialchars($savedArticle['url']) . "\" target=\"_blank\">" . htmlspecialchars($savedArticle['title']) . "</a></li>";
                            }
                        } else {
                            echo "<li>No articles saved.</li>";
                        }
                        ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
