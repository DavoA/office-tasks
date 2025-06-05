<?php
class SignUp{
    private Database $db;
    private array $errors = [];
    private array $input = [];
    public function __construct(Database $db){
        $this->db = $db;
    }
    public function validateSignUp(){
        $this->errors = [];
        $this->input = [];
        $info_fields = ['first_name', 'last_name', 'phone', 'email'];
        foreach($info_fields as $field){
            $this->input[$field] = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
        }
        $passwd_fields = ['password', 'confirm_password'];
        foreach($passwd_fields as $field){
            $this->input[$field] = filter_input(INPUT_POST, $field, FILTER_UNSAFE_RAW);
        }
        $this->validateNames();
        $this->validatePhoneNumber();
        $this->validateEmail();
        $this->validatePasswd();
        return [$this->errors, $this->input];
    }

    public function validateNames(){
        $name_fields = ['first_name', 'last_name'];
        foreach ($name_fields as $field) {
            $fieldName = ucwords(str_replace('_', ' ', $field));
            if(empty($this->input[$field])) {
                $this->errors[] = "{$fieldName} is required";
                continue;
            }
            if(strlen($this->input[$field]) < 3 || strlen($this->input[$field]) > 30) {
                $this->errors[] = "{$fieldName} length must be in range of (3,30)";
            }
            if (preg_match('/[^a-zA-Z ]/', $this->input[$field])) {
                $this->errors[] = "{$fieldName} can only contain letters and spaces.";
            }
        }
    }

    public function validatePhoneNumber(){
        if (empty($this->input['phone'])) {
            $this->errors[] = "Phone number is required";
        } else {
            $sanitized_phone = preg_replace('/[^\d+]/', '', $this->input['phone']);
            if (!preg_match('/^\+?\d{10,}$/', $sanitized_phone)) {
                $this->errors[] = "Phone number must be 10 digits or + and 10 digits";
            }
        }
    }

    public function validateEmail(){
        if(empty($this->input['email'])){
            $this->errors[] = "Email address is required";
        } elseif (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $this->input["email"])) {
            $this->errors[] = "Invalid email address.";
        } else{
            if ($this->db->emailExists($this->input['email'])){
                $this->errors[] = "Email address is already registered.";
            }
        }
    }

    public function validatePasswd(){
        $password_fields = ['password', 'confirm_password'];
        foreach ($password_fields as $field) {
            $fieldName = ucwords(str_replace('_', ' ', $field));
            if(empty($this->input[$field])) {
                $this->errors[] = "{$fieldName} is required";
                continue;
            }
            if(strlen($this->input[$field]) < 8 || strlen($this->input[$field]) > 64) {
                $this->errors[] = "{$fieldName} length must be in range of (8,64)";
            }
        }
        if($this->input["password"] !== $this->input["confirm_password"]) {
            $this->errors[] = "Passwords do not match";
        }
    }
}