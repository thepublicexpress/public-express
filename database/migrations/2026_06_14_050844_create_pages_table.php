<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default pages
        DB::table('pages')->insert([
            [
                'slug' => 'about',
                'title' => 'हमारे बारे में',
                'meta_title' => 'हमारे बारे में - द पब्लिक एक्सप्रेस',
                'meta_description' => 'द पब्लिक एक्सप्रेस के बारे में जानें',
                'content' => '<h1>द पब्लिक एक्सप्रेस के बारे में</h1><p>द पब्लिक एक्सप्रेस एक हाइपरलोकल न्यूज़ प्लेटफॉर्म है जो आपको आपके शहर, जिला और तहसील की ताज़ा खबरें प्रदान करता है।</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'contact',
                'title' => 'संपर्क करें',
                'meta_title' => 'संपर्क करें - द पब्लिक एक्सप्रेस',
                'meta_description' => 'द पब्लिक एक्सप्रेस से संपर्क करें',
                'content' => '<h1>संपर्क करें</h1><p>आप हमसे निम्नलिखित माध्यमों से संपर्क कर सकते हैं:</p><ul><li>ईमेल: info@thepublicexpress.com</li><li>फोन: +91-XXXXXXXXXX</li><li>पता: लखनऊ, उत्तर प्रदेश</li></ul>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'privacy',
                'title' => 'गोपनीयता नीति',
                'meta_title' => 'गोपनीयता नीति - द पब्लिक एक्सप्रेस',
                'meta_description' => 'द पब्लिक एक्सप्रेस की गोपनीयता नीति',
                'content' => '<h1>गोपनीयता नीति</h1><p>हम आपकी गोपनीयता का सम्मान करते हैं। यह नीति बताती है कि हम आपकी व्यक्तिगत जानकारी को कैसे एकत्रित, उपयोग और सुरक्षित करते हैं।</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'terms',
                'title' => 'नियम और शर्तें',
                'meta_title' => 'नियम और शर्तें - द पब्लिक एक्सप्रेस',
                'meta_description' => 'द पब्लिक एक्सप्रेस की नियम और शर्तें',
                'content' => '<h1>नियम और शर्तें</h1><p>हमारी वेबसाइट का उपयोग करके, आप निम्नलिखित नियमों और शर्तों से सहमत होते हैं।</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'disclaimer',
                'title' => 'अस्वीकरण',
                'meta_title' => 'अस्वीकरण - द पब्लिक एक्सप्रेस',
                'meta_description' => 'द पब्लिक एक्सप्रेस का अस्वीकरण',
                'content' => '<h1>अस्वीकरण</h1><p>इस वेबसाइट पर प्रदान की गई जानकारी केवल सामान्य जानकारी के उद्देश्यों के लिए है।</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
};