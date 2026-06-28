<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\District;
use App\Models\Tehsil;
class TehsilSeeder extends Seeder {
    public function run(): void {
        $azamgarh = District::where('slug','azamgarh')->first();
        if (!$azamgarh) return;
        $tehsils = [
            ['name'=>'Sadar',       'name_hi'=>'सदर',       'slug'=>'azamgarh-sadar'],
            ['name'=>'Sagri',       'name_hi'=>'सगड़ी',     'slug'=>'azamgarh-sagri'],
            ['name'=>'Lalganj',     'name_hi'=>'लालगंज',    'slug'=>'azamgarh-lalganj'],
            ['name'=>'Phulpur',     'name_hi'=>'फूलपुर',    'slug'=>'azamgarh-phulpur'],
            ['name'=>'Mehnagar',    'name_hi'=>'मेहनगर',    'slug'=>'azamgarh-mehnagar'],
            ['name'=>'Nizamabad',   'name_hi'=>'निज़ामाबाद', 'slug'=>'azamgarh-nizamabad'],
        ];
        foreach ($tehsils as $t) {
            Tehsil::firstOrCreate(['slug'=>$t['slug']], array_merge($t, ['district_id'=>$azamgarh->id]));
        }
    }
}
