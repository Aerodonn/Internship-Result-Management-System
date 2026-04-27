function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;
    const searchType = document.getElementById('searchType').value; 
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let content = '';
        let isStrictSearch = true;

        if (searchType === 'student') {
            const studentID = cells[0]?.textContent.toLowerCase().trim() || "";
            content =`${studentID}`;
        } else if (searchType === 'number') {
            const studentNumber = cells[1]?.textContent.toLowerCase().trim() || "";
            content =`${studentNumber}`;
        }  else {
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


function openEditForm(intern_id, company, start_date, end_date, report_status) {
    document.getElementById('form_intern_id').value = intern_id;
    document.getElementById('form_company').value = company;
    document.getElementById('form_start_date').value = start_date;
    document.getElementById('form_end_date').value = end_date;
    document.getElementById('form_report_status').value = report_status;
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

document.getElementById('searchType').addEventListener('change', filterTable);
document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);