<?php
require(__DIR__.'/Dao.php');
require(__DIR__.'/PageHandler.php');
/*
 * stps:
 * 1- grab the page ids from db in an array
 * 2- generate array of urls of pages
 * 3- grab each page's html content and process it (save in the db the data you need)
 *  */

$pages = new PageHandler();
$fbIDs=new Dao("sandbox.dev", "root", "addpeople", "facebook");
$rowCount=0;
$idArray=$fbIDs->grabFacebookPagesIDs($rowCount);
//$pages->printPages();
//$pages->grabInfoFromPages();

$EMAIL="leadfinder@email.com";
$PASSWORD="addpeople";
$cookie=null;

$a = $pages->cURL("https://login.facebook.com/login.php?login_attempt=1",true,null,"email=$EMAIL&pass=$PASSWORD");
preg_match('%Set-Cookie: ([^;]+);%',$a,$b);
$c = $pages->cURL("https://login.facebook.com/login.php?login_attempt=1",true,$b[1],"email=$EMAIL&pass=$PASSWORD");
preg_match_all('%Set-Cookie: ([^;]+);%',$c,$d);
for($i=0;$i<count($d[0]);$i++)
    $cookie.=$d[1][$i].";";
/*
NOW TO JUST OPEN ANOTHER URL EDIT THE FIRST ARGUMENT OF THE FOLLOWING FUNCTION.
TO SEND SOME DATA EDIT THE LAST ARGUMENT.
*/
//echo $fbPageLinks[0];die;
for($i=0; $i<10; $i++){
    $html=$pages->cURL("https://www.facebook.com/pg/".$idArray[$i]."/about/?ref=page_internal",null,$cookie,null);
    $string = htmlspecialchars($html);
    $regex="~<a.*?</a>(*SKIP)(*F)|http://www\S+~";
    $count = preg_match_all($regex,$string,$m);
    echo "<br>---------<br>";
    try{
        if(!empty($m[0])){
            echo $m[0][0];
        }else{
            echo "couldn't find a url";
        }
        
    }catch(Exception $e){
        echo "couldn't find a url";
    }
        
        
//    echo "<pre>";
//    print_r($m[0]);
//    echo "</pre>";
    echo "<br>---------<br>";
}



//$length = 0;
//$counter = 0;

//$regex = "%(\[(?:[^[\]]++|(?1))*\])|<[^>]*>|'[^']*'|[!-~]+%";
//$regex = '/^(http(s?):\/\/)?(www\.)+[a-zA-Z0-9\.\-\_]+(\.[a-zA-Z]{2,3})+(\/[a-zA-Z0-9\_\-\s\.\/\?\%\#\&\=]*)?$/';
//$regex = '!^http?://([^/]+\.)?domain(.com|co.uk)(/|#|$)!i';
//$regex="/^(www)((\.[A-Z0-9][A-Z0-9_-]*)+.(com|org|net|dk|at|us|tv|info|uk|co.uk|biz|se)$)(:(\d+))?\/?/i";

//$regex="(^|.)(<a.*?</a>(*SKIP)(*F)|http://www.+\.uk)($|.)";
//$string = "<As's\\as'dsd> asqwedasd <sa sdasd> [a sadasd] [<asdsad> [as ddsd]] 'asdsad assd'";

//echo "<pre>";
//print_r($m[0]);
//echo "</pre>";


//http://www.mrtsshirts.co.uk/</div></a></div><div

//echo "<br>---------<br>";
//while (isset($html[$length])) {
//    $length++;
//    
//}
//echo $length;
//echo "<br>";






/*$html2=str_get_html($html);
foreach($html2->find('a') as $element) 
       echo $element->href . '<br>';*/


// a new dom object 
// $dom = new DOMDocument('1.0', 'utf-8'); 
// // load the html into the object ***/ 
// $dom->loadHTML($html);
 //discard white space 
// $dom->preserveWhiteSpace = false; 
// $div= $dom->getElementsByTagName('div'); // here u use your desired tag
 //print_r($div);
// for($i=0; $i<$div->length; $i++){
//     echo "<br>";
//     echo  $div->item($i)->nodeValue;
//     echo "<br>";
// }
 
 
 
// echo "<pre>";
// echo htmlspecialchars($html);
// echo "</pre>";