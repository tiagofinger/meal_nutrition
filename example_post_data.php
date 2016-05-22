<?php
/* 
 * Saving data example
 */
$url = 'http://localhost/meal_nutrition/web/recipe';
$fields = array(
    'id_recipe' => 175,
    'recipe_name' => urlencode('Receita Finger 2'),
    'description' => urlencode('esta é minha receita'),
    'recipe' => array(
        array('ndbno' => '01009', 'unit' => 'gr'),
        array('ndbno' => '010010', 'unit' => 'kg')
    )
);

//url-ify the data for the POST
$fields_string = '';
foreach($fields as $key => $value) { 
    if ('recipe' == $key) {
        foreach ($value as $key2 => $value2) {
            $fields_string .= 'ndbno[]='.$value2['ndbno'].'&unit[]='.$value2['unit'].'&'; 
        }
    } else {
        $fields_string .= $key.'='.$value.'&'; 
    }
}
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);
echo $result;