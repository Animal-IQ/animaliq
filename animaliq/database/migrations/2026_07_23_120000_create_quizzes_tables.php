<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug', 200)->unique();
            $table->text('description')->nullable();
            $table->string('banner_image')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'expert'])->default('medium');
            $table->unsignedSmallInteger('duration_minutes')->nullable()->comment('Time limit in minutes; null = no limit');
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('show_explanations')->default(true);
            $table->boolean('require_login')->default(true);
            $table->boolean('allow_retake')->default(true);
            $table->unsignedSmallInteger('max_attempts')->nullable()->comment('Null = unlimited');
            $table->unsignedSmallInteger('pass_percentage')->default(50);
            $table->unsignedSmallInteger('points_complete')->default(8)->comment('Base points for finishing');
            $table->unsignedSmallInteger('points_perfect_bonus')->default(15);
            $table->unsignedSmallInteger('points_high_score_bonus')->default(10)->comment('Bonus if score >= pass%');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->string('type', 40)->comment('who_am_i, case_file, story_adventure, fact_fiction, silhouette, match_tracks, animal_vs_animal, odd_one_out, rank_them, multiple_choice');
            $table->string('prompt')->nullable();
            $table->json('payload')->nullable()->comment('Type-specific content (clues, options, pairs, etc.)');
            $table->text('explanation')->nullable();
            $table->string('image_path')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'expert'])->nullable();
            $table->unsignedSmallInteger('points')->default(10);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_token', 64)->nullable()->index();
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'timed_out'])->default('in_progress');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('max_score')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->json('question_order')->nullable();
            $table->timestamps();

            $table->index(['quiz_id', 'user_id']);
            $table->index(['quiz_id', 'status']);
        });

        Schema::create('quiz_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained('quiz_attempts')->cascadeOnDelete();
            $table->foreignId('quiz_question_id')->constrained('quiz_questions')->cascadeOnDelete();
            $table->json('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedSmallInteger('points_earned')->default(0);
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'quiz_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempt_answers');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
    }
};
