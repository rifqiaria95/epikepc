<?php

namespace App\Policies;

use App\Models\InstagramSetting;
use App\Models\User;

class InstagramSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_instagram');
    }

    public function view(User $user, InstagramSetting $setting): bool
    {
        return $user->can('view_instagram');
    }

    public function update(User $user, InstagramSetting $setting): bool
    {
        return $user->can('manage_instagram');
    }

    public function sync(User $user, InstagramSetting $setting): bool
    {
        return $user->can('sync_instagram');
    }

    public function publish(User $user, InstagramSetting $setting): bool
    {
        return $user->can('publish_instagram') || $user->can('manage_instagram');
    }
}
