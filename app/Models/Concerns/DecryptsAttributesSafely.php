<?php

namespace App\Models\Concerns;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

trait DecryptsAttributesSafely
{
    public function fromEncryptedString($value)
    {
        try {
            return parent::fromEncryptedString($value);
        } catch (DecryptException) {
            if (! static::$loggedUndecryptableAttribute) {
                static::$loggedUndecryptableAttribute = true;
                Log::warning('Encrypted attribute could not be decrypted. The stored value was encrypted with a different APP_KEY.');
            }

            return null;
        }
    }
}
