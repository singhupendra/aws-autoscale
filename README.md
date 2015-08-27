# aws-autoscale
To automatically add/remove new ec2 instances to nginx upstream server and avoid ELB.This script checks the health of the new instance prior adding the it to the load balancer.

<h2>Requirements</h2>

You need to perform several task before using this. Steps required are given below:<br/>
<strong>1. Create a SNS topic</strong><br/>
    You can refer below url for same <br/>
      http://docs.aws.amazon.com/sns/latest/dg/CreateTopic.html<br/>

<strong>2. Subscribe an endpoint(HTTP) (webserver where you will recieve the SNS notification and run this script)</strong><br/>
    You can refer below url for same <br/>
      http://docs.aws.amazon.com/sns/latest/dg/SubscribeTopic.html</br>

<strong>3. Create an autoscale lifecycle before termination</strong><br/>
    http://docs.aws.amazon.com/AutoScaling/latest/DeveloperGuide/adding-lifecycle-hooks.html<br/>

<strong>4. Provide access to nginx file so that webserver can edit the file</strong><br/>
    use <strong>setfacl</strong> command for this<br/>
    
<strong>5. Provide privilege to reload the nginx service to the use executing this script</strong><br/>
    use visudo and add <br>
      <strong>nginx ALL=(root)NOPASSWD:/etc/init.d/nginx</strong> (trying to find better and secure way to reload the service)</br>

<strong>6. Access Key ID and Secret Access Key</strong></br>
    http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html</br>


<h2>Installation</h2>

Just copy the files in your webroot (within a desired folder).<br/>
Edit the config file and provide your acess key id, secret access key and region

