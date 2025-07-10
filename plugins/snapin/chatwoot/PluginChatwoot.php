<?php

require_once 'modules/admin/models/SnapinPlugin.php';

class PluginChatwoot extends SnapinPlugin
{
    public function getVariables()
    {
        $variables = array(
            lang('Plugin Name') => array(
                'type'        => 'hidden',
                'description' => '',
                'value'       => lang('Chatwoot Live Chat'),
            ),
            lang('Base URL')  => array(
                'type'        => 'text',
                'description' => lang('Enter your Chatwoot installation URL (e.g., https://app.chatwoot.com)'),
                'value'       => '',
            ),
            lang('Website Token')  => array(
                'type'        => 'text',
                'description' => lang('Enter your Chatwoot Website Token'),
                'value'       => '',
            ),
            lang('HMAC Secret')  => array(
                'type'        => 'text',
                'description' => lang('Enter your HMAC secret key for identity validation (optional)'),
                'value'       => '',
            ),
        );

        return $variables;
    }

    public function init()
    {
        $this->setDescription("This feature adds Chatwoot Live Chat to all public pages");
        $this->addMappingHook("clientarea_footer", "footer", "Footer Integration", "Adds the required javascript to the client area footer.");
    }

    public function footer()
    {
        $this->view->baseUrl = $this->getVariable('Base URL');
        $this->view->websiteToken = $this->getVariable('Website Token');
        $this->view->email = $this->user->getEmail();
        $hmacSecret = $this->getVariable('HMAC Secret');
        if (!empty($hmacSecret) && !empty($this->view->email)) {
            $this->view->identifierHash = hash_hmac("sha256", $this->user->getId(), $hmacSecret);
        }
        $this->view->name = $this->user->getFirstName() . ' ' . $this->user->getLastName();
        $this->view->userId = $this->user->getId();
        $additionalFields = [
            'company' => $this->user->getOrganization(),
            'address' => $this->user->getAddress(),
            'city' => $this->user->getCity(),
            'state' => $this->user->getState(),
            'zipcode' => $this->user->getZipCode(),
            'country' => $this->user->getCountry(),
            'phone' => $this->user->getPhone()
        ];
        $this->view->additionalFields = $additionalFields;
    }
}
