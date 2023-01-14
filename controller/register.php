<?php
require '../components/_db.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

if ($_SERVER["REQUEST_METHOD"] != "POST") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (
    !isset($_POST['fullName'])
    || !isset($_POST['email'])
    || !isset($_POST['password'])
    || empty(trim($_POST['fullName']))
    || empty(trim($_POST['email']))
    || empty(trim($_POST['password']))
) :

    $fields = ['fields' => ['fullName', 'email', 'password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :

    $name = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    elseif (strlen($name) < 3) :
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');
    elseif($password != $cpassword):
        $returnData = msg(0, 422, 'Passwords do not match');
    else :
        try {

            $check_email = "SELECT `email` FROM `users` WHERE `email`=:email";
            $check_email_stmt = $conn->prepare($check_email);
            $check_email_stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $check_email_stmt->execute();

            if ($check_email_stmt->rowCount()) :
                $returnData = msg(0, 422, 'This E-mail already in use!');

            else :
                // $image = $_FILES["image"]["tmp_name"];
                // $uploads_dir = 'C:\xampp\htdocs\sample1\uploads';
                // $img_name = basename($_FILES["image"]["name"]);
                $insert_query = "INSERT INTO `users`(`full_name`,`image`,`email`,`password`) VALUES(:name,:image,:email,:password)";

                $insert_stmt = $conn->prepare($insert_query);

                // DATA BINDING
                $insert_stmt->bindValue(':name', $name, PDO::PARAM_STR);
                $insert_stmt->bindValue(':image', $img_name, PDO::PARAM_STR);
                $insert_stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $insert_stmt->bindValue(':password', $password, PDO::PARAM_STR);

                $insert_stmt->execute();
                // if(!file_exists("$uploads_dir/$img_name")){
                //     move_uploaded_file($image, "$uploads_dir/$img_name");
                // }
                $returnData = msg(1, 201, 'You have successfully registered.');

            endif;
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;
endif;

echo json_encode($returnData);