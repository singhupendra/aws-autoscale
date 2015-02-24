<?php
error_reporting(E_ALL); 
require __DIR__.'/vendor/autoload.php';
 
use Aws\Sns\MessageValidator\Message;
use Aws\Sns\MessageValidator\MessageValidator;
use Guzzle\Http\Client;
//use \Aws\Ec2\Ec2Client;

// Make sure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//    http_response_code(405);
header("HTTP/1.1 405 Error");
    die;
}
 
try {
    // Create a message from the post data and validate its signature
    $message = Message::fromRawPostData();
    $validator = new MessageValidator();
    $validator->validate($message);
} catch (Exception $e) {
    // Pretend we're not here if the message is invalid
   // http_response_code(404);
    header("HTTP/1.1 404 Not Found");
    die;
}
 
if ($message->get('Type') === 'SubscriptionConfirmation') {
    // Send a request to the SubscribeURL to complete subscription
      $client = new Guzzle\Http\Client();
      $client->get($message->get('SubscribeURL'))->send();
}
//Get the Instance ID 
$data = json_decode($message->get('Message'),true);
$instanceId = $data['EC2InstanceId'];
//Get the AutoScale event 
$event = $data['Event'];
$lifecycle =$data['LifecycleTransition'];

//Create ec2 client
$config = array();        
$config['key'] = '';                         //AWS key
$config['secret'] = ''; //Secret key
$config['region'] = '';                           //Region
$ec2 = \Aws\Ec2\Ec2Client::factory($config);                    //Create client

$response = $ec2->DescribeInstances(array(
    'InstanceIds' => array($instanceId)
));


//Get the private Ip address
$private_ip = ($response->getPath('Reservations/*/Instances/*/PrivateIpAddress'));
$private_ip = $private_ip[0];

//Get the HTTP headers for server status
if ($data['Event'] == "autoscaling:EC2_INSTANCE_LAUNCH")
{
sleep(30);
$header = get_headers("http://".$private_ip,1);
$serverStatus="Check";
$try = 1;

while( $try < 5){
	$header = get_headers("http://".$private_ip,1);
if($header[0]=="HTTP/1.1 200 OK"){
	
	$path_to_file = 'nginx.conf';   //nginx configuration file
	$file_contents = file_get_contents($path_to_file);
	$file_contents = str_replace("#IPHERE;","#IPHERE;\nserver ".$private_ip.";",$file_contents);
	file_put_contents($path_to_file,$file_contents);
	$serverStatus="Server is up!!!";
$file1->fwrite("Event is :".$event."->"." Instance id is ".$data['EC2InstanceId']." private ip is ".$private_ip."\n");
	break;
	
	}
	else
	{
		$serverStatus = "Oops the server is down at ";
		sleep(5);
		$try++;
	}
   }

}
else 
	if($lifecycle == "autoscaling:EC2_INSTANCE_TERMINATING")
	{
	$path_to_file = 'nginx.conf';
	$file_contents = file_get_contents($path_to_file);
	$file_contents = str_replace("server ".$private_ip.";\n","",$file_contents);
	file_put_contents($path_to_file,$file_contents);
	$file1->fwrite("Event is :".$lifecycle."->"." Instance id is ".$data['EC2InstanceId']." private ip is ".$private_ip."\n");
	}
$file2 = new SplFileObject('s.log','a'); //To log the message from SNS
$file2->fwrite($serverStatus."\n");      // To log the server status

?>
