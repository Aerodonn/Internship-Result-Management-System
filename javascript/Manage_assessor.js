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

function openEditForm(user_id, full_name, phone_number, email_address, organisation, assessor_type) {
    document.getElementById('form_user_id').value       = user_id;
    document.getElementById('form_full_name').value     = full_name;
    document.getElementById('form_phone_number').value  = phone_number;
    document.getElementById('form_email_address').value = email_address;
    document.getElementById('form_organisation').value  = organisation;
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

    form.style.animation    = 'fadeInOverlay 0.25s ease reverse forwards';
    formBox.style.animation = 'slideUpForm 0.25s ease reverse forwards';

    setTimeout(() => {
        form.classList.remove('active');
        form.style.animation    = '';
        formBox.style.animation = '';
    }, 250);
}

document.getElementById('addForm').addEventListener('click', function(e) {
    if (e.target === this) closeAddForm();
});


document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);