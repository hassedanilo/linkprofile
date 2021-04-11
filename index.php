<?php

echo "<h2 align=´center´>Projeto COLLEGA: Competence-based Mobile Community Response Networks</h2>";
echo "<h2 align=´center´></h2>";
echo "<h3 align=´center´>Formacao de redes de competencias pela extracao de dados no LinkedIn</h3>";
echo "<h2 align=´center´></h2>";
echo "<h3 align=´center´>Projeto desenvolvido pela Universidade do Estado de Santa Catarina (UDESC) em parceria com a Universidade de Bologna (UNIBO/Italia)</h3>";
echo "<h2 align=´center´></h2>";

echo "<td><img src=\"image.jpg\" alt=\"\" /><b></td>";

echo "<h3 align=´center´>Para participar basta realizar o Sign In no LinkedIn atraves do botao abaixo, Obrigado!</h3>";
echo "<h2 align=´center´></h2>";

require_once('config.php');
require_once('db.php');
if ($config['Client_ID'] === '' || $config['Client_Secret'] === '') {
  echo 'You need a API Key and Secret Key to test the sample code. Get one from <a href="https://www.linkedin.com/secure/developer">https://www.linkedin.com/secure/developer</a>';
  exit;
}
if(isset($_GET['code']))
{
    $url    = 'https://www.linkedin.com/uas/oauth2/accessToken';
    $param  = 'grant_type=authorization_code&code='.$_GET['code'].'&redirect_uri='.$config['callback_url'].'&client_id='.$config['Client_ID'].'&client_secret='.$config['Client_Secret'];
    $return = (json_decode(post_curl($url,$param),true));
    if($return['error'])
    {
        echo 'Some error occured<br><br>'.$return['error_description'].'<br><br>Please Try again.';
    }
    else   
    {
        $url    = 'https://api.linkedin.com/v1/people/~:(id,firstName,lastName,pictureUrls::(original),headline,publicProfileUrl,location,industry,positions,email-address,summary)?format=json&oauth2_access_token='.$return['access_token'];
        $User   = json_decode(post_curl($url));
        $id             = isset($User->id) ? $User->id : '';
        $firstName      = isset($User->firstName) ? $User->firstName : '';
        $lastName       = isset($User->lastName) ? $User->lastName : '';
        $emailAddress   = isset($User->emailAddress) ? $User->emailAddress : '';
        $headline       = isset($User->headline) ? $User->headline : '';
        $pictureUrls    = isset($User->pictureUrls->values[0]) ? $User->pictureUrls->values[0] : '';
        $location       = isset($User->location->name) ? $User->location->name : '';
        $positions      = isset($User->positions->values[0]->company->name) ? $User->positions->values[0]->company->name : '';
        $positionstitle = isset($User->positions->values[0]->title) ? $User->positions->values[0]->title : '';

	$industry       = isset($User->industry) ? $User->industry : '';        

	$publicProfileUrl = isset($User->publicProfileUrl) ? $User->publicProfileUrl : '';
       
        echo "
        <table border='1' cellpadding='7' style='border-collapse: collapse;'>
            <tr style='text-align: center;'>
                <td colspan='2'><img src='".$pictureUrls."' width='100' /><br></td>
            </tr>
            <tr>
                <td>ID: </td>
                <td>".$id."</td>
            </tr>
            <tr>
                <td>First Name: </td>
                <td>".$firstName."</td>
            </tr>
            <tr>
                <td>last Name: </td>
                <td>".$lastName."</td>
            </tr>
            <tr>
                <td>Email: </td>
                <td>".$emailAddress."</td>
            </tr>
            <tr>
                <td>Job Position: </td>
                <td>".$positionstitle.": ".$positions."</td>
            </tr>
            <tr>
                <td>Location: </td>
                <td>".$location."</td>
            </tr>
            
	 <tr>
                <td>Industry: </td>
                <td>".$industry."</td>
            </tr>

	 <tr>
                <td>Specialties: </td>
                <td>".$headline."</td>
            </tr>


	<tr>
                <td>Profile Link: </td>
                <td><a href='".$publicProfileUrl."' target='_blank'>".$publicProfileUrl."</a></td>
            </tr>
        </table>
        ";
        $query = "INSERT INTO `inventur_linkprofile`.`users` 
    (`userid`, 
    `firstName`, 
    `lastName`, 
    `emailAddress`, 
    `positions`, 
    `location`,
    `industry`,
    `positionstitle`,
    `profileURL`, 
    `pictureUrls`, 
    `headline`)
    
    VALUES
    
    ('$id', 
    '$firstName', 
    '$lastName', 
    '$emailAddress', 
    '$positions', 
    '$location',
    '$industry', 
    '$positionstitle',
    '$profileURL', 
    '$pictureUrls', 
    '$headline')";
        mysqli_query($connection,$query);

        
    }
}
elseif(isset($_GET['error']))
{
    echo 'Some error occured<br><br>'.$_GET['error_description'].'<br><br>Please Try again.';
}
else
{
    echo '<a href="https://www.linkedin.com/uas/oauth2/authorization?response_type=code&client_id='.$config['Client_ID'].'&redirect_uri='.$config['callback_url'].'&state=98765EeFWf45A53sdfKef4233&scope=r_basicprofile r_emailaddress"><img src="./images/linkedin_connect_button.png" alt="Sign in with LinkedIn"/></a>';
}


function post_curl($url,$param="")
{
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    if($param!="")
        curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
        
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}