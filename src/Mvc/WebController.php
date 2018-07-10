<?php

declare(strict_types=1);

/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shadon\Mvc;

/**
 * Class WebController.
 */
class WebController extends Controller
{
    /**
     * @var \Eelly\DTO\UserDTO
     */
    protected $user;

    public function onConstruct(): void
    {

    }
    
    public function isLogin(): void
    {
        // add cache service
        $this->di->setShared('cache', function () {
            $config = $this->getConfig()->cache->toArray();
            $frontend = $this->get($config['frontend'], [$config['options'][$config['frontend']]]);

            return $this->get($config['backend'], [$frontend, $config['options'][$config['backend']]]);
        });

        $options = $this->di->getConfig()->oauth2Client->eelly->toArray();
        $eellyClient = \Eelly\SDK\EellyClient::initialize($options, $this->di->getCache());
        
        /* @var \League\OAuth2\Client\Token\AccessToken $accessToken */
        $accessToken = $this->session->get('accessToken');

        if (isset($accessToken) && $accessToken->getRefreshToken()) {
            
            // token 过期
            if ($accessToken->hasExpired()) {
                try {
                    $accessToken = $eellyClient->getSdkClient()->getProvider()->getAccessToken(
                        'refresh_token',
                        ['refresh_token' => $accessToken->getRefreshToken()]
                    );
                } catch (IdentityProviderException $e) {
                    $this->session->destroy();

                    return;
                }
                $this->session->set('accessToken', $accessToken);
            }
            
            $eellyClient->getSdkClient()->setAccessToken($accessToken);
            
            $user = new User();

            try {
                $this->view->user = $this->user = $user->getInfo();
            } catch (LogicException $e) {
                $this->session->set('accessToken', null);
                $this->response->redirect('/login/account')->send();
            }
        } else{
            $this->response->redirect('/login/account')->send();
        }
    }
    
    /**
     * display other template.
     *
     * @param string $controller template name
     * @param string $action     action name
     */
    public function sendTemplateRender($controller, $action): void
    {
        $this->view->render(
            $controller,
            $action
        );
        $this->response->setContent($this->view->getContent());
    }
}
