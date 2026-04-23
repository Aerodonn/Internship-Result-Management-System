function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const content = row.textContent.toLowerCase();
        // status is now in index 5 (6th column) after adding company back
        const rowStatus = cells[5]?.querySelector('.status-badge')?.textContent.trim() ?? '';

        const matchesSearch = content.includes(search);
        const matchesStatus = status === '' || rowStatus === status;

        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}



document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);