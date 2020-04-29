<?php

namespace lakerLS\pencil\models\query;

use lakerLS\pencil\models\Image;

class ImageQuery
{
    public static function findByGroupAsArray ($group)
    {
        return Image::find()
            ->where(['group' => $group])
            ->asArray()
            ->orderBy(['position' => SORT_DESC])
            ->all();
    }
}