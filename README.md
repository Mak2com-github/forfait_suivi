# Forfait Suivi

Plugin WordPress pour suivre les interventions techniques réalisées sur le site d’un client. Il permet de créer des forfaits, d’y rattacher des tâches, et de suivre un solde de temps (y compris en négatif en cas de dépassement).

## Installation
1. Télécharger l’archive ZIP.
2. Dans WordPress > Extensions > Ajouter, importer l’archive.
3. Activer l’extension.

## Utilisation
1. Créer un forfait (titre, description, temps total).
2. Ajouter des tâches au forfait (durée + description).
3. Le temps des tâches est débité du forfait. Si le solde passe sous 0, le forfait est en dépassement.
4. Recharger un forfait à tout moment. Le temps ajouté compense d’abord le dépassement.
   Exemple : solde -02:00:00 + ajout 10:00:00 => solde 08:00:00.

## Notes importantes
- La recharge d’un forfait rend les tâches précédentes « historiques » (elles ne sont plus comptabilisées).
- La suppression d’un forfait supprime ses tâches associées.

## Structure du projet
- `forfait.php` : point d’entrée du plugin.
- `includes/` : logique PHP et actions DB.
- `views/` et `templates/` : UI admin et formulaires.
- `assets/` : CSS, JS, icônes.

## Permissions
Les actions de gestion sont limitées aux utilisateurs avec la capacité `manage_options`.
