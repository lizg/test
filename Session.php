<?php
session_start();
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author gurzaf
 */
class Session {
    
    public static function registrarToken($token){
        $_SESSION["token"] = $token;
        header("Location: ".$_SERVER["PHP_SELF"]);
    }

    public static function verificarToken(){
        if(isset ($_SESSION["token"]))
            return true;
        else
            return false;
    }

    public static function destruirToken(){
        session_unregister("token");
        session_destroy();
        header("Location: ".$_SERVER["PHP_SELF"]);
    }

    public static function mostrarLogin($url){
        return '<a href="'.$url.'">Acceder a Youtube</a><br />';
    }

    public static function mostrarLogOut(){
        return '<a href="?login=0">Cerrar Sesion</a><br />';
    }

    public static function getToken(){
        return $_SESSION["token"];
    }
}
?>
