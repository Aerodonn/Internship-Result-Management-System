function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const content = row.textContent.toLowerCase();
    
        const rowStatus = row.querySelectorAll('td')[6].textContent.trim();
        
        const matchesSearch = content.includes(search);
        const matchesStatus = status === '' || rowStatus === status;
        
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
}


function openEditForm(lecturer_id, supervisor_id, company, start_date, end_date, report_status, intern_id) {
    document.getElementById('form_lecturer_id').value  = lecturer_id;
    document.getElementById('form_supervisor_id').value = supervisor_id;
    document.getElementById('form_company').value = company;
    document.getElementById('form_start_date').value = start_date;
    document.getElementById('form_end_date').value = end_date;
    document.getElementById('form_report_status').value = report_status;
    document.getElementById('form_intern_id').value = intern_id;
    document.getElementById('editForm').classList.add('active');
}

function closeEditForm() {
    document.getElementById('editForm').classList.remove('active');
}

document.getElementById('editForm').addEventListener('click', function(e) {
    if (e.target === this) closeEditForm();
});

function openAddForm() {
    document.getElementById('addForm').classList.add('active');
}

function closeAddForm() {
    const form = document.getElementById('addForm');
    const formBox = form.querySelector('.form');

    form.style.animation = 'fadeInOverlay 0.25s ease reverse forwards';
    formBox.style.animation = 'slideUpForm 0.25s ease reverse forwards';

    setTimeout(() => {
        form.classList.remove('active');
        form.style.animation = '';
        formBox.style.animation = '';
    }, 250);
}

document.getElementById('addForm').addEventListener('click', function(e) {
    if (e.target === this) closeAddForm();
});

document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

