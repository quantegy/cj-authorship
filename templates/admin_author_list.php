<?php if(!empty($authors)): ?>
<?php foreach($authors as $num => $author): ?>
<div class="authorListItem" data-id="<?php echo $author->id; ?>">
    <div style="padding:10px; margin:0 0 10px 0; border:1px solid #dfdfdf; background-color: #fdfdfd;">
        <label style="vertical-align: top;" for="author_name_<?php echo $author->id; ?>">Name</label>
        <input class="listAuthorName" style="vertical-align: top; margin:0 10px 0 0;" type="text" id="author_name_<?php echo $author->id; ?>" name="author_name[<?php echo $author->id; ?>]" value="<?php echo $author->fullname; ?>" />
        <label style="vertical-align: top;" for="author_description_<?php echo $author->id; ?>">Description</label>
        <textarea class="listAuthorDesc" style="vertical-align: top; width:40%; height:70px; margin:0 10px 0 0;" id="author_description_<?php echo $author->id; ?>" name="author_description[<?php echo $author->id; ?>]"><?php echo $author->description; ?></textarea>
        <button class="updateAuthor button" type="button" data-id="<?php echo $author->id; ?>">Update</button>
        <button class="deleteAuthor button" type="button" data-id="<?php echo $author->id; ?>">Delete</button>
    </div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div style="margin:10px 0 10px 0;">No author(s).</div>
<?php endif; 