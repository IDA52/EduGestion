<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - EduGestion</title>
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

        .contact-section {
            padding: 120px 0 80px;
            color: white;
        }

        .contact-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .contact-subtitle {
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 3rem;
            opacity: 0.9;
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
            border: 1px solid #e5e7eb;
        }

        .contact-info {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .contact-item-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-contact {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-contact:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .map-container {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .map-placeholder {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }

        .social-link {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--accent-color);
            transform: translateY(-3px);
            color: white;
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 40px 0 20px;
            margin-top: 4rem;
        }

        @media (max-width: 768px) {
            .contact-title {
                font-size: 2.5rem;
            }
            
            .contact-subtitle {
                font-size: 1.1rem;
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
                        <a class="nav-link" href="about">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="contact">Contact</a>
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

    <!-- Section Contact -->
    <section class="contact-section">
        <div class="container">
            <h1 class="contact-title">Contactez-nous</h1>
            <p class="contact-subtitle">
                Nous sommes là pour vous aider. N'hésitez pas à nous contacter pour toute question ou demande d'assistance.
            </p>

            <div class="row g-4">
                <!-- Formulaire de contact -->
                <div class="col-lg-8">
                    <div class="contact-card">
                        <h3 class="mb-4">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            Envoyez-nous un message
                        </h3>
                        
                        <form id="contactForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Nom complet *</label>
                                        <input type="text" class="form-control" id="nom" name="nom" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telephone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="telephone" name="telephone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sujet" class="form-label">Sujet *</label>
                                        <select class="form-select" id="sujet" name="sujet" required>
                                            <option value="">Choisir un sujet</option>
                                            <option value="demande_info">Demande d'information</option>
                                            <option value="support_technique">Support technique</option>
                                            <option value="devis">Demande de devis</option>
                                            <option value="partenariat">Partenariat</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message *</label>
                                <textarea class="form-control" id="message" name="message" rows="6" required 
                                          placeholder="Décrivez votre demande en détail..."></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="consentement" required>
                                    <label class="form-check-label" for="consentement">
                                        J'accepte que mes données soient traitées pour le traitement de ma demande *
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-contact">
                                <i class="fas fa-paper-plane me-2"></i>
                                Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="col-lg-4">
                    <div class="contact-info">
                        <h3 class="mb-4">
                            <i class="fas fa-info-circle me-2 text-primary"></i>
                            Nos coordonnées
                        </h3>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Adresse</h6>
                                <p class="mb-0 text-muted">123 Rue de l'Éducation<br>75001 Paris, France</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Téléphone</h6>
                                <p class="mb-0 text-muted">+33 1 23 45 67 89</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Email</h6>
                                <p class="mb-0 text-muted">contact@edugestion.com</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-item-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Horaires</h6>
                                <p class="mb-0 text-muted">Lun-Ven: 9h-18h<br>Sam: 9h-12h</p>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <h6 class="mb-3">Suivez-nous</h6>
                            <div class="social-links">
                                <a href="#" class="social-link">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte -->
            <div class="map-container">
                <h3 class="mb-4">
                    <i class="fas fa-map me-2 text-primary"></i>
                    Notre localisation
                </h3>
                <div class="map-placeholder">
                    <div class="text-center">
                        <i class="fas fa-map-marked-alt fa-3x mb-3 text-muted"></i>
                        <p class="mb-0">Carte interactive</p>
                        <small class="text-muted">123 Rue de l'Éducation, 75001 Paris</small>
                    </div>
                </div>
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
        // Validation du formulaire
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Récupération des données du formulaire
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Validation basique
            if (!data.nom || !data.email || !data.sujet || !data.message) {
                alert('Veuillez remplir tous les champs obligatoires.');
                return;
            }
            
            // Simulation d'envoi
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
            submitBtn.disabled = true;
            
            // Simulation d'un délai d'envoi
            setTimeout(() => {
                alert('Merci pour votre message ! Nous vous répondrons dans les plus brefs délais.');
                this.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });

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

        // Observer les cartes de contact
        document.querySelectorAll('.contact-card, .contact-info').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html> 