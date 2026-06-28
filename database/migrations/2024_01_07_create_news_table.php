<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('news_categories');
            $table->foreignId('state_id')->nullable()->constrained('states');
            $table->foreignId('district_id')->nullable()->constrained('districts');
            $table->foreignId('tehsil_id')->nullable()->constrained('tehsils');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('body')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('type')->default('text'); // text, video, short
            $table->string('video_url')->nullable();
            $table->enum('status', ['draft','pending','published','rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('shares')->default(0);
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    public function down(): void { Schema::dropIfExists('news'); }
};
