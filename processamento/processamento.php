<?php
    /*abre conexao com redis*/
    $host = 'localhost';
    $port = 6379;
    $redis = new Redis();
    if ($redis->connect($host, $port) == false){
        die($redis->getLastError());
    }

    $redis->set('ultimadata', date('d/m/Y'));

    //capturando dados json
    $filename = "https://brasil.io/api/dataset/covid19/caso/data/?format=json&is_last=True&state=MG";
    $content = file_get_contents($filename);
    $jsonObj = json_decode($content);
    $results = $jsonObj->results;
    $redis->set('count', $jsonObj->count);

    //setando no banco redis
    for ($i=0;$i<$jsonObj->count;$i++){
        if ($results[$i]->city!=NULL){
            $redis->set($results[$i]->city, $i); //utilizado na pesquisa
            $redis->hmset($i, 
            [
                'city' => $results[$i]->city,
                'population' => $results[$i]->estimated_population_2019,
                'confirmed' => $results[$i]->confirmed,
                'deaths' => $results[$i]->deaths,
                'death_rate' => $results[$i]->death_rate,
                'date' => $results[$i]->date,
            ]);
        }else{
            $redis->set('pgeral', $results[$i]->estimated_population_2019);
            $redis->set('casos', $results[$i]->confirmed);
            $redis->set('mortes', $results[$i]->deaths);
        }        
    }        
?>
