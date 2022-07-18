<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    /* 
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('x-api-key');
        //dd($request->getPathInfo());
        //return true;
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('x-api-key');

        if (null === $apiToken)
        {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }
        
        return new SelfValidatingPassport(
            new UserBadge($apiToken, function($apiToken) {
                $user = $this->userRepository->findByApiToken($apiToken);
                //dd($user->getCallsign());
                if (!$user)
                {
                    throw new UserNotFoundException();
                }
                //

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];
        
        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
