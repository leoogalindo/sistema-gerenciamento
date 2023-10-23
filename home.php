<h1 class="text-center fw-bolder">Bem vindo ao ...</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<?php if($_SESSION['type'] != 0): ?>
<?php 
include("Master.php");  
?>
<div class="row">
    <?php if($_SESSION['type'] == 1): ?>
    <div class="col-lg-5 col-md-6 col-sm-12 mx-auto py-3">
        <div class="card rounded-0 shadow">
            <div class="card-body rounded-0">
                <div class="contain-fluid">
                    <div class="dash-box">
                        <div class="dash-box-title">Total de Tarefas</div>
                        <div class="dash-box-icon"><span class="material-symbols-outlined">checklist</span></div>
                        <div class="dash-box-text"><?= number_format($master->today_allocated()) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-6 col-sm-12 mx-auto py-3">
        <div class="card rounded-0 shadow">
            <div class="card-body rounded-0">
                <div class="contain-fluid">
                    <div class="dash-box">
                        <div class="dash-box-title">Tarefas para Revisar</div>
                        <div class="dash-box-icon"><span class="material-symbols-outlined">find_in_page</span></div>
                        <div class="dash-box-text"><?= number_format($master->to_review()) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="col-lg-5 col-md-6 col-sm-12 mx-auto py-3">
        <div class="card rounded-0 shadow">
            <div class="card-body rounded-0">
                <div class="contain-fluid">
                    <div class="dash-box">
                        <div class="dash-box-title">Total de Tarefas</div>
                        <div class="dash-box-icon"><span class="material-symbols-outlined">checklist</span></div>
                        <div class="dash-box-text"><?= number_format($master->today_assigned()) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 col-md-6 col-sm-12 mx-auto py-3">
        <div class="card rounded-0 shadow">
            <div class="card-body rounded-0">
                <div class="contain-fluid">
                    <div class="dash-box">
                        <div class="dash-box-title">Tarefas Pendentes</div>
                        <div class="dash-box-icon"><span class="material-symbols-outlined">pendentes</span></div>
                        <div class="dash-box-text"><?= number_format($master->pending_tasks()) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
