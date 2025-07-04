<?php $data_prominent_sites = Team::findMostAppearingTeamSites(limit: 20); ?>
<table class="table table-striped table-bordered" style="width: 100%;">
    <thead>
        <tr>
            <th>Link</th>
            <th>Frequency</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_prominent_sites as $site => $count): ?>
            <tr>
                <td><?php echo htmlspecialchars($site); ?></td>
                <td><?php echo htmlspecialchars($count); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
