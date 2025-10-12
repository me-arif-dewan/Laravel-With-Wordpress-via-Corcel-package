<?php

namespace App\Services;

use App\Services\PasswordHash;

class WordPressHasher
{
    function check_password(string $password, string $hash): bool
    {
        static $wp_hasher;

        // 1. Legacy MD5
        if (strlen($hash) <= 32 && ctype_xdigit($hash)) {
            return hash_equals($hash, md5($password));
        }

        // 2. If phpass is initialized
        if (!empty($wp_hasher)) {
            return $wp_hasher->CheckPassword($password, $hash);
        }

        // 3. Block absurdly long passwords
        if (strlen($password) > 4096) {
            return false;
        }

        // 4. New WP bcrypt hashes with "$wp" prefix (since WP 6.8)
        if (strpos($hash, '$wp') === 0) {
            // WordPress transforms the password with HMAC-SHA384 + base64 first
            $password_to_verify = base64_encode(hash_hmac('sha384', $password, 'wp-sha384', true));
            // Strip the "$wp" prefix (leave standard bcrypt hash)
            $real_hash = substr($hash, 3);
            return password_verify($password_to_verify, $real_hash);
        }

        // 5. Old PHPass hashes ($P$ or $H$)
        if (preg_match('/^\$P\$|\$H\$/', $hash)) {
            $wp_hasher = new PasswordHash(8, true);
            return $wp_hasher->CheckPassword($password, $hash);
        }

        // 6. Default: use password_verify for bcrypt/argon2
        return password_verify($password, $hash);
    }
}
