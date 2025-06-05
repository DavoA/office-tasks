<?php
class LogIn{
    private Database $db;
    private array $errors = [];
    private array $input = [];
    public function __construct(Database $db){
        $this->db = $db;
    }
    public function validateLogIn() : array{
        $this->errors = [];
        $this->input = [];
        $this->input["email"] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $this->input["password"] = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);

        if(empty($this->input["email"])){
            $this->errors[] = "Email address is required";
        } elseif (!$this->isValidEmail()) {
            $this->errors[] = "Invalid email address.";
        } else{
            if (!$this->db->emailExists($this->input["email"])){
                $this->errors[] = "Email address is not registered.";
            } else{
                $this->validatePassword();
            }
        }
        return [$this->errors, $this->input];
    }

    public function isValidEmail() {
        return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $this->input["email"]);
    }

    public function validatePassword(){
        if(empty($this->input["password"])) {
            $this->errors[] = "Password is required";
        } elseif(strlen($this->input["password"]) < 8 || strlen($this->input["password"]) > 64) {
            $this->errors[] = "Password length must be in range of (8,64)";
        } else{
            $storedHash = $this->db->getEmailPaswd($this->input["email"]);
            if ($storedHash === false || !password_verify($this->input['password'], $storedHash)) {
                $this->errors[] = "Invalid password";
            }
        }
    }
}
?>