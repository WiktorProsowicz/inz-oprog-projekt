<div class="gridtile rounded-3">

    <a class="d-flex flex-column h-100 align-items-center justify-content-between " <?php echo 'href="/read.php?p=' . $tile_id . '"';?> >
        <span class="gridtile__title">
            <?php echo $tile_title; ?>
        </span>

        <span class="gridtile__content text-secondary">
            <?php echo $tile_short; ?>
        </span>

        <div class="gridtile__author d-flex flex-column align-items-center">
                <?php 

                    echo '<span>' .$tile_author. '</span>';
                    echo '<span class="text-secondary">' .$tile_cat. '</span>';
                    
                ?>
        </div>
    </a>

</div>