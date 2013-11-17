<?php include('include/header.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h1>Review &amp; feedback on new legislation</h1>
                <p class="lead">
                    Public consultation and review of draft bills
                </p>
                <?php
                    $j = 0;
                    foreach (Bills::getBills() as $bill):
                        $j++;
                        if ($j > 5)
                            break;
                            
                        $sponsors = $bill->getMembers();

                        // Ignore early bills with no details yet (or somehow broken because of the crummy parsing)
                        if (count($sponsors) == 0)
                            continue;
                        
                        // Ingoring bills that only have a title and no actual text uploaded yet
                        //if ($bill->getBillTextUrl() == false)
                        //    continue;
                 ?>
                <div class="media">
                    <div class="media-object pull-left" style="padding-top: 10px;">
                        <div style="height: 65px; width: 75px;">
                            <div style="position: relative; left: -32px; top: -45px; height: 90px; width: 90px;" id="votes-<?= $bill->id ?>"></div>
                            <ul data-pie-id="votes-<?= $bill->id ?>" class="votes hidden">
                                <li data-value="80">For</li>
                                <li data-value="20">Against</li>
                            </ul>
                        </div>
                        <div class="vote-buttons">
                            <div class="btn btn-sm btn-default"><i class="fa fa-chevron-up"></i></div>
                            <div class="btn btn-sm btn-default"><i class="fa fa-chevron-down"></i></div>
                        </div>
                    </div>
                    <div class="media-body">
                        <h3 style="margin-top: 0;"><a href="/view-bill/?id=<?= $bill->id ?>" style="text-decoration: none;"><?= htmlspecialchars($bill->title) ?></a></h3>
                        <?php 
                            $i = 0;
                            foreach ($sponsors as $memberName):
                                if ($i>0)
                                    echo ', ';
                                $i++;
                                echo '<a href="#">'.htmlspecialchars($memberName).'</a>';
                            endforeach;
                        ?>
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
                <div class="clearfix"></div>
                <?php endforeach; ?>
            </div>
            <div class="col-md-3">
                <h3>Upcoming legislation</h3>
            <div>
        </div>
    </div><!-- /.container -->
    <script>
        $(function() {
            Pizza.init(document.body, {
                "show_percent": false,
                "donut": true
            });
        });
    </script>
<?php include('include/footer.php'); ?>