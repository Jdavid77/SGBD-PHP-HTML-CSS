<?php require_once("custom/php/common.php");

if ( is_user_logged_in() ) {
    if (current_user_can('insert_values') == false) {
        echo 'Não tem autorização a esta página';
    } else {
        $data_nascimento = $person_name ="";
        $edicaodedados = "inserirvalores";

        if (empty($_REQUEST["estado"])) {


            echo "<h3 class='h3div'>Inserção de valores - criança - procurar</h3>";
            echo '<form method="post" action="">  
			Nome completo:  <input type="text" name="person_name"> 
			Data nascimento:  <input type = "date"  name = "data_nascimento">
			<input type = "hidden" value = "escolher_crianca" name ="estado">
			<input type ="submit" value = "Submeter">			
			';
        }
        else if ($_REQUEST["estado"]=="escolher_crianca"){
            echo "<h3 class='h3div'>Inserção de valores - criança - escolher</h3>";
            $data_nascimento = test_input($_REQUEST["data_nascimento"]);
            $person_name = test_input($_REQUEST["person_name"]);


            $query = "SELECT name, birth_date, id FROM child WHERE name LIKE '%".$person_name."%' AND birth_date LIKE '%".$data_nascimento."%'"; // Query de pesquisa à BD com as informações dadas sobre a criança
            $resultado = mysqli_query($mysqli, $query);
            if (mysqli_num_rows($resultado)> 0){
                echo "<dl>";
                while ($row = $resultado->fetch_assoc()) {
                    echo "<li> <a href= 'insercao-de-valores?estado=escolher_item&crianca_id=".$row['id']."&crianca=".$row['name']."'> [".$row['name']."] </a> (".$row["birth_date"]. ") </li>";
                }
                echo "</dl>";
                echo "<br>";
                echo voltar();
            } else {
                echo "Não existem crianças na base de dados com esses dados. <br> ";
                echo "Insera novamente os dados -> ";
                echo voltar();
            }
        }
        else if ($_REQUEST["estado"]=="escolher_item"){
            echo "<h3 class='h3div'>Inserção de valores - escolher item</h3>";
            $_SESSION["child_id"] = $_REQUEST["crianca_id"];
            $_SESSION["child_name"] = $_REQUEST["crianca"];

            $query_tipo_item = "SELECT DISTINCT  item_type.code, item_type.id from item_type, subitem, item WHERE subitem.item_id = item.id AND item.item_type_id = item_type.id"; //Query à BD que procura todos os ids e codes dos tipos de item
            $resultado_tipo_item = mysqli_query($mysqli, $query_tipo_item);
            if (mysqli_num_rows($resultado_tipo_item)> 0){
                echo "<dl>";
                while ($row_tipo_item = $resultado_tipo_item->fetch_assoc()) { // While para percorrer os tipos de item
                    $query_item = "SELECT DISTINCT item.id, item.name, item.item_type_id, item.state FROM item, subitem WHERE item_type_id=".$row_tipo_item["id"]." AND subitem.item_id = item.id"; //Query à BD que procura o id, nome, e o estado dos itens do tipo de item que está no while
                    $resultado_item = mysqli_query($mysqli, $query_item);
                    echo "<dt> ".$row_tipo_item["code"]." </dt>"; // echo do tipo de item que está no while
                    if (mysqli_num_rows($resultado_item)> 0){ // While para percorrer os itens que pertecem ao tipo de item que está no while
                        while ($row_item = $resultado_item->fetch_assoc()) {
                            $query_verifica_bd = "SELECT DISTINCT subitem.item_id  FROM value, subitem WHERE value.child_id =".$_SESSION["child_id"]." AND value.subitem_id = subitem.id AND subitem.item_id =".$row_item['id'];
                            $resultado_verifica_bd = mysqli_query($mysqli, $query_verifica_bd);
                            if(mysqli_num_rows($resultado_verifica_bd)>0){
                                echo "<dd> <a href= 'edicao-de-dados?estado=editar&tipo=".$edicaodedados."&item=".$row_item['id']."&crianca=".$_SESSION["child_id"]."'> [EDITAR]-[".$row_item["name"]."] </a> </dd>"; //echo do item com ligação para editar os dados
                            } else {
                                echo "<dd> <a href= 'insercao-de-valores?estado=introducao&item=".$row_item['id']."'> [".$row_item["name"]."] </a> </dd>"; //echo do item com ligação para o próximo estado
                            }


                        }
                    } else {
                        echo "<dd> Este tipo de item não contém itens adicionados na Base de Dados! </dd>";
                    }
                }
                echo "</dl>";
            } else {
                echo "Não existem tipos de itens na base de dados! <br>";

            }


        }
        else if ($_REQUEST["estado"]=="introducao"){
            $_SESSION["item_id"] = $_REQUEST["item"];



            $query_introducao = "SELECT name, item_type_id FROM item WHERE id =".$_SESSION["item_id"]; // Busca o nome e o id do tipo de item do item escolhido para guardar nas variaveis de secção
            $resultado_introducao =  mysqli_query($mysqli, $query_introducao);
            if (mysqli_num_rows($resultado_introducao)> 0){
                $row_introducao = $resultado_introducao->fetch_assoc();
                $_SESSION["item_name"] = $row_introducao["name"];
                $_SESSION["item_type_id"] =$row_introducao["item_type_id"];
            }
            echo "<h3 class='h3div'>Inserção de valores - ".$_SESSION["item_name"]."</h3>";
            $query_sub_item = "SELECT id,name,item_id,value_type,form_field_name,form_field_type,unit_type_id,form_field_order,mandatory,state FROM subitem WHERE item_id =" . $_SESSION["item_id"];
            $resultado_sub_item = mysqli_query($mysqli, $query_sub_item);
            echo "<form method = '' action = 'insercao-de-valores?estado=validar&item=".$_SESSION['item_id']."' name ='item_type_".$_SESSION['item_type_id']."_item_".$_SESSION['item_id']."'>";
            if (mysqli_num_rows($resultado_sub_item)> 0){
                while ($row_sub_item = $resultado_sub_item->fetch_assoc()) {
                    if ($row_sub_item["state"] == "active"){
                        $temp = $row_sub_item["value_type"];

                        if($row_sub_item["unit_type_id"] != ""){
                            $query_unidade ="SELECT name FROM subitem_unit_type WHERE id =".$row_sub_item["unit_type_id"];
                            $resultado_unidade = mysqli_query($mysqli, $query_unidade);
                            if(mysqli_num_rows($resultado_unidade)> 0){
                                $row_unidade = $resultado_unidade->fetch_assoc();
                                echo "<strong>".$row_sub_item["name"].": </strong> Unidade: ".$row_unidade["name"]."<br>";
                            }

                        }else {
                            echo "<strong>".$row_sub_item["name"].": </strong>";
                        }

                        switch($temp){


                            case "text":
                                echo "<input type = ".$row_sub_item["form_field_type"]." name = '".$row_sub_item["form_field_name"]."' > ";
                                break;
                            case "bool":
                                echo '<input type="radio" name= "'.$row_sub_item["form_field_name"].'" value="true">True <br><input type="radio" name= "'.$row_sub_item["form_field_name"].'" value="false">False <br>';
                                break;
                            case "enum":

                                $query_allowed_value = "SELECT value FROM subitem_allowed_value WHERE subitem_id =".$row_sub_item["id"];
                                $resultado_allowed_value = mysqli_query($mysqli, $query_allowed_value);
                                if ($row_sub_item["form_field_type"] == "selectbox"){
                                    echo "<select name = '".$row_sub_item["form_field_name"]."' id = '".$row_sub_item["form_field_name"]."'>";
                                    echo "<label for '".$row_sub_item["form_field_name"]."'>".$row_sub_item["form_field_name"].": </label>";
                                    if (mysqli_num_rows($resultado_allowed_value) >0){
                                        while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
                                            echo "<option value = '".$row_allowed_value["value"]."'>".$row_allowed_value["value"]."</option>";
                                        }
                                    }
                                    echo "</select>";

                                }
                                else if ($row_sub_item["form_field_type"] == "radio"){
                                    if (mysqli_num_rows($resultado_allowed_value) >0){
                                        while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
                                            echo "<input type='radio' id='".$row_allowed_value["value"]."' name='".$row_sub_item["form_field_name"]."' value='".$row_allowed_value["value"]."'>";
                                            echo "<label for='".$row_allowed_value["value"]."'>".$row_allowed_value["value"]."</label>";
                                        }
                                    }


                                }
                                else if ($row_sub_item["form_field_type"] == "checkbox"){
                                    if (mysqli_num_rows($resultado_allowed_value) >0){
                                        while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
                                            echo "<input type='checkbox' id='".$row_allowed_value["value"]."' name='".$row_sub_item["form_field_name"]."-".$row_allowed_value["value"]."' value='".$row_allowed_value["value"]."'>";
                                            echo "<label for='".$row_allowed_value["value"]."'>".$row_allowed_value["value"].": </label>";
                                        }
                                    }
                                }
                                echo "<br>";
                                break;
                            default:

                                echo "<input type = 'text' name = '".$row_sub_item["form_field_name"]."'>";
                                break;
                        }
                    }
                }
                echo "
							<input type = 'hidden' value = 'validar' name ='estado'>
							<input type ='submit' value = 'Submeter'>
							</form>";

            }else {
                echo "Não existem subitens associados a este item! <br>";
                echo Voltar();
            }

        }
        else if ($_REQUEST["estado"]=="validar"){


            echo "<h3 class='h3div'> Inserção de valores - ".$_SESSION["item_name"]." - validarr</h3>";
            echo "<strong>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos? </strong> <br><br>";
            echo "<strong>Nome da criança: </strong>".$_SESSION["child_name"]."<br>";
            echo "<form method = '' action = 'insercao-de-valores?estado=inserir&item=".$_SESSION['item_id']."' >";
            $erros_validar =0;
            foreach($_REQUEST as $key => $value) {



                if($key != "estado"){

                    $key_partida = explode("-",$key);
                    $valor = $key_partida[0]."-".$key_partida[1]."-".$key_partida[2];

                    $query_validar = "SELECT name, mandatory FROM subitem WHERE form_field_name = '".$valor."'";
                    $resultado_validar = mysqli_query($mysqli, $query_validar);
                    if(mysqli_num_rows($resultado_validar)> 0){
                        $row_validar = $resultado_validar->fetch_assoc();

                        if($row_validar["mandatory"]==1){
                            if($value == ""){
                                echo "<strong>ERRO </strong>".$row_validar["name"]." é obrigatorio <br>";
                                //echo Voltar();
                                $erros_validar++;
                            }else {

                                echo "<strong>".$row_validar["name"]." = </strong> ".$value." <br>" ;
                                echo "<input type = 'hidden' value = '".$value."' name ='".$row_validar["name"]."-".$value."'>";
                            }

                        } else{
                            if($value != ""){
                                echo "<strong>".$row_validar["name"]." = </strong> ".$value." <br>" ;
                                echo "<input type = 'hidden' value = '".$value."' name ='".$row_validar["name"]."-".$value."'>";
                            }
                            //$row_validar = $resultado_validar->fetch_assoc();

                        }
                    }
                }

            }
            if($erros_validar >0){
                echo Voltar();
            }else{
                echo "
				<input type = 'hidden' value = 'inserir' name ='estado'>
				<input type ='submit' value = 'Submeter'>
				</form>";
            }


        }
        else if ($_REQUEST["estado"]=="inserir"){
            $i_inserido = 0;
            $i_total = 0;
            echo "<h3 class='h3div'>Inserção de valores - ".$_SESSION["item_name"]." - inserção	</h3>";
            foreach($_REQUEST as $key => $value) {
                if($key != "estado"){
                    $key_partida_inserir = explode("-",$key);
                    $nome = $key_partida_inserir[0];
                    $i_total +=1;
                    $query_sub_id = "SELECT id FROM subitem WHERE name = '".$nome."'";
                    $resultado_sub_id = mysqli_query($mysqli, $query_sub_id);
                    if(mysqli_num_rows($resultado_sub_id)> 0){
                        $row_sub_id = $resultado_sub_id->fetch_assoc();
                        $current_user = wp_get_current_user();
                        $producer =$current_user->user_login;
                        $time = date("H:i:s");
                        $date = date("Y-m-d");
                        $id = $row_sub_id["id"];
                        $query_inserir = sprintf("INSERT INTO value (child_id,subitem_id,value,date,time,producer) VALUES('".$_SESSION["child_id"]."','".$id."','".$value."','".$date."','".$time."','".$producer."')");
                        if( mysqli_query($mysqli,$query_inserir)){
                            $i_inserido +=1;
                        }else{
                            echo "Ocorreu um erro: ";
                            echo "<br>";
                            echo mysqli_error($mysqli);
                        }

                    }else{

                    }
                }else{

                }
            }
            if($i_total == $i_inserido){
                echo $i_total;
                echo "<br>";
                echo $i_inserido;
                echo "Inseriu o(s) valor(es) com sucesso. <br>";
                echo "Clique em ";
                echo '<a href="insercao-de-valores?estado="">Voltar</a>';
                echo " para voltar ao início da inserção de valores ou em ";
                echo "<a href= 'insercao-de-valores?estado=escolher_item&crianca_id=".$_SESSION['child_id']."&crianca=".$_SESSION['child_name']."'> escolher </a>";
                echo " item se quiser continuar a inserir valores associados a esta criança";
            }
        }

    }
}else{
    echo 'Não tem autorização a esta página';
}
?>