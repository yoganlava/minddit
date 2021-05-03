<?php 
include 'db/dbconn.php';
session_start();
if(!isset($_GET['name'])){
    header("location: index.php");
}
$mysqli = openConnection();
$user = $mysqli->query("SELECT id,username FROM users where username='".$_GET['name']."'");
if($user->num_rows){
    $user = $user->fetch_assoc();
    $sql = "SELECT * FROM posts where user_id = ".$user['id']." LIMIT 10";
    $result=$mysqli->query($sql);

    $sql = "SELECT * FROM details where user_id = ".$user['id'];
    $details = ($mysqli->query($sql))->fetch_assoc();
}
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
    <title>Minddit</title>
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
    
    <div class="side-bar"><?php
        if(isset($details)){
            echo "<div><image class='preview-profile-picture' src='".((isset($details['profile_picture'])?$details['profile_picture']:"res/default.png"))."'></div>";
            echo "<p>Gender: ".$details['gender']."</p><p>Country: ".$details['country']."</p><p>Age: ".$details['age']."</p>";
            if(isset($details['description'])){
                echo $details['description'];
            }else{
                echo "<p>Welcome to ".$user['username']."'s profile</p>";
                echo "</br>";
            }
        }else{
            echo "<p>User does not exist!</p>";
        }
            ?>
    </div>

    <div class="posts">
        <?php
        if(isset($result)){
          while($post = $result->fetch_assoc()){
            $user = ($mysqli->query("select * from users where id='".$post['user_id']."'"))->fetch_assoc();
            echo "<div class='post'>";
            echo "<div class='likes'>";
            if(isset($_SESSION['userid'])){
            $checkLiked = "SELECT likes.vote FROM likes, post_likes where post_likes.post_id = ".$post['id']." and likes.user_id = ".$_SESSION['userid']." and post_likes.like_id = likes.id";
            $checkLiked = ($mysqli->query($checkLiked))->fetch_assoc();
            }
            
            echo ((isset($checkLiked))? $checkLiked['vote'] === 'upvote' : false)? "<span class='chevron on upvote' id='u".$post['id']."' onclick='vote(".$post['id'].", ".$_SESSION['userid'].", \"post\", \"upvote\")'></span>" : "<span class='chevron upvote' id='u".$post['id']."' onclick='vote(".$post['id'].", ". ((isset($_SESSION['userid']))? $_SESSION['userid']:"null") .", \"post\", \"upvote\")'></span>";
            $likes = "SELECT (SELECT COUNT(*) FROM likes, post_likes where likes.vote = 'upvote' and post_likes.post_id = ".$post['id']." and post_likes.like_id = likes.id) - (SELECT COUNT(*) FROM likes, post_likes where likes.vote = 'downvote' and post_likes.post_id = ".$post['id']." and post_likes.like_id = likes.id) as likes";
            $likes = ($mysqli->query($likes))->fetch_assoc();

            echo "<p id='l".$post['id']."'>".$likes['likes']."</p>";
            echo  ((isset($checkLiked))? $checkLiked['vote'] === 'downvote' : false)? "<span class='chevron on downvote' id='d".$post['id']."' onclick='vote(".$post['id'].", ".$_SESSION['userid'].", \"post\", \"downvote\")'></span>" : "<span class='chevron downvote' id='d".$post['id']."' onclick='vote(".$post['id'].", ". ((isset($_SESSION['userid']))? $_SESSION['userid']:"null") .", \"post\", \"downvote\")'></span>";
            echo  "</div>";
            echo  "<div class='post-info'>";
            echo ($post['is_image'])?"<div><a class='post-link' href='".$post['body']."' >".$post['title']."</a></div>":"<div><a class='post-link' href='post.php?postID=".$post['id']."' >".$post['title']."</a></div>";
            echo "<div class='details'>
                  <div id='b".$post['id']."' onClick='togglePost(".$post['id'].");' class='expand'></div>";  
            echo "<p class='post-details'>Posted by <a href='#' class='normal-link'>".$user['username']."</a> in <a href='subreddit.php?name=".$post['subreddit_name']."' class='normal-link'>".$post['subreddit_name']."</a></p>";

            $awardCount = "select coalesce(count(distinct a.id),0) as gold, coalesce(count(distinct a2.id),0) as silver
            from post_awards pa
            left join awards a on a.id = pa.award_id and a.award = 'gold'
            left join awards a2 on a2.id = pa.award_id and a2.award= 'silver'
            where pa.post_id = ".$post['id'];
            
            $awardCount = ($mysqli->query($awardCount))->fetch_assoc();
            if($awardCount['gold']) echo "<div class='gold' tooltip='Gold'></div><p class='award-text'>x ".$awardCount['gold']."</p>";
            if($awardCount['silver']) echo "<div class='silver' tooltip='Silver'></div><p class='award-text'>x ".$awardCount['silver']."</p>";
            echo "</div>";
            echo "<div class='post-details'><a href='post.php?postID=".$post['id']."' class='normal-link'>Comments</a> <a class='normal-link' onClick='togglePostAwardMenu(".$post['id'].",".((isset($_SESSION['userid']))? $_SESSION['userid']:"null").")' style='padding-left:5px'>Award</a></div>";
            echo "<div id='c".$post['id']."' class='content'>";
            echo ($post['is_image'])?"<image class='preview-image' src='".$post['body']."'>":"<p>".$post['body']."</p>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
          }
        }
          closeConnection($mysqli);
      ?>
    </div>
</body>

</html>