<?php
$con = mysqli_connect('localhost', 'root', '', 'thesesarchive_db');

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = intval($_GET['status']);
    
    // Use prepared statement for security
    $stmt = $con->prepare("UPDATE `tbl_archives` SET `status` = ? WHERE `archive_id` = ?");
    $stmt->bind_param("ss", $status, $id);
    
    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['msg'] = 'Status updated successfully';
    } else {
        $response['status'] = 'error';
        $response['msg'] = 'Error updating status: ' . $stmt->error;
    }
    
    $stmt->close();

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function update_status()
{
    global $con;

    if (isset($_POST['id']) && isset($_POST['status'])) {
        $id = intval($_POST['id']);
        $status = intval($_POST['status']);

        // Use prepared statement for security
        $stmt = $con->prepare("UPDATE `tbl_archives` SET `status` = ? WHERE `archive_id` = ?");
        $stmt->bind_param("ss", $status, $id);

        if ($stmt->execute()) {
            $resp['status'] = 'success';
            $resp['msg'] = "Archive status has been successfully updated.";
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = "An error occurred. Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $resp['status'] = 'failed';
        $resp['msg'] = "Invalid or incomplete data received.";
    }

    // Send the JSON response back to the client
    header('Content-Type: application/json');
    echo json_encode($resp);
}
?>
