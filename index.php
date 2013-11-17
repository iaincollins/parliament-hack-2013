<?php
    include('include/header.php');
    $members = array();
?>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h1>
                    <span class="fa-stack fa-lg pull-left" style="margin-right: 20px;">
                      <i class="fa fa-sun-o fa-stack-2x"></i>
                      <i class="fa fa-bullhorn fa-stack-1x"></i>
                    </span>
                    <span class="pull-left">
                        Review &amp; comment on new legislation
                        <br/>
                        <small>Public feedback on draft bills</small>
                    </span>
                </h1>
                <div class="clearfix"></div>
                <br/>
                <?php
                    $j = 0;
                    foreach (Bills::getBills() as $bill):
                        $j++;
                        if ($j > 20)
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
                            <?=
                                $for = 100;
                                $against = rand(0,100);
                                $for -= $against;
                            ?>
                                <li data-value="<?= $for ?>">For</li>
                                <li data-value="<?= $against ?>">Against</li>
                            </ul>
                        </div>
                        <div class="vote-buttons">
                            <div class="btn btn-sm btn-default"><i class="fa fa-chevron-up"></i></div>
                            <div class="btn btn-sm btn-default"><i class="fa fa-chevron-down"></i></div>
                        </div>
                    </div>
                    <div class="media-body">
                        <h3 style="margin-top: 0;"><a href="/view-bill/<?= $bill->id ?>" style="text-decoration: none;"><?= htmlspecialchars($bill->title) ?> Bill</a></h3>
                        <?php  
                            foreach ($bill->getMembers() as $memberName):
                                $member = Member::getMemberByName($memberName);
                                
                                if (!isset($members[sha1($member->name)])) {
                                    $members[sha1($member->name)] = array();
                                    $members[sha1($member->name)]['member'] = $member;
                                    $members[sha1($member->name)]['bills'] = 1;
                                } else {
                                    $members[sha1($member->name)]['bills']++;
                                }
                        ?>
                                <?php if ($member->avatar): ?>
                                <img class="avatar" height="30px" src="<?= $member->avatar ?>" />
                                <?php endif; ?>
                                <a style="margin-right: 10px;" href="<?= $member->url ?>"><?= htmlspecialchars($member->name) ?></a>
                        <?php endforeach; ?>
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
                <br/>
                <?php endforeach; ?>
            </div>
            <div class="col-md-3">
                <h3>MPs with active bills</h3>
                <hr/>
            <?php  
                foreach ($members as $array):
                $member = $array['member'];
                $numberOfBills = $array['bills'];
            ?>
                <p>
                    <?php if ($member->avatar): ?>
                    <img class="avatar" height="48px" src="<?= $member->avatar ?>" />
                    <?php endif; ?>
                    <a style="margin-right: 10px;" href="<?= $member->url ?>"><?= htmlspecialchars($member->name) ?></a>
                    <span class="badge"><?= $numberOfBills ?></span>
                </p>
            <?php endforeach; ?>
            </div>
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