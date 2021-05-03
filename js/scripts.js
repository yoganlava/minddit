function togglePost(postID) {
    if (document.getElementById("b" + postID).className === 'expand') {
        document.getElementById("b" + postID).className = 'shrink'
        document.getElementById("c" + postID).style.display = 'block'
    } else {
        document.getElementById("b" + postID).className = 'expand'
        document.getElementById("c" + postID).style.display = 'none'
    }
}

function toggleReplies(id) {
    if (document.getElementById("r" + id).className.includes("shown")) {
        document.getElementById("r" + id).className = 'replies hidden'
        document.getElementById("d" + id).className = 'dropup'
        document.getElementById("i" + id).style.display = 'block'
    } else {
        document.getElementById("r" + id).className = 'replies shown'
        document.getElementById("d" + id).className = 'dropdown'
        document.getElementById("i" + id).style.display = 'none'
    }
}

function reply(commentID, postID, userid) {
    var replyBox = `<form class='pure-form comment-system' action='javascript:comment(` + commentID + "," + postID + "," + userid + `);'>
    <textarea id='comment-form-` + commentID + `' class='pure-input-1-2 submit-comment'></textarea>
        <div class='bottom-bar'>
            <div>
                <image onClick='bold();' class='comment-commands' src='res/bold.png'>
                <image onClick='italicise();' class='comment-commands' src='res/italic.png'>
                <image onClick='link();' class='comment-commands' src='res/link.png'>
            </div>
            <button type='submit' class='pure-button pure-button-primary comment-btn'>Submit</button>
        </div>
    </form>`;
    document.getElementById("reply" + commentID).parentElement.appendChild(document.createElement("div")).innerHTML = replyBox;
    document.getElementById("reply" + commentID).parentElement.insertBefore(document.getElementById("reply" + commentID).parentElement.childNodes[document.getElementById("reply" + commentID).parentElement.childNodes.length - 1], document.getElementById("reply" + commentID).parentElement.childNodes[7]);
}

function toggleImage(checked) {
    var textHTML = `<textarea name="content" class="pure-input-1-2 submit"></textarea>`;
    var imageHTML = `<div><image class='preview-image' id="preview"></div>
    <input name="imageLink" type="text" class="pure-input-1-2 submit title" placeholder="Image Link" required>`;

    document.getElementById("swappable").innerHTML = (checked) ? imageHTML : textHTML;
    if (checked) {
        text = document.querySelector("input[name=imageLink]");
        text.addEventListener('change', function () {
            previewImage(this.value);
        });
    }
}

function previewImage(link) {
    document.getElementById("preview").setAttribute("src", link);
}

function toggleCommentAwardMenu(awardID, userID) {
    document.getElementById("awardModal").style.display = (document.getElementById("awardModal").style.display == "none") ? "block" : "none";
    content = `<span class='close' onclick='toggleCommentAwardMenu(0)'>&times;</span>
               <p>What would you like to award this comment with?<p>
               <p><a class="normal-link" onClick="giveAward(` + userID + `,` + "'silver'" + `,` + awardID + `,` + "'comment'" + `)">Silver</a> or <a class="normal-link" onClick="giveAward(` + userID + `,` + "'gold'" + `,` + awardID + `,` + "'comment'" + `)">Gold</a></p>`;
    document.getElementById("award-content").innerHTML = content;
}

function togglePostAwardMenu(awardID, userID) {
    document.getElementById("awardModal").style.display = (document.getElementById("awardModal").style.display == "none") ? "block" : "none";
    content = `<span class='close' onclick='togglePostAwardMenu(0)'>&times;</span>
               <p>What would you like to award this post with?<p>
               <p><a class="normal-link" onClick="giveAward(` + userID + `,` + "'silver'" + `,` + awardID + `,` + "'post'" + `)">Silver</a> or <a class="normal-link" onClick="giveAward(` + userID + `,` + "'gold'" + `,` + awardID + `,` + "'post'" + `)">Gold</a></p>`;
    document.getElementById("award-content").innerHTML = content;
}


function giveCommentAward(awardType, commentID, userID) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState = 4 && this.status == 200) {
            displayAwardMessage(this.responseText);
        }
    }
    xhttp.open("POST", "/logic/giveaward.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("awardType=" + awardType + "&commentID=" + commentID + "&userID=" + userID);
}

function giveAward(userID, awardType, contentID, contentType) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState = 4 && this.status == 200) {
            displayAwardMessage(this.responseText);
        }
    }
    xhttp.open("POST", "/logic/giveaward.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("awardType=" + awardType + "&userid=" + userID + "&contentID=" + contentID + "&contentType=" + contentType);
}

