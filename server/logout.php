<?php
session_start();

session_unset();
session_destroy();
?>

<script>
localStorage.removeItem("loggedIn");
window.location.href = "../index.html";
</script>
