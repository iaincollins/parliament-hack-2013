<?php
    include('include/header.php'); 
    $bill = Bill::getBillById($_REQUEST['id']);
?>
    <div class="container">
        <div class="col-md-9">
            <div class="media">
                <div class="media-object pull-left" style="padding-top: 30px;">
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
                    <h1><?= htmlspecialchars($bill->title) ?></h1>
                    <p class="lead">
                        <?php
                            foreach (explode("\n", $bill->description) as $description) {
                                echo htmlspecialchars($description);
                                break;
                            }
                        ?>
                    </p>
                    <ul class="list-unstyled">
                        <?php
                            $i = 0;
                            foreach (explode("\n", $bill->description) as $description) {
                                $i++;
                                if ($i == 1)
                                    continue;
                                    
                                echo '<li><i class="fa fa-chevron-right"></i> '.htmlspecialchars($description).'</li>';
                            }
                        ?>
                    </ul>
                </div>
            </div>            
            <?php if ($bill->getBillText() == false): ?>
                <h4 class="text-danger text-center">The text for this bill is not yet available.</h4>
            <?php else: ?>
                <h4 class="pull-left">The current draft of the bill in full</h4>
                <p class="pull-right" style="padding-top: 10px;">
                    <a href="<?= $bill->getPdfUrl() ?>"><i class="fa fa-file"></i> View as PDF</a>
                </p>
                <div class="clearfix"></div>
                <div class="panel panel-default" style="height: 500px; overflow: hidden; border-width: 4px;">
                    <div class="panel-body" style="padding: 5px;">
                        <iframe width="100%" style="height: 480px; border: 0;" src="/bill-text/?id=<?= $bill->id ?>"></iframe>
                    </div>
                </div>
            <?php endif; ?>
            <h2>Comments</h2>
           <div id="disqus_thread"></div>
            <script type="text/javascript">
                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                var disqus_shortname = 'parliament-hack-2013'; // required: replace example with your forum shortname
        
                /* * * DON'T EDIT BELOW THIS LINE * * */
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
                <strong><?= $bill->getBillType() ?></strong>
            </p>
            <p>
                Sponsored by
            </p>
            <?php foreach ($bill->getMembers() as $memberName): ?>
                <h4><a href="#"><?= $memberName ?></a></h4>
            <?php endforeach; ?>
            <hr/>
            <!--
            <strong>Next stage</strong>
            <p>
                Second reading in the House of Commons on Tuesday 17th January, 2014.
            </p>
            <hr/>
            -->
            <h3>Progress</h3>
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

<!--             <h4>Related documents</h4> -->
        </div>
    </div><!-- /.container -->
<?php include('include/footer.php'); ?>