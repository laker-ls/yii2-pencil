<?php

use lakerLS\pencil\helpers\PencilHelper;
use lakerLS\pencil\models\Image;
use yii\helpers\Html;


/** @var Image $model */
/** @var string $group */
/** @var string $width */
/** @var string $height */
?>

<div class="modal fade" id="modal-pencil-image" tabindex="-1" role="dialog" aria-labelledby="modal-pencil" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <?= Html::beginForm('/pencil/image/create-update', 'post', [
            'enctype' => 'multipart/form-data'
        ]) ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= isset($model[0]) ? 'Редактирование' : 'Создание' ?>. Пропорции изображения: <b><?= "{$width}x{$height}px." ?></b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview">
                        <?php foreach ($model as $image) : ?>
                            <?php if (!empty($image->full)) : ?>
                                <div id="image-<?= $image->id ?>" class="col-lg-3 cart" data-position="<?= $image->position ?>" data-group="<?= $image->group ?>">
                                    <div class="delete">
                                        <a href="#">✖</a>
                                    </div>
                                    <img class="img-fluid" src="<?= $image->full ?>" alt="<?= $image->alt ?>">
                                    <p class="name-img"><?= PencilHelper::fullNameImg($image)?></p>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?= Html::hiddenInput('Image[group]', $group) ?>
                    <?= Html::hiddenInput('Image[width]', $width) ?>
                    <?= Html::hiddenInput('Image[height]', $height) ?>
                </div>
                <div class="modal-footer">
                    <div class="row buttons">
                        <div class="col-lg-4 file-input">
                            <div class="new-input">Выбрать изображения</div>
                            <input class="default-input" type="file" name="Image[full][]" multiple />
                        </div>
                        <div class="col-lg-8 action">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
                            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
                <div class="error-label"></div>
            </div>
        <?= Html::endForm() ?>
    </div>
</div>
