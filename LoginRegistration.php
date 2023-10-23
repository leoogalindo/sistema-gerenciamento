<?php 
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class LoginRegistration extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function login(){
        /**
         * Login Form
         * 
         */
        //Extracting Post array to variables.
        extract($_POST);
        // Retrieving Allowed Token
        $allowedToken = $_SESSION['formToken']['login'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            // throw new ErrorException("Security Check: Form Token is valid.");
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            // Query Statement
            $sql = "SELECT * FROM user_list where username = :username ";
            // Preparing Query Statement
            $stmt = $this->prepare($sql);
            // binding Query Value/s
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            // Executing Query
            $result = $stmt->execute();
            // Fetching Result
            $data = $result->fetchArray();
            if(!empty($data)){
                // print_r($data['password']);exit;
                //Verifying Password
                $password_verify = password_verify($password, $data['password']);
                if($password_verify){
                    if($data['status'] == 1){
                        // Login Success
                        $resp['status'] = "success";
                        $resp['msg'] = "Login successfully.";
                        foreach($data as $k => $v){
                            if(!is_numeric($k) && !in_array($k, ['password']))
                            $_SESSION[$k] = $v;
                        }
                    }elseif($data['status'] == 0){
                        // Pending
                        $resp['status'] = "failed";
                        $resp['msg'] = "Sua conta está esperando aprovação.";
                    }elseif($data['status'] == 2){
                        // Denied
                        $resp['status'] = "failed";
                        $resp['msg'] = "Acesso negado. Por favor entre em contato com o gerente.";
                    }
                    elseif($data['status'] == 3){
                        // Blocked
                        $resp['status'] = "failed";
                        $resp['msg'] = "Acesso bloqueado. Por favor entre em contato com o gerente.";
                    }else{
                        $resp['status'] = "failed";
                        $resp['msg'] = "Status Inválido. Por favor entre em contato com o gerente.";
                    }
                   
                }else{
                    // Invalid Password
                    $resp['status'] = "failed";
                    $resp['msg'] = "Usuário ou senha inválidos.";
                }
            }else{
                // Invalid Username
                $resp['status'] = "failed";
                $resp['msg'] = "Usuário ou senha inválidos.";
            }
        }
        return json_encode($resp);
    }
    function logout(){
        // Destroying user session
        session_destroy();
        header("location:./");
    }
    function register_user(){
        /**
         * User Registration Form Action
         */

         //escaping input values
        foreach($_POST as $k => $v){
            if(!in_array($k, ['user_id', 'formToken']) && !is_numeric($v) && !is_array($_POST[$k])){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        //extracting Post array values
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['registration'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            // throw new ErrorException("Security Check: Form Token is valid.");
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            // Table column 
            $dbColumn = "(`fullname`, `username`, `password`, `status`, `type`)";
            // Encypting Password
            $password = password_hash($password, PASSWORD_DEFAULT);
            // Table Values
            $values = "('{$fullname}', '{$username}', '{$password}', 0, 2)";
            // insertion Query Statement
            $sql = "INSERT INTO `user_list` {$dbColumn} VALUES {$values}";
            // Executing Insertion Query
            $insert = $this->query($sql);
            if($insert){
                // Successfull insertion
                $resp['status'] = 'success';
                $resp['msg'] = "Your Account has been created successfully but it is subject for approval.";

            }else{
                // Insertion Failed
                $resp['status'] = 'failed';
                $resp['msg'] = "Error: ".$this->lastErrorMsg();
            }
        }
        echo json_encode($resp);
    }
    function update_user(){
        /**
         * Update User Form Action
         */

        //extracting Post array values
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['manage_user'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            // throw new ErrorException("Security Check: Form Token is valid.");
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            //update data
            $data = "`status` = '{$status}'";
            $data .= ",`type` = '{$type}'";
            
            // UPDATE Query Statement
            $sql = "UPDATE `user_list` set {$data} where `user_id` = '{$user_id}'";
            // Executing update Query
            $update = $this->query($sql);
            if($update){
                // Successfull Update
                $resp['status'] = 'success';
                $resp['msg'] = "User Account has been updated successfully.";

            }else{
                // Update Failed
                $resp['status'] = 'failed';
                $resp['msg'] = "Error: ".$this->lastErrorMsg();
            }
        }
        echo json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$LG = new LoginRegistration();
switch($a){
    case 'login':
        echo $LG->login();
    break;
    case 'logout':
        echo $LG->logout();
    break;
    case 'register_user':
        echo $LG->register_user();
    break;
    case 'update_user':
        echo $LG->update_user();
    break;
    default:
    // default action here
    break;
}