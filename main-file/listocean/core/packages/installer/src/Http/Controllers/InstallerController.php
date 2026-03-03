<?php

namespace Xgenious\Installer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Xgenious\Installer\Helpers\InstallationHelper;

class InstallerController extends Controller
{

    public function index()
    {
        if(!InstallationHelper::isInstallerNeeded()){
            return url('/');
        }
        return view('installer::installer.index');
    }

    public function verifyPurchase(Request $request)
    {
        // Purchase verification against the external Xgenious license server is
        // intentionally bypassed. The original flow downloaded a SQL dump from
        // that server and imported it, which overwrote our custom database schema
        // with the original Codecanyon demo data.
        // Installation now uses Laravel migrations + seeders (see insert_database_sql_file).
        return response()->json([
            'type' => 'success',
            'msg'  => 'Verification Success'
        ]);
    }

    public function checkDatabase(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'db_name' => 'required',
            'db_username' => 'required',
            'db_host' => 'required',
            'db_password' => 'nullable',
        ]);
        if($validation->fails()){
            return response()->json(['type' => 'danger','msg' => 'make sure you have enter all the database details']);
        }
        $db_connection = InstallationHelper::check_database_connection($request->db_host,$request->db_name,$request->db_username,$request->db_password);
        if($db_connection['status'] === false){
            return response()->json(['type' => 'danger', 'msg' => $db_connection['msg']]);
        }
        // Implement database connection check
        return response()->json(['type' => 'success', 'msg' => 'Database connection successful']);
    }

    public function install(Request $request)
    {
        $validation = Validator::make($request->all(),[
            'db_name' => 'required',
            'db_username' => 'required',
            'db_host' => 'required',
            'db_password' => 'nullable',
            'admin_email' => 'required',
            'admin_password' => 'required',
            'admin_username' => 'required',
            'admin_name' => 'required',
        ]);
        if($validation->fails()){
            return response()->json(['type' => 'danger','msg' => 'make sure you have enter all the database and admin informtaion']);
        }

        $keyValuePairs = [
            'APP_DEBUG' => 'false',
            'APP_URL' => url('/'),
            'DB_HOST' => $request->db_host,
            'DB_DATABASE' => $request->db_name,
            'DB_USERNAME' => $request->db_username,
            'DB_PASSWORD' => is_null($request->db_password) ? "" : $request->db_password,
            'BROADCAST_DRIVER' => config('installer.broadcast_driver','log'),
            'CACHE_DRIVER' => config('installer.cache_driver','file'),
            'QUEUE_CONNECTION' => config('installer.queue_connection','sync'),
            'MAIL_PORT' => config('installer.mail_port','587'),
            'MAIL_ENCRYPTION' => config('installer.mail_encryption','tls'),
        ];
        $tenant_msg = '';
        if(config('installer.multi_tenant',false)){
            $keyValuePairs['CENTRAL_DOMAIN'] = $request->getHost();
            $keyValuePairs['TENANT_DATABASE_PREFIX'] = strtolower(env('APP_NAME')).'_tenant_db_';
            $tenant_msg = 'do not forget to setup wildcard subdomain in order to create subdomain by the system automatically <a target="_blank" href="https://docs.xgenious.com/docs/nazmart-multi-tenancy-ecommerce-platform-saas/wildcard-subdomain-configuration/"><i class="las la-external-link-alt"></i></a>';
        }
        //generate env file based on user and config file data
        InstallationHelper::generate_env_file($keyValuePairs);
        $db_host = $request->db_host;
        $db_name = $request->db_name;
        $db_user = $request->db_username;
        $db_pass = $request->db_password;
        // write helper for insert sql file
        $db_import = InstallationHelper::insert_database_sql_file($db_host,$db_name,$db_user,$db_pass);
        if($db_import['type'] === 'danger'){
            InstallationHelper::reverse_to_default_env();
            return response()->json(['type' => 'danger', 'msg' => $exception->getMessage()]);
        }
        $admin_email = $request->admin_email;
        $admin_password = $request->admin_password;
        $admin_username =  $request->admin_username;
        $admin_name = $request->admin_name;

        //write helper for create admin using the admin info
        InstallationHelper::create_admin($admin_email,$admin_password,$admin_username,$admin_name,$db_host,$db_name,$db_user,$db_pass);
        $msg = 'Installation Successful, if you still see install notice in your website, clear your browser cache ';
        $msg .= '<a href="'.url('/').'">visit website</a> <p>'.$tenant_msg.'. setup cron job for subscription system work properly here is article for it <a target="_blank" href="https://docs.xgenious.com/docs/nazmart-multi-tenancy-ecommerce-platform-saas/cron-job/"><i class="las la-external-link-alt"></i></a></p>'; //write instruction message for multi tenant or normal script
        return response()->json(['type' => 'success', 'msg' => $msg]);
    }

    public function checkDatabaseExists()
    {
        // We no longer use a downloaded database.sql file.
        // Installation runs php artisan migrate:fresh --seed instead.
        return response()->json(['type' => 'success', 'msg' => 'Ready to install via migrations']);
    }
}