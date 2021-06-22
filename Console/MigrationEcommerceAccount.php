<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

use Gdevilbat\SpardaCMS\Modules\Post\Entities\Post;
use Gdevilbat\SpardaCMS\Modules\Post\Entities\PostMeta;
use Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities\ProductMeta;

class MigrationEcommerceAccount extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'spardacms:ecommerce-account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration Ecommerce Column';

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
        /*==================================================
        =            Migrate Tokopedia Supplier            =
        ==================================================*/
        
            $query = PostMeta::where('meta_key', 'tokopedia_supplier')->whereNotNull('meta_value');

            $count_progress  = $query->count();

            $suppliers = $query->get();

            $bar = $this->output->createProgressBar($count_progress);

            $bar->start();


            foreach ($suppliers as $key => $supplier) {
                $source = PostMeta::where('meta_key', 'tokopedia_source')
                                    ->where(Post::FOREIGN_KEY, $supplier[POST::FOREIGN_KEY])
                                    ->first();

                if(!empty($source))
                {
                    $data = [
                        'merchant' => $supplier->meta_value,
                        'slug' => $source->meta_value,
                        'is_variant' => false
                    ];

                    PostMeta::unguard();
                    PostMeta::updateOrCreate(
                        ['meta_key' => ProductMeta::TOKPED_SUPPLIER, Post::FOREIGN_KEY => $supplier[POST::FOREIGN_KEY]],
                        ['meta_value' => $data]
                    );
                    PostMeta::reguard();
                }

                $bar->advance();
            }

            $this->info("\r\nTokopedia Supplier Has Been Migrated");
        
        /*=====  End of Migrate Tokopedia Supplier  ======*/

        /*==================================================
        =            Migrate Tokopedia Store            =
        ==================================================*/
        
            $query = PostMeta::where('meta_key', 'tokopedia_store')->whereNotNull('meta_value');

            $count_progress  = $query->count();

            $stores = $query->get();

            $bar = $this->output->createProgressBar($count_progress);

            $bar->start();


            foreach ($stores as $key => $store) {
                $source = PostMeta::where('meta_key', 'tokopedia_slug')
                                    ->where(Post::FOREIGN_KEY, $store[POST::FOREIGN_KEY])
                                    ->first();

                if(!empty($source))
                {
                    $data = [
                        'merchant' => $store->meta_value,
                        'slug' => $source->meta_value,
                        'is_variant' => false
                    ];

                    PostMeta::unguard();
                    PostMeta::updateOrCreate(
                        ['meta_key' => ProductMeta::TOKPED_STORE, Post::FOREIGN_KEY => $store[POST::FOREIGN_KEY]],
                        ['meta_value' => $data]
                    );
                    PostMeta::reguard();
                }

                $bar->advance();
            }

            $this->info("\r\nTokopedia Store Has Been Migrated");
        
        /*=====  End of Migrate Tokopedia Store  ======*/


        /*==================================================
        =            Migrate Shopee Store            =
        ==================================================*/
        
            $query = PostMeta::where('meta_key', 'shopee_slug')->whereNotNull('meta_value');

            $count_progress  = $query->count();

            $stores = $query->get();

            $bar = $this->output->createProgressBar($count_progress);

            $bar->start();


            foreach ($stores as $key => $store) {

                $exp = explode('/', $store->meta_value);

                $data = [
                    'shop_id' => $exp[1],
                    'product_id' => $exp[2],
                    'is_variant' => false
                ];

                PostMeta::unguard();
                PostMeta::updateOrCreate(
                    ['meta_key' => ProductMeta::SHOPEE_STORE, Post::FOREIGN_KEY => $store[POST::FOREIGN_KEY]],
                    ['meta_value' => $data]
                );
                PostMeta::reguard();

                $bar->advance();
            }

            $this->info("\r\nShopee Store Has Been Migrated");
        
        /*=====  End of Migrate Shopee Store  ======*/
        


        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
