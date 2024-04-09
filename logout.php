<?php
session_start(); /* use to access session */
if (isset($_SESSION['admin_id'])) {
    unset ($_SESSION['admin_id']);
}
header("Location: index.html")
?>