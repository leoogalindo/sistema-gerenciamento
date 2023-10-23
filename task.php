<?php 
$_SESSION['formToken']['tasks'] = password_hash(uniqid(),PASSWORD_DEFAULT);
$from = $_GET['from'] ?? date("Y-m-d");
$to = $_GET['to'] ?? date("Y-m-t");

?>
<h1 class="text-center fw-bolder">List of Tasks</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-10 col-md-11 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <?php if($_SESSION['type'] == 1): ?>
                <div class="row justify-content-end mb-3">
                    <div class="col-auto">
                        <a class="btn btn-sm btn-primary rounded-0 d-flex align-items-center" href="./?page=manage_task"><i class="material-symbols-outlined">add</i> Add New</a>
                    </div>
                </div>
                <?php endif; ?>
                <div class="mb-3">
                    <div class="row align-items-end">
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_from">Date From</label>
                            <input type="date" value="<?= $from ?>" class="form-control rounded-0" id="date_from" name="date_from" required="required">
                        </div>
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <label for="date_to">Date To</label>
                            <input type="date" value="<?= $to ?>" class="form-control rounded-0" id="date_to" name="date_to" required="required">
                        </div>
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12">
                            <button class="btn btn-primary rounded-0 d-flex align-items-center" id="filter"><span class="material-symbols-outlined">filter_alt</span> Filter</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover table-striped">
                        <colgroup>
                            <col width="5%">
                            <col width="15%">
                            <col width="30%">
                            <col width="20%">
                            <col width="15%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Date Added</th>
                                <th class="text-center">Title</th>
                                <?php if($_SESSION['type'] == 1): ?>
                                <th class="text-center">Assigned To</th>
                                <?php else: ?>
                                <th class="text-center">Assigned By</th>
                                <?php endif; ?>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $from = new DateTime($from, new DateTimeZone('Asia/Manila'));
                            $from->setTimezone(new DateTimeZone('UTC'));
                            $from = $from->format("Y-m-d");
                            $to = new DateTime($to, new DateTimeZone('Asia/Manila'));
                            $to->setTimezone(new DateTimeZone('UTC'));
                            $to = $to->format("Y-m-d");
                            $i = 1;
                            if($_SESSION['type'] == 1){
                                $tasks_sql = "SELECT `task_id`, `title`, `date_created`, `status`, (SELECT `fullname` FROM `user_list`  where `user_list`.`user_id` = `task_list`.`assigned_id`) as `assigned` FROM `task_list` where `user_id` = '{$_SESSION['user_id']}' and date(`date_created`) BETWEEN '{$from}' and '{$to}' ORDER BY strftime('%s', `date_created`) desc";
                            }else{
                                $tasks_sql = "SELECT `task_id`, `title`, `date_created`, `status`, (SELECT `fullname` FROM `user_list`  where `user_list`.`user_id` = `task_list`.`user_id`) as `assigned` FROM `task_list` where `assigned_id` = '{$_SESSION['user_id']}' and date(`date_created`) BETWEEN '{$from}' and '{$to}' ORDER BY strftime('%s', `date_created`) desc";
                            }

                            $tasks_qry = $conn->query($tasks_sql);
                            while($row = $tasks_qry->fetchArray()):
                                $date_created = new DateTime($row['date_created'], new DateTimeZone('UTC'));$date_created->setTimezone(new DateTimeZone('Asia/Manila'));
                            ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td><?= $date_created->format('Y-m-d g:i A') ?></td>
                                <td><?= $row['title'] ?></td>
                                <td class=""><?= ucwords($row['assigned']) ?></td>
                                <td class="text-center">
                                    <?php 
                                        switch($row['status']){
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
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-sm btn-outline-dark rounded-0 view_data" href="./?page=view_task&id=<?= $row['task_id'] ?>" data-id='<?= $row['task_id'] ?>' title="View Task"><span class="material-symbols-outlined">subject</span></a>
                                        <?php if($_SESSION['type'] == 1): ?>
                                        <a class="btn btn-sm btn-outline-primary rounded-0 edit_data" href="./?page=manage_task&id=<?= $row['task_id'] ?>" data-id='<?= $row['task_id'] ?>' title="Edit Task"><span class="material-symbols-outlined">edit</span></a>
                                        <button class="btn btn-sm btn-outline-danger rounded-0 delete_data" type="button" data-id='<?= $row['task_id'] ?>' title="Delete Task"><span class="material-symbols-outlined">delete</span></button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(!$tasks_qry->fetchArray()): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No data found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this task data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_task',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['tasks'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('#filter').click(function(e){
            e.preventDefault()
            var from = $('#date_from').val()
            var to = $('#date_to').val()
            location.replace(`./?page=tasks&from=${from}&to=${to}`)
        })
    })
</script>
