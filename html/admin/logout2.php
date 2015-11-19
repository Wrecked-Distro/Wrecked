<?php
  session_start();
  session_unregister("username");
  session_unregister("password");
  session_destroy();
  header("Location: login.php");
?>
