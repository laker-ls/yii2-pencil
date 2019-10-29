<?php

use lakerLS\pencil\models\Text;
use yii\widgets\ActiveForm;
use yii\helpers\Html;

/** @var Text $model */
?>
<div class="modal fade" id="modal-pencil-text" tabindex="-1" role="dialog" aria-labelledby="modal-pencil" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <?php
        $form = ActiveForm::begin([
            'method' => 'post',
            'action' => '/pencil/text/create-update',
            'options' => ['class' => 'pencil-form']
        ]);
        ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= empty($model->text) ? 'Создание' : 'Редактирование' ?> содержимого</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                echo $form->field($model, 'id_name')->hiddenInput(['value' => $model->id_name])->label(false);
                echo $form->field($model, 'category_id')->hiddenInput(['value' => $model->category_id])->label(false);
                echo $form->field($model, 'text')->textarea()->label(false);
                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>