<?php
include 'connect.php';

function executePreparedStatement($sql, $params) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    if (stripos(trim($sql), "SELECT") === 0) {
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    } else {
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}