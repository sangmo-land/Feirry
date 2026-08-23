# FEIRRY — site vitrine & back-office

Site public bilingue (français / anglais) et panneau d'administration pour une
entreprise de bâtiment et travaux publics basée à Douala, Cameroun.

**Stack** : Laravel 13 · Inertia 3 · React 19 · Tailwind CSS 4 · Filament 5 · MySQL / MariaDB

---

## Démarrage

```bash
composer setup        # install, .env, migrate, seed, storage:link, build
php artisan serve     # http://127.0.0.1:8000
npm run dev           # rechargement à chaud pendant le développement
```

`composer setup` suppose une base MySQL déjà créée et renseignée dans `.env` :

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=feirry
DB_USERNAME=root
DB_PASSWORD=
APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
```

**Accès au back-office** — `/admin`

| | |
|---|---|
| Identifiant | `admin@feirry.org` |
| Mot de passe | `feirry2026` |

> Changez ce mot de passe avant toute mise en ligne :
> `php artisan tinker --execute="App\Models\User::first()->update(['password' => Hash::make('…')]);"`

Autres commandes utiles :

```bash
composer fresh        # remet la base à zéro et rejoue les données de démonstration
composer test         # 30 tests fonctionnels
composer deploy       # migrations + mise en cache config/routes/vues/Filament
```

---

## Ce que contient le site

### Pages publiques

| URL | Contenu |
|---|---|
| `/` | Accueil : héros animé, métiers, réalisations en défilement horizontal, méthode, témoignages, actualités |
| `/entreprise` | Histoire, valeurs, frise chronologique, sécurité, parc matériel, certifications |
| `/metiers`, `/metiers/{slug}` | Six métiers, chacun avec sa fiche détaillée |
| `/realisations`, `/realisations/{slug}` | Portfolio filtrable ; étude de cas contrainte / réponse / résultat |
| `/methode` | Les cinq étapes d'un chantier et leurs livrables |
| `/equipe` | Direction et encadrement technique |
| `/actualites`, `/actualites/{slug}` | Blog avec rubriques, recherche et pagination |
| `/carrieres`, `/carrieres/{slug}` | Offres d'emploi et candidature avec dépôt de CV |
| `/faq`, `/contact`, `/devis` | Questions fréquentes, contact, demande de devis en trois étapes |
| `/mentions-legales`, `/confidentialite` | Pages légales (non indexées) |
| `/sitemap.xml`, `/robots.txt` | Générés à partir du contenu publié |

Le sélecteur `FR / EN` de l'en-tête bascule la langue et la mémorise en session.
Le français reste la langue par défaut quelle que soit la configuration du
navigateur : c'est la langue de travail à Douala.

### Back-office (`/admin`)

Regroupé par usage : **Contenu du site**, **Entreprise**, **Actualités**,
**Recrutement**, **Demandes entrantes**, **Réglages**.

- Chaque contenu éditorial se saisit dans deux onglets, Français et English.
- Les demandes de devis ont un suivi commercial (statut, date de rappel, notes
  internes) et des raccourcis « Appeler » / « Répondre ».
- Les candidatures permettent de télécharger le CV, stocké **hors de la racine
  web** sur le disque `local`.
- Le tableau de bord affiche les demandes de la semaine et les dossiers en attente.
- **Réglages → Paramètres du site** modifie le nom, le téléphone, l'adresse, le
  WhatsApp et les horaires affichés partout, sans redéploiement.

---

## Architecture

```
app/
  Filament/            Ressources, widgets et page de réglages du back-office
    Support/           FormFields : onglets FR/EN, slug, images, SEO partagés
  Http/Controllers/Site/   Un contrôleur par section publique
  Http/Middleware/     SetLocale (langue) + HandleInertiaRequests (props partagées)
  Models/              Modèles Eloquent, chacun avec toCard()/toDetail() pour Inertia
  Support/
    Translatable.php   tr('champ') résout _fr / _en avec repli sur le français
    SiteSettings.php   table settings > config/site.php
    Seo.php            métadonnées rendues côté serveur
config/site.php        Coordonnées, mentions légales, réseaux, chiffres clés
database/seeders/      Contenu de démonstration réaliste (12 chantiers, 6 articles…)
resources/js/
  Components/          Motion, Ui, Cards, Form, Media, Header, Footer, Chrome
  Sections/            Hero, HorizontalProjects, blocs partagés
  Pages/               Une page par écran Inertia
  Lib/icons.js         Registre d'icônes explicite
