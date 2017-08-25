<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PasarAPendiente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pasar_a_pendiente';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('transacciones')
            ->where('id',159104)
            ->update(array('estado' => 'Pendiente'));

        DB::table('transacciones')
            ->where('id',159102)
            ->update(array('estado' => 'Pendiente'));

        $this->info('Tablas cambiadas!');
    }
}
