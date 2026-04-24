<?php
function updateInternship($lecturer_id, $supervisor_id, $company, $start_date, $end_date, $report_status, $intern_id) {
    
    //getting current intern_id to search assessor id with
    $sql_get_old = "SELECT lecturer_id, supervisor_id FROM internship WHERE intern_id = ?";
    $old_data = executePreparedStatement($sql_get_old, [$intern_id])->fetch_assoc();
    //saving assessors id
    $old_lecturer = $old_data['lecturer_id'];
    $old_supervisor = $old_data['supervisor_id'];

    //updating the internship table data with new one
    $sql_main = "UPDATE internship SET lecturer_id = ?, supervisor_id = ?, internship_company = ?, start_date = ?, end_date = ?, report_status = ? WHERE intern_id = ?";
    $params_main = [$lecturer_id, $supervisor_id, $company, $start_date, $end_date, $report_status, $intern_id];
    $execute_main = executePreparedStatement($sql_main, $params_main);

    //updating lecturer id in report table
    $sql_report_lec = "UPDATE internship_report SET assessor_id = ? WHERE intern_id = ? AND assessor_id = ?";
    executePreparedStatement($sql_report_lec, [$lecturer_id, $intern_id, $old_lecturer]);

    //updating supervisor id in report table
    $sql_report_sup = "UPDATE internship_report SET assessor_id = ? WHERE intern_id = ? AND assessor_id = ?";
    executePreparedStatement($sql_report_sup, [$supervisor_id, $intern_id, $old_supervisor]);

    return $execute_main;
}

function deleteInternship($intern_id) {

    //delete internship report table data
    $sql_report = "DELETE FROM internship_report WHERE intern_id = ?";
    executePreparedStatement($sql_report, [$intern_id]);
    //delete internship table data
    $sql = "DELETE FROM internship WHERE intern_id = ?";
    $params = [$intern_id];
    return executePreparedStatement($sql, $params);
}

function addInternship($lecturer_id, $supervisor_id, $student_id, $company, $start_date, $end_date, $report_status) {
    global $conn;
    
    $sql_internship = "INSERT INTO internship (lecturer_id, supervisor_id, student_id, internship_company, start_date, end_date, report_status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params_internship = [$lecturer_id, $supervisor_id, $student_id, $company, $start_date, $end_date, $report_status];
    $execute_internship = executePreparedStatement($sql_internship, $params_internship);

    if (!$execute_internship) {
        return false;
    }
    //getting auto incremented id
    $new_intern_id = $conn->insert_id;
    //this update a row for lecturer in the internship_report table
    $sql_report = "INSERT INTO internship_report (intern_id, assessor_id) VALUES (?, ?)";

    $execute_lecturer_report = executePreparedStatement($sql_report, [$new_intern_id, $lecturer_id]);

    //this update a row for supervirsor in the internship_report table
    $execute_supervisor_report = executePreparedStatement($sql_report, [$new_intern_id, $supervisor_id]);

    return $execute_lecturer_report && $execute_supervisor_report;
}
?>