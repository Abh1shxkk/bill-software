<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // Branding colors
            $table->string('primary_color', 7)->default('#6366f1');
            $table->string('secondary_color', 7)->default('#8b5cf6');
            $table->string('accent_color', 7)->default('#10b981');
            $table->string('sidebar_color', 7)->default('#1e293b');
            $table->string('header_color', 7)->default('#0f172a');
            
            // Branding assets
            $table->string('favicon_path')->nullable();
            $table->string('login_background_path')->nullable();
            
            // Branding text
            $table->string('app_name')->nullable(); // Custom app name (instead of MediBill)
            $table->string('tagline')->nullable();
            $table->text('footer_text')->nullable();
            
            // Invoice/Receipt branding
            $table->text('invoice_header_html')->nullable();
            $table->text('invoice_footer_html')->nullable();
            $table->text('invoice_terms')->nullable();
            
            // Custom CSS
            $table->text('custom_css')->nullable();
            
            // Feature flags
            $table->boolean('show_powered_by')->default(true); // Show "Powered by MediBill"
            $table->boolean('custom_domain_enabled')->default(false);
            $table->string('custom_domain')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'primary_color',
                'secondary_color',
                'accent_color',
                'sidebar_color',
                'header_color',
                'favicon_path',
                'login_background_path',
                'app_name',
                'tagline',
                'footer_text',
                'invoice_header_html',
                'invoice_footer_html',
                'invoice_terms',
                'custom_css',
                'show_powered_by',
                'custom_domain_enabled',
                'custom_domain',
            ]);
        });
    }
};
