<?php

namespace Kanboard\Plugin\Line;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:config:integrations', 'line:config/integration');
        $this->template->hook->attach('template:project:integrations', 'line:project/integration');
        $this->template->hook->attach('template:user:integrations', 'line:user/integration');

        $this->userNotificationTypeModel->setType('line', t('Line'), '\Kanboard\Plugin\Line\Notification\Line');
        $this->projectNotificationTypeModel->setType('line', t('Line'), '\Kanboard\Plugin\Line\Notification\Line');
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'Line Chat Notification';
    }

    public function getPluginDescription()
    {
        return t('This plugin will send notification via Line Notify based on access token that you have made. This plugin will not send notifications when creating a new task.');
    }

    public function getPluginAuthor()
    {
        return 'Shelgi Prasetyo';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/shelgiprasetyo/Line.git';
    }
}

