<?php include('include/header.php'); ?>
    <div class="container">
        <div class="col-md-9">
            <h1>Review &amp; feedback on new legislation</h1>
            <p class="lead">
                Public consultation and review of draft bills
            </p>
            <?php
                foreach (Bills::getBills() as $bill):
                
                    // Ingoring bills that only have a title and no actual text uploaded yet
                    //if ($bill->getBillTextUrl() == false)
                    //    continue;
             ?>
            <div class="media">
                <div class="media-object pull-left" style="padding-top: 10px;">
                    <p>
                        <span class="text-success">0 for</span>
                    </p>
                    <p>
                        <span class="text-danger">0 against</span>
                    <p>
                    <div class="btn btn-sm btn-success"><i class="fa fa-chevron-up"></i></div>
                    <div class="btn btn-sm btn-danger"><i class="fa fa-chevron-down"></i></div>
                </div>
                <div class="media-body">
                    <h3 style="margin-top: 0;"><a href="/view-bill/?id=<?= $bill->id ?>" style="text-decoration: none;"><?= htmlspecialchars($bill->title) ?></a></h3>
                    <p style="max-height: 40px; overflow: hidden;">
                        <?= htmlspecialchars($bill->description) ?>
                    </p>
                    <p class="tags">
                        <?php foreach ($bill->tags as $tag): ?>
                        <span class="label label-info"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="col-md-3">
            <h3>Upcoming legislation</h3>
        <div>
    </div><!-- /.container -->
<?php include('include/footer.php'); ?>