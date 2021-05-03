<?php
include 'db/dbconn.php';
session_start();
if(!isset($_SESSION['userid'])){
    header("location: login.php");
}
$mysqli = openConnection();
$sql = "select name from subreddits";
$result = $mysqli->query($sql);
$subreddits= array();
$i = 0;
while($subreddit = mysqli_fetch_assoc($result)){
     $subreddits[$i] = $subreddit['name'];
     $i++;
}
closeConnection($mysqli);

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
        <?php 
            echo "var subreddits = ".json_encode($subreddits).";\n";
        ?>
        checkbox = document.querySelector("input[name=image]");
        checkbox.addEventListener('change', function() {
            toggleImage(this.checked);
        });

        subs = document.querySelector("input[name=subreddit]");
        autocomplete(subs, subreddits);
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

    <div class="form-container">
        <form class="pure-form pure-form-aligned" autocomplete="off" method="POST" action="/logic/submitpost.php" onsubmit="return validatePost()">
            <fieldset class="pure-group">
                <input name="title" type="text" class="pure-input-1-2 submit title" placeholder="A title" maxlength="255" required>
                <input type="checkbox" name="image" value="on"> Image <br>
                <div id="swappable">
                    <textarea name="content" class="pure-input-1-2 submit" required></textarea>
                </div>
                <div class="autocomplete">
                <input name="subreddit" type="text" class="pure-input-1-2 submit auto title" placeholder="Subreddit Name" required>
                </div>
                <button type="submit comment-btn" class="pure-button pure-button-primary">Submit</button>
            </fieldset>
        </form>
    </div>

</body>

</html>