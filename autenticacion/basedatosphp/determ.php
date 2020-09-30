<?php 

session_start();
//importando datos para
//conectarse
require_once 'PasswordHash.Class.php';
require_once 'config.php';
/**
* clase para hacer login
* a la seccionde administracion
*/
class Login{

	//campos que alamcenan los valores 
	private $Mail_       ="";
	private $Contrasena_ ="";
	private $Mensaje     ="";
	private $Nombre_usr  ="";
    /**
     * [constructor recibe argumentos]
     * @param [type] $Mail    [ingresar correo]
     * @param [type] $Pasword [Ingresar contraseña]
     */
	function __construct($Mail,$Pasword){
		$this->Mail_=$Mail;
		$this->Contrasena_=$Pasword;
	}

/**
 * [Metdo devuelve true o false para ingresar
 * a la sesccion de pagina de administracion
 * ]
 */
public function Ingresar(){
    //determinamos cada uno de los
    //metodos devueltos
	if($this->ValidarUser()==false){
	 $this->Mensaje=$this->Mensaje;	
	}else{
		if($this->Pasword_usr()==false){
         $this->Mensaje=$this->Mensaje;	
		}else{
     		//por lo es correcto el logeo realizamos la redireccion
			if (!empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS'])) {
				$uri = 'https://';
			}else{
				$uri = 'http://';
			}
		    $uri .= $_SERVER['HTTP_HOST'];

		    //Aqui modificar si el pag de aministracion esta 
		    //en un subdirectorio
		    // "<script type=\"text/javascript\">
			// window.location=\"".$uri."/wp-admin/admin.php\";
			// </script>";

			echo    "<script type=\"text/javascript\">
			           window.location=\"".$uri."/autenticacion/basedatosphp/admin.php\";
			          </script>";

		} 
	}
}

/**
 * Validamos la entrada de correo
 * electronico
 * @param [String mail]
 */
private function ValidarUser(){
	 $retornar=false;
	 $mailfilter =filter_var($this->Mail_,FILTER_VALIDATE_EMAIL);//filtramos el correo
	 //Validamos el formato  de correo electronico utilizando expresiones regulares
	 if(preg_match("/[a-zAZ0-9\_\-]+\@[a-zA-Z0-9]+\.[a-zA-Z0-9]/", $mailfilter )==true){
	 	//intanciando de las clases
	 	$confi=new Datos_conexion();
	 	$mysql=new mysqli($confi->host(),$confi->usuario(),$confi->pasword(),$confi->DB());
        //Determinamos si la conexion a la bd es correcto.
	 	if(!$mysql){
             $this->Mensaje='<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong> Servidor de datos no econtrado, vuelva a intentar mas tarde. </div>';
	 	}else{
	 		//consulta SQL para vereficar si existe tal correo del
	 		//usario que introdujo 
	 		  $query    = "SELECT
							tb_login.Correo
							FROM
							tb_login
							WHERE tb_login.Correo='".$mailfilter."';";
	 		 $respuesta = $mysql->query($query);
	 		    //Aqui determinamos con la instruccion if
	 		    //la consulta generada, si mayor a cero
	 		    //retornamos el valor verdadero
	 		    //por el contrario mesaje de error
	           if($respuesta->num_rows>0){
	           	 //asignamos el mail sanitizado  al campo Mail_
	           		 $this->Mail_=$mailfilter;
	           		 $retornar=true;// se retorna un valor verdadero
	           		
	           }else {
                 $this->Mensaje='<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong> EL correo no existe, usted no podra ingresar. </div>';
	           }
	 	}
	 }else{

	 	//Se muesta al usuario el mensaje de error sobre
	 	//el formato de correo
         $this->Mensaje='<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong> El correo que ingresaste no tiene formato correcto. </div>';
	 }
return $retornar;
}

/**
 * Metodo para determinar
 * la existencia de la contraseña y verificacion 
 * @param [type] $pasword [ingresar contraseña]
 */
private function Pasword_usr(){
	$retornar = false;
	//saneamos la entrada de los caracteres
	$contra   = filter_var($this->Contrasena_, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_ENCODE_AMP);
	if($contra==""){
	//si que no existen ningun
	//contraseña mostramos el mensaje de error
	$this->Mensaje='<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong> Escriba su contraseña. </div>';	
	}else{
		//Realizamos la consulta sql a la bd
		//y verificamos la contraseña
       $Contrasena = new PasswordHash(8, FALSE);
        $query="SELECT
				tb_login.Correo,
				tb_login.Contra,
				tb_login.Nombre,
                tb_login.id
				FROM
				tb_login
				WHERE tb_login.Correo='".$this->Mail_."'";
		//instancia de las clases
		$confi=new Datos_conexion();
	 	$mysql=new mysqli($confi->host(),$confi->usuario(),$confi->pasword(),$confi->DB());
	 	$respuesta = $mysql->query($query);//se ejecuta la consulta SQL
	 	//Determinamos con la instrccion if 
	 	//si es que la consulta nos devuelve un valro
	 	//mayor a cero
	 	if($respuesta->num_rows>0){
                   //se obtiene el arreglo de la base de datos
	           	   $row     			= $respuesta->fetch_row();
	           	   //Recuperacion el Hash de la BD
	           	   $Hashing 			= $row[1];
                   	
                      //Realizamos el comparacion del paswrod con la instrccion if
	               if($Contrasena->CheckPassword($contra, $Hashing)){
	               	   //Recuparamos el Id del usuario
	               	   $idsur              =$row[3];
	               	  //Recuperamos el nombre de usuario para imprimir
	               	  $this->Nombre_usr    = $row[2];
	               	  //Recuperando el IP del usuario atravez del metodo IPuser()  
	               	  $IpUsr               = $this->IPuser();
	               	  //Recuperando la hora en el que ingreso
	               	  $hora                = time();
	               	  //Recuperamos recuperando los dados para incriptar
	                  $Clave = $Contrasena->HashPassword($idsur.$IpUsr.$this->Nombre_usr.$hora); 
                      //Registrando a la varaible global datos en un arreglo para iniciar session
	                  $_SESSION['INGRESO'] = array(
	                  	"Id"    =>$idsur,
	                  	"Ip"    =>$IpUsr,
	                  	"Clave" =>$Clave,
	                  	"Nombre"=>$this->Nombre_usr,
	                  	"hora"  =>$hora); 

	                  //Asignamos el valor verdadero para retornarlo
	                  $retornar           = true;
	               }else {
	               	  $this->Mensaje ='<div class="alert alert-danger alert-dismissible fade in" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button> <strong> Error!</strong> Contraseña incorrecto escriba nuevamente. </div>';
	                  $retornar      =false; //El paswor ingresado no es correcto
	               }
	           		
	           }
	}
 return $retornar; //Retornaos el valor true o false
}
/**
 * Returna el IP de usuario
 * @return [string] [devuel la io del usuario]
 */
private function IPuser() {
	$returnar ="";
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
     $returnar=$_SERVER['HTTP_X_FORWARDED_FOR'];}
if (!empty($_SERVER['HTTP_CLIENT_IP'])){
     $returnar=$_SERVER['HTTP_CLIENT_IP'];}
if(!empty($_SERVER['REMOTE_ADDR'])){
	 $returnar=$_SERVER['REMOTE_ADDR'];}
return $returnar;
}
/**
 * Devuel el mesaje generado
 * para mostrar al suario
 */
public function MostrarMsg(){
	return $this->Mensaje;
}


}








 ?>




