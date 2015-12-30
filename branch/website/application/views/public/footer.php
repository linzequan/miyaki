    <?php
        $base_url = base_url('/application/views/assets').'/';
    ?>
    <!--[if !IE]> -->
    <script src="<?php echo $base_url; ?>js/jquery.min.js"></script>
    <!-- <![endif]-->

    <!--[if IE]>
    <script src="<?php echo $base_url; ?>jquery.min.js"></script>
    <![endif]-->

    <!--[if !IE]> -->
    <script type="text/javascript">
        window.jQuery || document.write("<script src='<?php echo $base_url; ?>js/jquery-2.0.3.min.js'>"+"<"+"/script>");
    </script>
    <!-- <![endif]-->

    <!--[if IE]>
    <script type="text/javascript">
        window.jQuery || document.write("<script src='<?php echo $base_url; ?>/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
    </script>
    <![endif]-->

    <script type="text/javascript">
        if("ontouchend" in document) document.write("<script src='<?php echo $base_url; ?>js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>

    <script src="<?php echo $base_url; ?>js/bootstrap.min.js"></script>
    <script src="<?php echo $base_url; ?>js/typeahead-bs2.min.js"></script>
    <script src="<?php echo $base_url; ?>js/ace-elements.min.js"></script>
    <script src="<?php echo $base_url; ?>js/ace.min.js"></script>
    <script src="<?php echo $base_url; ?>js/jquery.gritter.min.js"></script>
    <script src="<?php echo $base_url; ?>js/utils.js"></script>

    <?php
        if(isset($js)==true){
            foreach ($js as $val){
                echo '<script type="text/javascript" src="'.$base_url.'js/'.ltrim($val,'/').'"></script>'."\r\n";
            }
        }
    ?>
</html>
