<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos - EduGestion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .hero-section {
            padding: 120px 0 80px;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .content-section {
            padding: 80px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 3rem;
            color: var(--dark-color);
        }

        .about-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e5e7eb;
        }

        .about-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .about-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
        }

        .about-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .about-description {
            color: #6b7280;
            line-height: 1.6;
        }

        .team-section {
            padding: 80px 0;
            background: var(--light-color);
        }

        .team-member {
            text-align: center;
            margin-bottom: 2rem;
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 3rem;
        }

        .team-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .team-role {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .team-bio {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .stats-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .timeline-section {
            padding: 80px 0;
            background: white;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--primary-color);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding-left: 30px;
        }

        .timeline-marker {
            position: absolute;
            left: -22px;
            top: 0;
            width: 12px;
            height: 12px;
            background: var(--primary-color);
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--primary-color);
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
        }

        .timeline-year {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .timeline-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .timeline-description {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .cta-section {
            padding: 80px 0;
            background: var(--light-color);
            text-align: center;
        }

        .btn-cta {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="home">
                <i class="fas fa-graduation-cap me-2"></i>
                EduGestion
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home#features">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm ms-2" href="login">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Connexion
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">À propos d'EduGestion</h1>
            <p class="hero-subtitle">
                Découvrez notre mission, notre équipe et notre vision pour révolutionner la gestion académique
            </p>
        </div>
    </section>

    <!-- Notre Mission -->
    <section class="content-section">
        <div class="container">
            <h2 class="section-title">Notre Mission</h2>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="about-card">
                        <div class="about-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3 class="about-title">Vision</h3>
                        <p class="about-description">
                            Devenir la référence en matière de solutions de gestion académique, 
                            en facilitant l'administration des établissements éducatifs à travers le monde.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="about-card">
                        <div class="about-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3 class="about-title">Mission</h3>
                        <p class="about-description">
                            Simplifier et digitaliser la gestion académique pour permettre aux 
                            établissements de se concentrer sur leur mission principale : l'éducation.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="about-card">
                        <div class="about-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3 class="about-title">Valeurs</h3>
                        <p class="about-description">
                            Innovation, excellence, transparence et engagement envers 
                            l'amélioration continue de l'expérience éducative.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notre Histoire -->
    <section class="timeline-section">
        <div class="container">
            <h2 class="section-title">Notre Histoire</h2>
            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2020</div>
                        <h4 class="timeline-title">Naissance du projet</h4>
                        <p class="timeline-description">
                            Création d'EduGestion suite à l'identification des défis 
                            de gestion dans les établissements éducatifs.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2021</div>
                        <h4 class="timeline-title">Première version</h4>
                        <p class="timeline-description">
                            Lancement de la première version d'EduGestion avec les 
                            fonctionnalités de base de gestion des étudiants et enseignants.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2022</div>
                        <h4 class="timeline-title">Expansion</h4>
                        <p class="timeline-description">
                            Ajout de nouvelles fonctionnalités : gestion des notes, 
                            emplois du temps et rapports avancés.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2023</div>
                        <h4 class="timeline-title">Innovation</h4>
                        <p class="timeline-description">
                            Intégration de l'intelligence artificielle pour 
                            l'analyse prédictive et l'optimisation des processus.
                        </p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-marker"></div>
                    <div class="timeline-content">
                        <div class="timeline-year">2024</div>
                        <h4 class="timeline-title">Leadership</h4>
                        <p class="timeline-description">
                            EduGestion devient la solution de référence pour 
                            plus de 500 établissements éducatifs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Établissements</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">50K+</div>
                        <div class="stat-label">Étudiants</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">5K+</div>
                        <div class="stat-label">Enseignants</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">99%</div>
                        <div class="stat-label">Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notre Équipe -->
    <section class="team-section">
        <div class="container">
            <h2 class="section-title">Notre Équipe</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="team-name">Marie Dupont</h4>
                        <p class="team-role">Directrice Générale</p>
                        <p class="team-bio">
                            Experte en gestion éducative avec plus de 15 ans d'expérience 
                            dans l'administration académique.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="team-name">Pierre Martin</h4>
                        <p class="team-role">Directeur Technique</p>
                        <p class="team-bio">
                            Développeur passionné spécialisé dans les solutions 
                            éducatives et l'innovation technologique.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-member">
                        <div class="team-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="team-name">Sophie Bernard</h4>
                        <p class="team-role">Responsable Produit</p>
                        <p class="team-bio">
                            Spécialiste en expérience utilisateur et en conception 
                            de solutions adaptées aux besoins éducatifs.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container">
            <h2 class="section-title">Prêt à nous rejoindre ?</h2>
            <p class="hero-subtitle text-muted mb-4">
                Découvrez comment EduGestion peut transformer la gestion de votre établissement.
            </p>
            <div class="cta-buttons">
                <a href="contact" class="btn-cta">
                    <i class="fas fa-envelope me-2"></i>
                    Nous contacter
                </a>
                <a href="login" class="btn-cta">
                    <i class="fas fa-rocket me-2"></i>
                    Essayer gratuitement
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        © 2024 EduGestion. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        Développé avec <i class="fas fa-heart text-danger"></i> pour l'éducation
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animation des éléments au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les cartes et éléments
        document.querySelectorAll('.about-card, .team-member, .timeline-item').forEach(element => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'all 0.6s ease';
            observer.observe(element);
        });

        // Animation des statistiques
        function animateStats() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const target = parseInt(stat.textContent);
                const increment = target / 50;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current) + (stat.textContent.includes('+') ? '+' : '') + (stat.textContent.includes('%') ? '%' : '');
                }, 50);
            });
        }

        // Déclencher l'animation des stats quand la section est visible
        const statsSection = document.querySelector('.stats-section');
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statsObserver.observe(statsSection);
    </script>
</body>
</html> 