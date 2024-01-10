---
name: "Ansible"
logo: "build/images/services/ansible.svg"
externalLink: https://www.ansible.com/
metaDescription: Adeptes de la première heure d'Ansible, nous continuons 10 ans plus tard à l'utiliser de manière intensive pour la construction de nos infrastructures et de nos environnement de développement.
---

## C'est quoi Ansible ?
Ansible est une plateforme open-source de gestion de configuration et d'automatisation des tâches. 
Elle permet aux administrateurs système mais aussi aux développeurs de définir des configurations, des déploiements et des actions à effectuer sur des serveurs et des infrastructures de manière reproductible. 

Ansible se distingue par sa simplicité d'utilisation grâce à sa syntaxe déclarative en **YAML**, qui **décrit l'état souhaité du système**. Il n'exige **aucune installation d'agent** sur les machines cibles, fonctionne via SSH ou d'autres protocoles, et offre une grande flexibilité pour automatiser des opérations complexes, la gestion de configurations, et le déploiement d'applications.

Nous l'utilisons maintenant depuis 10 ans afin de piloter la configuration de nos environnements applicatifs et mettons à disposition de la communauté de nombreux rôles éprouvés en production sur des infrastructures conséquentes à travers notre projet Open Source [Manala](https://github.com/manala/ansible-roles).

Nous sommes également à l'origine de la création des **« meetups » Lyonnais Ansible**, que nous avons organisés et pilotés pendant plusieurs années.

## Pourquoi Ansible ?

Questions souvent posée, nous avons travaillons par le passé avec Chief et Puppet en passant par Salt mais quand Ansible est apparu il s'est rapidement imposé chez nous notamment en raison de sa rapidité d'apprentissage et de sa popularité au sein des équipes de développeurs.

En apportant notre expertise Ops nous devons également réfléchir à la facilité de prise en main des solutions que nous proposons aux équipes applicatives.

## Son utilisation chez Rix

Il est utilisé sur l'ensemble des tâches de configuration des environnements, entre autres: 

- Gestion des configurations
- Installation des services
- Gestion des accès

Nous le couplons à Terraform qui porte lui la responsabilité de constructions des éléments d'infrastructure.

Il est également prépondérant pour la construction des environnements de développement à destination des équipes applicatives.