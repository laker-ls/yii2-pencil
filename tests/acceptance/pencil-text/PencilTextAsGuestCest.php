<?php

namespace lakerLS\pencil\tests\acceptance\pencilText;

use AcceptanceTester;

class PencilTextAsGuestCest
{
    public function accessDeniedTextActions(AcceptanceTester $I)
    {
        $I->wantTo('Проверка ограничения прав доступа для работы с изображениями.');

        $I->amOnPage('/pencil/text/index');
        $I->see('403', '.site-error');

        $I->amOnPage('/pencil/text/create-update');
        $I->see('403', '.site-error');
    }

    public function displayText(AcceptanceTester $I)
    {
        $I->wantTo('Отображение текста у пользователя без прав на редактирование');

        $I->dontSeeElement('[data-modal=pencil-text]');
    }
}