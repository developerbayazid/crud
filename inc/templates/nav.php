<?php 
session_start(); 
require_once "inc/functions.php";
?>
<div style="float: left;">
    <p>
        <a href="index.php?task=report">All Students</a> 
        <?php if(isAdmin()): ?>
        <a href="index.php?task=add">| Add New Student</a>
        <?php endif; ?>
        <?php if(isAdmin()): ?>
        <a href="index.php?task=seed">| Seed</a>
        <?php endif; ?>
    </p>
</div>

<div style="float: right;">
<?php if($_SESSION['logedin'] == false): ?>
    <a href="auth.php">Login</a>
<?php else: ?>
    <a href="auth.php?logout=success">Log Out (<?php echo userRole(); ?>)</a>
<?php endif; ?>
</div>