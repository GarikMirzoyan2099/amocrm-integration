<?php

namespace App\Services;

use App\Models\Variable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class VariableStorageService
{
    public function setEncrypted(string $key, string $value): void
    {
        $salt = Str::random(32);
        $encrypted = $this->encryptWithSalt($value, $salt);

        Variable::updateOrCreate(
            ['variable' => $key],
            ['value' => $encrypted, 'salt' => $salt]
        );
    }

    public function getDecrypted(string $key): ?string
    {
        $var = Variable::where('variable', $key)->first();
        if (!$var) {
            return null;
        }

        return $this->decryptWithSalt($var->value, $var->salt);
    }

    protected function encryptWithSalt(string $value, string $salt): string
    {
        return base64_encode(openssl_encrypt($value, 'AES-256-CBC', $salt, 0, substr($salt, 0, 16)));
    }

    protected function decryptWithSalt(string $encrypted, string $salt): string
    {
        return openssl_decrypt(base64_decode($encrypted), 'AES-256-CBC', $salt, 0, substr($salt, 0, 16));
    }
}
