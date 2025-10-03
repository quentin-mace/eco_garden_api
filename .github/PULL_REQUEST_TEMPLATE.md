---
**Type de PR** : [Bugfix | Feature | Refactor | Docs | Tests | Chore]
**Lié à** : [#NuméroIssue ou "N/A" si non applicable]

---

## **Description**
*Décris brièvement les changements apportés et leur objectif.*
*(Ex: "Ajout d'un endpoint `/users/{id}/orders` pour récupérer les commandes d'un utilisateur")*

---

## **Changements clés**
- **Nouveaux endpoints** :
    - `METHOD /path` → Description (ex: `GET /users/{id}` → Récupère un utilisateur par ID)
- **Modifications d'endpoints existants** :
    - `METHOD /path` → Changement (ex: Ajout du champ `is_active` dans la réponse)
- **Modifications de schémas** :
    - Modèle `User` → Ajout du champ `last_login` (type: `datetime`)
- **Autres** :
    - Mise à jour des middlewares, config, dépendances, etc.
---

## **Checklist avant merge**
- [ ] Code terminé
- [ ] Code testé
- [ ] Relecture terminée
---
**Notes supplémentaires** :
*(Ajoute ici des détails pour les reviewers : choix techniques, questions ouvertes, etc.)*
