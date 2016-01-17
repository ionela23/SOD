<?php

namespace App\Http\Controllers;

use App\FacebookAccount;
use Facebook\Facebook;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class FacebookAuthController extends Controller
{
    public function initFb(){
        return new Facebook(config('facebook.app_data'));
    }

    public function getAuthUrl(){
        $fb = $this->initFb();
        try{
            $helper = $fb->getRedirectLoginHelper();
            $permissions = config('facebook.permissions');
            $permissions = explode(",",$permissions);
            return $helper->getLoginUrl(url('login'), $permissions);

        }catch(\Exception $e){
            return '';
        }
    }

    public function loginCallback($code){
        $fb = $this->initFb();
        $helper = $fb->getRedirectLoginHelper();
        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Exception $e) {
            return null;
        }
        $oAuth2Client = $fb->getOAuth2Client();
        $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        $fb->setDefaultAccessToken($longLivedAccessToken);

        return $fb;
    }



    public function authenticate(Request $request){
        $code = $request->input('code');
        $fb = $this->loginCallback($code);
        if($fb==null) {
            return redirect('/')->withErrors(['Error: ', 'User data cannot be read.']);
        }
        try {
            $response = $fb->get('/me?fields='.config('facebook.graph_user_parameters'));
            $userNode = $response->getGraphUser();
        } catch(\Exception $e) {
            // When Graph returns an error
            return redirect('/')->withErrors(['Graph returned an error: ', $e->getMessage()]);
        }

        $oAuth2Client = $fb->getOAuth2Client();
        $token = $fb->getDefaultAccessToken();

//        dd($token);

        $user = Auth::user();
        $fbAccount = new FacebookAccount();
        $fbAccount->access_token = serialize($token);
        $fbAccount->link = $userNode->getLink();
        $fbAccount->account_id = $userNode->getId();
        $user->facebookAccounts()->save($fbAccount);
    }
}
