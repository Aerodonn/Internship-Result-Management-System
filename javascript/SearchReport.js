function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;
    const searchType = document.getElementById('searchType').value; 
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let content = '';
        let isStrictSearch = true;

        if (searchType === 'intern') {
            const internID = cells[0]?.textContent.toLowerCase().trim() || "";
            content =`${internID}`;
        } else if (searchType === 'lecturer') {
            const lecturerID = cells[2]?.textContent.toLowerCase().trim() || "";
            content =`${lecturerID}`;
        } else if (searchType === 'supervisor') {
            const supervisorID = cells[6]?.textContent.toLowerCase().trim() || "";
            content =`${supervisorID}`;
        } else {
            content = row.textContent.toLowerCase();
            isStrictSearch = false;
        }
        const rowStatus = row.dataset.status || '';  // pull the status directly from the row's data attribute

        let matchesSearch = false;
        if (search === "") {
            matchesSearch = true; //if search is empty, show everything
        } else if (isStrictSearch) {
            matchesSearch = (content === search); // strict match
        } else {
            matchesSearch = content.includes(search); // partial match
        }

        const matchesStatus = status === '' || rowStatus === status; //if the dropdown is empty, it matches everything

        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
    
    console.log("JS file loaded successfully!");
}

console.log("JS file loaded successfully!");
document.getElementById('searchType').addEventListener('change', filterTable);
document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

