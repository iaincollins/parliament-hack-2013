<?php include('include/header.php'); ?>
    <div class="container">
        <div class="col-md-9">
            <h1>Review &amp; feedback on new legislation</h1>
            <p class="lead">
                Public consultation and review of draft bills
            </p>
            <?php for ($i = 0; $i < 10; $i++) { ?>
            <div class="media">
                <div class="media-object pull-left" style="padding-top: 10px;">
                    <p>
                        <span class="text-success">0 for</span>
                    </p>
                    <p>
                        <span class="text-danger">0 against</span>
                    <p>
                    <div class="btn btn-sm btn-default"><i class="fa fa-chevron-up"></i></div>
                    <div class="btn btn-sm btn-default"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="media-body">
                    <h3 style="margin-top: 0;"><a href="/view-bill/">Name of the bill</a></h3>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco.
                    </p>
                    <p class="tags">
                        <span class="label label-info">Tag</span>
                        <span class="label label-info">Another tag</span>
                    </p>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="col-md-3">
            <h3>Upcoming legislation</h3>
        <div>
    </div><!-- /.container -->
<?php include('include/footer.php'); ?>