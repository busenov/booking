<?php
namespace booking\entities\AmoCRM;

use booking\forms\AmoCRM\CredentialForm;
use League\OAuth2\Client\Token\AccessToken;
use yii\db\ActiveRecord;

/**
 * @property int $id
 *
 * @property string $domain
 * @property string $token
 * @property string $refresh_token
 * @property int    $expires
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 *
 */

class Credential extends ActiveRecord
{
    const MAIN_ID=1;
    public static function create(CredentialForm $form): self
    {
        $credential = new static();
        $credential->domain=$form->domain;
        $credential->token=$form->token;
        $credential->refresh_token=$form->refresh_token;
        $credential->expires=$form->expires;
        $credential->client_id=$form->client_id;
        $credential->client_secret=$form->client_secret;
        $credential->redirect_uri=$form->redirect_uri;

        return $credential;

    }
    public function edit(CredentialForm $form):void
    {
        $this->token=$form->token;
        $this->refresh_token=$form->refresh_token;
        $this->expires=$form->expires;
        $this->client_id=$form->client_id;
        $this->client_secret=$form->client_secret;
        $this->redirect_uri=$form->redirect_uri;
    }
    public function getAccessToken(): AccessToken
    {
        if  (
            isset($this->token) &&
            isset($this->refresh_token) &&
            isset($this->expires) &&
            isset($this->domain)
        ) {
            return new AccessToken([
                'access_token' => $this->token,
                'refresh_token' => $this->refresh_token,
                'expires' => $this->expires,
                'baseDomain' => $this->domain,
            ]);
        } else {
            throw new \RuntimeException("Empty token or refresh_token or expires or domain");
        }

    }
###gets


    #################################################################
    public static function tableName(): string
    {
        return '{{%amocrm_credentials}}';
    }
}