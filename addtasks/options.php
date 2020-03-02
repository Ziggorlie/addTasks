<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);

Loader::includeModule($module_id);
$aTabs = array(
    array(
        'DIV'     => 'edit1',
        'TAB'     => Loc::getMessage('ADDTASKS_OPTIONS_TAB_GENERAL'),
        'TITLE'   => Loc::getMessage('ADDTASKS_OPTIONS_TAB_GENERAL'),
        'OPTIONS' => array(
            array(
                'url',                                   
                Loc::getMessage('ADDTASKS_OPTIONS_URL'),  
				'http://dev-zone-srv.ru/',                                       // значение по умолчанию 50px
                array('text', 30)
            ),
            array(
                'login',                                  
                Loc::getMessage('ADDTASKS_OPTIONS_LOGIN'), 
				'admin',                                       // значение по умолчанию 50px
                array('text', 30)
              
            ),
            array(
                'password',                                  
                Loc::getMessage('ADDTASKS_OPTIONS_PASSWORD'),
               'admin',                                       // значение по умолчанию 50px
                array('text', 30)
            )
        )
    )
);

/*
 * Создаем форму для редактирвания параметров модуля
 */
$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->Begin();
?>

<form action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?=$module_id; ?>&lang=<?= LANGUAGE_ID; ?>" method="post">
    <?= bitrix_sessid_post(); ?>
    <?php
    foreach ($aTabs as $aTab) { // цикл по вкладкам
        if ($aTab['OPTIONS']) {
            $tabControl->BeginNextTab();
            __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
        }
    }
    $tabControl->Buttons();
    ?>
    <input type="submit" name="apply"
           value="<?= Loc::GetMessage('ADDTASKS_OPTIONS_INPUT_APPLY'); ?>" class="adm-btn-save" />

</form>

<?php
$tabControl->End();

/*
 * Обрабатываем данные после отправки формы
 */
if ($request->isPost() && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) { // цикл по вкладкам
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) { // если это название секции
                continue;
            }
            if ($arOption['note']) { // если это примечание
                continue;
            }
            if ($request['apply']) {
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], is_array($optionValue) ? implode(',', $optionValue) : $optionValue);
            } 
        }
    }

    LocalRedirect($APPLICATION->GetCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);

}