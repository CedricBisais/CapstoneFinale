<?php
session_start();
$connection = mysqli_connect("localhost", "root", "");
$database = mysqli_select_db($connection, 'thesesarchive_db');

if (isset($_POST['deleteData'])) {
    $id = $_POST['delete_id'];

    // Delete corresponding row in tbl_account_information
    $query_info = "DELETE FROM tbl_account_information WHERE account_id=?";
    $stmt_info = mysqli_prepare($connection, $query_info);
    mysqli_stmt_bind_param($stmt_info, "i", $id);
    $query_run_info = mysqli_stmt_execute($stmt_info);
    mysqli_stmt_close($stmt_info);

    if (!$query_run_info) {
        $_SESSION['status'] = "Data Deletion Failed: " . mysqli_error($connection);
        $_SESSION['status_code'] = 'error';
        header('Location: adminUserManagement.php');
        exit();
    }

    // Proceed with deleting the user from tbl_accounts
    $query = "DELETE FROM tbl_accounts WHERE account_id=?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    $query_run = mysqli_stmt_execute($stmt);

    if ($query_run) {
        $_SESSION['status'] = "Data Deleted Successfully!";
        $_SESSION['status_code'] = 'success';
        header('Location: adminUserManagement.php');
    } else {
        $_SESSION['status'] = "Data Deletion Failed: " . mysqli_error($connection);
        $_SESSION['status_code'] = 'error';
        header('Location: adminUserManagement.php');
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);
    exit();
}
?>
