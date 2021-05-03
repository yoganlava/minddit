<?php
    function format($comments,  $mysqli)
    {
        $threads = [];
        foreach ( $comments as $comment )
        {
            $replies = &$threads;
            $ids = explode( ',', $comment['path']);
            $commentid = array_pop($ids);
            if ($ids)
            {
                foreach ( $ids as $id ) 
                {
                    if(!isset($replies[$id]))
                    {
                        $replies[$id] = ['comment' => null, 'replies' => []];
                    }
                    $replies = &$replies[$id]['replies'];
                }
            }
            $replies[$commentid] = ['comment' => $comment, 'replies' => []];
        }
        $html = "";
        foreach ( $threads as $thread )
        {
            $html .= getComment($thread,  $mysqli, 0);
        }
        return $html;
       }

       function getComment($thread, $mysqli, $depth)
       {
           $out =  "<div class='comment' style='padding-left: ".$depth * 2 ."%'><div class='details'>";

           if(isset($_SESSION['userid'])){
            $checkLiked = "SELECT likes.vote FROM likes, comment_likes where comment_likes.comment_id = ".$thread['comment']['id']." and likes.user_id = ".$_SESSION['userid']." and comment_likes.like_id = likes.id";
            $checkLiked = ($mysqli->query($checkLiked))->fetch_assoc();
            }

            if(!$depth) $out.="<div class='dropdown' id='d".$thread['comment']['id']."' onclick='toggleReplies(".$thread['comment']['id'].");'></div>";
            $name = ($mysqli->query("SELECT username from users where id=".$thread['comment']['user_id']))->fetch_assoc()['username'];
           $out .="<p><a href='profile.php?name=".$name."' class='normal-link' style='position: relative;left: -15px'>".$name."</a> ".date("d/m/y",$thread['comment']['created_at'])." ".(($thread['comment']['created_at']!=$thread['comment']['updated_at'])?"(edited)":"")."</p>";
           
                       
           $awardCount = "select coalesce(count(distinct a.id),0) as gold, coalesce(count(distinct a2.id),0) as silver
           from comment_awards ca
           left join awards a on a.id = ca.award_id and a.award = 'gold'
           left join awards a2 on a2.id = ca.award_id and a2.award= 'silver'
           where ca.comment_id = ".$thread['comment']['id'];
           
           $awardCount = ($mysqli->query($awardCount))->fetch_assoc();
           if($awardCount['gold']) $out .= "<div class='gold-comment' tooltip='Gold'></div><p class='award-text'>x ".$awardCount['gold']."</p>";
           if($awardCount['silver']) $out .= "<div class='silver-comment' tooltip='Silver'></div><p class='award-text'>x ".$awardCount['silver']."</p>";
            
           $out .= ((isset($checkLiked))? $checkLiked['vote'] === 'upvote' : false)? "<span class='chevron on upvote' style='padding-left:5px' id='u".$thread['comment']['id']."' onclick='vote(".$thread['comment']['id'].", ".$_SESSION['userid'].", \"comment\", \"upvote\")'></span>" : "<span class='chevron upvote' style='padding-left:5px' id='u".$thread['comment']['id']."' onclick='vote(".$thread['comment']['id'].", ". ((isset($_SESSION['userid']))? $_SESSION['userid']:"null") .", \"comment\", \"upvote\")'></span>";
           $likes = "SELECT (SELECT COUNT(*) FROM likes, comment_likes where likes.vote = 'upvote' and comment_likes.comment_id = ".$thread['comment']['id']." and comment_likes.like_id = likes.id) - (SELECT COUNT(*) FROM likes, comment_likes where likes.vote = 'downvote' and comment_likes.comment_id = ".$thread['comment']['id']." and comment_likes.like_id = likes.id) as likes";
           $likes = ($mysqli->query($likes))->fetch_assoc();

           $out .=  "<p style='padding-left:10px' id='l".$thread['comment']['id']."'>".$likes['likes']."</p>";
           $out .=   ((isset($checkLiked))? $checkLiked['vote'] === 'downvote' : false)? "<span class='chevron on downvote' style='padding-left:5px' id='d".$thread['comment']['id']."' onclick='vote(".$thread['comment']['id'].", ".$_SESSION['userid'].", \"post\", \"downvote\")'></span>" : "<span class='chevron downvote' style='padding-left:5px' id='d".$thread['comment']['id']."' onclick='vote(".$thread['comment']['id'].", ". ((isset($_SESSION['userid']))? $_SESSION['userid']:"null") .", \"comment\", \"downvote\")'></span>";

           $out .="</div><div class='comment-body'><p class='comment-body' id='body".$thread['comment']['id']."'>".$thread['comment']['body']."<p></div>";
           if(isset($_SESSION['userid'])) $out .= "<a class='comment-link' onClick='reply(".$thread['comment']['id'].",".$thread['comment']['post_id']."," .((isset($_SESSION['userid']))?$_SESSION['userid']:"null"). ")' id='reply".$thread['comment']['id']."'>Reply</a>";
           if(isset($_SESSION['userid'])) $out .= "<a class='comment-link' onclick='toggleCommentAwardMenu(".$thread['comment']['id'].",".((isset($_SESSION['userid']))?$_SESSION['userid']:"null").")' style='padding-left: 5px;'>Award</a>";
                 if((isset($_SESSION['userid']))?$_SESSION['userid'] == $thread['comment']['user_id']:false) $out .= "<a class='comment-link' onclick='editComment(".$thread['comment']['id'].",". ((isset($_SESSION['userid']))?$_SESSION['userid']:"null") .")' style='padding-left: 5px;'>Edit</a>";
           if ( $thread['replies'] )
           {
               foreach ( $thread['replies'] as $reply )
               {    
                   if(!$depth) {
                   $out .= "<p class='hidden-replies' id='i".$thread['comment']['id']."'>Replies hidden</p>
                   <div class='replies shown' id='r".$thread['comment']['id']."'>";
                   }
                   $out .= getComment($reply,$mysqli, $reply['comment']['depth']);
               }
           }
           
           $out .= "</div>\n";
           return $out;
       }

?>