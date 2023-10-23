<?php 
$_SESSION['formToken']['expenses'] = password_hash(uniqid(),PASSWORD_DEFAULT);
$from = $_GET['from'] ?? date("Y-m-d");
$to = $_GET['to'] ?? date("Y-m-t");

?>
<h1 class="text-center fw-bolder">List of Expenses</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-10 col-md-11 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="row justify-content-end mb-3">
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary rounded-0 d-flex align-items-center" type="button" id="newExpense"><i class="material-symbols-outlined">add</i> Add New</button>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row align-items-center">
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
                            <col width="20%">
                            <col width="40%">
                            <col width="20%">
                            <col width="15%">
                        </colgroup>
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Date Added</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Amount</th>
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
                            $expenses_sql = "SELECT `expense_id`, `name`, `amount`, `date_created` FROM `expenses` where `user_id` = '{$_SESSION['user_id']}' and date(`date_created`) BETWEEN '{$from}' and '{$to}' ORDER BY strftime('%s', `date_created`) desc";
                            $expenses_qry = $conn->query($expenses_sql);
                            while($row = $expenses_qry->fetchArray()):
                                $date_created = new DateTime($row['date_created'], new DateTimeZone('UTC'));$date_created->setTimezone(new DateTimeZone('Asia/Manila'));
                            ?>
                            <tr>
                                <td class="text-center"><?= $i++; ?></td>
                                <td><?= $date_created->format('Y-m-d g:i A') ?></td>
                                <td><?= $row['name'] ?></td>
                                <td class="text-end"><?= number_format($row['amount'], 2) ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-sm btn-outline-primary rounded-0 edit_data" type="button" data-id='<?= $row['expense_id'] ?>' title="Edit Data"><span class="material-symbols-outlined">edit</span></button>
                                        <button class="btn btn-sm btn-outline-danger rounded-0 delete_data" type="button" data-id='<?= $row['expense_id'] ?>' title="Delete Data"><span class="material-symbols-outlined">delete</span></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                            <?php if(!$expenses_qry->fetchArray()): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No data found.</td>
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
        $('#newExpense').click(function(e){
            e.preventDefault()
            $('#expenseModal').find('.modal-title').text('Add New Expense')
            $('#expenseModal').modal('show')
        })
        $('.edit_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            $.ajax({
                url:'Master.php?a=get_expense',
                method: 'POST',
                data: {
                    token: '<?= $_SESSION['formToken']['expenses'] ?>',
                    id: id
                },
                dataType:'json',
                error: err => {
                    console.error(err)
                    alert('An error occured')
                    end_loader()
                },
                success: function(resp){
                    if(typeof resp === 'object'){
                        if(resp.status == 'success'){
                            var data = resp.data
                            var modal = $('#expenseModal')
                            modal.find('[name="expense_id"]').val(data.expense_id)
                            modal.find('[name="name"]').val(data.name)
                            modal.find('[name="amount"]').val(data.amount)
                            modal.find('.modal-title').text('Update Expense Data')
                            modal.modal('show')
                        }else{
                            console.error(resp)
                            alert(resp.error)
                        }
                    }else{
                        console.error(resp)
                        alert('An error occured')
                    }
                    end_loader()
                }
            })
        })
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this expense data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_expense',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['expenses'] ?>',
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
            location.replace(`./?page=expenses&from=${from}&to=${to}`)
        })
    })
</script>
