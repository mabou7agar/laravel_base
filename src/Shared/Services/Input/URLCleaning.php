<?php

namespace  BasePackage\Shared\Services\Input;

    /**
     * Created by Osama Khaled.
     * User: osama@qdrah.com
     * Date: 8/26/17
     * Time: 12:39 PM
     */


class URLCleaning
{
    public const FACEBOOK_REGEX_PATTERN = '/(.*?facebook\.com|^)(\/pages\/[^\/]+\/|\/people\/[^\/]+\/|\/profile\.php\?id\=|\/[^\/]+\-|\/)?(\d{10,})($|\/.*$|\?.*$|\&.*$|\/\?.*$)|(.*?facebook\.com\/|^)([a-zA-Z0-9_\.\-]+)($|\/.*$|\?.*$|\/\?.*$)/u';
    public const TWITTER_REGEX_PATTERN = '/^(@|.*?twitter\.com\/)?([a-zA-Z0-9_\-]+)([\/\?].*)?$/u';
    public const INSTAGRAM_REGEX_PATTERN = '/^(@|.*?instagram\.com\/)?([a-zA-Z0-9_\-\.]+)([\/\?].*)?$/u';
    public const SNAPCHAT_REGEX_PATTERN = '/^(@|.*?snapchat\.com\/add\/)?([a-zA-Z0-9_\.\-]+)([\/\?].*)?$/u';
    public const TIKTOK_REGEX_PATTERN = '/^(@|.*?tiktok\.com\/)?([@a-zA-Z0-9_\.\-]+)([\/\?].*)?$/u';
    public const WEBSITE_REGEX_PATTERN = '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;\"\':<]|\.\s|$)#i';

    public static function cleanFacebookUrl($url)
    {
        $url = trim($url);

        $facebook_input = trim(str_replace('@', '', urldecode($url)));

        preg_match(self::FACEBOOK_REGEX_PATTERN, $facebook_input, $facebook_matches);

        if (!empty($facebook_matches[3]) && !in_array($facebook_matches[3], ['home.php', 'pages', 'groups', 'public'])) {
            $result = StringCleaner::cleanSpaces($facebook_matches[3]);
        } elseif (!empty($facebook_matches[6]) && !in_array($facebook_matches[6], ['home.php', 'pages', 'groups', 'public'])) {
            $result = StringCleaner::cleanSpaces($facebook_matches[6]);
        } else {
            $result = null;
        }

        return $result;
    }

    public static function cleanTwitterUrl($url)
    {
        $url = trim($url);

        $twitter_input = trim(str_replace('@', '', urldecode($url)));

        preg_match(self::TWITTER_REGEX_PATTERN, $twitter_input, $twitter_matches);

        if (isset($twitter_matches[2])) {
            $result = StringCleaner::cleanSpaces($twitter_matches[2]);
        } else {
            $result = null;
        }

        return $result;
    }

    public static function cleanInstagramUrl($url)
    {
        $url = trim($url);

        $instagram_input = trim(str_replace('@', '', urldecode($url)));

        preg_match(self::INSTAGRAM_REGEX_PATTERN, $instagram_input, $instagram_matches);

        $result = null;

        if (isset($instagram_matches[2])) {
            $result = StringCleaner::cleanSpaces($instagram_matches[2]);
        }

        return $result;
    }

    public static function cleanSnapchatUrl($url)
    {
        $url = trim($url);

        $snapchat_input = trim(str_replace('@', '', urldecode($url)));

        preg_match(self::SNAPCHAT_REGEX_PATTERN, $snapchat_input, $snapchat_matches);

        if (isset($snapchat_matches[2])) {
            $result = StringCleaner::cleanSpaces($snapchat_matches[2]);
        } else {
            $result = null;
        }

        return $result;
    }

    public static function cleanTiktokUrl($url)
    {
        $url = trim($url);

        $tiktok_input = trim(urldecode($url));

        preg_match(self::TIKTOK_REGEX_PATTERN, $tiktok_input, $tiktok_matches);

        if (isset($tiktok_matches[2])) {
            $result = StringCleaner::cleanSpaces($tiktok_matches[2]);
            $result = str_replace('@', '', $result);
        } else {
            $result = null;
        }

        return $result;
    }

    public static function cleanWebsiteUrl($url)
    {
        $url = trim(strtolower($url));

        if (strpos($url, 'http://') === false) {
            if (strpos($url, 'https://') === false) {
                $url .= 'http://' . $url;
            }
        }

        $website_input = trim(str_replace('@', '', urldecode($url)));

        preg_match(self::WEBSITE_REGEX_PATTERN, $website_input, $website_matches);

        if (isset($website_matches[1])) {
            $result = StringCleaner::cleanSpaces($website_matches[1]);
        } else {
            $result = null;
        }

        return $result;
    }
}
