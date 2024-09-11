function changeElementsPerPage() {
    // Logic to handle the change in elements per page
    console.log('Change elements per page');
}

function changeSortOrder() {
    // Logic to change sort order
    console.log('Change sort order');
}

function toggleSortDirection() {
    // Toggle between ascending and descending sort
    const button = document.getElementById('sortDirection');
    if (button.textContent === '▲') {
        button.textContent = '▼';
    } else {
        button.textContent = '▲';
    }
}

function prevPage() {
    // Logic to go to the previous page
    console.log('Previous page');
}

function nextPage() {
    // Logic to go to the next page
    console.log('Next page');
}

function search() {
    // Logic to handle search functionality
    console.log('Search');
}
