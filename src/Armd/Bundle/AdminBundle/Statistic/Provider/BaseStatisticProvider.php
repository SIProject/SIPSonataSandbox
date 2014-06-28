<?php

/**
 * Базовый провайдер для получения статистики
 */
namespace Armd\Bundle\AdminBundle\Statistic\Provider;

use Armd\Bundle\AdminBundle\Statistic\Extra\OAuth2;

class BaseStatisticProvider
{
    /**
     * ID приложения
     * 
     * @var string
     */
    protected $appId;
    
    /**
     * Пароль приложения
     * 
     * @var string
     */
    protected $appSecret;
    
    /**
     * Логин пользователя
     * 
     * @var string
     */
    protected $clientLogin;
    
    /**
     * Пароль пользователя
     * 
     * @var string
     */
    protected $clientPassword;
    
    /**
     * URL авторизации
     * 
     * @var string
     */
    protected $authUrl;
    
    /**
     * URL получения токена
     * 
     * @var string
     */
    protected $tokenUrl;
    
    /**
     * URL получения данных
     * 
     * @var string
     */
    protected $dataUrl;
    
    /**
     * Объект OAuth клиента
     * 
     * @var Armd\Bundle\AdminBundle\Statistic\Extra\OAuth2\Client
     */
    protected $oauthClient;
    
    /**
     * Токен
     * 
     * @var string
     */
    protected $accessToken;
    
    /**
     * Конструктор
     * 
     * @param $appId string
     * @param $appSecret string
     * @param $clientLogin string
     * @param $clientPassword string
     */
    public function __construct($appId, $appSecret, $clientLogin='', $clientPassword='')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->clientLogin = $clientLogin;
        $this->clientPassword = $clientPassword;

        $this->oauthClient = new OAuth2\Client($this->appId, $this->appSecret);
    }
    
    /**
     * Получить токен
     * 
     * @param $params array
     * @param $grantType string
     */
    public function getToken($params = array(), $grantType = 'password')
    {
        $token = false;

        if ($grantType == 'password') {
            $params['username'] = $this->clientLogin;
            $params['password'] = $this->clientPassword;
        }

        $response = $this->oauthClient->getAccessToken($this->tokenUrl, $grantType, $params);

        if ($response['code'] == 200) {
            $this->accessToken = $response['result']['access_token'];
            $this->oauthClient->setAccessToken($this->accessToken);
        }
        
        return $token;
    }
    
    /**
     * Получить статистику
     * 
     * @param $params array
     */
    public function getStatistic($params = array())
    {
        return $this->getData('');
    }
    
    /**
     * Получить данные
     * 
     * @param $path string
     * @param $params array
     */
    protected function getData($path, $params = array())
    {
        $params['oauth_token'] = $this->accessToken;
        $response = $this->oauthClient->fetch($this->dataUrl .$path, $params);
        return $response['result'];
    }
}