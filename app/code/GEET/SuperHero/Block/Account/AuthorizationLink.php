<?php
namespace GEET\SuperHero\Block\Account;
class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{
    public function getLabel()
    {
        return $this->isLoggedIn() ? __('Sign Out') : __('Log In');
    }
}