resources/views/app.blade.php   Racine Inertia + balises SEO serveur
lang/{fr,en}/site.php  Tous les libellés de l'interface
tools/                 Scripts de vérification visuelle (Playwright)
```

### Deux choix qui méritent une explication

**Le SEO est rendu côté serveur, pas par Inertia.** `<Head>` d'Inertia ne
produit des balises exploitables par les moteurs que si un processus SSR tourne
en permanence. Pour éviter d'imposer ce service au client, les contrôleurs
appellent `Seo::set()` et `resources/views/app.blade.php` écrit `<title>`,
`description`, Open Graph, `canonical` et le JSON-LD directement dans le HTML.

**Les traductions sont des colonnes, pas du JSON.** Chaque champ traduisible
existe en `_fr` et `_en`. C'est plus verbeux en base, mais cela rend les champs
directement filtrables, triables et surtout éditables dans Filament sans plugin.

---

## Images

Toutes les photographies sont servies depuis le disque `public`
(`storage/app/public`, exposé sous `/storage` par le lien symbolique). Plus rien
n'est chargé depuis un service externe au moment du rendu.

| Dossier | Contenu | Nommage |
| --- | --- | --- |
| `heroes/` | le bandeau d'ouverture de chaque page | la clé de la page : `home.jpg`, `contact.jpg`, `career-detail.jpg` |
| `projects/covers/` · `projects/gallery/` | réalisations | le slug du projet ; galerie numérotée (`-1`, `-2`…) |
| `services/covers/` · `services/gallery/` | métiers | le slug du métier |
| `blog/covers/` | actualités | le slug de l'article |
| `sections/` | les visuels des blocs `SplitFeature` | page + rôle : `about-story.jpg` |
| `uploads/` · `uploads/gallery/` | ce que le back-office dépose | nom généré par Filament |

Deux façons de changer une image :

1. **Remplacer le fichier** au même chemin, sous le même nom. Rien d'autre à
   faire : la base pointe déjà dessus, et les composants aussi.
2. **Passer par le back-office**, qui écrit dans le dossier de la rubrique
   (`projects/covers/`, `projects/gallery/`…) et repointe la base toute seule.
   `App\Support\Media` accepte les deux formes — chemin relatif sur le disque
   `public` ou URL absolue.

Chaque emplacement a sa propre copie du fichier, même là où deux pages
partaient de la même photographie : remplacer le bandeau de la page Équipe ne
touche pas celui des Carrières.

Les images vivent directement dans `public/storage/`, versionnées avec le code :
le disque `public` y écrit sans passer par un lien symbolique. Rien à recréer
après un clone, et `php artisan storage:link` n'a plus rien à faire.

---

## Animations

| Effet | Mise en œuvre |
|---|---|
| Défilement fluide | Lenis piloté par le ticker GSAP, pour que ScrollTrigger et le scroll partagent la même horloge |
| Titres | Révélation mot à mot derrière un masque (`SplitText`) |
| Réalisations (accueil) | Section épinglée en défilement horizontal (GSAP ScrollTrigger), grille classique sous `lg` |
| Images | Parallaxe au scroll, volet d'ouverture (`ClipReveal`), zoom lent au survol |
| Compteurs | Animation à l'entrée dans le viewport |
| Navigation | Rideau entre les pages, curseur personnalisé, boutons magnétiques |
| Préchargeur | Compteur affiché une fois par session |

Tout est désactivé sous `prefers-reduced-motion: reduce`, et le curseur
personnalisé comme les effets magnétiques ne s'activent que sur pointeur fin.

> **Piège rencontré, à connaître avant de modifier ces composants.** Chrome
> intègre `clip-path` et les transformations au rectangle qu'il transmet à
> `IntersectionObserver`. Un élément qui démarre masqué (`clip-path` fermé,
> `scaleX(0)`) est donc vu comme invisible et son `whileInView` ne se déclenche
> jamais : il reste masqué indéfiniment. `ClipReveal` et `Bar` observent pour
> cette raison un conteneur non transformé et n'animent qu'un enfant.

---

## Vérification

```bash
composer test              # 31 tests fonctionnels (routes, formulaires, SEO, réglages)
npm run check:pages        # capture les 16 pages et signale toute erreur console
npm run check:flows        # navigation au clic, devis, contact, bascule FR/EN
npm run check:admin        # se connecte et parcourt chaque écran du back-office
npm run check:overflow     # détecte tout débordement horizontal de 360 à 1440 px
```

Les scripts Playwright écrivent leurs captures dans
`storage/app/screenshots` et affichent `ALL CLEAN` quand tout va bien.

`check:flows` mérite d'être lancé après toute modification des animations : il
navigue **au clic** plutôt que par chargement direct, ce qui est le seul moyen
de détecter les conflits entre GSAP et le rendu React d'Inertia.

> `check:flows` envoie de vraies demandes de devis et de contact. Supprimez-les
> depuis le back-office après usage sur un environnement de production.

---

## Google Search Console

Tout ce que Google lit est produit par l'application — rien n'est un fichier
déposé dans `public/`, parce qu'un déploiement remplace `public/` et qu'une
vérification qui disparaît avec un build est une propriété qui se
dévérifie toute seule.

| Adresse | Produite par |
| --- | --- |
| `/robots.txt` | `SitemapController@robots` — bloque `/admin`, `/storage/applications` et `/langue/`, et pointe vers le sitemap |
| `/sitemap.xml` | `SitemapController@index` — les deux langues, avec les alternates `hreflang` réciproques |
| `<link rel="canonical">`, `hreflang`, Open Graph, JSON-LD | `resources/views/app.blade.php`, alimenté par `App\Support\Seo` |

> **Il ne doit pas exister de `public/robots.txt`.** Le serveur web sert un
> fichier réel avant que Laravel ne voie la requête : celui qui accompagnait
> le squelette Laravel ne nommait aucun sitemap et masquait la route.
> `SearchEngineTest` vérifie son absence.

### Mise en service

1. **Renseigner `APP_URL`** avec le domaine définitif, en `https://`. C'est
   l'adresse qu'écrivent le sitemap, les balises canoniques et les `@id` des
   données structurées. Tant qu'elle vaut `localhost`, le sitemap déclare des
   URL que Google ne peut pas atteindre.
