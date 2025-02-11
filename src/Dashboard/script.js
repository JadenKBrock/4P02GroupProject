document.addEventListener("DOMContentLoaded", function () {
    const cardContainer = document.getElementById("cardContainer");
    const generateBtn = document.getElementById("generateBtn");
    const searchInput = document.getElementById("searchInput");
    const sortOptions = document.getElementById("sortOptions");

    searchInput.addEventListener("input", filterContent);
    filterDropdown.addEventListener("change", filterContent);

    let cardCount = 3; // Tracks the number of cards

    function filterContent(){
        const query = searchInput.value.toLowerCase();
        const filterBy = filterDropdown.value;
        const contentItems = document.querySelectorAll(".content-card");

        contentItems.forEach((item) =>{
            const title = item.getAttribute("data-title").toLowerCase();
            const date = item.getAttribute("data-date");
            const tags = item.getAttribute("data-tags").toLowerCase();

            let isVisible = false;

            if (filterBy === "title"){
                isVisible = title.includes(query);
            } else if(filterBy === "date"){
                isVisible = date.includes(query);
            }else if(filterBy === "tags"){
                isVisible = tags.includes(query);
            }

            item.style.display = isVisible ? "block" : "none";
        });

    }

    // Add more cards dynamically
    generateBtn.addEventListener("click", function () {
        cardCount++;
        let newCategory = "New Category " + cardCount;

        const newCard = document.createElement("article");
        newCard.classList.add("card");
        newCard.setAttribute("data-title", `Generated ${cardCount}`);
        newCard.setAttribute("data-category", newCategory);
        newCard.innerHTML = `
            <div class="card-content">
                <h3>Card ${cardCount}: ${newCategory}</h3>
                <p>Generated content for card ${cardCount}.</p>
                <a href="#" class="btn read-more" data-title="Generated ${cardCount}" data-content="words words words and more words for ${cardCount}.">Read more</a>
                <p> Published on: 2025-02-06</p>
                <div class="tags-container"></div>
            </div>
        `;

        // Append new card and update event listeners
        cardContainer.appendChild(newCard);
        newCard.querySelector(".read-more").addEventListener("click", openModal);
    });

    contentCards.forEach((card) => {
            const tagsContainer = card.querySelector(".tags-container");
            const tags = card.getAttribute("data-tags").split(",").map(tag => tag.trim());

            tags.forEach((tag) => {
                const tagElement = document.createElement("span");
                tagElement.classList.add("tag-bubble");
                tagElement.textContent = tag;
                tagsContainer.appendChild(tagElement);
            });
        });


});
