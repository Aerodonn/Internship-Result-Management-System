<?php
function updateReport($task_score, $safety_score, $theory_score, $present_score, $clarity_score, $learning_score, $proj_mgmt_score, $time_mgmt_score, $comment, $report_id) {
    $sql = "
    UPDATE internship_report 
    SET 
        task_score = ?, safety_score = ?,  theory_score = ?, 
        present_score = ?, clarity_score = ?, learning_score = ?,
        proj_mgmt_score = ?, time_mgmt_score = ?, comment = ?
    WHERE report_id = ?";
    $params = [$task_score, $safety_score, $theory_score, $present_score, $clarity_score, $learning_score, $proj_mgmt_score, $time_mgmt_score, $comment, $report_id];
    return executePreparedStatement($sql, $params);
}
function updateStatus($report_status, $intern_id) {
    $sql = "
    UPDATE internship
    SET report_status = ?
    WHERE intern_id = ?
    ";
    $params = [$report_status, $intern_id];
    return executePreparedStatement($sql, $params);
}

?>

