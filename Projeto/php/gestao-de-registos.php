<?php require_once("custom/php/common.php");
echo "<br>";

if ( is_user_logged_in() ) {
    if (current_user_can('manage_records') == false) {
        echo 'Não tem autorização a esta página';
    } else {




        if (empty($_POST["estado"])) {

            $person_name = $data_nascimento = $ee_name = $phone = $ee_email = $estado = "";

            echo "<h3 class='h3div'>Dados de registos - introdução</h3>";
            echo '<p><span class="error">* campo obrigatório</span></p>';
            echo '<form method="post" action="">  
			Nome completo: <span class="error">*</span> <input type="text" name="person_name"> 
			Data nascimento: <span class="error">*</span> <input type = "text"  name = "data_nascimento"> 
			Nome completo do encarregado de educação: <span class="error">*</span><input type="text" name="ee_name" > 
			Telefone do encarregado de educação: <span class="error">*</span> <input type="text" name="phone" placeholder="969696969"> 
			Endereço de e-mail do tutor: <input type="text" name="ee_mail" >
			<input type = "hidden" value = "validar" name ="estado">
			<input type ="submit" value = "Submeter">
		</form>';


        } elseif ($_POST["estado"] == "validar") {


            $erros = 0;
            $ee_email = test_input($_REQUEST["ee_mail"]);

            if (empty($_REQUEST["person_name"])) {
                echo "O nome é obrigatório!";
                echo "<br>";
                $erros = $erros + 1;


            } else {
                $person_name = test_input($_REQUEST["person_name"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["person_name"])) {
                    echo "Nome no formato errado, apenas carateres!!!!";
                    echo "<br>";
                    $erros = $erros + 1;
                }

            }
            if (empty($_REQUEST["data_nascimento"])) {
                echo "A data_nascimento é obrigatória!";
                echo "<br>";
                $erros = $erros + 1;

            }
            else{
                $data_nascimento = test_input($_REQUEST["data_nascimento"]);
                if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $_REQUEST["data_nascimento"])) {
                    echo "Data de nascimento no formato errado!!";
                    echo "<br>";
                    $erros = $erros + 1;
                }

            }
            if (empty($_REQUEST["ee_name"])) {
                echo "O nome do EE é obrigatório!";
                echo "<br>";
                $erros = $erros + 1;

            } else {
                $ee_name = test_input($_REQUEST["ee_name"]);
                if (!preg_match("/^[a-zA-Z ]*$/", $_REQUEST["ee_name"])) {
                    echo "Nome no formato errado, apenas carateres!!!!";
                    echo "<br>";
                    $erros = $erros + 1;
                }
            }

            if (empty($_REQUEST["phone"])) {
                echo "O telefone do E.E. é obrigatório!";
                echo "<br>";
                $erros = $erros + 1;

            } else {
                $phone = test_input($_REQUEST["phone"]);
                if (!preg_match('/^[0-9]{9}$/', $_REQUEST["phone"])) {
                    echo "O telefone terá de ter 9 dígitos ";
                    echo "<br>";
                    $erros = $erros + 1;
                }

            }
            if(empty($_REQUEST["ee_mail"])){
                $ee_email = test_input($_REQUEST["ee_mail"]);
            }
            else {
                if (!filter_var($_REQUEST["ee_mail"], FILTER_VALIDATE_EMAIL)) {
                    echo "E-mail no formato errado!!";
                    echo "<br>";

                    $erros = $erros + 1;
                }
            }



            if ($erros > 0) {
                echo voltar();
            } else {

                echo "<h3 class='h3div'>Dados de registos - validação</h3>";
                echo "Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos?";
                echo "<br>";
                echo "<br>";
                echo "Nome da criança: " . $person_name;
                echo "<br>";
                echo "Data de nascimento: " . $data_nascimento;
                echo "<br>";
                echo "Nome do E.E.: " . $ee_name;
                echo "<br>";
                echo "Telefone do E.E.: " . $phone;
                echo "<br>";
                echo "Email do E.E.:" . $ee_email;
                echo "<br>";
                echo '<form method="post" action=""> 
				<input type = "hidden" name = "person_name" value ="'.$person_name.'">
				<input type = "hidden" name = "ee_name" value ="' . $ee_name . '">
				<input type = "hidden" name = "phone" value ="' . $phone . '">
				<input type = "hidden" name = "ee_email" value ="' . $ee_email . '">
				<input type = "hidden" name = "data_nascimento" value ="' . $data_nascimento . '">
				<input type = "hidden" value = "inserir" name ="estado">
				<input type ="submit" value = "Submeter" >
				</form>';

            }



        } elseif ($_POST["estado"] == "inserir") {

            $person_name = $_REQUEST["person_name"];
            $ee_name = $_REQUEST["ee_name"];
            $phone = $_REQUEST["phone"];
            $ee_email = $_REQUEST["ee_email"];
            $data_nascimento = $_REQUEST["data_nascimento"];


            echo "<h3 class='h3div'>Dados de registo - inserção</h3>";

            $query = sprintf("INSERT INTO child (name,birth_date,tutor_name,tutor_phone,tutor_email) VALUES('" . $person_name . "','" . $data_nascimento . "','" . $ee_name . "','" . $phone . "','" . $ee_email . "')");


            if (mysqli_query($mysqli, $query)) {
                echo "Inseriu os dados de registo com sucesso.";
                echo "<br>";
                echo "Clique em ";
                echo '<a href="">continuar</a>';
                echo " para avançar";
            } else {
                echo "Ocorreu um erro: ";
                echo "<br>";
                echo "Clique em ";
                echo '<a href="">continuar</a>';
                echo " para avançar";
                echo mysqli_error($mysqli);
            }
            
        }


    }
}
else {
    echo 'Não tem autorização a esta pagina!!';
}

?>