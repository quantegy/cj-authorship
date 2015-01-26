<?php if(!empty($authors)): ?>
<ul class="authorship">
<?php foreach($authors as $author): ?>
<li class="">
    <?php echo stripslashes($author->fullname); ?>
    <!-- <p><?php //echo stripslashes($author->description); ?></p> -->
</li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
