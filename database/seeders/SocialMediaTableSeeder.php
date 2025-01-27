<?php

namespace Database\Seeders;


use Dipokhalder\EnvEditor\EnvEditor;
use Illuminate\Database\Seeder;

use Smartisan\Settings\Facades\Settings;

class SocialMediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();

        Settings::group('social_media')->set([
            'social_media_facebook'  => $envService->getValue('DEMO') ? 'https://www.facebook.com/mhl/' : '',
            'social_media_youtube'   => $envService->getValue('DEMO') ? 'https://www.youtube.com/@mhl3830' : '',
            'social_media_instagram' => $envService->getValue('DEMO') ? 'https://www.instagram.com/mhln' : '',
            'social_media_twitter'   => $envService->getValue('DEMO') ? 'https://twitter.com/mhln?lang=en' : ''
        ]);
    }
}
