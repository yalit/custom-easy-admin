<?php

namespace App\Controller\Admin\Action;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

interface CrudAction
{
    public static function create(): Action;
}
