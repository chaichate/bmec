<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
// header('Access-Control-Allow-Origin: *');  

define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

$basepath = realpath(dirname(__FILE__) . '/..');
define("BASEPATH", $basepath);
require_once(BASEPATH . '/application/config/database.php');
$DBCONECT = $db ;


$servername = "localhost";
$username = "zchate_lotto";
$password = "50J0bvIN";
$dbname = "zchate_lotto";


// Create connection
// $db = new mysqli($servername, $username, $password ,$dbname);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } 

$settings = require './src/settings.php';
$app = new \Slim\App();
$container = $app->getContainer();


$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


$app->get('/', function (Request $req,  Response $res, $args = []) {
    return $res->withStatus(400)->write('Bad Request');
});

$app->get('/emplooyee', function (Request $request, Response $response) {
    // $id = $request->getAttribute('id');
    $id = $request->getQueryParams()['id']; 
    // $response->getBody()->write("Hello, $id");

    $data = array('name' => 'Bob', 'age' => 40);
    $response = $response->withJson($data);

    

    return $response;
});
$app->run();



// $app->post('/login', function () use ($app, $db) {

//      // Update book identified by $args['id']
//      $username =$app->request->post('username');
//      $password =md5($app->request->post('password'));

//      if(!empty($username) &&  !empty($password) )
//      {
//         $sql = "SELECT * FROM tb_empooyee WHERE user ='{$username}' && password = '{$password}' ";
//         $result = $db->query($sql);
//         if ($result->num_rows > 0) {
//             $row = $result->fetch_assoc();
//             $arr = array("status"=>"success" , 'msg' => "login success" , 'count' => $result->num_rows , 'data'=>  $row  );
//         } else {
//             $arr = array("status"=>"fail" , 'msg' => "cannot login");
//         }

//      }
//      else
//      {
//         $arr = array("status"=>"fail" , 'msg' => "cannot login");
//      }


//     echo json_encode($arr);
// });

// $app->get('/emplooyee/:id', function () use ($id) {
    
//     echo $id;

//     var_dump($id);
//     echo "xxx";
    
// });
