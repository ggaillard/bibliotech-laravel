# 📚 Glossaire - Deployment & Production

> 📎 **Document de référence** repris de la version 2025-2026 et mis à jour (MySQL, Laravel 13, noms en français).
> Les fiches de [cours/](../README.md) et la [documentation officielle](https://laravel.com/docs) font foi.


**Terme** | **Définition** | **Exemple**
---------|--------------|----------
**Deployment** | Processus de mise en production | `git push → tests → deploy`
**Pipeline** | Série d'étapes automatisées | GitHub Actions workflow
**CI/CD** | Continuous Integration/Deployment | Tests + Deploy automatique
**Build** | Compilation des assets | npm build, env compile
**Artifact** | Fichier compilé prêt à déployer | .jar, .tar.gz, Docker image
**Release** | Version de production | v1.0.0
**Rollback** | Revenir à version précédente | Déployer v1.0.0 au lieu v1.0.1
**Hotfix** | Correction urgente en prod | Patch de sécurité
**Staging** | Environment de test pré-prod | Clone production pour tests
**Load Balancer** | Répartir requêtes entre serveurs | Nginx, HAProxy
**Uptime** | % temps application online | 99.99% = 4.38 min downtime/an
**SLA** | Service Level Agreement | Garantie uptime
**Blue-Green** | Deploy avec deux versions | Blue=v1, Green=v2, basculer
**Canary Deploy** | Déployer progressivement | 1% → 10% → 100%
**Zero-downtime** | Deploy sans interruption | Blue-Green, canary
**Monitoring** | Surveiller la production | New Relic, Datadog
**APM** | Application Performance Monitoring | Outils de monitoring
**Logging** | Enregistrer événements | Logs dans fichiers/services
**Alerting** | Notifier en cas de problème | Email, SMS, Slack
**Health Check** | Vérifier app fonctionne | Endpoint /health → 200 OK

---

## 🛠️ Commandes Essentielles

### **Tests**
```bash
php artisan test                    # Lancer tous les tests
php artisan test --filter test_name # Test spécifique
php artisan test --coverage         # Avec coverage %
composer test                       # Si configuré in composer.json
```

### **Optimization Production**
```bash
php artisan config:cache           # Cache config
php artisan route:cache            # Cache routes
php artisan view:cache             # Cache views
php artisan optimize               # Optimisation globale
php artisan optimize:clear         # Effacer optimisations
```

### **Database Production**
```bash
php artisan migrate --force        # Force migrations prod
php artisan migrate:rollback       # Rollback dernière migration
php artisan db:seed                # Seeder data
php artisan tinker                 # REPL interactif
```

### **Cache Management**
```bash
php artisan cache:clear            # Effacer cache
php artisan queue:work             # Lancer workers
php artisan schedule:work          # Lancer scheduler
```

---

## 🌐 Services de Déploiement

### **VPS (Virtual Private Server)**
- Contôle total
- Serveur dédié virtuel
- Exemples: DigitalOcean, Linode, Vultr

### **Managed Hosting**
- Moins de config
- Support inclus
- Exemples: Laravel Forge, Ploi.io

### **Platform as a Service (PaaS)**
- Zero infrastructure
- Auto-scaling
- Exemples : Laravel Cloud, Platform.sh, Railway (offres et tarifs à vérifier)

### **Containers**
- Docker, isolation
- K8s pour scaling
- Exemples: AWS ECS, Google CloudRun

---

## 📊 Tools Populaires

### **Monitoring**
- New Relic (APM complet)
- Datadog (Monitoring + logs)
- Sentry (Error tracking)
- Rollbar (Real-time monitoring)

### **CI/CD**
- GitHub Actions (gratuit pour public)
- GitLab CI (inclus GitLab)
- Jenkins (self-hosted)
- CircleCI

### **Logging**
- ELK Stack (Elasticsearch, Logstash, Kibana)
- Papertrail (SaaS logging)
- CloudWatch (AWS)

### **Analytics**
- Google Analytics (frontend)
- Plausible (privacy-first)
- Fathom (lightweight)

---

## 🔐 Checklist Production

```
Infrastructure:
☑️ SSL certificate valide
☑️ Firewall configuré
☑️ Backups automatisés
☑️ Load balancer (si plusieurs serveurs)

Application:
☑️ APP_DEBUG=false
☑️ APP_ENV=production
☑️ Migrations exécutées
☑️ Cache activé
☑️ Assets minifiés

Security:
☑️ HTTPS/SSL
☑️ Rate limiting
☑️ CORS headers
☑️ Security headers
☑️ Password reset secure

Monitoring:
☑️ Error tracking
☑️ Performance monitoring
☑️ Uptime monitoring
☑️ Log aggregation
☑️ Alerting en place

Backup:
☑️ Database backups
☑️ File backups
☑️ Frequency: daily/hourly
☑️ Test restore régulièrement
```

---

Avancez vers les TP ! 🧪 → [la séance 8](../../seances/seance-08.md)
