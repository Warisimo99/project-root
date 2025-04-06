import { handleSearch } from './search.js';

document.addEventListener("DOMContentLoaded", function () {
    // Sidebar toggler
    const toggler = document.querySelector(".btn");
    const sidebar = document.querySelector("#sidebar");

    // Debugging: Log elements to ensure they are correctly selected
    console.log("Toggler:", toggler);
    console.log("Sidebar:", sidebar);

    if (toggler && sidebar) {
        toggler.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
        });
    } else {
        console.error("Toggler button or sidebar element not found!");
    }

    // Search form handler
    const searchForm = document.getElementById('searchForm');

    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    } else {
        console.error("Search form not found!");
    }
});