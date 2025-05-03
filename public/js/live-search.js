/**
 * Live Search functionality for the Inventory Management System
 * This script adds real-time filtering as users type in search fields
 */

document.addEventListener('DOMContentLoaded', function () {
    // Get all live search inputs
    const liveSearchInputs = document.querySelectorAll('.live-search-input');

    // Add event listeners to each input
    liveSearchInputs.forEach(input => {
        // Set up debounce to prevent too many requests
        let debounceTimer;

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);

            // Show loading indicator
            const loadingIndicator = this.parentElement.querySelector('.loading-indicator');
            if (loadingIndicator) {
                loadingIndicator.classList.remove('d-none');
            }

            // Debounce the search (wait 500ms after user stops typing)
            debounceTimer = setTimeout(() => {
                // Get the current form
                const form = this.closest('form');

                // Get all form data
                const formData = new FormData(form);

                // Convert FormData to URL parameters
                const params = new URLSearchParams(formData);

                // Get the current URL without query parameters
                const baseUrl = window.location.pathname;

                // Create the new URL with search parameters
                const newUrl = `${baseUrl}?${params.toString()}`;

                // Make the AJAX request
                fetch(newUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => response.text())
                    .then(html => {
                        // Parse the HTML response
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Find the table or content area to update
                        const contentArea = document.querySelector('.content-area');
                        const newContentArea = doc.querySelector('.content-area');

                        if (contentArea && newContentArea) {
                            contentArea.innerHTML = newContentArea.innerHTML;

                            // Re-attach event listeners to any new elements
                            attachEventListeners();
                        }

                        // Update the URL without refreshing the page
                        window.history.pushState({}, '', newUrl);

                        // Hide loading indicator
                        if (loadingIndicator) {
                            loadingIndicator.classList.add('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Error during live search:', error);
                        // Hide loading indicator on error
                        if (loadingIndicator) {
                            loadingIndicator.classList.add('d-none');
                        }
                    });
            }, 500);
        });
    });

    // Also make other filter inputs trigger live search
    const filterSelects = document.querySelectorAll('form select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function () {
            // Find a live search input in the same form
            const liveSearchInput = this.closest('form').querySelector('.live-search-input');
            if (liveSearchInput) {
                // Trigger the input event on the live search input
                const event = new Event('input', { bubbles: true });
                liveSearchInput.dispatchEvent(event);
            } else {
                // Check if the select has a data-auto-submit attribute
                if (this.hasAttribute('data-auto-submit')) {
                    // Only submit the form if explicitly requested
                    this.closest('form').submit();
                }
                // Otherwise, do nothing and prevent automatic form submission
            }
        });
    });

    // Function to attach event listeners to dynamically added elements
    function attachEventListeners() {
        // Add any event listeners for elements inside the content area
        // For example, delete confirmations, etc.
    }
});
