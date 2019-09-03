<?php

use lakerLS\pencil\controllers\ImageController;
use lakerLS\pencil\models\Image;
use yii\bootstrap4\Html;


/** @var Image $model */
/** @var string $group */

?>

<div class="modal fade" id="modal-pencil-image" tabindex="-1" role="dialog" aria-labelledby="modal-pencil" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <?= Html::beginForm('/pencil/image/create-update', 'post', [
            'enctype' => 'multipart/form-data'
        ]) ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= empty($model->text) ? 'Создание' : 'Редактирование' ?> содержимого</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview">
                        <?php foreach ($model as $image) : ?>
                            <?php if (!empty($image->src)) : ?>
                                <div id="image-<?= $image->id ?>" class="col-lg-3 cart" data-position="<?= $image->position ?>">
                                    <div class="delete">
                                        <a href="#">✖</a>
                                    </div>
                                    <img class="img-fluid" src="<?= $image->src ?>" alt="<?= $image->alt ?>">
                                    <p class="name-img"><?= ImageController::fullName($image)?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?= Html::hiddenInput('Image[group]', $group) ?>
                </div>
                <div class="modal-footer">

                        <div class="col-lg-6 file-input">
                            <div class="new-input">Выбрать изображения</div>
                            <input class="default-input" type="file" name="Image[src][]" multiple />
                        </div>
                        <div class="col-lg-6 action">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                        </div>

                </div>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>
