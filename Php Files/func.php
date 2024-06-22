<?php

function getAllData($table, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();

    if ($where == null) {
        $query = "SELECT * FROM $table";
    } else {
        $query = "SELECT * FROM $table WHERE $where";
    }

    $stmt = $con->prepare($query);

    if ($values == null) {
        $stmt->execute();
    } else {
        $stmt->execute($values);
    }

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();

    if ($json) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        if ($count > 0) {
            return $data;
        } else {
            return array("status" => "failure");
        }
    }

    return $count;
}







?>