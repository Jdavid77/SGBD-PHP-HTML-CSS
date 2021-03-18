
<?php require_once("custom/php/common.php");

if ( is_user_logged_in() ) {
    if (current_user_can('search') == false) {
        echo 'Não tem autorização a esta página';
    }
    else {
		
		
		
		
		if (empty($_REQUEST["estado"])) {
			echo "<h3 class='h3div'>Pesquisa - escolher iteeem</h3>";
			
			$query_tipo_item = "SELECT id, code FROM `item_type`"; //Query à BD que procura todos os ids e codes dos tipos de item
			$resultado_tipo_item = mysqli_query($mysqli, $query_tipo_item);
			if (mysqli_num_rows($resultado_tipo_item)> 0){
				echo "<dl>";
				while ($row_tipo_item = $resultado_tipo_item->fetch_assoc()) { // While para percorrer os tipos de item
					$query_item = "SELECT id, name, item_type_id,state FROM item WHERE item_type_id=".$row_tipo_item["id"]; //Query à BD que procura o id, nome, e o estado dos itens do tipo de item que está no while
					$resultado_item = mysqli_query($mysqli, $query_item);
					echo "<dt> ".$row_tipo_item["code"]." </dt>"; // echo do tipo de item que está no while
					if (mysqli_num_rows($resultado_item)> 0){ // While para percorrer os itens que pertecem ao tipo de item que está no while
						while ($row_item = $resultado_item->fetch_assoc()) {
							//echo "<dd> <a href= 'insercao-de-valores?estado=introducao&item=".$row_item['id']."'> [".$row_item["name"]."] </a> </dd>"; //echo do item com ligação para o próximo estado
							echo "<dd> <a href= 'pesquisa?estado=escolha&item=".$row_item['id']."'> [".$row_item["name"]."] </a> </dd>"; //echo do item com ligação para o próximo estado
						}
					} else {
						echo "<dd> Este tipo de item não contém itens adicionados na Base de Dados! </dd>"; 
					}
				}
				echo "</dl>";
			} else {
				echo "Não existem tipos de itens na base de dados! <br>";
				
			}
		} else if ($_REQUEST["estado"]=="escolha"){
			$_SESSION["item_id"] = $_REQUEST["item"];
			$query_introducao = "SELECT name FROM item WHERE id =".$_SESSION["item_id"]; // Busca o nome do item escolhido para guardar nas variaveis de sessão
			$resultado_introducao =  mysqli_query($mysqli, $query_introducao);
			if (mysqli_num_rows($resultado_introducao)> 0){
				$row_introducao = $resultado_introducao->fetch_assoc();
				$_SESSION["item_name"] = $row_introducao["name"];				
			}
			
			echo '<form method="post" action="">'; // Imprime a tabela com os atributos das crianças e as checkboxes para os mesmos
			echo '<table class="mytable"><tr><th>Atributo</th><th>obter</th><th>filtro</th></tr>';
			echo '<tr><td>Nome</td><td><input type="checkbox" name="nome_obter" value="true"></td><td><input type="checkbox" name="nome_filtro" value="true"></td></tr>';
			echo '<tr><td>Data de Nascimento</td><td><input type="checkbox" name="data_obter" value="true"></td><td><input type="checkbox" name="data_filtro" value="true"></td></tr>';
			echo '<tr><td>Nome E.E.</td><td><input type="checkbox" name="nome-ee_obter" value="true"></td><td><input type="checkbox" name="nome-ee_filtro" value="true"></td></tr>';
			echo '<tr><td>Telefone E.E.</td><td><input type="checkbox" name="telefone_obter" value="true"></td><td><input type="checkbox" name="telefone_filtro" value="true"></td></tr>';
			echo '<tr><td>Email E.E.</td><td><input type="checkbox" name="mail_obter" value="true"></td><td><input type="checkbox" name="mail_filtro" value="true"></td></tr>';
			echo '</table>';
			echo '<table class ="mytable"><tr><th>Nome do subitem</th><th>obter</th><th>filtro</th></tr>'; //Imprime a tabela com os subitem do item escolhido e as checkboxes para o mesmo
			
			$query_subitem = "SELECT name, id from subitem WHERE item_id=".$_SESSION["item_id"]; //query para buscar nome e id do subitem
			$resultado_subitem = mysqli_query($mysqli,$query_subitem);
			//ciclo para imprimir os subitens
			
			if(mysqli_num_rows($resultado_subitem)>0){ // se existir subitem
				$n = 0;
				while($row_subitem = $resultado_subitem->fetch_assoc()){ //percorre todos os subitens encontrados e imprime para cada um o seu nome e as checkboxes para filtrar e obter					
					echo '<tr><td>'.$row_subitem["name"].'</td><td><input type="checkbox" name="'.$row_subitem["id"].'_obter" value="true"></td><td><input type="checkbox" name="'.$row_subitem["id"].'_filtro" value="true"></td></tr>';
					
				}
			}
			
			echo '</table>';
			
			echo '<input type = "hidden" value = "escolher_filtros" name ="estado">';
			echo '<input type ="submit" value = "Submeter">';
			echo '</form>';
			echo '<br>';
			echo voltar();
			
		} else if ($_REQUEST["estado"]=="escolher_filtros"){ //caso estado seja escolher filtros
			$obter = array(); //array para guardar as coisas que são para obter 
			$filtrar = array();//array para guardar as coisas que são para filtrar 
			$array_dados_crianca = array("nome","data","nome-ee","telefone","mail"); //array com os dados de crianças
			foreach($_REQUEST as $key => $value){ // Percorre o _REQUEST à procura do valor das checkboxes
				if($value == "true"){
					$key_partida = explode("_",$key); //Divide o nome das checkboxes
					$nome = $key_partida[1]; //Verifica se é obter ou filtrar
					if($nome =="obter"){ //Caso seja obter
						array_push($obter,$key_partida[0]); //Guarda no array
					}else if($nome =="filtro"){ //Caso seja filtrar
						array_push($filtrar,$key_partida[0]); //Guarda no array
					}
				}				
			}
			
			echo '<form method ="post" action="">';
			echo 'Irá ser realizada uma pesquisa que irá obter, como resultado, uma listagem de, para cada criança, dos seguintes dados pessoais escolhidos:<br>';
			foreach($array_dados_crianca as $x){ // Imprime os filtros selecionados que estão relacionados com os dados pessoais da crianca
				if (in_array($x,$filtrar)){ // Se estiver no array filtrar
					if($x=="nome"){ // Caso seja nome
						echo '<strong> Filtros Nome: </strong>';
						echo '<select name="nome_f" >
						<option value="igual"> = </option> 
						<option value="diferente"> != </option> 				
						<option value="tipo"> like </option>
						</select>
						<input type="text" name="nome_ftext">';
						$_SESSION["nome_filtro"] = TRUE;
					}else if ($x=="data"){// Caso seja data de nascimento
						echo '<strong> Filtros Data de Nascimento : </strong>';
						echo '
						<select name="data_nasc_f" >
						<option value="maior"> > </option> 
						<option value="maior_igual"> >= </option> 
						<option value="menor"> < </option> 
						<option value="menor_igual"> <= </option> 
						<option value="igual"> = </option> 
						<option value="diferente"> != </option> 				
						</select>
						<input type="date" name="data_nasc_ftext">';
						$_SESSION["data_filtro"] = TRUE;						
					}else if ($x=="nome-ee"){// Caso seja nome do ee
						echo '<strong> Filtros Nome do E.E. : </strong>';
						echo '<select name="nome_ee_f" >
						<option value="igual"> = </option> 
						<option value="diferente"> != </option> 				
						<option value="tipo"> like </option>
						</select>
						<input type="text" name="nome_ee_ftext">';
						$_SESSION["nome_ee_filtro"] = TRUE;
						
					}else if ($x=="telefone"){// Caso seja telefone do ee
						echo '<strong> Filtros Telefone E.E.: </strong>';
						echo '<select name="telefone_ee_f" > 
						<option value="igual"> = </option> 
						<option value="diferente"> != </option> 				
						<option value="tipo"> like </option>
						</select>
						<input type="number" name="telefone_ee_ftext" placeholder="969696969" pattern="[0-9]{9}">';
						$_SESSION["telefone_filtro"] = TRUE;
						
					}else if ($x=="mail"){// Caso seja mail do ee
						echo '<strong> Filtros Email do E.E. : </strong>';				
						echo '<select name="email_ee_f" >
						<option value="igual"> = </option> 
						<option value="diferente"> != </option> 				
						<option value="tipo"> like </option>
						</select>
						<input type="text" name="email_ee_ftext">';
						$_SESSION["mail_filtro"] = TRUE;
						
					}
				} else if(in_array($x,$obter) && (in_array($x,$filtrar)== false)){ // Caso seja apenas para obter o atributo sem filtrar pelo mesmo
					echo '<strong> '.$x.' </strong><br>';
				} 
			}
			


			$query_subitem_2 = "SELECT * from subitem WHERE item_id=".$_SESSION["item_id"]; // Query que devolve tudo sobre os subitens do item escolhido
			$resultado_subitem_2 = mysqli_query($mysqli,$query_subitem_2);
					
			
			echo 'e do item: ' .$_SESSION["item_name"].' uma listagem dos valores dos subitens: <br>'; // Texto do moodle
			
			while($row_subitem_2  = $resultado_subitem_2->fetch_array()){ // Percorre o resultado da query
				if(in_array($row_subitem_2["id"],$filtrar)){ // Verifica se esse subitem está no array filtrar Caso esteja verifica o tipo de valor  para o mesmo e os operadores
					echo '<strong> Filtros '.$row_subitem_2["name"].': </strong>';	
					switch($row_subitem_2["value_type"]){	
									case "text": 
										echo "<input type = ".$row_subitem_2["form_field_type"]." name = ".$row_subitem_2["name"].'ftext" > ';
										echo '<select name="subitem_'.$row_subitem_2["name"].'f" >';
										echo'<option value="igual"> = </option> 
										<option value="diferente"> != </option> 				
										<option value="tipo"> like </option>
										</select>';
										break;
									case "bool":
										echo '<input type = "radio" name = "'.$row_subitem_2["name"].'f">';
										echo '<select name="subitem_'.$row_subitem_2["name"].'f" >
										<option value="igual"> = </option> 
										<option value="diferente"> != </option> </select>' ;
										
										break;
									case "enum":
										$query_allowed_value = "SELECT value FROM subitem_allowed_value WHERE subitem_id =".$row_subitem_2["id"]; // Query dos valores permitdos do subitem que está a ser percorrido
										$resultado_allowed_value = mysqli_query($mysqli, $query_allowed_value);
										if ($row_subitem_2["form_field_type"] == "selectbox"){
											echo '<select name = "'.$row_subitem_2["name"].'f" id = "'.$row_subitem_2["name"].'f">';
											echo "<label for '".$row_subitem_2["form_field_name"]."'>".$row_subitem_2["form_field_name"].": </label>";
											echo 'boas';
											if (mysqli_num_rows($resultado_allowed_value) >0){
												while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
													echo "<option value = '".$row_allowed_value["value"]."'>".$row_allowed_value["value"]."</option>";
												}
											}
											echo "</select>";
											
										}
										else if ($row_subitem_2["form_field_type"] == "radio"){
											if (mysqli_num_rows($resultado_allowed_value) >0){
												while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
													echo '<input type="radio" id="'.$row_allowed_value["value"]."' name='".$row_subitem_2["name"].'f" value="'.$row_allowed_value["value"].'">';
													echo "<label for='".$row_allowed_value["value"]."'>".$row_allowed_value["value"]."</label>";
												}
											}
											
											
										}
										else if ($row_subitem_2["form_field_type"] == "checkbox"){
											if (mysqli_num_rows($resultado_allowed_value) >0){
												while ($row_allowed_value = $resultado_allowed_value->fetch_assoc()) {
													echo '<input type="checkbox" id="'.$row_allowed_value["value"]."' name='".$row_subitem_2["name"].'f" value="'.$row_allowed_value["value"].'">';
													echo "<label for='".$row_allowed_value["value"]."'>".$row_allowed_value["value"].": </label>";
												}
											}
										}
										echo '<select name="subitem_'.$row_subitem_2["name"].'" >
										<option value="igual"> = </option> 
										<option value="diferente"> != </option> </select>' ;
										echo "<br>";
										break;
									default:	//Os restantes tipos de valores ficam no default
										
										echo '<input type = "text" name = "'.$row_subitem_2["name"].'ftext">';
										echo '<select name="subitem_'.$row_subitem_2["name"].'f" >
											<option value="maior"> > </option> 
											<option value="maior_igual"> >= </option> 
											<option value="menor"> < </option> 
											<option value="menor_igual"> <= </option> 
											<option value="igual"> = </option> 
											<option value="diferente"> != </option> 				
											</select>';
										break;
								}
				}else if(in_array($row_subitem_2["id"],$obter) && (in_array($row_subitem_2["id"],$filtrar)== false)){ // Caso seja apenas para obter o subitem sem filtrar pelo mesmo
					echo '<strong> '.$row_subitem_2["name"].' </strong><br>';
				}			
			}		
			
			echo '<input type = "hidden" value = "execucao" name ="estado">';
			echo '<input type ="submit" value = "Submeter">';
			echo '<br>';
			$_SESSION["array_obter"] = $obter;  //Guarda os arrays em variavel local
			$_SESSION["array_filtrar"] =$filtrar; 
			 
			echo voltar();
			
		} else if ($_REQUEST["estado"]=="execucao"){
			
			
			$array_dados_crianca = array("nome","data","nome-ee","telefone","mail");
			echo "<table><tr><th>Criança</th>";
			foreach($array_dados_crianca as $x){ // Imprime os atributos selecionados que estão relacionados com os dados pessoais da crianca no cabeçalho da tabela
				if (in_array($x,$_SESSION['array_obter'])){
					echo "<th>".$x."</th>";
				}
			}
			foreach($_SESSION['array_obter'] as $variavel_obter){  // Imprime os subitens selecionados no cabeçalho da tabela
				if(in_array($variavel_obter,$array_dados_crianca) == false){
					$query_subitem4 = "SELECT name from subitem WHERE id=".$variavel_obter;
					$resultado_subitem4 = mysqli_query($mysqli,$query_subitem4);
					 if (mysqli_num_rows($resultado_subitem4) > 0) {
						 $row2 = $resultado_subitem4->fetch_array();
						 echo "<th>".$row2["name"]."</th>";
					 }
				}
			}
			echo "</tr>";
			
			//Tentamos fazer a query porém tivemos dificuldades e não a conseguimos concluir ficando só o cabeçalho da tabela feita, sem mostrar algum tipo de valor
			
			//$query_dinamica ="SELECT child.* FROM child WHERE "; 
			
			
			echo "</table>";
			
			
			
			
	
		
			
			echo voltar();
		}

        
    }


}