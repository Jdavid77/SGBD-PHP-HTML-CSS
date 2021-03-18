<?php require_once("custom/php/common.php");

if ( is_user_logged_in() ) {
    if (current_user_can('manage_subitems') == false) {
        echo 'Não tem autorização a esta página';
    } else {

// Check connection


        if (empty($_POST["estado"])) {

            $edicaodedados = "subitem"; //para o edição de dados
            $queryvazia = "SELECT * FROM subitem"; // para ver se existem subitens
            $resultado = mysqli_query($mysqli, $queryvazia);

            $query = "SELECT id,name FROM item ORDER BY name ASC"; // query para
            $resultado2 = mysqli_query($mysqli, $query);




            if (mysqli_num_rows($resultado)> 0) { // se existir subitens
                echo "<table class='mytable'><tr><th> item</th>
				<th>id</th><th>subitems</th>
				<th>tipos de valor</th>
				<th>nome do campo no formulário</th><th>tipo do campo no formulário</th>
				<th>tipo de unidade</th>
				<th>ordem do campo no formulário</th>
				<th>obrigatório</th>
				<th>estado</th>
				<th>ação</th></tr>";
                while ($row = $resultado2->fetch_array()) {
                    $query2 = "SELECT id,name,item_id,value_type,form_field_name,form_field_type,unit_type_id,form_field_order,mandatory,state FROM subitem WHERE item_id =" . $row["id"] . " ORDER BY name ";
                    $resultado3 = mysqli_query($mysqli, $query2);
                    //seleciona todos os subitens do item

                    if (mysqli_num_rows($resultado3)> 0){
                        echo "<tr><td rowspan =" . mysqli_num_rows($resultado3) . ">" . $row["name"] . "</td>";
                        while ($row2 = $resultado3->fetch_array()) {

                            echo "<td>" . $row2["id"] . "</td>
							<td>" . $row2["name"] . "</td>
							<td>". $row2["value_type"] . "</td>
							<td>". $row2["form_field_name"] . "</td>
							<td>". $row2["form_field_type"] . "</td>";
                            if($row2["unit_type_id"] != 0){ // se houveer unidade
                                $query3 = "SELECT name FROM subitem_unit_type WHERE id ='" . $row2["unit_type_id"] . "'";
                                $resultado4 = mysqli_query($mysqli, $query3);
                                $nometipo = mysqli_fetch_assoc($resultado4);
                                echo "<td>" . $nometipo["name"] . "</td>";

                            }
                            else{
                                echo "<td>-</td>";
                            }
                            echo "<td>". $row2["form_field_order"] . "</td>";
                            if($row2["mandatory"] == 1){
                                echo "<td> Sim </td>";
                            }else{
                                echo "<td> Não </td>";
                            }
                            echo  "<td>"  . $row2["state"] . "</td>";
                            echo "<td> <a href='edicao-de-dados?estado=editar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Editar]</a>"; //para o edição de dados
                            if ($row2["state"] == "active"){
                                echo "<a href='edicao-de-dados?estado=desativar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Desativar]</a> </td>"; //para o edição de dados
                            }else {
                                echo "<a href='edicao-de-dados?estado=ativar&tipo=".$edicaodedados."&id=".$row2["id"]."'>[Ativar]</td>"; //para o edição de dados
                            }
                            echo "</tr>";
                        }
                    }

                }
                echo"</table>";
            } else {
                echo "Não há subitems especificados";
            }

            echo "<h3 class='h3div'>Gestão de subitens - introdução</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<form method = "post" action="">
            <div>
            Nome do subitem:<span class="error">*</span> <input type="text" name="nome"><br>
            </div>';
            echo '<div>';
            echo 'Tipo de Valor: <span class="error"> <br>';

            $valorestipo = get_enum_values($mysqli, "subitem", "value_type");
            if (!empty($valorestipo)) {
                $tamanho = count($valorestipo);
                $indice = 1;
                while ($indice <= $tamanho) {
                    echo '.<input type="radio" name="tipo_valor" value=' . $valorestipo[$indice] . '>' . $valorestipo[$indice] . '<br>';
                    $indice++;
                }
            }
            echo '</div>';
            echo '<div>';
            echo 'Item: <span class="error"> <br>';

            $itens = sprintf("SELECT name FROM item");
            $nomeitens = mysqli_query($mysqli, $itens);

            echo '<select name="nome_item">';

            if (mysqli_num_rows($nomeitens) > 0) {
                while ($row4 = $nomeitens->fetch_array()) {
                    echo '<option value=' . $row4["name"] . '>' . $row4["name"] . '</option>';
                }
            }
            echo '<br></select>';
            echo '</div>';
            echo '<div>';
            echo 'Tipo do campo do formulário: <span class="error"> <br>';

            $valoresform = get_enum_values($mysqli, "subitem", "form_field_type");
            if (!empty($valoresform)) {
                $tamanho2 = count($valoresform);
                $indice1 = 1;
                while ($indice1 <= $tamanho2) {
                    echo '.<input type="radio" name="tipo_form" value=' . $valoresform[$indice1] . '>' . $valoresform[$indice1] . '<br>';
                    $indice1++;
                }
            }
            echo '</div>';
            echo '<div>';

            echo 'Tipo de Unidade: <br>';

            $nomesunidades = sprintf("SELECT name FROM subitem_unit_type");
            $tipounidades = mysqli_query($mysqli, $nomesunidades);

            echo '<select name="tipo_unidade">';
            echo '<option value="NULL"></option>';
            if (mysqli_num_rows($tipounidades) > 0) {
                while ($row5 = $tipounidades->fetch_array()) {
                    echo '<option value=' . $row5["name"] . '>' . $row5["name"] . '</option>';
                }
            }
            echo '<br></select>';
            echo '</div>';
            echo '<div>
            Ordem do campo no formulário: <span class="error">*</span> <input type="text" name="nomeordem"><br>
            Obrigatório:<span class="error">*</span><br>.<input type="radio" name= "tipo_estado" value="1">Sim <br>.<input type="radio" name= "tipo_estado" value="0">Não
            
            <input type = "hidden" value = "inserir" name ="estado">
			<input type ="submit" value = "Inserir subitem">
            </div>
            </form>';


        } elseif ($_POST["estado"] == "inserir") {

            $erros = 0;
            $nome_item = $_REQUEST["nome_item"];
            $tipo_unidade = $_REQUEST["tipo_unidade"];

            //---------------------------------------------------VALIDAÇÃO SERVER SIDE----------------------------------------------//
            if (empty($_REQUEST["nome"])) {
                echo "O nome é obrigatório!";
                echo "<br>";
                $erros++;
            } else {
                $nome = test_input($_REQUEST["nome"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["nome"])) {
                    echo "Nome no formato errado, apenas carateres!!!!";
                    echo "<br>";
                    $erros++;
                }
            }
            if (empty($_REQUEST["tipo_valor"])) {
                echo "O tipo é obrigatório!";
                echo "<br>";
                $erros++;
            }
            elseif (empty($_REQUEST["tipo_form"])) {
                echo "O tipo do campo do formulário obrigatório!";
                echo "<br>";
                $erros++;

            } else {
                /*De modo a proporcionar um componente correto
             * De acordo com a inserção de valores
             * na parte de inserção de valores é dito o seguinte:
             * para o tipo text apresentar um input do tipo text ou textbox (conforme o tipo de campo especificado na BD)
             * para o tipo bool apresentar um input do tipo radio
             * para os tipos int e double apresentar um input do tipo text
             * para o tipo enum apresentar um input do tipo radio, checkbox ou selectbox (conforme o tipo de campo especificado na BD) em que as opções são obtidas através de uma query à tabela subitem_allowed_value
             *
             * Para as inserções estarem corretas foi feita as seguintes verificações
             */

                $tipo_form = $_REQUEST["tipo_form"];
                $tipo_valor = $_REQUEST["tipo_valor"];
                if($tipo_valor == "text" && ($tipo_form == "radio" || $tipo_form == "checkbox" || $tipo_form == "selectbox")){

                    echo "Para o tipo de valor text apenas pode ser selecionado os tipos de campo text ou textbox!!!";
                    echo '<br>';
                    $erros++;
                }
                elseif($tipo_valor == "bool" && ($tipo_form == "text" || $tipo_form == "checkbox" || $tipo_form == "selectbox" || $tipo_form == "textbox")){

                    echo "Para o tipo de valor bool apenas pode ser selecionado o tipo de campo radio!!";
                    echo '<br>';
                    $erros++;
                }
                elseif(($tipo_valor == "int" || $tipo_valor == "double")  && ($tipo_form == "radio" || $tipo_form == "checkbox" || $tipo_form == "selectbox" || $tipo_form == "textbox")){

                    echo "Para o tipo de valor int/double apenas pode ser selecionado o tipo de campo text!!";
                    echo '<br>';
                    $erros++;

                }
                elseif($tipo_valor == "enum" && ($tipo_form == "text" || $tipo_form == "textbox")){

                    echo "Para o tipo de valor enum apenas podem ser selecionados os tipos de campo radio/checkbox/selectbox!!";
                    echo '<br>';
                    $erros++;

                }
            }
            if (empty($_REQUEST["nomeordem"])) {
                echo "Ordem do campo no formulário é obrigatório!";
                echo "<br>";
                $erros++;
            } elseif ($_REQUEST["nomeordem"] == '0') {
                echo "Ordem do campo no formulário tem que ser maior que 0!";
                echo "<br>";
                $erros++;

            } else {
                $nomeordem = $_REQUEST["nomeordem"];
                if (!preg_match("/^[0-9]*$/", $_REQUEST["nomeordem"])) {
                    echo "Nome no formato errado, apenas carateres numéricos!!!!";
                    echo "<br>";
                    $erros++;
                }
            }
            if ($_REQUEST["tipo_estado"] == "") {
                echo "O Requesito obrigatório é necessário!!";
                echo '<br>';
                $erros++;

            }else{
                $tipo_estado = $_REQUEST["tipo_estado"];
            }

            //-------------------------------------------------------FIM DA VALIDAÇÃO SERVER SIDE--------------------------------------------------------//



            if ($erros > 0) {
                echo voltar();
            } else { // se não houver erros

                $iditem = sprintf("SELECT id FROM item WHERE name='" . $nome_item . "'");
                $resultado_iditem = mysqli_query($mysqli,$iditem);
                $item = mysqli_fetch_assoc($resultado_iditem);

                if ($tipo_unidade == "NULL") { // se nao tiver selecionado unidade

                    $nome_item = preg_replace('/[^a-z0-9_ -]/i', '', $nome_item); // transformar a string em caracteres ASCII
                    $trescaracteres = substr($nome_item, 0, 3); // seleciona os tres primeiros carateres do item
                    $stringfinal = ("$trescaracteres-x-$nome"); // poe esse carateres numa string


                    $queryfinal = sprintf("INSERT INTO subitem (name, item_id,value_type,form_field_name,form_field_type,form_field_order,mandatory,state) VALUES ('" . $nome . "','" . $item["id"] . "','" . $tipo_valor . "','" . $stringfinal . "','" . $tipo_form . "','" . $nomeordem . "','" . $tipo_estado . "','active')");
                    $resultado4 = mysqli_query($mysqli, $queryfinal);

                    $idresultado = mysqli_insert_id($mysqli); //obtem o ultimo id

                    $id_string = ("-$idresultado-");// coloca o id numa string
                    $stringfinal = str_replace("-x-", $id_string, $stringfinal); // e substitui na antiga string

                    $stringfinal = preg_replace('/[^a-z0-9_ -]/i', '', $stringfinal);
                    $stringfinal = preg_replace('/[ ]/i', '_', $stringfinal); // substituir os espaços por underscore

                    $queryupdate = sprintf("UPDATE subitem SET form_field_name='".$stringfinal."' WHERE id='".$idresultado."'"); // query para dar update à base de dados
                    $resultadoupdate = mysqli_query($mysqli,$queryupdate); // realização do update
                }
                else { // se tiver sido selecionado unidade

                    $idunidade = sprintf("SELECT id FROM subitem_unit_type WHERE name = '" . $tipo_unidade . "'"); //procura o id do tipo de unidade para ser adicionado na query mais a frente
                    $resultado_unidade = mysqli_query($mysqli, $idunidade);
                    $unidade = mysqli_fetch_assoc($resultado_unidade);
                    $idunidadefinal = $unidade["id"];

                    // o procedimento a partir daqui é igual ao if de cima

                    $nome_item = preg_replace('/[^a-z0-9_ -]/i', '', $nome_item);
                    $trescaracteres = substr($nome_item, 0, 3);
                    $stringfinal = ("$trescaracteres-x-$nome");


                    $queryfinal = sprintf("INSERT INTO subitem (name, item_id,value_type,form_field_name,form_field_type,unit_type_id,form_field_order,mandatory,state) VALUES ('" . $nome . "','" . $item["id"] . "','" . $tipo_valor . "','" . $stringfinal . "','" . $tipo_form . "','" . $idunidadefinal . "','" . $nomeordem . "','" . $tipo_estado . "','active')");
                    $resultado4 = mysqli_query($mysqli, $queryfinal);

                    $idresultado = mysqli_insert_id($mysqli);

                    $id_str = ("-$idresultado-");
                    $stringfinal = str_replace("-x-", $id_str, $stringfinal);

                    $stringfinal = preg_replace('/[^a-z0-9_ -]/i', '', $stringfinal);
                    $stringfinal = preg_replace('/[ ]/i', '_', $stringfinal);

                    $queryupdate = sprintf("UPDATE subitem SET form_field_name='".$stringfinal."' WHERE id='".$idresultado."'");
                    $resultadoupdate = mysqli_query($mysqli,$queryupdate);

                }



                if($resultadoupdate){ // se tiver sido realizado o update

                    echo 'Inseriu os dados de novo subitem com sucesso.!!';
                    echo '<br>';
                    echo "Clique em ";
                    echo '<a href="">continuar</a>';
                    echo " para avançar";
                }
                else{ // caso contrário
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