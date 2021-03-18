<?php
$mysqli = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);


function get_enum_values($connection, $table, $field )
{
    $enum_fields = array();
    $enum_array = array();
    $query = " SHOW COLUMNS FROM `$table` LIKE '$field' ";
    $result = mysqli_query($connection, $query );
    $row = mysqli_fetch_row($result);
    $regex = "/'(.*?)'/";
    preg_match_all( $regex , $row[1], $enum_array );
    foreach($enum_array[1] as $chave => $valor){
        $enum_fields[$chave + 1] = $valor;
    }
    return $enum_fields;

    // pequena alteração feita aqui de modo a facilitar o acesso aos dados
}



function voltar() {
    echo "<script type='text/javascript'>document.write(\"<a href='javascript:history.back()' class='backLink' title='Voltar atr&aacute;s'>Voltar atr&aacute;s</a>\");</script>
<noscript>
<a href='".$_SERVER['HTTP_REFERER']."‘ class='backLink' title='Voltar atras'>Voltar atras</a>
</noscript>";
}


function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}



global $wp;
$current_page = add_query_arg( array(), $wp->request );

?>
