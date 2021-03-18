
<?php require_once("custom/php/common.php");


if ( is_user_logged_in() ) {
    if (current_user_can('manage_unit_types') == false) {
        echo 'Não tem autorização a esta página';
    }
    else {

        if(empty($_POST["estado"])) {


            $query = "SELECT id, name FROM subitem_unit_type ORDER BY name ASC";
            $resultado = mysqli_query($mysqli, $query);

            if ($resultado->num_rows > 0) {
                echo "<table class='mytable'><tr><th>id</th><th>nome</th></tr>";
                while ($row = $resultado->fetch_assoc()) {
                    echo "<tr><td>" . $row["id"] . "</td><td>" . $row["name"] . "</td></tr>";

                }
                echo "</table>";
            } else {
                echo "Não há tipos de unidades";
            }



            echo "<h3 class='h3div'>Gestão de unidades - introdução</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo'<form method="post" action="">
			<span class="error">*</span>Nome <input type="text" name="typename">
			<input type = "hidden" value = "inserir" name ="estado">
			<input type ="submit" value = "Submeter">
		</form>';


        }
        elseif($_POST["estado"] == "inserir"){
                // server side validation
                $erros = 0;

                if (empty($_REQUEST["typename"])) {
                    echo "O nome é obrigatório!";
                    echo "<br>";
                    $erros++;

                }

                else{
                    $typename = test_input($_REQUEST["typename"]);
                    if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["typename"])){
                    echo "O nome está no formato errado";
                    echo "<br>";
                    $erros++;
                    }

                }
                if ($erros > 0){
                    echo voltar();
                }
                //
                else{ // se nao houver erros

                    $queryrepetidos = "SELECT name FROM subitem_unit_type WHERE name ='".$_REQUEST["typename"]."'"; // para verificar se existem unidaddes repetidas
                    $repetido = mysqli_query($mysqli,$queryrepetidos);
                    $unidade = mysqli_fetch_assoc($repetido);

                    if(empty($unidade["name"])) {
                        echo "<h3 class='h3div'>Gestão de unidades - inserção</h3>";
                        $query = sprintf("INSERT INTO subitem_unit_type (name) VALUES('" . $typename . "')");
                        if (mysqli_query($mysqli, $query)) {
                            echo '<input type = "hidden" value = "" name ="estado">';
                            echo "Inseriu os dados de registo com sucesso.";
                            echo "<br>";
                            echo "Clique em ";
                            echo '<a href="">continuar</a>';
                            echo " para avançar";
                        } else {
                            echo "Ocorreu um erro: ";
                            echo "<br>";
                            echo mysqli_error($mysqli);
                        }
                    }
                    else{
                        echo "Já existe uma unidade com esse nome!!!!!";
                        echo '<br>';
                        echo voltar();
                    }
                }

            }

        }
}

?>