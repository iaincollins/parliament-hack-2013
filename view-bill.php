<?php
    include('include/header.php'); 
    $billId = str_replace('/view-bill/', '', $_SERVER['REQUEST_URI']);
    $bill = Bill::getBillById($billId);
?>
    <div class="container">
        <div class="col-md-9">
            <div class="media">
                <div class="media-object pull-left" style="padding-top: 30px;">
                    <!--
                    <div style="height: 75px; width: 75px;">
                        <div style="position: relative; left: -36px; top: -40px; height: 96px; width: 96px;" id="votes"></div>
                        <ul data-pie-id="votes" class="votes hidden">
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
                    <h2><?= htmlspecialchars($bill->title) ?> Bill</h2>
                    <p>
                        <span class="label label-info"><?= $bill->type->name ?></span>
                    </p>
                    <p class="lead">
                        <?php       
                            $description = $bill->description;
                            $description = preg_replace("/to make provision/i", "", $description);                            
                            $description = ucfirst(trim($description));
                            foreach (explode(";", $description) as $line) {
                                $line = preg_replace("/\.$/i", "", $line);                            
                                $line .= '.';
                                echo htmlspecialchars($line);
                                break;
                            }
                        ?>
                    </p>
                    <ul class="list-unstyled">
                        <?php
                            $i = 0;
                            foreach (explode(";", $description) as $line) {
                                $i++;
                                if ($i == 1)
                                    continue;                                    

                                $line = ucfirst(trim($line));
                                $line = preg_replace("/\.$/i", "", $line);                            
                                $line .= '.';
                                
                                if ($line = "And for connected purposes.")
                                    continue;
                                
                                echo '<li><i class="fa fa-chevron-right"></i> '.htmlspecialchars($line).'</li>';
                            }
                        ?>
                    </ul>
                    <br/>
                    <p>
                        <span class="st_facebook" displayText="Facebook"></span>
                        <span class="st_twitter" displayText="Tweet"></span>
                        <span class="st_googleplus" displayText="Google"></span>
                        <span class="st_email" displayText="Email"></span>
                    </p>
                    <br/>                    
                </div>
            </div>
            <?php if ($bill->getBillText() == false): ?>
                <div class="alert alert-warning">
                    <p>
                        <i class="fa fa-warning"></i>
                        <?= $bill->members[0]->name ?> (<?= $bill->members[0]->party ?>) has not yet submitted any text for this bill. Contact them for details.
                    <p>
                </div>
            <?php else: ?>
                <div class="clearfix"></div>
                <div class="panel panel-default" style="height: 540px; overflow: hidden; border-width: 4px;">
                    <div class="panel-heading">
                        <strong class="pull-left">The current draft of the bill as of <i class="fa fa-calendar"></i> <?= date('l jS F, Y'); ?></strong>
                        <div class="pull-right">
                            <a href="<?= $bill->getPdfUrl() ?>"><i class="fa fa-file"></i> View as PDF</a>
                            | <a href="<?= $bill->url ?>"><i class="fa fa-globe"></i> parliament.uk</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="panel-body" style="padding: 5px;">
                        <iframe width="100%" style="height: 480px; border: 0;" src="/bill-text/<?= $bill->id ?>"></iframe>
                    </div>
                </div>
            <?php endif; ?>
            <h2>Comments</h2>
           <div id="disqus_thread"></div>
            <script type="text/javascript">
                var disqus_shortname = 'parliament-hack-2013';
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            
        </div>
        <div class="col-md-3">
            <p>
                Sponsored by
            </p>
            <?php foreach ($bill->getMembers() as $member): ?>
                <h4>
                    <?php if ($member->avatar): ?>
                    <img class="avatar" height="42px" src="<?= $member->avatar ?>" />
                    <?php endif; ?>
                    <a href="<?= $member->url ?>"><?= htmlspecialchars($member->name) ?></a>
                </h4>
            <?php endforeach; ?>
            <hr/>
            <!--
            <strong>Next stage</strong>
            <p>
                Second reading in the House of Commons on Tuesday 17th January, 2014.
            </p>
            <hr/>
            -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Progress</strong>
                </div>
                <div class="panel-body" style="padding-top: 0;">
                    <?php
                        $phases = array('House of Commons' => array('First reading', 'Second reading', 'Committee stage', 'Report stage', 'Third reading'),
                                        'House of Lords' => array('First reading', 'Second reading', 'Committee stage', 'Report stage', 'Third reading'),   
                                        'Final stages' => array('Amendments', 'Royal assent')
                                        );
                        
                        $liClass = '';
                        $iconClass = 'fa-check';
                        $i = 0;
                        foreach ($phases as $phase => $stages) {
                            echo '<h4>'.$phase.'</h4>';
                            echo '<ol class="list-unstyled">';
                            foreach ($stages as $stage) {
                                echo '<li class="'.$liClass.'">';
                                if ($i == $bill->stage) {
                                    echo '<i class="fa fa-arrow-right"></i> ';
                                    $liClass = "text-muted";
                                    $iconClass .= ' invisible';
                                } else {
                                    echo '<i class="fa '.$iconClass.'"></i> ';
                                }
                                echo $stage;
                                echo '</li>';
                                $i++;
                            }
                            echo ' </ol>';
                        }
                    ?>
                </div>
            </div>
            <h3>Debates &amp; events</h3>
            <?php if (count($bill->getEvents()) == 0): ?>
                <p>
                    <span class="text-muted">No events related to this bill scheduled.</span>
                </p>
            <?php endif; ?>
            <?php foreach ($bill->getEvents() as $event): ?>
                <p> 
                    <i class="fa fa-calendar"></i> <?= date('l jS F, Y', strtotime($event->date)); ?><br/>
                    <a href="<?= htmlentities($event->url) ?>"><?= htmlentities($event->name) ?></a>
                </p>
            <?php endforeach; ?>
        </div>
    </div><!-- /.container -->
    <script>
        $(function() {
            /*
            Pizza.init(document.body, {
                "show_percent": false,
                "donut": true
            });
            */
        });
    </script>
<?php include('include/footer.php'); ?>