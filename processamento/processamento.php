<?php
    
	
	function saveRedis(){
		/*abre conexao com redis*/
		$host = 'localhost';
		$port = 6379;
		$redis = new Redis();
		if ($redis->connect($host, $port) == false){
			die($redis->getLastError());
		}

		$redis->set('ultimadata', date('d/m/Y'));

		//capturando dados do arquivo .csv

		$delimitador = ',';
		$cerca = '"';

		// Abrir arquivo para leitura
		$f = fopen('processamento/caso_full.csv', 'r');
		$count = 0;
		$pgeral = 0;
		$casos = 0;
		$mortes = 0;
		
		$dataEscolhida = "2020-12-10";
		
		
		if ($f) { 

			// Ler cabecalho do arquivo
			$cabecalho = fgetcsv($f, 0, $delimitador, $cerca);
			
		
			// Enquanto nao terminar o arquivo
			while (!feof($f)) { 

				// Ler uma linha do arquivo
				$linha = fgetcsv($f, 0, $delimitador, $cerca);
				if (!$linha) {
					continue;
				}

				// Montar registro com valores indexados pelo cabecalho
				$registro = array_combine($cabecalho, $linha);

				// Obtendo dados para armazenamento no Redis
			
				
				$redis->set($registro['city'], $count); //utilizado na pesquisa
				
				
				$redis->set('count', $count); //armazena contador na key 'count'
				
				$pgeral += $registro['population'];
				$mortes += $registro['deaths'];
				$casos += $registro['confirmed'];
				
				$redis->hmset($count, 
				[
					'city' => $registro['city'],
					'population' => $registro['population'],
					'confirmed' => $registro['confirmed'],
					'deaths' => $registro['deaths'],
					'death_rate' =>  $registro['death_rate'],
					'date' => $registro['last_date'],
				]);
				
				$count = $count + 1; //contador de cidades
				
				
			
			}
			fclose($f);
		}
		
		//dados gerais
		$redis->set('pgeral', $pgeral);
		$redis->set('mortes', $mortes);
		$redis->set('casos', $casos);
		
	}

	function exibicao($pesquisar){
        //abre conexao na funcao
        $host = 'localhost';
        $port = 6379;
        $redis = new Redis();
        if ($redis->connect($host, $port) == false){
            die($redis->getLastError());
        }

        $i=0;
        if (strcmp($pesquisar, "") == 0){

            while ($i<$redis->get('count')){
                echo '<tr class="conteudo"><td>'.$redis->hget($i, 'city').'</td>'; //nome cidade
                echo '<td><font color="green">'.$redis->hget($i, 'population').'</font></td>'; //populacao
                echo '<td><font color="orange">'.$redis->hget($i, 'confirmed').'</font></td>'; //casos confirmados
                echo '<td><font color="red">'.$redis->hget($i, 'deaths').'</font></td>'; //mortes confirmadas
                echo '<td><font color="red">'.$redis->hget($i, 'death_rate').'</font></td>'; //taxa mortalidade
                echo '<td><font color="yellow">'.$redis->hget($i, 'date').'</font></td>'; //data da atualização     
                $i++;            
            }
        
        }else{
            
            $i = $redis->get($pesquisar);            
            echo '<tr class="conteudo"><td>'.$redis->hget($i, 'city').'</td>'; //nome cidade
            echo '<td><font color="green">'.$redis->hget($i, 'population').'</font></td>'; //populacao
            echo '<td><font color="orange">'.$redis->hget($i, 'confirmed').'</font></td>'; //casos confirmados
            echo '<td><font color="red">'.$redis->hget($i, 'deaths').'</font></td>'; //mortes confirmadas
            echo '<td><font color="red">'.$redis->hget($i, 'death_rate').'</font></td>'; //taxa mortalidade
            echo '<td><font color="yellow">'.$redis->hget($i, 'date').'</font></td>'; //data da atualização         
        }

          
    }                
    
	
?>
