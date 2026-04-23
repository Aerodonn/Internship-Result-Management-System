<?php
function updateStudent($intern_id, $company, $start_date, $end_date, $report_status) {
    $sql = "UPDATE internship SET internship_company = ?, start_date = ?, end_date = ?, report_status = ? WHERE intern_id = ?";
    $params = [$company, $start_date, $end_date, $report_status, $intern_id];
    return executePreparedStatement($sql, $params);
}

function deleteStudent($intern_id) {
    $sql = "DELETE FROM internship WHERE intern_id = ?";
    $params = [$intern_id];
    return executePreparedStatement($sql, $params);
}

function addStudent($student_regnum, $student_name, $student_email, $student_programme, $student_enrollment, $student_status, $company, $start_date, $end_date, $lecturer_id, $supervisor_id, $report_status) {
    global $conn;
    $sql_student = "INSERT INTO student (student_reg_number, student_name, email_address, programme, enrollment_date, account_status) VALUES (?, ?, ?, ?, ?, ?)";
    $params_student = [$student_regnum, $student_name, $student_email, $student_programme, $student_enrollment, $student_status];
    $execute_student = executePreparedStatement($sql_student, $params_student);

    if (!$execute_student) {
        return false;
    }

    // get the new autoincremented student id
    $new_student_id = $conn->insert_id;

    //insert using student_id we just grab, into this table
    $sql_internship = "INSERT INTO internship (student_id, internship_company, start_date, end_date, lecturer_id, supervisor_id, report_status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params_internship = [$new_student_id, $company, $start_date, $end_date, $lecturer_id, $supervisor_id, $report_status];
    $execute_internship = executePreparedStatement($sql_internship, $params_internship);

    return $execute_internship;
}
?>