function displayAwardMessage(responseText) {
    console.log(responseText);
    content = `<span class='close' onclick='toggleCommentAwardMenu(0)'>&times;</span>
    <p>` + responseText + `</p>`
    document.getElementById("award-content").innerHTML = content;
}

function givePostAward(awardType, postID, userID) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState = 4 && this.status == 200) {
            displayAwardMessage(this.responseText);
        }
    }
    xhttp.open("POST", "/logic/giveaward.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("awardType=" + awardType + "&postID=" + postID + "&userID=" + userID);
}

function comment(parentID, postID, userid) {
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/logic/comment.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("postID=" + postID + "&body=" + document.getElementById((parentID == null) ? "comment-form" : "comment-form-" + parentID).value + "&userid=" + userid + "&parentID=" + parentID);
    location.reload();
}

function subscribe(subredditName, userID, buttonID) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState = 4 && this.status == 200) {
            disableButton(this.responseText, buttonID);
        }
    }
    xhttp.open("POST", "/logic/subscribe.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("name=" + subredditName + "&userid=" + userID);

}

function disableButton(message, buttonID) {
    document.getElementById(buttonID).classList.toggle("pure-button-disabled");
    document.getElementById(buttonID).textContent = message;
}

function back() {
    var args = getArgs();
    window.location.href = window.location.href + ((args.length > 0) ? "&page=" + (args.hasOwnProperty("page") ? --args[page] : "0") : "?page=0");
}

function forward() {
    var args = getArgs();
    window.location.href = window.location.href + ((args.length > 0) ? "&page=" + (args.hasOwnProperty("page") ? ++args[page] : "2") : "?page=2");
}

function vote(contentID, userID, contentType, likeType) {
    hasVoted =
        document.getElementById(
            (likeType === 'upvote') ? "u" + contentID : "d" + contentID
        ).classList.contains("on") ? true : false;

    if (userID == null) {
        window.location.href = "login.php";
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState = 4 && this.status == 200) {
            if (this.responseText) toggleChevron(likeType, contentID, hasVoted);
        }
    }
    xhttp.open("POST", "/logic/like.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("contentID=" + contentID + "&userid=" + userID + "&contentType=" + contentType + "&likeType=" + likeType);
}

function getArgs() {
    var request = {};
    var vars = location.search.substring(1).split('&');
    for (var i = 0; i < vars.length; i++) {
        var v = vars[i].split('=');
        request[v[0]] = v[1];
    }
    return request;
}

function toggleChevron(voteType, postID, hasVoted) {
    if (!hasVoted) {
        if (document.getElementById("u" + postID).classList.contains("on")) {
            document.getElementById("l" + postID).innerText++;
            document.getElementById("u" + postID).classList.toggle("on");
        }
        if (document.getElementById("d" + postID).classList.contains("on")) {
            document.getElementById("l" + postID).innerText--;
            document.getElementById("d" + postID).classList.toggle("on");
        }
        document.getElementById(voteType.charAt(0) + postID).classList.toggle("on");
    }
}

function sortBy(option) {
    window.location.href = "http://" + window.location.hostname + window.location.pathname + (getArgs().hasOwnProperty("name") ? "?name=" + getArgs()["name"] + "&sortBy=" + option : "?sortBy=" + option);
}


function editComment(id) {
    var comment = document.getElementById("body" + id).innerText;
    var ta = document.createElement("textarea");
    ta.setAttribute("class", "edit-box");
    ta.setAttribute("id", "tac" + id);
    document.getElementById("body" + id).parentNode.appendChild(ta);
    var but = document.createElement("button");
    but.setAttribute("class", "pure-button pure-button-primary");
    but.setAttribute("style", "margin-bottom: 30px;margin-left: 5px;")
    but.setAttribute("onclick", "updateComment(" + id + ")");
    but.innerText = "update";
    document.getElementById("body" + id).parentNode.appendChild(but);
    document.getElementById("body" + id).parentNode.childNodes[2].value = comment;
    document.getElementById("body" + id).parentNode.removeChild(document.getElementById("body" + id));

}

function validatePost() {
    var title = document.getElementByName("title");
    if (title.value === "") {
        alert("Title can't be empty");
        return false;
    }
}

function updateComment(id) {
    var ta = document.getElementById("tac" + id);
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/logic/updatecontent.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + id + "&comment=" + ta.value + "&contentType=comment");
    var com = document.createElement("p");
    com.innerText = ta.value;
    ta.parentNode.removeChild(ta.parentNode.childNodes[2]);
    ta.parentNode.replaceChild(com, ta);

}

