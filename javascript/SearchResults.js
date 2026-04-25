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
    
    console.log("JS file loaded successfully!");
}

function openEditForm(report_id, intern_id, name, task, safety, theory, present, clarity, learning, proj, time, status, comment) {
    // These IDs MUST match the 'id' attributes in your results.php form
    console.log("JS file loaded successfully!");
    document.getElementById('report_id').value = report_id;
    document.getElementById('intern_id').value = intern_id;
    document.getElementById('task_score').value = task;
    document.getElementById('safety_score').value = safety;
    document.getElementById('theory_score').value = theory;
    document.getElementById('present_score').value = present;
    document.getElementById('clarity_score').value = clarity;
    document.getElementById('learning_score').value = learning;
    document.getElementById('proj_mgmt_score').value = proj;
    document.getElementById('time_mgmt_score').value = time;
    document.getElementById('report_status').value = status;
    document.getElementById('comment').value = comment;

    document.getElementById('editForm').classList.add('active');
}

function closeEditForm() {
    document.getElementById('editForm').classList.remove('active');
}

document.getElementById('editForm').addEventListener('click', function(e) {
    if (e.target === this) closeEditForm();
});
console.log("JS file loaded successfully!");
document.getElementById('searchStudent').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);