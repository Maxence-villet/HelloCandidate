<?php
$page_title = 'Détails de la Candidature';
include __DIR__ . '/../../utils/header/header_student.php';
?>

<main class="flex-1 p-4 sm:p-6 md:p-8">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Détails de la Candidature
        </h2>

        <div class="space-y-4">
            <div>
                <span class="font-semibold text-gray-700">Entreprise :</span>
                <span class="text-gray-900"><?php echo htmlspecialchars($application['company_name']); ?></span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Poste :</span>
                <span class="text-gray-900"><?php echo htmlspecialchars($application['position']); ?></span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Date de soumission :</span>
                <span class="text-gray-900"><?php echo date('d/m/Y', strtotime($application['submission_date'])); ?></span>
            </div>
            <div>
                <span class="font-semibold text-gray-700">Statut :</span>
                <?php 
                    $statusClasses = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'interview' => 'bg-blue-100 text-blue-800',
                        'rejected' => 'bg-red-100 text-red-800',
                        'accepted' => 'bg-green-100 text-green-800'
                    ];
                    $statusText = [
                        'pending' => 'En attente',
                        'interview' => 'Entretien',
                        'rejected' => 'Refusé',
                        'accepted' => 'Accepté'
                    ];
                ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClasses[$application['status']]; ?>">
                    <?php echo $statusText[$application['status']]; ?>
                </span>
            </div>
            <?php if ($application['address']): ?>
                <div>
                    <span class="font-semibold text-gray-700">Adresse :</span>
                    <span class="text-gray-900"><?php echo htmlspecialchars($application['address']); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($application['offer_link']): ?>
                <div>
                    <span class="font-semibold text-gray-700">Lien de l’offre :</span>
                    <a href="<?php echo htmlspecialchars($application['offer_link']); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($application['offer_link']); ?></a>
                </div>
            <?php endif; ?>
            <?php if ($application['description']): ?>
                <div>
                    <span class="font-semibold text-gray-700">Description :</span>
                    <p class="text-gray-900"><?php echo nl2br(htmlspecialchars($application['description'])); ?></p>
                </div>
            <?php endif; ?>
            <?php if ($application['cover_letter_path']): ?>
                <div>
                    <span class="font-semibold text-gray-700">Lettre de motivation :</span>
                    <a href="<?php echo "../public" . $application['cover_letter_path']; ?>" target="_blank" class="text-blue-600 hover:underline">Télécharger</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-6">
            <a href="/applications" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Retour à la liste
            </a>
        </div>
    </div>
</main>
</body>
</html>