<?php

namespace App\Services;

use App\Contracts\UrlRepositoryInterface;
use App\Models\CodeGeneratorConfiguration;
use App\Models\Url;
use Exception;
use Illuminate\Support\Facades\Cache;

class UrlService
{
    private $urlRepository;

    public function __construct(UrlRepositoryInterface $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    public function generateUrlCode($tries = 0): string
    {
        $config = Cache::rememberForever('code_generator_configuration', function () {
            return CodeGeneratorConfiguration::firstOrFail();
        });

        for ($i = 0; $i < $config->max_attempts; $i++) {
            $max_length = $config->max_length;

            $code = $this->generateUniqueCodeByAlgorithm($max_length);

            if (Url::byCode($code)->count() === 0) {
                return $code;
            }
        }

        $config->update(['max_length' => $config->max_length + 1]);
        Cache::put('code_generator_configuration', $config);

        if ($tries < $config->max_retries) {
            return $this->generateUrlCode($tries + 1);
        }

        throw new Exception('Unable to generate unique code.');
    }



    public function generateUniqueCodeByAlgorithm($length): string
    {
        $runes = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $base = strlen($runes);

        $code = '';
        $random = random_bytes($length);

        for ($i = 0; $i < $length; $i++) {
            $index = ord($random[$i]) % $base;
            $code .= $runes[$index];
        }

        return $code;
    }

    public function create(string $originalUrl, string $code): Url
    {
        return $this->urlRepository->create([
            'original_url' => $originalUrl,
            'code' => $code,
            'user_id' => auth()->id(),
        ]);
    }
}
