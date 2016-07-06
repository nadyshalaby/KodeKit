<?php

namespace App\Classes;

use App\Libs\Statics\Config;
use App\Libs\Statics\Url;
use Facebook\Facebook;

class Facebook {

    private $helper;
    private $permissions = [
        'public_profile',
        'user_friends',
        'email',
        'user_about_me',
        'user_actions.books',
        'user_actions.fitness',
        'user_actions.music',
        'user_actions.news',
        'user_actions.video',
        'user_birthday',
        'user_education_history',
        'user_events',
        'user_games_activity',
        'user_hometown',
        'user_likes',
        'user_location',
        'user_managed_groups',
        'user_photos',
        'user_posts',
        'user_relationships',
        'user_relationship_details',
        'user_religion_politics',
        'user_tagged_places',
        'user_videos',
        'user_website',
        'user_work_history',
        'read_custom_friendlists',
        'read_insights',
        'read_audience_network_insights',
        'read_page_mailboxes',
        'manage_pages',
        'publish_pages',
        'publish_actions',
        'rsvp_event',
        'pages_show_list',
        'pages_manage_cta',
        'pages_manage_instant_articles',
        'ads_read',
        'ads_management',
        'pages_messaging',
        'pages_messaging_phone_number',]; // optional

    /*
      notice all premissions provided:
      $premissions = ['public_profile',
      'user_friends',
      'email',
      'user_about_me',
      'user_actions.books',
      'user_actions.fitness',
      'user_actions.music',
      'user_actions.news',
      'user_actions.video',
      'user_actions:{app_namespace}',
      'user_birthday',
      'user_education_history',
      'user_events',
      'user_games_activity',
      'user_hometown',
      'user_likes',
      'user_location',
      'user_managed_groups',
      'user_photos',
      'user_posts',
      'user_relationships',
      'user_relationship_details',
      'user_religion_politics',
      'user_tagged_places',
      'user_videos',
      'user_website',
      'user_work_history',
      'read_custom_friendlists',
      'read_insights',
      'read_audience_network_insights',
      'read_page_mailboxes',
      'manage_pages',
      'publish_pages',
      'publish_actions',
      'rsvp_event',
      'pages_show_list',
      'pages_manage_cta',
      'pages_manage_instant_articles',
      'ads_read',
      'ads_management',
      'pages_messaging',
      'pages_messaging_phone_number',];
     */

    function __construct() {
        $fb = new Facebook([
            'app_id' => Config::extra('social.facebook.app_id'),
            'app_secret' => Config::extra('social.facebook.app_secret'),
            'default_graph_version' => Config::extra('social.facebook.default_graph_version'),
        ]);

        $this->helper = $fb->getRedirectLoginHelper();
    }

    public function getLoginUrl() {
        return $this->helper->getLoginUrl(route('facebook'), $this->permissions);
    }

}
