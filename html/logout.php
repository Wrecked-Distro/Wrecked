<?php
  session_unregister("usertype");
  session_unregister("username");
  session_unregister("password");
  session_destroy();
  
  echo "<meta http-equiv=\"refresh\" content=\"0;url=http://wrecked-distro.com\">";
?>
