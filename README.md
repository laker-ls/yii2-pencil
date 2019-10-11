<h1 align="center">
    yii2-pencil
</h1>


[![Stable Version](https://poser.pugx.org/laker-ls/yii2-pencil/v/stable)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![Unstable Version](https://poser.pugx.org/laker-ls/yii2-pencil/v/unstable)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![License](https://poser.pugx.org/laker-ls/yii2-pencil/license)](https://packagist.org/packages/laker-ls/yii2-pencil)
[![Total Downloads](https://poser.pugx.org/laker-ls/yii2-pencil/downloads)](https://packagist.org/packages/laker-ls/yii2-pencil)

> ВНИМАНИЕ: Для работы необходим Rbac с существующей ролью. Используется bootstrap 4.

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
$ php composer.phar require laker-ls/yii2-pencil "~2.0.2"
```

или добавьте в `composer.json` в раздел `require` следующую строку

```
"laker-ls/yii2-pencil": "~2.0.2"
```

> Смотрите [список изменений](https://github.com/laker-ls/yii2-pencil/blob/master/CHANGE.md) для подробной информации о версиях.

## Подключение
Выполните миграции в консоли:
```
yii migrate --migrationPath=@lakerLS/pencil/migrations
```

В конфиге приложения подключите модуль и укажите роли параметром `accessRoles`, которым разрешено 
редактирование. Параметром `imagePath` передайте пути к папкам (оригинал и миниатюра), в которых будут храниться 
изображения:
```php
'modules' => [
    'pencil' => [
        'class' => '\lakerLS\pencil\Module',
        'params' => [
            'accessRoles' => ['admin'],
            'imagePath' => [
                'full' => 'upload/image-gallery/full',
                'mini' => 'upload/image-gallery/mini',
            ],
        ],
    ],
]
```

В контроллере, в котором вызывается экшен с "карандашами" обязательно должно передаваться `id` текущей
категории. Данный код служит примером, в каждом случае переданные параметры будут отличаться, но свойствой `categoryId` не должно
менять своё имя.
```php
    public $categoryId;
    
    public function actionIndex($category)
    {
        // Где $category объект текущей категории.
        $this->categoryId = $category->id;
        
        // Если страница статическая, то можем задать `id` явно, но данный способ не является хорошей практикой.
        $this->categoryId = 1;
        
        return $this->render('view');
    }
```

Расширение готово к работе.

## Использование виджета для текста

```php
use lakerLS\pencil\widgets\PencilText;
           
<?= PencilText::widget(['id' => 'example-id']) ?>
```

Использование виджета в layout:
```php
use lakerLS\pencil\widgets\PencilText;
           
<?= PencilText::widget(['id' => 'example-id', 'nonUnique' => 'this-name-layout']) ?>
```

С использованием дополнительных параметров:
```php
use lakerLS\pencil\widgets\Pencil;
           
<?= PencilText::widget(['id' => 'example-id', 'tag' => 'h2', 'options' => ['class' => 'my-class']]) ?>
```

`id` (string) - обязательный параметр, для удобства, id указывать строкой. Необходимы уникальные имена в пределах одной страницы.
Повторное использование имен на других страницах не вызовет конфликта.

`tag` (string) - имя тега, в котором будет содержимое.

`options` (array) - параметры тега, которые видны как всем пользователям, так и администратору.

`nonUnique` (string) - необязательный параметр. Для отображения одного и того же текста на нескольких страницах необходимо передать
строку, которая будет использоваться вместо `id`.

`optionsAdmin` (array) - параметры тега, которые видет только администратор. С помощью классов и стилей задается такой стиль текста,
что бы было понятно, что он интерактивен (можно редактировать). По умолчанию синее подчеркивание.

`textIsEmpty` (string) - текст который виден админу если содержимое пусто или не существует,
для возможности редактирования, т.к. текст единственный способ вызвать модальное окно администратору.

## Использование виджета для изображений

Между `begin` и `end` передаем шаблон для каждого отдельного изображения. В этом шаблоне обязательно должен быть
один пустой тег `<img>`, который будет заменен на реальное изображение.

Для использования виджета в layout'е обязательно передать параметром `layout` имя layout'a.

Передавая `true` или `false` параметру `small` выбираем вид кнопки для редактирования (видна только администратору).

> ВАЖНО: используя значение `true` для параметра `small` обязательно указывайте родительскому элементу в котором 
расположено изображение стиль `position: relative`.

```php
<?php
use lakerLS\pencil\widgets\PencilImage;
use yii\bootstrap4\Html;
?>
 <div class="example-container" style="position: relative">
     <?php 
     $pencilImg = PencilImage::begin([
        'group' => 'our-characteristics-img-main', 
        'small' => true, 
        'thumbnail' => [
            'width' => 634, 
            'height' => 466
        ]
     ]);
     ?>
     
         <a href="<?= $pencilImg->urlFull() ?>">
             <img src="<?= $pencilImg->urlMini() ?>" alt="<?= $pencilImg->alt() ?>">
         </a>
         
     <?php PencilImage::end() ?>
 </div>
```
`group` (string) - обязательный параметр, который необходимо передавать для создания альбома изображений.
Может использоваться кириллица.

`thumbnail` (array) - обязательный параметр, в котором передаем ширину и высоту миниатюры.

`small` (boolean) - необязательный параметр. По умолчанию `false`.<br />
Если значение `false`, отображается большая кнопка для редактирования. 
Подходит для создания/редактирования альбомов. <br />
Если значение `true`, кнопка имеет маленький размер и позицию absolute. Ни как не влияет на верстку, отображается в нижнем
правом углу.

`nonUnique` (string) - необязательный параметр. Для отображения одних и тех же изображений на нескольких страницах необходимо передать
строку, которая будет использоваться вместо `id`.

## Лицензия

**yii2-pencil** выпущено по лицензии BSD-3-Clause. Ознакомиться можно в файле `LICENSE.md`.
