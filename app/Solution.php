<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Solution extends Model
{
    protected $table = 'solutions';

    protected $fillable = [
        'isApproved', 'isFeatured', 'url', 'title', 'description',
        'image', 'content', 'features', 'pricing', 
        'compatibility', 'deployments', 'languages',
        'market', 'contact_link', 'vendor_id', 
        'client_images', 'banner_uploads','explore_banner','brochure_upload', 'award_and_certificates',
        'awards','interface','faqs', 'case_study',
        'cyberpal_review_id', 'user_id', 'category_id',
        'organisation_size', 'up_votes', 'down_votes', 'total_votes',
        'order', 'ease_of_operations', 'user_feedbacks', 'typical_customers', 
        'customer_support', 'cons', 'pros', 'services','company_name', 'years_of_business', 'vendor_status',
        'company', 'value_for_money', 'usability', 'ease_of_deployment', 'reliability', 'support_customer',
        'performance', 'partnership', 'standout_feature_grading', 'integration', 'revenue', 'commutative_rating',
        'positivie', 'negative', 'neutral','vendor_customer_communication', 'r_and_d_labs'
        ,'vendors_existing_clients', 'core_team_quality', 'infrastructure_state','differentiator_factor', 'vendor_market_share'
        ,'funding_arrangements', 'global_presence', 'price_type_one_off', 'perpetual_licensing', 'subscription_based'
        ,'vendors_pricing_classification', 'additional_plugin_support', 'third_pary_integration', 'windows_one', 'ios_integration', 'android_integration'
        ,'mac_integration', 'linux_integration', 'api_integration', 'cloud_deployment', 'saas_deployment', 'web_deployment', 'premise_appliance'
        ,'premise_appliance', 'scalability_feature', 'upgrade_feature', 'hosting_infrasturucture_feature', 'user_experience', 'global_customer_support'
        ,'email_customer_support', 'live_chat_customer_support', 'phone_customer_support', 'insurance', 'all_customer_support', 'customer_support_rating'
        ,'brick_mortar', 'relavant_documents', 'bbc_verification', 'credit_report', 'quality_reliability','certifications_accreditation', 'faqs'
        ,'cpu_usage', 'domain_verification', 'bugs_glitches', 'solution_quality', 'years_operation'
        , 'score', 'star_rating', 'strength', 'client_audit', 'assessment_report', 'past_security_breaches'
        ,'assessment_frequency', 'security_obligations', 'sdlc_practice', 'data_security_access_managment'
        ,'data_encryption', 'audit_trails', 'monitoring', 'address', 'number_of_employees', 'company_type', 'establishment_year', "preview_video" 
    ];
    
    protected $casts = [
        'features' => 'json',
        'pricing' => 'json',
        'price_from'=> 'json',
        'price_to' => 'json',
        'client_images'=> 'json',
        'banner_uploads'=> 'json',
        'award_and_certificates'=> 'json',
        'price_statement' => 'json',
        'compatibility' => 'json',
        'deployments' => 'json',
        'languages' => 'json',
        'market' => 'json',
        'ease_of_operations' => 'json', 
        'user_feedbacks' => 'json', 
        'typical_customers' => 'json', 
        'customer_support' => 'json', 
        'cons' => 'json', 
        'faqs' => 'json',
        'case_study' => 'json',
        'pros' => 'json',
        'services' => 'json',
        'company_name' => 'json',
    ];

    // - Vendor
    public function merchant () {
        return $this->hasOne(Merchants::class, 'id' ,'vendor_id');
    }

    // - CyberPal Review
    public function cyberpalReview () {
        return $this->hasOne(CyberPalReviews::class, 'id' ,'cyberpal_review_id');
    }

    public function company() {
        return $this->hasOne(Company::class, 'id' ,'company_name')->select(['id','company_name']);
    }
       
    // - CyberPal Review
    public function reviews () {
        return $this->hasMany(Reviews::class, 'solution_id' ,'id');
    }

    // - User
    public function user () {
        return $this->hasOne(User::class, 'id' ,'user_id');
    }
       
    // - Category
    public function category () {
        return $this->hasOne(Categories::class, 'id' ,'category_id');
    }

    // - Resellers
    public function resellers () {
        return $this->belongsToMany(Resellers::class, 'reseller_solution', 'solution_id', 'reseller_id');
    }

    // - Cyberpal Solution Marking
    public function solutionMarking(){
        return $this->hasOne(SolutionMarking::class, 'solution_id', 'id');
    }
    public function votes(){
        return $this->hasMany(SolutionLike::class, 'solution_id', 'id');
    }
}
