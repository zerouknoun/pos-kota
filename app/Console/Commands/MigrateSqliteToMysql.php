<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSqliteToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate-sqlite-to-mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi data dari database SQLite ke database MySQL (XAMPP)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai migrasi database dari SQLite ke MySQL...');

        // 1. Explicitly override SQLite path so it is not affected by env('DB_DATABASE')
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

        // 2. Define application tables to migrate (in order of dependency)
        $tables = [
            'users',
            'products',
            'shifts',
            'orders',
            'order_items',
        ];

        // 3. Verify database connections
        try {
            $sqlite = DB::connection('sqlite');
            $sqlite->getPdo();
            $this->info('Koneksi database SQLite berhasil.');
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke database SQLite: ' . $e->getMessage());
            return Command::FAILURE;
        }

        try {
            $mysql = DB::connection('mysql');
            $mysql->getPdo();
            $this->info('Koneksi database MySQL berhasil.');
        } catch (\Exception $e) {
            $this->error('Gagal terhubung ke database MySQL. Pastikan MySQL di XAMPP sudah aktif: ' . $e->getMessage());
            return Command::FAILURE;
        }

        // 4. Temporarily disable foreign key constraints in MySQL
        $this->info('Menonaktifkan sementara pemeriksaan foreign key di MySQL...');
        $mysql->statement('SET FOREIGN_KEY_CHECKS=0;');

        // 5. Migrate table data
        foreach ($tables as $table) {
            $this->info("Memigrasikan tabel [{$table}]...");

            // Fetch all records from SQLite
            $rows = $sqlite->table($table)->get()->map(function ($row) {
                return (array) $row;
            })->toArray();

            $rowCount = count($rows);
            $this->comment("Ditemukan {$rowCount} data di tabel SQLite [{$table}].");

            // Empty the target MySQL table
            $mysql->table($table)->truncate();

            if ($rowCount > 0) {
                // Chunk records to prevent memory/parameter limits
                $chunks = array_chunk($rows, 100);
                foreach ($chunks as $chunk) {
                    $mysql->table($table)->insert($chunk);
                }
                $this->info("Berhasil memindahkan {$rowCount} data ke tabel MySQL [{$table}].");
            } else {
                $this->info("Tabel [{$table}] kosong, melewati pengisian data.");
            }
        }

        // 6. Re-enable foreign key constraints
        $mysql->statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info('Pemeriksaan foreign key di MySQL diaktifkan kembali.');

        $this->info('Migrasi data selesai dan sukses!');
        return Command::SUCCESS;
    }
}