function editPost(id) {
    var post = document.getElementById("post" + id).innerText;
    var ta = document.createElement("textarea");
    ta.setAttribute("class", "edit-box")
    ta.setAttribute("id", "tap" + id);
    document.getElementById("post" + id).parentNode.appendChild(ta);
    var but = document.createElement("button");
    but.setAttribute("class", "pure-button pure-button-primary");
    but.setAttribute("style", "margin-bottom: 30px;margin-left: 5px;")
    but.setAttribute("onclick", "updatePost(" + id + ")");
    but.innerText = "update";
    document.getElementById("post" + id).parentNode.appendChild(but);
    document.getElementById("post" + id).parentNode.childNodes[2].value = post;
    document.getElementById("post" + id).parentNode.removeChild(document.getElementById("post" + id));
}

function updatePost(id) {
    var ta = document.getElementById("tap" + id);
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/logic/updatecontent.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + id + "&comment=" + ta.value + "&contentType=post");
    var post = document.createElement("p");
    post.innerText = ta.value;
    ta.parentNode.removeChild(ta.parentNode.childNodes[2]);
    ta.parentNode.replaceChild(post, ta);
}

function autocomplete(inp, arr) {
    /*the autocomplete function takes two arguments,
    the text field element and an array of possible autocompleted values:*/
    var currentFocus;
    /*execute a function when someone writes in the text field:*/
    inp.addEventListener("input", function (e) {
        var a, b, i, val = this.value;
        /*close any already open lists of autocompleted values*/
        closeAllLists();
        if (!val) {
            return false;
        }
        currentFocus = -1;
        /*create a DIV element that will contain the items (values):*/
        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");
        /*append the DIV element as a child of the autocomplete container:*/
        this.parentNode.appendChild(a);
        /*for each item in the array...*/
        for (i = 0; i < arr.length; i++) {
            /*check if the item starts with the same letters as the text field value:*/
            if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
                /*create a DIV element for each matching element:*/
                b = document.createElement("DIV");
                /*make the matching letters bold:*/
                b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
                b.innerHTML += arr[i].substr(val.length);
                /*insert a input field that will hold the current array item's value:*/
                b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
                /*execute a function when someone clicks on the item value (DIV element):*/
                b.addEventListener("click", function (e) {
                    /*insert the value for the autocomplete text field:*/
                    inp.value = this.getElementsByTagName("input")[0].value;
                    /*close the list of autocompleted values,
                    (or any other open lists of autocompleted values:*/
                    closeAllLists();
                });
                a.appendChild(b);
            }
        }
    });
    /*execute a function presses a key on the keyboard:*/
    inp.addEventListener("keydown", function (e) {
        var x = document.getElementById(this.id + "autocomplete-list");
        if (x) x = x.getElementsByTagName("div");
        if (e.keyCode == 40) {
            /*If the arrow DOWN key is pressed,
            increase the currentFocus variable:*/
            currentFocus++;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 38) { //up
            /*If the arrow UP key is pressed,
            decrease the currentFocus variable:*/
            currentFocus--;
            /*and and make the current item more visible:*/
            addActive(x);
        } else if (e.keyCode == 13) {
            /*If the ENTER key is pressed, prevent the form from being submitted,*/
            e.preventDefault();
            if (currentFocus > -1) {
                /*and simulate a click on the "active" item:*/
                if (x) x[currentFocus].click();
            }
        }
    });

    function addActive(x) {
        /*a function to classify an item as "active":*/
        if (!x) return false;
        /*start by removing the "active" class on all items:*/
        removeActive(x);
        if (currentFocus >= x.length) currentFocus = 0;
        if (currentFocus < 0) currentFocus = (x.length - 1);
        /*add class "autocomplete-active":*/
        x[currentFocus].classList.add("autocomplete-active");
    }

    function removeActive(x) {
        /*a function to remove the "active" class from all autocomplete items:*/
        for (var i = 0; i < x.length; i++) {
            x[i].classList.remove("autocomplete-active");
        }
    }

    function closeAllLists(elmnt) {
        /*close all autocomplete lists in the document,
        except the one passed as an argument:*/
        var x = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < x.length; i++) {
            if (elmnt != x[i] && elmnt != inp) {
                x[i].parentNode.removeChild(x[i]);
            }
        }
    }
    /*execute a function when someone clicks in the document:*/
    document.addEventListener("click", function (e) {
        closeAllLists(e.target);
    });
}