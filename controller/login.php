<?php
require '../components/_db.php';
session_start();
function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$returnData = [];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// CHECKING EMPTY FIELDS
elseif(!isset($_POST['email']) 
    || !isset($_POST['password'])
    || empty(trim($_POST['email']))
    || empty(trim($_POST['password']))
    ):

    $fields = ['fields' => ['email','password']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // CHECKING THE EMAIL FORMAT (IF INVALID FORMAT)
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)):
        $returnData = msg(0,422,'Invalid Email Address!');

    // THE USER IS ABLE TO PERFORM THE LOGIN ACTION
    else:
        try{
            
            $fetch_user_by_email = "SELECT * FROM `users` WHERE `email`=:email";
            $query_stmt = $conn->prepare($fetch_user_by_email);
            $query_stmt->bindValue(':email', $email,PDO::PARAM_STR);
            $query_stmt->execute();

            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()):
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                $check_password = $password == $row['password'];

                // VERIFYING THE PASSWORD (IS CORRECT OR NOT?)
                // IF PASSWORD IS CORRECT THEN SEND THE LOGIN TOKEN
                if($check_password):
                    $_SESSION['loggedin'] = true;
                    $_SESSION['email'] = $email;
                    $returnData = [
                        'success' => 1,
                        'message' => 'You have successfully logged in.'
                    ];

                // IF INVALID PASSWORD
                else:
                    $returnData = msg(0,422,'Invalid Password!');
                endif;

            // IF THE USER IS NOT FOUNDED BY EMAIL THEN SHOW THE FOLLOWING ERROR
            else:
                $returnData = msg(0,422,'Invalid Email Address!');
            endif;
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }

    endif;

endif;

echo json_encode($returnData);