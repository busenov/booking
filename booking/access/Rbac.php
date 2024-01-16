<?php

namespace booking\access;

class Rbac
{
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_USER = 'user';

    public static function getRoleList():array
    {
        return [
            static::ROLE_SUPER_ADMIN=>'Супер админ',
            static::ROLE_ADMIN=>'Администратор',
            static::ROLE_MANAGER=>'Менеджер',
            static::ROLE_USER=>'Пользователь',
        ];
    }

    public static function getRoleName(string $role):string
    {
        return self::getRoleList()[$role];
    }
}