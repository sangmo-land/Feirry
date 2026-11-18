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
git checkout main && git merge --no-edit origin/v7-refonte   # main contient un commit de test (public/deploy-probe.txt)
git rm -q --ignore-unmatch public/deploy-probe.txt   # ou déployer la branche
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
  tests/Feature/{FormSubmissionTest,PageHeroTest,PublicSiteTest}.php
# Anciens fichiers compilés : on retire tout fichier que manifest.json ne référence plus.
(cd public/build/assets && for f in *; do grep -qF "$f" ../manifest.json || git rm -q "$f"; done)
git commit -m "Nettoyage des fichiers remplacés par la V7"
```
(La boucle retire les anciens fichiers compilés : seul ce que référence `public/build/manifest.json` est utilisé par le site.)

Ces fichiers ne sont plus utilisés par le site ; sans ce nettoyage, le site fonctionne mais les anciens tests échouent.
Vérifier aussi que `public/storage` est un vrai dossier sur le serveur (pas un lien symbolique), et que `mod_headers` / `mod_deflate` sont actifs.

La migration `install_v7_content` installe les 6 expertises, 5 projets, 3 offres et la session
Coup de Pouce (fermée). Le contenu de démonstration de la première version est **dépublié, pas
supprimé** : il reste visible dans l’admin.

## 2 bis. Rubrique « Le Coup de Pouce FEIRRY » (CDC Coup de Pouce V2)

- Pages : `/coup-de-pouce` (programme), `/coup-de-pouce/consultation` (parcours A), `/coup-de-pouce/visite` (parcours B),
  `/coup-de-pouce/confirmation` (noindex, affichée seulement après enregistrement réel). Envoi : `POST /coup-de-pouce/demandes`.
- La migration V7 a changé (tables `campaigns`, `assistance_requests`, `assistance_audit_logs`, colonne `users.role`).
  Elle n’a jamais été déployée : un `php artisan migrate --force` normal suffit. **Ne jamais lancer `migrate:fresh` en production.**
- L’édition pilote « Édition fin d’année 2026 » est créée en état **Préparation** (formulaires fermés) :
  ouverture des demandes 20/11/2026, interventions 23/11 → 19/12/2026, Douala, 10 consultations et 5 visites annoncées
  par semaine (capacité interne 20/20, jamais affichée). FEIRRY la passe à « Ouverte » dans l’admin
  (Coup de Pouce → Éditions) après sa validation finale.
- Pièces privées sur le disque `local` : `storage/app/private/coup-de-pouce/editions/{édition}/demandes/{référence}/…`
  (originaux, rapports). Elles ne sont jamais publiques ; chaque téléchargement est journalisé.
- Limites d’envoi : 3 PDF × 15 Mo (parcours A), 5 photos × 8 Mo (parcours B). Régler PHP en conséquence :
  `upload_max_filesize = 16M`, `post_max_size = 64M`, `max_file_uploads = 10`.
- Rôles admin (Réglages → Équipe et rôles) : Administrateur technique, Administration FEIRRY, Référent technique
  (ne voit que les demandes qui lui sont affectées), Communication (aucun accès aux demandes ni aux plans).
- Double authentification de l’admin (application Google/Microsoft Authenticator), migration
  `2026_11_17_000001_add_admin_two_factor`. Chaque membre l’active dans « Profil ». Une fois que tous l’ont configurée,
  mettre `ADMIN_MFA_REQUIRED=true` dans le `.env` pour la rendre obligatoire (CDC §22).
- Les rôles Communication et Référent n’ont plus accès aux demandes « Mon projet », messages, candidatures ni abonnés.
- Correctif important : le modèle `User` déclare maintenant l’accès au panneau Filament. Sans cela, Filament refuse
  tous les comptes dès que `APP_ENV` n’est pas `local`.

## 2 bis-2. Coup de Pouce : réservation automatique (décision FEIRRY du 18/11/2026)

- Migration `2026_11_18_000001_add_coup_de_pouce_slots` : colonnes `slot_key` et `seat_key` (uniques) sur `assistance_requests`,
  et mise à jour de la phrase des conditions de l'édition pilote (seulement si le texte installé n'a pas été modifié). `php artisan migrate --force` suffit.
- Fuseau de l'application : `Africa/Douala` par défaut (`APP_TIMEZONE`). Ne pas remettre `UTC` : les créneaux et les dates d'édition sont lus à l'heure de Douala.
- Créneaux (`config/site.php`, clé `cdp.slots`) : consultations le lundi de 9 h à 16 h par tranches de 30 minutes ; visites le mercredi et le jeudi à 8 h 30, 11 h 30 et 14 h 30.
  Réservable jusqu'à 24 h avant (`CDP_SLOT_LEAD_HOURS`). Plafond hebdomadaire = places annoncées de l'édition (10 consultations, 5 visites), modifiables dans l'admin.
- Un créneau réservé met la demande au statut « Programmée » avec sa date. Passer une demande en « Hors périmètre » libère le créneau et la place.
- Quand tous les créneaux sont pris, les demandes vont en liste d'attente (statut « En attente »).

## 2 ter. Sécurité (audit du 17/11/2026)

- Partir du modèle **`docs/env-production.example`** (à copier en `.env` sur le serveur) : `APP_ENV=production`, `APP_DEBUG=false`, sessions chiffrées et
  cookies sécurisés, `FORCE_HTTPS=true`, utilisateur MySQL dédié (pas root).
- `TRUSTED_PROXIES` : à renseigner **uniquement** si le site passe par Cloudflare ou un répartiteur de charge
  (adresses du proxy). Avant, l’application faisait confiance à n’importe quel proxy : on pouvait falsifier son IP
  et contourner les limites anti-spam et anti-force brute.
- En-têtes envoyés par l’application elle-même (CSP stricte avec nonce, X-Frame-Options, Permissions-Policy,
  Referrer-Policy, HSTS en HTTPS) ; `public/.htaccess` bloque les fichiers cachés (.env, .git) et
  `public/storage/.htaccess` interdit l’exécution de scripts dans les fichiers téléversés.
- `php.ini` : `expose_php = Off`. Lancer `composer audit` sur le serveur (impossible depuis mon environnement).
- Changer le mot de passe admin historique (il apparaît dans l’historique git de `main`). Mots de passe admin :
  12 caractères minimum avec lettres et chiffres.
- Double authentification : chaque membre l’active dans « Profil », puis `ADMIN_MFA_REQUIRED=true`.
- Sauvegardes : base + `storage/app/private` (plans et photos du Coup de Pouce), quotidiennes, testées en restauration.

## 3. Variables d’environnement (.env du serveur, sans secret dans le dépôt)

| Variable | Rôle |
|---|---|
| `LEADS_NOTIFY_EMAIL` | Adresse qui reçoit l’alerte « nouvelle demande » (vide = aucune alerte, les demandes restent dans l’admin). |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | Service d’envoi d’e-mails. |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Utilisés uniquement par le seeder en local. |
| `CDP_PDF_MAX_FILES`, `CDP_PDF_MAX_KB`, `CDP_PHOTO_MAX_FILES`, `CDP_PHOTO_MAX_KB` | Facultatif : limites des pièces Coup de Pouce (défaut 3 × 15360 Ko, 5 × 8192 Ko). |
| `TRUSTED_PROXIES` | Adresses des proxys de confiance (Cloudflare, répartiteur). Vide = aucun. |
| `FORCE_HTTPS` | Liens générés en HTTPS (défaut : `true` en production). |
| `ADMIN_MFA_REQUIRED` | `true` pour imposer la double authentification à tous les comptes admin (défaut `false`). |
| `GOOGLE_SITE_VERIFICATION`, `BING_SITE_VERIFICATION` | Facultatif. |

## 4. Contrôles à chaud

`/`, `/feirry`, `/expertises`, `/projets`, `/offres`, `/coup-de-pouce`, `/conseils`, `/mon-projet`,
`/en`, `/sitemap.xml`, `/robots.txt` ; redirections `/entreprise`, `/realisations`, `/devis` (301) ;
thème clair/sombre ; FR/EN ; barre basse mobile ; envoi d’une demande test « Mon projet » puis
vérification dans l’admin (Demandes entrantes) et de l’e-mail d’alerte. Coup de Pouce : avec l’édition
encore en « Préparation », vérifier que les formulaires affichent « Les demandes en ligne ne sont pas encore ouvertes ».

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
