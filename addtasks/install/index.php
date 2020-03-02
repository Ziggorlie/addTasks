<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class addtasks extends CModule {

    public function __construct() {
        if (is_file(__DIR__.'/version.php')){
            include_once(__DIR__.'/version.php');
            $this->MODULE_ID           = get_class($this);
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
            $this->MODULE_NAME         = Loc::getMessage('ADDTASKS_NAME');
            $this->MODULE_DESCRIPTION  = Loc::getMessage('ADDTASKS_DESCRIPTION');
        } else {
            CAdminMessage::ShowMessage(
                Loc::getMessage('ADDTASKS_FILE_NOT_FOUND').' version.php'
            );
        }
    }
    
    public function DoInstall() {

        global $APPLICATION;

        // мы используем функционал нового ядра D7 — поддерживает ли его система?
        if (CheckVersion(ModuleManager::getVersion('main'), '14.00.00')) {
            // копируем файлы, необходимые для работы модуля
            $this->InstallFiles();
            // создаем таблицы БД, необходимые для работы модуля
            $this->InstallDB();
            // регистрируем модуль в системе
            ModuleManager::registerModule($this->MODULE_ID);
            // регистрируем обработчики событий
            $this->InstallEvents();
        } else {
            CAdminMessage::ShowMessage(
                Loc::getMessage('ADDTASKS_INSTALL_ERROR')
            );
            return;
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('ADDTASKS_INSTALL_TITLE').' «'.Loc::getMessage('ADDTASKS_NAME').'»',
            __DIR__.'/step.php'
        );
    }
    
    public function InstallFiles() {
        return;
    }
    
    public function InstallDB() {
        return;
    }

    public function InstallEvents() {

		\CAgent::AddAgent("\addtasks\Main::GetTasks();",$this->MODULE_ID,"N",3600,"","Y","",30); //поставим пока раз в час
    }

    public function DoUninstall() {

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('ADDTASKS_UNINSTALL_TITLE').' «'.Loc::getMessage('ADDTASKS_NAME').'»',
            __DIR__.'/unstep.php'
        );

    }

    public function UnInstallFiles() {
        // удаляем настройки нашего модуля
        Option::delete($this->MODULE_ID);
    }
    
    public function UnInstallDB() {
        return;
    }
    
    public function UnInstallEvents(){
        // удаляем наш обработчик события
      	\CAgent::RemoveModuleAgents($this->MODULE_ID);
    }

}