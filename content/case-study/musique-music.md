---
title: "Stockage résilient à haute disponibilité."
lastModified: "2022-09-12"
date: "2023-04-21"

# Params
metaDescription: "Musique & Music — librairie musicale pour les professionnels"
description: "Musique & Music permet aux professionnels de la vidéo d'enrichir leurs productions avec de l'illustration sonore."
websiteUrl: https://www.musique-music.com/
shortDescription: "Musique & Music est une librairie musicale pour les professionnels."
clients: Musique & Music
size: 3 mois
services: ["Conception", "Infrastructure", "Stockage haute dispo.", "Stratégie de déploiement"]
terms: ["ovh-cloud"]
images: ["content/images/case-study/headers/musique-music-banner.jpg"]
enabled: true
---

## Le contexte du projet

**Musique & Music est un éditeur spécialisé dans la musique de production dédiée aux professionnels. L'application web permet aux monteurs vidéos de chercher facilement des sons afin d'illustrer leurs productions.** Parmi les atouts de l'application, il y a notamment la richesse du catalogue, la fluidité de la recherche et la pertinence des résultats proposés. Une recherche par similarité permet aux clients de Musique & Music de rechercher finement un style de musique en important des fichiers audio.

**Musique & Music a confié à Rix la conception de son infrastructure et son infogérance,** permettant de s'appuyer sur une équipe rodée à l'exploitation.

## L'expertise Rix déployée pour l'application Musique Music

### Analyse de l'existant

Musique & Music n'en était pas à sa première version, l'application existait déjà depuis plusieurs années mais la dette technique, l'obsolescence du code existant et la contrainte d'exploiter des briques logicielles propriétaires ne donnant plus satisfaction, ont décidé les fondateurs à repartir d'une feuille blanche.
Nous avons dès lors été solicités pour étudier et concevoir une infrastructure destinée à acceuillir la nouvelle application.
En collaboration avec [les équipes de concepteurs **d'Elao**](https://www.elao.com) nous avons commencé à imaginer ce que pourrait être cette nouvelle infrastructure en fonction des contraintes métiers du projet (disponibilité, performance et résilience).

La première étape étant de récupérer, sécuriser et rendre hautement disponible les données musicales.

### Le stockage

C'est la pierre angulaire du métier de Musique & Music, si l'application peut s'autoriser d'exceptionnelles interruptions de service la donnée doit elle, rester disponible.
Avec la volonté de **rester souverain** sur l'ensemble de son infrastructure, nous avons opté pour une solution reposant sur CEPH avec le Cloud Disk Array de chez OVH.

Les points important qui ont permis de retenir cette solution:

- La distribution du stockage
- La triple réplication des données
- La disponibilité
- Le redimensionnement à chaud

### La sureté des données

Au dela de l'aspect disponibilité des données, nous devions également veiller à disposer des pistes musicales hors infrastructure, en cas d'incident grave sur la brique de stockage entrainant son indisponibilité.
Nous avons opté pour une solution de **synchronisation incrémentale** des données sur un NAS Synology à travers un flux chiffré sur une instance dédiée à cette tâche.

### La brique applicative

Elle repose sur une « stack » web assez standard basée sur du public cloud et mettant en oeuvre sur réseau privé:

- Un répartiteur de charge de type **HAProxy**
- Un serveur **Nginx**
- Un serveur de base de données de type **MariaDB**

Le tout fonctionnant sur un environnement applicatif **PHP/Symfony**.

<figure>
    <img src="../images/case-study/schemas/m_and_m/case_studies_m_and_m.png" alt="Étude de cas - Schema d'infrastructure">
    <figcaption>
      <span class="figure__legend">Musique & Music - Schema d'infrastructure</span>
    </figcaption>
</figure>

### Conception de l'infrastructure

Comme pour l'ensemble de nos infras, nous appuyons fortement sur l'automatisation, à la fois des déploiements applicatifs via CI/CD mais également de l'infrastructure avec différents outils:

- [**Terraform**](https://www.terraform.io/) pour l'IaC (Infrastructure as Code) afin de déployer nos différentes briques;
- [**Ansible**](https://www.ansible.com/) pour la construction des environnements d'exécution applicatif;
- [**Helm**](https://helm.sh/) pour le déploiement des infrastructures de type Kubernetes.

### Environnement d'exploitation

L'environnement d'exploitation réponds aux standards Rix à savoir:

- Un alerting de l'ensemble des services via **messagerie, mail et SMS**;
- Une remontée des métriques dans différents dashboard **Grafana** (Système et applicatif) hébergé et infogéré sur nos infrastructures 🇫🇷;
- Une exploitation des logs applicatifs et système via le composant Loki (Grafana) hébergé et infogéré sur nos infrastructures 🇫🇷;
- Remontée des erreurs aux équipes applicatives via une plateforme **Sentry** (SaaS);
- Les secrets applicatifs sont stockés dans un coffre de type [**Hashicorp Vault**](https://www.vaultproject.io/) hébergé et infogéré sur nos infrastructures 🇫🇷.
