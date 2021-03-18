
<?php require_once("custom/php/common.php");

if ( is_user_logged_in() ) {
    if (current_user_can('manage_items') == false) {
        echo 'Não tem autorização a esta página';
    } else {




        if (empty($_POST["estado"])) {

            $edicaodedados = "item";
            $tipo = $tipoestado = $nome = ""; // inicialização de variáveis

            $queryteste = "SELECT name FROM item"; // query para ver se tem itens
            $vazia = mysqli_query($mysqli, $queryteste);

            $query = "SELECT id,code FROM item_type"; // query para imprimir tipos de item
            $resultado = mysqli_query($mysqli, $query);



            if (mysqli_num_rows($vazia)> 0) {
                echo "<table class='mytable'><tr><th>tipo de item</th><th>id</th><th>nome do item</th><th>estado</th><th>ação</th></tr>";
                while ($row = $resultado->fetch_assoc()) {
                    $query2 = "SELECT id, name, item_type_id,state FROM item WHERE item_type_id=".$row["id"]." ORDER BY item.name ASC"; // query que muda conforme ciclo
                    $resultadodc = mysqli_query($mysqli, $query2);
                    if (mysqli_num_rows($resultadodc)> 0){
                        echo "<tr><td rowspan =" . mysqli_num_rows($resultadodc) . ">" . $row["code"] . "</td>";
                        while ($row2 = $resultadodc->fetch_assoc()) {
                            echo "<td>" . $row2["id"] . "</td><td>" . $row2["name"] . "</td><td>" . $row2["state"] . "</td>";
                            echo "<td> <a href='edicao-de-dados?estado=editar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Editar]</a>"; // hiperligação para o ediçao de dados
                            if ($row2["state"] == "active"){
                                echo "<a href='edicao-de-dados?estado=desativar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Desativar]</a> </td>"; // hiperligação para o ediçao de dados
                            }else {
                                echo "<a href='edicao-de-dados?estado=ativar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Ativar]</a> </td>"; // hiperligação para o ediçao de dados
                            }
                            echo "</tr>";
                        }
                    }

                }
                echo"</table>";
            } else {
                echo "Não há itens!!";
            }




            echo "<h3 class='h3div'>Gestão de itens - introdução</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<form method="post" action="">
            <div> 
			Nome : <span class="error">*</span> <input class="myinput" type="text" name="nome"><br>
			</div>';

            $querytipos = sprintf("SELECT code, id FROM item_type"); // query dos tipos de item
            $resultadotipos = mysqli_query($mysqli,$querytipos);


            echo '<div>';

            echo 'Tipo: <span class="error">*</span><br>';
            if (mysqli_num_rows($resultado)){
                while($tipos = $resultadotipos->fetch_array()){

                    $line = sprintf('.<input type="radio" name="tipo" value='.$tipos["id"].'>'.$tipos["code"].'  <br>');
                    echo "$line";
                }
            }
            echo '</div>';


            echo '<div>
        
			<br>Estado:<span class="error">*</span><br>.<input type="radio" name= "tipoestado" value="active">Ativo <br>.<input type="radio" name= "tipoestado" value="inactive">Inativo 
			<br>
			<input type = "hidden" value = "inserir" name ="estado">
			<input type ="submit" value = "Inserir item">
			</div>
		</form>';

        } elseif ($_POST["estado"] == "inserir") {
            //server side validation

            $erros = 0;
            $tipoestado = $_REQUEST["tipoestado"];
            $id = $_REQUEST["tipo"];


            if (empty($_REQUEST["nome"])) {
                echo "O nome é obrigatório!";
                echo "<br>";
                $erros++;
            }
            else{
                $nome = test_input($_REQUEST["nome"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["nome"])) {
                    echo "Nome no formato errado, apenas carateres!!!!";
                    echo "<br>";
                    $erros++;
                }
            }
            if (empty($_REQUEST["tipo"])) {
                echo "O tipo de item é obrigatório!";
                echo "<br>";
                $erros++;
            }
            if (empty($_REQUEST["tipoestado"])) {
                echo "O estado é obrigatório!";
                echo "<br>";
                $erros++;

            }
            //
            if ($erros > 0){
                echo voltar();
            }
            else {

                echo "<h3 class='h3div'>Gestão de itens - inserção</h3>";


                $queryi = sprintf("INSERT INTO item (name,item_type_id,state) VALUES('" . $nome . "','" . $id. "','" . $tipoestado . "')");
                if (mysqli_query($mysqli, $queryi)) {
                    echo "Inseriu os dados de novo item com sucesso.";
                    echo "<br>";
                    echo "Clique em ";
                    echo '<a href="">continuar</a>';
                    echo " para avançar";
                } else {
                    echo "Ocorreu um erro: ";
                    echo "<br>";
                    echo mysqli_error($mysqli);
                    echo "Clique em ";
                    echo '<a href="">continuar</a>';
                    echo " para avançar";
                }


            }

        }
    }
}
else{
    echo 'Não tem autorização a esta página';
}
?>