<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('department')->nullable()->after('job_title');
            $table->string('role')->default('Super Admin')->after('department');
            $table->string('account_status')->default('active')->after('role');
            $table->string('profile_image_path')->nullable()->after('account_status');
            $table->timestamp('last_login_at')->nullable()->after('profile_image_path');
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('attachment_path');
            $table->string('status')->default('New')->after('is_read');
            $table->string('assigned_to')->nullable()->after('status');
            $table->text('internal_notes')->nullable()->after('assigned_to');
            $table->timestamp('last_contacted_at')->nullable()->after('internal_notes');
            $table->timestamp('archived_at')->nullable()->after('last_contacted_at');
            $table->softDeletes();
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('message');
            $table->string('assigned_to')->nullable()->after('is_read');
            $table->text('internal_notes')->nullable()->after('assigned_to');
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->default('Website enquiry');
            $table->string('service')->nullable();
            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->unsignedTinyInteger('probability')->default(20);
            $table->string('sales_stage')->default('New Lead');
            $table->string('assigned_to')->nullable();
            $table->date('next_follow_up_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('activity_history')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('consultation_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('service')->nullable();
            $table->timestamp('meeting_at')->nullable();
            $table->string('meeting_type')->default('Discovery call');
            $table->string('meeting_link')->nullable();
            $table->string('assigned_consultant')->nullable();
            $table->string('status')->default('Pending');
            $table->text('client_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->string('content_type')->index();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_path')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->string('status')->default('draft');
            $table->integer('display_order')->default(0);
            $table->timestamp('publish_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('source_page')->nullable();
            $table->string('status')->default('subscribed');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->longText('description')->nullable();
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->string('salary_range')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->nullable()->constrained()->nullOnDelete();
            $table->string('candidate_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('status')->default('New');
            $table->string('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('company');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text');
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module')->nullable();
            $table->string('record_type')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('previous_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('path')->index();
            $table->string('route_name')->nullable();
            $table->string('method', 12)->default('GET');
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 8)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->timestamp('visited_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('admin_settings');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_openings');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('consultation_bookings');
        Schema::dropIfExists('leads');

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn(['is_read', 'assigned_to', 'internal_notes']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'is_read',
                'status',
                'assigned_to',
                'internal_notes',
                'last_contacted_at',
                'archived_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'job_title',
                'department',
                'role',
                'account_status',
                'profile_image_path',
                'last_login_at',
            ]);
        });
    }
};
