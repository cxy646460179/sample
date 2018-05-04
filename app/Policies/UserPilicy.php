<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPilicy
{
    use HandlesAuthorization;


    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
//      用户权限-删除 is_admin 为 true 并且 当前 id 不等于 用户 id 的时候
    public function destroy(User $currentUser, User $user)
    {
        return $currentUser->is_admin && $currentUser->id !== $user->id;
    }
}
