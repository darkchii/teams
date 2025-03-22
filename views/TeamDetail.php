<?php
$team = Team::getTeamById($_GET['id']);

if (!$team) {
    $team = Team::getTeamById($_GET['id'], 'short_name');
}

$description = "No team found";

if($team){
    $team->fetchMembers();

    if(count($team->members_list) > 0){
        $description = "Leader: ".$team->getLeader()->getUsername() . " | Members: " . $team->getMembers();
    }else{
        $description = "Check out ".$team->getName()." out on osu! website";
    }
}
?>

<head>
    <title><?php echo ($team!=null ? $team->getShortName().' '.$team->getName() : 'No team found') ?> - osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>

    <meta property="og:title" content="<?php echo ($team!=null ? '['.$team->getShortName().'] '.$team->getName() : 'No team found') ?> - osu! teams browser">
    <meta property="og:description" content="<?php echo $description ?>">
    <meta property="og:image" content="<?php echo $team?->getFlagUrl() ?? '' ?>">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <meta name="theme-color" content="<?php echo $team?->getColor() ?? '#ff21b9' ?>">
    <meta name="twitter:card" content="summary_large_image">
</head>

<!-- redirect to the osu website, we dont have anything to display at this point. We just generate meta data for display in Discord -->

<body>
    <script>
        window.location.replace('https://osu.ppy.sh/teams/<?php echo $team?->getId() ?? '1' ?>');
    </script>
</body>