<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\NewsCategory;
class NewsCategorySeeder extends Seeder {
    public function run(): void {
        $categories = [
            ['name'=>'Politics',      'name_hi'=>'राजनीति',     'slug'=>'politics',     'color'=>'#e53e3e', 'sort_order'=>1],
            ['name'=>'Crime',         'name_hi'=>'अपराध',       'slug'=>'crime',        'color'=>'#dd6b20', 'sort_order'=>2],
            ['name'=>'Development',   'name_hi'=>'विकास',       'slug'=>'development',  'color'=>'#38a169', 'sort_order'=>3],
            ['name'=>'Education',     'name_hi'=>'शिक्षा',      'slug'=>'education',    'color'=>'#3182ce', 'sort_order'=>4],
            ['name'=>'Health',        'name_hi'=>'स्वास्थ्य',   'slug'=>'health',       'color'=>'#00b5d8', 'sort_order'=>5],
            ['name'=>'Agriculture',   'name_hi'=>'कृषि',        'slug'=>'agriculture',  'color'=>'#68d391', 'sort_order'=>6],
            ['name'=>'Sports',        'name_hi'=>'खेल',         'slug'=>'sports',       'color'=>'#ed8936', 'sort_order'=>7],
            ['name'=>'Entertainment', 'name_hi'=>'मनोरंजन',    'slug'=>'entertainment','color'=>'#9f7aea', 'sort_order'=>8],
            ['name'=>'Business',      'name_hi'=>'व्यापार',     'slug'=>'business',     'color'=>'#667eea', 'sort_order'=>9],
            ['name'=>'Accident',      'name_hi'=>'हादसा',       'slug'=>'accident',     'color'=>'#fc8181', 'sort_order'=>10],
        ];
        foreach ($categories as $cat) { NewsCategory::firstOrCreate(['slug'=>$cat['slug']], $cat); }
    }
}
