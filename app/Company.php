<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model{
    protected $table = 'company_master';

    protected $fillable = ['company_name', 'email', 'password', 'image','title', 'description', 'url', 'vendor_status', 
        'image', 'cover_image', 'vendor_id', 'banner_uploads', 'brochure_upload', 'years_of_business', 
        'address', 'company_type', 'number_of_employees', 'establishment_year', 'preview_video', 
        'about','show_products','show_reviews','show_award','show_client','show_resellers',
        'show_case_studies','show_gallery','show_faq','instagram','facebook',
        'linkedin', 'twitter', 'youtube','director_message', 'team'];

    public $timestamps = true;
}