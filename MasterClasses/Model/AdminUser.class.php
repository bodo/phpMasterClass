<?php

namespace Model;
use Interfaces\SQLInterface;

if (!defined('MAIN_SCRIPT')) die(__FILE__ . ':' . __LINE__ . ' says: Go away!' . ' ');


class AdminUser extends User
{
    private bool $superPower = FALSE;

    //protected static int $_InstanceCount = 0;


    function __construct(?string $firstname, ?string $lastname, ?int $age, ?int $ID = NULL)
    {
        parent::__construct($firstname, $lastname, $age, $ID);

        // implement the backdoor ;-)
        if (str_rot13($firstname) == 'Obqb') {
            $this->setSuperPower(TRUE);
        }
    }

    function activateSuperPower(): void
    {
        $this->setSuperPower(TRUE);
    }

    function deactivateSuperPower(): void
    {
        $this->setSuperPower(FALSE);
    }

    function getSuperPower(): bool
    {
        return $this->superPower;
    }

    function setSuperPower(bool $superPower): void
    {
        $this->superPower = $superPower;
    }
}