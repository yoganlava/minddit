<!-- AUTO GENERATE POST -->
<?php
include 'logic/commentFormatter.php';
include 'db/dbconn.php';
session_start();
$mysqli = openConnection();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css"
        integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous" />
    <link rel="stylesheet" href="css/css.css" />
    <script src="/js/scripts.js"></script>
    <script>
    window.onload = function() {
        window.onclick = function(event) {
            if (event.target == document.getElementById("awardModal")) {
                document.getElementById("awardModal").style.display = "none";
            }
        }
    }
    </script>
    <title>Name TBA</title>
</head>

<body>
    <div class="home-menu pure-menu pure-menu-horizontal">
        <a href="index.php" class="pure-menu-heading pure-menu-link">Minddit</a>
        <ul class="pure-menu-list">
            <li class="pure-menu-item">
                <a href="submit.php" class="pure-menu-link">Submit a new Post</a>
            </li>
            <li class="pure-menu-item">
                <a href="createsubreddit.php" class="pure-menu-link">Create a new subreddit</a>
            </li>
        </ul>
        <ul class="pure-menu-list align-right">
            <?php if(!isset($_SESSION['username'])){echo "<li class='pure-menu-item'>
        <a href='register.php' class='pure-menu-link'>Sign In</a>
      </li>";
      } else { echo "<li class='pure-menu-item pure-menu-has-children pure-menu-allow-hover'>
        <a href='#' id='menuLink1' class='pure-menu-link'>".$_SESSION['username']."</a>
        <ul class='pure-menu-children'>
            <li class='pure-menu-item'><a href='profile.php?name=".$_SESSION['username']."' class='pure-menu-link'>Profile</a></li>
            <li class='pure-menu-item'><a href='details.php' class='pure-menu-link'>Details</a></li>
            <li class='pure-menu-item'><a href='/logic/logout.php' class='pure-menu-link'>Log Out</a></li>
        </ul>
    </li>";
      }?>
        </ul>
    </div>
    <div id="awardModal" class="modal" style="display: none;">
        <div class="modal-content" id="award-content">
        </div>
    </div>
    <div class="posts">
        <?php
            $sql = "SELECT * FROM posts WHERE id = ".$_GET['postID'];
            $result = $mysqli->query($sql);
            $post = $result->fetch_assoc();
            echo"  <div class='post-header'>
                    <div><a class='post-link'>".$post['title']."</a><p style='padding-left: 10px;display: inline-block;'>".date("d/m/y",$post['created_at'])."</p><p style='padding-left: 10px;display: inline-block;'>".(($post['created_at'] != $post['updated_at'])?"(edited)":"")."</div>
                    <div class='details'>";
            $user = ($mysqli->query("select * from users where id='".$post['user_id']."'"))->fetch_assoc();
            echo "<p class='post-details'>Posted by <a href='profile.php?name=".$user['username']."' class='normal-link'>".$user['username']."</a>";
            $awardCount = "select coalesce(count(distinct a.id),0) as gold, coalesce(count(distinct a2.id),0) as silver
                from post_awards pa
                left join awards a on a.id = pa.award_id and a.award = 'gold'
                left join awards a2 on a2.id = pa.award_id and a2.award= 'silver'
                where pa.post_id = ".$post['id'];
                $awardCount = ($mysqli->query($awardCount))->fetch_assoc();
                if($awardCount['gold']) echo "<div class='gold' tooltip='Gold'></div><p class='award-text'>x ".$awardCount['gold']."</p>";
                if($awardCount['silver']) echo "<div class='silver' tooltip='Silver'></div><p class='award-text'>x ".$awardCount['silver']."</p>";
            if((isset($_SESSION['userid'])?true:false)?$_SESSION['userid'] == $post['user_id'] && !$post['is_image']:false)echo"<a class='normal-link' onclick='editPost(".$post['id'].")' style='padding-left: 50px'>Edit</a>";
            echo "</div>";
            echo "<div class='post-content'>";
                    echo ($post['is_image'])?"<image class='preview-image' src='".$post['body']."'> </div>
                    </div>":"<p id='post".$post['id']."'>".$post['body']."</p>
                    </div>
                </div>";
                ?>

        <div class="comments">
            <?php
          if(!isset($_SESSION['username'])){
            echo "<p>Please login to comment</p>";
          }else{
            echo "<form class='pure-form comment-system' action='javascript:comment(null,".$_GET['postID'].",".$_SESSION['userid'].");'>
            <textarea id='comment-form' class='pure-input-1-2 submit-comment'></textarea>
                <div class='bottom-bar'>
                    <div>
                        <image onClick='bold();' class='comment-commands' src='res/bold.png'>
                        <image onClick='italicise();' class='comment-commands' src='res/italic.png'>
                        <image onClick='link();' class='comment-commands' src='res/link.png'>
                    </div>
                    <button type='submit' class='pure-button pure-button-primary comment-btn'>Submit</button>
                </div>
            </form>";
          }
            ?>
            
            <?php
          $sql = "WITH recursive temp (id, parent_id, user_id, post_id, body, created_at, updated_at, path, depth) as (
            select id, parent_id, user_id, post_id, body, created_at, updated_at, cast(id as char(20)) as path, 1 as depth
            from
                comments
            where
                post_id = ".$_GET['postID']." and parent_id is null
            union all
            select
                c.id,
                c.parent_id,
                c.user_id,
                c.post_id,
                c.body,
                c.created_at,
                c.updated_at,
                concat(temp.path,',', c.id) as path,
                temp.depth + 1 as depth
            from
                comments c
                join temp on c.parent_id = temp.id
        )
        SELECT
            *
        from
            temp
        order by
            path;";
          $result = $mysqli->query($sql);

          
          $comments = array();
          if(mysqli_num_rows($result) == 0){
            echo "<p>No comments found :(</p>";
          }else{
            while($comment = $result->fetch_assoc()){
              $comments[] = $comment;
            }
            echo format($comments, $mysqli);
          }

          closeConnection($mysqli);
            ?>


        </div>

    </div>

</body>

</html>