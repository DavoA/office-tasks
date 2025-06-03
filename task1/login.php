<?php
$db = new PDO('mysql:host=localhost;dbname=website', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if("POST" == $_SERVER["REQUEST_METHOD"]) {
    [$errors, $input] = validate_login();
    if(empty($errors)){
        header("Location: /office_tasks/task1/hello.php");
        exit;
    }
}

function validate_login(){
    global $db;
    $errors = [];
    $input = [];
    $input["email"] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $input["password"] = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

    if(empty($input["email"])){
        $errors[] = "Email address is required";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $input["email"])) {
        $errors[] = "Invalid email address.";
    } else{
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $input["email"]);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count === 0){
            $errors[] = "Email address is not registered.";
        } else{
            if(empty($input["password"])) {
                $errors[] = "Password is required";
            } elseif(strlen($input["password"]) < 8 || strlen($input["password"]) > 64) {
                $errors[] = "Password length must be in range of (8,64)";
            } else{
                $stmt = $db->prepare("SELECT password_hash FROM users WHERE email = :email");
                $stmt->bindParam(':email', $input["email"]);
                $stmt->execute();
                $storedHash = $stmt->fetchColumn();
                if ($storedHash === false || !password_verify($input['password'], $storedHash)) {
                    $errors[] = "Invalid password";
                }
            }
        }
    }
    return [$errors, $input];
}
?>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-container">
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach($errors as $index => $error): ?>
                    <p><?php echo $error; ?></p>
                    <?php if ($index < count($errors) - 1): ?>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <h2>Log in</h2>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" autocomplete="off">
        </div>
        <div class="form-group">
            <input type="submit" value="Register">
        </div>
        
        <div class="form-group" style="text-align: center; margin-top: 15px;">
            <p>Didn't have account? <a href="registration.php">Sign up here</a></p>
        </div>
    </div>
</form>