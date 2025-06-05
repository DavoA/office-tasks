<?php
require_once 'config/config.php';
require_once 'src/database.php';
require_once 'src/login_validator.php';
require_once 'components/form.php';
$db = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$db->getConnection();
$login = new LogIn($db);
$errors = [];
if("POST" == $_SERVER["REQUEST_METHOD"]) {
    [$errors, $input] = $login->validateLogIn();
    if(empty($errors)){
        header("Location: /office_tasks/task2/hello.php");
        exit;
    }
}
$field_names = ['email','password'];
customForm("log in", $field_names,'log in', 1, $errors);
?>