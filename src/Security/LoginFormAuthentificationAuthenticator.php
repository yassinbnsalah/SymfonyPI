<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthentificationAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $Email = $request->request->get('Email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $Email);

        return new Passport(
            new UserBadge($Email),
            new PasswordCredentials($request->request->get('Password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();

        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_admin'));
        }elseif(in_array('ROLE_CLIENT',$user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_client'));
        }elseif(in_array('ROLE_MEDCIN',$user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_medcin'));
        }elseif(in_array('ROLE_COACH',$user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_coach'));
        }elseif(in_array('ROLE_PHARMACIEN',$user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_pharmacien'));
        }

        // For example:
         return new RedirectResponse($this->urlGenerator->generate('app_client'));
    
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
