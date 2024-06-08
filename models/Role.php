<?php

namespace app\models;

use amnah\yii2\user\models\Role as AmnahRole;

class Role extends AmnahRole
{
    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;
    const ROLE_LAB = 3;

    public static function getValidRoleIds()
    {
        return [self::ROLE_USER, self::ROLE_ADMIN, self::ROLE_LAB];
    }
}
