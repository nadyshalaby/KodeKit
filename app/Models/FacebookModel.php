<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use App\Libs\Statics\Config;
use App\Libs\Statics\Func;
use App\Libs\Statics\Hash;
use Carbon\Carbon;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookModel extends Model {

    public $fb;
    public $helper;
    public $accessToken;
    public $table = 'users';
    public $defaults;
    public $insertable = [
        'f_name',
        'l_name',
        'email',
        'pass',
        'remember_me',
        'active',
        'avatar',
        'national_id',
        'slug',
        'role',
        'created_at',
        'updated_at',
    ];

    public function __construct() {
        $this->fb = new Facebook([
            'app_id' => Config::extra('social.facebook.app_id'),
            'app_secret' => Config::extra('social.facebook.app_secret'),
            'default_graph_version' => Config::extra('social.facebook.default_graph_version'),
        ]);
        $this->defaults = [
            'role' => 'normal',
            'national_id' => '',
            'pass' => 'nothing',
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function setLoginHelper() {

        $this->helper = $this->fb->getRedirectLoginHelper();

        try {
            $this->accessToken = $this->helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error  
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues  
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
    }

    public function updateUserInformation() {

        if (isset($this->accessToken)) {
            try {
                // Returns a `Facebook\FacebookResponse` object
                $res1 = $this->fb->get('/me/picture?type=large&redirect=false', $this->accessToken->getValue());
                $res2 = $this->fb->get('/me?fields=id,name,email,gender,cover,location', $this->accessToken->getValue());
            } catch (FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch (FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }

            // Fetched by the Graph API
            $picture = $res1->getGraphUser();
            $details = $res2->getGraphUser();


            // Fetching the fields
            $email = $details->getProperty('email');
            if (!$this->isUserExist($email)) {

                $id = $details->getProperty('id');
                $image = $picture->getProperty('url');
                $name = $details->getProperty('name');
                $gender = $details->getProperty('gender');
                $cover = $details->getProperty('cover')->getField('source');
                $address = $details->getProperty('location')->getField('name');

                // Fetched normaliy
                $slug = $this->getSlug($name);
                $name = explode(' ', $name);
                $f_name = $name[0];
                $l_name = $name[count($name) - 1];
                $remember_me = $this->getRememberMe();

                $user_columns = [
                    'f_name' => $f_name,
                    'l_name' => $l_name,
                    'email' => $email,
                    'slug' => $slug,
                    'remember_me' => $remember_me,
                    'avatar' => $image,
                ];

                $this->setUserSlug($slug);
                $this->setUserRememberMe($remember_me);
                return self::insert($user_columns);
            } else {
                $user = self::first('email = ?', [$email]);
                
                $this->setUserSlug($user->slug);
                $this->setUserRememberMe($user->remember_me);
                
                return true;
            }
        }
        return false;
    }

    //Getters and Setters
    public function getUserSlug() {
        return $this->userSlug;
    }

    public function setUserSlug($userSlug) {
        $this->userSlug = $userSlug;
    }
    
    public function getUserRememberMe() {
        return $this->userRememberMe;
    }

    public function setUserRememberMe($userRememberMe) {
        $this->userRememberMe = $userRememberMe;
    }

    // Helper methods
    private function getSlug($name) {
        $slug = slugify($name);
        $new_slug = $slug;
        while (self::findBy(['slug' => $new_slug])) {
            $new_slug = $slug . '-' . rand(0, 99999);
        }
        return $new_slug;
    }

    private function getRememberMe() {
        $remember_me = Hash::unique(30);
        while (self::findBy(['remember_me' => $remember_me])) {
            $remember_me = Hash::unique(30);
        }
        return $remember_me;
    }

    private function isUserExist($email) {
        return self::findBy(['email' => $email]);
    }

}
