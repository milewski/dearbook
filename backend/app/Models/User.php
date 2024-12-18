<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
}
