<?php
function customForm($title, $fields, $buttonText, $type, $errors){
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
        <h2><?php echo htmlspecialchars(ucfirst($title)); ?></h2>
        <hr>
        <?php foreach($fields as $index => $field): ?>
            <div class="form-group">
                <label for="<?php echo htmlspecialchars($field) ?>"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $field))); ?>:</label>
                <?php if($type == 2){?>
                    <input type="<?php echo htmlspecialchars(($index < 3) ? "text" : ($index == 3 ? "email" : "password"));?>" name="<?php echo htmlspecialchars($field) ?>" id="<?php echo htmlspecialchars($field) ?>" autocomplete="off">
                <?php } elseif ($type == 1) {?>
                    <input type="<?php echo htmlspecialchars(($index == 0) ? "email" : "password");?>" name="<?php echo htmlspecialchars($field) ?>" id="<?php echo htmlspecialchars($field) ?>" autocomplete="off">
                <?php } ?>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <input type="submit" value="<?php echo htmlspecialchars(ucfirst($buttonText)) ?>">
        </div>
        <div class="form-group" style="text-align: center; margin-top: 15px;">
            <?php if($type == 1){ ?>
                <p>Didn't have account? <a href="signup.php">Sign up here</a></p>
            <?php } elseif($type == 2){ ?>
                <p>Have an account? <a href="login.php">Log In here</a></p>
            <?php } ?>
        </div>
    </div>
</form>
<?php
}
?>