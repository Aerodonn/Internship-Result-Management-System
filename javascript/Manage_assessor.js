function filterTable() {
    const search = document.getElementById('searchStudent').value.toLowerCase().trim();
    const status = document.getElementById('statusFilter').value;
    const searchType = document.getElementById('searchType').value; 
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let content = '';
        let isStrictSearch = true;

        if (searchType === 'assessor') {
            const studentID = cells[0]?.textContent.toLowerCase().trim() || "";
            content =`${studentID}`;
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

function openEditForm(user_id, full_name, phone_number, email_address, organisation, assessor_type) {
    document.getElementById('form_user_id').value = user_id;
    document.getElementById('form_full_name').value = full_name;
    document.getElementById('form_phone_number').value = phone_number;
    document.getElementById('form_email_address').value = email_address;
    document.getElementById('form_organisation').value = organisation;
    document.getElementById('form_assessor_type').value = assessor_type;
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
    const form    = document.getElementById('addForm');
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