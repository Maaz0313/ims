/**
 * Form Controls for the Inventory Management System
 * This script adds functionality for form controls that need manual submission
 */

document.addEventListener('DOMContentLoaded', function () {
    // Get all forms with select elements that don't have live-search
    const formsWithSelects = document.querySelectorAll('form:not(:has(.live-search-input)) select');
    
    // Add submit buttons next to select elements if they don't already exist
    formsWithSelects.forEach(select => {
        // Check if there's already a submit button in the form
        const form = select.closest('form');
        const existingSubmitBtn = form.querySelector('button[type="submit"]');
        
        // If no submit button exists and one hasn't been added by this script yet
        if (!existingSubmitBtn && !form.classList.contains('submit-btn-added')) {
            // Create a container for the button if it doesn't exist
            let btnContainer = form.querySelector('.select-submit-container');
            if (!btnContainer) {
                btnContainer = document.createElement('div');
                btnContainer.className = 'select-submit-container mt-2';
                select.parentNode.appendChild(btnContainer);
            }
            
            // Create and add the submit button
            const submitBtn = document.createElement('button');
            submitBtn.type = 'submit';
            submitBtn.className = 'btn btn-primary btn-sm';
            submitBtn.innerHTML = '<i class="fas fa-filter"></i> Apply Filter';
            btnContainer.appendChild(submitBtn);
            
            // Mark the form as having a submit button added
            form.classList.add('submit-btn-added');
        }
    });
    
    // Add data-auto-submit attribute to selects that should still auto-submit
    // For example, selects in forms that handle pagination or sorting
    const autoSubmitSelects = document.querySelectorAll('select[name="per_page"], select[name="sort_by"]');
    autoSubmitSelects.forEach(select => {
        select.setAttribute('data-auto-submit', 'true');
    });
});
