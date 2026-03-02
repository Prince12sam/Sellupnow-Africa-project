<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Advertisement;
use App\Models\Backend\Category;
use App\Models\Backend\ChildCategory;
use App\Models\Backend\Language;
use App\Models\Backend\Listing;
use App\Models\Backend\MediaUpload;
use App\Models\Backend\Notice;
use App\Models\Backend\SubCategory;
use App\Models\Frontend\Visitor;
use Illuminate\Http\Request;
use App\Models\Backend\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Blog\app\Models\Blog;
use Modules\Blog\app\Models\Tag;
use Modules\Brand\app\Models\Brand;
use Modules\CountryManage\app\Models\City;
use Modules\CountryManage\app\Models\Country;
use Modules\CountryManage\app\Models\State;
use Modules\Membership\app\Models\MembershipHistory;
use Modules\Membership\app\Models\UserMembership;
use Modules\NewsLetter\app\Models\NewsLetter;
use Modules\SupportTicket\app\Models\Ticket;
use Modules\Wallet\app\Models\Wallet;
use Modules\Wallet\app\Models\WalletHistory;

class AdminDashboardController extends Controller
{
    public function __construct() {
        $this->middleware('auth:admin');
    }

    public function adminDashboard()
    {
        // membership module
        $module_check = false;
        if (moduleExists('Membership')){
          if (membershipModuleExistsAndEnable('Membership')){
              $module_check = true;
             $total_user_membership = UserMembership::count();
              $total_membership_earning = MembershipHistory::where('payment_status', 'complete')
                  ->where('payment_gateway', '!=', 'Trial')
                  ->where('price', '!=', '0')
                  ->sum('price');
          }
        }

        $wallet_module_check = false;
        if (moduleExists('Wallet')){
          if (membershipModuleExistsAndEnable('Wallet')){
              $wallet_module_check = true;
             $total_user_wallet = Wallet::count();
          }
        }

        $dashboardData = [
            ['title' => __('Total Admins'),  'route' => 'admin.all','value' => Admin::count()],
            ['title' => __('Total Users'), 'route' => 'admin.user.all', 'value' => User::count()],
            ['title' => __('Total Listings'), 'value' => Listing::count()],
            ['title' => __('Total Guest Listings'),  'route' => 'admin.guest.all.listings','value' => Listing::guestListings()->count()],
            ['title' => __('Total User Listings'),  'route' => 'admin.user.all.listings','value' => Listing::userListings()->count()],
            ['title' => __('Total Admin Listings'),  'route' => 'admin.all.listings','value' => Listing::adminListings()->count()],
            ['title' => __('Total Blogs'), 'route' => 'admin.all.blog', 'value' => Blog::count()],
            ['title' => __('Total Brand'), 'route' => 'admin.brand.all', 'value' => Brand::count()],
            ['title' => __('Total Categories'), 'route' => 'admin.category', 'value' => Category::count()],
            ['title' => __('Total Subcategories'),  'route' => 'admin.subcategory','value' => SubCategory::count()],
            ['title' => __('Total Child Categories'), 'route' => 'admin.child.category', 'value' => ChildCategory::count()],
            ['title' => __('Total Countries'),  'route' => 'admin.country.all','value' => Country::count()],
            ['title' => __('Total States'),  'route' => 'admin.state.all','value' => State::count()],
            ['title' => __('Total Cities'),  'route' => 'admin.city.all','value' => City::count()],
            ['title' => __('Total Tags'),  'route' => 'admin.blog.tags','value' => Tag::count()],
            ['title' => __('Total 	Tickets'),  'route' => 'admin.ticket','value' => Ticket::count()],
            ['title' => __('Total 	Newsletter'), 'route' => 'admin.newsletter.index', 'value' => NewsLetter::count()],
            ['title' => __('Total 	Advertisements'), 'route' => 'admin.advertisement', 'value' => Advertisement::count()],
            ['title' => __('Total 	Languages'), 'route' => 'admin.languages', 'value' => Language::count()],
            ['title' => __('Total 	Media Images'), 'route' => 'admin.upload.media.images.page', 'value' => MediaUpload::count()],
            ['title' => __('Total 	Notice'), 'route' => 'admin.all.notice', 'value' => Notice::count()],
        ];

        // Conditionally add the User Membership data if the module is enabled
        if ($module_check === true) {
            $dashboardData[] = ['title' => __('Total Member'), 'route' => 'admin.user.membership.all', 'value' => $total_user_membership];
            $dashboardData[] = ['title' => __('Total Membership Earnings'), 'value' => float_amount_with_currency_symbol($total_membership_earning)];
        }

        if ($wallet_module_check === true) {
            $dashboardData[] = ['title' => __('Total Wallet User'), 'route' => 'admin.wallet.lists', 'value' => $total_user_wallet];
        }

        $total_user = User::count();
        $total_listings = Listing::count();
        $recent_users = User::latest()->take(5)->get();
        $recent_listings = Listing::latest()->take(5)->get();

        $page = 1; // Current page number
        $pageSize = 900; // Number of records per page
        $offset = ($page - 1) * $pageSize;

        $visitors = Visitor::select('city', 'country_code', 'latitude', 'longitude', 'country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('city', 'country_code', 'latitude', 'longitude', 'country')
            ->offset($offset)
            ->limit($pageSize)
            ->get();


        $countryCodes = Visitor::select('country_code')
            ->whereNotNull('country')
            ->offset($offset)
            ->limit($pageSize)
            ->get();

        return view('backend.pages.dashboard.dashboard', compact(
            'dashboardData',
            'total_user',
            'recent_users',
            'recent_listings',
            'total_listings',
            'visitors',
            'countryCodes'
        ));
    }

    public function getUserData(Request $request) {
        $interval = $request->input('interval');

        switch ($interval) {
            case '0': // Weekly
                // Example logic to fetch weekly user data
                $data = User::selectRaw('WEEK(created_at) as week, count(*) as total')
                    ->groupBy('week')
                    ->pluck('total', 'week')->toArray();
                break;
            case '1': // Monthly
                // Example logic to fetch monthly user data
                $data = User::selectRaw('MONTH(created_at) as month, count(*) as total')
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();
                break;
            case '2': // Yearly
                // Example logic to fetch yearly user data
                $data = User::selectRaw('YEAR(created_at) as year, count(*) as total')
                    ->groupBy('year')
                    ->pluck('total', 'year')->toArray();
                break;
            case '3': // Daily
                // Example logic to fetch daily user data
                $data = User::selectRaw('DAYOFWEEK(created_at) as day, count(*) as total')
                    ->groupBy('day')
                    ->pluck('total', 'day')->toArray();
                break;
            case '4': // Hourly
                // Example logic to fetch hourly user data
                $data = User::selectRaw('HOUR(created_at) as hour, count(*) as total')
                    ->groupBy('hour')
                    ->pluck('total', 'hour')->toArray();
                break;
            default:
                $data = [];
                $uniqueYears = [];
                break;
        }


        return response()->json($data);
    }

    public function getListingData(Request $request) {
        $interval = $request->input('interval');

        switch ($interval) {
            case '0': // Weekly
                // Example logic to fetch weekly user data
                $data = Listing::selectRaw('WEEK(created_at) as week, count(*) as total')
                    ->groupBy('week')
                    ->pluck('total', 'week')->toArray();
                break;
            case '1': // Monthly
                // Example logic to fetch monthly user data
                $data = Listing::selectRaw('MONTH(created_at) as month, count(*) as total')
                    ->groupBy('month')
                    ->pluck('total', 'month')->toArray();
                break;
            case '2': // Yearly
                // Example logic to fetch yearly user data
                $data = Listing::selectRaw('YEAR(created_at) as year, count(*) as total')
                    ->groupBy('year')
                    ->pluck('total', 'year')->toArray();
                break;
            case '3': // Daily
                // Example logic to fetch daily user data
                $data = Listing::selectRaw('DAYOFWEEK(created_at) as day, count(*) as total')
                    ->groupBy('day')
                    ->pluck('total', 'day')->toArray();
                break;
            case '4': // Hourly
                // Example logic to fetch hourly user data
                $data = Listing::selectRaw('HOUR(created_at) as hour, count(*) as total')
                    ->groupBy('hour')
                    ->pluck('total', 'hour')->toArray();
                break;
            default:
                $data = [];
                $uniqueYears = [];
                break;
        }


        return response()->json($data);
    }


    public function darkModeToggle(Request $request){

        $data = get_static_option('site_admin_dark_mode');
        if($request->mode == 'off' || empty($data)){
            update_static_option('site_admin_dark_mode','on');
        }
        if($request->mode == 'on'){
            update_static_option('site_admin_dark_mode','off');
        }
        return response()->json(['status'=>'done']);
    }


}
