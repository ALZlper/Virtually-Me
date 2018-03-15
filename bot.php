<?php

    include __DIR__.'/Discord/vendor/autoload.php';

    $chatbot = false;
    $flomoraFunctions = true;
    $flomoraChat = true;

    use Discord\Discord;
    
    $discord = new Discord([
        'token' => '<yourToken>',
    ]);
    
    $discord->on('ready', function ($discord) {
        echo "Bot is up!", PHP_EOL;
    
        // Listen for messages.
        $discord->on('message', function ($message, $discord) {

            try {
                echo PHP_EOL;
                echo $message->author->username.": ".$message->content;
                
                if ($GLOBALS['chatbot']) {
                    if(checkIfCommand($message, "bot", false)) cleverBot($message);
                    if(checkIfCommand($message, "maschine", false)) cleverBot($message);
                    if(checkIfCommand($message, "mashine", false)) cleverBot($message);
                    if(checkControlChar($message, "+")) cleverBot($message);
                }
                if ($GLOBALS['flomoraFunctions']) {
                    if(checkIfCommand($message, "dice")) rollDice($message);
                }
                if ($GLOBALS['flomoraChat']) {
                    if(checkIfCommand($message, "flomora")) sendText($message, "Willkommen in der Welt von Esdo. Welche Farbe hätte deine Flomora?");
                    if(checkIfCommand($message, "blau")) sendText($message, "Der Gedanke an Verrat ist dir in deinem Leben nie gekommen. Treu standest du aufrecht zu deinen Kameraden. Deshalb ist deine Flomora blau.");
                    if(checkIfCommand($message, "weiß")) sendText($message, "Ein Leben voller Ehrlichkeit, Aufrichtigkeit und Rechtschaffenheit hat deine Flomora weiß gefärbt.");
                }

            } catch(Exception $ex) {}
        });
    });
    
    $discord->run();

    function check_numeric($str) {
        if($str == "0" and $str != "") {
            return true;
        } else {
            return is_numeric($str); 
        }
    }

    function checkControlChar($message, $char, $useSpace = true) {
        return (substr($message->content, 0, strlen($char) + 1) == $char.($useSpace ? " " : ""));
    }

    function checkIfCommand($message, $str, $needCommand = true) {
        return ($needCommand ? (strpos(strtolower($message->content), "/$str") !== false or
                strpos(strtolower($message->content), "/ $str") !== false or 
                strpos(strtolower($message->content), "!$str") !== false or
                strpos(strtolower($message->content), "! $str") !== false) : 
                strpos(strtolower($message->content), "$str") !== false);
    }

    function sendText($message, $text) {
        $message->reply( $text );
    }

    function getParameters($message, $command, $delimiter = " ") {
        $message_array = explode($delimiter, $message->content);
        var_dump($message_array);
        foreach ($message_array as $i=>$message_element) {
            if ($message_element == $command || $message_element == "/$command" || $message_element == "!$command" ) {
                unset($message_array[$i]);
                return array_values($message_array);
            } else {
                unset($message_array[$i]);
            }
        }
    }

    function checkIfHasRole($message, $role) {
        foreach ($roles as $role) {
            if ($role->name == $role) {
                return true;
            }
        }
        return false;
    }

    function rollDice($message, $errors = false, $channelId = false, $NeedRole = false, $showPatreonStill = false) {

        if ($message->channel_id == $channelId or $channelId == false) {

            if (checkIfHasRole($message, "St.Patronius") or !$NeedRole) {

                $parameters = getParameters($message, "dice");

                if (check_numeric($parameters[0]) and check_numeric($parameters[1])) {
                    if ($parameters[0] > $parameters[1]) {
                        if ($errors) $message->reply( "Der erste Parameter darf nicht größer als der Zweite sein." );
                    } else {
                        $rand = rand($parameters[0], $parameters[1]);
                        echo PHP_EOL;
                        echo "Command issued with: ".$parameters[0].", ".$parameters[1]." and result: $rand";
                        $message->reply("schwang die Würfel und warf eine $rand!");
                    }
                } else {
                    if ($errors) $message->reply( "Beide Parameter müssen eine Zahl sein." );
                }
            } else {
                if ($errors or $showPatreonStill) $message->reply( "Du bist gar kein St. Patrone! Schämst du dich nicht? Werde JETZT ein Bruder auf: https://www.patreon.com/Flomora" );
            }
            
        }
    }

    function cleverBot($message) {
        $url = "https://www.cleverbot.com/getreply";
        $key = "<yourKey>";
        $input = $message->content;
        $input = str_replace("bot", "", $input);
        $input = str_replace("+ ", "", $input);
        $input = str_replace("maschine", "", $input);
        $input = str_replace("mashine", "", $input);
        $input = rawurlencode ($input);
        $message->reply(json_decode (file_get_contents ("$url?input=$input&key=$key"))->output);
    }

?>
