function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const content = row.textContent.toLowerCase();
        //index 5 means status column
        const rowStatus = row.querySelectorAll('td')[5].textContent.trim();
        //return true if the searchbar item contains stuff from "content" variable (also works if nothing is enters, it become an empty string so it still return True)
        const matchesSearch = content.includes(search);
        //matchStatus returns true if the current status is empty or the rowStatus are equal to the status search state
        const matchesStatus = status === '' || rowStatus === status;
        //display row if both matches else hide it
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}

document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);