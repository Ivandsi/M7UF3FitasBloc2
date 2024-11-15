<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fita 5.2</title>
    <style>
		table,
		td {
			border: 1px solid black;
			border-spacing: 0px;
		}

        td {
            padding: 5px;
        }
	</style>
</head>

<body>
    <form action="ex1.php" method="POST">
        <label for="country">Pais: </label>
        <input type="text" name="country">
        <input type="submit">
    </form>
    <br>

    <table>
        <!-- la capçalera de la taula l'hem de fer nosaltres -->
        <thead>
            <tr>
                <th colspan="4" align="center" bgcolor="cyan">Llistat de llengües</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $country = null;
            if (isset($_POST['country'])) {
                $country = $_POST['country'];
            }

            //connexió dins block try-catch:
            //  prova d'executar el contingut del try
            //  si falla executa el catch
            try {
                $hostname = "localhost";
                $dbname = "mundo";
                $username = "admin";
                $pw = "SQL no me gusta!";
                $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", "$username", "$pw");
            } catch (PDOException $e) {
                echo "Error al accedir a la base de dades" . $e->getMessage() . "\n";
                exit;
            }


            //preparem i executem la consulta
            $queryText = "SELECT cl.* FROM countrylanguage cl " .
                "INNER JOIN country co ON cl.CountryCode = co.Code " .
                "WHERE co.Name = '';";
            if ($country != null) {
                //preparem i executem la consulta
                $queryText = "SELECT co.Name 'Country', cl.Language 'Language', " .
                    "cl.IsOfficial 'Official', cl.Percentage 'Percentage' FROM countrylanguage cl " .
                    "INNER JOIN country co ON cl.CountryCode = co.Code " .
                    "WHERE co.Name LIKE '%$country%';";
            }


            try {
                //preparem i executem la consulta
                $query = $pdo->prepare($queryText);
                $query->execute();
            } catch (PDOException $e) {
                echo "Error de SQL<br>\n";
                //comprovo errors:
                $e = $query->errorInfo();
                if ($e[0] != '00000') {
                    echo "\nPDO::errorInfo():\n";
                    die("Error accedint a dades: " . $e[2]);
                }
            }

            //anem agafant les fileres d'amb una amb una
            if ($query->rowCount() == 0) {
                echo "\t\t\t<tr>\n";
                echo "\t\t\t\t<td colspan=\"2\">No hi ha dades</td>\n";
                echo "\t\t\t</tr>\n";
            } else {
                foreach ($query as $row) {
                    echo "\t\t\t<tr>\n";

                    # (3.4) cadascuna de les columnes ha d'anar precedida d'un <td>
                    #	després concatenar el contingut del camp del registre
                    #	i tancar amb un </td>
                    echo "\t\t\t\t<td>" . $row["Country"] . "</td>\n";
                    echo "\t\t\t\t<td>" . $row["Language"] . "</td>\n";
                    if($row["Official"] == "T"){
                        echo "\t\t\t\t<td>Llengua oficial</td>\n";
                    } else {
                        echo "\t\t\t\t<td>Llengua no oficial</td>\n";
                    }
                    echo "\t\t\t\t<td>" . $row["Percentage"] . "%</td>\n";

                    # (3.5) tanquem la fila
                    echo "\t\t\t</tr>\n";
                }

                /* $row = $query->fetch();
                while ($row) {
                    echo $row['i'] . " - " . $row['a'] . "<br/>";
                    $row = $query->fetch();
                } */
            }

            //versió alternativa amb foreach
            /*foreach ($query as $row) {
        echo $row['i']." - " . $row['a']. "<br/>";
      }*/

            //eliminem els objectes per alliberar memòria 
            unset($pdo);
            unset($query);

            ?>
        </tbody>
    </table>
</body>

</html>