<?php
namespace booking\forms\AmoCRM;
use booking\entities\AmoCRM\Credential;

/**
 * @property string $domain
 * @property string $token
 * @property string $refresh_token
 * @property string $expires
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 **/
class CredentialForm extends Credential
{
    public ?Credential $_entity=null;
    public function __construct(Credential $entity=null, $config = [])
    {
        parent::__construct($config);
        if ($entity) {
            $this->client_id=$entity->client_id;
            $this->domain=$entity->domain;
            $this->token=$entity->token;
            $this->refresh_token=$entity->refresh_token;
            $this->expires=$entity->expires;
            $this->client_secret=$entity->client_secret;
            $this->redirect_uri=$entity->redirect_uri;
            $this->_entity = $entity;
        }
    }
    public function rules(): array
    {
        return [
            [
                [
                    'domain',
                    'token',
                    'refresh_token',
                    'client_id',
                    'client_secret',
                    'redirect_uri',
                ], 'string'],
            [['expires','user_id','client_id'],'integer'],
            [['domain','client_id','client_secret','redirect_uri'], 'required'],
        ];
    }
}