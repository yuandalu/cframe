<?php

namespace App\Conf;

class UserConf
{
    const USER1 = 'user1';
    const USER2 = 'user2';

    public static function getUsers()
    {
        return array(self::USER1, self::USER2);
    }
}