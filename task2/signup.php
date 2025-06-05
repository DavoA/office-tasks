<?php
require_once 'config/config.php';
require_once 'src/database.php';
require_once 'src/signup_validator.php';
require_once 'components/form.php';
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$db->getConnection();
$signup = new SignUp($db);
$errors = [];
if("POST" == $_SERVER["REQUEST_METHOD"]) {
    [$errors, $input] = $signup->validateSignUp();
    if(empty($errors)) {
        $db->insertRow($input);
        header("Location: /office_tasks/task2/hello.php");
        exit;
    }
}
$field_names = ['first_name','last_name', 'phone', 'email','password','confirm_password'];
customForm("sign up", $field_names,'sign up', 2, $errors);
?>