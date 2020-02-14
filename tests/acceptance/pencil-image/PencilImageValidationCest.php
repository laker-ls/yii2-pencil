<?php

namespace lakerLS\pencil\tests\acceptance\pencilImage;

use AcceptanceTester;

class PencilImageValidationCest
{
    private const WAIT = 0.5;
    private const WAIT_LONG = 1;
    private const MODAL = '#modal-pencil-image ';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/');
        $I->prepareImages(['koala.jpg', 'medusa.jpg']);
        $I->openModalPencilImage();
    }

    public function _after(AcceptanceTester $I)
    {
        $I->amDeletingAllImages();
    }

    public function imageMatch(AcceptanceTester $I)
    {
        $I->wantTo('Проверка клиентской валидации при совпадении имен');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
    }

    public function imageNotSelect(AcceptanceTester $I)
    {
        $I->wantTo('Ошибка совпадения имен исчезает, если отменить выбор изображений');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
        $I->clearField('Image[full][]');
        $I->dontSeeValidationError(true);
    }

    public function imageReplaceOnNotMatch(AcceptanceTester $I)
    {
        $I->wantTo('Ошибка совпадения имен исчезает, если выбрать другое изображение с уникальным именем');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
        $I->clearField('Image[full][]');
        $I->attachFile('Image[full][]', 'images\hydrangea.jpg');
        $I->wait(self::WAIT);
        $I->dontSeeValidationError();
        $I->seeElement(self::MODAL . '.cart.success');
    }

    public function imageMatchDeleteOne(AcceptanceTester $I)
    {
        $I->wantTo('Ошибка совпадения имен исчезает, если удалить существующее изображение, которое не имеет уникальное имя');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
        $I->click(self::MODAL . '.cart:first-child .delete a');
        $I->acceptPopup();
        $I->wait(self::WAIT);
        $I->dontSeeValidationError();
        $I->seeElement(self::MODAL . '.cart.success');
    }

    public function imageNotMatchDeleteOne(AcceptanceTester $I)
    {
        $I->wantTo('Ошибка совпадения имен не исчезает, если удалить изображени, которое имеет уникальное имя');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
        $I->click(self::MODAL . '.cart:nth-child(2) .delete a');
        $I->acceptPopup();
        $I->wait(self::WAIT);
        $I->seeValidationError();
        $I->seeElement(self::MODAL . '.cart.error');
    }

    public function imageDeleteAll(AcceptanceTester $I)
    {
        $I->wantTo('Ошибка совпадения имен исчезает, если удалить все изображения');

        $I->attachFile('Image[full][]', 'images\koala.jpg');
        $I->seeValidationError();
        $I->click(self::MODAL . '.delete-all');
        $I->wait(self::WAIT);
        $I->acceptPopup();
        $I->wait(self::WAIT);
        $I->dontSeeValidationError();
        $I->seeElement(self::MODAL . '.cart.success');
        $I->click(self::MODAL . '[type=submit]');
        $I->wait(self::WAIT_LONG);
        $I->openModalPencilImage();
    }
}