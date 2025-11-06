<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateEOQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:eoq';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hitung EOQ, ROP, Safety Stock, dan HPP untuk semua Bahan Baku';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // TODO: letakkan kode perhitungan EOQ di sini
        $this->info('Script EOQ dijalankan!');
        return Command::SUCCESS;
    }
}
