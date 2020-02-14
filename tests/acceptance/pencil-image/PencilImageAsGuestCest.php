<?php

namespace lakerLS\pencil\tests\acceptance\pencilImage;

use AcceptanceTester;

class PencilImageAsGuestCest
{
    public function accessDeniedActions(AcceptanceTester $I)
    {
        $I->wantTo('Проверка ограничения прав доступа для работы с изображениями.');

        $I->amOnPage('/pencil/image/index');
        $I->see('403', '.site-error');

        $I->amOnPage('/pencil/image/create-update');
        $I->see('403', '.site-error');

        $I->amOnPage('/pencil/image/delete');
        $I->see('403', '.site-error');

        $I->amOnPage('/pencil/image/delete-all');
        $I->see('403', '.site-error');
    }

    public function displayImages(AcceptanceTester $I)
    {
        $I->wantTo('Отображение изображений у пользователя без прав на редактирование');

        $I->dontSeeElement('[data-modal=pencil-image]');
    }
}