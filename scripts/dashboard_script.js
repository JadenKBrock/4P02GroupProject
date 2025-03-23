let selectedRole = "all";  // Tracks the selected audience/role

const allArticles = [
  { id: 1, title: "AI in Banking", category: "Business", topic: "Tech Industry", tags: ["AI", "Banking"], content: "AI is transforming banking...", date: "2025-02-15" },
  { id: 2, title: "Stock Market Boom", category: "Business", topic: "Stock Market", tags: ["Investments", "AI"], content: "AI-driven stock market trends...", date: "2025-02-14" },
  { id: 3, title: "Marketing Trends 2025", category: "Business", topic: "Marketing", tags: ["Advertising"], content: "Personalized marketing is dominating...", date: "2025-02-13" },
  { id: 4, title: "YouTube Millionaires", category: "Influencer", topic: "Social Media", tags: ["YouTube"], content: "More influencers are making millions...", date: "2025-02-12" },
  { id: 5, title: "TikTok Fashion Trends", category: "Influencer", topic: "Lifestyle", tags: ["TikTok"], content: "TikTok fashion trends are setting the tone...", date: "2025-02-11" },
  { id: 6, title: "Fitness Influencers", category: "Influencer", topic: "Fitness", tags: ["Workout"], content: "Fitness influencers are revolutionizing workouts...", date: "2025-02-10" }
];

function applyFilters() {
  const newsContainer = document.getElementById("news-container");
  newsContainer.innerHTML = "";
  const searchInput = document.getElementById("search-input").value.trim().toLowerCase();
  const sortOrder = document.getElementById("sort-select").value;

  // Filter articles based on search text and selected role
  let filteredArticles = allArticles.filter(article => {
    const matchesSearch = searchInput === "" ||
      article.title.toLowerCase().includes(searchInput) ||
      article.content.toLowerCase().includes(searchInput);
    const matchesRole = (selectedRole === "all" ||
      article.category.toLowerCase() === selectedRole);
    return matchesSearch && matchesRole;
  });

  // Sort articles based on date
  filteredArticles.sort((a, b) => {
    const dateA = new Date(a.date);
    const dateB = new Date(b.date);
    return sortOrder === "desc" ? dateB - dateA : dateA - dateB;
  });

  // Render articles
  filteredArticles.forEach(article => {
    const tagsHTML = article.tags.map(tag => <span class="article-tag">${tag}</span>).join(", ");
    newsContainer.innerHTML += 
      <article class="news-item">
        <h3>${article.title}</h3>
        <p class="preview">${article.content.substring(0, 100)}...</p>
        <button class="expand-btn" onclick="toggleArticle(${article.id})">Read More</button>
        <div class="full-content hidden" id="article-${article.id}">
          <p>${article.content}</p>
          <p><strong>Tags:</strong> ${tagsHTML}</p>
          <p><strong>Date:</strong> ${article.date}</p>
        </div>
      </article>;
  });
}

function toggleArticle(id) {
  const articleContent = document.getElementById(article-$, {id});
  const btn = articleContent.previousElementSibling;
  articleContent.classList.toggle("hidden");
  btn.innerText = articleContent.classList.contains("hidden") ? "Read More" : "Show Less";
}

function clearFilters() {
  document.getElementById("search-input").value = "";
  selectedRole = "all";
  document.getElementById("role-selector-btn").innerHTML = 'Choose your audience/role <span class="arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 10l5 5 5-5H7z"/></svg></span>';
  document.getElementById("sort-select").value = "desc";
  applyFilters();
}

function generateContent() {
  const idea = document.getElementById("generate-input").value.trim();
  if (idea === "") return;
  const newsContainer = document.getElementById("news-container");
  newsContainer.innerHTML = 
    <article class="news-item">
      <h3>Generated: ${idea}</h3>
      <p class="preview">This is generated content based on your idea: ${idea} ...</p>
      <button class="expand-btn" onclick="alert('More details coming soon')">Read More</button>
    </article>;
}

function generateContentByKeyword(keyword) {
  const newsContainer = document.getElementById("news-container");
  newsContainer.innerHTML = 
    <article class="news-item">
      <h3>Generated: ${keyword}</h3>
      <p class="preview">This is a generated article for the hot topic: ${keyword}.</p>
      <button class="expand-btn" onclick="alert('More details coming soon')">Read More</button>
    </article>;
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("clear-btn").addEventListener("click", clearFilters);
  document.getElementById("search-btn").addEventListener("click", applyFilters);
  document.getElementById("sort-select").addEventListener("change", applyFilters);

  // Sidebar role selector dropdown
  document.getElementById("role-selector-btn").addEventListener("click", () => {
    const options = document.getElementById("role-options");
    options.classList.toggle("hidden");
  });

  document.getElementById("role-business-btn").addEventListener("click", () => {
    selectedRole = "business";
    document.getElementById("role-selector-btn").innerHTML = 'Business <span class="arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 10l5 5 5-5H7z"/></svg></span>';
    document.getElementById("role-options").classList.add("hidden");
    applyFilters();
  });

  document.getElementById("role-influencer-btn").addEventListener("click", () => {
    selectedRole = "influencer";
    document.getElementById("role-selector-btn").innerHTML = 'Influencer <span class="arrow-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16"><path fill="currentColor" d="M7 10l5 5 5-5H7z"/></svg></span>';
    document.getElementById("role-options").classList.add("hidden");
    applyFilters();
  });

  // Header user dropdown for Login/Sign Up
  document.getElementById("user-selector-btn").addEventListener("click", () => {
    const userOptions = document.getElementById("user-options");
    userOptions.classList.toggle("hidden");
  });

  // Content generator event
  document.getElementById("generate-btn").addEventListener("click", generateContent);

  // Hot Topics filter buttons in Generate Content section
  document.querySelectorAll(".gen-filter").forEach(button => {
    button.addEventListener("click", () => {
      const keyword = button.getAttribute("data-keyword");
      generateContentByKAeyword(keyword);
    });
  });

  applyFilters();
});

//add event listener for profile icon event handling
document.addEventListener("DOMContentLoaded", function () {
  const profileIcon = document.getElementById("profile-icon");
  const dropdownMenu = document.getElementById("profile-dropdown");

  if (profileIcon) {
    profileIcon.addEventListener("click", function () {
      dropdownMenu.classList.toggle("hidden");
    });

    //Close the profile dropdown when clicked outside of it
    document.addEventListener("click", function (event) {
      if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.add("hidden");
      }
    });
  }
});