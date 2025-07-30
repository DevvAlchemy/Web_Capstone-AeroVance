
 // Catalog JavaScript
   
        // View Toggle Functionality (React-ready pattern)
        function toggleView(view) {
            const gridView = document.getElementById('grid-view');
            const listView = document.getElementById('list-view');
            const gridBtn = document.getElementById('grid-btn');
            const listBtn = document.getElementById('list-btn');

            if (view === 'grid') {
                gridView.style.display = 'grid';
                listView.style.display = 'none';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
                localStorage.setItem('viewPreference', 'grid');
            } else {
                gridView.style.display = 'none';
                listView.style.display = 'block';
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
                localStorage.setItem('viewPreference', 'list');
            }
        }

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('viewPreference') || 'grid';
            toggleView(savedView);
            
            // Initialize search functionality
            initializeSearch();
            
            // Initialize sorting
            initializeSorting();
            
            // Initialize filters
            initializeFilters();
            
            console.log('✅ Catalog page loaded with', document.querySelectorAll('.helicopter-card').length, 'helicopters');
        });

        // Search functionality (React-ready pattern)
        function initializeSearch() {
            const searchInput = document.getElementById('search');
            if (searchInput) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        // Auto-submit form after 1 second of no typing
                        if (this.value.length >= 3 || this.value.length === 0) {
                            document.querySelector('.search-filters').submit();
                        }
                    }, 1000);
                });
            }
        }

        // Sorting functionality (React-ready pattern)
        function initializeSorting() {
            const sortSelect = document.getElementById('sort-select');
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const currentUrl = new URL(window.location);
                    currentUrl.searchParams.set('sort', this.value);
                    window.location.href = currentUrl.toString();
                });
                
                // Set current sort value from URL
                const urlParams = new URLSearchParams(window.location.search);
                const currentSort = urlParams.get('sort');
                if (currentSort) {
                    sortSelect.value = currentSort;
                }
            }
        }

        // Filter functionality (React-ready pattern)
        function initializeFilters() {
            // Price range validation
            const minPrice = document.querySelector('input[name="min_price"]');
            const maxPrice = document.querySelector('input[name="max_price"]');

// sourcery skip: avoid-function-declarations-in-blocks
            function validatePriceRange() {
                const min = parseInt(minPrice.value) || 0;
                const max = parseInt(maxPrice.value) || Infinity;
                
                if (min > max && maxPrice.value) {
                    maxPrice.value = minPrice.value;
                }
            }

            if (minPrice && maxPrice) {
                minPrice.addEventListener('change', validatePriceRange);
                maxPrice.addEventListener('change', validatePriceRange);
            }

            // Category filter quick buttons
            document.querySelectorAll('[data-category]').forEach(button => {
                button.addEventListener('click', function() {
                    const {category} = this.dataset;
                    document.getElementById('category').value = category;
                    document.querySelector('.search-filters').submit();
                });
            });
        }

        // Contact seller functionality (React-ready pattern)
        function contactSeller(helicopterId) {
            // Check if user is logged in (you'll implement this with your auth system)
            const isLoggedIn = false; // Replace with actual auth check
            
            if (isLoggedIn) {
                            window.location.href = `/contact?helicopter_id=${helicopterId}`;
                        }
            else if (confirm('Please login to contact the seller. Redirect to login page?')) {
                                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                            }
        }

        // Advanced search functionality (React-ready pattern)
        function performAdvancedSearch(filters) {
            // This function structure is ready for React conversion
            const searchParams = new URLSearchParams();
            
            Object.entries(filters).forEach(([key, value]) => {
                if (value && value !== '') {
                    searchParams.append(key, value);
                }
            });
            
            window.location.href = `/helicopters?${searchParams.toString()}`;
        }

        // Wishlist functionality (Rrp)
        function toggleWishlist(helicopterId) {
            // This will be implemented when you add user authentication
            console.log('Toggle wishlist for helicopter:', helicopterId);
            
            // Example of what this would look like:
            /*
            fetch('/api/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ helicopter_id: helicopterId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to show added/removed from wishlist
                    showNotification(data.added ? 'Added to wishlist' : 'Removed from wishlist');
                }
            });
            */
        }

        // Notification system (rrp)
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = `
                <div class="notification-content">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()">×</button>
                </div>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 3000);
        }

        // Header scroll effect (shared component)
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Smooth scrolling for anchor links (shared component)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Analytics tracking (Rrp)
        function trackHelicopterView(helicopterId) {
            // This structure is ready for React conversion
            /*
            fetch('/api/analytics/helicopter-view', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    helicopter_id: helicopterId,
                    page: 'catalog',
                    timestamp: new Date().toISOString()
                })
            });
            */
        }

        // Search suggestions (Rrp)
        function initializeSearchSuggestions() {
            const searchInput = document.getElementById('search');
            const suggestionsContainer = document.createElement('div');
            suggestionsContainer.className = 'search-suggestions';
            searchInput.parentElement.appendChild(suggestionsContainer);
            
            let suggestionsTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(suggestionsTimeout);
                const query = this.value.trim();
                
                if (query.length >= 2) {
                    suggestionsTimeout = setTimeout(() => {
                        fetchSearchSuggestions(query, suggestionsContainer);
                    }, 300);
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });
            
            // Hide suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                    suggestionsContainer.style.display = 'none';
                }
            });
        }

        function fetchSearchSuggestions(query, container) {
            // This would connect to your search API
            // For now, we'll use mock data that's ready for React conversion
            const mockSuggestions = [
                'Robinson R44',
                'Bell 407',
                'Airbus H145',
                'Emergency Services',
                'Business Aviation'
            ].filter(item => item.toLowerCase().includes(query.toLowerCase()));
            
            if (mockSuggestions.length > 0) {
                container.innerHTML = mockSuggestions.map(suggestion => 
                    `<div class="suggestion-item" onclick="selectSuggestion('${suggestion}')">${suggestion}</div>`
                ).join('');
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }

        function selectSuggestion(suggestion) {
            document.getElementById('search').value = suggestion;
            document.querySelector('.search-filters').submit();
        }

        // Initialize advanced features
        document.addEventListener('DOMContentLoaded', function() {
            // Uncomment when ready for advanced features
            // initializeSearchSuggestions();
        });
