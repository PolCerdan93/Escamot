<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
       $this->security = $security;
       $this->urlGenerator = $urlGenerator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?RedirectResponse
    {
        $user = $this->security->getUser();
        
        for($i=0;$i<count($user->getRoles());$i++){
            if($user->getRoles()[$i] == "ROLE_USER"){
                return new RedirectResponse($this->urlGenerator->generate('success'));
            }
        }
        
        return new RedirectResponse($this->urlGenerator->generate('index'));
    }
}
?>