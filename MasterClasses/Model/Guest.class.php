<?php

namespace Model;
use Interfaces\SQLInterface;

class Guest extends CRUDAbstract implements SQLInterface
{
    const _PKFieldName = 'ID';
    const _TableName = 'User';

    /**
     * @var string $firstname
     */
    protected string $firstname;

    /**
     * @var string $lastname
     */
    protected string $lastname;

    protected static int $_InstanceCount = 0;

    function __construct()
    {
        parent::__construct();

        $this->firstname = 'Ano';
        $this->lastname = 'Nymous';
        //$this->age = 0;

    }

    function create()
    {
        // TODO: Implement create() method.
    }

    function update()
    {
        // TODO: Implement update() method.
    }
}