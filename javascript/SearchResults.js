function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const content = row.textContent.toLowerCase();
        const rowStatus = row.dataset.status || '';  // pull the status directly from the row's data attribute
        const matchesSearch = content.includes(search);
        const matchesStatus = status === '' || rowStatus === status; //if the dropdown is empty, it matches everything

        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);