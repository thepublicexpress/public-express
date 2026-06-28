<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\State;
class StateSeeder extends Seeder {
    public function run(): void {
        $states = [
            ['name'=>'Uttar Pradesh', 'name_hi'=>'उत्तर प्रदेश', 'slug'=>'uttar-pradesh'],
            ['name'=>'Bihar',         'name_hi'=>'बिहार',        'slug'=>'bihar'],
            ['name'=>'Jharkhand',     'name_hi'=>'झारखंड',       'slug'=>'jharkhand'],
            ['name'=>'Delhi',         'name_hi'=>'दिल्ली',       'slug'=>'delhi'],
        ];
        foreach ($states as $s) { State::firstOrCreate(['slug'=>$s['slug']], $s); }
    }
}
