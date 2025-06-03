<?php
$db = new PDO('mysql:host=localhost;dbname=website', 'root', 'root');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
if("POST" == $_SERVER["REQUEST_METHOD"]) {
    [$errors, $input] = validate_form();
    if(empty($errors)) {
        process_registration($input);
        header("Location: /office_tasks/task1/hello.php");
        exit;
    }
}
function process_registration($input){
    global $db;
    $stmt = $db->prepare('INSERT INTO users (first_name, last_name, phone_number, email, password_hash) VALUES (?, ?, ?, ?, ?)');
    $hashedPassword = password_hash($input["password"], PASSWORD_DEFAULT);
    $stmt->execute([$input["first_name"], $input["last_name"], $input["phone"], $input["email"],$hashedPassword]);
}
function validate_form(){
    global $db;
    $errors = [];
    $input = [];
    $input['first_name'] = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $input['last_name'] = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $input["phone"] = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $input["email"] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $input["password"] = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $input["confirm_password"] = filter_input(INPUT_POST, 'confirm_password', FILTER_UNSAFE_RAW);
    
    $name_fields = ['first_name', 'last_name'];
    foreach ($name_fields as $field) {
        $fieldName = ucwords(str_replace('_', ' ', $field));
        if(empty($input[$field])) {
            $errors[] = "{$fieldName} is required";
            continue;
        }
        if(strlen($input[$field]) < 3 || strlen($input[$field]) > 30) {
            $errors[] = "{$fieldName} length must be in range of (3,30)";
        }
        if (preg_match('/[^a-zA-Z ]/', $input[$field])) {
            $errors[] = "{$fieldName} can only contain letters and spaces.";
        }
    }

    if (empty($input['phone'])) {
        $errors[] = "Phone number is required";
    } else {
        $sanitized_phone = preg_replace('/[^\d+]/', '', $input['phone']);
        if (!preg_match('/^\+?\d{10,}$/', $sanitized_phone)) {
             $errors[] = "Phone number must be 10 digits or + and 10 digits";
        }
    }

    if(empty($input['email'])){
        $errors[] = "Email address is required";
    } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $input["email"])) {
        $errors[] = "Invalid email address.";
    } else{
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->bindParam(':email', $input["email"]);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        if ($count > 0){
            $errors[] = "Email address is already registered.";
        }
    }

    $password_fields = ['password', 'confirm_password'];
    foreach ($password_fields as $field) {
        $fieldName = ucwords(str_replace('_', ' ', $field));
        if(empty($input[$field])) {
            $errors[] = "{$fieldName} is required";
            continue;
        }
        if(strlen($input[$field]) < 8 || strlen($input[$field]) > 64) {
            $errors[] = "{$fieldName} length must be in range of (8,64)";
        }
    }
    if($input["password"] !== $input["confirm_password"]) {
        $errors[] = "Passwords do not match";
    }

    return [$errors, $input];
}
?>
<link href="https://fonts.googleapis.com/css2?family=Share+Tech&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<?php if (!empty($errors)): ?>
    <div class="error-message">
        <?php foreach($errors as $index => $error): ?>
            <p><?php echo $error; ?></p>
                <?php if ($index < count($errors) - 1): ?>
                <hr>
                <?php endif; ?>
            </p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
    <div class="form-container">
        <h2>Registration Form</h2>
        <hr>
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" autocomplete="off">
        </div>
        <div class="form-group" >
            <label for="phone">Phone:</label>
            <input type="text" name="phone" id="phone" autocomplete="off">
        </div>
        <hr>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" autocomplete="off">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" autocomplete="new-password">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password">
        </div>  
        <div class="form-group">
            <input type="submit" value="Register">
        </div>
    </div>
</form>