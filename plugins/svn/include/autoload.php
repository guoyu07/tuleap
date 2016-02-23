<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
function autoload37481ff8658b5a77c099452eb10b5b5a($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = array(
            'svnplugin' => '/svnPlugin.class.php',
            'svnplugindescriptor' => '/SvnPluginDescriptor.class.php',
            'svnplugininfo' => '/SvnPluginInfo.class.php',
            'tuleap\\svn\\admin\\accesscontrol\\accesscontrolcontroller' => '/Svn/Admin/AccessControl/AccessControlController.php',
            'tuleap\\svn\\admin\\accesscontrol\\accesscontrolpresenter' => '/Svn/Admin/AccessControl/AccessControlPresenter.php',
            'tuleap\\svn\\admin\\accesscontrol\\accessfilehistory' => '/Svn/Admin/AccessControl/AccessFileHistory.php',
            'tuleap\\svn\\admin\\accesscontrol\\accessfilehistorydao' => '/Svn/Admin/AccessControl/AccessFileHistoryDao.php',
            'tuleap\\svn\\admin\\accesscontrol\\accessfilehistorymanager' => '/Svn/Admin/AccessControl/AccessFileHistoryManager.php',
            'tuleap\\svn\\admin\\accesscontrol\\cannotcreateaccessfilehistoryexception' => '/Svn/Admin/AccessControl/CannotCreateAccessFileHistoryException.php',
            'tuleap\\svn\\admin\\accesscontrol\\nullaccessfilehistory' => '/Svn/Admin/AccessControl/NullAccessFileHistory.php',
            'tuleap\\svn\\admin\\cannotcreatemailheaderexception' => '/Svn/Admin/CannotCreateMailHeaderException.class.php',
            'tuleap\\svn\\admin\\cannotdeletemailnotificationexception' => '/Svn/Admin/CannotDeleteMailNotificationException.php',
            'tuleap\\svn\\admin\\mailheader' => '/Svn/Admin/MailHeader.class.php',
            'tuleap\\svn\\admin\\mailheaderdao' => '/Svn/Admin/MailHeaderDao.class.php',
            'tuleap\\svn\\admin\\mailheadermanager' => '/Svn/Admin/MailHeaderManager.class.php',
            'tuleap\\svn\\admin\\mailnotification' => '/Svn/Admin/MailNotification.class.php',
            'tuleap\\svn\\admin\\mailnotificationcontroller' => '/Svn/Admin/MailNotificationController.class.php',
            'tuleap\\svn\\admin\\mailnotificationdao' => '/Svn/Admin/MailNotificationDao.class.php',
            'tuleap\\svn\\admin\\mailnotificationmanager' => '/Svn/Admin/MailNotificationManager.class.php',
            'tuleap\\svn\\admin\\mailnotificationpresenter' => '/Svn/Admin/MailNotificationPresenter.class.php',
            'tuleap\\svn\\admin\\mailreceivedfromuserextractor' => '/Svn/Admin/MailReceivedFromUserExtractor.php',
            'tuleap\\svn\\admin\\sectionspresenter' => '/Svn/Admin/SectionsPresenter.php',
            'tuleap\\svn\\dao' => '/Svn/Dao.class.php',
            'tuleap\\svn\\eventrepository\\systemevent_svn_create_repository' => '/events/SystemEvent_SVN_CREATE_REPOSITORY.class.php',
            'tuleap\\svn\\explorer\\explorercontroller' => '/Svn/Explorer/ExplorerController.class.php',
            'tuleap\\svn\\explorer\\explorerpresenter' => '/Svn/Explorer/ExplorerPresenter.class.php',
            'tuleap\\svn\\explorer\\repositorydisplaycontroller' => '/Svn/Explorer/RepositoryDisplayController.class.php',
            'tuleap\\svn\\explorer\\repositorydisplaypresenter' => '/Svn/Explorer/RepositoryDisplayPresenter.class.php',
            'tuleap\\svn\\repository\\cannotcreaterepositoryexception' => '/Svn/Repository/CannotCreateRepositoryException.class.php',
            'tuleap\\svn\\repository\\cannotfindrepositoryexception' => '/Svn/Repository/CannotFindRepositoryException.class.php',
            'tuleap\\svn\\repository\\repository' => '/Svn/Repository/Repository.class.php',
            'tuleap\\svn\\repository\\repositorymanager' => '/Svn/Repository/RepositoryManager.class.php',
            'tuleap\\svn\\repository\\rulename' => '/Svn/Repository/RuleName.class.php',
            'tuleap\\svn\\servicesvn' => '/Svn/ServiceSvn.class.php',
            'tuleap\\svn\\svnrouter' => '/Svn/SvnRouter.class.php',
            'tuleap\\svn\\usercannotadministraterepositoryexception' => '/Svn/UserCannotAdministrateRepositoryException.php',
            'tuleap\\svn\\viewvcproxy\\viewvcproxy' => '/Svn/ViewVCProxy/ViewVCProxy.class.php'
        );
    }
    $cn = strtolower($class);
    if (isset($classes[$cn])) {
        require dirname(__FILE__) . $classes[$cn];
    }
}
spl_autoload_register('autoload37481ff8658b5a77c099452eb10b5b5a');
// @codeCoverageIgnoreEnd
