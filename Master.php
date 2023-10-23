<?php 
if(session_id() ==="")
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class Master extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function save_settings(){
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['wallet_management'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $user_id = $_SESSION['user_id'];
            $columns = [];
            $values = [];
            foreach($_POST as $k => $v){
                if(!is_array($_POST[$k]) && !in_array($k, ['formToken'])){
                    $columns[] = $k;
                    $values[] = $v;
                }
            }
            if(empty($columns) && empty($values)){
               $resp['status'] = 'failed';
               $resp['msg'] = "No data has been sent.";
            }else{
                foreach($columns as $k => $v){
                    $setting_id = "";
                    $check = $this->query("SELECT setting_id FROM `settings` where `user_id` = '{$user_id}' and `name` = '{$v}'");
                    $settingsData = $check->fetchArray();
                    if(!empty($settingsData)){
                        $setting_id = $settingsData['setting_id'];
                    }
                    if(!empty($setting_id)){
                        $sql = "UPDATE `settings` set `value` = '{$values[$k]}' where `setting_id` = '{$setting_id}'";
                    }else{
                        $sql = "INSERT INTO `settings` (`user_id`, `name`, `value`) VALUES ('{$user_id}', '{$v}', '{$values[$k]}')";
                    }
                    $qry = $this->query($sql);
                    if(!$qry){
                        $resp['status'] = 'failed';
                        $resp['msg'] = "Error: ".$this->lastErrorMsg();
                        break;
                    }
                }
                $resp['status'] = 'success';
                $resp['msg'] = "Wallet Data has been updated successfully.";
            }
        }
        return json_encode($resp);
    }
    function save_task(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['task-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            if(empty($task_id)){
                $sql = "INSERT INTO `task_list` (`user_id`, `assigned_id`, `title`, `description`, `status`) VALUES ('{$user_id}', '{$assigned_id}', '{$title}', '{$description}', '{$status}')";
            }else{
                $sql = "UPDATE `task_list` set `assigned_id` = '{$assigned_id}', `title` = '{$title}', `description` = '{$description}', `status` = '{$status}' where `task_id` = '{$task_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($task_id))
                $resp['msg'] = 'New Task has been addedd successfully';
                else
                $resp['msg'] = 'Task Data has been updated successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function update_task_status(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['taskDetails'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "UPDATE `task_list` set `status` = '{$status}' where `task_id` = '{$task_id}'";
            $update = $this->query($sql);
            if($update){
                $resp['status'] = 'success';
                $resp['msg'] = "Task status has been updated successfully";
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function delete_task(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['tasks'];
        if(!isset($token) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `task_list` where `task_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The task data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function today_allocated(){
        $date = new DateTime(date("Y-m-d"), new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Manila'));
        $date = $date->format("Y-m-d");
        $sql = "SELECT count(task_id) FROM `task_list` where `user_id` = '{$_SESSION['user_id']}' and date(`date_created`) = '{$date}'";
        $qry = $this->querySingle($sql);
        return $qry ?? 0;
    }

    function to_review(){
        $sql = "SELECT count(task_id) FROM `task_list` where `user_id` = '{$_SESSION['user_id']}' and `status` = 2";
        $qry = $this->querySingle($sql);
        return $qry ?? 0;
    }

    function today_assigned(){
        $date = new DateTime(date("Y-m-d"), new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('Asia/Manila'));
        $date = $date->format("Y-m-d");
        $sql = "SELECT count(task_id) FROM `task_list` where `assigned_id` = '{$_SESSION['user_id']}' and date(`date_created`) = '{$date}'";
        $qry = $this->querySingle($sql);
        return $qry ?? 0;
    }
    function pending_tasks(){
        $sql = "SELECT count(task_id) FROM `task_list` where `user_id` = '{$_SESSION['user_id']}' and `status` = 0 ";
        $qry = $this->querySingle($sql);
        return $qry ?? 0;
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$master = new Master();
switch($a){
    case 'save_settings':
        echo $master->save_settings();
    break;
    case 'save_task':
        echo $master->save_task();
    break;
    case 'update_task_status':
        echo $master->update_task_status();
    break;
    case 'delete_task':
        echo $master->delete_task();
    break;
    case 'save_earning':
        echo $master->save_earning();
    break;
    case 'get_earning':
        echo $master->get_earning();
    break;
    case 'delete_earning':
        echo $master->delete_earning();
    break;
    default:
    // default action here
    break;
}