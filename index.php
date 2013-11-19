<?php
    include('include/header.php');
    $allMembers = array();
    $allEvents = array();
?>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h1>
                    <div class="row">
                        <div class="col-xs-4 col-sm-2">
                            <span class="fa-stack fa-lg pull-right">
                              <i class="fa fa-sun-o fa-stack-2x"></i>
                              <i class="fa fa-bullhorn fa-stack-1x"></i>
                            </span>
                        </div>
                        <div class="col-xs-8 col-sm-10" style="padding-top: 10px;">
                            Public review of new legislation
                            <br/>
                            <small>Review and comment on new bills</small>
                        </div>
                    </div>
                </h1>
                <div class="clearfix"></div>
                
                <div class="row">
                    <div class="col-sm-2">
                        &nbsp;
                    </div>
                    <div class="col-sm-10">
                        <div class="alert alert-info">
                            <p><i class="fa fa-info-circle"></i> This site was originally created during Parliament Hack 2013. 
                            <a href="https://twitter.com/search?q=%23rsparly2013">#RSPARLY2013</a>
                        </div>
                    </div>
                </div>
                
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#">Bills before Parliament</a></li>
                </ul>
                
                <?php
                    $j = 0;
                    foreach (Bills::getAllBillsBeforeParliament() as $bill):
                    
                        foreach ($bill->getEvents() as $event) {
                            $event->url = '/view-bill/'.$bill->id;
                            array_push($allEvents, $event);
                            break;
                        }

                        //$j++;
                        //if ($j > 20)
                        //    break;
                            
                        // Ignore early bills with no details yet (or somehow broken because of the crummy parsing)
                        if (count($bill->getMembers()) == 0)
                            continue;
                 ?>
                <div class="media">
                    <div class="media-object pull-left" style="padding-top: 10px;">
                        <!--
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
                        -->
                    </div>
                    <div class="media-body">
                        <h3 style="margin-top: 0;"><a href="/view-bill/<?= $bill->id ?>" style="text-decoration: none;"><?= htmlspecialchars($bill->title) ?> Bill</a></h3>
                        <?php  
                            foreach ($bill->getMembers() as $member):

                                if (!isset($allMembers[sha1($member->name)])) {
                                    $allMembers[sha1($member->name)] = array();
                                    $allMembers[sha1($member->name)]['member'] = $member;
                                    $allMembers[sha1($member->name)]['bills'] = 1;
                                } else {
                                    $allMembers[sha1($member->name)]['bills']++;
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
            
                <h3>Upcoming events</h3>
                <?php if (count($allEvents) == 0): ?>
                    <p>
                        <span class="text-muted">No events related to bills scheduled.</span>
                    </p>
                <?php endif; ?>
                <?php 
                    $sortedEvents = array();
                    foreach ($allEvents as $event) {
                        $timestamp = strtotime($event->date);
                        
                        // Ignore past events
                        if ($timestamp < time())
                            continue;
                                                    
                        if (!array_key_exists($timestamp, $sortedEvents))
                            $sortedEvents[$timestamp] = array();
                        
                        array_push($sortedEvents[$timestamp], $event);
                    }
                    ksort($sortedEvents);
                ?>                
                <?php foreach ($sortedEvents as $day => $events): ?>
                    <p>
                        <i class="fa fa-calendar"></i> <?= date('l jS F, Y', $day); ?><br/>
                    </p>
                    <?php foreach ($events as $event): ?>
                        <p> 
                            <a href="<?= htmlspecialchars($event->url) ?>"><?= $event->name ?></a>
                        </p>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            
                <h3>Members with bills</h3>
                <hr/>
                <?php  
                    foreach ($allMembers as $array):
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