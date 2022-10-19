<div class="gridtile bg-light rounded-3">

    <a class="d-flex flex-column h-100 align-items-center justify-content-between " <?php echo 'href="/read.php?p=' . $tile_title . '"';?> >
        <span class="gridtile__title">
            <?php echo $tile_title; ?>
        </span>

        <span class="gridtile__content">
            <?php echo $tile_short; ?>
        </span>

        <div class="gridtile__tags">

            <ul class="gridtile__tagsList d-flex justify-content-start">

                <?php 
                
                    foreach($tile_tags as $tag) {
                        echo '
                            <li class="p-1 bg-secondary text-light rounded-2">'
                            
                                .$tag.
                            
                            '</li>
                        ';
                    }
                
                ?>

            </ul>

        </div>
    </a>


</div>