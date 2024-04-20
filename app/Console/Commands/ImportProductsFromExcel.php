<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportProductsFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Products from spreadsheet file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        $this->components->info('Importing Products from ' . $file);

        Excel::import(new ProductsImport(), $file);

        $this->components->info('All Done!');
    }
}
