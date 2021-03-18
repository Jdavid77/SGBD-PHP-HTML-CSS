<?php require_once("custom/php/common.php");


$name_error = "";
$tipoitemerror = "";
$tipo_valor_error = "";
$tipo_form_error = "";
$nome_ordem_error = "";
$tipo_estado_error = "";
$error_tipos = "";

if ($_GET["estado"] == "editar") {
    if ($_REQUEST["tipo"] == "item") {
        $erros = 1; // necessário para aparecer o formulário!!!

        if(isset($_REQUEST["Submit"])) { // validação
            $erros = 0;

                if (empty($_REQUEST["nome"])) {

                    $name_error = '<p>"Nome é obrigatório!!"</p>';
                    $erros++;

                }else {
                    if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["nome"])) {
                        $name_error = '<p>"Nome no formato errado, apenas carateres!!!!"</p>';
                        $erros++;
                        echo "<br>";

                    }
                }if (empty($_REQUEST["tipoitem"])) {

                    $tipoitemerror = '<p>"Tipo obrigatório</p>';
                    $erros++;

                }

        }
        if($erros >0) { // apresentação do formulario com dados pre preenchidos

            $queryitens = "SELECT name, item_type_id from item WHERE id ='".$_REQUEST["id"]."'"; // procurar o item em questão e os atributos necessários para o formulario dinamico
            $resultado_item = mysqli_query($mysqli, $queryitens);
            $item = mysqli_fetch_assoc($resultado_item);

            echo "<h3 class='h3div'>Edição de Dados de Item - '".$item["name"]."'</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<div>';
            echo '<form method="post" action="">
		<br>Nome: <span class="error">*</span> <input type="text" name="nome" value="'.$item["name"].'"><br>';
            echo '<span class="error">';
            echo $name_error;
            echo '</span>';
            echo '</div>';
            echo '<div>';
            $querytipos = sprintf("SELECT code, id FROM item_type");
            $resultadotipos = mysqli_query($mysqli, $querytipos);

            echo 'Tipo: <span class="error">*</span><br>';
            if (mysqli_num_rows($resultadotipos)) {
                while ($tipos = $resultadotipos->fetch_array()) {
                    if ($tipos["id"] == $item["item_type_id"]) { // de modo a aparecer o pre selecionado
                        $linha = sprintf('.<input type="radio" name="tipoitem" value=' . $tipos["id"] . ' checked>' . $tipos["code"] . '<br>');
                        echo "$linha";
                    }
                    else{
                        $linha = sprintf('.<input type="radio" name="tipoitem" value=' . $tipos["id"] . '>' . $tipos["code"] . ' <br>');
                        echo "$linha";
                    }
                }
            }


            echo '<span class="error">';
            echo $tipoitemerror; // para imprimir o erro
            echo '</span>';
            echo '</div>';

            echo '<input type ="submit" name = "Submit" value = "Alterar item">';
            echo '</form>';
            echo '<br>';
            echo voltar();
        }
        elseif($erros == 0){
            $nomeitem = $_REQUEST["nome"];
            $quetyitens = "UPDATE item SET name ='".$nomeitem."',item_type_id ='".$_REQUEST["tipoitem"]."' WHERE id='".$_REQUEST["id"]."'";
            mysqli_query($mysqli,$quetyitens);


            //-----------------------------------------------PARA ATUALIZAR OS SUBITENS------------------------------------------//
            $trescaracteres = substr($nomeitem, 0, 3);
            $queryupdatesub = "SELECT id,name FROM subitem WHERE item_id='".$_REQUEST["id"]."'";
            $resultadosubitens = mysqli_query($mysqli,$queryupdatesub);
            while($row = $resultadosubitens->fetch_array()){
                $stringfield = ("$trescaracteres-".$row["id"]."-".$row["name"]."");
                $queryfield = "UPDATE subitem SET form_field_name='".$stringfield."'WHERE id='".$row["id"]."'";
                mysqli_query($mysqli,$queryfield);
            }
            //------------------------------------------------------------------------------------------------------------------//

            echo "Alteração feita com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-itens">continuar</a>';
            echo " para voltar à página dos itens";

        }
    }
    elseif($_REQUEST["tipo"] == "subitem"){
        $erros = 1;

        if(isset($_REQUEST["Submit"])) { // validação dos campos submetidos
            $erros = 0;

            if (empty($_REQUEST["nome"])) {

                $name_error = '<p>"Nome é obrigatório!!"</p>';
                $erros++;

            }else {
                if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["nome"])) {
                    $name_error = '<p>"Nome no formato errado, apenas carateres!!!!"</p>';
                    $erros++;


                }
            }
            if (empty($_REQUEST["tipo_valor"])){

                $tipo_valor_error = '<p>"Tipo de valor obrigatório"</p>';
                $erros++;


            }
            elseif (empty($_REQUEST["tipo_form"])){

                $tipo_form_error = '<p>"Tipo do campo do formulário é obrigatório!!"</p>';
                $erros++;

            }
            else{
                if($_REQUEST["tipo_valor"] == "text" && ($_REQUEST["tipo_form"] == "radio" || $_REQUEST["tipo_form"] == "checkbox" || $_REQUEST["tipo_form"] == "selectbox")){

                    $error_tipos = '<p>"Para o tipo de valor text apenas pode ser selecionado os tipos de campo text ou textbox!!!"</p>';
                    $erros++;
                }
                elseif($_REQUEST["tipo_valor"] == "bool" && ($_REQUEST["tipo_form"] == "text" || $_REQUEST["tipo_form"] == "checkbox" || $_REQUEST["tipo_form"] == "selectbox" || $_REQUEST["tipo_form"] == "textbox")){

                    $error_tipos = '<p>"Para o tipo de valor bool apenas pode ser selecionado o tipo de campo radio!!"</p>';
                    $erros++;
                }
                elseif(($_REQUEST["tipo_valor"]== "int" || $_REQUEST["tipo_valor"] == "double")  && ($_REQUEST["tipo_form"]== "radio" || $_REQUEST["tipo_form"] == "checkbox" || $_REQUEST["tipo_form"] == "selectbox" || $_REQUEST["tipo_form"] == "textbox")){

                    $error_tipos = '<p>"Para o tipo de valor int/double apenas pode ser selecionado o tipo de campo text!!"</p>';
                    $erros++;

                }
                elseif($_REQUEST["tipo_valor"] == "enum" && ($_REQUEST["tipo_form"] == "text" || $_REQUEST["tipo_form"]== "textbox")){

                    $error_tipos = '<p>"Para o tipo de valor enum apenas podem ser selecionados os tipos de campo radio/checkbox/selectbox!!"</p>';
                    $erros++;

                }

            }
            if (empty($_REQUEST["nomeordem"])) {

                $nome_ordem_error = '<p>"Ordem do campo no formulário é obrigatório!!"</p>';
                $erros++;

            }else {
                if (!preg_match("/^[0-9]*$/", $_REQUEST["nomeordem"])){
                    $nome_order_error = '<p>"Nome no formato errado, apenas carateres numéricos!!!!"</p>';
                    $erros++;

                }
            }
            if ($_REQUEST["tipo_estado"] == ""){

                $tipo_estado_error = '<p>"O Requesito obrigatório é obrigatório!!!"</p>';
                $erros++;


            }


        }

        if ($erros > 0) { // para aparecer formulario
            $querysubitens = "SELECT * from subitem WHERE id ='" . $_REQUEST["id"] . "'";
            $resultado_subitem = mysqli_query($mysqli, $querysubitens);
            $subitem = mysqli_fetch_assoc($resultado_subitem);

            echo "<h3 class='h3div'>Edição de Dados de Subitem - '" . $subitem["name"] . "'</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<form method="post" action="">
            <div>
		    <br>Nome: <span class="error">*</span> <input type="text" name="nome" value="' . $subitem["name"] . '"><br>';
            echo '<span class="error">';
            echo $name_error;
            echo '</span>';
            echo '</div>';
            echo '<div>';
            echo 'Tipo de Valor: <span class="error"> <br>';

            $valorestipo = get_enum_values($mysqli, "subitem", "value_type");
            if (!empty($valorestipo)) {
                $tamanho = count($valorestipo);
                $indice = 1;
                while ($indice <= $tamanho) { // para aparecer o valor pre selecionado
                    if ($subitem["value_type"] == $valorestipo[$indice]) {
                        echo '.<input type="radio" name="tipo_valor" value=' . $valorestipo[$indice] . ' checked>' . $valorestipo[$indice] . '<br>';
                        $indice++;
                    }
                    else{
                        echo '.<input type="radio" name="tipo_valor" value=' . $valorestipo[$indice] . '>' . $valorestipo[$indice] . '<br>';
                        $indice++;
                    }
                }
            }
            echo '<span class="error">';
            echo $tipo_valor_error;
            echo '</span>';
            echo '</div>';
            echo '<div>';
            echo 'Tipo do campo do formulário: <span class="error"> <br>';

            $valoresform = get_enum_values($mysqli, "subitem", "form_field_type");
            if (!empty($valoresform)) {
                $tamanho2 = count($valoresform);
                $indice1 = 1;
                while ($indice1 <= $tamanho2) { // para aparecer o valor pre selecionado
                    if($subitem["form_field_type"] == $valoresform[$indice1]) {
                        echo '.<input type="radio" name="tipo_form" value=' . $valoresform[$indice1] . ' checked>' . $valoresform[$indice1] . '<br>';
                        $indice1++;
                    }
                    else{
                        echo '.<input type="radio" name="tipo_form" value=' . $valoresform[$indice1] . '>' . $valoresform[$indice1] . '<br>';
                        $indice1++;
                    }
                }
            }
            echo '<span class="error">';
            echo $tipo_form_error;
            echo '</span>';
            echo '</div>';
            echo '<div>';
            echo 'Tipo de Unidade: <br>';

            $nomesunidades = sprintf("SELECT name FROM subitem_unit_type"); //query para ir buscar as unidades
            $tipounidades = mysqli_query($mysqli, $nomesunidades);

            echo '<select name="tipo_unidade" >';
            echo '<option value="NULL"></option>';
            if (mysqli_num_rows($tipounidades) > 0) {
                while ($row5 = $tipounidades->fetch_array()) {
                    echo '<option value=' . $row5["name"] . '>' . $row5["name"] . '</option>';
                }
            }
            echo '<br></select>';
            echo '</div>';
            echo '<div>';
            echo 'Ordem do campo no formulário: <span class="error">*</span> <input type="text" name="nomeordem" value="' . $subitem["form_field_order"] . '">';
            echo '<span class="error">';
            echo $nome_ordem_error;
            echo '</span>';
            echo '</div>';
            echo '<div>';
            if ($subitem["mandatory"] == '1') { // para aparecer o pre selecionado
                echo 'Obrigatório:<span class="error">*</span><br>.<input type="radio" name= "tipo_estado" value="1" checked>Sim <br>.<input type="radio" name= "tipo_estado" value="0">Não';
            }
            else{
                echo 'Obrigatório:<span class="error">*</span><br>.<input type="radio" name= "tipo_estado" value="1">Sim <br>.<input type="radio" name= "tipo_estado" value="0" checked>Não';
            }
            echo '<span class="error">';
            echo $tipo_estado_error;
            echo '</span>';
            echo '</div>';
            echo '<br>';
            echo '<div>
            <input type ="submit" value = "Alterar subitem" name = "Submit">';
            echo '<span class="error">';
            echo $error_tipos;
            echo '</span>';
            echo '</div></form>';
            echo '<br>';
            echo voltar();
        }
        elseif($erros == 0){

            if($_REQUEST["tipo_unidade"] == "NULL"){ // se nao tiver sido selecionado unidade
                // Dados introduzidos pelo utilizador
                $idsubitem = $_REQUEST["id"];
                $novonome = $_REQUEST["nome"];
                $tipovalornovo = $_REQUEST["tipo_valor"];
                $tipoordemnovo = $_REQUEST["tipo_form"];
                $nomeordemnovo = $_REQUEST["nomeordem"];
                $tipoestadonovo = $_REQUEST["tipo_estado"];


                //----------------------------------

                //-------------------- PARA MUDAR O FORM FIELD NAME-----------------------

                $queryitem = "SELECT item.name FROM item,subitem WHERE subitem.item_id = item.id AND subitem.id ='".$idsubitem."'";
                $resultadoitem = mysqli_query($mysqli,$queryitem);
                $nome_item = mysqli_fetch_assoc($resultadoitem);
                $trescarateres = substr($nome_item["name"], 0, 3);
                $stringfinal = ("$trescarateres-".$idsubitem."-".$novonome."");

                //------------------------------------------------------------------------

                $query_update = "UPDATE subitem SET name='".$novonome."',value_type = '".$tipovalornovo."',form_field_name ='".$stringfinal."',form_field_type ='".$tipoordemnovo."',unit_type_id =NULL,form_field_order ='".$nomeordemnovo."',mandatory ='".$tipoestadonovo."'WHERE id ='".$idsubitem."'";
                mysqli_query($mysqli,$query_update);


                echo "Alteração feita com sucesso!!";
                echo "Clique em ";
                echo '<a href="gestao-de-subitens">continuar</a>';
                echo " para voltar à página dos subitens";


            }
            else{

                $idsubitem = $_REQUEST["id"];
                $novonome = $_REQUEST["nome"];
                $tipovalornovo = $_REQUEST["tipo_valor"];
                $tipoordemnovo = $_REQUEST["tipo_form"];
                $nomeordemnovo = $_REQUEST["nomeordem"];
                $tipoestadonovo = $_REQUEST["tipo_estado"];
                $unidade = $_REQUEST["tipo_unidade"];

                //------------------------PARA OBTER O ID DA UNIDADE NOVA----------------------------
                $idunidade = sprintf("SELECT id FROM subitem_unit_type WHERE name = '" . $unidade . "'");
                $resultado_unidade = mysqli_query($mysqli, $idunidade);
                $unidade = mysqli_fetch_assoc($resultado_unidade);
                $unidadenova = $unidade["id"];
                //-----------------------------------------------------------------------------------

                //-------------------- PARA MUDAR O FORM FIELD NAME-----------------------

                $queryitem = "SELECT item.name FROM item,subitem WHERE subitem.item_id = item.id AND subitem.id ='".$idsubitem."'";
                $resultadoitem = mysqli_query($mysqli,$queryitem);
                $nome_item = mysqli_fetch_assoc($resultadoitem);
                $trescarateres = substr($nome_item["name"], 0, 3);
                $stringfinal = ("$trescarateres-".$idsubitem."-".$novonome."");

                //------------------------------------------------------------------------

                $query_update = "UPDATE subitem SET name='".$novonome."',value_type = '".$tipovalornovo."',form_field_name ='".$stringfinal."',form_field_type ='".$tipoordemnovo."',unit_type_id ='".$unidadenova."',form_field_order ='".$nomeordemnovo."',mandatory ='".$tipoestadonovo."'WHERE id ='".$idsubitem."'";
                mysqli_query($mysqli,$query_update);

                echo "Alteração feita com sucesso!!";
                echo "Clique em ";
                echo '<a href="gestao-de-subitens">continuar</a>';
                echo " para voltar à página dos subitens";

            }

        }

    }
    elseif($_REQUEST["tipo"] == "valorespermitidos"){

        $erros = 1; // de modo a que apareça o formulario no inicio
        if(isset($_REQUEST["Submit"])){ // validação
            $erros = 0;
            if (empty($_REQUEST["valor"])) {

                $name_error = '<p>"Valor é obrigatório!!"</p>';
                $erros++;

            }
        }


        if ($erros > 0) {
            $queryvalores = "SELECT value from subitem_allowed_value WHERE id ='" . $_REQUEST["id"] . "'";
            $resultado_valorpermitido = mysqli_query($mysqli, $queryvalores);
            $valorpermitido = mysqli_fetch_assoc($resultado_valorpermitido);


            echo "<h3 class='h3div'>Edição de Dados de Item - '" . $valorpermitido["value"] . "'</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<div>';
            echo '<form method="post" action="">
		<br>Nome: <span class="error">*</span> <input type="text" name="valor" value="' . $valorpermitido["value"] . '"><br>';
            echo '<span class="error">';
            echo $name_error;
            echo '</span>';
            echo '</div>';
            echo '<input type ="submit" name = "Submit" value = "Alterar item"></form>';

        }
        elseif($erros == 0){

            $queryupdate = "UPDATE subitem_allowed_value SET value='".$_REQUEST["valor"]."'WHERE id ='".$_REQUEST["id"]."'"; //update a bd
            mysqli_query($mysqli,$queryupdate);

            echo "Alteração feita com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-valores-permitidos">continuar</a>';
            echo " para voltar à página dos valores permitidos";

        }
    }

}
elseif($_GET["estado"] == "ativar") {
    if ($_REQUEST["tipo"] == "item") {
        $iditem = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE item SET state ='active' WHERE id ='" . $iditem . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo ativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Ativar Elemento">
            </form>';
        }
        else{
            echo "Elemento ativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-itens">continuar</a>';
            echo " para voltar à página dos itens";
        }
    }
    elseif($_REQUEST["tipo"] == "subitem"){
        $idsubitem = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE subitem SET state ='active' WHERE id ='" . $idsubitem . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo ativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Ativar Elemento">
            </form>';
        }
        else{
            echo "Elemento ativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-subitens">continuar</a>';
            echo " para voltar à página dos itens";
        }

    }
    elseif($_REQUEST["tipo"] == "valorespermitidos"){
        $idvalorpermitido = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE subitem_allowed_value SET state ='active' WHERE id ='" . $idvalorpermitido . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo ativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Ativar Elemento">
            </form>';
        }
        else{
            echo "Elemento ativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-valores-permitidos">continuar</a>';
            echo " para voltar à página dos valores permitidos";
        }

    }
}
elseif($_GET["estado"] == "desativar") {
    if ($_REQUEST["tipo"] == "item") {
        $iditem = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE item SET state ='inactive' WHERE id ='" . $iditem . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo desativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Desativar Elemento">
            </form>';
        }
        else{
            echo "Elemento desativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-itens">continuar</a>';
            echo " para voltar à página dos itens";
        }
    }
    elseif($_REQUEST["tipo"] == "subitem"){
        $idsubitem = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE subitem SET state ='inactive' WHERE id ='" . $idsubitem . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo desativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Desativar Elemento">
            </form>';
        }
        else{
            echo "Elemento desativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-subitens">continuar</a>';
            echo " para voltar à página dos itens";
        }

    }
    elseif($_REQUEST["tipo"] == "valorespermitidos"){
        $idvalorpermitido = $_REQUEST["id"];
        $update = 0;
        if (isset($_REQUEST["Submit"])) {

            $queryupdate = "UPDATE subitem_allowed_value SET state ='inactive' WHERE id ='" . $idvalorpermitido . "'";
            $resultadoupdate = mysqli_query($mysqli, $queryupdate);
            $update = 1;

        }
        if ($update == 0) {

            echo "Deseja mesmo desativar o elemento??";
            echo '<br>';
            echo '<form method="post" action ="">
            <input type ="submit" name = "Submit" value = "Desativar Elemento">
            </form>';
        }
        else{
            echo "Elemento desativado com sucesso!!";
            echo "Clique em ";
            echo '<a href="gestao-de-valores-permitidos">continuar</a>';
            echo " para voltar à página dos valores permitidos";
        }

    }

}

?>