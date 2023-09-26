---
title: "Tenue de charge, disponibilité, scalabilité."
lastModified: "2021-04-21"
date: "2021-04-21"

# Params
metaDescription: "Panneau Pocket — L'application d'informations et d'alertes N°1 en France"
description: "Panneau Pocket, acteur majeur auprès des collectivités pour l'information de leurs usagés."
websiteUrl: https://www.panneaupocket.com/
shortDescription: "L'application d'informations et d'alertes N°1 à destination des collectivités."
clients: Panneau Pocket
size: 3 mois
services: ["Conception", "Accompagnement", "Pilotage"]
terms: ["ovh-cloud", "scaleway"]
images: ["content/images/case-study/headers/panneaupocket-banner.jpg"]
enabled: true
---

## Le contexte du projet

**Panneau Pocket** a pour objectif de permettre aux collectivités de mieux communiquer avec leurs administrés et propose à ce titre une application mobile capable d'alerter en temps réel ses utilisateurs d'une nouvelle publication.

### Les objectifs
- Être capables de tenir une solicitation des infrastructures soutenue;
- Répondre à des pics de trafic importants lors de communications d'envergure;
- Avoir l'obligation de disponibilité;
- Assurer la sécurité, la sureté et la confidentialités des données.

## Architecture

Panneau pocket répose essentiellement sur de l'API qui permet d'exposer de manière structurée et sécurisée ses données à l'application mobile.
Nous intervenons en collaboration avec les équipes de développement applicatif afin de trouver les meilleures solutions pour répondre au besoin exposé par les équipes de PanneauPocket.

### Métier et contraintes

Le travail de Rix est de faire en sorte que 3 points essentiels soient au rendez-vous:
- les temps de réponses doivent être bons (Ne pas oublier que nos utilisateurs sont souvent sur de l'itinérance et loin des infrastructures des grandes métropoles);
- l'application doit mettre en oeuvre une tolérance à la panne resposant sur de la redondance et assurer, même en cas d'incident, une qualité de service minimum;
- les incidents dans la mesure du possible, doivent être anticipés.

À cela vient s'ajouter les contraintes de **sureté des données personnelles** et bien évidemment les données des utilisateurs **ne doivent pas** quitter l'union européenne.

__Auquel nous ajoutons bien évidemment nos propres contraintes de qualité/supervision:__
- l'ensemble de l'infrastructure doit être décrite sour forme d'IAC (Infrastructure As Code);
- les environnements applicatifs doivent être « [versionnés](https://fr.wiktionary.org/wiki/versionner) » et doivent pouvoir être redéployés de manière automatique et idempotente;
- l'ensemble des secrets applicatifs sont stockés dans un espace chiffré, sécurisé;
- les sauvegardes bénéficient d'une triple réplique sur deux fournisseurs différents et sont chiffrées;
- les accès sont controlés et audités.

### Mise en oeuvre

Afin de remplir ce contrat et répondre aux contraintes métier nous nous sommes appuyés sur du matériel **OVH** et **Scaleway** en proposant une infrastructure redondée classique orchestrée sur un réseau privé intégrant:

- Un répartiteur de charge en frontal (redondé);
- Plusieurs instances applicatives;
- Un répartiteur de charge SQL Maxscale (redondé);
- Plusieurs instances de base de données.

À cela vient s'ajouter les briques logiques standard (WAF, SG...) que nous ne détaillerons pas ici ainsi que les espaces de stockage reposants sur différentes stratégies mixant NFS, stockage de type bloc et stockage objet (S3).

### Environnement d'exploitation

L'environnement d'exploitation réponds aux standards Rix à savoir:

- Une remontée des métriques dans différents dashboard **Grafana** (Système et applicatif);
- Un alerting de l'ensemble des services via **messagerie, mail et SMS**;
- Une exploitation des logs applicatifs et système via le composant Loki (Grafana);
- Remontée des erreurs aux équipes applicatives via une plateforme **Sentry**;
- Les secrets applicatifs sont stockés dans un coffre de type **Hashicorp Vault** déployé sur nos infrastructures.
