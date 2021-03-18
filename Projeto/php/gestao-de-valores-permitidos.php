<?php require_once("custom/php/common.php");

global $wp;
$current_page = add_query_arg( array(), $wp->request );

if ( is_user_logged_in() ) {
    if (current_user_can('manage_allowed_values') == false) {
        echo 'Não tem autorização a esta página';
    }
    else {


        $edicaodedados = "valorespermitidos"; // para edição de dados


        if (empty($_REQUEST["estado"])){

            $query = "SELECT DISTINCT item.name,item.id FROM subitem, item WHERE subitem.value_type ='enum' AND subitem.item_id = item.id ORDER BY item.name ASC"; // query para imprimir os itens que têm subitems com value type enum
            $resultado = mysqli_query($mysqli,$query);




            if (mysqli_num_rows($resultado)> 0) {
                echo "<table class='mytable'><tr><th>item</th><th>id</th><th>subitem</th><th>id</th><th>valores permitidos</th><th>estado</th><th>ação</th></tr>";

                while($row = $resultado->fetch_assoc() ){// query id name do item que tem enum

                    // ---------------------------------NECESSÁRIO PARA SABER O ROWSPAWN DO ITEM-----------------------------------
                    $item_span = 0;
                    $query_num = "SELECT id FROM subitem WHERE item_id ='" . $row["id"] . "'AND subitem.value_type = 'enum' ";  //query id dos subitens com o id do outro query e do tipo enum
                    $result_num = mysqli_query($mysqli, $query_num);
                    while ($row2 = $result_num->fetch_assoc()) { //serve para quando houver um subitem sem valores permitidos a tabela não ficar desalinhada 
                        $query_num2 = "SELECT * FROM subitem_allowed_value WHERE subitem_id = '" . $row2["id"] . "'";
                        $result_num2 = mysqli_query($mysqli, $query_num2);
                        if (mysqli_num_rows($result_num2) == 0) {
                            $item_span = $item_span + 1;
                        } else {
                            while ($row3 = $result_num2->fetch_array()) {
                                $item_span++;
                            }
                        }
                    }
                    //-------------------------------------------------------------------------------------------------------------

                    echo "<tr><td rowspan= '".$item_span."'> ".$row["name"]." </td>";  //rowspan do nome que usa o valor do item_span de cima para não haver tabelas desalinhadas
                    $query_subitem = 'SELECT id,name from subitem WHERE value_type = "enum" AND item_id='.$row["id"]; //query do id e do nome do subitem  onde é do tipo enum e com o item_id dos itens selecionados no 1º query
                    $resultado_subitem = $resultado_num = mysqli_query($mysqli,$query_subitem);
                    if (mysqli_num_rows($resultado_subitem)> 0) {
                        while($row_subitem = $resultado_subitem->fetch_assoc() ){
                            $query_allowed = "SELECT id,value, state FROM subitem_allowed_value WHERE subitem_id =".$row_subitem["id"];//query para ir buscar as variáveis do subitem_allowed_value
                            $resultado_allowed = mysqli_query($mysqli,$query_allowed);
                            
                            if (mysqli_num_rows($resultado_allowed)> 0) {
                                echo "<td rowspan ='".mysqli_num_rows($resultado_allowed)."' >".$row_subitem["id"]."</td>"; //rowspan do id do subitem
                                echo "<td rowspan ='".mysqli_num_rows($resultado_allowed)."' >[<a href='gestao-de-valores-permitidos?estado=introducao&subitem=".$row_subitem['id']."'>".$row_subitem["name"]."</a>]</td>";

                                $indice_allowed = 0; //Usado para os valores permitidos não ficarem desalinhados 
                                while($row_allowed = $resultado_allowed->fetch_assoc() ){
                                    if($indice_allowed ==0){//só é usado no primeiro subitem
                                        echo "<td>".$row_allowed["id"]."</td>";
                                        echo "<td>".$row_allowed["value"]."</td>";
                                        echo "<td>".$row_allowed["state"]."</td>";
                                        echo "<td> <a href='edicao-de-dados?estado=editar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Editar]</a>";
                                        if ($row_allowed["state"] == "active"){ //usado para mostrar desativar se o estado for ativar 
                                            echo "<a href='edicao-de-dados?estado=desativar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Desativar]</a> </td>";
                                        }else {//se não mete o ativar 
                                            echo "<a href='edicao-de-dados?estado=ativar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Ativar]</td>";
                                        }
                                        echo "</tr>";
                                        $indice_allowed +=1;
                                    }else { // abre e fecha um novo row para não haver nenhum dado desalinhado
                                        echo "<tr> <td>".$row_allowed["id"]."</td>";
                                        echo "<td>".$row_allowed["value"]."</td>";
                                        echo "<td>".$row_allowed["state"]."</td>";
                                        echo "<td> <a href='edicao-de-dados?estado=editar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Editar]</a>";
                                        if ($row_allowed["state"] == "active"){//usado para mostrar desativar se o estado for ativar 
                                            echo "<a href='edicao-de-dados?estado=desativar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Desativar]</a> </td>";
                                        }else {//se não mete o ativar 
                                            echo "<a href='edicao-de-dados?estado=ativar&tipo=".$edicaodedados."&id=".$row_allowed["id"]."'>[Ativar]</td>";
                                        }
                                        echo "</tr>";
                                    }

                                }
                            }
                            else{ //quando não tem valores permitidos
                                echo "<td >".$row_subitem["id"]."</td>";
                                echo "<td >[<a href='gestao-de-valores-permitidos?estado=introducao&subitem=".$row_subitem['id']."'>".$row_subitem["name"]."</a>]</td>";
                                echo "<td> Não há valores permitidos definidos</td></tr>";
                            }

                        }
                    }


                }




                echo "</table>";
            }
            else{
                echo "Não há subitems especificados cujo tipo de valor seja enum. Especificar primeiro novo(s) iten(s) e depois voltar a esta opção.";
            }




        }else if ($_REQUEST["estado"] == "introducao"){ //Request de variável de sessão para o estado e para o id do subitem
            $_SESSION["subitem_id"] = $_REQUEST["subitem"]; //guarda o valor do id do subitem numa variável de sessão
            //imprime o formulário
            echo "<h3 class='h3div'>Gestão de valores permitidos - introdução </h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo'<form metdod="post" action="">  
			Valor: <span class="error">*</span> <input type="text" name="valor">
			<input type = "hidden" value = "inserir" name ="estado">
			<input type ="submit" value = "Submeter">
			</form>';
            echo voltar();


        }else if ($_REQUEST["estado"] == "inserir"){

            echo "<h3 class='h3div'>Gestão de valores permitidos - inserção </h3>";

            $erros = 0;
            $subitem_id = $_SESSION["subitem_id"];
			
			//verificação dos valores inseridos
            if (empty($_REQUEST["valor"])) {
                echo "O Valor é obrigatório!";
                echo "<br>";
                $erros++;
            } else {
                $valor = test_input($_REQUEST["valor"]);
                //$valor = $_REQUEST["valor"];
            }
            if ($erros > 0) {
                echo voltar();

            } else {//quando os valores inseridos são válidos faz a query com o valores inseridos e executa


                $iquery =sprintf ("INSERT INTO subitem_allowed_value (subitem_id,value,state) VALUES('".$subitem_id."','".$valor."','active')");

                $inserir = mysqli_query($mysqli,$iquery);
                if($inserir){

                    echo 'Inseriu os dados de novo subitem allowed value com sucesso.';
                    echo '<br>';
                    echo "Clique em ";
                    echo '<a href="'.$current_page.'">continuar</a>';
                    echo " para avançar";
                }
                else{
                    echo "Ocorreu um erro: ";
                    echo "<br>";
                    echo mysqli_error($mysqli);
                    echo "Clique em ";
                    echo '<a href="'.$current_page.'">continuar</a>';
                    echo " para avançar";

                }

            }
        }


    }
}