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
    <form class="pure-form pure-form-aligned" method="POST" action="/logic/reg.php">
      <fieldset>
      <p style="font-size:32px"><b>Register</b></p>
        <div class="form-center">
          <label for="name">Username</label>
          <input name="username" type="text" placeholder="Username">
        </div>

        <div class="form-center">
          <label for="password">Password</label>
          <input type="password" name="password" placeholder="Password">
        </div>
        <button type="submit" class="pure-button pure-button-primary">Submit</button>
        <p>Already have an account? <a href="login.php" class="normal-link">Sign in</a></p>
        <?php if(isset($_SESSION['message'])) echo "<p>".$_SESSION['message']."</p>"; $_SESSION['message'] = null;?>
        
  </div>
  </fieldset>
  </form>
  </div>

</body>

</html>