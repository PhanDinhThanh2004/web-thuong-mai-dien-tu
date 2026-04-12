<?php
session_start();
session_unset();
session_destroy();

// Redirect back with a script to clear localStorage
echo "<script>
    localStorage.removeItem('currentUser');
    window.location.href = 'index.html';
</script>";
exit();
?>
