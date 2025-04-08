<?php include __DIR__ . '/layout.php'; ?>
<div class="bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Trouvez votre alternance avec HelloCandidate !
            </h1>
            <p class="text-lg md:text-xl mb-8">
                Une plateforme gamifiée pour rendre votre recherche d'alternance motivante et engageante.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="/register" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 text-sm font-medium rounded-md hover:bg-gray-100 transition-colors duration-200">
                    Inscription
                </a>
                <a href="/login" class="inline-flex items-center px-6 py-3 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 transition-colors duration-200">
                    Connexion
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">
                Pourquoi choisir HelloCandidate ?
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1: Gamification -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Système de Gamification</h3>
                    <p class="text-gray-600">
                        Progressez à travers des rangs et sous-rangs en envoyant des candidatures. Chaque étape vous rapproche du sommet !
                    </p>
                </div>

                <!-- Feature 2: Community Rankings -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Classement Communautaire</h3>
                    <p class="text-gray-600">
                        Comparez-vous aux autres utilisateurs et visez le top 100 du classement global.
                    </p>
                </div>

                <!-- Feature 3: Progress Tracking -->
                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <svg class="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Suivi de Progression</h3>
                    <p class="text-gray-600">
                        Suivez vos candidatures et votre progression dans les rangs avec des outils intuitifs.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Rank Progression Preview -->
    <div class="bg-gray-100 py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">
                Découvrez notre système de rangs
            </h2>
            <div class="flex flex-wrap justify-center items-center space-x-4">
                <?php foreach ($ranks as $index => $rank): ?>
                    <!-- Rank Image -->
                    <div class="flex flex-col items-center">
                        <?php
                        // Normalize the rank_name to lowercase and encode spaces for the URL
                        $rankImageName = strtolower($rank);
                        // Do NOT replace spaces with hyphens; instead, encode the filename for the URL
                        $rankImagePath = "/public/rank/" . rawurlencode($rankImageName) . ".png";
                        ?>
                        <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($rank); ?>" class="w-12 h-12 mb-2">
                        <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($rank); ?></span>
                    </div>

                    <!-- Arrow (except after the last rank) -->
                    <?php if ($index < count($ranks) - 1): ?>
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <p class="text-center text-gray-600 mt-8">
                Progressez de Fer à Challenger en envoyant des candidatures et atteignez le sommet !
            </p>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="py-16">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">
                Foire aux questions (FAQ)
            </h2>
            <div class="bg-white rounded-lg shadow-lg p-8 max-w-3xl mx-auto">
                <?php
                $faqs = [
                    [
                        'question' => 'Comment fonctionne le système de rangs ?',
                        'answer' => 'Il y a 10 rangs à conquérir : Fer, Bronze, Argent, Or, Platine, Émeraude, Diamant, Maître, Grand Maître, et Challenger. Chaque rang est divisé en 3 sous-rangs (par exemple, Fer 3, Fer 2, Fer 1). Pour passer d’un rang à un autre (ex. : Bronze 1 à Argent 3), vous devez compléter les 3 sous-rangs du rang actuel en envoyant des candidatures.'
                    ],
                    [
                        'question' => 'Que se passe-t-il si je n\'envoie pas de candidatures ?',
                        'answer' => 'Si vous n\'envoyez pas de candidatures régulièrement, vous risquez de stagner dans votre rang actuel. De plus, un système de déclassement hebdomadaire peut réduire votre progression si vous êtes inactif trop longtemps. Restez actif pour continuer à grimper dans les rangs !'
                    ],
                    [
                        'question' => 'Comment puis-je commencer à utiliser HelloCandidate ?',
                        'answer' => 'C\'est simple ! Inscrivez-vous sur la plateforme en cliquant sur "Inscription", puis ajoutez votre première candidature via l\'option "Ajouter une candidature". Vous commencerez immédiatement à progresser dans les rangs.'
                    ],
                    [
                        'question' => 'Puis-je comparer mes progrès avec d\'autres utilisateurs ?',
                        'answer' => 'Oui ! Vous pouvez consulter le classement global pour voir votre position par rapport aux autres utilisateurs et viser le top 100. C\'est une excellente façon de rester motivé !'
                    ]
                ];
                ?>

                <?php foreach ($faqs as $index => $faq): ?>
                    <!-- FAQ Item -->
                    <div class="faq-item mb-4" data-index="<?php echo $index; ?>">
                        <button
                            class="faq-question w-full text-left text-lg font-semibold text-blue-600 hover:text-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md py-2 px-4 flex justify-between items-center"
                            aria-expanded="false"
                            aria-controls="faq-answer-<?php echo $index; ?>"
                            tabindex="0"
                        >
                            <span><?php echo htmlspecialchars($faq['question']); ?></span>
                            <svg class="w-5 h-5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div
                            id="faq-answer-<?php echo $index; ?>"
                            class="faq-answer overflow-hidden max-h-0 transition-all duration-500 ease-in-out"
                        >
                            <p class="text-gray-600 mt-2 px-4 pb-4">
                                <?php echo htmlspecialchars($faq['answer']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Divider (except after the last FAQ) -->
                    <?php if ($index < count($faqs) - 1): ?>
                        <hr class="border-t border-gray-200 my-4" aria-hidden="true">
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Call to Action Section -->
    <div class="bg-blue-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">
                Prêt à booster votre recherche d'alternance ?
            </h2>
            <p class="text-lg mb-8">
                Rejoignez HelloCandidate dès aujourd'hui et commencez votre ascension dans les rangs !
            </p>
            <a href="/register" class="inline-flex items-center px-6 py-3 bg-white text-blue-600 text-sm font-medium rounded-md hover:bg-gray-100 transition-colors duration-200">
                Inscription
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Column 1: About HelloCandidate -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">À propos de HelloCandidate</h3>
                    <p class="text-gray-400">
                        HelloCandidate est une plateforme gamifiée qui aide les étudiants à trouver une alternance tout en les motivant grâce à un système de rangs unique.
                    </p>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Liens rapides</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="/" class="text-gray-400 hover:text-white transition-colors duration-200">Accueil</a>
                        </li>
                        <li>
                            <a href="/help" class="text-gray-400 hover:text-white transition-colors duration-200">Aide</a>
                        </li>
                        <li>
                            <a href="/register" class="text-gray-400 hover:text-white transition-colors duration-200">Inscription</a>
                        </li>
                        <li>
                            <a href="/login" class="text-gray-400 hover:text-white transition-colors duration-200">Connexion</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Contact Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contactez-nous</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-gray-400">maxence.villetschoumaker@gmail.com</span>
                        </li>
                    </ul>
                </div>

                <!-- Column 4: Social Media Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Suivez-nous</h3>
                    <div class="flex space-x-4">
                        <a href="https://www.linkedin.com/in/maxence-villet-schoumaker-developpeur-web-mobile/" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                                <circle cx="4" cy="4" r="2" stroke="none" fill="currentColor"></circle>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright Notice -->
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-gray-400 text-sm">
                    © <?php echo date('Y'); ?> HelloCandidate. Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
</div>

<!-- Custom CSS for FAQ Animation -->
<style>
    .faq-answer {
        transition: max-height 0.5s ease-in-out;
    }

    .faq-item.is-open .faq-answer {
        max-height: 500px; /* Adjust this value based on your content height */
    }

    .faq-item.is-open .faq-question svg {
        transform: rotate(180deg);
    }
</style>

<!-- JavaScript for FAQ Accordion -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const button = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');

            // Set initial max-height for closed state
            answer.style.maxHeight = '0px';

            button.addEventListener('click', () => {
                const isOpen = item.classList.contains('is-open');
                const answerId = button.getAttribute('aria-controls');

                // Toggle the is-open class
                item.classList.toggle('is-open');

                // Update aria-expanded
                button.setAttribute('aria-expanded', !isOpen);

                // Set max-height for animation
                if (!isOpen) {
                    // When opening, set max-height to the scrollHeight of the content
                    answer.style.maxHeight = `${answer.scrollHeight}px`;
                } else {
                    // When closing, set max-height back to 0
                    answer.style.maxHeight = '0px';
                }
            });

            // Add keyboard support (Enter and Space keys)
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    button.click();
                }
            });
        });
    });
</script>
</body>
</html>