<?php

namespace Dashboard\DashboardApi\Integrations\Slim\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Dashboard\DashboardApi\Authentication\AuthenticationService;
use Dashboard\DashboardApi\Components\User\UserRole;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class Authentication
{
    private const ERROR_UNAUTHENTICATED = 'unauthenticated';

    private array $noAutRoutePatterns = [
        '/auth',
        '/request-password-reset',
        '/reset-password',
    ];

    private AuthenticationService $authenticationService;

    private string $appName;

    public function __construct(
        AuthenticationService $authenticationService,
        string $appName
    ) {
        $this->authenticationService = $authenticationService;
        $this->appName = $appName;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        if (is_null($route)) {
            throw new HttpNotFoundException($request);
        }

        $routePattern = $route->getPattern();
        if (in_array($routePattern, $this->noAutRoutePatterns)) {
            return $handler->handle($request);
        }

        $authHeaderLine = $request->getHeaderLine('Authorization');
        $accessToken = str_replace('Bearer ', '', $authHeaderLine);

        $authenticatedUser = $this->authenticationService->getAuthenticatedUser(
            $accessToken,
            $this->appName === "admin" ? UserRole::ROLE_ADMIN : UserRole::ROLE_COWORKER,
        );

        if ($authenticatedUser) {
            $request = $request->withAttribute('AuthUser', $authenticatedUser);

            $response = $handler->handle($request);

            $newAccessToken = $this->authenticationService->generateAccessToken($authenticatedUser);

            return $response->withHeader('Authorization', 'Bearer ' . $newAccessToken);
        } else {
            $response = new \Slim\Psr7\Response(401);
            $response
                ->getBody()
                ->write(
                    json_encode(
                        [
                            'code' => self::ERROR_UNAUTHENTICATED,
                            'message' => 'UnAuthenticated',
                        ]
                    )
                );
            return $response;
        }
    }
}
