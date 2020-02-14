<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    private const WAIT = 0.5;
    private const MODAL_TEXT = '#modal-pencil-text ';
    private const MODAL_IMAGE = '#modal-pencil-image ';

    /**
     * Авторизация как администратор.
     */
    public function loginAsAdmin()
    {
        $I = $this;

        $I->amOnPage('/user/login');
        $I->submitForm('#login-form', [
            'login-form[login]' => 'admin',
            'login-form[password]' => '123123',
        ]);
    }

    /**
     * Открытие модального окна с формой редактирования текста.
     */
    public function openModalPencilText()
    {
        $I = $this;

        $I->dontSeeElement(self::MODAL_TEXT);
        $I->click("[data-modal=pencil-text]");
        $I->wait(self::WAIT);
        $I->seeElement('.modal-backdrop');
        $I->seeElement(self::MODAL_TEXT);
    }

    /**
     * Элементы указывающие на ошибку валидации видны.
     */
    public function seeValidationError()
    {
        $I = $this;

        $I->seeElement(self::MODAL_IMAGE . '.cart.error');
        $I->seeElement(self::MODAL_IMAGE . '.error-label');
        $I->seeElement(self::MODAL_IMAGE . '[type=submit][disabled=disabled]');
    }

    /**
     * Элементы указывающие на ошибку валидации скрыты.
     * @param $imageEmpty boolean есть ли новые изображения для загрузки
     */
    public function dontSeeValidationError($imageEmpty = false)
    {
        $I = $this;

        $I->dontSeeElement(self::MODAL_IMAGE . '.cart.error');
        $I->dontSeeElement(self::MODAL_IMAGE . '.error-label');
        if ($imageEmpty) {
            $I->seeElement(self::MODAL_IMAGE . '[type=submit][disabled=disabled]');
        } else {
            $I->dontSeeElement(self::MODAL_IMAGE . '[type=submit][disabled=disabled]');
        }
    }

    /**
     * Подготовка изображений для тестирования.
     * @param $images array
     */
    public function prepareImages($images)
    {
        $I = $this;

        $I->openModalPencilImage();
        foreach ($images as $image) {
            $I->attachFile('Image[full][]', "images\\{$image}");
        }
        $I->click(self::MODAL_IMAGE . '[type=submit]');
        $I->wait(self::WAIT);
    }

    /**
     * Открытие модального окна с формой редактирования изображений.
     */
   public function openModalPencilImage()
   {
       $I = $this;

       $I->dontSeeElement(self::MODAL_IMAGE);
       $I->click("[data-modal=pencil-image]");
       $I->wait(self::WAIT);
       $I->seeElement('.modal-backdrop');
       $I->seeElement(self::MODAL_IMAGE);
   }

    /**
     * Удаление всех изображений.
     */
   public function amDeletingAllImages()
   {
       $I = $this;

       $I->click(self::MODAL_IMAGE . '.delete-all');
       $I->wait(self::WAIT);
       $I->acceptPopup();
       $I->wait(self::WAIT);
   }
}
