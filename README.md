# Aplicação do banco de dados Redis com o PHP

Estes arquivos fazem referência à um trabalho sobre a implementação do banco de dados Redis na linguagem de programação PHP. O front-end não possui foco principal e por isso as páginas não apresentam o conteúdo de forma responsiva.

Os dados armazenados no Redis representam números do COVID-19 no estado de Minas Gerais, e são captados atráves de um arquivo .csv disponibilizado pelo site brasil.io.

<h2> Tecnologias Utilizadas </h2>

- [Redis](https://redis.io/)
- [MySQL](https://www.mysql.com/)
- [PHP](https://www.php.net/)
- [HTML](https://developer.mozilla.org/pt-BR/docs/Web/HTML)
- [CSS](https://devdocs.io/css/)

<h2> Limpeza dos Dados </h2>

O arquivo .csv completo contendo dados da covid-19 em todo o Brasil pode ser baixado atraés da página <a href="https://brasil.io/dataset/covid19/caso_full/">Brasil.io</a>. Contudo, como o trabalho faz referência à somente a Minas Gerais, foi necessário criar um banco no MySQL para executar o SQL necessário para limpeza dos dados.
```
CREATE DATABASE covidmg;
USE covidmg;

CREATE TABLE caso_full(
    city VARCHAR (45) NOT NULL,
    population INTEGER NOT NULL,
    confirmed INTEGER NOT NULL,
    deaths INTEGER NOT NULL,
    death_rate DOUBLE NOT NULL,
    last_date VARCHAR (45)
);
```

Após isso, foi codificado um script em PHP para inserção dos dados de Minas Gerais no banco <b>covidmg</b>.
```
<?php

    $host = "localhost";
	$usuario = "root";
	$senha = "";
	$base = "covidmg";

	//conecta no banco de dados
	$conn = mysqli_connect($host, $usuario, $senha, $base);

	$delimitador = ',';

	// Abrir arquivo para leitura
	$f = fopen('caso_full.csv', 'r');
	$count = 0;
	$pgeral = 0;
	$casos = 0;
	$mortes = 0;
	
	$dataEscolhida = "2020-12-10";
	
	
	if ($f) { 

		// Ler cabecalho do arquivo
		$cabecalho = fgetcsv($f, 0, $delimitador);
		
	
		// Enquanto nao terminar o arquivo
		while (!feof($f)) { 

			// Ler uma linha do arquivo
			$linha = fgetcsv($f, 0, $delimitador);
			if (!$linha) {
				continue;
			}

			// Montar registro com valores indexados pelo cabecalho
			$registro = array_combine($cabecalho, $linha);

			// Obtendo dados para armazenamento no Redis
			
			if (strcmp($registro['state'],"MG") == 0 && strcmp($registro['date'],$dataEscolhida) == 0){
				
				$city = $registro['city'];
				$population = $registro['estimated_population_2019'];
				$confirmed = $registro['last_available_confirmed'];
				$deaths = $registro['last_available_deaths'];
				$death_rate = $registro['last_available_death_rate'];
				$date = $registro['date'];		
				
				$query = "INSERT INTO caso_full VALUES ('$city', '$population', '$confirmed', '$deaths', '$death_rate', '$date')";
				
				/*executa a query*/
				mysqli_query($conn, $query);
				
			}
		}
		fclose($f);
	}

?>

```
Em sequencia exporta-se a consulta referente a <b>caso_full</b>; 

```
SELECT * from caso_full;

```

<h2> Conexão Redis </h2>

```
$host = 'localhost';
$port = 6379;
$redis = new Redis();
if ($redis->connect($host, $port) == false){
    die($redis->getLastError());
}
```

<h2> Página </h2>


  


