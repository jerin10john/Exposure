<?php
session_start();
include_once 'include/config.php';
include_once 'include/gallery_user_functions.php';
include_once 'include/getuser.php';

//get user
$user = getSessionUser();


// Connect to MySQL
$db = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);

// Error checking
if (!$db) {
    print "<p>Error - Could not connect to MySQL</p>";
    exit;
}
$error = mysqli_connect_error();

if ($error != null) {
    $output = "<p>Unable to connet to database</p>" . $error;
    exit($output);
}

//get images from db
$query = genSQL();
$result = mysqli_query($db, $query);


?>
<!DOCTYPE html>
<html lan="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link href="css/style.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/57c4c79ee8.js" crossorigin="anonymous"></script>

</head>

<body>

    <!--
        main nav bar
    -->
    <nav class="main-nav">
        <div class="logo">
            <img src="images/logo.png">

        </div>
        <div class="hamburger">
            <div class="line"></div>
            <div class="line"></div>
            <div class="line"></div>
        </div>
        <div class="post-bt">
            <a href="post.php"><i class="fas fa-plus"></i>&nbsp Post new image</a>
        </div>
        <ul class="nav-links">
            <li><a href="gallery.php"><i class="fas fa-th"></i>&nbsp Gallery</a></li>
            <li><a href="user.php"><i class="fas fa-house-user"></i>&nbsp My dashboard</a></li>
            <li><a href="account.php"><i class="fas fa-user-cog"></i></i>&nbsp settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i>&nbsp Sign out</a></li>
            <li><a href="about.html"><i class="fas fa-question-circle"></i>&nbsp About</a></li>
        </ul>
    </nav>

    <!--
        Full screen image div
    -->
    <div class="gal-full-view">
        <button class="fullscreen-close-bt"><i class="far fa-times-circle"></i></button>
        <img src="">
    </div>

    <!--
        Gallery nav bar
    -->

    <nav class="gal-nav">

        <div id="gal-search">
            <input type="text" <?php addSearch() ?> name="gal-search" placeholder="Search title or start with # to search tag">
            <div class="button-holder">
                <button id='gal-clear-bt'><i class="fas fa-times"></i></button>
                <button id='gal-search-bt'><i class="fas fa-search"></i></button>
            </div>
        </div>
        <div id="gal-sort">
            Sort:
            <select class="gal-contr" name="sort">
                <?php addSortOp() ?>
            </select>
            In
            <select class="gal-contr" name="order">
                <?php addOrderOp() ?>
            </select>
        </div>

    </nav>

    <!--
        Gallery 
    -->
    <div class="grid">
               <!-- columns -->
  <div class="grid-col grid-col--1"></div>
  <div class="grid-col grid-col--2"></div>
  <div class="grid-col grid-col--3"></div>
  <div class="grid-col grid-col--4"></div>
        <?php
        $admin=isAdmin($db,$user);
        while ($row = mysqli_fetch_assoc($result)) {
            // output data of each row
            //$row = mysqli_fetch_assoc($result);
            $id = $row["Img_id"];
            $path = $row["Img_file_name"];
            $title=$row['title']

        ?>
        <div class="grid-item">
            <div class="img_box" <?php echo 'data-user= "' . $user . '" data-img= "' . $id . '"' ?>>
            <div class="img_box_header">
            <h1 ><?php echo $title ?></h1>
        
            </div>   
            <img src=<?php echo "'" . $path . "'" ?>>
                <div class="pic-edit-picker">
                    <span><img class="pic-edit-picked" src=<?php echo "'" . $path . "'" . "data-img= '" . $id . "'"." data-img-title= '".$title."'" ?>></span>

                    <?php
                    //returns list of edits of the image
                    $query = "SELECT * FROM img_edit, image WHERE img_edit.img_id=image.Img_id AND img_edit.edit_id=$id";
                    $edit_result = mysqli_query($db, $query);
                    while ($edit_row = mysqli_fetch_assoc($edit_result)) {
                        $edit_id = $edit_row["Img_id"];
                        $edit_title = $edit_row["title"];
                        $edit_path = $edit_row["Img_file_name"];
                        echo '<span><img src="' . $edit_path . '" data-img= "' . $edit_id . '" data-img-title= "' . $edit_title . '"></span>';
                    }
                    ?>

                </div>
                <div class="pic-control-bar">
                    <span><?php addLike($db, $user, $id) ?></span>
                    <span><button onclick="location.href='<?php echo 'editor.php?id='.$id.'&path='.urlencode($path)?>'" ><i class="fas fa-edit"></i></button></span>
                    <span><button class="fullscreen-bt"><i class="fas fa-expand"></i></button></span>
                    <span><button><i class="fas fa-info"></i></button></span>
                    <span><button class="edit-sl-arw-l"><i class="fas fa-arrow-left"></i></button></span>
                    <span><button class="edit-sl-bn"><i class="fas fa-images"></i></button></span>
                    <span><button class="edit-sl-arw-r"><i class="fas fa-arrow-right"></i></button></span>
                    <?php
                    if($admin){
                    ?>
                    <span><button onclick="location.href='<?php echo 'include/delete_img.php?id='.$id?>'" class="edit-sl-delete"><i class="fas fa-trash-alt"></i></button></span>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>
    <script src="js/nav.js"></script>
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/gallery.js"></script>
    <script src="https://unpkg.com/colcade@0/colcade.js"></script>
<script>
    $('.grid').colcade({
  columns: '.grid-col',
  items: '.grid-item'
})
</script>
</body>