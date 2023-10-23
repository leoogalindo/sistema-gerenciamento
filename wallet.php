<?php 
$_SESSION['formToken']['wallet_management'] = password_hash(uniqid(),PASSWORD_DEFAULT);
include("Master.php");  
?>
<h1 class="text-center fw-bolder">Your Wallet Management</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-5 col-md-6 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <form action="" id="wallet-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['wallet_management'] ?>">
                    <div class="mb-3">
                        <label for="startingBalance">Starting Balance</label>
                        <input type="number" min="0" name="startingBalance" id="startingBalance" class="form-control text-end" value="<?= $master->get_baseAmount()?>" step="any" required>
                    </div>
                    <div class="mb-3">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <button class="btn btn-sm btn-primary rounded-0">Update</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#wallet-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            _this.find('button').attr('disabled',true)
            _this.find('button[type="submit"]').text('Loging in...')
            $.ajax({
                url:'./Master.php?a=save_settings',
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
                            _el.remove()
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
