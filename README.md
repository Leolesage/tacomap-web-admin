# TacoMap France Web Admin (PHP 8.2, no framework)

Interface d'administration responsive pour gerer les `TacosPlace` via l'API TacoMap (JWT Bearer).

## Prerequis
- PHP 8.2+
- API TacoMap demarree sur `http://localhost:8001` (ou autre URL configuree)
- Composer (si besoin d'installer les dependances)

## Installation
1. Copier l'environnement:
```bash
copy .env.example .env
```
2. Installer les dependances:
```bash
composer install
```
3. Lancer le serveur:
```bash
php -S localhost:8000 -t public
```

## Authentification
- Ecran login: `/login`
- Le login appelle `POST /api/auth/login`
- Le JWT est conserve en session serveur PHP (pas en `localStorage`)
- Les appels admin vers l'API ajoutent `Authorization: Bearer <token>`

## Parcours admin
1. Login
2. Liste paginee + recherche AJAX (`/admin/tacos-places`)
3. Creation avec upload photo + carte Leaflet (clic => latitude/longitude)
4. Detail avec marker
5. Export PDF (`/admin/tacos-places/{id}/pdf`, proxy API)
6. Update / delete avec confirmation

## Accessibilite (RGAA minimum)
- Labels explicites sur tous les champs
- Navigation clavier conservee (focus visible + skip link)
- Contrastes raisonnables
- Attributs ARIA sur cartes et retours d'etat
