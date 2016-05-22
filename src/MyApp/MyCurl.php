<?php
namespace MyApp;

class MyCurl {
    const URL_FOOD = 'http://api.nal.usda.gov/ndb/reports/?';
    const URL_NUTRIENT = 'http://api.nal.usda.gov/ndb/nutrients/?';
    const URL_LIST_FOOD = 'http://api.nal.usda.gov/ndb/list?';
    const URL_LIST_NUTRIENT = 'http://api.nal.usda.gov/ndb/list?';
    function __construct () {}
    
    public static function getData(array $param, $url = MyCurl::URL_FOOD) {
        $url .= urldecode(http_build_query($param));

        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)'
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        
        return $resp;
    }
    
    public static function saveData($url, array $data) {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ));
        // Send the request & save response to $resp
        $resp = curl_exec($curl);
        // Close request to clear up some resources
        curl_close($curl);
        return $resp;
    }
}