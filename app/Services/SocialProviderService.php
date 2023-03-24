<?php

namespace App\Services;

use App\Models\SocialProvider;
class SocialProviderService
{
    public function findOrCreate($id, Array $data): SocialProvider {
        return SocialProvider::firstOrCreate(
            ['provider_id' => $id],
            $data
        );
    }
}
