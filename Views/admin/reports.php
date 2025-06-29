<?php include_once __DIR__ . '/../layouts/header.php'; ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-white mb-6">Signalements & Sanctions</h1>
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-4">Signalements récents</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Cible</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Motif</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Détails</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Par</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Statut</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td class="px-4 py-2 text-gray-300"><?php echo $report['id']; ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo ucfirst($report['type']); ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo $report['target_id']; ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($report['reason']); ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo nl2br(htmlspecialchars($report['details'])); ?></td>
                        <td class="px-4 py-2 text-gray-300"><?php echo htmlspecialchars($report['reporter']); ?></td>
                        <td class="px-4 py-2">
                            <?php if ($report['status'] === 'pending'): ?>
                                <span class="px-2 py-1 bg-yellow-600 text-white rounded text-xs">En attente</span>
                            <?php elseif ($report['status'] === 'reviewed'): ?>
                                <span class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Traité</span>
                            <?php elseif ($report['status'] === 'rejected'): ?>
                                <span class="px-2 py-1 bg-gray-600 text-white rounded text-xs">Rejeté</span>
                            <?php elseif ($report['status'] === 'sanctioned'): ?>
                                <span class="px-2 py-1 bg-red-600 text-white rounded text-xs">Sanction</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-2 text-gray-400 text-xs"><?php echo $report['created_at']; ?></td>
                        <td class="px-4 py-2">
                            <a href="/admin/reports/view?id=<?php echo $report['id']; ?>" class="text-blue-400 hover:underline">Voir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../layouts/footer.php'; ?> 