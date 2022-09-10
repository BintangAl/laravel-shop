<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('notificationFromAdmin.{user_id}', function ($user, $id) {
    // return (int) $user->id === (int) $id;
    if ($user->id == $id) {
        return true;
    }
});

Broadcast::channel('notificationFromMunnShop.{user_id}', function ($user, $id) {
    // return (int) $user->id === (int) $id;
    if ($user->id == $id) {
        return true;
    }
});
