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

function openEditModal(intern_id, company, start_date, end_date, report_status) {
        document.getElementById('modal_intern_id').value       = intern_id;
        document.getElementById('modal_company').value         = company;
        document.getElementById('modal_start_date').value      = start_date;
        document.getElementById('modal_end_date').value        = end_date;
        document.getElementById('modal_report_status').value   = report_status;
        document.getElementById('editModal').classList.add('active');
    }

function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
}

document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
});

function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeAddModal() {
    const modal    = document.getElementById('addModal');
    const modalBox = modal.querySelector('.modal');

    modal.style.animation    = 'fadeInOverlay 0.25s ease reverse forwards';
    modalBox.style.animation = 'slideUpModal 0.25s ease reverse forwards';

    setTimeout(() => {
        modal.classList.remove('active');
        modal.style.animation    = '';
        modalBox.style.animation = '';
    }, 250);
}

document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});


document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);