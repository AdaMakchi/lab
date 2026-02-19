# e89_lab1 — API REST de gestion de livres

Petit projet PHP pour apprendre à créer une API REST (CRUD) avec stockage JSON.

## Structure

- `api/` — code source (point d'entrée `index.php`)
- `data/books.json` — stockage JSON

## Lancer le serveur

1. Ouvrir un terminal dans `e89_lab1/api`
2. Lancer :

   php -S localhost:8000

## Endpoints

- GET  /index.php            → lister tous les livres
- GET  /index.php/{id}       → récupérer un livre
- POST /index.php            → créer un livre
- PUT  /index.php/{id}       → mettre à jour un livre
- DELETE /index.php/{id}     → supprimer un livre

> Avec Apache + `.htaccess` vous pouvez utiliser `/api/books` et `/api/books/{id}`.

## Validation (règles principales)

- `title` : requis (non vide)
- `author`: requis (non vide)
- `isbn` : optionnel, 10–17 caractères (chiffres et `-`)
- `year` : optionnel, entre 1000 et l'année courante

## Exemples cURL

# 1. Créer un livre
curl -X POST http://localhost:8000/index.php \
  -H "Content-Type: application/json" \
  -d '{"title":"The Pragmatic Programmer","author":"Andrew Hunt","isbn":"978-0135957059","year":2019}'

# 2. Lister tous les livres
curl http://localhost:8000/index.php

# 3. Récupérer le livre ID 1
curl http://localhost:8000/index.php/1

# 4. Mettre à jour le livre ID 1
curl -X PUT http://localhost:8000/index.php/1 \
  -H "Content-Type: application/json" \
  -d '{"title":"Clean Code - Updated"}'

# 5. Supprimer le livre ID 1
curl -X DELETE http://localhost:8000/index.php/1

## Checklist (attendu)
- [x] GET /books retourne tous les livres (200)
- [x] GET /books/1 retourne le livre 1 (200)
- [ ] GET /books/999 retourne 404
- [ ] POST crée un livre (201)
- [ ] POST sans `title` retourne 400
- [ ] PUT met à jour (200)
- [ ] PUT sur ID inexistant retourne 404
- [ ] DELETE supprime le livre (200)
- [ ] Toutes les réponses sont JSON et utilisent les bons codes HTTP

## Extensions possibles
- recherche / filtrage / pagination
- validation ISBN-10/13 complète
- tests unitaires

---
Bon travail — dites-moi si vous voulez que j'ajoute le filtrage, la pagination ou des tests automatisés. 🚀
