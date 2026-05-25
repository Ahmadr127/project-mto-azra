/**
 * AJAX Page Navigation & Alpine.js Turbo Compatibility Hook
 * Intercepts sidebar clicks to fetch and swap main content area dynamically.
 */

// 1. Alpine.js Initializer Hook (MUST run immediately in <head>)
window.alpineInitialized = false;
document.addEventListener('alpine:init', () => {
    window.alpineInitialized = true;
});

const originalAddEventListener = document.addEventListener;
document.addEventListener = function(type, listener, options) {
    if (type === 'alpine:init' && window.alpineInitialized) {
        try {
            listener();
        } catch (e) {
            console.error('Error executing alpine:init listener:', e);
        }
    } else {
        originalAddEventListener.call(document, type, listener, options);
    }
};

// 2. AJAX Sidebar Navigation (runs after DOM is ready)
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.getElementById('main-content-area');
    if (!sidebar || !mainContent) return;

    // Function to perform AJAX navigation
    function loadPage(url, pushState = true) {
        mainContent.style.opacity = '0.5';
        mainContent.style.transition = 'opacity 0.15s ease-in-out';

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Response error');
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newMainContent = doc.getElementById('main-content-area');
                if (newMainContent) {
                    // Update main content area
                    mainContent.innerHTML = newMainContent.innerHTML;
                    mainContent.style.opacity = '1';
                    
                    // Update page title
                    document.title = doc.title;

                    // Push history state
                    if (pushState) {
                        history.pushState({ url: url }, '', url);
                    }

                    // Update active states of links in the sidebar
                    const currentLinks = sidebar.querySelectorAll('a');
                    const newLinks = doc.querySelectorAll('.sidebar a');
                    currentLinks.forEach(curLink => {
                        const href = curLink.getAttribute('href');
                        const matchedNewLink = Array.from(newLinks).find(nl => nl.getAttribute('href') === href);
                        if (matchedNewLink) {
                            curLink.className = matchedNewLink.className;
                        }
                    });

                    // Re-initialize Alpine.js on the new content
                    if (window.Alpine) {
                        window.Alpine.initTree(mainContent);
                    }

                    // Extract and execute scripts from the fetched page's main content and footer
                    const newScripts = doc.querySelectorAll('#main-content-area script, body > script:not([src])');
                    newScripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        Array.from(oldScript.attributes).forEach(attr => {
                            newScript.setAttribute(attr.name, attr.value);
                        });
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        document.body.appendChild(newScript);
                        newScript.remove();
                    });
                } else {
                    window.location.href = url;
                }
            })
            .catch(error => {
                console.error('AJAX navigation failed, falling back to full load:', error);
                window.location.href = url;
            });
    }

    // Intercept link clicks inside the sidebar
    sidebar.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (!link) return;

        const url = link.getAttribute('href');
        if (!url || url === '#' || url.startsWith('javascript:')) return;
        
        // Only handle local URLs
        if (url.startsWith('http') && !url.startsWith(window.location.origin)) return;

        e.preventDefault();
        
        // Close mobile sidebar if open
        if (window.Alpine) {
            const AlpineEl = document.querySelector('[x-data="sidebarComponent()"]');
            if (AlpineEl) {
                const data = window.Alpine.$data(AlpineEl);
                if (data) {
                    data.mobileOpen = false;
                }
            }
        }

        loadPage(url);
    });

    // Handle popstate for back/forward navigation
    window.addEventListener('popstate', function(e) {
        loadPage(window.location.href, false);
    });
});
