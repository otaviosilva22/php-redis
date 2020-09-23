<?php //incluindo processameto do json
    //conexão com redis
    
    $host = 'localhost';
    $port = 6379;
    $redis = new Redis();
    if ($redis->connect($host, $port) == false){
        die($redis->getLastError());
    }

    $hoje = date('d/m/Y');

    //realiza o processamento do json apenas uma vez no dia
    if (strcmp($hoje, $redis->get('ultimadata')) != 0 || $redis->exists('ultimadata') == 0){
        include 'processamento/processamento.php';
        echo  "<script>alert('Dados atualizados com sucesso!');</script>";
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

<html>
    <head>
        <link rel="stylesheet" type="text/css" media="screen and (min-width: 1350px)" href="estilo/estilo.css"/>
        <title>COVID-19 MG</title>
        <link rel="shortcut icon" type="image/x-icon" href="imagens/favicon.ico">
		
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    </head>
    <body>
        <div id="interface">
            <header id="cabecalho">
                <h1> COVID-19 MG </h1>
                <nav id="menu_principal">
                    <ol>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="paginas/construcao.html">Dados</a></li>
                        <li><a href="paginas/construcao.html">Sobre</a></li>                      
                    </ol>
                </nav>
            </header>
            <section id="geral">
                <table id="casosGeral">
                    <tr>
                        <td>População Geral</td>
                        <td>Casos</td>
                        <td>Mortes</td>
                    </tr>
                    <tr>
                        <td><font color="green"><?php echo $redis->get('pgeral'); ?></font></td>
                        <td><font color="orange"><?php echo $redis->get('casos'); ?></font></td>
                        <td><font color="red"><?php echo $redis->get('mortes'); ?></font></td>
                    </tr>
                </table>
            </section>
            <section id="titulo">
                <h2><font color="white">Cidades</font></h2>
                <form method="post">
                    <input type="text" name="pesquisar" placeholder=" Pesquisar cidade" autocomplete="off">
                    <input type="submit" value="Buscar">
                </form>
            </section>

            <section id="dados">
                <table id="casosMG" BORDER=0 CELLSPACING=0>
                    <tr class="titulo">
                        <th>Nome</th><th>População</th><th>Casos</th><th>Mortes</th>
                        <th>Taxa de Mortalidade</th><th>Data</th>                        
                    </tr>
                    <?php
                        if (empty($_POST['pesquisar'])) {
                            // Variáveis que estão referenciadas nas textbox
                            $pesquisar = "";
                            
                        }else{
                            $pesquisar = $_POST['pesquisar'];
                        }
                        exibicao($pesquisar);
                    ?>             
                </table>
            </section>
            <footer>
                <p>Fonte: Secretarias de Saúde das Unidades Federativas, dados tratados por Álvaro Justen e colaboradores/Brasil.IO, e </br> exibidos por Otávio Silva, Rafael Freitas e Marcos Andrade</p>
            </footer>
        </div>        
    </body>
</html>
