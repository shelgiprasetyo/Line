<?php

namespace Kanboard\Plugin\Line\Notification;

use Kanboard\Core\Base;
use Kanboard\Core\Notification\NotificationInterface;
use Kanboard\Model\TaskModel;

/**
 * Line Notification
 *
 * @package  notification
 * @author   Frederic Guillot
 */
class Line extends Base implements NotificationInterface
{
    /**
     * Send notification to a user
     *
     * @access public
     * @param  array     $user
     * @param  string    $eventName
     * @param  array     $eventData
     */
    public function notifyUser(array $user, $eventName, array $eventData)
    {
        
        $webhook = $this->userMetadataModel->get($user['id'], 'line_access_token', $this->configModel->get('line_access_token'));
        
        if (! empty($webhook)) {
            if ($eventName === TaskModel::EVENT_OVERDUE) {
                foreach ($eventData['tasks'] as $task) {
                    $project = $this->projectModel->getById($task['project_id']);
                    $eventData['task'] = $task;
                }
            } else {
                $project = $this->projectModel->getById($eventData['task']['project_id']);
            }

            $this->sendMessage($webhook, $project, $eventName, $eventData);
        }
    }

    /**
     * Send notification to a project
     *
     * @access public
     * @param  array     $project
     * @param  string    $eventName
     * @param  array     $eventData
     */
    public function notifyProject(array $project, $eventName, array $eventData)
    {
        $webhook = $this->projectMetadataModel->get($project['id'], 'line_access_token', $this->configModel->get('line_access_token'));

        if (! empty($webhook)) {
            $this->sendMessage($webhook, $project, $eventName, $eventData);
        }
    }

    /**
     * Get message to send
     *
     * @access public
     * @param  array     $project
     * @param  string    $eventName
     * @param  array     $eventData
     * @return array
     */
    public function getMessage(array $project, $eventName, array $eventData)
    {
        if ($this->userSession->isLogged()) {
            $author = $this->helper->user->getFullname();
            $title = $this->notificationModel->getTitleWithAuthor($author, $eventName, $eventData);
        } else {
            $title = $this->notificationModel->getTitleWithoutAuthor($eventName, $eventData);
        }
        
        $message = '*['.$project['name'].']* ';
        $message .= $title;
        $message .= ' ('.$eventData['task']['title'].')';

        if ($this->configModel->get('application_url') !== '') {
            $message .= ' - <';
            $message .= $this->helper->url->to('TaskViewController', 'show', array('task_id' => $eventData['task']['id'], 'project_id' => $project['id']), '', true);
            $message .= '|'.t('view the task on Kanboard').'>';
        }

        return array(
            'text' => $message,
            'username' => 'Kanboard',
            'icon_url' => 'https://raw.githubusercontent.com/kanboard/kanboard/master/assets/img/favicon.png',
        );
    }

    /**
     * Send message to Line
     *
     * @access protected
     * @param  string    $webhook
     * @param  string    $channel
     * @param  array     $project
     * @param  string    $eventName
     * @param  array     $eventData
     */
    protected function sendMessage($webhook, array $project, $eventName, array $eventData)
    {
        $payload = $this->getMessage($project, $eventName, $eventData);
        $message = $payload['text'];

        if ($message != "") {
            $fields = array(
                'message' => $message
            );
    
            // $this->httpClient->postJsonAsync($webhook, $payload);
            $curlUrl = 'https://notify-api.line.me/api/notify';
    
            $httpHeadersArray = Array();
            $httpHeadersArray[] = 'Authorization: Bearer ' . $webhook;
    
            //open connection
            $ch = curl_init();
    
            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $curlUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeadersArray);
    
            //execute post
            curl_exec($ch);
        }
    }
}
