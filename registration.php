<?php
//require/load the authentication file script
require_once('auth.php');
// Generate Login Form Token
$_SESSION['formToken']['registration'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration | SQLite Login and Registration</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/custom.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
</head>
<body class="bg-dark bg-gradient">
   <div class="h-100 d-flex jsutify-content-center align-items-center">
       <div class='w-100'>
        <h1 class="py-5 text-center text-light">Create a New Account</h1>
        <div class="card my-3 col-md-4 offset-md-4">
            <div class="card-body">
                <!-- Registration Form Wrapper -->
                <form action="" id="register-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['registration'] ?>">
                    <center><small>Please enter your credentials.</small></center>
                    <div class="mb-3">
                        <label for="fullname" class="control-label">Fullname</label>
                        <input type="text" id="fullname" autofocus name="fullname" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="control-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="control-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control rounded-0" required>
                    </div>
                    <div class="mb-3 d-flex w-100 justify-content-between align-items-end">
                        <a href="login.php">Already have an Account? Login here</a>
                        <button class="btn btn-sm btn-primary rounded-0 my-1">Register</button>
                    </div>
                </form>
                <!-- Registration Form Wrapper -->
            </div>
        </div>
       </div>
   </div>
   <script>
    $(function(){
        $('#register-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Please wait...')
            $.ajax({
                url:'./LoginRegistration.php?a=register_user',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        _el.addClass('alert alert-success')
                        setTimeout(() => {
                            location.replace('./');
                        }, 2000);
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    _this.find('button').attr('disabled',false)
                    _this.find('button[type="submit"]').text('Save')
                }
            })
        })
    })
</script>
</body>
</html>