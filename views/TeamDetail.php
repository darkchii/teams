<?php
$team = Team::getTeamById($_GET['id']);

if (!$team) {
    $team = Team::getTeamById($_GET['id'], 'short_name');
}
?>

<head>
    <title><?php echo ($team!=null ? $team->getShortName().' '.$team->getName() : 'No team found') ?> - osu! teams browser</title>

    <?php require_once('views/TeamBaseHead.php'); ?>

    <meta property="og:title" content="<?php echo ($team!=null ? '['.$team->getShortName().'] '.$team->getName() : 'No team found') ?> - osu! teams browser">
    <meta property="og:description" content="View information about <?php echo $team?->getName() ?? 'this team' ?> in osu!">
    <meta property="og:image" content="<?php echo $team?->getFlagUrl() ?? '' ?>">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI'] ?>">
    <meta name="theme-color" content="#ff21b9">
    <meta name="twitter:card" content="summary_large_image">
</head>

<!-- redirect to the osu website, we dont have anything to display at this point. We just generate meta data for display in Discord -->

<body>
    <script>
        window.location.replace('https://osu.ppy.sh/teams/<?php echo $team?->getId() ?? '1' ?>');
    </script>
</body>