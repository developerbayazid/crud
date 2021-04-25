<?php
define('DB_NAME', '/data/db.txt');
define('DB_USER', '/data/users.txt');

// Data Seeding
function seed(){
    $data = array(
        array(
            'id' => 1,
            'fname' => 'Bayazid',
            'lname' => 'Hasan',
            'roll' => 12
        ),
        array(
            'id' => 2,
            'fname' => 'Kamal',
            'lname' => 'Ahmed',
            'roll' => 11
        ),
        array(
            'id' => 3,
            'fname' => 'Rajen',
            'lname' => 'Saleh',
            'roll' => 10
        ),
        array(
            'id' => 4,
            'fname' => 'Ripon',
            'lname' => 'Miya',
            'roll' => 9
        ),
    );
    $data = json_encode($data);
    file_put_contents(DB_NAME, $data, LOCK_EX);
}
// Report Generat
function generateReport(){
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Roll</th>
            <th>Action</th>
        </tr>
        <?php foreach($students as $student): ?>
            <tr>
                <td><?php printf("%s %s", $student['fname'], $student['lname']); ?></td>
                <td><?php printf("%s", $student['roll']); ?></td>
                <?php if(isAdmin()): ?>
                <td><a href="index.php?task=edit&id=<?php echo $student['id']; ?>">Edit | </a><a class="delete" href="index.php?task=delete&id=<?php echo $student['id']; ?>">Delete</a></td>
                <?php endif; ?>
                <?php if(isEditor()): ?>
                <td><a href="index.php?task=edit&id=<?php echo $student['id']; ?>">Edit </a></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
<?php    
}
// Add New Student
function addStudent($fname, $lname, $roll){
    $found = false;
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    $newId = getNewId($students);

    foreach($students as $_student){
        if($_student['roll'] == $roll){
            $found = true;
            break;
        }
    }
    if(!$found){
        $student = array(
            'id' => $newId,
            'fname' => $fname,
            'lname' => $lname,
            'roll' => $roll,
        );
        array_push($students, $student);
        $data = json_encode($students);
        file_put_contents(DB_NAME, $data, LOCK_EX);
        return true;
    }else{
        return false;
    }
}

function getStudent($id){
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    foreach($students as $student){
        if($student['id'] == $id){
           return $student;
        }
    }
    return false;
}

function updateStudent($id, $fname, $lname, $roll){
    $found = false;
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    foreach($students as $_student){
        if($_student['roll'] == $roll && $_student['id'] != $id){
            $found = true;
            break;
        }
    }
    if(!$found){
        $students[$id-1]['fname'] = $fname;
        $students[$id-1]['lname'] = $lname;
        $students[$id-1]['roll'] = $roll;
        $data = json_encode($students);
        file_put_contents(DB_NAME, $data, LOCK_EX);
        return true;
    }
    return false;
}

function deleteStudent($id){
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    
    unset($students[$id-1]);
    $data = json_encode($students);
    file_put_contents(DB_NAME, $data, LOCK_EX);
}

function printRaw(){
    $jsonData = file_get_contents(DB_NAME);
    $students = json_decode($jsonData, true);
    print_r($students);
}

function getNewId($students){
    $maxId = max(array_column($students, 'id'));
    return $maxId+1;
}

##=================================================================

// Add New User
function addUser($username, $user, $password){
    $found = false;
    $jsonData = file_get_contents(DB_USER);
    $users = json_decode($jsonData, true);
    $newId = getNewIdUser($users);

    foreach($users as $_user){
        if($_user['r_username'] == $username){
            $found = true;
            break;
        }
    }
    if(!$found){
        $user = array(
            'r_id' => $newId,
            'r_username' => $username,
            'user' => $user,
            'r_password' => $password,
        );
        array_push($users, $user);
        $data = json_encode($users);
        file_put_contents(DB_USER, $data, LOCK_EX);
        return true;
    }else{
        return false;
    }
}
function getNewIdUser($users){
    $maxId = max(array_column($users, 'r_id'));
    return $maxId+1;
}

// Login User
function userLogin($username, $password){
    $jsonData = file_get_contents(DB_USER);
    $users = json_decode($jsonData, true);
    foreach($users as $_user){
        if($_user['r_username'] == $username && $_user['r_password'] == $password){
           return true;
        }
    }
    return false;
}

function isAdmin(){
    $jsonData = file_get_contents(DB_USER);
    $users = json_decode($jsonData, true);
    $userRole = $users[$_SESSION['user_id']-1]['user'];
    if($userRole == 'admin'){
       return true;
    }
}
function isEditor(){
    $jsonData = file_get_contents(DB_USER);
    $users = json_decode($jsonData, true);
    $userRole = $users[$_SESSION['user_id']-1]['user'];
    if($userRole == 'editor'){
        return true;
    }
}
function userRole(){
    $jsonData = file_get_contents(DB_USER);
    $users = json_decode($jsonData, true);
    $userRole = $users[$_SESSION['user_id']-1]['user'];
    if($userRole == 'editor'){
        return 'editor';
    }else{
        return 'admin';
    }
}
