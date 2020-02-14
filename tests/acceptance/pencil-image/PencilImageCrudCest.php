<?php

namespace lakerLS\pencil\tests\acceptance\pencilImage;

use AcceptanceTester;

class PencilImageCrudCest
{
    private const WAIT = 0.5;
    private const WAIT_LONG = 1;
    private const MODAL = '#modal-pencil-image ';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/');
        $I->prepareImages(['koala.jpg', 'hydrangea.jpg']);
        $I->openModalPencilImage();
    }

    public function addMoreImages(AcceptanceTester $I)
    {
        $I->wantTo('Загрузка изображений');

        $I->attachFile('Image[full][]', 'images\medusa.jpg');
        $I->see('medusa.jpg', self::MODAL . '.cart.success p');
        $I->click(self::MODAL . '[type=submit]');
        $I->wait(self::WAIT_LONG);
        $I->dontSeeElement(self::MODAL);
        $I->seeElement('img[alt=koala]');
        $I->seeElement('img[alt=hydrangea]');
        $I->seeElement('img[alt=medusa]');

        $I->openModalPencilImage();
        $I->amDeletingAllImages();
    }

    public function deleteOneImage(AcceptanceTester $I)
    {
        $I->wantTo('Удаление одного изображения');

        $I->click('.cart:last-child .delete a');
        $I->wait(self::WAIT);
        $I->acceptPopup();
        $I->wait(self::WAIT);
        $I->dontSee('hydrangea', self::MODAL . '.cart p');
        $I->click(self::MODAL . '.close');
        $I->wait(self::WAIT);
        $I->dontSeeElement(self::MODAL);
        $I->dontSeeElement('img[alt=hydrangea]');

        $I->openModalPencilImage();
        $I->amDeletingAllImages();
    }

    public function deleteAllImages(AcceptanceTester $I)
    {
        $I->wantTo('Удаление всех изображений');

        $I->see('koala.jpg', self::MODAL . '.cart p');
        $I->see('hydrangea.jpg', self::MODAL . '.cart p');
        $I->click(self::MODAL . '.delete-all');
        $I->wait(self::WAIT);
        $I->acceptPopup();
        $I->wait(self::WAIT);
        $I->dontSeeElement(self::MODAL . '.cart');
        $I->click(self::MODAL . '.close');
        $I->wait(self::WAIT);
        $I->dontSeeElement(self::MODAL . 'img[alt=koala]');
        $I->dontSeeElement(self::MODAL . 'img[alt=hydrangea]');
    }
}