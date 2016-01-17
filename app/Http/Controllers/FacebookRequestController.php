<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Facebook\Facebook;
use Facebook\FacebookRequest;

class FacebookRequestController extends Controller
{
    public static function initApi($token){
        $fb = new Facebook([
            'app_id' => config('facebook.app_data')['app_id'],
            'app_secret' => config('facebook.app_data')['app_secret'],
            'default_graph_version' => 'v2.5',
        ]);
//        Api::init($fb->getApp()->getId(), $fb->getApp()->getSecret(), $token);
        return $fb;
    }

    public function executeGetRequest($url='/me/taggable_friends?fields=name,books,movies,education,events,location',$token='CAAOuHnrUBu0BANp78NYl2iCyNoAP2bZAI1SAHcxu8DNKr2nC6G4LdjZBQ78KTcELPlWXkl84YcMhzqDt6OE0g2mXjRbeI3RKZCV8hobG4oxIY3DB0gGUZBY1t6Ytse74uakMPoSVLhZC01m3kPbWhREJhnZAe11duSxwy79gP5fbj2CZCukbpyCWpYpVFlnBgcVsdZCTgIaAVwZDZD'){
        $fb=$this->initApi($token);
        $fb->setDefaultAccessToken($token);
        $request = new FacebookRequest($fb->getApp(), $fb->getDefaultAccessToken(), 'GET', $url);
        try {
            $response = $fb->getClient()->sendRequest($request); // dd($response);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $response=null; dd($e);
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $response=null; dd($e);
        }
        if($response == null) {
            return [];
        }

        $objects = json_decode($response->getBody());
        if(isset($objects->error)){
            return [];
        }
        $objects = (isset($objects->data)) ? $objects->data : $objects;

        dd($objects);
        return $objects;
    }
}
