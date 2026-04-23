<?php
function updateAssessor($user_id,$full_name, $phone_number, $email_address, $organisation, $assessor_type) {
    $sql = "UPDATE assessor SET full_name = ?, phone_number = ?, email_address = ?, organisation = ?, assessor_type = ? WHERE user_id = ?";
    $params = [$full_name, $phone_number, $email_address, $organisation, $assessor_type, $user_id];
    return executePreparedStatement($sql, $params);
}

function deleteAssessor($user_id) {
    $sql = "DELETE FROM assessor WHERE user_id = ?";
    $params = [$user_id];
    return executePreparedStatement($sql, $params);
}

function addAssessor($username, $password, $full_name, $phone_number, $email_address, $organisation, $assessor_type) {
    global $conn;

    $sql_login = "INSERT INTO user_login (username, password, system_role) VALUES (?, ?, 'Assessor')";
    $params_login = [$username, $password];
    $execute_login = executePreparedStatement($sql_login, $params_login);

    if (!$execute_login) {
        return false;
    }

    // get the new autoincremented student id
    $new_user_id = $conn->insert_id;

    //insert using student_id we just grab, into this table
    $sql_assessor = "INSERT INTO assessor (user_id, full_name, phone_number, email_address, organisation, assessor_type) VALUES (?, ?, ?, ?, ?, ?)";
    $params_assessor = [$new_user_id, $full_name, $phone_number, $email_address, $organisation, $assessor_type];
    $execute_assessor = executePreparedStatement($sql_assessor, $params_assessor);

    return $execute_assessor;
}