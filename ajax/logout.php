<?php
session_start();
unset($_SESSION['username']);
echo"<script>window.location='../ajax/login.php';</script>";
?>