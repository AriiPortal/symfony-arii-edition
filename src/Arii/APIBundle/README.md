Gestion des alertes
===================

Surveillance
------------

La surveillance donne une vue globale des différents évènements survenant sur le Système d'Information.
On distingue les éléments suivants:
- l'objet concerné (serveur, traitement, etc...)
- la source de l'objet (surveillance, ordonnancement, helpdesk)
- le status (erreur, blocage, durée dépassée...)
- l'état (ouvert, pris en charge, en attente, fermé, hors perimètre)

Cette liste exhaustive permet d'accéder aux informations de diagnostic:
- journal
- instructions

Sources de données
------------------

### Evènements

Les évènements concernent toutes les interventions présentes et futures afin d'identifier les impacts potentiels. La source est généralement le logiciel de HelpDesk qui centralise les actions:
- futures mises en production
- incidents critiques

### Traitements

Les traitements proviennent des logiciels d'ordonnancement, de transferts de fichiers ou de tout autre outil capable d'exécuter des commandes.

### Réseau & Infrastructures

L'état du réseau est fourni par le logiciel de supervision Nagios à travers l'outil socat.

Pour un état plus précis, il faudra implémenter un synchronisation avec ElasticSearch.
