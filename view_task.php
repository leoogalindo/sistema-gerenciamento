<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT *, COALESCE((SELECT `fullname` FROM `user_list` where `user_list`.`user_id` = `task_list`.`assigned_id`),'N/A') as `assigned` FROM `task_list` where `task_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    if(!empty($data) && !in_array($_SESSION['user_id'], [$data['assigned_id'], $data['user_id']])){
        echo "<scritp> alert(`You are not allowed to view this page.`); location.replace(document.referer)</script>";
    }

}else{
    throw new ErrorException("This page requires a valid ID.");
}
$_SESSION['formToken']['taskDetails'] = password_hash(uniqid(), PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder">Task Details</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-8 col-md-10 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <div class="col-auto flex-shrink-1 flex-grow-1">
                        <h2><b><?= $data['title'] ?? "" ?></b></h2>
                    </div>
                    <div class="col-auto">
                        <?php if(isset($data['status'])):
                            switch($data['status']){
                                case 0:
                                    echo "<span class='badge bg-light border rounded-pill px-3 text-dark'>Pending</span>";
                                    break;
                                case 1:
                                    echo "<span class='badge bg-primary border rounded-pill px-3'>On-Progress</span>";
                                    break;
                                case 2:
                                    echo "<span class='badge bg-warning border rounded-pill px-3'>For Review</span>";
                                    break;
                                case 3:
                                    echo "<span class='badge bg-danger border rounded-pill px-3'>Closed</span>";
                                    break;
                            }
                        endif;
                        ?>
                    </div>
                    <?php if(isset($data['assigned_id']) && isset($data['status']) && $data['assigned_id'] == $_SESSION['user_id'] && $_SESSION['type'] == 2): ?>
                        <?php if($data['status'] == 0): ?>
                            <button class="btn btn-primary btn-sm rounded-0 mx-2" type="button" id="update_status" data-status="1">Mark Task as Started</button>
                        <?php elseif($data['status'] == 1): ?>
                            <button class="btn btn-warning btn-sm rounded-0 mx-2" type="button" id="update_status" data-status="2">Set for Review</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <hr class="mx-auto border-primary opacity-100" style="width:50px;height:3px">
                <dl>
                    <?php if(isset($data['assigned_id']) && $data['assigned_id'] != $_SESSION['user_id']): ?>
                    <dt class="text-body-tertiary">Assigned To:</dt>
                    <dd class="ps-4 h5 fw-lighter"><?= $data['assigned'] ?? "" ?></dd>
                    <?php endif; ?>
                    <dt class="text-body-tertiary">Details:</dt>
                    <dd class="ps-4 h5 fw-lighter py-3"><?= $data['description'] ?? "" ?></dd>
                </dl>

                <hr>
                <div class="text-center">
                    <a href="./?page=task" class="btn btn btn-secondary rounded-0">Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#update_status').click(function(e){
            e.preventDefault()
            start_loader()
            var _conf = confirm(`Are you sure you want update the status of this task?`)
            if(_conf === true){
                $.ajax({
                    url:"./Master.php?a=update_task_status",
                    method:'POST',
                    data:{
                        formToken:'<?= $_SESSION['formToken']['taskDetails'] ?>',
                        task_id:'<?= $data['task_id'] ?? '' ?>',
                        status: $(this).attr('data-status')
                    },
                    dataType:'json',
                    error: err=>{
                        end_loader()
                        console.alert(err)
                        alert(`An error occurred while updating the task status`);
                    },
                    success: function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            if(!!resp.msg){
                                alert(resp.msg);
                            }
                            end_loader()
                            console.error(resp)
                        }
                    }
                })
            }
            end_loader()
        })
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
