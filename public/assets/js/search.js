// Replace your current search.js with this version
export async function handleSearch(event) {
    event.preventDefault();
    const query = document.querySelector('input[name="q"]').value.trim();
    const resultsDiv = document.getElementById('searchResults');
    
    if (!query) {
        resultsDiv.innerHTML = '<div class="search-result p-2">Please enter a search term</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    resultsDiv.innerHTML = '<div class="search-result p-2">Searching...</div>';
    resultsDiv.style.display = 'block';
    
    try {
        const response = await fetch(`blog.php?search=1&q=${encodeURIComponent(query)}`);
        const posts = await response.json();
        
        if (posts.error) {
            resultsDiv.innerHTML = `<div class="search-result p-2 text-danger">${posts.error}</div>`;
            return;
        }
        
        if (posts.length === 0) {
            resultsDiv.innerHTML = '<div class="search-result p-2">No results found</div>';
            return;
        }
        
        let html = '';
        posts.forEach(post => {
            html += `
                <div class="search-result">
                    <a href="public/read.php?id=${post.id}" class="d-block p-2 text-decoration-none text-dark">
                        <strong>${escapeHtml(post.title)}</strong>
                        <div class="text-muted small">${escapeHtml(post.fname)} • ${formatDate(post.created_at)}</div>
                    </a>
                </div>
            `;
        });
        
        resultsDiv.innerHTML = html;
    } catch (error) {
        console.error('Search failed:', error);
        resultsDiv.innerHTML = '<div class="search-result p-2 text-danger">Search failed</div>';
    }
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Event listeners for showing/hiding results
document.addEventListener('click', function(e) {
    if (!e.target.closest('#searchForm')) {
        document.getElementById('searchResults').style.display = 'none';
    }
});

document.querySelector('input[name="q"]').addEventListener('input', function() {
    const resultsDiv = document.getElementById('searchResults');
    if (this.value.trim()) {
        resultsDiv.style.display = 'block';
    } else {
        resultsDiv.style.display = 'none';
    }
});