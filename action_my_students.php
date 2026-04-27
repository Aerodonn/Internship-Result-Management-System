<?php
// Added $student_id as the first parameter so it matches what manage_student.php is sending
function updateStudent($intern_id, $internship_company, $start_date, $end_date, $report_status) {
    $sql = "UPDATE internship SET internship_company = ?, start_date = ?, end_date = ?, report_status = ? WHERE intern_id = ?";
    $params = [$internship_company, $start_date, $end_date, $report_status, $intern_id];
    return executePreparedStatement($sql, $params);
}

function deleteStudent($intern_id) {
    $sql = "DELETE FROM internship WHERE intern_id = ?";
    $params = [$intern_id];
    return executePreparedStatement($sql, $params);

}

function deleteStudentInternReport($intern_id) {
    $sql = "DELETE FROM internship_report WHERE intern_id = ?";
    $params = [$intern_id];
    return executePreparedStatement($sql, $params);
}
?>