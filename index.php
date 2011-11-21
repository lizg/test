<?php
//aaaaaaaaaaaaaaaaaaa
require_once 'Session.php';
require_once 'AuthYoutube.php';
require_once 'YoutubeClient.php';

$ay = new AuthYoutube("videocms.abc");
if(isset ($_GET["login"]) && $_GET["login"] == 0){
    Session::destruirToken();
}else if(isset ($_GET['token'])){
    Session::registrarToken($ay->getSessionToken($_GET['token']));
}else if(Session::verificarToken()){
    echo Session::mostrarLogOut();
    $yt = new YoutubeClient($ay->getYoutubeHttpClient());
    if(isset($_GET["video"])){
        echo $yt->showPlayer ($_GET["video"]);
        
        if(isset($_GET["comentario"]))
        {
           $yt->addcoment($_GET['comentario'], $_GET['video']);
          
        }
        echo $yt->getAndPrintCommentFeed($_GET['video']);
    }
    else{
        echo $yt->showFormUser($_GET["user"]);
        echo $yt->getUserUploads($_GET["user"]);
    }
}else{
    echo Session::mostrarLogin($ay->getAuthURL());
}