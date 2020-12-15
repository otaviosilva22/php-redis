# Aplicação do banco de dados Redis com o PHP

Estes arquivos fazem referência à um trabalho sobre a implementação do banco de dados Redis na linguagem de programação PHP. O front-end não possui foco principal e por isso as páginas não apresentam o conteúdo de forma responsiva.

Os dados armazenados no Redis representam números do COVID-19 no estado de Minas Gerais, e são captados atráves de um arquivo .csv disponibilizado pelo site brasil.io.

<h2> Tecnologias Utilizadas </h2>

- [Redis](https://redis.io/)
- [PHP](https://www.php.net/)
- [HTML](https://developer.mozilla.org/pt-BR/docs/Web/HTML)
- [CSS](https://devdocs.io/css/)

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


  


