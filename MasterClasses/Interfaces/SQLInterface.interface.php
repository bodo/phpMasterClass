<?php
namespace Interfaces;

if(!defined('MAIN_SCRIPT'))   die(__FILE__.':'.__LINE__.' says: Go away!'.' ');


interface SQLInterface {
    const _PKFieldName = 'id';
    //const _TableName = NULL;

    public string $_pkName {    get; set;   }
    public mixed $_pkValue {    get; set;   }

    public function connectStorage();

    public function getVerbindung();

    //function create();
    //function update();
}
