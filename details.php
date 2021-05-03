<?php session_start()?>
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
        img = document.querySelector("input[name=profile_picture]");
        img.addEventListener('change', function() {
            previewImage(this.value);
        });
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
        <form class="pure-form pure-form-aligned" method="POST" action="/logic/changedetails.php">
            <fieldset class="pure-group">
                <div>
                    <p>Edit details:</p>
                </div>
                <div>
                    <image class='preview-profile-picture' id="preview">
                </div>
                <input name="profile_picture" type="text" class="pure-input-1-2 submit"
                    placeholder="Link to profile picture">
                <select name="gender" class="gender-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input name="country" type="text" class="pure-input-1-2 submit" placeholder="Country">
                <input name="age" type="text" class="pure-input-1-2 submit age" placeholder="18">
                <textarea name="description" class="pure-input-1-2 submit"></textarea>
                <div>
                    <button type="submit comment-btn" class="pure-button pure-button-primary">Submit</button>
                </div>
                <?php
                if(isset($_SESSION['message'])){
                    echo "<p>".$_SESSION['message']."</p>";
                    $_SESSION['message'] = null;
                }
                ?>
            </fieldset>
        </form>
    </div>

</body>

</html>