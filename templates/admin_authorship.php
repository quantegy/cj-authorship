<div style="">
    <form id="authorForm" method="get">
        <div id="authorList">
            <?php include 'templates/admin_author_list.php'; ?>
        </div>
        <hr />
        <div style="">
            <label for="fullname">Name</label>
            <br />
            <input class="input" type="text" id="fullname" name="fullname" value="" />
        </div>
        <div>
            <label for="authorDesc">Description</label>
            <br />
            <textarea name="authorDesc" id="authorDesc" style="width:100%; height: 89px;"></textarea>
        </div>
        <div style="margin:10px 0 10px 0;">
            <button id="authorAdd" data-post_id="<?php echo $post->ID; ?>" class="button" type="button">Add Author</button>
        </div>
    </form>
</div>
<p>
    <span>Use Authorship as post/page display name?</span> 
    <input type="checkbox" name="useCJAuthor" id="useCJAuthor" class="" <?php echo ($isDisplayed === true) ? 'checked' : ''; ?> />
</p>