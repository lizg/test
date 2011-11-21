<?php
require_once 'Zend/Loader.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of YoutubeClient
 *
 * @author gurzaf
 */
class YoutubeClient {
    
    private $_yt;
    private $_commentFeed;
    
    

    function __construct($httpClient) {
        $this->_yt = new Zend_Gdata_YouTube($httpClient);
    }

    public function printVideoEntry($videoEntry, $tabs = "")
    {
      // the videoEntry object contains many helper functions that access the underlying mediaGroup object
      $resulta = $tabs . "\tVideo: " . $videoEntry->getVideoTitle() . "\n";
      $resulta .= $tabs . "\tDescripcion: " . $videoEntry->getVideoDescription() . "\n";
      $resulta .= $tabs . "\tURL en Youtube: " . $videoEntry->getVideoWatchPageUrl() . "\n";
      $resulta .= $tabs . "\tDuracion: " . $videoEntry->getVideoDuration() . "\n";
      $resulta .= $tabs . "\tContador de Vistas: " .$videoEntry->getVideoViewCount() . "</p>\n";
      

      $videoThumbnails = $videoEntry->getVideoThumbnails();
      $videoThumbnail = $videoThumbnails[0];
      $resulta .= $tabs . "\t<a href=\"?video=".$videoEntry->getVideoId()."\">";
      $resulta .= $tabs . "\t\t<img src=\"" . $videoThumbnail["url"]."\"";
      $resulta .= " height=\"" . $videoThumbnail["height"]."\"";
      $resulta .= " width=\"" . $videoThumbnail["width"]."\" />";
      $resulta .= "</a>\n";

      return $resulta;
    }

    public function printVideoFeed($videoFeed, $displayTitle = null)
    {
      $count = 1;
      if ($displayTitle === null) {
        $displayTitle = $videoFeed->title->text;
      }
      $result = '<h2>' . $displayTitle . "</h2>\n";
      $result .= "<pre>\n";
      foreach ($videoFeed as $videoEntry) {
        $result .= '<h3>Entrada # ' . $count . "</h3>\n";
        $result .= $this->printVideoEntry($videoEntry);
        $result .= "\n";
        $count++;
      }
      $result .= "</pre>\n";
      return $result;
    }

    public function getUserUploads($userName)
    {
        if($userName==null) $userName = "default";
        return $this->printVideoFeed($this->_yt->getuserUploads($userName));
    }

    public function showPlayer($videoId){
        $result="";
        try {
            $entry = $this->_yt->getVideoEntry($videoId);
        } catch (Zend_Gdata_App_HttpException $httpException) {
            $result .= 'ERROR ' . $httpException->getMessage()
            . ' HTTP details<br /><textarea cols="100" rows="20">'
            . $httpException->getRawResponseBody()
            . '</textarea><br />';
            return $result;
        }

        $videoTitle = htmlspecialchars($entry->getVideoTitle());
        $videoUrl = htmlspecialchars($entry->getFlashPlayerUrl());

        $result .= "<b>$videoTitle</b><br />"
        . '<object width="425" height="350">
            <param name="movie" value="'.$videoUrl.'"></param>
            <param name="allowFullScreen" value="true"></param>
            <param name="allowscriptaccess" value="always">
            <param name="wmode" value="transparent" /></param>
            <embed src="'.$videoUrl.'"
            width="425" height="350" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" allowfullscreen="true"
            movie="'.$videoUrl.'" wmode="transparent"></embed></object>';
     
        $result .= '<br /><br />';
        $result.='<form name=\'comentarios\' method=\'get\'><textarea name="comentario" cols=50></textarea><br /><input type=\'hidden\' value='.$_GET['video'].' name="video"><input type=\'submit\' value=\'comentar\'></form>';
        return $result;
        }

        public function showFormUser($user){
            $html = "<form name=\"formulario\" method=\"get\">
                        <input name=\"user\" type=\"text\" value=\"$user\" /><input type=\"submit\" value=\"Ver Videos\" />
                    </form>";
            return $html;
        }

        public function addcoment($coment,$video_id)
        {
            
            $newComment = $this->_yt->newCommentEntry();
            $newComment->content =  $this->_yt->newContent()->setText($coment);             //$service->newContent()->setText($coment);
            $comment_post_url = 'http://gdata.youtube.com/feeds/videos/'. $video_id .'/comments';
            $updatedVideoEntry = $this->_yt->insertEntry($newComment, $comment_post_url);
            header("Location: ".$_SERVER["PHP_SELF"]."?video=".$video_id);
        }

        function getAndPrintCommentFeed($videoId)
        {
            $this->_commentFeed = $this->_yt->getVideoCommentFeed($videoId);
             return $this->printCommentFeed($this->_commentFeed);
        }

        function printCommentFeed($commentFeed, $displayTitle = null)
        {
          $count = 1;
          $html="";
          if ($displayTitle === null) {
            $displayTitle = $commentFeed->title->text;
          }

          $html.='<h2>' . $displayTitle . "</h2>\n";
          $html.="<pre>\n";

          foreach ($commentFeed as $commentEntry) {
            $html.= 'Entrada # ' . $count . "\n";
            $html.=$this->printCommentEntry($commentEntry);
            $html.= "\n";
            $count++;
          }
          $html.= "</pre>\n";
          return $html;
        }

        function printCommentEntry($commentEntry)
        {
           $html="";
           
          $html.= 'Comentario: ' . $commentEntry->title->text . "\n";
          $html.= "\tTexto: " . $commentEntry->content->text . "\n";
          $html.= "\tAutor: ". $commentEntry->author[0]->name->text. "\n";

          return $html;
        }

        

        


}
?>
