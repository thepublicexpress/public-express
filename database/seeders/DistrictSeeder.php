<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\District;
class DistrictSeeder extends Seeder {
    public function run(): void {
        $up = State::where('slug','uttar-pradesh')->first();
        if (!$up) return;
        $districts = [
            ['name'=>'Azamgarh',    'name_hi'=>'आजमगढ़',    'slug'=>'azamgarh'],
            ['name'=>'Mau',         'name_hi'=>'मऊ',         'slug'=>'mau'],
            ['name'=>'Ballia',      'name_hi'=>'बलिया',      'slug'=>'ballia'],
            ['name'=>'Gorakhpur',   'name_hi'=>'गोरखपुर',   'slug'=>'gorakhpur'],
            ['name'=>'Varanasi',    'name_hi'=>'वाराणसी',    'slug'=>'varanasi'],
            ['name'=>'Jaunpur',     'name_hi'=>'जौनपुर',    'slug'=>'jaunpur'],
            ['name'=>'Ghazipur',    'name_hi'=>'ग़ाज़ीपुर', 'slug'=>'ghazipur'],
            ['name'=>'Sant Kabir Nagar','name_hi'=>'संत कबीर नगर','slug'=>'sant-kabir-nagar'],
            ['name'=>'Ambedkar Nagar','name_hi'=>'अंबेडकर नगर','slug'=>'ambedkar-nagar'],
            ['name'=>'Deoria',      'name_hi'=>'देवरिया',   'slug'=>'deoria'],
            ['name'=>'Kushinagar',  'name_hi'=>'कुशीनगर',  'slug'=>'kushinagar'],
        ];
        foreach ($districts as $d) {
            District::firstOrCreate(['slug'=>$d['slug']], array_merge($d, ['state_id'=>$up->id]));
        }
    }
}
