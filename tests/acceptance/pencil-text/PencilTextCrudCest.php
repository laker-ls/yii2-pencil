<?php

namespace lakerLS\pencil\tests\acceptance\pencilText;

use AcceptanceTester;

class PencilTextCrudCest
{
    private const WAIT = 0.5;
    private const MODAL = '#modal-pencil-text ';

    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->amOnPage('/');
        $I->openModalPencilText();
    }

    public function _after(AcceptanceTester $I)
    {
        $I->openModalPencilText();
        $I->fillField('Text[text]', '');
        $I->click(self::MODAL . '[type=submit]');
    }

    public function createUpdateText(AcceptanceTester $I)
    {
        $message = 'You have successfully created your Yii-powered application.';

        $I->wantTo('Сохранение строки с текстом');

        $I->fillField('Text[text]', $message);
        $I->click(self::MODAL . '[type=submit]');
        $I->wait(self::WAIT);
        $I->dontSeeElement(self::MODAL);
        $I->see($message, '.pencil-button');
    }

    public function createUpdateTextEmpty(AcceptanceTester $I)
    {
        $I->wantTo('Сохранение пустой строки');

        $I->fillField('Text[text]', '');
        $I->click(self::MODAL . '[type=submit]');
        $I->wait(self::WAIT);
        $I->dontSeeElement(self::MODAL);
        $I->see('Добавить текст', '.pencil-button');
    }
}