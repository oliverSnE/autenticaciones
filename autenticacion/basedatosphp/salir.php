<?php 


session_start();
//quitamos la variable global
unset($_SESSION['INGRESO']);
if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
				$uri = 'https://';
			}else{
				$uri = 'http://';
			}
		   $uri .= $_SERVER['HTTP_HOST'];

    //Se redicciona una vez borrado la variable global
  	//Modificar como en la siguiente linea de codigo
  	//si es que esta en un subdirectorio
  	// header("location: ".$uri."/wp-admin"); 
    header("location: ".$uri);		   

 ?>


