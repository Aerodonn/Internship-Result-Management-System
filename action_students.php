<?php
// Added $student_id as the first parameter so it matches what manage_student.php is sending
function updateStudent($student_id, $student_reg_number, $student_name, $email_address, $programme, $enrollment_date, $account_status) {
    $sql = "UPDATE student SET student_reg_number = ?, student_name = ?, email_address = ?, programme = ?, enrollment_date = ?, account_status = ? WHERE student_id = ?";
    $params = [$student_reg_number, $student_name, $email_address, $programme, $enrollment_date, $account_status, $student_id];
    return executePreparedStatement($sql, $params);
}

function deleteStudent($student_id) {
    $sql = "DELETE FROM student WHERE student_id = ?";
    $params = [$student_id];
    return executePreparedStatement($sql, $params);
}

// Removed $student_id from the parameters here, so it accepts the 6 arguments being sent
function addStudent($student_reg_number, $student_name, $email_address, $programme, $enrollment_date, $account_status) {
    global $conn;
    $sql_student = "INSERT INTO student (student_reg_number, student_name, email_address, programme, enrollment_date, account_status) VALUES (?, ?, ?, ?, ?, ?)";
    $params_student = [$student_reg_number, $student_name, $email_address, $programme, $enrollment_date, $account_status];
    $execute_student = executePreparedStatement($sql_student, $params_student);

    if (!$execute_student) {
        return false;
    }

    return $execute_student;
}
?>