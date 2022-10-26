<div class="gridtile bg-light rounded-3">

    <a class="d-flex flex-column h-100 align-items-center justify-content-between " <?php echo 'href="/read.php?p=' . $tile_id . '"';?> >
        <span class="gridtile__title">
            <?php echo $tile_title; ?>
        </span>

        <span class="gridtile__content text-secondary">
            <?php echo $tile_short; ?>
        </span>

        <div class="gridtile__author">
                <?php 
                
                    echo $tile_author;
                
                ?>
        </div>
    </a>

</div>