<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call('ApiKeyTableSeeder');
        $this->call('UserTableSeeder');
        $this->call('SiteTableSeeder');
        $this->call('ServiceTableSeeder');
        $this->call('PackageTableSeeder');
        $this->call('QuoteTableSeeder');
        $this->call('PageTableSeeder');
        $this->call('EnquiryTableSeeder');
        $this->call('DocumentTableSeeder');

        Model::reguard();
    }
}
