<?php
session_start(['cookie_lifetime' => 300]);
if($_SESSION['logedin'] == false){
    $_SESSION['private_page'] = "<blockquote>You have to login first to visit the page</blockquote>";
    header('location: auth.php');
}
require_once "inc/functions.php";
$info = '';
$task = $_GET['task'] ?? 'report';
$error = $_GET['error'] ?? '0';
$empty = $_GET['data'] ?? '1';

if('delete' == $task){
    if(!isAdmin()){
        header('location: index.php');
        die();
    }
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if($id > 0){
        deleteStudent($id);
        header('location: index.php');
    }
}
if('seed' == $task && userRole() == 'admin'){
    seed();
    $info = "Seeding is complete";
}elseif('seed' == $task && userRole() == 'editor'){
    $info = "Only admin can seeding";
}
if(isset($_POST['submit'])){
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $roll = filter_input(INPUT_POST, 'roll', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if($id){
        // Update Student
        if($fname != '' && $lname != '' && $roll != ''){
            $update = updateStudent($id, $fname, $lname, $roll);
            if($update){
                header('location: index.php');
            }else{
                $error = 1;
            }
        }
    }else{
        // Add Student
        if($fname != '' && $lname != '' && $roll != ''){
            $reseult = addStudent($fname, $lname, $roll);
            if($reseult){
                header('location: index.php');
            }else{
                $error = 1;
            }
        }else{
            header('location: index.php?task=add&data=empty');
        }
    }
    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.css">
</head>
<style>
    body{
        margin-top: 30px;
    }
</style>
<body>
    <div class="container">
        <div class="row">
            <div class="column column-60 column-offset-20">
                <h2>Crud Project</h2>
                <p>A sample project to perform CRUD operations using plain files and PHP</p>
                <?php include_once ('inc/templates/nav.php'); ?>
                <hr>
                <?php 
                if($info != ''){
                    echo "<blockquote>{$info}</blockquote>";
                }
                ?>
            </div>
        </div>

        <?php if('1' == $error): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <blockquote>Roll Number Duplicated</blockquote>
            </div>
        </div>
        <?php endif; ?>

        <?php if('empty' == $empty): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <blockquote>You must need to fill-up all the fields</blockquote>
            </div>
        </div>
        <?php endif; ?>

        <?php if('report' == $task): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <?php generateReport(); ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Add Student -->
        <?php if('add' == $task): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
            <?php 
                if(!isAdmin()){
                    echo "<blockquote>You must need to admin access for adding student</blockquote>";
                    die();
                } 
            ?>
                <form action="index.php?task=add" method="POST">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" value="<?php echo $fname; ?>" id="fname">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" value="<?php echo $lname; ?>" id="lname">
                    <label for="roll">Roll</label>
                    <input type="number" name="roll" value="<?php echo $roll; ?>" id="roll">
                    <button type="submit" name="submit" class="button-primary">Save</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Update Student -->
        <?php 
            if('edit' == $task): 
            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
            $student = getStudent($id);
            if($student): 
        ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <label for="fname">First Name</label>
                    <input type="text" name="fname" value="<?php echo $student['fname']; ?>" id="fname">
                    <label for="lname">Last Name</label>
                    <input type="text" name="lname" value="<?php echo $student['lname']; ?>" id="lname">
                    <label for="roll">Roll</label>
                    <input type="number" name="roll" value="<?php echo $student['roll']; ?>" id="roll">
                    <button type="submit" name="submit" class="button-primary">Update</button>
                </form>
            </div>
        </div>
        <?php 
            endif; 
        endif;
        ?>

    </div>
    <script src="assets/js/script.js"></script>
</body>
</html>