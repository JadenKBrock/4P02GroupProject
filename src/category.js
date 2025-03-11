document.addEventListener('DOMContentLoaded', () => {
    const categorySelect = document.getElementById('categorySelect');
    const saveCategoryBtn = document.getElementById('saveCategoryBtn');
    const savedCategoriesList = document.getElementById('savedCategoriesList');
  
    // Load saved categories from localStorage as a fallback.
    const loadSavedCategories = () => {
      const saved = localStorage.getItem('savedCategories');
      let categories = saved ? JSON.parse(saved) : [];
      savedCategoriesList.innerHTML = '';
      categories.forEach(cat => {
        const li = document.createElement('li');
        li.textContent = cat;
        savedCategoriesList.appendChild(li);
      });
    };
  
    // Save the selected category in localStorage.
    const saveCategory = (category) => {
      if (!category) return;
      const saved = localStorage.getItem('savedCategories');
      let categories = saved ? JSON.parse(saved) : [];
      if (!categories.includes(category)) {
        categories.push(category);
        localStorage.setItem('savedCategories', JSON.stringify(categories));
      }
      loadSavedCategories();
    };
  
    saveCategoryBtn.addEventListener('click', () => {
      const selectedCategory = categorySelect.value;
      saveCategory(selectedCategory);
    });
  
    loadSavedCategories();
  });
  