<?php

// Transformando o arquivo XML em objeto
$xml = simplexml_load_file('category.xml');

//Exibindo as informações do XML
foreach($xml->children() as $category){
    echo "Nome: ". $category->name;
    echo "<br>Subcategoria 1: ". $category->subcategories->subcategory1;
    echo "<br>Subcategoria 2: ". $category->subcategories->subcategory2;
    echo "<br>Subcategoria 3: ". $category->subcategories->subcategory3;
    echo "<br>";
}
?>