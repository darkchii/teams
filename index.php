<?php //enable error reporting 
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
require_once('models/TeamFilter.php');
require_once('models/Team.php');

?>
<html>
    <?php
        //if ID is not set in url query
        if(!isset($_GET['id'])){
            if(isset($_GET['view']) && $_GET['view'] !== 'list'){
                require_once('views/TeamGraphs.php');
            }else{
                require_once('views/TeamListing.php');
            }
        }else{
            require_once('views/TeamDetail.php');
        }
    ?>
    <script>
        //get from php
        let active_sorter = '<?php echo $filter?->getOrder() ?? 'rank' ?>';
    </script>
    <script src="script.js"></script>
</html>