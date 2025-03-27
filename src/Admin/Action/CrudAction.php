<?php

namespace App\Admin\Action;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

interface CrudAction
{
    public static function create(): Action;
}
