let selectedCategory = "all";

const allArticles = [
    { id: 1, title: "AI in Banking", category: "Business", topic: "Tech Industry", tags: ["AI", "Banking"], content: "AI is transforming banking..." },
    { id: 2, title: "Stock Market Boom", category: "Business", topic: "Stock Market", tags: ["Investments", "AI"], content: "AI-driven stock market trends..." },
    { id: 3, title: "Marketing Trends 2025", category: "Business", topic: "Marketing", tags: ["Advertising"], content: "Personalized marketing is dominating..." },

    { id: 4, title: "YouTube Millionaires", category: "Influencer", topic: "Social Media", tags: ["YouTube"], content: "More influencers are making millions..." },
    { id: 5, title: "TikTok Fashion Trends", category: "Influencer", topic: "Lifestyle", tags: ["TikTok"], content: "TikTok fashion trends are setting the tone..." },
    { id: 6, title: "Fitness Influencers", category: "Influencer", topic: "Fitness", tags: ["Workout"], content: "Fitness influencers are revolutionizing workouts..." }
];

// Display Articles
function applyFilters() {
    const newsContainer = document.getElementById("news-container");
    newsContainer.innerHTML = "";

    allArticles.forEach(article => {
        if (selectedCategory === "all" || article.category.toLowerCase() === selectedCategory) {
            const tagsHTML = article.tags.map(tag => `<span class="article-tag">${tag}</span>`).join(", ");
            newsContainer.innerHTML += `
                <article class="news-item">
                    <h3>${article.title}</h3>
                    <p class="preview">${article.content.substring(0, 100)}...</p>
                    <button class="expand-btn" onclick="toggleArticle(${article.id})">Read More</button>
                    <div class="full-content hidden" id="article-${article.id}">
                        <p>${article.content}</p>
                        <p><strong>Tags:</strong> ${tagsHTML}</p>
                    </div>
                </article>`;
        }
    });
}

// Toggle Read More
function toggleArticle(id) {
    const articleContent = document.getElementById(`article-${id}`);
    const btn = articleContent.previousElementSibling;
    articleContent.classList.toggle("hidden");
    btn.innerText = articleContent.classList.contains("hidden") ? "Read More" : "Show Less";
}

// Function to Toggle Categories
function toggleCategory(category) {
    selectedCategory = category;
    applyFilters();
}

// Function to Clear Filters
function clearFilters() {
    selectedCategory = "all";
    applyFilters();
}

// Attach Event Listeners
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("business-btn").addEventListener("click", () => toggleCategory("business"));
    document.getElementById("influencer-btn").addEventListener("click", () => toggleCategory("influencer"));
    document.getElementById("clear-btn").addEventListener("click", clearFilters);

    applyFilters();
});
