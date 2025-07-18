<?php $data_prominent_names = Team::findMostAppearingTeamNames(limit: 20); ?>
<!-- <canvas id="team-name-words-graph"></canvas> -->
<table class="table table-striped table-bordered" style="width: 100%;">
    <thead>
        <tr>
            <th>Word</th>
            <th>Frequency</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_prominent_names as $word => $count): ?>
            <tr>
                <td><?php echo htmlspecialchars($word); ?></td>
                <td><?php echo htmlspecialchars($count); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>