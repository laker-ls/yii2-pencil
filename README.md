<h1 align="center">
    yii2-pencil
</h1>


[![Stable Version](https://poser.pugx.org/laker-ls/yii2-pencil/v/stable)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-pencil/v/unstable)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![License](https://poser.pugx.org/laker-ls/yii2-pencil/license)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-pencil/downloads)](https://packagist.org/packages/laker-ls/yii2-pencil)

> ВНИМАНИЕ: расширение работает только с существующей таблицей `category` в базе данных, где хранится `id` страницы.
Для работы так же необходим Rbac с существующей ролью. Используется bootstrap 4.

Отображение текста, которое редактируется через модальное окно, когда пользователь авторизован как администратор.
Не админ, видит обычный текст, в то же время администратор может взаимодействовать с ним, по нажатию на текст
всплывает модальное окно с одним полем textarea, переносы в данном поле работают и конвертируются в `<br />`, вся
стилизация текста должна задаваться через css. <br />
Вы можете настраивать в каком теге выводить текст и передавать ему классы и другие атрибуты.
Вид текста для администратора отличается и может быть дополнен атрибутами, которые выводят только для админа.

Отображение изображений, которые редактируются через модальное окна, когда пользователь авторизован как администратор.
Все пользователи видят изображения, однако админу дополнительно отображается кнопка для редактирования изображений.
  
## Установка

Рекомендуемый способ установки этого расширения является использование [composer](http://getcomposer.org/download/).
Проверьте [composer.json](https://github.com/laker-ls/yii2-pencil/blob/master/composer.json) на предмет требований и зависимостей данного расширения.

Для установки запустите

```
$ php composer.phar require laker-ls/yii2-pencil "~1.2.1"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-pencil": "~1.2.1"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-pencil/blob/master/CHANGE.md) для подробной информации о версиях.

## Подключение
Исполните миграции в консоли:
```
yii migrate --migrationPath=@lakerLS/pencil/migrations
```

В конфиге приложения подключите модуль и укажите роли (обязательно) параметром `accessRoles`, для которых разрешен доступ.
Параметром `imagePath` укажите существующую папку в директории `web` приложения для сохранения изображений:
```php
'modules' => [
    'pencil' => [
        'class' => '\lakerLS\pencil\Module',
        'params' => [
            'accessRoles' => ['admin'],
            'imagePath' => 'upload/image-gallery',
        ],
    ],
]
```

В контроллере, в котором вызывается экшен с "карандашами" обязательно должно передаваться свойством экземляр текущей
категории. Данный код служит примером, в каждом случае переданные параметры будут отличаться, но свойствой `meta` не должно
менять своё имя.
```php
    public $meta;
    
    public function actionIndex()
    {
        $this->meta = Category::findOne($id);
        return $this->render('view');
    }
```

Расширение готово к работе.

## Использование виджета для текста

```php
use lakerLS\pencil\widgets\PencilText;
           
<?= PencilText::widget(['id' => 'example-id']) ?>
```

С использованием дополнительных параметров:
```php
use lakerLS\pencil\widgets\Pencil;
           
<?= PencilText::widget(['id' => 'example-id', 'tag' => 'h2', 'options' => ['class' => 'my-class']]) ?>
```

`id` - (string) обязательный параметр, для удобства, id указывать строкой. Необходимы уникальные имена в пределах одной страницы.
Повторное использование имен на других страницах не вызовет конфликта.

`tag` - (string) имя тега, в котором будет содержимое.

`options` - (array) параметры тега, которые видны как всем пользователям, так и администратору.

`optionsAdmin` - (array) параметры тега, которые видет только администратор. С помощью классов и стилей задается такой стиль текста,
что бы было понятно, что он интерактивен (можно редактировать). По умолчанию синее подчеркивание.

`textIsEmpty` - (string) текст который виден админу если содержимое пусто или не существует,
для возможности редактирования, т.к. текст единственный способ вызвать модальное окно администратору.

## Использование виджета для изображений

Используйте `Image::widget()` для отображения кнопки редактирования. <br />
Используйте `Image::arrayImg()` для получения массива с изображениями для последующего отображения.

```php
use lakerLS\pencil\widgets\PencilImage;
 
 foreach (Image::arrayImg(['group' => 'Новости']) as $image) {
    echo Html::img($image->src, ['alt' => $image->alt]);
 }
 
 echo Image::widget(['group' => 'Новости']);
```

group - единственный и обязательный параметр, который необходимо передавать для создания альбома изображений.
Может использоваться кириллица.

## Лицензия

**yii2-pencil** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.
