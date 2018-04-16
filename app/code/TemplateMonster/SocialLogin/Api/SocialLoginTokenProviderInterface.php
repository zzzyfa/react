<?php

namespace TemplateMonster\SocialLogin\Api;

interface SocialLoginTokenProviderInterface
{    
    /**
     * Get user data.
     *
     * @api
     *
     * @param string $fb_access_token
     *
     * @return string
     */
     
    public function getTokenforFacebook($fb_access_token);
    
    /**
     * Get user data.
     *
     * @api
     *
     * @param string $fb_access_token
     *
     * @return string
     */
     
    public function getTokenforGoogle($google_access_token);
}
