<?php if ($max > 0) { ?>

    <div class="pagination">
        <ul>
            <li><a href="#" data-page="<?php echo $input['page'] - 1 ?>">Prev</a></li>
            <li <?php echo $input['page'] == 1 ? 'class="active"' : '' ?>><a href="#" data-page="1">1</a></li>

            <?php if ($input['page'] > 5) { ?>
                <li><a href="#" data-page="0">...</a></li>
            <?php } ?>

            <?php $start = $input['page'] > 4 ? $input['page'] - 3 : 2 ?>

            <?php for ($i = $start; $i < $max && $i < $start + 7; ++$i) { ?>
                <li <?php echo $input['page'] == $i ? 'class="active"' : '' ?>><a href="#" data-page="<?php echo $i ?>"><?php echo $i ?></a></li>
            <?php } ?>

            <?php if ($input['page'] + 7 < $max) { ?>
                <li><a href="#" data-page="0">...</a></li>
            <?php } ?> 

            <?php if ($max != 1) { ?>
                <li <?php echo $input['page'] == $max ? 'class="active"' : '' ?>><a href="#" data-page="<?php echo $max ?>"><?php echo $max ?></a></li>
            <?php } ?>
            <li><a href="#" data-page="<?php echo $input['page'] + 1 ?>">Next</a></li>
        </ul>
    </div>
<?php } ?>

<table class="table table-striped table-hover table-condensed courses">
    <tr>
        <th>®datetime®</th>
        <th>®table®</th>
        <th>®message®</th>
        <th>®author®</th>
    </tr>

    <?php foreach ($logs as $l) {
        ?>
        <tr>
            <td><?php echo $l['time']; ?></td>
            <td><?php echo $l['table']; ?></td>
            <td><?php echo $l['message']; ?></td>
            <td><?php echo $l['author']; ?></td>
        </tr>
        <?php
    }
    ?>

</table>

<script>

    $(function () {

        $(".pagination li").click(function () {
            if ($(this).hasClass('active'))
                return;
            page($(this).find("a").data("page"));
        });

        function page(n) {
            if (!n || n < 1 || n > <?php echo $max ?>)
                return;
            var $form = $("form.search_logs");
            $form.find("input[name='page']").first().val(n);
            $form.submit();
        }
    });

</script>