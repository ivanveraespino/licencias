<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserIsVerified implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Tu cuenta no está verificada.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Lógica adicional si la necesitas
    }
}
