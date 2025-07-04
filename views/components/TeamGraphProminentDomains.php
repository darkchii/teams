<?php $data_prominent_domains = Team::findMostAppearingTeamDomains(limit: 20); ?>
<table class="table table-striped table-bordered" style="width: 100%;">
    <thead>
        <tr>
            <th>Domain</th>
            <th>Frequency</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data_prominent_domains as $domain => $count): ?>
            <tr>
                <td><?php echo htmlspecialchars($domain); ?></td>
                <td><?php echo htmlspecialchars($count); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
