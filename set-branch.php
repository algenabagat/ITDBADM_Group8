<?php
session_start();

if (isset($_POST['branch_id'])) {
    $_SESSION['selected_branch_id'] = (int)$_POST['branch_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>