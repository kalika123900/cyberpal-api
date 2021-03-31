<?php

use App\Http\Controllers\Client\LocationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// - CLIENT API's
Route::prefix('v1/client')->group(function () {
    // - AUTH
    Route::post('auth/login', 'Client\AuthController@login');
    Route::post('auth/register', 'Client\AuthController@register');
    Route::get('auth/register/{token}', 'Client\AuthController@verifyRegister');

    Route::post('auth/callback/google', 'Client\AuthController@google');
    Route::post('auth/callback/linkedin', 'Client\AuthController@linkedin');

    Route::post('auth/password/create', 'Client\ForgotPasswordController@create');
    Route::get('auth/password/create/{token}', 'Client\ForgotPasswordController@find');
    Route::post('auth/password/reset', 'Client\ForgotPasswordController@reset');
    Route::get('get-locale','Client\AuthController@getlocale');
    Route::get('conversion-rate','Client\AuthController@getConversionRate');

    // - Dynamic Page
    Route::get('dynamic-pages/solutions', 'Client\DynamicPageController@getHomepageData');
    // - Blogs
    Route::get('blogs', 'Client\BlogsController@getAllData');
    Route::get('blogs/search', 'Client\BlogsController@searchData');
    Route::get('blogs/{url}', 'Client\BlogsController@getSingleData');
    // - Buyers Guide
    Route::get('buyers-guide', 'Client\BuyersGuideController@getAllData');
    Route::get('buyers-guide/search', 'Client\BuyersGuideController@searchData');
    Route::get('buyers-guide/download-solutions-pdf', 'Client\BuyersGuideController@downloadSolutionsPDF');
    // - Categories
    Route::get('categories', 'Client\CategoriesController@getAllData');
    Route::get('categories/search', 'Client\CategoriesController@searchData');
    Route::get('categories/{url}', 'Client\CategoriesController@getSingleData');
    Route::get('get-category-group', 'Client\CategoriesController@getAllCategoryGroup');
    Route::get('categories/{category_url}/events', 'Client\EventsController@getAllCategoryEvents');
    Route::get('categories/{category_url}/courses', 'Client\CoursesController@getAllCategoryCourses');

    // - Courses
    Route::get('courses', 'Client\CoursesController@getAllData');
    Route::get('courses/search', 'Client\CoursesController@searchData');
    Route::get('courses/filters', 'Client\CoursesController@getFiltersData');
    Route::get('courses/{url}', 'Client\CoursesController@getSingleData');
    // - Events
    Route::get('events', 'Client\EventsController@getAllData');
    Route::get('events/search', 'Client\EventsController@searchData');
    Route::get('events/filters', 'Client\EventsController@getFiltersData');
    Route::get('events/{url}', 'Client\EventsController@getSingleData');
    Route::get('best-fit', 'Client\CategoriesController@getBestFit');
    Route::get('get-category-features', 'Client\CategoriesController@categoryBasedFeatures');
    // - Forms
    Route::post('forms/contact', 'Client\FormsController@addNewContactFormData');
    Route::post('forms/ask-service', 'Client\FormsController@addNewRequestSeriviceData');
    // - Reviews
    Route::get('reviews', 'Client\ReviewsController@getAllData');
    Route::post('reviews/write', 'Client\ReviewsController@addNewReview');
    Route::get('reviews/{url}', 'Client\ReviewsController@getSingleSolutionReviews');
    // - Vendors
    Route::post('vendors/become-partner', 'Client\VendorsController@addNewVendor');
    // - Solutions
    Route::get('solutions', 'Client\SolutionsController@getAllData');
    Route::post('track', 'Client\SolutionsController@track');
    Route::get('homepage-search', 'Client\SolutionsController@homepage_search');
    Route::post('solution-hit', 'Client\SolutionsController@solution_hit');
    Route::get('solutions/top-solutions', 'Client\SolutionsController@getTopSolutions');
    Route::get('solutions/search', 'Client\SolutionsController@searchData');
    Route::get('solutions/cyber-security-recruiters', 'Client\SolutionsController@cyberSecurityRecruiters');
    Route::get('solutions/cyber-security-insurances', 'Client\SolutionsController@cyberSecurityInsurances');
    Route::post('solution-vote', 'Client\SolutionsController@upvoteSolution');
    Route::post('solution/ask-mail', 'Client\SolutionsController@solutionsAskMail');

    Route::get('solutions/updateLatLngResellers','Client\SolutionsController@updateLatLngResellers');
    Route::get('solutions/getResellers', 'Client\SolutionsController@getResellers');
    Route::get('solutions/resellers/{url}', 'Client\SolutionsController@getSingleReseller');
    Route::get('solutions/range-resellers', 'Client\SolutionsController@getResellerInRange');
    // Route::get('solutions/{url}/reviews', 'Client\SolutionsController@getReviews');
    Route::get('solutions/{url}', 'Client\SolutionsController@getSingleData');
    // - Static Pages
    Route::get('static-pages/{url}', 'Client\PagesController@getSingleData');
    // - Locations
    Route::get('locations/search', 'Client\LocationsController@searchData');
    Route::get('locations/{id}', 'Client\LocationsController@getSingleLocation');
    // - Community
    Route::get('community', 'Client\CommunityController@getAllData');
    Route::post('community/add-question', 'Client\CommunityController@addNewQuestion');
    Route::get('community/upvote', 'Client\CommunityController@upvoteQuestion');
    Route::post('community/question/{url}/add-answer', 'Client\CommunityController@addNewAnswer');
    Route::get('community/question/{url}', 'Client\CommunityController@getSingleQuestion');
    // - Dynamic_homepage
    Route::get('homepage/solutions', "Client\DynamicSolutionsPage@getHomepageData");
    Route::post('subscribe/newsletter', "ContactsController@subscribeNewsletter");
    //Route::post('subscribe/newsletter', "ThirdpartyController@subscribeNewsletter");

    //-Email
    Route::post('send-email', 'Client\SolutionsController@sendEmail');
    // - Protected Routes     
    Route::middleware('auth:api')->group(function () {
        Route::get('user/projects/requests/{id}/activate-project', 'Client\ProjectsController@activeSingleUserRequest');

        Route::get('user', 'Client\AuthController@getUser');
        Route::post('user', 'Client\AuthController@updateLoggedInUser');
        // - Project Payments
        // - Projects
        Route::get('projects/metadata', 'Client\ProjectsController@getStripeSecret');
        Route::post('projects/add', 'Client\ProjectsController@addNewData');
        Route::get('user/projects/requests', 'Client\ProjectsController@getAllUserRequests');
        // - Proposals
        Route::post('user/projects/requests/{id}/status', 'Client\ProjectsController@updateClientStatus');
        Route::post('user/projects/requests/{id}/action', 'Client\ProjectsController@proposalAction');
        Route::get('user/projects/requests/{id}/initiate-payment', 'Client\PaymentsController@initiateProjectPayment');
        Route::get('user/projects/requests/{id}', 'Client\ProjectsController@getSingleUserRequest');
        Route::get('user/projects/proposal/{id}', 'Client\ProjectsController@singleProposal');
         // - Reviews
        Route::get('user/reviews', 'Client\ReviewsController@getAllUserReviews');
        Route::get('user/reviews/{id}', 'Client\ReviewsController@getSingleUserReview');
        Route::post('user/reviews/{id}', 'Client\ReviewsController@editSingleUserReview');
        Route::post('user/reviews/delete/{id}', 'Client\ReviewsController@deleteSingleUserReview');
        // - Leads
        Route::get('user/leads', 'Client\LeadsController@getAllLeads');
        Route::get('user/leads/{id}', 'Client\LeadsController@getSingleLead');
    });
});
// - Merchant API's
Route::prefix('v1/merchant')->group(function () {
    Route::post('login', 'Merchant\AuthController@login');
    Route::get('auth/register/{token}', 'Client\AuthController@verifyRegister');
   
    Route::post('auth/password/create', 'Merchant\AuthController@create');
    Route::get('auth/password/create/{token}', 'Merchant\AuthController@find');
    Route::post('auth/password/reset', 'Merchant\AuthController@reset');

    // - #TODO: Check for roles properly.
    Route::middleware('auth:api')->group(function () {
        
        // - Analytics Leads
        Route::get('vendor/analytics/overall-leads', 'AnalyticsController@getOverallLeadsData');
        Route::get('vendor/analytics/today-leads', 'AnalyticsController@getTodayleadsData');
        Route::get('vendor/analytics/graph-data', 'AnalyticsController@getLeadsGraphData');
        Route::get('vendor/analytics/website-clicks-data', 'AnalyticsController@getWebsiteClicksData');
        Route::get('vendor/analytics/overall-clicks', 'AnalyticsController@overallClicksData');
        Route::get('vendor/analytics/new-leads', 'AnalyticsController@getNewLeadsData');
        Route::get('vendor/analytics/in-progress-leads', 'AnalyticsController@getInProgressData');
        Route::get('vendor/analytics/closed-leads', 'AnalyticsController@getClosedLeadsData');
        Route::post('vendor/analytics/update-lead-status', 'AnalyticsController@updateLeadStatus');
        Route::get('vendor/analytics/lead-details/{id}', 'AnalyticsController@getLeadDetails');
        // - Analytics Projects
        Route::get('expert/analytics/overall-projects', 'AnalyticsController@getOverallProjectsData');
        Route::get('expert/analytics/today-projects', 'AnalyticsController@getTodayProjectsData');
        Route::get('expert/analytics/graph-data', 'AnalyticsController@getExpertsGraphData');
        // - User
        Route::get('user', 'Merchant\AuthController@getLoggedInUser');
        Route::post('user', 'Merchant\AuthController@updateLoggedInUser');
        Route::post('user/avatar', 'Merchant\AuthController@updateAvatar');
        // - Leads
        Route::get('vendor/invites', 'Merchant\LeadController@all');
        Route::post('vendor/invites/{id}/status', 'Merchant\LeadController@update');
        Route::get('vendor/invites/{id}', 'Merchant\LeadController@get');
        // - Projects
        Route::get('projects/invites', 'Merchant\ProjectsController@getUserProjectInvites');
        Route::post('projects/invites/{id}/proposal', 'Merchant\ProjectsController@sendInviteProposal');
        Route::post('projects/invites/{id}/status', 'Merchant\ProjectsController@updateStatus');
        Route::get('projects/invites/{id}', 'Merchant\ProjectsController@getSingleProjectInvite');
        // - Solution
        Route::get('solution/{id}', 'Merchant\LeadController@getSingleSolution');
        Route::get('vendor/get-company',   'Merchant\CompanyController@getCompany');
        Route::post('vendor/update-company',   'Merchant\CompanyController@updateCompany');
        Route::get('get-category-features', 'CategoriesController@categoryBasedFeatures');
    });
});
// - ADMIN API'S
Route::prefix('v1/admin')->group(function () {
    Route::post('login', 'AuthController@adminLogin');

    Route::middleware('auth:api')->group(function () {
        Route::get('user', 'AuthController@getLoggedInUser');
        Route::post('user', 'AuthController@updateLoggedInUser');
    });

    Route::post('listResellers', 'Client\SolutionsController@listResellers');
    Route::get('listResellers', 'Client\SolutionsController@listResellers');


    // - Finalized
    Route::post('users/search', 'VendorsController@searchData');
    Route::post('merchants/search', 'ProjectsController@searchMerchantData');
    Route::post('users/disable', 'VendorsController@disableAccount');
    Route::post('users/enable', 'VendorsController@enableAccount');
    Route::apiResource('users', 'VendorsController');
    // Route::post('users/delete', 'VendorsController@disableMultipleData');
    Route::apiResource('locations', 'LocationsController');
    Route::post('locations/delete', 'LocationsController@deleteMultipleData');
    Route::post('locations/search', 'LocationsController@searchData');

    Route::apiResource('pages', 'PagesController');
    Route::post('pages/delete', 'PagesController@deleteMultipleData');
    Route::post('pages/search', 'PagesController@searchData');

    Route::apiResource('blogs','BlogsController');
    Route::post('blogs/delete', 'BlogsController@deleteMultipleData');
    Route::post('blogs/search', 'BlogsController@searchData');

    Route::apiResource('faq', 'BuyersGuideController');
    Route::post('faq/delete', 'BuyersGuideController@deleteMultipleData');
    Route::post('faq/search', 'BuyersGuideController@searchData');

    Route::apiResource('events', 'EventController');
    Route::post('events/delete', 'EventController@deleteMultipleData');
    Route::post('events/search', 'EventController@searchData');

    Route::apiResource('courses', 'CourseController');
    Route::post('courses/delete', 'CourseController@deleteMultipleData');
    Route::post('courses/search', 'CourseController@searchData');

    Route::apiResource('categories', 'CategoriesController');
    Route::post('categories/delete', 'CategoriesController@deleteMultipleData');
    Route::post('categories/search', 'CategoriesController@searchData');

    Route::apiResource('category-group', 'CategoryGroupController');
    Route::post('category-group/delete', 'CategoryGroupController@deleteMultipleData');
    Route::post('category-group/search', 'CategoryGroupController@searchData');

    Route::apiResource('solutions', 'SolutionsController');
    Route::get('getanalytics', 'SolutionsController@getAnalytics');
    Route::get('test2', 'Client\SolutionsController@test2');
    Route::get('getanalyticslocation', 'SolutionsController@get_country_city');
    Route::post('solutions/delete', 'SolutionsController@deleteMultipleData');
    Route::post('solutions/search', 'SolutionsController@searchData');
    Route::get('get-features', 'SolutionsController@get_feature');
    Route::get('get-companies', 'SolutionsController@get_companies');

    Route::get('getAllFeatures', 'SolutionsController@getAllFeatures');
    Route::get('getSingleFeature/{id}', 'SolutionsController@getSingleFeature');

    Route::apiResource('company', 'CompanyController');
    Route::post('company/delete', 'CompanyController@deleteMultipleData');
    Route::post('company/search', 'CompanyController@searchData');

    
    Route::apiResource('feature', 'FeatureController');
    Route::post('feature/delete', 'FeatureController@deleteMultipleData');
    Route::post('feature/search', 'FeatureController@searchData');
    
    Route::apiResource('projects', 'ProjectsController');
    Route::put('projects/update-data/{id}', 'ProjectsController@updateData');
    Route::post('projects/delete', 'ProjectsController@deleteMultipleData');
    Route::post('projects/search', 'ProjectsController@searchData');

    Route::post('projects/invite-merchant/{id}', 'ProjectsController@inviteMerchantsToAProject');
    // Route::put('projects/assign-agent/{id}', 'ProjectsController@updateAssignAgent');
    Route::get('projects/assign-agent/{id}', 'ProjectsController@getAssignedAgents');

    Route::apiResource('leads', 'LeadController');
    Route::post('leads/delete', 'LeadController@deleteMultipleData');
    Route::post('leads/search', 'LeadController@searchData');

    Route::post('leads/invite-merchant/{id}', 'LeadController@inviteMerchantsToAProject');
    // Route::put('projects/assign-agent/{id}', 'ProjectsController@updateAssignAgent');
    Route::get('leads/assign-agent/{id}', 'LeadController@getAssignedAgents');

    Route::apiResource('contacts', 'ContactsController');
    Route::post('contacts/delete', 'ContactsController@deleteMultipleData');
    Route::post('contacts/search', 'ContactsController@searchData');

    Route::apiResource('resellers', 'ResellersController');
    Route::post('resellers/delete', 'ResellersController@deleteMultipleData');
    Route::post('resellers/search', 'ResellersController@searchData');

    Route::apiResource('top-solutions', 'TopSolutionsController');
    Route::post('top-solutions/delete', 'TopSolutionsController@deleteMultipleData');
    Route::post('top-solutions/search', 'TopSolutionsController@searchData');

    Route::apiResource('dynamic-pages', 'DynamicPageController');
    Route::post('dynamic-pages/delete', 'DynamicPageController@deleteMultipleData');
    
    Route::apiResource('reviews', 'ReviewsController');
    Route::post('reviews/approve', 'ReviewsController@approveReview');
    Route::post('reviews/unapprove', 'ReviewsController@unapproveReview');
    
    // - Protected Routes
    Route::apiResource('cyberpal-reviews', 'CyberPalReviewController');
    Route::get('dashboard-records','DashboardController@index');
    
    Route::apiResource('images', 'ImagesControlller');

    Route::group(['prefix' => 'export'], function () {
        Route::get('leads', 'LeadController@export');
        Route::get('projects', 'ProjectsController@export');
        Route::get('single-leads/{id}', 'LeadController@exportSingle');
    });
    // });
});