2. **Créer la propriété** dans Search Console — de préférence une propriété
   de domaine (vérification DNS), qui couvre `http`, `https`, `www` et les
   sous-domaines d'un coup.
3. **Sinon, la balise méta** : coller le jeton dans `GOOGLE_SITE_VERIFICATION`
   (le contenu de `content="…"`, pas la balise entière). Vide, aucune balise
   n'est émise — un `content` vide se lit comme une preuve ratée, pas comme
   une preuve absente. `BING_SITE_VERIFICATION` fait de même pour Bing.
4. **Soumettre `https://…/sitemap.xml`** dans *Sitemaps*.
5. **Contrôler le rendu** : *Inspection de l'URL* → *Tester l'URL en direct* →
   *HTML rendu*. Le site est rendu côté client ; c'est le seul écran qui dit si
   Googlebot a bien exécuté le JavaScript.

### La localisation, côté recherche

La section « Nous trouver » de `/entreprise` porte l'id `#localisation`
(`config/site.seo.location_anchor`, partagé jusqu'au composant React), et la
page émet un graphe JSON-LD où un nœud `Place` porte ce même fragment, avec
les coordonnées, le lien vers le point et les horaires d'ouverture.

Deux limites qu'il vaut mieux connaître d'avance :

- **La carte elle-même n'est jamais indexée.** C'est une iframe appartenant à
  `google.com` ; son contenu n'est pas celui de la page. Ce qu'un robot lit,
  c'est le texte autour — titre, adresse postale, horaires, lien vers le point.
  D'où leur présence en texte réel plutôt qu'en image.
- **Un fragment n'est pas une URL.** `/entreprise#localisation` n'aura jamais sa
  propre ligne dans l'index. L'ancre donne une adresse stable à la section et un
  sujet auquel rattacher la fiche ; c'est Google qui décide, ou non, de pointer
  directement dessus depuis un résultat.

Pour apparaître sur Google Maps et dans le panneau latéral, les données
structurées ne suffisent pas : il faut **une fiche Google Business Profile**
vérifiée à la même adresse, au même nom et au même numéro que
`config/site.php`. Le balisage confirme la fiche, il ne la remplace pas.

## À faire avant la mise en ligne

1. **Changer le mot de passe administrateur.**
2. **Remplacer les photographies.** Les visuels actuels viennent d'Unsplash et
   servent de repères de cadrage. Ils sont désormais posés dans
   `storage/app/public`, classés par usage : il suffit d'écraser un fichier par
   la photo de chantier correspondante, ou de la téléverser depuis le
   back-office. Voir la section **Images**.
3. **Ajouter les photos de l'équipe.** Sans photo, une carte affiche un
   monogramme typographique — c'est volontaire, pour ne jamais présenter un
   inconnu comme un collaborateur.
4. **Vérifier les mentions légales** : RCCM, NIU, capital social et assureur
   décennale sont dans `config/site.php` et doivent être confirmés.
5. **Configurer l'envoi d'e-mails** (`MAIL_*`). Les demandes sont enregistrées
   en base et visibles dans le back-office ; aucune notification n'est envoyée
   tant que le SMTP n'est pas renseigné.
6. **Passer en HTTPS** et renseigner `APP_URL`, utilisée par le sitemap, les
   balises canoniques et les `@id` des données structurées. Voir la section
   **Google Search Console**.
7. **Créer la fiche Google Business Profile** à l'adresse de `config/site.php`.
   Sans elle, le balisage de localisation n'a aucune fiche à confirmer.
8. **`composer deploy`** après chaque mise en production.

## Licence

Code développé pour FEIRRY. Photographies de démonstration sous licence Unsplash.
