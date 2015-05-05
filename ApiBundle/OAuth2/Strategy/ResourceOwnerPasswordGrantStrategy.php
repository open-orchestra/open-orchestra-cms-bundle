<?php

namespace OpenOrchestra\ApiBundle\OAuth2\Strategy;

use JMS\Serializer\Serializer;
use OpenOrchestra\ApiBundle\Exceptions\HttpException\BadUserCredentialsHttpException;
use OpenOrchestra\ApiBundle\Facade\OAuth2\AccessTokenFacade;
use OpenOrchestra\UserBundle\Document\AccessToken;
use OpenOrchestra\UserBundle\Repository\AccessTokenRepository;
use OpenOrchestra\UserBundle\Repository\ApiClientRepository;
use OpenOrchestra\UserBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\LegacyValidator;

/**
 * Class ResourceOwnerPasswordGrantStrategy
 *
 * @deprecated use the one from base-api-bundle, will be removed in 0.2.2
 */
class ResourceOwnerPasswordGrantStrategy extends AbstractStrategy
{
    protected $apiClientRepository;
    protected $encoderFactory;
    protected $userRepository;

    /**
     * @param ApiClientRepository   $apiClientRepository
     * @param AccessTokenRepository $accessTokenRepository
     * @param UserRepository        $userRepository
     * @param EncoderFactory        $encoderFactory
     * @param Serializer            $serializer
     * @param LegacyValidator       $validator
     * @param string                $tokenExpiration
     * @param string                $tokenClass
     */
    public function __construct(
        ApiClientRepository $apiClientRepository,
        AccessTokenRepository $accessTokenRepository,
        UserRepository $userRepository,
        EncoderFactory $encoderFactory,
        Serializer $serializer,
        LegacyValidator $validator,
        $tokenExpiration,
        $tokenClass
        )
    {
        parent::__construct($apiClientRepository, $accessTokenRepository, $serializer, $validator, $tokenExpiration, $tokenClass);
        $this->encoderFactory = $encoderFactory;
        $this->userRepository = $userRepository;
    }
    /**
     * @param Request $request
     *
     * @return boolean
     */
    public function supportRequestToken(Request $request)
    {
        $clientExist = $request->getUser() && $request->getPassword();
        $oauthParams = $request->get('grant_type') === 'password' && $request->get('username') && $request->get('password');

        return $oauthParams && $clientExist;
    }

    /**
     * @param Request $request [description]
     *
     * @return Response
     */
    public function requestToken(Request $request)
    {
        $client = $this->getClient($request);
        $user   = $this->getUser($request);

        // Create/Validate AccessToken
        $tokenClass = $this->tokenClass;
        $accessToken = $tokenClass::create($user, $client);
        $accessToken->setExpiredAt(new \DateTime($this->tokenExpiration));
        if (!$accessToken->isValid($this->validator)) {
            return Response::create($this->serializer->serialize($accessToken->getViolations(), 'json'), 200, array())->prepare($request);
        }
        $this->accessTokenRepository->save($accessToken);

        $tokenFacade = new AccessTokenFacade();
        $tokenFacade->accessToken   = $accessToken->getCode();
        $tokenFacade->expiresIn     = $accessToken->getExpiredAt();

        return Response::create($this->serializer->serialize($tokenFacade, 'json'), 200, array('Content-Type' => 'application/json'))->prepare($request);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'resource_owner_password_grant';
    }

    /**
     * @param Request $request
     *
     * @return UserInterface
     * @throws BadUserCredentialsHttpException
     */
    protected function getUser(Request $request)
    {
        // find the user
        $user = $this->userRepository->findOneByUsername($request->get('username'));
        if (!$user) {
            throw new BadUserCredentialsHttpException();
        }

        // Check the validity of the password
        $encoder = $this->encoderFactory->getEncoder($user);
        if (!$encoder->isPasswordValid($user->getPassword(), $request->get('password'), $user->getSalt())) {
            throw new BadUserCredentialsHttpException();
        }

        return $user;
    }
}
