<?php
use yii\grid\GridView;
use yii\helpers\Html;
use app\widgets\Alert;

$this->title = 'Supplier';
$this->params['breadcrumbs'][] = $this->title;
$formClass = 'ajax-form download';
$output = Html::beginForm('/site/export-supplier','post',['class'=>'inline-block '.$formClass]);
$output .= Html::hiddenInput('ids','',['id'=>'ids']);
$output .= Html::submitButton('ExportSupplier', ['class' => 'btn btn-primary mr5','form-class'=>'ajax-form download', 'data-action-before'=>'get_ids']);
$output .= Html::endForm();
?>
<div class="site-supplier">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="messages-bar"></div>
    <div class="row btn-group-top">
        <div class="col-xs-12">
            <div class="btn-group pull-right">
                <div class="btn-group-top">
                    <?= $output; ?>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
        </div>
    </div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\CheckboxColumn'],
        [
            'label' => 'Id',
            'attribute' => 'id',
        ],
        [
            'label' =>'Name',
            'attribute' => 'name',
        ],
        [
            'label' =>'Code',
            'attribute' => 'code',
        ],
        [
            'label' => 'Status',
            'attribute' => 't_status',
            'filter' => \app\models\Supplier::getStatus()
        ]
    ],
]); ?>
</div>
<?php
$js = <<<JS
$(document).on('click','.select-on-check-all',function (){
    var ids = $('.grid-view,.grid-view2').yiiGridView('getSelectedRows');
    if (ids.length){
        $('.messages-bar').html('<div id="w0-message-0" class="alert-success alert-message alert alert-dismissible" role="alert">All <b>'+ids.length+'</b> Conversations on this page have been selected. <a href="javascript:void(0);" id="all-selected1">Select all conversations that match this search</a><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button></div>');   
    }else{
        $('#w0-message-0').remove();
    }
});

//selected all page data
$(document).on('click','#all-selected1',function (){
    $('table').find(":checkbox").prop('checked', true);
    var ids = $('.grid-view,.grid-view2').yiiGridView('getSelectedRows');
    if (ids.length){
        $('.messages-bar').html('<div id="w0-message-0" class="alert-success alert-message alert alert-dismissible" role="alert">All Conversations in this search have been selected. <a href="javascript:void(0);" id="clear-selected1">Clear selection</a><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">×</span></button></div>');   
    }else{
        $('#w0-message-0').remove();
    }
});

//clear selected 
$(document).on('click','#clear-selected1',function (){
    $('table').find(":checkbox").prop('checked', false);
    $('#w0-message-0').remove();
    var ids = $('.grid-view,.grid-view2').yiiGridView('getSelectedRows');
    console.log(ids)
});

$(document).on('click', 'button[data-action-before=get_ids]', function () {
    var ids = $('.grid-view,.grid-view2').yiiGridView('getSelectedRows');
    $(this).parents('form').find('#ids').val(ids.join());
});

$(document).on('submit','form.ajax-form',function(event){
    var target = $(this);
    var ids = $("#ids").val();
    console.log(ids);
    $.ajax({
        url:target.attr("action"),
        type:target.attr("method"),
        async: false,
        data:{"ids":ids},
        success:function(data) {
            if (data.status === '11'){
                window.location.href = data.data;
                return true;
            }
            alert(data.msg);
        }
    });
    return false;
});

JS;
$this->registerJs($js);
?>