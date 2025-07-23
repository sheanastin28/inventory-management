<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<div class="header">
    <span class="datetime" id="datetime"></span>
    <?php include 'logout.php';?>
    <div class="user-dropdown">
        <span class="user" id="userToggle">
        <i class="fas fa-user-circle"></i> <?php echo $_SESSION['fullname']; ?> <i class="fas fa-caret-down"></i>
        </span>
        <div class="dropdown-menu" id="userMenu">
        <a href="profile.php">Profile</a>
        <form action="post">
            <button type="submit" name="lgout" style="border-style: none; color:#333;">Logout</button>
        </form>
        <?php if(isset($_POST['lgout'])){session_start();$_SESSION = array(); session_destroy();header("Location: index.php");exit;} ?>
        </div>
    </div>
</div>
