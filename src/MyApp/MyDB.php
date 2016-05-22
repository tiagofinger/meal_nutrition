<?php
namespace MyApp;
use Predis\Client;

class MyDB {
    public static $instance;
    function __construct() {}
    
    public static function getInstance() {
        try {
            if (!isset(self::$instance)) {
                self::$instance = new Client();
            }
        } catch (Exception $e) {
            echo "Couldn't connected to Redis";
            echo $e->getMessage();
        }
        
        return self::$instance;
    }
    
    public function get($tag) {
        try {
            $db = MyDB::getInstance();
            return $db->hgetall($tag);
        } catch (Exception $e) {
            echo "Couldn't get data to Redis";
            echo $e->getMessage();
        }
    }
    
    public function save($tag, array $data) {
        try {
            $db = MyDB::getInstance();
            $db->hmset($tag, $data);
        } catch (Exception $e) {
            echo "Couldn't save data to Redis";
            echo $e->getMessage();
        }
    }
    
    public function delete($val) {
        try {
            $db = MyDB::getInstance();
            $db->hdel($val);
        } catch (Exception $e) {
            echo "Couldn't insert data to Redis";
            echo $e->getMessage();
        }
    }
}