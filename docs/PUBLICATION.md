# FEIRRY — Mise en ligne de la version V7

Branche : `v7-refonte`. Référence : cahier des charges V7 (12 nov. 2026), maquette V7.1 validée.

## 1. Avant la bascule (obligatoire, CDC §12)

1. **Sauvegarder** la base MySQL de production :
   `mysqldump -u USER -p BASE > feirry-avant-v7-$(date +%F).sql`
2. **Sauvegarder** le dossier `public/storage` (images téléversées).
3. Noter le commit actuellement en ligne : `git rev-parse HEAD` (retour arrière).

## 2. Déploiement

```bash
git fetch origin
git checkout main && git merge --ff-only origin/v7-refonte   # ou déployer la branche
composer install --no-dev --optimize-autoloader
composer deploy        # migrate --force + caches
```

Les fichiers compilés (`public/build`) et les images V7 (`public/storage/v7`) sont dans le dépôt :
aucun `npm` n’est nécessaire sur le serveur.

### Nettoyage des fichiers remplacés (à faire une fois, après la fusion)

```bash
git rm -q app/Http/Controllers/Site/{Contact,Home,Page,Project,Quote,Service}Controller.php \
  app/Http/Requests/{StoreContactRequest,StoreQuoteRequest}.php \
  resources/js/Layouts/SiteLayout.jsx \
  resources/js/Pages/{About,Contact,Faq,Home,Legal,Process,Quote,Team}.jsx \
  resources/js/Pages/{Blog,Projects,Services}/{Index,Show}.jsx \
  tests/Feature/{FormSubmissionTest,PageHeroTest,PublicSiteTest}.php \
  public/build/assets/{app-B5Pq6R0f.css,app-CT4qmrEy.js,motion-BIV-clJw.js,react-DhWLjSIC.js,rolldown-runtime-hePW80VL.js,scroll-BxvexZXi.js}
git commit -m "Nettoyage des fichiers remplacés par la V7"
```

Ces fichiers ne sont plus utilisés par le site ; sans ce nettoyage, le site fonctionne mais les anciens tests échouent.
Vérifier aussi que `public/storage` est un vrai dossier sur le serveur (pas un lien symbolique), et que `mod_headers` / `mod_deflate` sont actifs.

La migration `install_v7_content` installe les 6 expertises, 5 projets, 3 offres et la session
Coup de Pouce (fermée). Le contenu de démonstration de la première version est **dépublié, pas
supprimé** : il reste visible dans l’admin.

## 3. Variables d’environnement (.env du serveur, sans secret dans le dépôt)

| Variable | Rôle |
|---|---|
| `LEADS_NOTIFY_EMAIL` | Adresse qui reçoit l’alerte « nouvelle demande » (vide = aucune alerte, les demandes restent dans l’admin). |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | Service d’envoi d’e-mails. |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Utilisés uniquement par le seeder en local. |
| `GOOGLE_SITE_VERIFICATION`, `BING_SITE_VERIFICATION` | Facultatif. |

## 4. Contrôles à chaud

`/`, `/feirry`, `/expertises`, `/projets`, `/offres`, `/coup-de-pouce`, `/conseils`, `/mon-projet`,
`/en`, `/sitemap.xml`, `/robots.txt` ; redirections `/entreprise`, `/realisations`, `/devis` (301) ;
thème clair/sombre ; FR/EN ; barre basse mobile ; envoi d’une demande test « Mon projet » puis
vérification dans l’admin (Demandes entrantes) et de l’e-mail d’alerte.

## 5. Retour arrière

```bash
git checkout <commit noté à l’étape 1>
mysql -u USER -p BASE < feirry-avant-v7-AAAA-MM-JJ.sql
php artisan optimize:clear && composer deploy
```

## 6. Gestion des contenus (admin `/admin`)

- **Contenu du site › Offres** : formules, inclus/exclus, libellé de prix (« Sur devis » par défaut).
- **Contenu du site › Coup de Pouce — sessions** : passer une session en « Ouverte » avec des dates pour activer les inscriptions ; une session passée ou complète se ferme seule.
- **Projets** : rôle FEIRRY, stade, filtres, visibilité (brouillon/publié/archivé), droits photo.
- **Métiers (expertises)** : situations, livrables, limites, besoin présélectionné.
- **Demandes entrantes** : demandes « Mon projet » et Coup de Pouce, documents privés téléchargeables.
