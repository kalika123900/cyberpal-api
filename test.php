<?php
 $name = 'kalika';
       $group = '106589563';
       $email = 'kalika.mongoosetech@gmail.com';
       $subscriber = array('email' => $email,'name' => $name);
       echo "https://api.mailerlite.com/api/v2/groups/".$group."/subscribers";
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,"https://api.mailerlite.com/api/v2/groups/".$group."/subscribers");
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($subscriber)); 
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       $headers = [];

       curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MailerLite-ApiKey:2d5cbff4afddc1e809204a6a155985f5','Content-Type:application/json'));

       $server_output = curl_exec($ch);    
       
       print_r($ch); 
       curl_close ($ch);
       
       echo '<pre>';
       print_r($server_output);
    die();
	
  $url = "http://139.59.83.219:5000/find-best-fit?category=$request->category&industry=$request->industry&organizationSize=$request->organizationSize&deployment=$request->deployment&implementationTime=$request->implementationTime&currentSolution=$request->currentSolution&generation=$request->generation&budget=$request->budget&easeOfUseEnabled=$request->easeOfUseEnabled&managedServiceProvider=$request->managedServiceProvider";
        $category_id = $request->category;
        foreach($request->integrations as $integrations){
            $url .= "&integrations[]=".$integrations;
        }
        foreach($request->features as $features){
            $url .= "&features[]=".$features;
        }
        
        $ch = curl_init();
        $headers = array(
        'Accept: application/json',
        'Content-Type: application/json',
        );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        $httpCode = curl_getinfo($ch , CURLINFO_HTTP_CODE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
        //Timeout in seconds
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        
        $error = '';
        if ($response === false) 
        $error = curl_error($ch);

        curl_close($ch);
        print_r($response);

        die('1');

       $response = json_decode(curl_exec($ch));
?>