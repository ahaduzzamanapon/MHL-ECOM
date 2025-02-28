<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\NotificationSetting;
use Dipokhalder\EnvEditor\EnvEditor;
use Smartisan\Settings\Facades\Settings;


class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $envService = new EnvEditor();
        Settings::group('notification')->set([
            'notification_fcm_public_vapid_key'    => $envService->getValue('DEMO') ? 'BAnppVKS0QFyYzH0pEIIs5EYIAnwoxtVqhOqZ_ILOVhRyT4iTL3h2ZLVBewvqNS6ar1IfpaFuFqEXRiKSqXZg1Y' : '',
            'notification_fcm_api_key'             => $envService->getValue('DEMO') ? 'AIzaSyDvvrGgb1k8eJmj5XNjlQnWoruQfcgqbLE' : '',
            'notification_fcm_auth_domain'         => $envService->getValue('DEMO') ? 'shopking-b8b78.firebaseapp.com' : '',
            'notification_fcm_project_id'          => $envService->getValue('DEMO') ? 'shopking-b8b78' : '',
            'notification_fcm_storage_bucket'      => $envService->getValue('DEMO') ? 'shopking-b8b78.appspot.com' : '',
            'notification_fcm_messaging_sender_id' => $envService->getValue('DEMO') ? '806204285496' : '',
            'notification_fcm_app_id'              => $envService->getValue('DEMO') ? '1:806204285496:web:7b0c79f6df7ba1bba07ccd' : '',
            'notification_fcm_measurement_id'      => $envService->getValue('DEMO') ? 'G-V2SH66NJ2E' : '',
            'notification_fcm_json_file'           => '',
        ]);

        if ($envService->getValue('DEMO') && file_exists(public_path('/file/service-account-file.json'))) {
            $setting = NotificationSetting::where('key', 'notification_fcm_json_file')->first();
            $setting->addMedia(public_path('/file/service-account-file.json'))->preservingOriginal()->usingFileName('service-account-file.json')->toMediaCollection('notification-file');
        }
    }
}
