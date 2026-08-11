<?php

namespace dndforslag;

/**
 * PoC-modul for Dataforeningen: foreslår faggrupper (spaces) basert på
 * interessene i brukerens profilfelt "interesser".
 */
class Module extends \humhub\components\Module
{
    public function getName()
    {
        return 'DND Gruppeforslag (PoC)';
    }

    public function getDescription()
    {
        return 'Dynamiske forslag til faggrupper basert på medlemmets interesser.';
    }
}
