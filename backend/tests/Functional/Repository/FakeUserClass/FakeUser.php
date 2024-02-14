<?php

namespace App\Tests\Functional\Repository\FakeUserClass;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class FakeUser implements PasswordAuthenticatedUserInterface
{
    public function getPassword(): ?string
    {
        return 'Fake user password';
    }
}