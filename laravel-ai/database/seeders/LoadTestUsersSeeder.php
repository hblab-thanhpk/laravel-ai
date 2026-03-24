<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoadTestUsersSeeder extends Seeder
{
    /**
     * Số lượng user tạo ra (override bằng --count khi gọi Artisan).
     */
    public const DEFAULT_COUNT = 10_000;

    public function run(): void
    {
        $count = (int) (env('LOAD_TEST_COUNT', self::DEFAULT_COUNT));

        $customerRoleId = Role::query()->where('name', 'customer')->value('id');

        // Dùng cùng một password hash cho toàn bộ — tránh bcrypt N lần
        $hashedPassword = Hash::make('password');
        $now = now()->toDateTimeString();

        $this->command?->info("Tạo {$count} load-test users...");
        $this->command?->info('Xoá load-test users cũ nếu có...');
        DB::table('users')->where('email', 'like', '%@loadtest.local')->delete();
        $this->command?->info('Bắt đầu insert...');

        $bar = $this->command?->getOutput()->createProgressBar($count);
        $bar?->start();

        // Bulk insert theo chunk 1000 rows/query để tránh memory spike
        $chunkSize = 1_000;
        $chunks = (int) ceil($count / $chunkSize);

        for ($chunk = 0; $chunk < $chunks; $chunk++) {
            $rows = [];
            $batchSize = min($chunkSize, $count - $chunk * $chunkSize);

            for ($i = 0; $i < $batchSize; $i++) {
                $n = $chunk * $chunkSize + $i + 1;
                $rows[] = [
                    'id' => Str::uuid()->toString(),
                    'name' => 'LoadTest User '.$n,
                    'email' => "loadtest_{$n}@loadtest.local",
                    'password' => $hashedPassword,
                    'is_admin' => false,
                    'role_id' => $customerRoleId,
                    'email_verified_at' => $now,
                    'remember_token' => Str::random(10),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users')->insert($rows);
            $bar?->advance($batchSize);
        }

        $bar?->finish();
        $this->command?->newLine();
        $this->command?->info("Hoàn thành: đã tạo {$count} users.");
        $this->command?->info('Đăng nhập với email bất kỳ loadtest_*@loadtest.local / password: password');
    }
}
