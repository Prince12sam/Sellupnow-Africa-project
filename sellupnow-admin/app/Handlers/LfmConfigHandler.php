<?php

namespace App\Handlers;

class LfmConfigHandler extends \UniSharp\LaravelFilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        $user = auth()->user();

        if (! $user) {
            return null;
        }

        return (string) $user->id;
    }



    public function baseDirectory()
    {
        return 'public';
    }
}
