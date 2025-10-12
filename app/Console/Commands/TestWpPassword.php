<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WordPressHasher;

class TestWpPassword extends Command
{
    protected $signature = 'test:wp-password {password} {hash}';
    protected $description = 'Test WordPress password hashing';

    public function handle()
    {
        $password = $this->argument('password');
        $hash = $this->argument('hash');

        $hasher = new WordPressHasher();
        $result = $hasher->check($password, $hash);

        $this->info("Password: {$password}");
        $this->info("Hash: {$hash}");
        $this->info("Match: " . ($result ? 'YES' : 'NO'));

        // Test specific formats
        $this->info("\nTesting specific formats:");

        if (preg_match('/^\$(?:wp\$)?2y\$/', $hash)) {
            $this->info("Detected: bcrypt");
            $cleanHash = str_replace('$wp$', '$', $hash);
            $bcryptResult = password_verify($password, $cleanHash);
            $this->info("bcrypt result: " . ($bcryptResult ? 'YES' : 'NO'));
        }

        if (strlen($hash) === 32 && ctype_xdigit($hash)) {
            $this->info("Detected: MD5");
            $md5Result = md5($password) === $hash;
            $this->info("MD5 result: " . ($md5Result ? 'YES' : 'NO'));
        }

        if (preg_match('/^\$[PH]\$/', $hash)) {
            $this->info("Detected: PHPass");
            $phpassResult = $hasher->CheckPassword($password, $hash);
            $this->info("PHPass result: " . ($phpassResult ? 'YES' : 'NO'));
        }
    }
}
