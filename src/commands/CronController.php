<?php

namespace lakerLS\pencil\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;

/**
 * Контроллер предназначенный для работы через консоль.
 */
class CronController extends Controller
{
    /**
     * Удаление не используемых изображений в папках image_full и image_mini.
     */
    public function actionDeleteImage()
    {
        $imgPath = ArrayHelper::getValue(Yii::$app->getModule('pencil')->params, 'imagePath');

        /** Проверка, указан ли `imagePath` для `full` и `mini`. В config.console */
        if ($imgPath === null) {
            return ExitCode::CONFIG;
        } else {
            if (empty($imgPath['full']) || empty($imgPath['mini'])) {
                return ExitCode::CONFIG;
            }
        }

        $dataDB = Yii::$app->db->createCommand("SELECT * FROM `pencil_image`")->queryAll();
        $count = 0;
        $aliasWeb = __DIR__ . '/../../../../../web/';

        foreach ($imgPath as $directory) {
            $directory = $aliasWeb . $directory . '/';

            $foldersImage = scandir($directory);
            unset($foldersImage[0], $foldersImage[1]);

            foreach($foldersImage as $folder) {
                $filesImage = scandir($directory . $folder);
                unset($filesImage[0], $filesImage[1]);

                /** @var string $image получили изображение, дальше проходимся по БД в поисках совпадений */
                foreach ($filesImage as $image) {
                    $coincidence = false;

                    foreach($dataDB as $row) {
                        foreach ($row as $column) {
                            if (strpos($column, $image) !== false) {
                                $coincidence = true;
                            }
                        }
                    }
                    if ($coincidence == false) {
                        $imgFull = $directory . $folder . '/' . $image;
                        $imgMini = str_replace('image_full', 'image_mini', $imgFull);

                        unlink($imgFull);
                        if (file_exists($imgMini)) {
                            unlink($imgMini);
                        }

                        $count++;
                    }
                }
            }
        }

        echo 'Неиспользуемые изображения были успешно удалены. Количество: ' . $count . PHP_EOL;
        return ExitCode::OK;
    }
}