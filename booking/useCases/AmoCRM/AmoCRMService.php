<?php
namespace booking\useCases\AmoCRM;

use AmoCRM\Client\AmoCRMApiClient;
use booking\entities\AmoCRM\Credential;
use booking\entities\Order\Order;
use booking\forms\AmoCRM\LeadFormsInterface;
use Exception;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AmoCRMService
{
    public Credential $credential;
    public AmoCRMApiClient $apiClient;

    public string $logFile;
    private bool $debug;

    public function __construct(bool $debug=false)
    {
        $this->debug=$debug;
        $this->logFile=$_SERVER['DOCUMENT_ROOT'] . '/amoCRM.log';
    }

    /**
     * Простая авторизция. Подходит для приватных интеграций в рамках своего аккаунта
     * @return bool
     */
    public function simpleAuthorization():bool
    {
        $this->log(__FUNCTION__);
        $link = 'https://' . $this->credential->domain . '/oauth2/access_token'; //Формируем URL для запроса


        /** Соберем данные для запроса */
        $data = [
            'client_id' => $this->credential->client_id,
            'client_secret' => $this->credential->client_secret,
            'grant_type' => 'authorization_code',
            'code' => $this->credential->token,
            'redirect_uri' => $this->credential->redirect_uri,
        ];

        /**
         * Нам необходимо инициировать запрос к серверу.
         * Воспользуемся библиотекой cURL (поставляется в составе PHP).
         * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
         */
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
        if ($code < 200 || $code > 204) {
            $response=json_decode($out, true);
            dump($response);exit;
//            die(isset($errors[$code]) ? $errors[$code].'. '.$response['hint'].'. '.$response['detail']. PHP_EOL : 'Undefined error');
            throw new Exception(isset($errors[$code]) ? $errors[$code].'. '.$response['hint'].'. '.$response['detail']. PHP_EOL : 'Undefined error');
        }
//
//        try
//        {
//            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
//            if ($code < 200 || $code > 204) {
//                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
//            }
//        }
//        catch(\Exception $e)
//        {
//            dump($e);
//            die('Авторизация не выполнена. Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode() . PHP_EOL);
//        }

        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */
        $response = json_decode($out, true);
        $this->saveToken($response['access_token'],$response['refresh_token'],(time()+$response['expires_in']));

        return true;
    }

    public function longAuthorization():bool
    {
        $this->log(__FUNCTION__);
        $link = 'https://' . $this->credential->domain . '/oauth2/access_token'; //Формируем URL для запроса
        /** Формируем заголовки */
        $headers = [
            'Authorization: Bearer ' . $this->credential->token,
        ];
        /**
         * Нам необходимо инициировать запрос к серверу.
         * Воспользуемся библиотекой cURL (поставляется в составе PHP).
         * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
         */
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];
        try
        {
            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
            if ($code < 200 || $code > 204) {
                $response=json_decode($out, true);
                dump($response);exit;
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        } catch(\Exception $e)
        {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }
        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */
        $response = json_decode($out, true);
        dump($response);exit;
        $this->saveToken($response['access_token'],$response['refresh_token'],(time()+$response['expires_in']));
        return true;
    }
    public function simpleRefreshToken():bool
    {
        $this->log(__FUNCTION__);
        $this->log('Запуск simpleRefreshToken0');
        $this->log('Запуск simpleRefreshToken',false);
        $link = 'https://' . $this->credential->domain . '/oauth2/access_token'; //Формируем URL для запроса


        /** Соберем данные для запроса */
        $data = [
            'client_id' => $this->credential->client_id,
            'client_secret' => $this->credential->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->credential->refresh_token,
            'redirect_uri' => $this->credential->redirect_uri,
        ];

        /**
         * Нам необходимо инициировать запрос к серверу.
         * Воспользуемся библиотекой cURL (поставляется в составе PHP).
         * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
         */
        $curl = curl_init(); //Сохраняем дескриптор сеанса cURL
        /** Устанавливаем необходимые опции для сеанса cURL  */
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        /** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = [
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
        ];

        if ($code==401) {
            return $this->simpleAuthorization();
//            return $this->longAuthorization();
        }

        if ($code < 200 || $code > 204) {
            $response=json_decode($out, true);
            die(isset($errors[$code]) ? $errors[$code].'. '.$response['hint'].'. '.$response['detail']. PHP_EOL : 'Undefined error');
        }


//
//        try
//        {
//            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
//            if ($code < 200 || $code > 204) {
//                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
//            }
//        }
//        catch(\Exception $e)
//        {
//            dump($e);
//            die('Авторизация не выполнена. Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode() . PHP_EOL);
//        }

        /**
         * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         * нам придётся перевести ответ в формат, понятный PHP
         */
        $response = json_decode($out, true);
        $this->saveToken($response['access_token'],$response['refresh_token'],(time()+$response['expires_in']));

        return true;
    }

    public function log(string $msg,bool $isError=true)
    {
        $messageWithDate=date('Y-m-d H:i:s') . ' '.$msg;
        if ($isError) {
            file_put_contents($this->logFile, $messageWithDate . PHP_EOL, FILE_APPEND);
        }
        if ($this->debug) {
            echo $messageWithDate."\n";
        }
    }

    public function saveToken($access_token,$refresh_token,$expires_in,$domain=null)
    {
        $this->log(__FUNCTION__);
        $this->log('Запуск saveToken',false);
        if ($domain) {
            $this->credential->domain=$domain;
        }
        $this->credential->token=$access_token;
        $this->credential->refresh_token=$refresh_token;
        $this->credential->expires=$expires_in;
        $this->credential->save();
    }

    public function setCredential(Credential $credential)
    {
        $this->log(__FUNCTION__);
//        dump(date('Y-m-d h:i:s',$credential->expires));
        $this->credential=$credential;
        if (($this->credential->expires<=time())) {
//            dump('tit');
            $this->simpleRefreshToken();
        }
        $this->apiClient = new AmoCRMApiClient($credential->client_id, $credential->client_secret, $credential->domain);
        if ($accessToken = $this->getToken()) {
//            dump($accessToken);
//            dump($accessToken->getTimeNow());
//            dump($accessToken->getExpires());

            $this->apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
                ->onAccessTokenRefresh(
                    function (AccessTokenInterface $accessToken, string $baseDomain) {
                        $this->log('Запуск onAccessTokenRefresh',false);
                        $this->saveToken( $accessToken->getToken(),$accessToken->getRefreshToken(),$accessToken->getExpires()+60,$baseDomain);
                    }
                );
            $this->saveToken( $accessToken->getToken(),$accessToken->getRefreshToken(),$accessToken->getExpires()+60,$accessToken->getValues()['baseDomain']);
        }
    }

    public function getToken(): ?AccessToken
    {
        $this->log(__FUNCTION__);
        if  (
            isset($this->credential->token) &&
            isset($this->credential->refresh_token) &&
            isset($this->credential->expires) &&
            isset($this->credential->domain)
        ) {
            return new AccessToken([
                'access_token' => $this->credential->token,
                'refresh_token' => $this->credential->refresh_token,
                'expires' => $this->credential->expires,
                'baseDomain' => $this->credential->domain,
            ]);
        } else {
            return null;
        }

    }

    /**
     * Создание лида.
     * Т.к. одновременно создать лид и нвоый контакт не получается, поэтому сначала создается контакт, потом сам лид
     * @param LeadFormsInterface $lead
     * @throws \AmoCRM\Exceptions\AmoCRMApiException
     * @throws \AmoCRM\Exceptions\AmoCRMMissedTokenException
     * @throws \AmoCRM\Exceptions\AmoCRMoAuthApiException
     */
    public function addLead(LeadFormsInterface $lead):int
    {
        $this->log(__FUNCTION__);
        $contact=$this->apiClient->contacts()->addOne($lead->getContact());
        $lead->setContactId($contact->getId());
        $result=$this->apiClient->leads()->addOne($lead->getLead());

        $this->apiClient->notes('leads')->add($lead->getNotes());

        return $result->getId();
    }

}