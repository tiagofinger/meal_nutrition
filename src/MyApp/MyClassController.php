<?php
namespace MyApp;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Exception;
 
class MyClassController implements ControllerProviderInterface {
    // Routers
    public function connect(Application $app) {
        $factory = $app['controllers_factory'];
        $factory->get('/','MyApp\MyClassController::home');
        $factory->get('/food', function () use ($app) {
            return MyClassController::listFood();
        });
        $factory->get('/food/{ndbno}', function ($ndbno) use ($app) {
            return MyClassController::food($ndbno);
        });
        $factory->get('/nutrient', function () use ($app) {
            return MyClassController::listNutrient();
        });
        $factory->get('/nutrient/{nutrient}', function ($nutrient) use ($app) {
            return MyClassController::nutrient($nutrient);
        });
        $factory->get('/recipe/{id_recipe}', function ($id_recipe) use ($app) {
            return MyClassController::getRecipe($id_recipe);
        });
        $factory->post('/recipe', function (Request $request) use ($app) {
            try {
                if (empty($request->get('id_recipe'))) {
                    throw new Exception('You did not send the id_recipe parameter.', Response::HTTP_BAD_REQUEST);
                }
                if (empty($request->get('recipe_name'))) {
                    throw new Exception('You did not send the recipe_name parameter.', Response::HTTP_BAD_REQUEST);
                }
                if (empty($request->get('description'))) {
                    throw new Exception('You did not send the description parameter.', Response::HTTP_BAD_REQUEST);
                }
                if (empty($request->get('ndbno'))) {
                    throw new Exception('You did not send the ndbno parameter.', Response::HTTP_BAD_REQUEST);
                }
                if (empty($request->get('unit'))) {
                    throw new Exception('You did not send the unit parameter.', Response::HTTP_BAD_REQUEST);
                }
                if (!is_array($request->get('ndbno'))) {
                    throw new Exception('You need send the ndbno parameter like array.', Response::HTTP_BAD_REQUEST);
                }
                if (!is_array($request->get('unit'))) {
                    throw new Exception('You need send the unit parameter like array.', Response::HTTP_BAD_REQUEST);
                }
                $ndbno = $request->get('ndbno');
                $unit = $request->get('unit');
                
                $food = array();
                foreach ($ndbno as $i => $val) {
                    $food[$i]['ndbno'] = $val;
                }
                foreach ($unit as $i => $val) {
                    $food[$i]['unit'] = $val;
                }

                $tag = $request->get('id_recipe');
                $data = array(
                    'recipe_name' => $request->get('recipe_name'),
                    'description' => $request->get('description'),
                    'foods' => json_encode($food)
                );
                $code = MyClassController::saveRecipe($tag, $data);
                
                $msg = 'Data saved successfully!';
                if ($code == 400) {
                    $msg = 'Data was not saved. Please verify, if your variables are correct.';
                }
            } catch (Exception $e) {                
                $code = $e->getCode();
                $msg = $e->getMessage();
            }
            return new Response($msg, $code);
        });

        return $factory;
    }

    public function home() {
        return '<h1>Back-end challenge: Meal Nutrition API</h1>
                <h2>Objective</h2>
                <p>The goal of this challenge is to build a small <em>RESTful API</em> allowing users to save recipes and to get their aggregate nutrition information based on the nutrition information of each ingredient, using the USDA Nutrient Database API (https://ndb.nal.usda.gov/ndb/doc/index). The API should be accessible from any API client (cli/curl, Postman, etc).</p>
                <p>Deliver your submission in a .zip file, with instructions on how to run & use it in a README file. Make sure it can be run as easily as possible.</p>
                <h3>Bonus</h3>
                <p>Any related feature you think would be cool or useful.</p>';
    }
    
    public static function listFood() {
        try {
            $code = Response::HTTP_OK;
            $param = array('format' => 'json', 'lt' => 'f', 'sort' => 'n', 'api_key' => 'DEMO_KEY');
            $data = json_decode(MyCurl::getData($param, MyCurl::URL_LIST_FOOD));
            if (!empty($data->errors)) {
                $code = $data->errors->error[0]->status;
                throw new Exception('Couldn\'t get food\'s list.', $code);
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $data = array('error' => $e->getMessage());
        }
        return new Response(json_encode($data), $code);
    }

    public static function food($ndbno) {
        try {
            $code = Response::HTTP_OK;
            $param = array('ndbno' => "$ndbno", 'type' => 'f', 'format' => 'json', 'api_key' => 'DEMO_KEY');
            $data = json_decode(MyCurl::getData($param, MyCurl::URL_FOOD));
            if (!empty($data->errors)) {
                $code = $data->errors->error[0]->status;
                throw new Exception('Couldn\'t get the food.', $code);
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $data = array('error' => $e->getMessage());
        }
        return new Response(json_encode($data), $code);
    }
    
    public static function listNutrient() {
        try {
            $code = Response::HTTP_OK;
            $param = array('format' => 'json', 'lt' => 'n', 'sort' => 'n', 'api_key' => 'DEMO_KEY');
            $data = json_decode(MyCurl::getData($param, MyCurl::URL_LIST_NUTRIENT));
            if (!empty($data->errors)) {
                $code = $data->errors->error[0]->status;
                throw new Exception('Couldn\'t get food\'s list.', $code);
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $data = array('error' => $e->getMessage());
        }
        return new Response(json_encode($data), $code);
    }

    public static function nutrient($nutrient) {
        try {
            $code = Response::HTTP_OK;
            $nutrients = implode('&nutrients=', explode(',', $nutrient));
            $param = array('nutrients' => $nutrients, 'format' => 'json', 'api_key' => 'DEMO_KEY');
            $data = json_decode(MyCurl::getData($param, MyCurl::URL_NUTRIENT));
            if (!empty($data->errors)) {
                $code = $data->errors->error[0]->status;
                throw new Exception('Couldn\'t get the nutrients.', $code);
            }
        } catch (Exception $e) {
            $code = $e->getCode();
            $data = array('error' => $e->getMessage());
        }
      return new Response(json_encode($data), $code);
    }

    public static function saveRecipe($tag, $data) {
        try {
            $db = new MyDB();
            $db->save($tag, $data);
            $code = Response::HTTP_CREATED;
        } catch (Exception $e) {
            echo "Couldn't save the recipe.";
            echo $e->getMessage();
            $code = Response::HTTP_BAD_REQUEST;
        }
        return $code;
    }

    public static function getRecipe($tag) {
        try {
            $code = Response::HTTP_OK;
            $db = new MyDB();
            $data = $db->get($tag);
            if (empty($data)) {
                throw new Exception('No data', Response::HTTP_OK);
            } 
            $data['foods'] = json_decode($data['foods']);    
        } catch (Exception $e) {
            $code = $e->getCode();
            $data = array('error' => $e->getMessage());
        }
        return new Response(json_encode($data), $code);
    }
}